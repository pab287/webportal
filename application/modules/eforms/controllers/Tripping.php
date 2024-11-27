<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Tripping extends MY_Controller {
	public function __construct(){
        parent::__construct();
        $this->authenticate->setModuleAccess("eforms-tripping");
        $this->authenticate->doRedirect();
        $this->core_layout->setPrivilegeName("eforms-tripping");
    
        $this->load->model("tripping_m","tripping");
    }

    function index(){
        $this->core_layout->setPageTitle("Driver's Incentive - Dashboard");
        $this->core_layout->addJs("global/js/amcharts4/core.js", true);
        $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
        $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
        $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);
        $this->core_layout->addJs("js/eforms/billing/index.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/tripping/index');
        $this->load->view('core/templates/footer');
    }

    function drivers(){
        $this->core_layout->setPageTitle("Driver's Incentive - Tripping Drivers");
        $this->core_layout->setPrivilegeName("eforms_tripping-drivers");
        $this->core_layout->addJs("js/eforms/tripping/drivers.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/tripping/drivers');
        $this->load->view('core/templates/footer');
    }

    function projects(){
        $this->core_layout->setPageTitle("Driver's Incentive - Tripping Projects");
        $this->core_layout->setPrivilegeName("eforms_tripping-projects");
        $this->core_layout->addJs("js/eforms/tripping/project.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/tripping/projects');
        $this->load->view('core/templates/footer');
    }

    function routes(){
        $this->core_layout->setPageTitle("Driver's Incentive - Tripping Routes");
        $this->core_layout->setPrivilegeName("eforms_tripping-routes");
        $this->core_layout->addJs("js/eforms/tripping/routes.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/tripping/routes');
        $this->load->view('core/templates/footer');
    }

    function rates(){
        $this->core_layout->setPageTitle("Driver's Incentive - Tripping Rates");
        $this->core_layout->setPrivilegeName("eforms_tripping-rates");
        $this->core_layout->addJs("js/eforms/tripping/rates.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/tripping/rates');
        $this->load->view('core/templates/footer');
    }

    function import(){
        $this->core_layout->setPageTitle("Driver's Incentive -Tripping Import Data");
        $this->core_layout->setPrivilegeName("eforms_tripping-import");
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addCss("plugins/lightbox/js/lightbox.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
        $this->core_layout->addJs("plugins/lightbox/js/lightbox.js");
        $this->core_layout->addJs("js/eforms/tripping/import.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/tripping/import');
        $this->load->view('core/templates/footer');
    }

    function reports(){
        $this->core_layout->setPageTitle("Driver's Incentive - Tripping Report");
        $this->core_layout->setPrivilegeName("eforms_tripping-reports");
        $this->core_layout->addJs("js/eforms/tripping/reports.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/tripping/reports');
        $this->load->view('core/templates/footer');
    }

    function rental_form(){
        $this->core_layout->setPageTitle("Driver's Incentive - New Rental Entry");
        $this->core_layout->setPrivilegeName("eforms_rental-form");
        $this->core_layout->addJs("js/eforms/tripping/rentals/form.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/tripping/rental/form');
        $this->load->view('core/templates/footer');
    }

    function rental_rate(){
        $this->core_layout->setPageTitle("Driver's Incentive - Rental Rate");
        $this->core_layout->setPrivilegeName("eforms_rental-rate");
        $this->core_layout->addJs("js/eforms/tripping/rentals/rate.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/tripping/rental/rate');
        $this->load->view('core/templates/footer');
    }

    function rental_report(){
        $this->core_layout->setPageTitle("Driver's Incentive - Rental Report");
        $this->core_layout->setPrivilegeName("eforms_rental-report");
        $this->core_layout->addJs("js/eforms/tripping/rentals/reports.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/tripping/rental/report');
        $this->load->view('core/templates/footer');
    }

    function get_drivers_collection(){
        $data = $this->tripping->getDriversCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_drivers_vehicle(){
        $data = $this->tripping->getDriversVehicle();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function new_driver(){
        $data = $this->tripping->newDriver();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_driver_collection(){
        $data = $this->tripping->getDriverCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_driver_data(){
        $data = $this->tripping->getDriverData();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function update_driver(){
        $data = $this->tripping->updateDriver();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_company(){
        $data = $this->tripping->getCompany();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function new_project(){
        $data = $this->tripping->newProject();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_project_collection(){
        $data = $this->tripping->getProjectCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_project_data(){
        $data = $this->tripping->getProjectData();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function update_project(){
        $data = $this->tripping->updateProject();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function new_route(){
        $data = $this->tripping->newRoute();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_route_collection(){
        $data = $this->tripping->getRouteCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_route_data(){
        $data = $this->tripping->getRouteData();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function update_route(){
        $data = $this->tripping->updateRoute();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_routes(){
        $data = $this->tripping->getRoutes();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_routes_Code(){
        $data = $this->tripping->getRoutesCode();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_projects(){
        $data = $this->tripping->getProjects();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_projects_report(){
        $data = $this->tripping->getProjectsReport();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function new_rate(){
        $data = $this->tripping->newRate();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_rate_collection(){
        $data = $this->tripping->getRateCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_rate_data(){
        $data = $this->tripping->getRateData();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function update_rate(){
        $data = $this->tripping->updateRate();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function temp_upload_file(){
        $data = $this->tripping->tempUploadFile();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_driver_select2(){
        $data = $this->tripping->getDriverSelect2();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function generate_report(){
        $data = $this->tripping->generateReport();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function save_rental(){
        $data = $this->tripping->saveRental();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function new_rental_rate(){
        $data = $this->tripping->newRentalRate();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_rental_rate_collection(){
        $data = $this->tripping->getRentalRateCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_rental_rate_data(){
        $data = $this->tripping->getRentalRateData();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function generate_rental_report(){
        $data = $this->tripping->generateRentalReport();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function download_template(){
        $this->load->helper('file');
        $this->load->dbutil();
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $enclosure = '"';
        $data = $this->tripping->downloadTemplate();

        $list = $data;
        $fp = fopen("./uploads/files/trippings/template/template_for_import_trppings.csv", 'w');
        $header = array("reference_no","project","date","time","driver","origin","destination"); 
        fputcsv($fp, $header);
        foreach ($list->result_array() as $key=>$line) {
            fputcsv($fp, $line);
            }

        $data1 = file_get_contents('./uploads/files/trippings/template/template_for_import_trppings.csv'); 
        $name = 'data.csv';

        // Build the headers to push out the file properly.
        header('Pragma: public');     // required
        header('Expires: 0');         // no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private',false);
        header('Content-Disposition: attachment; filename="'.basename($name).'"');  // Add the file name
        header('Content-Transfer-Encoding: binary');
        header('Connection: close');
        fclose($fp);

        $this->output
            ->set_content_type('json')
            ->set_output(json_encode(array("status"=>TRUE)));
    }


}