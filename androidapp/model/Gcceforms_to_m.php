<?php
    class Gcceforms_to_m extends Dbase{
        
        public function get_to_data(){
            $conn = $this->conn();

            $response['data_array'] = array();

            $temp = strtotime("-1 year", time());
            $check = date("Y-m-d", $temp);

            // $start = $_POST['start'];
            // $end = $_POST['end'];

            // if ($start && $end) {

            // } else {

            // }

            $sth = $conn->prepare("SELECT DISTINCT a.id, a.status, a.is_service, a.driver_id, a.vehicle_id, a.reference_no, a.driver, a.company, a.is_commute,
                                                     a.is_personal, a.is_others, a.others_remarks
                                   FROM gcceforms.travel_order AS a
                                   LEFT JOIN gcceforms.travel_destination AS td 
                                        ON a.id = td.travel_order_id
                                   where a.created_dt >= '$check' and a.status != 'Cancelled' order by a.id desc limit 10");
            $sth->execute();

            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];

                $list['id'] = $id;
                $list['status'] = $row['status'];
                $list['company'] = $row['company'];
                $list['is_service'] = $row['is_service'];
                $list['reference_no'] = $row['reference_no'];
                $list['personel'] = $this->getPersonel($conn, $id);
                $list['destination'] = $this->getDestination($conn, $id, 'destination');  
                $list['from_to'] = $this->getDestination($conn, $id, 'from_to'); 

                if ($row['is_service'] == '1') {
                    $list['driver'] = $this->getDriver($conn, $row['driver_id'], $row['vehicle_id'], $row['driver'], 'driver');
                    $list['vehicle'] = $this->getDriver($conn, $row['driver_id'], $row['vehicle_id'], $row['driver'], 'vehicle');
                } else if ($row['is_commute'] == '1') {
                    $list['driver'] = 'Commute';
                    $list['vehicle'] = '';
                } else if ($row['is_personal'] == '1') {
                    $list['driver'] = 'Personal Vehicle';
                    $list['vehicle'] = '';
                } else if ($row['is_others'] == '1') {
                    $list['driver'] = utf8_encode($row['others_remarks']);
                    $list['vehicle'] = '';
                }
                

                array_push($response['data_array'], $list);
            }

            echo json_encode($response);
        }

        function getDestination($conn, $id, $show){
            $rowDestination = array();
            $destinationDateTime = array();
            $queryDestination = $conn->prepare("SELECT destination,date_from,date_to from gcceforms.travel_destination where travel_order_id='$id'");
            $queryDestination->execute();
            if ($queryDestination->rowcount() > 0) {
                $des_num = 0;
                while ($vx = $queryDestination->fetch(PDO::FETCH_ASSOC)) {
                    $des_num++;
                    $rowDestination[] = utf8_encode($vx['destination']);
                    $x = explode(' ', $vx['date_from']);
                    $y = explode(' ', $vx['date_to']);

                    
                    if ($des_num == 1) {
                        $date_start = date("M d, Y g:i A", strtotime($vx['date_from']));
                        $z = explode(' ', $vx['date_from']);
                    } else {
                        $date_start = date("M d, Y g:i A", strtotime($x[1]));
                        $z = explode(' ', $vx['date_from']);
                    }

                    if ($z[0] != $x[0]) {
                        $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                    } else if ($x[0] == $y[0]) {
                        $date_end = date("g:i A", strtotime($y[1]));
                    } else {
                        $date_end = date("M d, Y", strtotime($y[0])) . ' ' . date("g:i A", strtotime($y[1]));
                    }

                    $date = $date_start . "-" . $date_end;
                    array_push($destinationDateTime, $date);
                }
            }

            $final  = array();

            foreach ($destinationDateTime as $current) {
                if ( ! in_array($current, $final)) {
                    $final[] = $current;
                }
            }

            if ($show == 'destination') {
                return $rowDestination;
            } else if ($show == 'from_to'){
                return $final;
            }
        }

        function getDriver($conn, $driver_id, $vehicle_id, $driver, $show){
            $rowDriver = "";
            $rowVehicle_ = "";
            $rowVehiclePlate = "";
            $rowVehicleDesc = "";
            $queryDriver = $conn->prepare("SELECT * FROM gccmaster.tblemployees where id = '$driver_id'");
            $queryDriver->execute();

            if ($queryDriver->rowCount() == 1) {
                $rowData = $queryDriver->fetch(PDO::FETCH_ASSOC);
                $fullname = $this->getDisplayName($rowData);
                $tempFullname = (object)$fullname;
                $display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                if ($display_name) {
                    $rowDriver = $display_name;
                } else {
                    $rowDriver = $driver;
                }
            }

            $queryVehicle = $conn->prepare("SELECT plateno,description,name FROM gccasset.vehicles where id = '$vehicle_id'");
            $queryVehicle->execute();

            if ($queryVehicle->rowCount() == 1) {
                $rowVehicle = $queryVehicle->fetch(PDO::FETCH_ASSOC);
                $rowVehiclePlate = $rowVehicle['plateno'];
                $rowVehicleDesc = $rowVehicle['name'];

                $rowVehicle_ = $rowVehiclePlate. ' ' .$rowVehicleDesc; 
            }

            if ($show=='driver') {
                return utf8_encode($rowDriver);
            } else if ($show=='vehicle'){
                return utf8_encode($rowVehicle_);
            }
        }

        function getPersonel($conn, $id){
            $rowPersonnel = array();
            $sth = $conn->prepare("SELECT tp.employee_id, te.firstname, te.middlename, te.lastname, te.suffix
                                   FROM gcceforms.travel_personnel AS tp
                                   LEFT JOIN gccmaster.tblemployees AS te 
                                        ON te.id = tp.employee_id
                                   where tp.travel_order_id = '$id'");
            $sth->execute();
            if ($sth->rowCount() > 0) {

                while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {

                    $fullname = $this->getDisplayName($row);
                    $tempFullname = (object)$fullname;
                    $display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";
                    $rowPersonnel[] = utf8_encode($display_name);
                }
            }
            return $rowPersonnel;
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

        public function get_collections(){
            $conn = $this->conn();

            // getCompanyCollection()
            $response['collection_company'] = array();
            $queryCompany = $conn->query("SELECT `id`,`description` FROM gcchris.tblcompanies ORDER BY `description` ASC");
            if ($queryCompany->rowCount() > 0) {
                while ($row = $queryCompany->fetch(PDO::FETCH_ASSOC)) {
                    $list = array();
                    $list["id"] = $row["id"];
                    $list["text"] = utf8_encode($row["description"]);
                    array_push($response['collection_company'], $list);
                }
            }

            // getDepartmentCollection()
            $response['collection_department'] = array();
            $queryDepartment = $conn->query("SELECT `id`,`description` FROM gcchris.tbldepartments ORDER BY `description` ASC");
            if ($queryDepartment->rowCount() > 0) {
                while ($row = $queryDepartment->fetch(PDO::FETCH_ASSOC)) {
                    $list2 = array();
                    $list2["id"] = $row["id"];
                    $list2["text"] = utf8_encode($row["description"]);
                    array_push($response['collection_department'], $list2);
                }
            }

            // getEmployeeCollection()
            $response['collection_employee'] = array();
            $queryEmployee = $conn->query("SELECT DISTINCT e.id, e.firstname, e.middlename, e.lastname, e.suffix, p.name as position_name, e.position as emp_position
                                           FROM gccmaster.tblemployees as e
                                           LEFT JOIN gcchris.tblposition AS p
                                            ON p.id = e.position OR p.name = e.position
                                           WHERE e.employee_status='Active' ORDER BY e.firstname ASC");
            if ($queryEmployee->rowCount() > 0) {
                while ($row = $queryEmployee->fetch(PDO::FETCH_ASSOC)) {
                    $list3 = array();
                    $tempRs = (array)$row;
                    $fullname = $this->getDisplayName($tempRs);
                    $tempFullname = (object)$fullname;
                    $display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1 : "No Assigned Name";

                    $list3["id"] = $row["id"];
                    $list3["position_name"] = utf8_encode( $row["position_name"] ? $row["position_name"] : $row["emp_position"]);
                    $list3["text"] = utf8_encode($display_name);
                    array_push($response['collection_employee'], $list3);
                }
            }

            // getVehicleCollection()
            $response['collection_vehicle'] = array();
            $queryVehicle = $conn->query("SELECT  id, CONCAT(gen_code, ' | ',plateno,' | ',name) AS vehicle FROM gccasset.vehicles WHERE isCompo='0' 
                    AND status2 NOT IN ('archived', 'damage', 'junk', 'lost', 'sold') ORDER BY plateno ASC");
            if ($queryVehicle->rowCount() > 0) {
                while ($row = $queryVehicle->fetch(PDO::FETCH_ASSOC)) {
                    $list4 = array();
                    $list4["id"] = $row["id"];
                    $list4["text"] = utf8_encode($row["vehicle"]);
                    array_push($response['collection_vehicle'], $list4);
                }
            }

            // driver()
            $response['collection_driver'] = array();
            $queryDriver = $conn->query("SELECT  e.id, e.firstname, e.middlename, e.lastname, e.suffix, e.position, c.description as company FROM gccmaster.tblemployees as e LEFT JOIN gcchris.tblcompanies as c ON c.id=e.company_id WHERE e.employee_status='Active' ORDER BY e.firstname ASC");
            if ($queryDriver->rowCount() > 0) {
                while ($row = $queryDriver->fetch(PDO::FETCH_ASSOC)) {
                    if(is_numeric($row['position'])){
                        $driver = mb_strtoupper($this->getPosition($conn, $row['position']));
                    }else{
                        $driver = mb_strtoupper($row['position']);
                    }
                    if (strpos($driver, "DRIVER") !== false || strpos($driver, 'BACKHOE') !== false || strpos($driver, 'TRACTOR') !== false || strpos($driver, 'GRADER') !== false || strpos($driver, 'ROLLER') !== false || strpos($driver, 'PAYLOADER') !== false || strpos($driver, 'BULLDOZER') !== false || strpos($driver, 'CRANE') !== false || strpos($driver, 'MCC') !== false || strpos($driver, 'OPERATOR') !== false) {
                        $list5 = array();
                        $tempRs = (array)$row;
                        $fullname = $this->getDisplayName($tempRs);
                        $tempFullname = (object)$fullname;
                        $tempCompany = $row['company'];
                        $display_name = ($tempFullname->display_name_1) ? $tempFullname->display_name_1." - ".$tempCompany : "No Assigned Name";

                        $list5["id"] = $row["id"];
                        $list5["text"] = utf8_encode($display_name);
                        array_push($response['collection_driver'], $list5);
                    }
                }
            }

            echo json_encode($response);
        }

        function getPosition($conn, $position){
            $sth = $conn->prepare("SELECT name from gcchris.tblposition where id = '$position'");
            $sth->execute();
            $row = $sth->fetch(PDO::FETCH_ASSOC);
            return $row['name'];
        }



    }

?>