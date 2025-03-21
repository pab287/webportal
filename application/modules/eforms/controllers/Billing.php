<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Billing extends MY_Controller {
	public function __construct(){
        parent::__construct();
        $this->authenticate->setModuleAccess("eforms-billing");
        $this->authenticate->doRedirect();
        $this->core_layout->setPrivilegeName("eforms-billing");
    
        $this->load->model("billing_m","billing");
    }

    function index(){
        $this->core_layout->setPrivilegeName("billing_dashboard");
        $this->core_layout->addJs("global/js/amcharts4/core.js", true);
        $this->core_layout->addJs("global/js/amcharts4/charts.js", true);
        $this->core_layout->addJs("global/js/amcharts4/maps.js", true);
        $this->core_layout->addJs("global/js/amcharts4/themes/animated.js", true);
        $this->core_layout->addJs("js/eforms/billing/index.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/index');
        $this->load->view('core/templates/footer');
    }

    function accounts(){
        $this->core_layout->setPageTitle("Hydra - Accounts");
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);

        $this->core_layout->setPrivilegeName("billing_accounts");
        $this->core_layout->addJs("js/eforms/billing/accounts/index.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/accounts/index');
        $this->load->view('core/templates/footer');
    }

    function accounts_reconnection(){
      $this->core_layout->setPageTitle("Hydra - Accounts");

      $this->core_layout->setPrivilegeName("billing_accounts");
      $this->core_layout->addJs("js/eforms/billing/accounts/index_reconnection.js", true);
      $this->load->view('core/templates/header');
      $this->load->view('eforms/billing/accounts/index_reconnection');
      $this->load->view('core/templates/footer');
    }

    function accounts_disconnection(){
      $this->core_layout->setPageTitle("Hydra - Accounts");

      $this->core_layout->setPrivilegeName("billing_accounts");
      $this->core_layout->addJs("js/eforms/billing/accounts/index_disconnection.js", true);
      $this->load->view('core/templates/header');
      $this->load->view('eforms/billing/accounts/index_disconnection');
      $this->load->view('core/templates/footer');
    }

    function new_account(){
        $this->core_layout->setPrivilegeName("billing_accounts");
        $this->core_layout->addJs("js/eforms/billing/accounts/new_account.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/accounts/new_account');
        $this->load->view('core/templates/footer');
    }
    
    function edit_account($id){
        $this->core_layout->setPrivilegeName("billing_accounts");
        $this->core_layout->addJs("js/eforms/billing/accounts/edit_account.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/accounts/edit_account');
        $this->load->view('core/templates/footer');
    }

    function billing(){
        $this->core_layout->setPageTitle("Hydra - Billing");
        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);
        $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
        $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js", true);
        $this->core_layout->setPrivilegeName("billing_billing");
        $this->core_layout->addJs("js/eforms/billing/billing/index.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/billing/index');
        $this->load->view('core/templates/footer');
    }

    function setup(){
        $this->core_layout->setPageTitle("Hydra - Setup");
        $this->core_layout->setPrivilegeName("billing_setup");
        $this->core_layout->addJs("js/eforms/billing/setup.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/setup/index');
        $this->load->view('core/templates/footer');
    }
    
    function payment(){
        $this->core_layout->setPageTitle("Hydra - Payments");
        $this->core_layout->setPrivilegeName("billing_payment");
        $this->core_layout->addJs("js/eforms/billing/payment/index.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/payment/index');
        $this->load->view('core/templates/footer');
    }

    function payment_archive(){
      $this->core_layout->setPageTitle("Hydra - Payment Archive");
      $this->core_layout->setPrivilegeName("billing_payment");
      $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
      $this->core_layout->addJs("js/eforms/billing/payment/archive.js", true);
      $this->load->view('core/templates/header');
      $this->load->view('eforms/billing/payment/archive');
      $this->load->view('core/templates/footer');
  }

    function event_logs(){
        $this->core_layout->setPageTitle("Hydra - Logs");
        $this->core_layout->setPrivilegeName("billing_event_logs");
        $this->core_layout->addJs("js/eforms/billing/event_logs/index.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/event_logs/index');
        $this->load->view('core/templates/footer');
    }

    function subdivision(){
      $this->core_layout->setPageTitle("Hydra - Subdivision");
        $this->core_layout->setPrivilegeName("billing_subdivision");
        $this->core_layout->addJs("js/eforms/billing/subdivision/index.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/subdivision/index');
        $this->load->view('core/templates/footer');
    }

    function distribution(){
      $this->core_layout->setPageTitle("Hydra - Distribution");
        $this->core_layout->setPrivilegeName("billing_distribution");
        $this->core_layout->addJs("js/eforms/billing/distribution/index.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/distribution/index');
        $this->load->view('core/templates/footer');
    }

    function reports_soa(){
      $this->core_layout->setPageTitle("Hydra - SOA");
        $this->core_layout->setPrivilegeName("billing_reports_soa");
        $this->core_layout->addJs("js/eforms/billing/reports_soa/index.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/reports_soa/index');
        $this->load->view('core/templates/footer');
    }

    function payment_collection(){
      $this->core_layout->setPageTitle("Hydra - Payment Collection");
        $this->core_layout->setPrivilegeName("payment_collection");
        $this->core_layout->addJs("js/eforms/billing/reports_soa/payment_collection.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/reports_soa/payment_collection');
        $this->load->view('core/templates/footer');
    }

    function sales_collection(){
      $this->core_layout->setPageTitle("Hydra - Sales Collection");
        $this->core_layout->setPrivilegeName("payment_collection");
        $this->core_layout->addJs("js/eforms/billing/reports_soa/sales_collection.js", true);
        $this->core_layout->addJs("js/buttons.html5.min.js", true);
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addCss("css/buttons.dataTables.min.css", true);

        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/reports_soa/sales_collection');
        $this->load->view('core/templates/footer');
    }

    function distribution_reports() {
        $this->core_layout->setPageTitle("Hydra - Distribution Reports");
        $this->core_layout->setPrivilegeName("distribution_reports");
        $this->core_layout->addJs("js/eforms/billing/distribution/reports.js", true);

        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/distribution/reports');
        $this->load->view('core/templates/footer');
    }

    function create_subdivision(){
      $this->core_layout->setPageTitle("Hydra - New Subdivision");
        $this->core_layout->setPrivilegeName("billing_subdivision");
        $this->core_layout->addJs("js/eforms/billing/subdivision/create_subdivision.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/subdivision/create_subdivision');
        $this->load->view('core/templates/footer');
    }
    
    function create_payment(){
      $this->core_layout->setPageTitle("Hydra - New Payment");
        $this->core_layout->setPrivilegeName("billing_payment");
        $this->core_layout->addJs("js/eforms/billing/payment/create.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/payment/create_payment');
        $this->load->view('core/templates/footer');
    }

    function edit_payment(){
      $this->core_layout->setPageTitle("Hydra - Edit Payment");
        $this->core_layout->setPrivilegeName("billing_payment");
        $this->core_layout->addJs("js/eforms/billing/payment/edit.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/payment/edit_payment');
        $this->load->view('core/templates/footer');
    }

    function get_datatable_request_disconnection(){
      $data = $this->billing->getDatatableRequestDisconnection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_datatable_request_reconnection(){
      $data = $this->billing->getDatatableRequestReconnection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_customer_soa_details($id){
        $data = $this->billing->getCustomerSoaDetails($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function createaccount(){
        $data = $this->billing->createAccount();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function updateaccount(){
        $data = $this->billing->updateAccount();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_datatable_request(){
        $data = $this->billing->getDatatableRequest();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_account_details($id = NULL){
        $data = $this->billing->getAccountDetails($id);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function update_account_status(){
        $data = $this->billing->updateAccountStatus();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function archive_account(){
        $data = $this->billing->archiveAccount();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_account_select_billing(){
        $data = $this->billing->getAccountSelectBilling();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_account_select_payments(){
        $data = $this->billing->getAccountSelectPayments();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_employee_collector(){
        $data = $this->billing->getEmployeeCollector();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    } 

    function get_account_select_reading(){
        $data = $this->billing->getAccountSelectReading();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function readings(){
      $this->core_layout->setPageTitle("Hydra - Readings");
        $this->core_layout->setPrivilegeName("billing_readings");
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
        $this->core_layout->addJs("js/eforms/billing/readings/index.js", true);

        $this->core_layout->addCss('js/querybuilder/query-builder.default.min.css', TRUE);
        $this->core_layout->addJs('js/querybuilder/query-builder.standalone.min.js', TRUE);

        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/readings/index');
        $this->load->view('core/templates/footer');
    }

    function generate_payment_ar(){
        $data = $this->billing->generatePaymentAR();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function createreading(){
        $data = $this->billing->createReading();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_reading_collection(){
        $data = $this->billing->getReadingCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function upload_reading_photo(){
        $data = $this->billing->uploadReadingPhoto();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_reading_details($id){
        $data = $this->billing->getReadingDetails($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function updatereading(){
        $data = $this->billing->updateReading();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function approve_reading(){
        $data = $this->billing->approveReading();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function create_rate(){
        $data = $this->billing->createRate();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function create_new_bill(){
        $data = $this->billing->createNewBill();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function get_billing_collection(){
        $data = $this->billing->getBillingCollection();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function create(){
      $this->core_layout->setPageTitle("Hydra - New Billing");
        $this->core_layout->setPrivilegeName("billing_billing");
        $this->core_layout->addCss("plugins/fileupload/css/jquery.fileupload.css");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.iframe-transport.js");
        $this->core_layout->addJs("plugins/fileupload/js/jquery.fileupload.js");
        $this->core_layout->addJs("js/eforms/billing/billing/create.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/billing/create');
        $this->load->view('core/templates/footer');
    }

    function get_readings_by_account_id(){
        $data = $this->billing->getReadingbyAccount();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_account_previous_meter_reading(){
        $data = $this->billing->getAccountPreviousMeterReading();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_account_current_meter_reading(){
        $data = $this->billing->getAccountCurrentMeterReading();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_applied_rate(){
        $data = $this->billing->getAppliedRate();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_applied_limit(){
        $data = $this->billing->getAppliedLimit();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_bill_data(){
        $data = $this->billing->getBillData();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function print_bill($id){
        $arrData = array();
        $arrData["data"] = $this->billing->getBillforPrintData($id);
        $this->load->view("eforms/billing/billing/print", $arrData);
    }

    function updatebill(){
        $data = $this->billing->updatebill();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function count_print(){
        $data = $this->billing->countPrint();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_print_limit(){
        $data = $this->billing->updatePrintLimit();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_penalty_details(){
        $data = $this->billing->updatePenaltyDetails();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_penalty_details(){
        $data = $this->billing->getPenaltyDetails();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function mass_bill_print(){
        $data = $this->billing->massBillPrint();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_upaid_bills_by_account_id(){
        $data = $this->billing->getUnpaidBillbyAccountId();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_customer_details(){
        $data = $this->billing->getCustomerDetails();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function check_overdue(){
        $data = $this->billing->checkOverdue();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_bill_payments(){
      $data = $this->billing->getAllBillPayments();
      $this->output
          ->set_content_type('json')
          ->set_output(json_encode($data));
    }


    function create_new_payment(){
        $data = $this->billing->createNewPayment();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_billing_payment(){
      $data = $this->billing->getBillingPayment();
      $this->output
      ->set_content_type('json')
      ->set_output(json_encode($data));
    }

    function get_payment_details($id = NULL){
        $data = $this->billing->getPaymentDetails($id);
        $account = $this->billing->getAccountDetails($data['account_id']);
        $bill = $this->billing->getBillingDetails($data['bill_id']);

        echo json_encode(array("data"=>$data, "bill"=>$bill, "account"=>$account));
    }

    function update_payment(){
        $data = $this->billing->updatePayment();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function archive_payment(){
        $data = $this->billing->archivePayment();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function dashboard_analytics(){
        $data = $this->billing->dashboardAnalytics();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function dashboard_linegraph_top_payment(){
        $data = $this->billing->dashboardLineGraph_totalPayment();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function dashboard_subdivision_data(){
        $data = $this->billing->getSubdivisionData();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function dashboard_analytics_top_consumer(){
        $data = $this->billing->dashboardAnalytics_TopConsumer();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function dashboard_analytics_total_usage_per_subdivision(){
        $data = $this->billing->dashboardAnalytics_TotalUsagePerSubdivision();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function dashboard_linegraph_total_usage_per_subdivision(){
        $data = $this->billing->dashboardLineGraph_TotalUsagePerSubdivision();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function dashboard_consumer_vs_supplier(){
        $data = $this->billing->dashboardConsumerVsSupplier();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function print_payment(){
        $data = $this->billing->massPaymentPrint();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function view_penalties(){
        $data = $this->billing->viewPenalties();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_reconnection_fee(){
        $data = $this->billing->updateReconnectionFee();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_reconnection_fee(){
        $data = $this->billing->getReconnectionFee();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function get_due_for_today(){
        $data = $this->billing->getDueForToday();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function archive_bill(){
        $data = $this->billing->archiveBill();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_subdivision_select(){
        $data = $this->billing->getSubdivisionSelect();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_subdivision_select_distribution(){
        $data = $this->billing->getSubdivisionSelectDistribution();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_disconnected_customer(){
        $data = $this->billing->getDisconnectedCustomer();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_entries_bill(){
        $data = $this->billing->getEntriesBill();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_overdue_bill(){
        $data = $this->billing->getOverdueBill();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_usage_bill(){
        $data = $this->billing->getBillUsage();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_upcomingdue_bill(){
        $data = $this->billing->getUpcomingDueBill();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
    
    function save_print_logs(){
        $data = $this->billing->savePrintLogs();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_event_logs(){
        $data = $this->billing->getEventLogs();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_subdivision(){
        $data = $this->billing->getSubdivision();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_subdivision(){
        $data = $this->billing->saveSubdivision();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_subdivision_details(){
        $data = $this->billing->getSubdivisionDetails();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_subdivision_details(){
        $data = $this->billing->updateSubdivisionDetails();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function archive_subdivision(){
        $data = $this->billing->archiveSubdivision();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_export_logs(){
        $data = $this->billing->saveExportLogs();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function dashboard_water_usage(){
        $data = $this->billing->water_usage();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function dashboard_water_usage_subdivision(){
        $data = $this->billing->water_usage_subdivision();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_payment(){
        $data = $this->billing->get_PaymentDetails();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_distribution(){
        $data = $this->billing->getDistribution();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_distribution_reports2(){
        $data = $this->billing->get_distribution_reports2();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function get_distribution_reports(){
        $data = $this->billing->get_distribution_reports();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function save_distribution(){
        $data = $this->billing->saveDistribution();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_distribution_details(){
        $data = $this->billing->getDistributionDetails();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_distribution_details(){
        $data = $this->billing->updateDistributionDetails();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_reports_soa(){
        $data = $this->billing->getReportsSOA();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_total_balance_etc(){
        $data = $this->billing->getTotalBalanceEtc();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_balance_for_disconnection($id){
      $data = $this->billing->getBalanceForDisconnection($id);
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_reports_soa_details(){
        $data = $this->billing->getReportsSOA_details();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_reports_soa_readings(){
        $data = $this->billing->getReportsSOA_readings();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_reports_soa_ledger(){
        $data = $this->billing->getReportsSOA_ledger();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }
    
    function get_payment_collection_report(){
      $data = $this->billing->getPaymentCollectionReport();
      $this->output
          ->set_content_type('json')
          ->set_output(json_encode($data));
    }

    function get_reports_user_collection(){
      $data = $this->billing->getReportsUserCollection();
      $this->output
          ->set_content_type('json')
          ->set_output(json_encode($data));
    }

    function get_reports_soa_details_billing(){
        $data = $this->billing->getReportsSOA_billing();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_reports_soa_dates(){
        $data = $this->billing->getReportsSOA_dates();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function print_reports_soa(){
        $data = $this->billing->printReportsSOA();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function dashboard_cum(){
        $data = $this->billing->dashboardCUM();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_cutoffPeriod(){
        $data = $this->billing->updateCutOffPeriod();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_cut_off_period(){
        $data = $this->billing->getCutOffPeriod();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_dueDate(){
        $data = $this->billing->updateDueDate();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_due_date(){
        $data = $this->billing->getDueDate();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function generate_bill(){
        $data = $this->billing->generateBill();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function update_account_meter(){
        $data = $this->billing->updateAccountMeter();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function archive_destribution(){
        $data = $this->billing->archiveDestribution();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    public function archive_reading(){
        $data = $this->billing->archiveReading();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_reading_accounts(){
        $data = $this->billing->getReadingAccounts();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function test_statement(){
        // $data["data"] = $this->billing->getBillforPrintData("229");
        // $this->load->view("eforms/email_templates/hydra_billing_templ/billing_statement", $data);

        $data = $this->billing->test_email();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function archive(){
      $this->core_layout->setPageTitle("Hydra - Readings Archive");
        $this->core_layout->setPrivilegeName("billing_readings");
        $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
        $this->core_layout->addJs("js/eforms/billing/readings/archive.js", true);
        $this->load->view('core/templates/header');
        $this->load->view('eforms/billing/readings/archive');
        $this->load->view('core/templates/footer');
    }

    function billing_archive(){
      $this->core_layout->setPageTitle("Hydra - Billing Archive");
      $this->core_layout->setPrivilegeName("billing_billing");
      $this->core_layout->addJs("plugins/fileupload/js/vendor/jquery.ui.widget.js");
      $this->core_layout->addJs("js/eforms/billing/billing/archive.js", true);
      $this->load->view('core/templates/header');
      $this->load->view('eforms/billing/billing/archive');
      $this->load->view('core/templates/footer');
   }
  
    function get_reading_archive_collection(){
        $data = $this->billing->getReadingArchiveCollection();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function get_billing_archive_collection(){
      $data = $this->billing->getBillingArchiveCollection();
      $this->output
          ->set_content_type('json')
          ->set_output(json_encode($data));
    }
    
    function restore_reading(){
        $data = $this->billing->restoreReading();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function generate_reading_report(){
        $data = $this->billing->generateReadingReport();
		$this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function get_payment_archive_collection(){
      $data = $this->billing->getPaymentArchiveCollection();
      $this->output
          ->set_content_type('json')
          ->set_output(json_encode($data));
    }

    function restore_payment(){
      $data = $this->billing->restorePayment();
      $this->output
      ->set_content_type('json')
      ->set_output(json_encode($data));
    }

    // function restore_billing(){
    //     $data = $this->billing->restoreBilling();
	// 	$this->output
    //     ->set_content_type('json')
    //     ->set_output(json_encode($data));
    // }

    function disconnect_selected(){
      $data = $this->billing->disconnectSelected();
      $this->output
      ->set_content_type('json')
      ->set_output(json_encode($data));
    }

    function reconnect_selected(){
      $data = $this->billing->reconnectSelected();
      $this->output
      ->set_content_type('json')
      ->set_output(json_encode($data));
    }

    function get_disconnected_account(){
      $data = $this->billing->disconnectedAccounts();
      $this->output
      ->set_content_type('json')
      ->set_output(json_encode($data));
    }

    function get_sales_report(){
      $data = $this->billing->getSalesReport();
      $this->output
      ->set_content_type('json')
      ->set_output(json_encode($data));
    }
    
}

