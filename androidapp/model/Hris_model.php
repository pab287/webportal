<?php 
    class Hris_model extends Dbase{
        protected $employeeTable = "gccmaster.tblemployees";
        protected $userTable = "gccmaster.tblusers";
        protected $companyTable = "gcchris.tblcompanies";
        protected $departmentTable = "gcchris.tbldepartments";
        protected $positionTable = "gcchris.tblposition";
        protected $employeeDependentsTable = "gcchris.tbldependents";
        protected $employeeEducationTable = "gcchris.tbleducations";
        protected $employeeLicensureTable = "gcchris.tbllicenses";
        protected $employeeDriverLicenseTable = "gcchris.tbldriverlicense";
        protected $employeeWorkExperienceTable = "gcchris.tblworkxps";
        protected $employeeAwardsTable = "gcchris.tblawards";
        protected $employeeSkillsTable = "gcchris.tblskills";
        protected $employeeOrganizationTable = "gcchris.tblorganizations";
        protected $employeeTrainingsTable = "gcchris.tbltrainings";
        protected $employeeReferencesTable = "gcchris.tblreferences";
        protected $employeeMedicalHistoryTable = "gcchris.tblmedrecs";
        protected $employeeLegalHistoryTable = "gcchris.tbllegalrecs";
        protected $employeeOffensesTable = "gcchris.tbloffcoms";
        protected $employeeSalaryTable = "gcchris.tblsalaries";
        protected $employeeCashAdvanceTable = "gcceforms.cash_advance";
        protected $employeeDocumentsTable = "gcchris.tbldocuments";
        protected $employeePerformanceTable = "gcchris.tblperfomance_eval_docs";
        protected $tblArchivedItems = "gccmaster.archived_items";
        protected $tblProbiCalendar = "gcchris.tblprobicalendar";
        protected $tblEmployeesCompanyHistory = "gccmaster.tblemployees_company_history";
        protected $payrollTypeTable = "payroll.payroll_type";
        protected $tblReturnToWork = "gcceforms.return_to_work";
        protected $tblPersonnelLocation = "gcctimeutility.personnel_locations";
        protected $tblPersonnel = "gcctimeutility.personnel";
        protected $tblAppLocationSites = "gcctimeutility.app_location_sites";
        protected $tblAllowances = "gcchris.allowances";
        protected $locationTable = "gcctimeutility.location";
        protected $licenseTable = "gcchris.tbl_license";

        public function get201(){
            $data = array();
            $post = $_POST;

            $main = $this->getEmployee($post['id']);
            $dependents = $this->getDependent($post['id']);
            $licences = $this->getLicenses($post['id']);
            $driverLicense = $this->getDriverLicense($post['id']);
            $experiences = $this->getEmployeeExperiencesForPDS($post['id']);
            $awards = $this->getAwards($post['id']);
            $skills = $this->getSkills($post['id']);
            $organizations = $this->getOrganizations($post['id']);
            $training = $this->getTrainings($post['id']);
            $references = $this->getReferences($post['id']);
            $medicals = $this->getMedicals($post['id']);
            $legal = $this->getLegals($post['id']);
            $offenses = $this->getOffenses($post['id']);
            $accountability = $this->getEmployeeAccountability($post['id']);
            $educations = $this->getEducations($post['id']);
            $rtw = $this->getRtw($post['id']);
            $salaries = $this->getSalaries($post['id']);
            $ifDriver = $this->getIfDriver($post['id']);
            $driver = $this->getDriver($ifDriver->position, $post['id']);
            $company_logo = $this->getLogo($post['id']);
            $station = $this->getStation($main->biometricno['id']);
            $personaldata = $this->getPersonalInformation($post['id']);
            $additionalinfo = $this->getAdditionalInformation($post['id']);
            $jobdescription = $this->getJobDescription($post['id']);
            $offcoms = $this->getOffComs($post['id']);
            $salhis = $this->getSalHis($post['id']);
            $empInfo = $this->getEmpInfo($post['id']);
            $empStations = $this->getEmpStations($post['id']);
            $questions = $this->getQuestions($post['id']);

            return 
                json_encode(
                    array (
                        'main' => $main,
                        'personaldata' => $personaldata,
                        'additionalinfo' => $additionalinfo,
                        'dependents' => $dependents,
                        'licences' => $licences,
                        'driverLicense' => $driverLicense,
                        'experiences' => $experiences,
                        'awards' => $awards,
                        'skills' => $skills,
                        'organizations' => $organizations,
                        'training' => $training,
                        'references' => $references,
                        'medicals' => $medicals,
                        'legal' => $legal,
                        'offenses' => $offenses,
                        'accountability' => $accountability,
                        'educations' => $educations,
                        'rtw' => $rtw,
                        'salaries' => $salaries,
                        'if_driver' => $driver,
                        'company_logo' => $company_logo,
                        'station' => $station,
                        'jobdescription' => $jobdescription,
                        'getOffComs' => $offcoms,
                        'salhis' => $salhis,
                        'empInfo' => $empInfo,
                        'empStations' => $empStations,
                        'questions' => $questions,
                    )
                );

        }

        /* Employee Questions Data :: Start */
        public function getQuestions($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId):
                $conn = $this->conn('gccmaster');
                $sql = "SELECT e.ques1, e.ques2, e.ques3, e.ques4, e.ques5, e.ques6, e.ques7, e.ques8, e.ques9 
                        FROM $this->employeeTable as e
                        WHERE e.id = :id;";

                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId); 
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetch();

                    $arrData = $row;
                }
            endif;
            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }
        /* Employee Questions Data :: End */

        /* Employee Stations Collection Data :: Start */
        public function getEmpStations($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId):
                $conn = $this->conn('gccmaster');
                $sql = "SELECT pl.location_name FROM gccmaster.tblemployees as e
                LEFT JOIN gcctimeutility.personnel as p ON p.biometric_id = e.biometricno
                LEFT JOIN gcctimeutility.personnel_locations as pl ON p.id = pl.personnel_id
                WHERE e.id = :id;";

                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId); 
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
            endif;
            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }
        /* Employee Stations Collection Data :: End */

        /* Employee Info Row Data :: Start */
        public function getEmpInfo($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();
            if($empId):
                $conn = $this->conn('gccmaster');
                $sql = "SELECT c.code as company, e.work_mode, e.work_status, e.payroll_type, e.level as level_rank, e.date_end_prob, e.date_regular, e.date_end, e.resign_reason, d.description as depart
                        FROM gccmaster.tblemployees as e 
                        LEFT JOIN gcchris.tbldepartments as d ON d.id = e.department_id
                        LEFT JOIN gcchris.tblcompanies as c ON c.id = e.company_id
                        WHERE e.id = :id;";

                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId); 
                $query->execute();

                if($query->rowCount() == 1){
                    $row = $query->fetch();

                    $arrData = $row;
                }
            endif;
            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }
        /* Employee Info Row Data :: End */

        /* Employee Salary History Collection Data :: Start */
        public function getSalHis($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId):
                $conn = $this->conn('gccmaster');
                $sql = "SELECT sal.sal_date, sal.sal_rate, sal.sal_remarks, IF(pos.id IS NULL, sal.sal_position, pos.name) sal_position 
                FROM gcchris.tblsalaries as sal
                LEFT JOIN gcchris.tblposition as pos ON pos.id = sal.sal_position 
                WHERE sal.emp_id = :id
                AND sal.is_archived = 0
                ORDER BY sal.sal_date DESC";

                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId); 
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
            endif;
            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }
        /* Employee Salary History Collection Data :: End */

        /* Employee Offenses and Comendation Collection Data :: Start */
        public function getOffComs($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId):
                $conn = $this->conn('gccmaster');
                $sql = "SELECT offcom_type, offcom_date, offcom_nature, offcom_action 
                FROM gcchris.tbloffcoms 
                WHERE emp_id = :id 
                ORDER BY offcom_date DESC;";

                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId); 
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
            endif;
            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }
        /* Employee Offenses and Comendation Collection Data :: End */

        /* Personel Job Description Row Data :: Start */
        public function getJobDescription($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();
            if($empId):
                $conn = $this->conn('gccmaster');
                $sql = "SELECT pos.job_desc
                 FROM gccmaster.tblemployees as e
                 LEFT JOIN gcchris.tblposition as pos 
                 ON pos.id = e.position 
                 WHERE e.id = :id";

                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId); 
                $query->execute();

                if($query->rowCount() == 1){
                    $row = $query->fetch();

                    $arrData = $row;
                }
            endif;
            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }
        /* Personel Job Description Row Data :: End */

        /* Personal Information Row Data :: Start */
        public function getPersonalInformation($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();
            if($empId):
                $conn = $this->conn('gccmaster');

                $sql = "SELECT firstname, middlename, lastname, suffix, curr_addr, prov_addr, citizenship, religion, languages, gender, civil_stat, bday, birthplace, bloodtype, height, `weight`, hair_color, complexion, tel_no, mobile_no
                
                FROM $this->employeeTable
                WHERE id = :id";
 
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId); 
                $query->execute();

                if($query->rowCount() == 1){
                    $row = $query->fetch();

                    $arrData = $row;
                }

            endif;

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }
        /* Personal Information Row Data :: End */

        

        /* Additional Information Row Data :: Start */
        public function getAdditionalInformation($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();
            if($empId):
                $conn = $this->conn('gccmaster');

                $sql = "SELECT email, tax_status, tin_no, phealth_no, pagibig_no, sss_no, fat_name, fat_addr, fat_company, fat_occupation, fat_contact, fat_deceased, mot_name, mot_addr, mot_company, mot_occupation, mot_contact, mot_deceased, partners_name, partners_addr, partners_company, partners_occupation, partners_contact, partners_deceased, emer_name, emer_contact, emer_addr
                
                FROM $this->employeeTable
                WHERE id = :id";
 
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId); 
                $query->execute();

                if($query->rowCount() == 1){
                    $row = $query->fetch();

                    $arrData = $row;
                }

            endif;

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }
        /* Additional Information Row Data :: End */

        public function getEmployee($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();
            if($empId){
                $conn = $this->conn('gccmaster');

                $sql = "SELECT a.id, a.biometricno, a.idno, a.lastname, a.firstname, a.middlename,a.spo_name, a.spo_addr, a.spo_company, a.spo_occupation, a.spo_contact, 
                    a.suffix, a.employee_status, b.role_id, b.group_id, b.email, b.username, b.reset_pin, b.is_suspended, a.date_start, a.date_end, 
                    a.work_status, c.name as position, company.description as company_description
                    FROM $this->employeeTable as a
                    LEFT JOIN $this->userTable as b ON b.emp_id = a.id
                    LEFT JOIN $this->companyTable as company ON company.id = a.company_id
                    LEFT JOIN $this->positionTable as c ON a.position = c.id OR a.position = c.name
                    WHERE a.id = :id
                    GROUP BY a.id";

                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() == 1){
                    $row = (object) $query->fetch();

                    $arrData = $row;
                }
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }

        public function getDependent($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT dep_name, dep_age, dep_birthdate, dep_relation
                    FROM $this->employeeDependentsTable
                    WHERE emp_id = :id";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row; 
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }

        public function getLicenses($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT a.license_type, a.exam_place, a.rating, a.release_date, a.exam_date, a.license_no, a.expiration_date, b.description
                    FROM $this->employeeLicensureTable as a
                    LEFT JOIN $this->licenseTable as b ON a.license_id = b.id
                    WHERE a.emp_id = :id AND a.is_archived = 0";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }

        public function getDriverLicense($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT restriction, license_no, expiration_date
                    FROM $this->employeeDriverLicenseTable
                    WHERE emp_id = :id AND is_archived = 0";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }

        public function getEmployeeExperiencesForPDS($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT xps.id, xps.emp_id, xps.work_from, xps.work_to,
                        xps.work_company, xps.work_status, xps.work_reason, xps.old_idno,
                        IF(com.id IS NULL, xps.work_company, com.description) work_company,
                        IF(pos.id IS NULL, xps.work_position, pos.name) work_position
                    FROM $this->employeeWorkExperienceTable as xps
                    LEFT JOIN $this->positionTable as pos ON pos.id = xps.work_position
                    LEFT JOIN $this->companyTable as com ON com.id = xps.work_company
                    WHERE xps.emp_id = :id AND xps.is_archived = 0
                    ORDER BY xps.work_from DESC";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }

        public function getAwards($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT award, award_institution as institution
                    FROM $this->employeeAwardsTable
                    WHERE emp_id = :id AND is_archived = 0
                    ORDER BY award_date DESC";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }
        
        public function getSkills($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT skills
                    FROM $this->employeeSkillsTable
                    WHERE emp_id = :id AND is_archived = 0";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }
        
        public function getOrganizations($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT org_institution as institution, org_membership_title as title, org_from, org_to
                    FROM $this->employeeOrganizationTable
                    WHERE emp_id = :id AND is_archived = 0
                    ORDER BY org_to DESC";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }
        
        public function getTrainings($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT train_from, train_to, training, train_institution as institution, train_conductor as conductor, train_venue as venue
                    FROM $this->employeeTrainingsTable
                    WHERE emp_id = :id AND is_archived = 0
                    ORDER BY train_to DESC";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }
        
        public function getReferences($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT ref_name as name, ref_contact_no as contact, ref_address as address
                    FROM $this->employeeReferencesTable
                    WHERE emp_id = :id AND is_archived = 0";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }
        
        public function getMedicals($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT med_details as details, med_no, med_date, med_venue as venue, med_physician as physician, remarks
                    FROM $this->employeeMedicalHistoryTable
                    WHERE emp_id = :id AND is_archived = 0
                    ORDER BY med_date DESC";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }

        public function getLegals($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT leg_case_no as case_no, leg_details as details, leg_case_date as case_date, leg_court_field as court_field, leg_prosecutor as prosecutor, leg_status
                    FROM $this->employeeLegalHistoryTable
                    WHERE emp_id = :id AND is_archived = 0
                    ORDER BY leg_case_date DESC";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }
        
        public function getOffenses($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT offcom_type as type, offcom_date as date, offcom_nature as nature, offcom_action as action
                    FROM $this->employeeOffensesTable
                    WHERE emp_id = :id AND is_archived = 0
                    ORDER BY offcom_date DESC";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }

        public function getEmployeeAccountability($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT acct_body.asset_id, acct.reference_no, acct_body.asset_code, acct_body.description, acct_body.amount, acct_body.is_returned, acct_body.date_returned, acct.status, acct_body.type,
                        CASE
                            WHEN assets.name IS NULL THEN vehicles.name
                            WHEN vehicles.name IS NULL THEN assets.name
                        END aname
                    FROM gcceforms.accountability as acct
                    INNER JOIN gcceforms.accountability_body as acct_body ON acct_body.accountability_id = acct.id
                    LEFT JOIN gccasset.assets as assets ON assets.id = acct_body.asset_id AND acct_body.type = 'Asset'
                    LEFT JOIN gccasset.vehicles vehicles ON vehicles.id = acct_body.asset_id AND acct_body.type = 'Vehicle'
                    WHERE acct.issued_to = :id AND acct.status = 'Released'";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

           return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }
        
        public function getEducations($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT educ_level_type as level_type, educ_school as school, educ_degree as degree, educ_units as units, educ_honors as honors, educ_from, educ_to
                    FROM $this->employeeEducationTable
                    WHERE emp_id = :id AND is_archived = 0
                    ORDER BY educ_to DESC";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }

        public function getStation($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');
                $bio = $this->getEmpLocation($id);

                $sql = "SELECT location_name
                    FROM $this->tblPersonnelLocation
                    WHERE personnel_id = :id
                    ORDER BY id DESC";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $bio);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }

        public function getEmpLocation($bio){
            $conn = $this->conn('gcchris');

            $sql = "SELECT id FROM $this->tblPersonnel WHERE biometricno = :bio";
            $query = $conn->prepare($sql);
            $query->bindParam(':bio', $bio);
            $query->execute();

            $row = (object) $query->fetch();
            return $row->id;
        }

        public function getRtw($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT rtw.id, rtw.reference_no, rtw.return_type, rtw.reason, rtw.from_date, rtw.approved_by, CONCAT(emp.firstname,' ',emp.lastname) as firstname
                    FROM $this->tblReturnToWork as rtw
                    LEFT JOIN $this->employeeTable as emp ON emp.id = rtw.approved_by
                    WHERE rtw.employee_id = :id";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }

        public function getSalaries($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT sal.id, sal.sal_date, sal.sal_rate, sal.sal_remarks, IF(pos.id IS NULL, sal.sal_position, pos.name) sal_position
                    FROM $this->employeeSalaryTable as sal
                    LEFT JOIN $this->positionTable as pos ON pos.id = sal.sal_position
                    WHERE sal.emp_id = :id AND sal.is_archived = 0";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arrData = $row;
                }
                
            }

            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }

        public function getIfDriver($id = null){
            $empId = $id ? $id : $_POST['id'];
            $arrData = array();
            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT emp.position as position
                    FROM $this->employeeTable
                    WHERE emp_id = :id";
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() > 0){
                    $row = (object) $query->fetch();

                    $arrData = $row;
                }
            }
            return (isset($_POST['id']) && $_POST['id']) ? json_encode($arrData) : $arrData;
        }

        public function getDriver($position, $id = null){
            $empId = $id ? $id : $_POST['id'];
            $count = 0;

            if($empId){
                $conn = $this->conn('gcchris');

                if(is_numeric($position)){
                    $sql = "SELECT *
                        FROM $this->positionTable as pos
                        LEFT JOIN $this->employeeTable as emp ON emp.position = pos.id
                        WHERE emp.id = :id AND (pos.name LIKE '%driver%' OR pos.name LIKE '%operator%')";
                
                    $query = $conn->prepare($sql);
                    $query->bindParam(':id', $empId);
                    $query->execute();

                    $count = $query->rowCount();
                }else{
                    $sql = "SELECT *
                        FROM $this->employeeTable
                        WHERE emp.id = :id AND (emp.position LIKE '%driver%' OR emp.position LIKE '%operator%')";
                
                    $query = $conn->prepare($sql);
                    $query->bindParam(':id', $empId);
                    $query->execute();

                    $count = $query->rowCount();
                }
            }

            return $count;
        }

        public function getLogo($id = null){
            $empId = $id ? $id : $_POST['id'];
            $company_logo = '';            

            if($empId){
                $conn = $this->conn('gcchris');

                $sql = "SELECT companies.logo
                    FROM $this->employeeTable as employee
                    LEFT JOIN $this->companyTable as companies ON employee.company_id = companies.id
                    WHERE employee.id = :id";
                
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $empId);
                $query->execute();

                if($query->rowCount() == 1){
                    $row = (object) $query->fetch();

                    $logo = trim(str_replace('%', ' ', $row->logo));
                    $company_logo = "assets/images/company/" . $logo;
                    if (!file_exists(realpath(dirname('../'.$company_logo)))) {
                        $company_logo = "assets/images/company/no_image.jpg";
                    }
                }
                
            }            

            return $company_logo;
        }

        // public function get_searched_employees(){ search
        //     $post = $_POST;
        //     $arrData = array();
        //     $arr = array();

        //     $conn = $this->conn('gcchris');

        //     $sql = "SELECT UCASE(
        //             TRIM(CONCAT(
        //                 firstname, ' ',
        //                 CASE
        //                     WHEN middlename IS NOT NULL AND middlename != '' THEN CONCAT(' ', substr(middlename,1,1),'.')
        //                     ELSE ''
        //                 END,
        //                 ' ', lastname,
        //                 CASE
        //                     WHEN suffix IS NOT NULL AND suffix != '' AND suffix != 'N/A' AND suffix != 'NONE' THEN CONCAT(' ', suffix)
        //                     ELSE ''
        //                 END
        //             ))
        //         ) employee_name, id, pic_filename, employee_status, trim(firstname) as firstname, trim(lastname) as lastname FROM $this->employeeTable WHERE UCASE( CONCAT( firstname, ' ', CASE WHEN middlename IS NOT NULL AND middlename != '' THEN CONCAT(' ', substr(middlename,1,1),'.') ELSE '' END,
        //                 ' ', lastname, CASE WHEN suffix IS NOT NULL AND suffix != '' AND suffix != 'N/A' AND suffix != 'NONE' THEN CONCAT(' ', suffix) ELSE ''END ) ) LIKE '%$post[search]%' ORDER BY trim(lastname), trim(firstname) ASC";
            
        //     $query = $conn->prepare($sql);
        //     $query->execute();

        //     if($query->rowCount() > 0){
        //         $image = './assets/images/profile/no_image.jpg';

        //         foreach($query->fetchAll(PDO::FETCH_ASSOC) as $key => $row){
        //             $tempFile = "../uploads/files/images/employee_files/empcode_{$row[id]}/thumbnails/{$row[pic_filename]}";

        //             if(file_exists(realpath(dirname($tempFile)))){
        //                 $image = "/uploads/files/images/employee_files/empcode_{$row[id]}/thumbnails/{$row[pic_filename]}";
        //             }

        //             $row['pic_filename'] = $image;


        //             $arrData[$key] = $row;
        //         }

        //         foreach ($arrData as $k => $v) {
        //             $arr[] = $v;
        //         }
        //     }

        //     return json_encode($arr);
        // }

        public function isHead($id) {
            $conn = $this->conn('gcchris');
            $sql = "SELECT head_id
                    FROM $this->departmentTable
                    WHERE head_id = :id";
            $query = $conn->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            return $query->rowCount() > 0;
        }

        public function get_searched_employees(){
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            $post = $_POST;
            $arrData = array();
            $arr = array();
            $dept_id = $_POST['dept_id'];
            $user_id = $this->isHead($_POST['user_id']);
            $user_idv = $_POST['user_id'];

            if ($user_id) {
                $dept_request = "WHERE department_id = $dept_id";
            } elseif ($dept_id == '8' || $user_idv == '2' || $dept_id == '2' || $user_idv == '1') {
                $dept_request = "";
            } else {
                $dept_request = "WHERE id = $user_idv";
            }
            $conn = $this->conn('gcchris');
            $sql = "SELECT UCASE(
                    TRIM(CONCAT(
                        firstname, ' ',
                        CASE
                            WHEN middlename IS NOT NULL AND middlename != '' THEN CONCAT(' ', substr(middlename,1,1),'.')
                            ELSE ''
                        END,
                        ' ', lastname,
                        CASE
                            WHEN suffix IS NOT NULL AND suffix != '' AND suffix != 'N/A' AND suffix != 'NONE' THEN CONCAT(' ', suffix)
                            ELSE ''
                        END
                    ))
                ) employee_name, id, pic_filename, employee_status, trim(firstname) as firstname, trim(lastname) as lastname, department_id FROM $this->employeeTable $dept_request";
            $query = $conn->prepare($sql);
            $query->execute();

            if($query->rowCount() > 0){
                $image = './assets/images/profile/no_image.jpg';

                foreach($query->fetchAll(PDO::FETCH_ASSOC) as $key => $row){
                    $tempFile = "../uploads/files/images/employee_files/empcode_{$row['id']}/thumbnails/{$row['pic_filename']}";

                    if(file_exists(realpath(dirname($tempFile)))){
                        $image = "/uploads/files/images/employee_files/empcode_{$row['id']}/thumbnails/{$row['pic_filename']}";
                    }
                    $row['pic_filename'] = $image;
                    $arrData[$key] = $row;
                }
                foreach ($arrData as $k => $v) {
                    $arr[] = $v;
                }
            }
            return json_encode($arr);
        }
    }
?>