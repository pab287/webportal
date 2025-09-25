<?php defined('BASEPATH') || exit('No direct script access allowed');
class Company_model extends CI_Model{
	protected $companyTable = "gcchris.tblcompanies";
	protected $archivedTable = "gccmaster.archived_items";
	
	function __construct(){
		parent::__construct();
		$this->load->model("datatable_model","dt_model");
		$this->load->model("core/upload_model", "file_upload");

		$this->loggedinData = $this->user_data = $this->session->userdata("logged_in");
        $this->loggedInUsername = $this->loggedinData["username"];
    }

    function getCompanyDatatableRequest(){
        $post = $this->input->post();
		if($post){
			$orderx = (isset($post["order"]) && $post["order"])? $post["order"]: false;
			$columns = array("logo", "code", "description", "id", "is_archived", "work_days_in_year", "sss_class", "exclude", "email_to", "cc_to", "bcc_to");
			$dir = "DESC";
			$order = "id";
			if($orderx){
				$dir = $orderx[0]["dir"];
				$order = $columns[$orderx[0]["column"]];
			}
				
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			$dtTemp = $this->dt_model->dataTable();
			$dtTemp->setTable($this->companyTable);
			$dtTemp->setParameterFields($columns);
			
			$parameters = array();
			$parameters["is_archived"] = (isset($post['is_archived']) && $post['is_archived']) ? 1 : 0; // is_archived has value when user accessed archive
			
			$dtTemp->setWhereParameters($parameters);

			$totalData = $dtTemp->dtAllPostsCount();
			$totalFiltered = $totalData;
			
			if(empty($searchValue)){
				$posts = $dtTemp->dtAllPosts($limit, $start, $order, $dir);
			}else {
				$posts = $dtTemp->dtSearch($limit, $start, $searchValue, $order, $dir);
				$totalFiltered = $dtTemp->dtPostSearchCount($searchValue);
			}
			
			$data = array();
			if(!empty($posts)){
				
				foreach ($posts as $pst){
					$currentImage = base_url("assets/images/company/no_image.jpg");
					$imageFile = $pst->logo;
					$imagePath = realpath("./uploads/files/images/company_logo/thumbnails/{$imageFile}");
					if(file_exists($imagePath) && $imageFile){
						$currentImage = base_url("uploads/files/images/company_logo/{$imageFile}");
					}
					$email_to = @unserialize($pst->email_to)? unserialize($pst->email_to) : $pst->email_to;
					$cc_to = @unserialize($pst->cc_to)? unserialize($pst->cc_to) : $pst->cc_to;
					$bcc_to = @unserialize($pst->bcc_to)? unserialize($pst->bcc_to) : $pst->bcc_to;

					$nestedData = array();
					$nestedData['id'] = $pst->id;
					$nestedData['logo'] = $currentImage;
					$nestedData['code'] = $pst->code;
					$nestedData['description'] = $pst->description;
					$nestedData['work_days_in_year'] = $pst->work_days_in_year;
					$nestedData['sss_class'] = $pst->sss_class;
					$nestedData['exclude'] = $pst->exclude;
					$nestedData['email_to'] = $email_to;
					$nestedData['cc_to'] =  $cc_to;
					$nestedData['bcc_to'] =  $bcc_to;

					
					$data[] = $nestedData;
				}


			}
			return array(
                    "draw" => intval($draw),  
                    "recordsTotal" => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data,   
                    );
            
		}else{
			return array(
				"draw"=>1,
				"recordsTotal"=>0,
				"recordsFiltered"=>0,
				"data"=>array(),
				);
		}
	}
	
	function getCompanyModalContent($content="add"){
		$resultset = array();
		$html = "";
		$arrData = array();

		if($content == "add"){
			$html = $this->load->view("hris/masterfile/company/modals/add_content", null, true);
		}
		if($content == "edit"){
			$post = $this->input->post();
			if(isset($post) && $post){
				unset($post["csrf_token"]);
				$tempCompany = $this->db->get_where($this->companyTable, $post);
				if($tempCompany->num_rows() == 1){
					$arrData = $tempCompany->row();
				}
			}
			$html = $this->load->view("hris/masterfile/company/modals/edit_content", array("data"=>$arrData), true);
		}

		if($html){
			$resultset["response"] = true;
			$resultset["html"] = $html;
			$resultset["data"] = $arrData;
		}else{
			$resultset["response"] = false;
		}
		
		return $resultset;
	}

	function tempUploadCompanyFile($thumbnails=false){
        $resultset = array();
        $session = $this->core_layout->getCurrentSession();

        if(isset($session["emp_id"]) && $session["emp_id"]){
            $imagesPath = "./uploads/files/images/company_logo/temp_{$session["emp_id"]}";

            $createFilePath = false;

            if (!file_exists($imagesPath)) {
                $mkdir = mkdir($imagesPath, 0777, true);
                if ($mkdir){ $createFilePath = true; }
            }else{ $createFilePath = true; }

            if($createFilePath === false){
                $resultset["response"] = false;
                $resultset["toastr_msg"] = "Failed to create directory folder for the uploaded file!";
                $resultset["toastr_state"] = "warning";
				$this->core_layout->setEventLog("User has failed to create directory folder for the upload file of company logo.","upload", "error", "gcchris", "system");
            }else{
                $config = array();
                $config['upload_path']          = $imagesPath;
                $config['allowed_types']        = 'jpg|jpeg|png|PNG|JPG|JPEG';
                $config['max_size']             = 10000;
                $config['create_thumbnail']     = false;

                $data = $this->file_upload->uploadFile($config);
                if($data["response"] === true){
                    $files = $data["files"][0];
                    $filename = $files["file_name"];
                    if($filename){
                        $resultset["response"] = true;

                        $resultset["added_image"] = base_url("uploads/files/images/company_logo/temp_{$session["emp_id"]}/{$filename}");
                        $resultset["filename"] = "{$filename}";

                        $resultset["toastr_msg"] = "Upload image successful.";
                        $resultset["toastr_state"] = "success";
						$this->core_layout->setEventLog("User has successfully uploaded company logo.","upload", "success", "gcchris", "user");
                    }else{
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Image upload to specific path failed!";
                        $resultset["toastr_state"] = "error";
						$this->core_layout->setEventLog("User has failed uploading images to specified path of company logo.","upload", "error", "gcchris", "system");
                    }
                }else{
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Image upload failed!";
                    $resultset["toastr_state"] = "error";
					$this->core_layout->setEventLog("User has failed uploading images of company logo.","upload", "error", "gcchris", "system");
                }
            }

        }else{
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Employee data not found!";
            $resultset["toastr_state"] = "error";
			$this->core_layout->setEventLog("Company masterfile - Error, No post data found.","upload", "error", "gcchris", "user");
        }

        return $resultset;
	}
	
	function setModalCompany(){
		$resultset = array();
		$session = $this->core_layout->getCurrentSession();

		$post = $this->input->post();
		if(isset($post) && $post){
			$logo = $post["logo_attachment"];
			unset($post["csrf_token"], $post["logo_attachment"]);

			if($logo){ $post["logo"] = $logo; }
			$post["add_date"] = date("Y-m-d H:i:s");
            $post["add_by"] = $session["emp_id"];
			$post['email_to'] = "";
			$post['cc_to'] = "";
			$post['bcc_to'] = "";
			$allow = $this->checkCompanyCode($post);
			if($allow){
				$insert = $this->db->insert($this->companyTable, $post);
				if($insert){
					if($logo){
						$tempFile = $logo;
						$tempFileFrom = "./uploads/files/images/company_logo/temp_{$session["emp_id"]}";
						$tempDestination = "./uploads/files/images/company_logo";
					
						$this->file_upload->moveUploadedFile($tempFile, $tempFileFrom, $tempDestination, true);
					}
					$resultset["response"] = true;
					$resultset["toastr_msg"] = "Company data has been added.";
					$this->core_layout->setEventLog("User added new company with code: <strong>".$post["code"]."</strong>","insert", "success", "gcchris", "user");
				}else{
					$resultset["response"] = false;
					$resultset["toastr_msg"] = "Failed saving company data!";
					$this->core_layout->setEventLog("User has error inserting company details for company code: <strong> ".$post["code"]."</strong>","insert", "error", "gcchris", "system");
				}
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Company code already exist!";
				$this->core_layout->setEventLog("User has error inserting company already exist for company code:  <strong>".$post["code"]."</strong>","insert", "error", "gcchris", "system");
			}
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Company masterfile - Error, No post data found.","insert", "error", "gcchris", "system");
		}
        return $resultset;
	}
	
	function updateModalCompany(){
		$resultset = array();
		$session = $this->core_layout->getCurrentSession();

		$post = $this->input->post();
		if(isset($post) && $post){
			$id = $post["id"];
			$logo = $post["logo_attachment"];
			unset($post["csrf_token"], $post["logo_attachment"], $post["id"]);

			if($logo){ $post["logo"] = $logo; }
			$post["update_date"] = date("Y-m-d H:i:s");
            $post["update_by"] = $session["emp_id"];
			$post['email_to'] = isset($post['email_to']) ? serialize($post['email_to']) : "";
			$post['cc_to'] = isset($post['cc_to']) ? serialize($post['cc_to']) : "";
			$post['bcc_to'] = isset($post['bcc_to']) ? serialize($post['bcc_to']) : "";
			$post["exclude"] = isset($post["exclude"]) && intval($post["exclude"]) === 1 ? 1 : 0;

			$currentCompanyData = $this->getCompanyData($id);
			$update = $this->db->update($this->companyTable, $post, array("id"=>$id));
			if($update){
				if($logo){
					$tempFile = $logo;
					$tempFileFrom = "./uploads/files/images/company_logo/temp_{$session["emp_id"]}";
					$tempDestination = "./uploads/files/images/company_logo";
				
					$this->file_upload->moveUploadedFile($tempFile, $tempFileFrom, $tempDestination, true);
				}
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Company data has been updated.";
				unset($post['update_date']); 
                unset($post['update_by']);
				$changes = $this->logChanges($currentCompanyData ,$post);
				$this->core_layout->setEventLog("User updated company: <strong>".$post['description']."</strong> ".$changes,"update", "success", "gcchris", "user");
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed updating company data!";
				$this->core_layout->setEventLog("User has error updating company data.","update", "error", "gcchris", "system");
			}
        }else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Company masterfile - Error, No post data found.","update", "error", "gcchris", "system");
		}
        
        return $resultset;
	}
	
	function removeCurrentCompany(){
		$resultset = array();
		$post = $this->input->post();
		$company = $post['company'];
		if(isset($post) && $post){
			unset($post["csrf_token"],$post["company"]);
			$updated = $this->db->update($this->companyTable, array("is_archived"=>1), $post);
			if($updated){
				$session = $this->core_layout->getCurrentSession();
				$data = array(
					"archived_table"=>$this->companyTable,
					"archived_id"=>$post["id"],
					"archived_by"=>$session["emp_id"]
				);

				$this->db->insert($this->archivedTable, $data);
					
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Company has been removed.";
				$this->core_layout->setEventLog("User archived company: <strong>".$company."</strong>","archived", "success", "gcchris", "user");
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed to remove company!";
				$this->core_layout->setEventLog("User error archiving company: <strong>".$company."</strong>","archived", "success", "gcchris", "system");
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Company masterfile - Error, No post data found.","archived", "error", "gcchris", "system");
		}

		return $resultset;
	}

	private function checkCompanyCode($data=array()){
		if($data){
			$query = $this->db->get_where($this->companyTable, array("code"=>$data["code"]));
			return  $query->num_rows() == 0 ? true: false;
		}else{
			return false;
		}
	}

	public function getCompanyCodeList(){
		$arrData = array();
		$query = $this->db->get_where($this->companyTable, array("is_archived"=>0));
		if($query->num_rows() > 0){
			foreach($query->result() as $rs){
				$arrData[] = $rs->code;
			}
		}
		return $arrData;
	}

	public function getCompanySelect2Data(){
		$resultset = array();
		$arrData = array();

		$get = $this->input->get();
		$this->db->select("id, description as text");
		$this->db->from($this->companyTable);
		$this->db->where("is_archived", 0);
		$this->db->where("exclude", 0);
		if(isset($get["term"]) && $get["term"]){ $this->db->like("description", trim($get["term"]), "both"); }
		$query = $this->db->get();

		if($query->num_rows() > 0){
			$arrData = $query->result();
		}
		
		$resultset["results"] = $arrData;
		return $resultset;
	}

	function emailLookup_company(){
        $get = $this->input->get();
        $resultarray = array();
        if(isset($get['q'])){
            $query = $this->db->query("SELECT a.id, b.firstname, b.lastname, b.middlename, b.suffix, a.email FROM gccmaster.tblusers a, gccmaster.tblemployees b WHERE b.id=a.emp_id AND (a.email !='NO EMAIL ADDRESS' AND a.email !='NO EMAIL' AND a.email !='' AND b.employee_status = 'Active') AND (b.firstname LIKE '%{$get['q']}%' || b.lastname LIKE '%{$get['q']}%' || a.email LIKE '%{$get['q']}%') ORDER BY b.firstname ASC LIMIT 10");
        }else{
            $query = $this->db->query("SELECT a.id, b.firstname, b.lastname, b.middlename, b.suffix, a.email FROM gccmaster.tblusers a, gccmaster.tblemployees b WHERE b.id=a.emp_id AND (a.email !='NO EMAIL ADDRESS' AND a.email !='NO EMAIL' AND a.email !='' AND b.employee_status = 'Active') ORDER BY b.firstname ASC LIMIT 10");
        }
        if($query->num_rows() > 0){
            foreach($query->result_array() as $_query){
                $data = array();
                $tempRs = (array) $_query;
                $fullname = $this->core_layout->getDisplayName($tempRs);
                $tempFullname = (object) $fullname;
                $_query['display_name'] = ($tempFullname->display_name_1)? $tempFullname->display_name_1: "No Assigned Name";
                $data["id"] = $_query["email"];
                $data["text"] =  $_query["email"]." | ".$_query["display_name"];
                $resultarray[] = $data;
            }
        }
        return array("results"=>$resultarray);
    }

	public function editDetails_company($id){
        $this->db->select("*");
        $this->db->from("gcchris.tblcompanies");
        $this->db->where("id",$id);
        $query = $this->db->get();
       
        $query = $query->num_rows() > 0 ? $query->row_array() :  FALSE;
        $query["email_to"]=unserialize($query["email_to"]);   
        $query["cc_to"]=unserialize($query["cc_to"]);
		$query["bcc_to"]=unserialize($query["bcc_to"]);
        return $query;
    }

	public function getCompany(){
		$get = $this->input->get();
		$filterFields = array("code", "description");

		$sql = "id, CASE WHEN code = description THEN description ELSE CONCAT(code, ' | ', description) END as text";
		$this->db->select($sql);
		$this->db->from("gcchris.tblcompanies");

		if($get && isset($get['term'])){
			foreach($filterFields as $key => $field){
                if($key == 0){
					$this->db->like($field, $get['term'], "both");
				}else{
					$this->db->or_like($field, $get['term'], "both");
				}
            }
		}

		$this->db->where('is_archived', 0);
		$this->db->order_by('code', 'asc');
		$query = $this->db->get();
		return  array(
			"results" => $query->result()
		);
	}

	public function select2CompanyData(){
        $this->db->select("companies.id, companies.`code` `text`, companies.*");
		$this->db->where('companies.is_archived', 0);
        $this->db->order_by("`code`", "ASC");
        $results = $this->db->get("gcchris.tblcompanies companies")->result();
        return $results;
    }

	function restoreCurrentCompany(){
		$resultset = array();
		$post = $this->input->post();
		if(isset($post) && $post){
			unset($post["csrf_token"]);
			$updated = $this->db->update($this->companyTable, array("is_archived"=>0), $post);
			$company = $this->getCompanyData($post["id"]);
			if($updated){
				// $session = $this->core_layout->getCurrentSession();
				// $data = array(
				// 	"archived_table"=>$this->companyTable,
				// 	"restored_by"=>$session["emp_id"],
				// 	"restored_at"=>date('Y-m-d H:i:s')
				// );

				// $this->db->update($this->archivedTable, $data);
					
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Company has been restored.";
				$this->core_layout->setEventLog("User restored company: <strong>".$company->description."</strong>","restore", "success", "gcchris", "user");
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed to restore company!";
				$this->core_layout->setEventLog("User error restoring company: <strong>".$company->description."</strong>","restore", "success", "gcchris", "system");
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
			$this->core_layout->setEventLog("Company masterfile - Error, No post data found.","restore", "error", "gcchris", "system");
		}

		return $resultset;
	}
	private function getCompanyData($id) {
		$this->db->select("*");
		$this->db->from($this->companyTable);
		$this->db->where('id', $id);
		$query = $this->db->get(); 
		return $query->row();
	}

	private function logChanges($currentData, $newData) {
		$changes = array();
		$changesString = '';
		foreach ($currentData as $field => $value) {
			if (isset($newData[$field]) && $newData[$field]!= $value) {
				$changes[$field] = array(
					'old' => $value,
					'new' => $newData[$field]
				);
			}
		}
		foreach ($changes as $field => $change) {
			$changesString.= " Field: $field, from: <strong>$change[old]</strong>, to: <strong>$change[new]</strong>\n";
		}
		return $changesString;
	}

}