<?php
    class Gcceforms_sa_m extends Dbase{

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
                $list['_position_id'] = is_numeric($row['position']) ? $row['position'] : $check_null_position;
                $list['company_desc'] = utf8_encode(is_numeric($row['company_id']) ? $company_['description'] : $row['company_id']);
                $list['department_desc'] = utf8_encode(is_numeric($row['department_id']) ? $department_['description'] : $row['department_id']);
                $list['position_name'] = utf8_encode(is_numeric($row['position']) ? $position_['name'] : $row['position']);
                array_push($response['employee_array'], $list);
            }

            // ---------------------
            $response['array_file_under'] = array();
            $sth = $conn->query("SELECT id, description FROM gcchris.tblcompanies ORDER BY description ASC");
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $list_fileunder['id'] = $row['id'];
                $list_fileunder['description'] = $row['description'];
                array_push($response['array_file_under'], $list_fileunder);
            }

            // ---------------------
            $response['array_department'] = array();
            $sth = $conn->query("SELECT id, description FROM gcchris.tbldepartments ORDER BY description ASC");
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $list_depart['id'] = $row['id'];
                $list_depart['description'] = $row['description'];
                array_push($response['array_department'], $list_depart);
            }

            // ---------------------
            $response['array_inventory'] = array();
            $sth = $conn->query("SELECT id, inventorycode, uom, item_description, CONCAT(inventorycode,' | ', item_description) AS item FROM gccis.items ORDER BY inventorycode ASC");
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $list_invent['id'] = $row['id'];
                $list_invent['inventorycode'] = $row['inventorycode'];
                $list_invent['item'] = utf8_encode($row['item']);
                $list_invent["uom"] = $row["uom"];
                $list_invent["description"] = utf8_encode($row["item_description"]);
                array_push($response['array_inventory'], $list_invent);
            }

            // ---------------------
            $response['array_asset'] = array();
            $sth = $conn->query("SELECT id,assetacode,isComponent,isReleased,uom,name, CONCAT(assetacode,' | ', name) AS asset FROM gccasset.assets WHERE isComponent='0' OR isReleased='0' OR assetacode!=' ' ORDER BY assetacode ASC");
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $list_asset['id'] = $row['id'];
                $list_asset['assetacode'] = $row['assetacode'];
                $list_asset['asset'] = utf8_encode($row['asset']);
                $list_asset["uom"] = $row["uom"];
                $list_asset["description"] = utf8_encode($row["name"]);
                array_push($response['array_asset'], $list_asset);
            }

            // ---------------------
            $response['array_location'] = array();
            $sth = $conn->query("SELECT id, location FROM gccasset.location ORDER BY location ASC");
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $list_location['id'] = $row['id'];
                $list_location['location'] = utf8_encode($row['location']);
                array_push($response['array_location'], $list_location);
            }

            // ---------------------
            $response['array_vehicle'] = array();
            $sth = $conn->query("SELECT id, CONCAT(plateno,' | ',name) AS vehicle FROM gccasset.vehicles WHERE isCompo='0' AND is_borrowed='0' ORDER BY plateno ASC");
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $list_vehicle['id'] = $row['id'];
                $list_vehicle['vehicle'] = utf8_encode($row['vehicle']);
                array_push($response['array_vehicle'], $list_vehicle);
            }

            // ---------------------
            $response['array_driver'] = array();
            $sth = $conn->query("SELECT id, firstname, middlename, lastname, suffix, position FROM gccmaster.tblemployees WHERE employee_status='Active' ORDER BY firstname ASC");
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                if(is_numeric($row['position'])){
                    $driver = mb_strtoupper($this->getPosition($conn,$row['position'])['name']);
                }else{
                    $driver = mb_strtoupper($row['position']);
                }
                if(strpos($driver, "DRIVER") !== false || strpos($driver, 'BACKHOE') !== false || strpos($driver, 'TRACTOR') !== false || strpos($driver, 'GRADER') !== false || strpos($driver, 'ROLLER') !== false || strpos($driver, 'PAYLOADER') !== false||strpos($driver, 'BULLDOZER') !== false||strpos($driver, 'CRANE') !== false||strpos($driver, 'MCC') !== false || $row['id']==140){
                    $fullname = $this->getDisplayName($row);
                    $tempFullname = (object) $fullname;
                    $display_name = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                    $employee_name = utf8_encode($display_name);
    
                    $list_driver["id"] = $row["id"];
                    $list_driver["text"] = $employee_name;
                    array_push($response['array_driver'], $list_driver);
                }
            }

            echo json_encode($response);
        }

        public function get_content(){
            $conn = $this->conn();

            $response['response_array'] = array();

            $user_id = $_POST['user_id'];

            $sth = $conn->query("SELECT * FROM gcceforms.shipping_body_temp WHERE user_id='$user_id'");
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $list['id'] = $row['id'];
                $list['asset_id'] = $row['asset_id'];
                $list['stock_code'] = $row['stock_code'];
                $list['quantity'] = $row['quantity'];
                $list['uom'] = $row['uom'];
                $list['description'] = $row['description'];
                $list['item_purpose'] = $row['item_purpose'];
                $list['cat'] = $row['cat'];
                $list['item'] = $row['stock_code'].' | '.$row['description'];
                array_push($response['response_array'], $list);
            }

            echo json_encode($response);
        }

        public function clear_content(){
            $conn = $this->conn();

            $response['response_array'] = array();

            $user_id = $_POST['user_id'];

            $sth = $conn->query("DELETE FROM gcceforms.shipping_body_temp WHERE user_id='$user_id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function delete_content(){
            $conn = $this->conn();

            $response['response_array'] = array();

            $id = $_POST['id'];

            $sth = $conn->query("DELETE FROM gcceforms.shipping_body_temp WHERE id='$id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }
        
        public function get_sa_data(){
            $conn = $this->conn();

            $response['response_array'] = array();

            $date = date("Y-m-d", strtotime("-1 year"));

            $sth = $conn->query("SELECT DISTINCT a.cat, a.id as shipp_id, a.status, a.priority, a.reference_no, d.firstname, d.middlename, d.lastname, d.suffix, c.description AS file_under, a.company_to, 
                                                 a.department_to, a.ship_to, b.description, a.ship_date, b.id as shipp_body_id
                                 FROM gcceforms.shipping a 
                                 LEFT JOIN gcchris.tblcompanies c
                                 ON a.company_from = c.id
                                 LEFT JOIN gcceforms.shipping_body b
                                 ON a.id = b.shipping_id
                                 LEFT JOIN gccmaster.tblemployees d
                                 ON a.ship_to = d.id
                                 WHERE a.created_dt >='$date' AND a.status!='Cancelled' ORDER BY a.ship_date desc");
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {

                $list['shipp_body_id'] = $row['shipp_body_id'];
                $list['shipp_id'] = $row['shipp_id'];
                $list['status'] = $row['status'];
                $list['priority'] = $row['priority'];
                $list['reference_no'] = $row['reference_no'];
                $list['file_under'] = $row['file_under'];
                $list['company_to'] = $row['company_to'];
                $list['department_to'] = $row['department_to'];
                $list['ship_to'] = $row['cat'] == 'in' ? $this->getUserName($conn,$row['ship_to']) : utf8_encode($row['ship_to']);
                $list['description'] = utf8_encode($row['description']);
                $list['ship_date'] = date('F j, Y g:i A',strtotime($row['ship_date']));
                array_push($response['response_array'], $list);
            }

            echo json_encode($response);
        }

        public function view_shipping_details(){
            $conn = $this->conn();

            $response['response_array'] = array();

            $id = $_POST['id'];

            $sth = $conn->query("SELECT a.*, f.name AS vehicle_name, d.position, b.description AS company, c.description AS department, d.firstname, d.middlename,d.lastname, d.suffix, e.location, f.plateno
                                 FROM gcceforms.shipping a
                                 LEFT JOIN gcchris.tblcompanies b
                                 ON b.id = a.company_from
                                 LEFT JOIN gcchris.tbldepartments c
                                 ON c.id = a.department_from
                                 LEFT JOIN gccmaster.tblemployees d
                                 ON d.id = a.requested_by
                                 LEFT JOIN gccasset.location e
                                 ON e.id = a.area_id
                                 LEFT JOIN gccasset.vehicles f
                                 ON f.id = a.vehicle_id
                                 WHERE a.id='$id'");

            if ($sth->rowCount() > 0) {

                $row = $sth->fetch(PDO::FETCH_ASSOC);
                
                $cat = $row['cat'];
                $driver = $row['driver'];

                $driver_name = is_numeric($driver) ? $this->getUserName($conn,$driver) : '';

                $list['reference_no'] = $row['reference_no'];
                $list['priority'] = $row['priority'];
                $list['position'] = $row['position'];
                $list['company'] = $row['company'];
                $list['department'] = $row['department'];
                $list['vehicle_name'] = $row['vehicle_name'];
                $list['status'] = $row['status'];
                $list['last_edited_by'] = $row['last_edited_by'] ? $row['last_edited_by'] : '';
                $list['last_edited_dt'] = date('F j, Y g:i A',strtotime($row['last_edited_dt']));
                $list['created_by'] = $row['created_by'];
                $list['requested_by'] = $this->getUserName($conn,$row['requested_by']);
                $list['created_dt'] = date('F j, Y g:i A',strtotime($row['created_dt']));
                $list['approved_by'] = $row['approved_by'];
                $list['approved_dt'] = date('F j, Y g:i A',strtotime($row['approved_dt']));
                $list['disapproved_by'] = $row['disapproved_by'];
                $list['disapproved_dt'] = date('F j, Y g:i A',strtotime($row['disapproved_dt']));
                $list['cancelled_by'] = $row['cancelled_by'];
                $list['cancelled_dt'] = date('F j, Y g:i A',strtotime($row['cancelled_dt']));
                $list['cancelled_remarks'] = $row['cancelled_remarks'];
                $list['received_by'] = $row['received_by'];
                $list['received_dt'] = date('F j, Y g:i A',strtotime($row['received_dt']));
                $list['received_remarks'] = $row['received_remarks'];
                $list['created_id'] = $row['created_id'];
                $list['last_edited_id'] = $row['last_edited_id'];
                $list['others_remarks'] = $row['others_remarks'];
                $list['is_service'] = $row['is_service'];
                $list['waybill'] = $row['waybill'];
                $list['ship_date'] = date('F j, Y g:i A',strtotime($row['ship_date']));
                $list['company'] = $row['company'];
                $list['transporter'] = utf8_encode($row['transporter']);
                $list['cat'] = $cat;
                $list['ship_to_name'] = $cat == 'in' ? $this->getUserName($conn,$row['ship_to']) : $row['ship_to'];
                $list['plateno'] = $driver ? $row['plateno'] : "N/A";
                $list['driver_name'] = $driver ? $driver_name : "N/A";
                $list['location'] = $cat == 'in' ? $row['location'] : $row['company_to'].", ".$row['department_to'];
                $list['ship_to_company'] = $cat == 'in' ? '' : $row['company_to']."\n".$row['department_to'];
                $list['department_to'] = $row['department_to'];
                $list['company_to'] = $row['company_to'];
                $list['ship_to_address'] = $row['ship_to_address'];
                $list['content_table'] = $this->shipping_content_table($id);
                
            } else {
                $list['status'] = 'empty';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function shipping_content_table($id){
            $conn = $this->conn();

            $response['array'] = array();

            $sth = $conn->query("SELECT * FROM gcceforms.shipping_body WHERE shipping_id='$id'");
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $list['stock_code'] = utf8_encode($row['stock_code']);
                $list['item_purpose'] = utf8_encode($row['item_purpose']);
                $list['description'] = utf8_encode($row['description']);
                $list['quantity'] = $row['quantity'];
                $list['id'] = $row['id'];
                array_push($response['array'], $list);
            }

            return $response;
        }

        public function qr_scanned_receive(){
            $conn = $this->conn();

            $response['response_array'] = array();

            $reference_no = $_POST['reference_no'];

            $sth = $conn->query("SELECT * FROM gcceforms.shipping WHERE reference_no='$reference_no'");
            if ($sth->rowCount() > 0) {

                $row = $sth->fetch(PDO::FETCH_ASSOC);

                $list['id'] = $row['id'];
                $list['reference_no'] = $row['reference_no'];
                $list['status'] = $row['status'];
                $list['received_remarks'] = $row['received_remarks'];
                $list['received_by'] = $row['received_by'];
                $list['received_dt'] = date('F j, Y g:i A',strtotime($row['received_dt']));
                $list['disapproved_by'] = $row['disapproved_by'];
                $list['disapproved_dt'] = date('F j, Y g:i A',strtotime($row['disapproved_dt']));
                $list['cancelled_by'] = $row['cancelled_by'];
                $list['cancelled_dt'] = date('F j, Y g:i A',strtotime($row['cancelled_dt']));
                $list['content_table'] = $this->shipping_content_table($row['id']);
            } else {
                $list['status'] = 'not exist';
                $list['reference_no'] = $reference_no;
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function disapprove_shipping(){
            $conn = $this->conn();

            $date = date('Y-m-d H:i:s');

            $response['response_array'] = array();

            $sa_id = $_POST['sa_id'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $disapproved_by = $firstname.' '.$lastname;
            $disapproved_dt = $date;
            $status = 'Disapproved';

            $sth = $conn->query("UPDATE gcceforms.shipping SET disapproved_by='$disapproved_by', disapproved_dt='$disapproved_dt', status='$status' WHERE id='$sa_id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function undo_disapprove_shipping(){
            $conn = $this->conn();

            $response['response_array'] = array();

            $sa_id = $_POST['sa_id'];
            $status = 'Pending';

            $sth = $conn->query("UPDATE gcceforms.shipping SET status='$status' WHERE id='$sa_id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function cancel_shipping(){
            $conn = $this->conn();

            $response['response_array'] = array();

            $date = date('Y-m-d H:i:s');

            $sa_id = $_POST['sa_id'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $cancelled_remarks = $_POST['cancelled_remarks'];
            $cancelled_by = $firstname.' '.$lastname;
            $cancelled_dt = $date;
            $status = 'Cancelled';

            $sth = $conn->query("UPDATE gcceforms.shipping SET cancelled_dt='$cancelled_dt', cancelled_by='$cancelled_by', cancelled_remarks='$cancelled_remarks', status='$status' WHERE id='$sa_id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function undo_cancel_shipping(){
            $conn = $this->conn();

            $response['response_array'] = array();

            $sa_id = $_POST['sa_id'];
            $status = 'Pending';

            $sth = $conn->query("UPDATE gcceforms.shipping SET status='$status' WHERE id='$sa_id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function approve_shipping(){
            $conn = $this->conn();

            $date = date('Y-m-d H:i:s');

            $response['response_array'] = array();

            $sa_id = $_POST['sa_id'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $approved_by = $firstname.' '.$lastname;
            $approved_dt = $date;
            $status = 'Approved';

            $sth = $conn->query("UPDATE gcceforms.shipping SET approved_dt='$approved_dt', approved_by='$approved_by', status='$status' WHERE id='$sa_id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function undo_approve_shipping(){
            $conn = $this->conn();

            $date = date('Y-m-d H:i:s');

            $response['response_array'] = array();

            $sa_id = $_POST['sa_id'];
            $status = 'Pending';

            $sth = $conn->query("UPDATE gcceforms.shipping SET status='$status' WHERE id='$sa_id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function receive_shipping(){
            $conn = $this->conn();

            $date = date('Y-m-d H:i:s');

            $response['response_array'] = array();

            $sa_id = $_POST['sa_id'];
            $received_remarks = $_POST['received_remarks'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $received_by = $firstname.' '.$lastname;
            $received_dt = $date;
            $status = 'Received';

            $sth = $conn->query("UPDATE gcceforms.shipping SET received_remarks='$received_remarks', received_dt='$received_dt', received_by='$received_by', status='$status' WHERE id='$sa_id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function undo_receive_shipping(){
            $conn = $this->conn();

            $response['response_array'] = array();

            $sa_id = $_POST['sa_id'];
            $status = 'Approved';

            $sth = $conn->query("UPDATE gcceforms.shipping SET status='$status' WHERE id='$sa_id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function save_qr_received(){
            $conn = $this->conn();

            $date = date('Y-m-d H:i:s');

            $response['response_array'] = array();

            $sa_id = $_POST['sa_id'];
            $received_remarks = $_POST['received_remarks'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $isIncomplete = $_POST['isIncomplete'];
            $contentData = json_decode($_POST['contentData'], true); 
            $received_by = $firstname.' '.$lastname;
            $received_dt = $date;
            $status = 'Received';

            $sth = $conn->query("UPDATE gcceforms.shipping SET received_remarks='$received_remarks', received_dt='$received_dt', received_by='$received_by', status='$status' WHERE id='$sa_id'");
            if ($sth) {
                foreach ($contentData as $row) {
                    if ($row['isChecked'] == 'true') {
                        $content_id = $row['content_id'];
                        $conn->query("UPDATE gcceforms.shipping_body SET is_received='1' WHERE id='$content_id'");
                    }
                }
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function add_item_modal(){
            $conn = $this->conn();

            $date = date('Y-m-d H:i:s');

            $response['response_array'] = array();

            $user_id = $_POST['user_id'];
            $stock_code = $_POST['stock_code'];
            $quantity = $_POST['quantity'];
            $uom = $_POST['uom'];
            $description = $_POST['description'];
            $item_purpose = $_POST['item_purpose'];
            $cat = $_POST['cat'];
            $asset_id = '';

            $sth = $conn->query("INSERT INTO gcceforms.shipping_body_temp (user_id,stock_code,quantity,uom,description,item_purpose,cat,asset_id) VALUES ('$user_id','$stock_code','$quantity','$uom','$description','$item_purpose','$cat','$asset_id')");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        public function update_content(){
            $conn = $this->conn();

            $response['response_array'] = array();

            $id = $_POST['id'];
            $user_id = $_POST['user_id'];
            $stock_code = $_POST['stock_code'];
            $quantity = $_POST['quantity'];
            $uom = $_POST['uom'];
            $description = $_POST['description'];
            $item_purpose = $_POST['item_purpose'];
            $cat = $_POST['cat'];

            $sth = $conn->query("UPDATE gcceforms.shipping_body_temp SET stock_code='$stock_code', quantity='$quantity', uom='$uom', description='$description', item_purpose='$item_purpose', cat='$cat' WHERE id='$id'");
            if ($sth) {
                $list['status'] = 'success';
            } else {
                $list['status'] = 'error';
            }

            array_push($response['response_array'], $list);

            echo json_encode($response);
        }

        function getUserName($conn,$id){
            $sth = $conn->query("SELECT firstname,middlename,lastname,suffix from gccmaster.tblemployees where id='$id'");
            $data = $sth->fetch(PDO::FETCH_ASSOC);

            if ($sth->rowCount() > 0) {
                $fullname = $this->getDisplayName($data);
                $tempFullname = (object) $fullname;
                $display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
            } else {
                $display_name = "No Assigned Name";
            }
            
            return utf8_encode($display_name);
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