<?php defined('BASEPATH') OR exit('No direct script access allowed');

    class inventory extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->authenticate->setModuleAccess("ams");
            $this->authenticate->doRedirect();
            $this->load->model("Inventory_model", "inventory");
            $this->load->model("Assets_model", "assets");
            $this->load->library('upload');
        }

        // load report view
        public function report()
        {
            $this->core_layout->addJs("global/js/amcharts4/core.js", true);
            $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
            $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
            $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);

            $this->core_layout->addJs("js/dataTables.buttons.min.js", true);
            $this->core_layout->addJs("js/buttons.flash.min.js", true);
            $this->core_layout->addJs("js/jszip.min.js", true);
            $this->core_layout->addJs("js/pdfmake.min.js", true);
            $this->core_layout->addJs("js/vfs_fonts.js", true);
            $this->core_layout->addJs("js/buttons.html5.min.js", true);
            $this->core_layout->addJs("js/buttons.print.min.js", true);

            $this->core_layout->addCss("css/buttons.dataTables.min.css", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);
            $this->core_layout->addJs("js/ams/inventory.js", true);

            $this->load->view('core/templates/header');
            $this->load->view('inventory/report');
            $this->load->view('core/templates/footer');
        }

        public function get_inventory_report_data()
        {
            $data = $this->inventory->getInventoryReportData();
            echo json_encode($data);
        }

        public function get_verified_inventory_list()
        {
             $data = $this->inventory->getVerifiedInventoryList();
             echo json_encode($data);
        }

        public function asset_inventory_report()
        {
            $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
            $this->core_layout->addCss("css/buttons.dataTables.min.css", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);
            $this->core_layout->addJs("js/ams/asset_inventory_report.js", true);

            $this->core_layout->setPrivilegeName("assets_inventory");

            $this->load->view('core/templates/header');
            $this->load->view('inventory/asset_inventory_report/asset_inventory_report');
            $this->load->view('core/templates/footer');
        }

        public function vehicle_inventory_report()
        {
            $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
            $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
            $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
            $this->core_layout->addCss("css/buttons.dataTables.min.css", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);
            $this->core_layout->addJs("js/ams/vehicle_inventory_report.js", true);

            $this->core_layout->setPrivilegeName("vehicle_inventory");

            $this->load->view('core/templates/header');
            $this->load->view('inventory/asset_inventory_report/vehicle_inventory_report');
            $this->load->view('core/templates/footer');
        }

        public function inventory_fileupload($type = "assets")
        {
             $data = $this->inventory->inventoryFileupload($type);
             $this->output
             ->set_content_type('json')
             ->set_output(json_encode($data));
        }

        function save_inventory_check($asset_type) {
            $data =$this->inventory->saveInventoryCheck($asset_type);
            echo json_encode($data);
        }

        public function logs()
        {
            $this->core_layout->addCss("css/buttons.dataTables.min.css", true);
            $this->core_layout->addCss("css/ams/ams_styles.css", true);
            $this->core_layout->addJs("js/ams/inventory_logs.js", true);

            $this->core_layout->setPrivilegeName("inventory_logs");

            $this->load->view('core/templates/header');
            $this->load->view('inventory/logs');
            $this->load->view('core/templates/footer');
        }

        public function inventory_log()
        {
             $data = $this->inventory->inventoryLog();
             $this->output
             ->set_content_type('json')
             ->set_output(json_encode($data));
        }

    }
