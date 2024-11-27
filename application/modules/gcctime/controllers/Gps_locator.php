<?php defined('BASEPATH') OR exit('No direct script access allowed');
include(dirname(__DIR__).'/src/AbstractGeocoder.php');
include(dirname(__DIR__).'/src/Geocoder.php');
class Gps_locator extends MY_Controller {
	function __construct(){
        parent::__construct();
        $this->authenticate->setModuleAccess("time");
        $this->authenticate->doRedirect();
		$this->load->model("Crudv2_model","crud2");
		$this->core_layout->setBodyClass("attendance");
		$this->core_layout->setPrivilegeName("gps");
    }

	public function index(){
		// $externalUrl = "https://maps.googleapis.com/maps/api/js?key=AIzaSyD2szEzfIU7_Hec55jNy8JtoNr_uj8R2_M&callback=initMapTemp&libraries=places,drawing&v=weekly";
		// $externalUrl = "https://maps.googleapis.com/maps/api/js?key=AIzaSyCTzlKHdtvrOuKv7LEQjW8HVmy1QFFgalM&callback=initMapTemp&libraries=places,drawing&v=weekly";
		$externalUrl = "https://maps.googleapis.com/maps/api/js?key=AIzaSyCm_pTwQzhaAKspErhW9ptpubv_ATLrpgE&callback=initMapTemp&libraries=places,drawing&v=weekly";
		$arrData = array();
		$arrData["script_attribute"] = array("async");
		$this->core_layout->addExternalJs($externalUrl, true, $arrData);
		$this->load->view('core/templates/header');
		$this->load->view('imageview/index');
		$this->load->view('core/templates/footer');
	}

	public function convertor(){
		$query = $this->crud2->getCollection(array(),"gcctimeutility.images",array("id"=>"DESC"));
			if($query){
				foreach($query as $_query){
					if($_query["longtitude"]!=null&&$_query["address"]==null){
						$temp = $_query["longtitude"];
						$temp2 = $_query["latitude"];

						$float  = floatval($temp);
						$float2  = floatval($temp2);

						$temp3 = $float2.','.$float;			  
						$geocoder = new \OpenCage\Geocoder\Geocoder('b4aa53163f2047dba4aba426690a986b');
						$result = $geocoder->geocode($temp3); # latitude,longitude (y,x)
						$temp4 = $result['results'][0]['formatted'];
						// $temp4 = $this->geo2address($temp,$temp2);

						$data = array('address' => $temp4);
						$this->crud->update2(array('id' => $_query["id"]), $data);
						echo json_encode(array("status" => TRUE));
					}
				}
			}
		}

	public function select(){
		$result=$this->crud2->select();
		foreach ($result as $myList) {
			$x=$myList->image;
		}
		if($x==""){
			$x=site_url("uploads/module/gcctime/images/no.png");
		}
		echo json_encode($x);
	}
	public function select2(){
		$result=$this->crud2->select();
		foreach ($result as $myList) {
			$x=$myList->longtitude;
			$y=$myList->latitude;
		}
		$z[0]=$x;
		$z[1]=$y;
		echo json_encode($z);
	}
	public function maptest($long,$lat){
		$this->load->library('googlemaps');
		$config['center'] = $lat.",".$long;
		$config['zoom'] = 'auto';
		$config['map_type'] = 'HYBRID';
		$this->googlemaps->initialize($config);

		$marker = array();
		$marker['position'] = $lat.",".$long;
		$this->googlemaps->add_marker($marker);
		$data['map'] = $this->googlemaps->create_map();
		$this->load->view('gps/map', $data);
	}

	public function geo2address($long,$lat) {
	    // $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$long."&language=en-EN&sensor=false&key=AIzaSyCTzlKHdtvrOuKv7LEQjW8HVmy1QFFgalM";
	    $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$long."&language=en-EN&sensor=false&key=AIzaSyB0P6151i4JuPBG79VhRhaiEzqR4Awmnmw";
	    $curlData=file_get_contents($url);
	    $address = json_decode($curlData);
	    $a=$address->results[0];

	    return $a->formatted_address;
	}

	public function getimage()
	{
		$data = $this->crud2->get_all_images_data();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
	}

	public function locationImage()
	{
		$data = $this->crud2->get_location_image();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
	}

	public function locationCoords(){
		$data = $this->crud2->get_location_coords();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
	}

}