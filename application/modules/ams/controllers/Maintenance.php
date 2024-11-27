<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Maintenance extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->authenticate->setModuleAccess("ams");
        $this->authenticate->doRedirect();
        $this->load->model("Maintenance_model", "maintenance");

        $this->core_layout->setPrivilegeName("fixed_masterfile");
    }

    public function category()
    {
        $this->core_layout->addJs("js/ams/category.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('ams/maintenance/category');
        $this->load->view('core/templates/footer');
    }

    function get_category_collection()
    {
        $data = $this->maintenance->getCategoryCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function add_category()
    {

        $data = array(
            'code' => $this->input->post('code'),
            'description' => $this->input->post('description'),
            'type' => $this->input->post('type')
        );
        $insert = $this->maintenance->save_category($data);
        echo json_encode(array("status" => TRUE));
    }

    public function edit_category($id)
    {
        $data = $this->maintenance->edit_category($id);
        echo json_encode($data);
    }

    public function update_category()
    {

        $data = array(
            'code' => $this->input->post('code'),
            'description' => $this->input->post('description'),
            'type' => $this->input->post('type')
        );
        $this->maintenance->update_category(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function delete_category($id)
    {
        $this->maintenance->delete_category($id);
        echo json_encode(array("status" => TRUE));
    }

    public function sub_category()
    {
        $this->core_layout->addJs("js/ams/sub_category.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('ams/maintenance/sub_category');
        $this->load->view('core/templates/footer');
    }

    function get_sub_category_collection()
    {
        $data = $this->maintenance->getSubCategoryCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_category()
    {
        $data = $this->maintenance->getCategory();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function add_sub_category()
    {

        $data = array(
            'cat_id' => $this->input->post('cat_id'),
            'sub_cat_code' => $this->input->post('code'),
            'sub_cat_desc' => $this->input->post('description')
        );
        $insert = $this->maintenance->save_sub_category($data);
        echo json_encode(array("status" => TRUE));
    }

    public function edit_sub_category($id)
    {
        $data = $this->maintenance->edit_sub_category($id);
        echo json_encode($data);
    }

    public function update_sub_category()
    {

        $data = array(
            'cat_id' => $this->input->post('cat_id'),
            'sub_cat_code' => $this->input->post('code'),
            'sub_cat_desc' => $this->input->post('description')
        );
        $this->maintenance->update_sub_category(array('sub_cat_id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function delete_sub_category($id)
    {
        $this->maintenance->delete_sub_category($id);
        echo json_encode(array("status" => TRUE));
    }

    public function station()
    {
        $this->core_layout->addJs("js/ams/station.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('ams/maintenance/station');
        $this->load->view('core/templates/footer');
    }

    function get_station_collection()
    {
        $data = $this->maintenance->getStationCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function add_station()
    {

        $data = array(

            'station' => $this->input->post('station')
        );
        $insert = $this->maintenance->save_station($data);
        echo json_encode(array("status" => TRUE));
    }

    public function edit_station($id)
    {
        $data = $this->maintenance->edit_station($id);
        echo json_encode($data);
    }

    public function update_station()
    {

        $data = array(

            'station' => $this->input->post('station')
        );
        $this->maintenance->update_station(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function delete_station($id)
    {
        $this->maintenance->delete_station($id);
        echo json_encode(array("status" => TRUE));
    }

    public function location()
    {
        // $externalUrl = "https://maps.googleapis.com/maps/api/js?key=AIzaSyD2szEzfIU7_Hec55jNy8JtoNr_uj8R2_M&callback=initMap&libraries=places,drawing";
        // $externalUrl = "https://maps.googleapis.com/maps/api/js?key=AIzaSyCTzlKHdtvrOuKv7LEQjW8HVmy1QFFgalM&callback=initMap&libraries=places,drawing";
        $externalUrl = "https://maps.googleapis.com/maps/api/js?key=AIzaSyCm_pTwQzhaAKspErhW9ptpubv_ATLrpgE&callback=initMap&libraries=places,drawing";

        $this->core_layout->addJs("js/ams/location.js", true);
        $arrData = array();
        $arrData["script_attribute"] = array("async");
        $this->core_layout->addExternalJs($externalUrl, true, $arrData);

        $this->load->view('core/templates/header');
        $this->load->view('ams/maintenance/location');
        $this->load->view('core/templates/footer');
    }

    function get_location_collection()
    {
        $data = $this->maintenance->getLocationCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function add_location()
    {
        $latitude = 0;
        $longitude = 0;

        if($this->input->post('locationCoords')){
            $coordinates = json_decode($this->input->post('locationCoords'));
            $latitude = $coordinates->lat;
            $longitude = $coordinates->lng;
        }

        $data = array(
            'location' => $this->input->post('location'),
            'latitude' => $latitude,
            'longitude' => $longitude,
        );
        $insert = $this->maintenance->save_location($data);
        echo json_encode(array("status" => TRUE));
    }

    public function edit_location($id)
    {
        $data = $this->maintenance->edit_location($id);
        echo json_encode($data);
    }

    public function update_location()
    {

        $latitude = 0;
        $longitude = 0;

        if($this->input->post('locationCoords')){
            $coordinates = json_decode($this->input->post('locationCoords'));
            $latitude = $coordinates->lat;
            $longitude = $coordinates->lng;
        }

        $data = array(

            'location' => $this->input->post('location'),
            'latitude' => $latitude,
            'longitude' => $longitude,
        );
        $this->maintenance->update_location(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function delete_location($id)
    {
        $this->maintenance->delete_location($id);
        echo json_encode(array("status" => TRUE));
    }

    public function equipment_type()
    {
        $this->core_layout->addJs("js/ams/equipment_type.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('ams/maintenance/equipment_type');
        $this->load->view('core/templates/footer');
    }

    function get_equipment_type_collection()
    {
        $data = $this->maintenance->getEquipmentTypeCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_equipment_category()
    {
        $data = $this->maintenance->getEquipCategory();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    public function add_equipment_type()
    {

        $data = array(
            'ec_id' => $this->input->post('ec_id'),
            'code' => $this->input->post('code'),
            'description' => $this->input->post('description')
        );
        $insert = $this->maintenance->save_equipment_type($data);
        echo json_encode(array("status" => TRUE));
    }

    public function edit_equipment_type($id)
    {
        $data = $this->maintenance->edit_equipment_type($id);
        echo json_encode($data);
    }

    public function update_equipment_type()
    {

        $data = array(
            'ec_id' => $this->input->post('ec_id'),
            'code' => $this->input->post('code'),
            'description' => $this->input->post('description')
        );
        $this->maintenance->update_equipment_type(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function delete_equipment_type($id)
    {
        $this->maintenance->delete_equipment_type($id);
        echo json_encode(array("status" => TRUE));
    }
}