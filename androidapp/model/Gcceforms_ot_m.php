<?php

    class Gcceforms_ot_m extends Dbase{
        
        public function get_employee_data(){
            $conn = $this->conn();

            $response['employee_array'] = array();

            $sth = $conn->query("SELECT * FROM gccmaster.tblemployees where employee_status='Active' order by firstname asc");
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {

                $fullname = $this->getDisplayName($row);
                $tempFullname = (object) $fullname;
                $display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                $employee_name = utf8_encode($display_name);

                $company_ = $this->getCompany($conn,$row['company_id']);
                $department_ = $this->getDepartment($conn,$row['department_id']);
                $position_ = $this->getPosition($conn,$row['position']);

                // If position name exist in the DB return id. Else return still the same name.
                $check_null_company = utf8_encode($company_['id'] ? $company_['id'] : $row['company_id']);
                $check_null_department = utf8_encode($department_['id'] ? $department_['id'] : $row['department_id']); 
                $check_null_position = utf8_encode($position_['id'] ? $position_['id'] : $row['position']);

                $list['id'] = $row['id'];
                $list['employee_name'] = $employee_name;
                $list['_company_id'] = is_numeric($row['company_id']) ? $row['company_id'] : $check_null_company;
                $list['_department_id'] = is_numeric($row['department_id']) ? $row['department_id'] : $check_null_department;
                $list['is_dephead'] = $this->isHead($row['id']) ? true : false;
                $list['_position_id'] = is_numeric($row['position']) ? $row['position'] : $check_null_position;
                $list['company_desc'] = utf8_encode(is_numeric($row['company_id']) ? $company_['description'] : $row['company_id']);
                $list['department_desc'] = utf8_encode(is_numeric($row['department_id']) ? $department_['description'] : $row['department_id']);
                $list['position_name'] = utf8_encode(is_numeric($row['position']) ? $position_['name'] : $row['position']);
                array_push($response['employee_array'], $list);
            }

            echo json_encode($response);
        }

        public function view_edit_ot_details(){
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            $conn = $this->conn();

            $response['employee_details'] = array();
            $ot_id = $_POST['ot_id'];

            $sth = $conn->prepare("SELECT * FROM gcceforms.overtime WHERE id = :ot_id");
            $sth->bindParam(':ot_id', $ot_id, PDO::PARAM_INT);
            $sth->execute();

            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {

                $list['id'] = $row['id'];
                $list['reference_no'] = $row['reference_no'];
                $list['employee'] = $this->getUserName($row['employee']);
                $list['employee_id'] = $row['employee'];
                $list['company'] = $row['company'];
                $list['department'] = $row['department'];
                $list['position'] = $row['position'];
                $list['purpose'] = $row['purpose'];
                $list['status'] = $row['status'];
                $list['created_by'] = is_numeric($row['created_by']) ? $this->getUserName($row['created_by']):'';
                $list['cancelled_remarks'] = $row['cancelled_remarks']!=''?trim($row['cancelled_remarks']):''; 
                $list['requested_by'] = $row['requested_by']!='0'?$this->getUserName($row['requested_by']):'';
                $list['requested_remarks'] = $row['requested_remarks']!=''? trim($row['requested_remarks']):'';
                $list['date_from'] = $row['date_from'];
                $list['date_to'] = $row['date_to'];
                array_push($response['employee_details'], $list);
            }

            echo json_encode($response);
        }

        public function view_ot_details(){
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            $conn = $this->conn();

            $response['employee_details'] = array();
            $ot_id = $_POST['ot_id'];

            $sth = $conn->prepare("SELECT * FROM gcceforms.overtime WHERE id = :ot_id");
            $sth->bindParam(':ot_id', $ot_id, PDO::PARAM_INT);
            $sth->execute();

            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {

                $list['id'] = $row['id'];
                $list['reference_no'] = $row['reference_no'];
                $list['employee'] = $this->getUserName($row['employee']);
                $list['employee_id'] = $row['employee'];
                $list['company'] = $row['company'];
                $list['department'] = $row['department'];
                $list['position'] = $row['position'];
                $list['purpose'] = $row['purpose'];
                $list['status'] = $row['status'];
                $list['date_from'] = date('F j, Y g:i a', strtotime($row['date_from']));
                $list['date_to'] = date('F j, Y g:i a', strtotime($row['date_to']));

                $list['created_by'] = is_numeric($row['created_by']) ? $this->getUserName($row['created_by']):'';
                $list['created_at'] =  $row['created_at']!='0000-00-00 00:00:00'?date('F j, Y g:i a', strtotime($row['created_at'])):'';

                $list['updated_by'] = $row['updated_by']!='0'?$this->getUserName($row['updated_by']):'';
                $list['updated_at'] = $row['updated_at']!='0000-00-00 00:00:00' ?date('F j, Y g:i a', strtotime($row['updated_at'])):'';

                $list['cancelled_by'] = $row['cancelled_by']!= '0'?$this->getUserName($row['cancelled_by']):'';
                $list['cancelled_at'] = $row['cancelled_at']!= '0000-00-00 00:00:00' ? date('F j, Y g:i a', strtotime($row['cancelled_at'])):'';
                $list['cancelled_remarks'] = $row['cancelled_remarks']!=''?trim($row['cancelled_remarks']):''; 

                $list['requested_by'] = $row['requested_by']!='0'?$this->getUserName($row['requested_by']):'';
                $list['requested_at'] = $row['requested_at']!='0000-00-00 00:00:00' ?date('F j, Y g:i a', strtotime($row['requested_at'])):'';
                $list['requested_remarks'] = $row['requested_remarks']!=''? trim($row['requested_remarks']):'';

                $list['approved_by'] = $row['approved_by']!='0'?$this->getUserName($row['approved_by']):'';
                $list['approved_at'] = $row['approved_at']!='0000-00-00 00:00:00' ?date('F j, Y g:i a', strtotime($row['approved_at'])):'';

                $list['disapproved_by'] = $row['disapproved_by']!='0'?$this->getUserName($row['disapproved_by']):'';
                $list['disapproved_at'] = $row['disapproved_at']!='0000-00-00 00:00:00' ?date('F j, Y g:i a', strtotime($row['disapproved_at'])):'';

                $time1 = date_create($row['date_from']);
                $time2 = date_create($row['date_to']);
                $time_diff = date_diff($time1, $time2);
                $tempHr = $time_diff->h;
                $tempMn = $time_diff->i;
                $tempHrLabel = ($tempHr == 1)? "Hour": "Hours";
                $tempMnLabel = ($tempMn == 1)? "Minute": "Minutes";
                $tempDuration = "{$tempHr} {$tempHrLabel} {$tempMn} {$tempMnLabel}";
                if($tempHr > 0 && $tempMn > 0){
                    $tempDuration = "{$tempHr} {$tempHrLabel} {$tempMn} {$tempMnLabel}";
                }elseif($tempHr > 0 && $tempMn == 0){
                    $tempDuration = "{$tempHr} {$tempHrLabel}";
                }elseif($tempHr == 0 && $tempMn > 0){
                    $tempDuration = "{$tempMn} {$tempMnLabel}";
                }

                $list['duration'] = $tempDuration;
                // $list['attachment_image'] = $row['attachment_image'] ? unserialize($row['attachment_image']) : array();

                $_tempImages = array();
                $tempImage = ($row['attachment_image'])? unserialize($row['attachment_image']): array();
                if(is_array($tempImage) && count($tempImage) > 0){
                    $list['has_attachment'] = true;
                    foreach ($tempImage as $key => $value) {
                        $tempRow = array();
                        $tempValue = explode("/", $value);
                        $thumbnail = "";
                        $filename = "";

                        if(count($tempValue) == 2){
                            $thumbnail = "{$tempValue[0]}/thumbnails/{$tempValue[1]}";
                            $filename = "{$tempValue[1]}";
                        }

                        $tempRow["filename"] = $value;
                        $tempRow["image"] = "uploads/files/images/overtime/{$value}";
                        $tempRow["thumbnail"] = "uploads/files/images/overtime/{$thumbnail}";
                        $_tempImages[] = $tempRow;
                    }
                    $list['images'] = $_tempImages;
                }else{
                    $list['images'] = $_tempImages;
                    $list['has_attachment'] = false;
                }

                $list['is_imported'] = $row['is_imported'];
                $list['gps_tracking_in'] = $row['gps_tracking_in'];
                $list['gps_tracking_out'] = $row['gps_tracking_out'];

                array_push($response['employee_details'], $list);
            }

            echo json_encode($response);
        }




        public function get_overtime_request_details(){
            $conn = $this->conn();

            $response['response_array'] = array();

            $id = $_POST['id'];
            $user_id = $_POST['user_id'];
            $img_path = $_POST['img_path'];

            $sth = $conn->query("SELECT DISTINCT o.*, e.firstname,e.middlename, e.lastname, e.suffix 
                                FROM gcceforms.overtime as o
                                LEFT JOIN gccmaster.tblemployees as e
                                ON e.id = o.employee
                                WHERE o.id='$id'");

            $row = $sth->fetch(PDO::FETCH_ASSOC);

            if ($sth->rowCount() > 0) {
                $company_ = $this->getCompany($conn,$row['company']);
                $department_ = $this->getDepartment($conn,$row['department']);
                $position_ = $this->getPosition($conn,$row['position']);
                
                $company = is_numeric($row['company']) ? $company_['description'] : $row['company'];
                $department = is_numeric($row['department']) ? $department_['description'] : $row['department'];
                $position = is_numeric($row['position']) ? $position_['name'] : $row['position'];

                $list['id'] = $row['id'];
                $list['reference_no'] = $row['reference_no'];
                $list['employee_name'] = $this->getEmployeeName($row);
                $list['company'] = $company;
                $list['department'] = $department;
                $list['position'] = $position;
                $list['created_by'] = $row['created_by'] ? $this->getUserName($row['created_by']) : "N/A";
                $list['requested_by'] = $row['requested_by'] ? $this->getUserName($row['requested_by']) : "N/A";
                $list['updated_by'] = $row['updated_by'] ? $this->getUserName($row['updated_by']) : "N/A";
                $list['approved_by'] = $row['approved_by'] ? $this->getUserName($row['approved_by']) : "N/A";
                $list['disapproved_by'] = $row['disapproved_by'] ? $this->getUserName($row['disapproved_by']) : "N/A";
                $list['cancelled_by'] = $row['cancelled_by'] ? $this->getUserName($row['cancelled_by']) : "N/A";
                $list['requested_by_id'] = $row['requested_by'];
                $list['purpose'] = $row['purpose'];
                $list['status'] = $row['status'];
                $list['employee'] = $row['employee'];
                $list['cancelled_remarks'] = $row['cancelled_remarks'];
                $list['requested_remarks'] = $row['requested_remarks'];
                $list['date_from'] = date('F j, Y g:i a', strtotime($row['date_from']));
                $list['date_to'] = date('F j, Y g:i a', strtotime($row['date_to']));
                $list['created_at'] = date('F j, Y g:i a', strtotime($row['created_at']));
                $list['updated_at'] = date('F j, Y g:i a', strtotime($row['updated_at']));
                $list['cancelled_at'] = date('F j, Y g:i a', strtotime($row['cancelled_at']));
                $list['requested_at'] = date('F j, Y g:i a', strtotime($row['requested_at']));
                $list['approved_at'] = date('F j, Y g:i a', strtotime($row['approved_at']));
                $list['disapproved_at'] = date('F j, Y g:i a', strtotime($row['disapproved_at']));
                $list['emp_folder_images'] = $this->getCurrentUploadedFile($user_id,$img_path);

                $_tempImages = array();
                $tempImage = ($row['attachment_image'])? unserialize($row['attachment_image']): array();
                if(is_array($tempImage) && count($tempImage) > 0){
                    $list['has_attachment'] = true;
                    foreach ($tempImage as $key => $value) {
                        $tempRow = array();
                        $tempValue = explode("/", $value);
                        $thumbnail = "";
                        $filename = "";
                        if(count($tempValue) == 2){
                            $thumbnail = "{$tempValue[0]}/thumbnails/{$tempValue[1]}";
                            $filename = "{$tempValue[1]}";
                        }
                        $tempRow["filename"] = $filename;
                        $tempRow["image"] = $img_path."files/images/overtime/{$value}";
                        $tempRow["thumbnail"] = $img_path."files/images/overtime/{$thumbnail}";
                        $_tempImages[] = $tempRow;
                    }
                    $list['images'] = $_tempImages;
                }else{
                    $list['images'] = $_tempImages;
                    $list['has_attachment'] = false;
                }

                array_push($response['response_array'], $list);
            }

            echo json_encode($response);
        }

        public function get_ot_data(){

            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            $conn = $this->conn();
            $response['response_array'] = array();
            $user_idv = $_POST['user_id'];
            $isView_own_request = $_POST['isView_own_request'];
            $current_date = date('Y-m-d');
            $date = date('Y-m-d', strtotime('-5 months', strtotime($current_date)));

            if($isView_own_request=='true' && $user_idv!=1) {
                $own_request = "AND a.employee='$user_idv'";
            }
            else {
                $own_request = "";
            }

            $sth = $conn->query("SELECT a.*, b.firstname, b.middlename, b.lastname, b.suffix, a.id, a.employee, a.purpose, a.status, a.reference_no, a.date_from, a.date_to, a.company, a.department, a.position
                                FROM gcceforms.overtime a
                                LEFT join gccmaster.tblemployees b
                                ON a.employee = b.id
                                WHERE a.status != 'Cancelled' $own_request AND DATE(a.created_at) >= '$date' order by a.created_at desc");

            if ($sth->rowCount() > 0) {            
                while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {

                    $fullname = $this->getDisplayName($row);
                    $tempFullname = (object) $fullname;
                    $display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                    $employee_name = utf8_encode($display_name);
    
                    $company_ = $this->getCompany($conn,$row['company']);
                    $department_ = $this->getDepartment($conn,$row['department']);
                    $position_ = $this->getPosition($conn,$row['position']);
    
                    $time1 = date_create($row['date_from']);
                    $time2 = date_create($row['date_to']);
                    $time_diff = date_diff($time1, $time2);
                    $tempHr = $time_diff->h;
                    $tempMn = $time_diff->i;
                    $tempHrLabel = ($tempHr == 1)? "Hour": "Hours";
                    $tempMnLabel = ($tempMn == 1)? "Minute": "Minutes";
                    $tempDuration = "{$tempHr} {$tempHrLabel} {$tempMn} {$tempMnLabel}";
                    if($tempHr > 0 && $tempMn > 0){
                        $tempDuration = "{$tempHr} {$tempHrLabel} {$tempMn} {$tempMnLabel}";
                    }elseif($tempHr > 0 && $tempMn == 0){
                        $tempDuration = "{$tempHr} {$tempHrLabel}";
                    }elseif($tempHr == 0 && $tempMn > 0){
                        $tempDuration = "{$tempMn} {$tempMnLabel}";
                    }
                    
                    $list['id'] = $row['id'];
                    $list['employee'] = $row['employee'];
                    $list['purpose'] = $row['purpose'];
                    $list['status'] = $row['status'];
                    $list['reference_no'] = $row['reference_no'];
                    $list['duration'] = $tempDuration;
                    $list['employee_name'] = $employee_name;
                    $list['date_from'] = date('F j, Y g:i a', strtotime($row['date_from']));
                    $list['date_to'] = date('F j, Y g:i a', strtotime($row['date_to']));
                    $list['company'] = utf8_encode(is_numeric($row['company']) ? $company_['description'] : $row['company']);
                    $list['department'] = utf8_encode(is_numeric($row['department']) ? $department_['description'] : $row['department']);
                    $list['position'] = utf8_encode(is_numeric($row['position']) ? $position_['name'] : $row['position']);
                    array_push($response['response_array'], $list);
                }
    
            }else{
                $list['status'] = false;
                array_push($response['response_array'], $list);
            }
            
            echo json_encode($response);
        }

        public function isHead($id) {
            $conn = $this->conn('gcchris');
            $sql = "SELECT head_id
                    FROM gcchris.tbldepartments
                    WHERE head_id = :id";
            $query = $conn->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            return $query->rowCount() > 0;
        }



        public function save_overtime(){
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            $conn = $this->conn();
            $response['response_array'] = array();
            $date = date('Y-m-d H:i:s');
            $year = date('y');
            $month = date('m');
            $ref_series = $this->getSeries($year,$month);
            $reference_no = 'OT'.$year.'-'.$month.'-'.$ref_series;
            $ref_yr = $year;
            $ref_series = $ref_series;
            $ref_month = $month;
            $user_id = $_POST['user_id'];
            $employee = $_POST['employee'];
            $company = $_POST['company'];
            $department = $_POST['department'];
            $position = $_POST['position'];//
            $requested_by = $_POST['requested_by'];
            $remarks = trim($_POST['remarks']);
            $purpose = trim($_POST['purpose']);
            $date_from = date("Y-m-d H:i:s", strtotime($_POST['date_from']));//
            $date_to = date("Y-m-d H:i:s", strtotime($_POST['date_to']));//
            $requested_at = $date;
            $created_by = $user_id;
            $created_at = $date;
            $status = 'Pending';

            $sth = $conn->query("INSERT INTO gcceforms.overtime (ref_yr,ref_series,ref_month,reference_no,status,employee,company,department,position,requested_by,requested_at,requested_remarks,purpose,date_from,date_to,created_by,created_at) VALUES ('$ref_yr','$ref_series','$ref_month','$reference_no','$status','$employee','$company','$department','$position','$requested_by','$requested_at','$remarks','$purpose','$date_from','$date_to','$created_by','$created_at')");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function update_overtime(){
            $conn = $this->conn();

            $response['response_array'] = array();

            $date = date('Y-m-d H:i:s');

            $id = $_POST['ot_id'];
            $user_id = $_POST['user_id'];
            $employee = $_POST['employee'];
            $company = $_POST['company'];
            $department = $_POST['department'];
            $position = $_POST['position'];
            $requested_by = $_POST['requested_by'];
            $requested_remarks = trim($_POST['requested_remarks']);
            $purpose = trim($_POST['purpose']);
            $date_from = date("Y-m-d H:i:s", strtotime($_POST['date_from']));
            $date_to = date("Y-m-d H:i:s", strtotime($_POST['date_to']));
            $updated_by = $user_id;
            $updated_at = $date;

            $sth = $conn->query("UPDATE gcceforms.overtime SET employee='$employee', company='$company', department='$department', position='$position', requested_by='$requested_by', requested_remarks='$requested_remarks', purpose='$purpose', date_from='$date_from', date_to='$date_to', updated_by='$updated_by', updated_at='$updated_at' WHERE id='$id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }













        public function approve_overtime(){
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            $conn = $this->conn();
            $date = date('Y-m-d H:i:s');
            $response['response_array'] = array();
            $ot_id = $_POST['ot_id'];
            $user_id = $_POST['user_id'];
            $emp_id = $_POST['emp_id'];

            $filenames = [];
            foreach($_FILES as $key => $files){
                $names = 'temp_'.$emp_id.'/'.$files['name'];
                array_push($filenames, $names);
            }
            $attachment = serialize($filenames);

            $approved_by = $user_id;
            $approved_at = $date;
            $attachment_image = $attachment;
            // $status = 'Pending';

            $status = 'Approved';

            $sth = $conn->query("UPDATE gcceforms.overtime SET status='$status', approved_by='$approved_by', approved_at='$approved_at', attachment_image='$attachment_image' WHERE id='$ot_id'");
            if ($sth) {
                foreach($_FILES as $key => $rs){
                    $uploadPath = '../uploads/files/images/overtime/temp_'.$emp_id;
                    if(!file_exists($uploadPath)){mkdir($uploadPath,0777,true);}

                    $_temp = $this->uploadFilev1($uploadPath, $rs);
                    if($_temp){
                        $response['upload_status'] = 'success';
                    }else{
                        $response['upload_status'] = 'failed';
                    }
                }
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        function uploadFilev1($uploadPath, $file){
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            $realpath = realpath($uploadPath);
            if(file_exists($realpath)){
                $moveFile = $realpath.'/'.basename($file['name']);
                if (move_uploaded_file($file["tmp_name"], $moveFile)) {   
                    if ($file['type'] == 'image/jpeg') { 
                        $this->createThumbnailv1($realpath . '/', $file);
                    }
                    return true;
                } else {
                    return false;
                }
            }else{
                return false;
            }
        }

        function createThumbnailv1($uploadPath,$file){
            $thumbnail = $uploadPath.'thumbnails/';
            if (!file_exists($thumbnail)) { 
                mkdir($thumbnail, 0777, true);
            }
    
            $orig_image = $uploadPath.basename($file["name"]); 
            $dest = $thumbnail.basename($file["name"]); 
    
            $this->make_thumbv1($orig_image, $dest,'80');
    
        }

        function make_thumbv1($src, $dest, $desired_width) {
            if (!file_exists($src)) {
                die("Error: File does not exist at path: $src");
            }
        
            $source_image = imagecreatefromjpeg($src);
            if (!$source_image) {
                die("Error: Unable to create image from $src. Possible reasons include invalid format or missing permissions.");
            }
        
            $exif = @exif_read_data($src);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 8:
                        $source_image = imagerotate($source_image, 90, 0);
                        break;
                    case 3:
                        $source_image = imagerotate($source_image, 180, 0);
                        break;
                    case 6:
                        $source_image = imagerotate($source_image, -90, 0);
                        break;
                }
            }
        
            $width = imagesx($source_image);
            $height = imagesy($source_image);
        
            $desired_height = floor($height * ($desired_width / $width));
        
            $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
        
            imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
        
            if (!imagejpeg($virtual_image, $dest)) {
                die("Error: Unable to save thumbnail to $dest.");
            }
        
            imagedestroy($source_image);
            imagedestroy($virtual_image);
        }


        // function saveFile($ca_id,$filename){
        //     $conn = $this->conn();
        //     $sth = $conn->query("INSERT INTO gcceforms.ca_attachments (ca_id,filename) VALUES ('$ca_id','$filename')");
        //     return $sth;
        // }








        // function checkFileExist($ca_id){
        //     $conn = $this->conn();
        //     $sth = $conn->query("SELECT id from gcceforms.ca_attachments where ca_id='$ca_id'");
            
        //     if ($sth) {
        //         $data = $sth->fetch(PDO::FETCH_ASSOC);
        
        //         if ($data) {
        //             return $data['id'];
        //         }
        //     }
            
        //     return false;
        // }

        // function updateFile($id,$filename){
        //     $conn = $this->conn();
        //     $sth = $conn->query("UPDATE gcceforms.ca_attachments SET filename='$filename' WHERE id='$id'");
        //     return $sth;
        // }





        public function disapprove_overtime(){
            $conn = $this->conn();

            $date = date('Y-m-d H:i:s');

            $response['response_array'] = array();
            
            $ot_id = $_POST['ot_id'];
            $user_id = $_POST['user_id'];
            $disapproved_by = $user_id;
            $disapproved_at = $date;
            $status = 'Disapproved';

            $sth = $conn->query("UPDATE gcceforms.overtime SET status='$status', disapproved_by='$disapproved_by', disapproved_at='$disapproved_at' WHERE id='$ot_id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function undo_disapprove_overtime(){
            $conn = $this->conn();

            $response['response_array'] = array();
            
            $ot_id = $_POST['ot_id'];
            $disapproved_by = '';
            $disapproved_at = '';
            $status = 'Pending';

            $sth = $conn->query("UPDATE gcceforms.overtime SET status='$status', disapproved_by='$disapproved_by', disapproved_at='$disapproved_at' WHERE id='$ot_id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function undo_approve_overtime(){
            $conn = $this->conn();

            $response['response_array'] = array();

            $ot_id = $_POST['ot_id'];
            $approved_by = '';
            $approved_at = '';
            $status = 'Pending';

            $sth = $conn->query("UPDATE gcceforms.overtime SET status='$status', approved_by='$approved_by', approved_at='$approved_at' WHERE id='$ot_id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function cancel_overtime(){
            $conn = $this->conn();

            $date = date('Y-m-d H:i:s');

            $response['response_array'] = array();

            $ot_id = $_POST['ot_id'];
            $user_id = $_POST['user_id'];
            $cancelled_remarks = $_POST['cancelled_remarks'];
            $cancelled_by = $user_id;
            $cancelled_at = $date;
            $status = 'Cancelled';

            $sth = $conn->query("UPDATE gcceforms.overtime SET status='$status', cancelled_by='$cancelled_by', cancelled_at='$cancelled_at', cancelled_remarks='$cancelled_remarks' WHERE id='$ot_id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function temp_upload_file(){

            $response['response_array'] = array();
            
            if (isset($_POST["emp_id"]) && $_POST["emp_id"]) {
                $emp_id = $_POST['emp_id'];
                $file = $_FILES['file'];

                $imagesPath = "../uploads/files/images/overtime/temp_{$emp_id}/";

                if (!file_exists($imagesPath)) {
                    $mkdir = mkdir($imagesPath, 0777, true);
                    if ($mkdir) {
                        $createFilePath = true;
                    }
                } else {
                    $createFilePath = true;
                }

                if ($createFilePath == false) {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                    $resultset["toastr_state"] = "warning";
                } else {

                    if (move_uploaded_file($file["tmp_name"], $imagesPath.basename($file["name"]))) {

                        // create thumbnail
                        $this->createThumbnail($imagesPath,$file);

                        $resultset["response"] = true;
                        $resultset["toastr_msg"] = "Upload image successful.";
                        $resultset["toastr_state"] = "success";
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Image upload to specific path failed!";
                        $resultset["toastr_state"] = "error";
                    }
                }

            } else {
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Employee data not found!";
                $resultset["toastr_state"] = "error";
            }

            array_push($response['response_array'], $resultset);

            echo json_encode($response);
        }

        function getCurrentUploadedFile($emp_id,$img_path){
            $resultset = array();
            $arrData = array();
            $imagesPath = "../uploads/files/images/overtime/temp_{$emp_id}";
            $thumbnailpath = "../uploads/files/images/overtime/temp_{$emp_id}/thumbnails";
            if(file_exists($imagesPath)){
                $tempFiles = scandir($imagesPath);
                if(is_array($tempFiles) && count($tempFiles) > 0){
                    foreach ($tempFiles as $key => $value) {
                        if($value !== "thumbnails" && $value !== "." && $value !== ".."){
                            $tempRow = array();
                            $tempRow["filename"] = $value;
                            $tempRow["current_image"] = "temp_{$emp_id}/{$value}";
                            $tempRow["image"] = $img_path."files/images/overtime/temp_{$emp_id}/{$value}";
                            $tempRow["thumbnail"] = $img_path."files/images/overtime/temp_{$emp_id}/thumbnails/{$value}";
                            $tempRow["isChecked"] = false;
                            $tempRow["isAddButton"] = false;
                            $arrData[] = $tempRow;
                        }
                    }
                }
            }

            if($arrData && count($arrData) > 0){
                $resultset["response"] = true;
                $resultset["rows"] = $arrData;
                $resultset["count"] = count($arrData);
            }else{
                $resultset["response"] = false;
            }

            return $resultset;
        }

        function createThumbnail($uploadPath,$file){
            $thumbnail = $uploadPath.'thumbnails/';
            if (!file_exists($thumbnail)) {
                mkdir($thumbnail, 0700, true);
            }

            $orig_image = $uploadPath.basename($file["name"]);
            $dest = $thumbnail.basename($file["name"]);

            $this->make_thumb($orig_image, $dest,'80');
        }

        function make_thumb($src, $dest, $desired_width) {
            $source_image = imagecreatefromjpeg($src);

            $exif = @exif_read_data($src);
            if(!empty($exif['Orientation'])) {
                switch($exif['Orientation']) {
                case 8:
                    $source_image = imagerotate($source_image,90,0);
                    break;
                case 3:
                    $source_image = imagerotate($source_image,180,0);
                    break;
                case 6:
                    $source_image = imagerotate($source_image,-90,0);
                    break;
                } 
            }

            $width = imagesx($source_image);
            $height = imagesy($source_image);

            $desired_height = floor($height * ($desired_width / $width));

            $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

            imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

            imagejpeg($virtual_image, $dest);
        }

        // function base_url($path){
        //     $serverHost = $_SERVER["HTTP_HOST"];
        //     //return "https://{$serverHost}/portaldev/".$path;
        //     return dir.'/'.$path;
        // }

        function getSeries($year,$month){
            $conn = $this->conn();

            $list = array();

            $sth = $conn->query("SELECT ref_series from gcceforms.overtime where ref_yr='$year' and ref_month='$month' order by ref_series asc");
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                array_push($list, $row['ref_series']);
            }

            if (sizeof($list) > 0) {
                foreach ($list as $arr) {
                    $x = $arr;
                }
                $series = intval($x) + 1;
                if (strlen($series) == 1) {
                    $series = '000' . $series;
                } else if (strlen($series) == 2) {
                    $series = '00' . $series;
                } else if (strlen($series) == 3) {
                    $series = '0' . $series;
                } else {
                    $series = $series;
                }
            } else {
                $series = '0001';
            }

            return $series;
        }

        function getCompany($conn,$id){
            $sth = $conn->query("SELECT id,description from gcchris.tblcompanies where id='$id' or description='$id'");
            $data = $sth->fetch(PDO::FETCH_ASSOC);
            return $data;
        }

        function getDepartment($conn,$id){
            $sth = $conn->query("SELECT id,description from gcchris.tbldepartments where id='$id' or description='$id' or code='$id'");
            $data = $sth->fetch(PDO::FETCH_ASSOC);
            return $data;
        }

        function getPosition($conn,$id){
            $sth = $conn->query("SELECT id,name from gcchris.tblposition where id='$id' or name='$id'");
            $data = $sth->fetch(PDO::FETCH_ASSOC);
            return $data;
        }

        function getUserName($employee){
            $conn = $this->conn();
            $sth = $conn->query("SELECT firstname,middlename,lastname,suffix from gccmaster.tblemployees where id='$employee'");
            $data = $sth->fetch(PDO::FETCH_ASSOC);

            $fullname = $this->getDisplayName($data);
            $tempFullname = (object) $fullname;
            $display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
            return utf8_encode($display_name);
        }

        function getEmployeeName($row){
            $fullname = $this->getDisplayName($row);
            $tempFullname = (object) $fullname;
            $display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
            return utf8_encode($display_name);
        }

        function getDisplayName($arrData = array()){
            if ($arrData) {
                $lastname = $arrData["lastname"];
                $firstname = $arrData["firstname"];
                $middlename = strtoupper($arrData["middlename"]);
                $suffix = strtoupper($arrData["suffix"]);

                $nSuffix = "";
                $nMiddleName = "";

                if ($suffix !== "" && ($suffix !== "N/A" && $suffix !== "NONE")) {
                    $nSuffix = $suffix;
                }
                if ($middlename !== "" && ($middlename !== "N/A" && $middlename !== "NONE")) {
                    $nMiddleName = $middlename;
                }

                $displayName1 = "";
                $displayName2 = "";

                $nMiddleName = trim($nMiddleName);
                $nMiddleName = substr($nMiddleName, 0, 1);
                $nMiddleName = ($nMiddleName) ? "{$nMiddleName}." : "";
                if ($nMiddleName && $nSuffix) {
                    $displayName1 = "{$lastname}, {$firstname} {$nMiddleName} {$nSuffix}";
                    $displayName2 = "{$firstname} {$nMiddleName} {$lastname} {$nSuffix}";
                } else if ($nSuffix) {
                    $displayName1 = "{$lastname}, {$firstname} {$nSuffix}";
                    $displayName2 = "{$firstname} {$lastname} {$nSuffix}";
                } else if ($nMiddleName) {
                    $displayName1 = "{$lastname}, {$firstname} {$nMiddleName}";
                    $displayName2 = "{$firstname} {$nMiddleName} {$lastname}";
                } else {
                    $displayName1 = "{$lastname}, {$firstname}";
                    $displayName2 = "{$firstname} {$lastname}";
                }

                $displayName1 = strtoupper($displayName1);
                $displayName2 = strtoupper($displayName2);

                $data = array();
                $data["display_name_0"] = $displayName1;
                $data["display_name_1"] = $displayName2;

                return $data;
            } else {
                return false;
            }
        }



    }

?>