<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Upload extends MY_Controller {
	protected $itemsTable = "items";
	protected $uomTable = "uom";
	
	function __construct(){
		parent::__construct();
		$this->authenticate->doRedirect();
		$this->load->model("upload_model", "file_upload");
	}
	
	function excel_files(){
		$resultset = array();
		$config = array();
		$config['upload_path']          = './uploads/files/csv/';
		$config['allowed_types']        = 'csv';
		$config['max_size']             = 1000000;
		
		$session = $this->core_layout->getCurrentSession();
		$resultset = $this->file_upload->uploadFile($config);
		
		if(isset($resultset["response"]) && $resultset["response"]){
			$file = (isset($resultset["files"][0]["file_name"]) && $resultset["files"][0]["file_name"])? $resultset["files"][0]["file_name"]: "";
			if($file){
				$fileExist = realpath("uploads/files/csv/{$file}");
				if(file_exists($fileExist)){
					if ($fh = fopen($fileExist, 'r')) {
						$addedCount = 0;
						$existCount = 0;
						$count = 0;
						while (!feof($fh) && $row = fgetcsv($fh)) {
							if($count !== 0){
								$stockCode = trim($row[0]);
								$unit = trim($row[2]);
								$beginningQty = (isset($row[3]) && $row[3])? trim($row[3]): 0;
								$itemName = trim($row[1]);
								if($stockCode){
									$unitCode = 0;
									
									$data = array();
									$data["sku"] = $stockCode;
									$data["name"] = $itemName;
									$data["qty"] = $beginningQty;
									$data["beginning_qty"] = $beginningQty;
									$data["beginning_date"] = ($beginningQty)? date("Y-m-d H:i:s"): "0000-00-00 00:00:00";
									$data["created_by"] = $session["emp_id"];
									$data["created_at"] = date("Y-m-d H:i:s");
									
									$queryItem = $this->db->get_where($this->itemsTable, array("sku"=>$stockCode));
									if($queryItem->num_rows() == 0){
										if($unit){
											$queryUom = $this->db->get_where($this->uomTable, array("uom_code"=>$unit));
											if($queryUom->num_rows() == 1){
												$uomRow = $queryUom->row();
												$unitCode = $uomRow->id;
											}
										}
										
										$data["unit"] = $unitCode;
										$added = $this->db->insert($this->itemsTable, $data);
										if($added){ 
											$addedCount++; 
											$resultset["added_count"] = $addedCount;
										}
									}else{
										$existCount++;
										$resultset["existing_count"] = $existCount;
									}
								}
								
							}
							$count++;
						}
						fclose($fh);
						
						if($addedCount){
							$resultset["message"] .= ", {$addedCount} records has been imported.";							
						}else{
							$resultset["message"] .= ", current data are updated!";
						}
					}
				}
			}
		}
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}
	
	function update_stock_files(){
		$resultset = array();
		$config = array();
		$config['upload_path']          = './uploads/files/csv/';
		$config['allowed_types']        = 'csv';
		$config['max_size']             = 1000000;
		
		$session = $this->core_layout->getCurrentSession();
		$resultset = $this->file_upload->uploadFile($config);
		
		if(isset($resultset["response"]) && $resultset["response"]){
			$file = (isset($resultset["files"][0]["file_name"]) && $resultset["files"][0]["file_name"])? $resultset["files"][0]["file_name"]: "";
			if($file){
				$fileExist = realpath("uploads/files/csv/{$file}");
				if(file_exists($fileExist)){
					if ($fh = fopen($fileExist, 'r')) {
						$addedCount = 0;
						$count = 0;
						
						while (!feof($fh) && $row = fgetcsv($fh)) {
							if($count !== 0){
								$stockId = (isset($row[0]) && $row[0])? trim($row[0]): 0;
								$stockName = trim($row[1]);
								$stockSku = trim($row[2]);
								
								if($stockId){
									$where = array();
									$where["id"] = $stockId;
									
									$query = $this->db->get_where($this->itemsTable, $where);
									if($query->num_rows() == 1){
										$tempRow = array();
										$xrow = $query->row();
										$data = array();
										
										$tempRow["name"] = $xrow->name;
										$tempRow["sku"] = $xrow->sku;
										
										if($stockName && $stockName !== $xrow->name){
											$data["name"] = $stockName;
										$tempRow["name"] = "{$xrow->name} -> <strong>{$stockName}</strong>";
										}
										
										if($stockSku && $stockSku !== $xrow->sku){
											$data["sku"] = $stockSku;
											$tempRow["sku"] = "{$xrow->sku} -> <strong>{$stockSku}</strong>";
										}
										
										if($data){
											$update = $this->db->update($this->itemsTable, $data, $where);
											if($update){
												$resultset["dt_draw"][] = $tempRow;
												$addedCount++;
											}								
										}
									}
								}
							}
							
							$count++;
						}
						fclose($fh);
						
						if($addedCount){
							$resultset["message"] .= ", {$addedCount} records has been updated.";							
						}else{
							$resultset["message"] .= ", current data are updated!";
						}
					}
					
					$resultset["added_count"] = $addedCount;
				}
			}
		}
		
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}

	function generate_unused_files(){
		$resultset = array();
		$resultset = $this->file_upload->generateUnusedFiles();

		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}
	function get_unused_files(){
		$resultset = array();
		$resultset = $this->file_upload->getUnusedFiles();

		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($resultset));
	}
}