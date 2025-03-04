<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Return_work_m extends CI_Model {
    private $returnToWorkTable = "gcceforms.return_to_work";
    private $employeeTable = "gccmaster.tblemployees";
    private $companyTable = "gcchris.tblcompanies";
    private $departmentTable = "gcchris.tbldepartments";
    private $positionTable = "gcchris.tblposition";
	protected $current_action;

    public function __construct() {
		parent::__construct();
		$this->load->model("core/upload_model", "file_upload");
        $this->user_data = $this->session->userdata("logged_in");
		date_default_timezone_set("Asia/Manila");
		$this->load->library("image_lib");

		$this->current_action = $this->core_layout->getCurrentActions();
    }

    public function getRtwDatatableRequest(){
        $post = $this->input->post();
		if($post){
			$view_by_company = (in_array("view_by_company", $this->current_action)) ? true : false;
			$companyDescription = null;
	
			if ($view_by_company) {
				$companyDescription = $this->db->select("description")->get_where('gcchris.tblcompanies', array('id' => $this->user_data['company']))->row()->description;
			}

            $orderx = (isset($post["order"]) && $post["order"])? $post["order"]: false;
            $columns = array("a.status", "a.reference_no", "b.firstname", "a.return_type", "a.reason", "a.created_at", "a.id", "b.lastname", "b.middlename", "b.suffix", "a.from_date", "a.to_date");
            $dir = "DESC";
			$order = "a.id";
			if($orderx){
				$dir = $orderx[0]["dir"];
				$order = $columns[$orderx[0]["column"]];
			}
			$draw = (isset($post['draw']) && $post['draw'])? $post['draw']: 0;
			$start = (isset($post["start"]) && $post["start"])? $post["start"]: 0;
			$limit = (isset($post["length"]) && $post["length"])? $post["length"]: 0;
			$searchValue = (isset($post["search"]["value"]) && $post["search"]["value"])? $post["search"]["value"]: "";
			$filtered = (isset($post["filtered"]) && $post["filtered"])? $post["filtered"]: false;
			$searchValue = (isset($post["ids"]) && count($post["ids"]) > 0 && $filtered)? $post["ids"]: $searchValue;
		
			$tempTable = $this->dt_model->dataTable();
			$tempTable->setTable($this->returnToWorkTable);
			$tempTable->setTableAlias("a");
			
			$tempTable->setParameterFields($columns);
			
			$joinTable = array();
			$joinTable["table"][$this->employeeTable] = "b";
			$joinTable["fields"][] = "b.id=a.employee_id";
			$joinTable["field_loc"][] = "LEFT";
			
			$tempTable->setJoinTable($joinTable);
			$tempTable->setWhereInField("a.id");
			
			
			$tempActions = $this->core_layout->getCurrentActions();
			if(in_array("view_own_request", $tempActions)){
				$tempSession = $this->core_layout->getCurrentSession();
				$tempSession = (object) $tempSession;

				$parameters = array();
				$parameters["a.employee_id"] = $tempSession->emp_id;
				$parameters2 = array();
				$parameters2["a.created_by"] = $tempSession->emp_id;
				
				$tempTable->setWhereParameters($parameters);
				$tempTable->setOrWhereParameters($parameters2);
			}

			if ($view_by_company) {
				$_parameters = array();

				if (isset($companyDescription) && $companyDescription) {
					$_parameters['a.company'] = strtoupper($companyDescription);
				}

				$tempTable->setWhereParameters($_parameters);
			}

			if(is_array($searchValue)){
				$tempTable->setWhereInParameters("a.id", $searchValue);
				$searchValue = "";
            }
			$totalData = $tempTable->dtAllPostsCount();
			$totalFiltered = $totalData;
			
			if(empty($searchValue)){            
				$posts = $tempTable->dtAllPosts($limit, $start, $order, $dir);
			}else {
				$posts = $tempTable->dtSearch($limit, $start, $searchValue, $order, $dir);
				$totalFiltered = $tempTable->dtPostSearchCount($searchValue);
				$this->core_layout->setEventLog("Return to Work Masterfile - Search {$searchValue} in datatable.", "search", "success", "gcceforms", "user");
			}
			
			$data = array();
			if(!empty($posts)){
				foreach ($posts as $pst){
                    $tempRs = (array) $pst;
					$tempName = $this->core_layout->getDisplayName($tempRs);
					$tempName = (object) $tempName;
                    $tempName = (isset($tempName->display_name_1) && $tempName->display_name_1)? $tempName->display_name_1: "No assigned name";
                    $tempCreated = date("Y-m-d H:i:s", strtotime($pst->created_at));

					$tempFromTo = $this->generateAppliedDate($pst);

					$nestedData['id'] = $pst->id;
					$nestedData['reference_no'] = $pst->reference_no;
					$nestedData['employee_name'] = $tempName;
					$nestedData['created_at'] = $tempFromTo;
					$nestedData['status'] = $pst->status;
					$nestedData['reason'] = $pst->reason;
					$nestedData['return_type'] = $pst->return_type;
					$data[] = $nestedData;
				}
			}
			$json_data = array(
                    "draw" => intval($draw),  
                    "recordsTotal" => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data,   
                    );
            
			return $json_data;
		}else{
			return array(
				"draw"=>1,
				"recordsTotal"=>0,
				"recordsFiltered"=>0,
				"data"=>array(),
				);
		}
	}
	
	function generateAppliedDate($arrData=array()){
		$tempFromTo = "---";

		$tempFromYr = date("Y", strtotime($arrData->from_date));
		$tempToYr = date("Y", strtotime($arrData->to_date));
		$fromDate = date("F d, Y", strtotime($arrData->from_date));
		$toDate = date("F d, Y", strtotime($arrData->to_date));
		$tempFromTo = "{$fromDate} - {$toDate}";
		
		if(intval($tempToYr) == intval($tempFromYr)){
			$xtFrom = date("m", strtotime($arrData->from_date));
			$xtTo = date("m", strtotime($arrData->to_date));

			if($fromDate == $toDate){
				$tempFromTo = $fromDate;
			}else if($xtFrom == $xtTo){
				$fromDate = date("F d", strtotime($arrData->from_date));
				$toDate = date("d Y", strtotime($arrData->to_date));
				$tempFromTo = "{$fromDate} - {$toDate}";
			}
		}
		
		return $tempFromTo;
	}

	function getTempFields($type=0){
		$resultset = array();
		$tempHtml = $this->load->view("eforms/return_work/form_content/unauthorized", null, true);
		if($type){
			$tempHtml = $this->load->view("eforms/return_work/form_content/recalled", null, true);
		}
		$resultset["html"] = $tempHtml;
		return $resultset;
	}

	function getEmployeeSelect2Data(){
		$get = $this->input->get();
		$tempData = array();
		$this->db->select("id, lastname, firstname, middlename, suffix");
		$this->db->from($this->employeeTable);
		$this->db->where("employee_status", "Active");

		if (in_array("view_by_company", $this->current_action)) {
			$this->db->where("company_id", $this->user_data['company']);
		}

		if(isset($get["term"]) && $get["term"]){
			$this->db->group_start();
			$this->db->like("CONCAT(firstname, ' ', lastname)", $get["term"], "both");
			$this->db->or_like("firstname", $get["term"], "both");
			$this->db->or_like("lastname", $get["term"], "both");
			$this->db->group_end();
		}
		$this->db->order_by("firstname", "ASC");
		$queryEmployee = $this->db->get();
		if($queryEmployee->num_rows() > 0){
			foreach ($queryEmployee->result() as $key => $value) {
				$tempRs = (array) $value;
				$tempName = $this->core_layout->getDisplayName($tempRs);
				$tempName = (isset($tempName["display_name_1"]) && $tempName["display_name_1"])? $tempName["display_name_1"]: "No assigned name";
				$tempRow = array();
				$tempRow["id"] = $value->id;
				$tempRow["text"] = $tempName;
				$tempData[] = $tempRow;
			}
		}
		$resultset = array();
		$resultset["results"] = $tempData;
		$resultset["temp"] = $queryEmployee->num_rows();
		return $resultset;
	}

	function getEmployeeSelect2Company($id=null){
		$resultset = array();
		if($id){
			$tempData = array();
			$this->db->select("a.company_id, a.department_id, a.position, b.description as company_name, c.description as department_name, d.name as position_name");
			$this->db->from($this->employeeTable." a");
			$this->db->join($this->companyTable." b", "b.id = a.company_id", "LEFT");
			$this->db->join($this->departmentTable." c", "c.id = a.department_id", "LEFT");
			$this->db->join($this->positionTable." d", "d.id = a.position", "LEFT");
			$this->db->where("a.id", $id);
			$query = $this->db->get();
			if($query->num_rows() == 1){
				$qRow = $query->row();
				$tempData["company"] = (is_numeric($qRow->company_id) && $qRow->company_name)? trim($qRow->company_name): trim($qRow->company_id);
				$tempData["department"] = (is_numeric($qRow->department_id) && $qRow->department_name)? trim($qRow->department_name): trim($qRow->department_id);
				$tempData["position"] = (is_numeric($qRow->position) && $qRow->position_name)? trim($qRow->position_name): trim($qRow->position);
				$tempData["company_id"] = (is_numeric($qRow->company_id))? $qRow->company_id: 0;
				$tempData["department_id"] = (is_numeric($qRow->department_id))? $qRow->department_id: 0;
				$tempData["position_id"] = (is_numeric($qRow->position))? $qRow->position: 0;
				$tempData = array_map('strtoupper', $tempData);
				$resultset["response"] = true;
				$resultset["row"] = $tempData;
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}
		return $resultset;
	}

	function getRTWReference($id){
		return $this->db->get_where("gcceforms.return_to_work", array("id"=>$id))->row('reference_no');
	}

	function setNewRtw(){
		$resultset = array();
		$post = $this->input->post();
		if(isset($post) && $post){
			if(isset($post["csrf_token"]) && $post["csrf_token"]){ unset($post["csrf_token"]); }
			if(isset($post["date_from_to"]) && $post["date_from_to"]){
				$dateFromTo = explode("-", $post["date_from_to"]);
				$post["from_date"] = trim($dateFromTo[0]);
				$post["to_date"] = trim($dateFromTo[1]);
				unset($post["date_from_to"]);
			}
			$post["created_at"] = date("Y-m-d H:i:s");
			$post["created_by"] = $this->core_layout->getCurrentEmployeeId();
			$tempCode = $this->generateReferenceNo();
			$qTemp = $this->db->get_where($this->returnToWorkTable, array("reference_no"=>$tempCode));
			if($qTemp->num_rows() > 0){
				$tempCode = $this->generateReferenceNo();
			}
			$post["reference_no"] = $tempCode;
			$saved = $this->db->insert($this->returnToWorkTable, $post);
			if($saved){
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Data has been saved successfully.";
				$this->core_layout->setEventLog("New Return to Work - Add return to work.", "add", "success", "gcceforms", "user");
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed to save data for new return to work!";
				$this->core_layout->setEventLog("New Return to Work - Failed add return to work.", "add", "error", "gcceforms", "system");
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
		}

		return $resultset;
	}

	function updateRtwData(){
		$resultset = array();
		$post = $this->input->post();
		if(isset($post) && $post){
			$where = array();
			$where["id"] = $post["id"];
			if(isset($post["csrf_token"]) && $post["csrf_token"]){ unset($post["csrf_token"]); }
			unset($post["id"]);
			if(isset($post["date_from_to"]) && $post["date_from_to"]){
				$dateFromTo = explode("-", $post["date_from_to"]);
				$post["from_date"] = trim($dateFromTo[0]);
				$post["to_date"] = trim($dateFromTo[1]);
				unset($post["date_from_to"]);
			}else{
				$post["to_date"] = "0000-00-00";
			}

			$post["updated_at"] = date("Y-m-d H:i:s");
			$post["updated_by"] = $this->core_layout->getCurrentEmployeeId();
			$updated = $this->db->update($this->returnToWorkTable, $post, $where);
			if($updated && $this->db->affected_rows() > 0){
				$resultset["response"] = true;
				$resultset["toastr_msg"] = "Data has been updated successfully.";
				$this->core_layout->setEventLog("Edit Return to Work - Update return to work {$this->getRTWReference($this->input->post('id'))}.", "update", "success", "gcceforms", "user");
			}else{
				$resultset["response"] = false;
				$resultset["toastr_msg"] = "Failed to update data for edit return to work!";
				$this->core_layout->setEventLog("Edit Return to Work - Failed add return to work {$this->getRTWReference($this->input->post('id'))}.", "update", "error", "gcceforms", "system");
			}
		}else{
			$resultset["response"] = false;
			$resultset["toastr_msg"] = "No post data found!";
		}

		return $resultset;
	}

	public function getRtwData($id=null){
		$resultset = array();
		if($id){
			$this->db->from($this->returnToWorkTable);
			$this->db->where("id", $id);
			$qRtw = $this->db->get();
			if($qRtw->num_rows() == 1){
				$row = $qRtw->row();
				$row->dt_from = $row->from_date;
				$row->dt_to = $row->to_date;
				$row->dt_created = $row->created_at;

				$row->from_date = ($row->from_date !== "0000-00-00")? date("F d, Y", strtotime($row->from_date)): "---";
				$row->to_date = ($row->to_date !== "0000-00-00")? date("F d, Y", strtotime($row->to_date)): "---";

				switch($row->return_type){
					case 1: $row->type_description = "Recalled"; break;
					case 2: $row->type_description = "Request to extend days of work"; break;
					default: $row->type_description = "Unauthorized absence / No notification"; break;
				}

				switch($row->status){
					case 1: $row->status_type = "Aprroved"; break;
					case 2: $row->status_type = "Disapproved"; break;
					case 3: $row->status_type = "Cancelled"; break;
					default: $row->status_type = "Pending"; break;
				}

				$tempRequestor = $this->core_layout->getEmployeeData($row->employee_id);
				$tempRequestor = (isset($tempRequestor["display_name_1"]) && $tempRequestor["display_name_1"])? $tempRequestor["display_name_1"]: "No assigned name";
				$row->requested_name = $tempRequestor;

				$tempCreatedBy = $this->core_layout->getEmployeeData($row->created_by);
				$tempCreatedBy = (isset($tempCreatedBy["display_name_1"]) && $tempCreatedBy["display_name_1"])? $tempCreatedBy["display_name_1"]: "No assigned name";
				$row->created_name = $tempCreatedBy;
				$row->created_at = date("F d, Y h:i A", strtotime($row->created_at));

				$tempUpdatedBy = $this->core_layout->getEmployeeData($row->updated_by);
				$tempUpdatedBy = (isset($tempUpdatedBy["display_name_1"]) && $tempUpdatedBy["display_name_1"])? $tempUpdatedBy["display_name_1"]: "No assigned name";
				if($row->updated_by){
					$row->updated_name = $tempUpdatedBy;
					$row->updated_at = date("F d, Y h:i A", strtotime($row->updated_at));
				}

				$tempApprovedBy = $this->core_layout->getEmployeeData($row->approved_by);
				$tempApprovedBy = (isset($tempApprovedBy["display_name_1"]) && $tempApprovedBy["display_name_1"])? $tempApprovedBy["display_name_1"]: "No assigned name";
				if($row->approved_by){
					$row->approved_name = $tempApprovedBy;
					$row->approved_at = date("F d, Y h:i A", strtotime($row->approved_at));
				}

				$tempDisapprovedBy = $this->core_layout->getEmployeeData($row->disapproved_by);
				$tempDisapprovedBy = (isset($tempDisapprovedBy["display_name_1"]) && $tempDisapprovedBy["display_name_1"])? $tempDisapprovedBy["display_name_1"]: "No assigned name";
				if($row->disapproved_by){
					$row->disapproved_name = $tempDisapprovedBy;
					$row->disapproved_at = date("F d, Y h:i A", strtotime($row->disapproved_at));
				}

				$tempCancelledBy = $this->core_layout->getEmployeeData($row->cancelled_by);
				$tempCancelledBy = (isset($tempCancelledBy["display_name_1"]) && $tempCancelledBy["display_name_1"])? $tempCancelledBy["display_name_1"]: "No assigned name";
				if($row->cancelled_by){
					$row->cancelled_name = $tempCancelledBy;
					$row->cancelled_at = date("F d, Y h:i A", strtotime($row->cancelled_at));
				}

				$row->allow_edit = false;
				$row->allow_approval = false;
				$row->allow_undo_cancel = false;

				if(($row->created_by == $this->core_layout->getCurrentEmployeeId() 
				|| $row->employee_id == $this->core_layout->getCurrentEmployeeId()) && $row->status == "0"){
					$row->allow_edit = true;
				}

				if($row->status == "0"){ $row->allow_approval = true; }
				if($row->status !== "0"){ $row->allow_undo_cancel = true; }

				$tempImage = ($row->attachment_image)? unserialize($row->attachment_image): array();
                if(is_array($tempImage) && count($tempImage) > 0){
                    $row->has_attachment = true;
                    $_tempImages = array();
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
                        $tempRow["image"] = base_url("uploads/files/images/rtw/{$value}");
                        $tempRow["thumbnail"] = base_url("uploads/files/images/rtw/{$thumbnail}");
                        $_tempImages[] = $tempRow;
                    }
                    $row->images = $_tempImages;
                }else{
                    $row->has_attachment = false;
				}
				
				$resultset["response"] = true;
				$resultset["row"] = $row;
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}
		return $resultset;
	}

	function approveRtw($id=null){
        $post = $this->input->post();
        $attachment = (isset($post["attachment_image"]) && $post["attachment_image"])? $post["attachment_image"]: array();
		$attachment = serialize($attachment);
		
		$where = array('id'=> $id);
        $data = array(
			'status' => 1,
			'approved_by' => $this->core_layout->getCurrentEmployeeId(),
			'approved_at' => date('Y-m-d H:i:s'),
			'attachment_image' => $attachment,
        );

		$qTempx = $this->db->get_where($this->returnToWorkTable, $where);
		if($qTempx->num_rows() == 1){
			$qTempRow = $qTempx->row();
			$updated = $this->db->update($this->returnToWorkTable, $data, $where);
			if($updated && $this->db->affected_rows() > 0){
				$tempSession = $this->core_layout->getEmployeeData();
				$tempSession = (object) $tempSession;
				$empName = (isset($tempSession->display_name_1) && $tempSession->display_name_1)? $tempSession->display_name_1: "No assigned name";
				$resultset["response"] = true;
				$resultset['toastr_msg'] = "Return to work request has been approved!";
				$this->core_layout->setEventLog("View Return to Work - Approve return to work {$this->getRTWReference($this->input->post('id'))}.", "update", "success", "gcceforms", "user");
			}else{
				$resultset['response'] = false;
				$resultset['toastr_msg'] = "Error updating form!";
				$this->core_layout->setEventLog("View Return to Work - Failed approve return to work {$this->getRTWReference($this->input->post('id'))}.", "update", "error", "gcceforms", "system");
			}
		}else{
			$resultset['response'] = false;
			$resultset['toastr_msg'] = "Data not found!";
		}

        return $resultset;
	}
	
	function tempUploadFile() {
        $resultset = array();
        $session = $this->core_layout->getCurrentSession();

        if (isset($session["emp_id"]) && $session["emp_id"]) {
            $imagesPath = "./uploads/files/images/rtw/temp_{$session["emp_id"]}";

            $createFilePath = false;

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
                $config = array();
                $config['upload_path'] = $imagesPath;
                $config['allowed_types'] = 'jpg|jpeg|png|PNG|JPG|JPEG';
                $config['max_size'] = 10000;
                $config['create_thumbnail'] = true;

                $data = $this->file_upload->uploadFile($config);
                if ($data["response"] == true) {
                    $thumbnailpath = "./uploads/files/images/rtw/temp_{$session["emp_id"]}/thumbnails";

                    if (!file_exists(realpath($thumbnailpath))) {
                        mkdir($thumbnailpath, 0777, true);
                    }
                    $files = $data["files"][0];
                    $filename = $files["file_name"];
                    if ($filename) {
                        $dirpath = $imagesPath;
                        $s = $this->saveThumbnail($dirpath . "/" . $filename, $thumbnailpath . "/" . $filename);

                        $resultset["response"] = true;

                        $resultset["added_image"] = base_url("uploads/files/images/rtw/temp_{$session["emp_id"]}/{$filename}");
                        $resultset["temp_image"] = "{$filename}";

                        $resultset["toastr_msg"] = "Upload image successful.";
                        $resultset["toastr_state"] = "success";
                    } else {
                        $resultset["response"] = false;
                        $resultset["toastr_msg"] = "Image upload to specific path failed!";
                        $resultset["toastr_state"] = "error";
                    }
                } else {
                    $resultset["response"] = false;
                    $resultset["toastr_msg"] = "Image upload failed!";
                    $resultset["toastr_state"] = "error";
                }
            }

        } else {
            $resultset["response"] = false;
            $resultset["toastr_msg"] = "Employee data not found!";
            $resultset["toastr_state"] = "error";
        }

        return $resultset;
    }

    function getCurrentUploadedFile(){
        $resultset = array();
        $session = $this->core_layout->getCurrentSession();
        if(isset($session["emp_id"]) && $session["emp_id"]){
            $arrData = array();
            $imagesPath = "./uploads/files/images/rtw/temp_{$session["emp_id"]}";
            $thumbnailpath = "./uploads/files/images/rtw/temp_{$session["emp_id"]}/thumbnails";
            if(file_exists($imagesPath)){
                $tempFiles = scandir($imagesPath);
                if(is_array($tempFiles) && count($tempFiles) > 0){
                    foreach ($tempFiles as $key => $value) {
                        if($value !== "thumbnails" && $value !== "." && $value !== ".."){
                            $tempRow = array();
                            $tempRow["filename"] = $value;
                            $tempRow["current_image"] = "temp_{$session['emp_id']}/{$value}";
                            $tempRow["image"] = base_url("uploads/files/images/rtw/temp_{$session['emp_id']}/{$value}");
                            $tempRow["thumbnail"] = base_url("uploads/files/images/rtw/temp_{$session['emp_id']}/thumbnails/{$value}");
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
        }else{
            $resultset["response"] = false;
        }

        return $resultset;
    }

	function disapproveRtw($id=null){
		$resultset = array();
		if($id){
			$where = array("id"=>$id);
			$tempData = array();
			$tempData["disapproved_by"] = $this->core_layout->getCurrentEmployeeId();
			$tempData["disapproved_at"] = date("Y-m-d H:i:s");
			$tempData["attachment_image"] = serialize(array());
			$tempData["status"] = 2;
			$qTempx = $this->db->get_where($this->returnToWorkTable, $where);
			if($qTempx->num_rows() == 1){
				$qTempRow = $qTempx->row();
				$updated = $this->db->update($this->returnToWorkTable, $tempData, $where);
				if($updated && $this->db->affected_rows() == 1){
					$tempSession = $this->core_layout->getEmployeeData();
					$tempSession = (object) $tempSession;
					$empName = (isset($tempSession->display_name_1) && $tempSession->display_name_1)? $tempSession->display_name_1: "No assigned name";
					$this->core_layout->setEventLog("View Return to Work - Disapprove return to work {$this->getRTWReference($id)}.", "update", "success", "gcceforms", "user");
					$resultset["response"] = true;
				}else{
					$this->core_layout->setEventLog("View Return to Work - Failed disapprove return to work {$this->getRTWReference($id)}.", "update", "error", "gcceforms", "system");
					$resultset["response"] = false;
				}
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}

		return $resultset;
	}

	function cancelledRtw($id=null){
		$resultset = array();
		if($id){
			$post = $this->input->post();
			if(isset($post["csrf_token"]) && $post["csrf_token"]){ unset($post["csrf_token"]); }
			$where = array("id"=>$id);
			$tempData = array();
			$tempData["cancelled_by"] = $this->core_layout->getCurrentEmployeeId();
			$tempData["cancelled_at"] = date("Y-m-d H:i:s");
			$tempData["status"] = 3;
			if(isset($post["cancelled_remarks"]) && $post["cancelled_remarks"]){
				$tempData["cancelled_remarks"] = $post["cancelled_remarks"];
			}
			$qTempx = $this->db->get_where($this->returnToWorkTable, $where);
			if($qTempx->num_rows() == 1){
				$qTempRow = $qTempx->row();
				$updated = $this->db->update($this->returnToWorkTable, $tempData, $where);
				if($updated && $this->db->affected_rows() == 1){
					$tempSession = $this->core_layout->getEmployeeData();
					$tempSession = (object) $tempSession;
					$empName = (isset($tempSession->display_name_1) && $tempSession->display_name_1)? $tempSession->display_name_1: "No assigned name";
					$this->core_layout->setEventLog("View Return to Work - Cancel return to work {$this->getRTWReference($id)}.", "update", "success", "gcceforms", "user");
					$resultset["response"] = true;
				}else{
					$this->core_layout->setEventLog("View Return to Work - Failed cancel return to work {$this->getRTWReference($id)}.", "update", "error", "gcceforms", "system");
					$resultset["response"] = false;
				}
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}

		return $resultset;
	}

	function undoRtwApproval($id=null){
		$resultset = array();
		if($id){
			$where = array("id"=>$id);
			$tempData = array();
			$tempData["approved_by"] = 0;
			$tempData["approved_at"] = "0000-00-00 00:00:00";
			$tempData["updated_by"] = $this->core_layout->getCurrentEmployeeId();
			$tempData["updated_at"] = date("Y-m-d H:i:s");
			$tempData["attachment_image"] = serialize(array());
			$tempData["status"] = 0;
			$qTempx = $this->db->get_where($this->returnToWorkTable, $where);
			if($qTempx->num_rows() == 1){
				$qTempRow = $qTempx->row();
				$updated = $this->db->update($this->returnToWorkTable, $tempData, $where);
				if($updated && $this->db->affected_rows() == 1){
					$tempSession = $this->core_layout->getEmployeeData();
					$tempSession = (object) $tempSession;
					$empName = (isset($tempSession->display_name_1) && $tempSession->display_name_1)? $tempSession->display_name_1: "No assigned name";
					$this->core_layout->setEventLog("View Return to Work - Undo approve return to work {$this->getRTWReference($id)}.", "update", "success", "gcceforms", "user");
					$resultset["response"] = true;
				}else{
					$this->core_layout->setEventLog("View Return to Work - Failed undo approve return to work {$this->getRTWReference($id)}.", "update", "error", "gcceforms", "system");
					$resultset["response"] = false;
				}
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}

		return $resultset;
	}

	function undoRtwDispproval($id=null){
		$resultset = array();
		if($id){
			$where = array("id"=>$id);
			$tempData = array();
			$tempData["disapproved_by"] = 0;
			$tempData["disapproved_at"] = "0000-00-00 00:00:00";
			$tempData["updated_by"] = $this->core_layout->getCurrentEmployeeId();
			$tempData["updated_at"] = date("Y-m-d H:i:s");
			$tempData["attachment_image"] = serialize(array());
			$tempData["status"] = 0;
			$qTempx = $this->db->get_where($this->returnToWorkTable, $where);
			if($qTempx->num_rows() == 1){
				$qTempRow = $qTempx->row();
				$updated = $this->db->update($this->returnToWorkTable, $tempData, $where);
				if($updated && $this->db->affected_rows() == 1){
					$tempSession = $this->core_layout->getEmployeeData();
					$tempSession = (object) $tempSession;
					$empName = (isset($tempSession->display_name_1) && $tempSession->display_name_1)? $tempSession->display_name_1: "No assigned name";
					$this->core_layout->setEventLog("View Return to Work - Undo disapprove return to work {$this->getRTWReference($id)}.", "update", "success", "gcceforms", "user");
					$resultset["response"] = true;
				}else{
					$this->core_layout->setEventLog("View Return to Work - Failed undo disapprove return to work {$this->getRTWReference($id)}.", "update", "error", "gcceforms", "system");
					$resultset["response"] = false;
				}
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}

		return $resultset;
	}

	function undoRtwDisapproval($id=null){
		$resultset = array();
		if($id){
			$where = array("id"=>$id);
			$tempData = array();
			$tempData["disapproved_by"] = 0;
			$tempData["disapproved_at"] = "0000-00-00 00:00:00";
			$tempData["updated_by"] = $this->core_layout->getCurrentEmployeeId();
			$tempData["updated_at"] = date("Y-m-d H:i:s");
			$tempData["attachment_image"] = serialize(array());
			$tempData["status"] = 0;
			$qTempx = $this->db->get_where($this->returnToWorkTable, $where);
			if($qTempx->num_rows() == 1){
				$qTempRow = $qTempx->row();
				$updated = $this->db->update($this->returnToWorkTable, $tempData, $where);
				if($updated && $this->db->affected_rows() == 1){
					$tempSession = $this->core_layout->getEmployeeData();
					$tempSession = (object) $tempSession;
					$empName = (isset($tempSession->display_name_1) && $tempSession->display_name_1)? $tempSession->display_name_1: "No assigned name";
					$this->core_layout->setEventLog("View Return to Work - Undo dissapprove return to work {$this->getRTWReference($id)}.", "update", "success", "gcceforms", "user");
					$resultset["response"] = true;
				}else{
					$this->core_layout->setEventLog("View Return to Work - Undo dissapprove return to work {$this->getRTWReference($id)}.", "update", "error", "gcceforms", "system");
					$resultset["response"] = false;
				}
			}else{
				$resultset["response"] = false;
			}
		}else{
			$resultset["response"] = false;
		}

		return $resultset;
	}

    function saveThumbnail($source, $target) {
        $config = array(
            'image_library' => 'gd2',
            'source_image' => $source,
            'new_image' => $target,
            'maintain_ratio' => TRUE,
            'create_thumb' => TRUE,
            'thumb_marker' => '',
            'width' => 75,
            'height' => 75
        );
        $this->image_lib->initialize($config);
        $this->image_lib->resize();
        $this->image_lib->clear();
	}
	
	function printApprovedRtw($id=null){
		$data = array();
		if($id){
			$query = $this->db->get_where($this->returnToWorkTable, array("id"=>$id, "status"=>1));
			if($query->num_rows() == 1){
				$tempData = $this->getRtwData($query->row()->id);
				$tempData = (object) $tempData;
				if($tempData->response == true){
					$data = $tempData->row;
				}
			}
		}
		return $data;
	}

	function generateReferenceNo(){
		$qData = $this->db->get($this->returnToWorkTable);
		$tempCount = $qData->num_rows();
		$tempCount = ($tempCount == 0)? 1: intval($tempCount) + 1;
		$tempSeries = str_pad($tempCount, 4, "0", STR_PAD_LEFT);
		$tempYear = Date("y");
		$tempMonth = Date("m");
		$tempCode = "RWF{$tempYear}-{$tempMonth}-{$tempSeries}";
		return $tempCode;
	}

    function advancedSearchRequest(){
        $resultset = array();
        $resultset["has_error"] = false;
        $post = $this->input->post();

        if(isset($post) && $post){
            $tempHasError = false;
            $arrIds = array();
            $filteredIds = array();

            if(isset($post["date_time"]) && $post["date_time"]){
                $tempDates = explode("-", $post["date_time"]);
                $this->db->from($this->returnToWorkTable);
                $this->db->where("status !=", 3);
                $queryFilterDates = $this->db->get();
                if($queryFilterDates->num_rows() > 0){
                    foreach ($queryFilterDates->result() as $kk => $vv) {
                        if(count($tempDates) == 2 && is_array($tempDates)){
                            $filterDate1 = date("Y-m-d", strtotime(trim($tempDates[0])));
                            $filterDate2 = date("Y-m-d", strtotime(trim($tempDates[1])));
                            $xtempD1 = date("Y-m-d", strtotime($vv->from_date));
                            $xtempD2 = date("Y-m-d", strtotime($vv->to_date));
                            if(($xtempD1 >= $filterDate1) && ($xtempD1 <= $filterDate2)){
                                if(!in_array($vv->id, $filteredIds)){ $filteredIds[] = $vv->id; }
                            }
                            if(($xtempD2 >= $filterDate1) && ($xtempD2 <= $filterDate2)){
                                if(!in_array($vv->id, $filteredIds)){ $filteredIds[] = $vv->id; }
                            }
                        }
                    }
                }
            }
			
            $this->db->select("*");
            $this->db->from($this->returnToWorkTable);
            $this->db->where("status !=", 3);
            if(isset($post["status"]) && ($post["status"] || $post["status"] == "0")){
                $this->db->where("status", $post["status"]);
            }
            if(isset($post["employee_id"]) && $post["employee_id"]){
                $this->db->where("employee_id", $post["employee_id"]);
            }
            
            if(isset($post["date_time"]) && $post["date_time"] && count($filteredIds) > 0){
                $this->db->where_in("id", $filteredIds);
            }
            if(isset($post["date_time"]) && $post["date_time"] && count($filteredIds) == 0){ $tempHasError = true; }

			$queryFilter = $this->db->get();
            if($queryFilter->num_rows() > 0){
                foreach ($queryFilter->result() as $key => $value) {
                    if(!in_array($value->id, $arrIds)){ $arrIds[] = $value->id; }
                }
            }

            $resultset["response"] = true;
            $resultset["ids"] = $arrIds;
            if(empty($arrIds) || $tempHasError){
                $resultset["has_error"] = true;
                $resultset["toastr_msg"] = "Filtered search data not found!";
            }
        }else{
            $resultset["response"] = false;
        }

        return $resultset;
    }

	function exportData($export){
		if($export == 1){
			$this->core_layout->setEventLog("Return To Work Masterfile - Export excel file of Return To Work Masterfile.","export", "success", "gcceforms", "user");
		}elseif($export == 2){
			$this->core_layout->setEventLog("Return To Work Masterfile - Export csv file of Return To Work Masterfile.","export", "success", "gcceforms", "user");
		}else{
			$this->core_layout->setEventLog("Return To Work Masterfile - Export pdf file of Return To Work Masterfile.","export", "success", "gcceforms", "user");
		}
	}

}