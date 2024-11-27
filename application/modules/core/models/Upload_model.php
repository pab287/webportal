<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Upload_model extends CI_Model{
	function __construct(){
		parent::__construct();
	}
	
	function uploadFile($config=array()){
		$resultset = array();
		$files = (isset($_FILES["files"]) && $_FILES["files"])? $_FILES["files"]: false;
		if($config){
			$tempFileName = (isset($config["input_field"]) && $config["input_field"])? $config["input_field"]: "files";
			$files = (isset($config["input_field"]) && $config["input_field"])? $_FILES[$config["input_field"]]: $files;
			$createThumbnail = false;
			$uploadPath = realpath(".uploads/files/images");

			if(isset($config["create_thumbnail"]) && $config["create_thumbnail"] == true){
				unset($config["create_thumbnail"]);
				$uploadPath = $config["upload_path"];
				$createThumbnail = true;
			}

			$this->upload->initialize($config);
			
			if($files){
				if(count((array)$files["name"]) > 1){
					foreach($files["name"] as $key => $image){
						$_FILES[$tempFileName]["name"] = $files["name"][$key];
						$_FILES[$tempFileName]["type"] = $files["type"][$key];
						$_FILES[$tempFileName]["tmp_name"] = $files["tmp_name"][$key];
						$_FILES[$tempFileName]["error"] = $files["error"][$key];
						$_FILES[$tempFileName]["size"] = $files["size"][$key];
					}
				}
				
				if (!$this->upload->do_upload($tempFileName)){
						$error = array('error' => $this->upload->display_errors());
						$resultset["response"] = false;
						$resultset["data"] = $error;
						$resultset["message"] = $this->upload->display_errors();
				}else{
					$data = $this->upload->data();
					if($data){
						if($createThumbnail == true){
							$tempFileName = $data["file_name"];
							$created = $this->createThumbnailPathFolder($uploadPath);
							if($created){
								$this->resizeImage($tempFileName, $uploadPath);
							}
						}

						$resultset["response"] = true;
						$resultset["files"][] = $data;
						$resultset["message"] = "upload file successful.";
					}else{
						$resultset["response"] = false;
						$resultset["message"] = "no data to upload!";
					}
				}
			}else{
				$resultset["response"] = false;				
				$resultset["message"] = "no file to upload!";
			}
		}else{
			$resultset["response"] = false;
			$$resultset["message"] = "upload configuration was not set!";
		}
		
		return $resultset;
	}

	public function createThumbnailPathFolder($currentPath=null){
		if($currentPath){
			$createFilePath = false;
			$thumbnailPath = rtrim($currentPath, '/')."/thumbnails";

			if (!file_exists($thumbnailPath)) {
                $mkdir = mkdir($thumbnailPath, 0775, true);
                if ($mkdir){ $createFilePath = true; }
			}else{ $createFilePath = true; }
			
			if($createFilePath){ return true; }
			else{ return false; }
		}else{ return false; }
	}

	public function resizeImage($filename=null, $filepath=null){
		$config = array();
		$source_path = rtrim($filepath, '/')."/".$filename;
		$target_path = rtrim($filepath, '/')."/thumbnails";

		// original config in resizing image
		/*** $config_manip = array(
			 	'image_library' => 'gd2',
			 	'source_image' => $source_path,
			 	'new_image' => $target_path,
			 	'maintain_ratio' => TRUE,
			 	'create_thumb' => TRUE,
			 	'thumb_marker' => '',
			 	'width' => 75,
			 	'height' => 75
		 ); */

		$config['image_library'] = 'gd2';
		$config['source_image'] = $source_path;
		$config['new_image'] = $target_path;
		$config['maintain_ratio'] = TRUE;
		$config['create_thumb'] = TRUE;
		$config['thumb_marker'] = '';
		$config['width'] = 75;
		$config['height'] = 75;
  
		$this->load->library('image_lib', $config);

		// added for resizing of image to thumbnail
		$this->image_lib->initialize($config);
		// added for resizing of image to thumbnail

		if (!$this->image_lib->resize()) { return false; }
		else{ return true; }
		$this->image_lib->clear();
	}
	 
	 public function checkImagePath($image=null, $filepath=null){
		 if($image){
			 $filePath = realpath($filepath);
			 $file = $filePath."/".$image;
			 
			 if(file_exists($file)){ return true; }
			 else{ return false; }
		 }else{ return false; }
	 }
	 
	 public function checkImageThumbnailPath($image=null, $filepath=null){
		 if($image){
			 $filePath = realpath($filepath);
			 $file = $filePath."/".$image;
			 
			 if(file_exists($file)){ return true; }
			 else{ return false; }
		 }else{ return false; }
	 }

	 public function moveUploadedFile($file=null, $fromFolder=null, $destinationFolder=null, $createThumb=false){
        if($file && $fromFolder && $destinationFolder){
            $session = $this->core_layout->getCurrentSession();

            $imagesPath = $fromFolder."/".$file;
            if (file_exists($imagesPath)) {
                $createFilePath = false;

                if (!file_exists($destinationFolder)) {
                    $mkdir = mkdir($destinationFolder, 0775, true);
                    if ($mkdir){ $createFilePath = true; }
                }else{ $createFilePath = true; }

                if($createFilePath == false){
                    return false;
                }else{
                    $newFolder = $destinationFolder."/".$file;
					rename($imagesPath, $newFolder);
					
					if($createThumb){
						$created = $this->createThumbnailPathFolder($destinationFolder);
						if($created){
							$this->resizeImage($file, $destinationFolder);
						}
					}
                    return true;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

	public function generateUnusedFiles(){
		ini_set('max_execution_time', 3600);
		$rootFilePath = "./uploads/files/images/assets";
		$logFilePath = "./uploads/logs";
		if(is_dir($rootFilePath)){
			$file = scandir($rootFilePath);
			$data = array();
			foreach ($file as $key => $value) {
				if($value !== "." && $value !== ".."){
					$tempFilePath = $rootFilePath."/".$value;
					if(is_dir($tempFilePath)){
						$fileTemp = scandir($tempFilePath);
						foreach ($fileTemp as $kk => $vv) {
							if($vv !== "." && $vv !== ".." && $vv !== "thumbnails"){
								$data[$value][] = array("image"=>$vv);
							}
						}
					}
				}
			}
			if(is_array($data) && count($data) > 0){
				$tempJson = json_encode($data);
				$tempDate = Date("Ymd");
				$tempFilename = "{$logFilePath}/{$tempDate}-logs.txt"; 
				$myfile = fopen($tempFilename, "w") or die("Unable to open file!");
				fwrite($myfile, $tempJson);
				fclose($myfile);
			}
		}
	}

	public function getUnusedFiles(){
		ini_set('max_execution_time', 3600);
		$rootFilePath = "./uploads/logs";
		$tempDate = Date("Ymd");
		$tempFilename = "{$rootFilePath}/{$tempDate}-logs.txt"; 
		if(file_exists($tempFilename)){
			$file = file_get_contents($tempFilename, true);
			$arrData = json_decode($file, true);
			echo "<pre>";
			$ctr = 0;
			foreach ($arrData as $key => $value) {
				$ctr++;
				foreach ($value as $kk => $vv) {
					$this->db->from("gccasset.asset_image as a");
					$this->db->join("gccasset.assets as b", "b.id = a.asset_id");
					$this->db->where("b.id", $key);
					$this->db->group_start();
					$this->db->where("b.pic_filename", $vv["image"]);
					$this->db->or_where("a.image", $vv["image"]);
					$this->db->group_end();
					$qTemp0 = $this->db->get();
					if($qTemp0->num_rows() == 0){
						var_dump($key);
					}
					if($ctr == 10){ break; }
				}
			}
		}
	}
}