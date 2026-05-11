<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Reports extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->authenticate->setModuleAccess("payroll");
        $this->authenticate->doRedirect();
        $this->load->model("Reports_m", "reports");
        $this->core_layout->addJs("plugins/daterange_picker/daterangepicker.min.js");
        $this->core_layout->addCss("plugins/daterange_picker/daterangepicker.css");
        date_default_timezone_set('Asia/Manila');
    }

    function netpay(){
        $this->load->model("payroll/payroll_m", "payroll");
        $tempData = array();
        $tempData["years"] = $this->payroll->getPostedPayrollSheetYearsData();
        $tempData["company"] = $this->payroll->select2CompanyData();
        $tempData["payout_schedule"] = $this->payroll->select2PayoutScheduleData();

        $this->core_layout->setPageTitle("Payroll - Netpay Report");
        $this->core_layout->setPrivilegeName("payroll_report_netpay");
        $this->core_layout->addJs("js/buttons.print.min.js", true);

        $this->core_layout->addCss('global/plugins/swal/sweetalert2.min.css', true);
        $this->core_layout->addJs('global/plugins/swal/sweetalert2.all.min.js', true);

        $this->core_layout->addJs("js/payroll/reports/netpay.script.js", true, $tempData);

        $this->load->view("core/templates/header");
        $this->load->view("payroll/reports/netpay");
        $this->load->view("core/templates/footer");
    }

    function remittances(){
        $this->core_layout->setPageTitle("Payroll - Reports");
        $this->core_layout->setPrivilegeName("payroll_remittances");
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("js/payroll/reports/remittances.script.js", true);

        $this->load->view("core/templates/header");
        $this->load->view("payroll/reports/remittances");
        $this->load->view("core/templates/footer");
    }

    function journal(){
        $data = $this->reports->getLoanTypes();
        $this->core_layout->setPageTitle("Payroll - Journal");
        $this->core_layout->setPrivilegeName("payroll_journal");
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("js/payroll/reports/journal.script.js", true);

        $this->load->view("core/templates/header");
        $this->load->view("payroll/reports/journal", $data);
        $this->load->view("core/templates/footer");
    }

    function taxable_income(){
        $this->load->model("payroll/payroll_m", "payroll");
        $tempData = array();
        $tempData["years"] = $this->payroll->getPostedPayrollSheetYearsData();
        $tempData["company"] = $this->payroll->select2CompanyData();

        $this->core_layout->setPageTitle("Payroll - Reports");
        $this->core_layout->setPrivilegeName("payroll_taxable_income");
        $this->core_layout->addJs("js/payroll/reports/taxable_income.script.js", true, $tempData);

        $this->load->view("core/templates/header");
        $this->load->view("payroll/reports/taxable_income");
        $this->load->view("core/templates/footer");
    }

    function contribution_deduction(){
        $this->load->model("payroll/payroll_m", "payroll");
        $tempData = array(); 
        $tempData["years"] = $this->payroll->getPostedPayrollSheetYearsData();
        $tempData["company"] = $this->payroll->select2CompanyData();
        $tempData['payout_mode'] = $this->payroll->getPaymentModeSelect2Data();
        $tempData["payout_schedule"] = $this->payroll->select2PayoutScheduleData();

        $version = filemtime(FCPATH.'assets/js/payroll/reports/contribution_deduction.script.js');

        $this->core_layout->setPageTitle("Payroll - Reports");
        $this->core_layout->setPrivilegeName("payroll_contribution_deduction");
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("js/payroll/reports/contribution_deduction.script.js", true, $tempData, "?v={$version}");

        $this->load->view("core/templates/header");
        $this->load->view("payroll/reports/contribution_deduction");
        $this->load->view("core/templates/footer");
    }

    function sss_contribution(){
        $this->load->model("payroll/payroll_m", "payroll");
        $tempData = array(); 
        $tempData["years"] = $this->payroll->getPostedPayrollSheetYearsData();
        $tempData["company"] = $this->payroll->select2CompanyData();
        
        $this->core_layout->setPageTitle("Payroll - SSS Contribution Reports");
        $this->core_layout->setPrivilegeName("payroll_sss_contribution");
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("js/payroll/reports/sss_contribution.script.js", true, $tempData);

        $this->load->view("core/templates/header");
        $this->load->view("payroll/reports/sss_contribution");
        $this->load->view("core/templates/footer");
    }

    function phic_contribution(){
        $this->load->model("payroll/payroll_m", "payroll");
        $tempData = array();
        $tempData["years"] = $this->payroll->getPostedPayrollSheetYearsData();
        $tempData["company"] = $this->payroll->select2CompanyData();

        $this->core_layout->setPageTitle("Payroll - PHIC Contribution Reports");
        $this->core_layout->setPrivilegeName("payroll_phic_contribution");
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("js/payroll/reports/phic_contribution.script.js", true, $tempData);

        $this->load->view("core/templates/header");
        $this->load->view("payroll/reports/phic_contribution");
        $this->load->view("core/templates/footer");
    }

    function hdmf_contribution(){
        $this->load->model("payroll/payroll_m", "payroll");
        $tempData = array();
        $tempData["years"] = $this->payroll->getPostedPayrollSheetYearsData();
        $tempData["company"] = $this->payroll->select2CompanyData();

        $this->core_layout->setPageTitle("Payroll - HDMF Contribution Reports");
        $this->core_layout->setPrivilegeName("payroll_hdmf_contribution");
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("js/payroll/reports/hdmf_contribution.script.js", true, $tempData);

        $this->load->view("core/templates/header");
        $this->load->view("payroll/reports/hdmf_contribution");
        $this->load->view("core/templates/footer");
    }

    function overtime(){
        $this->core_layout->setPageTitle("Payroll - Overtime Report");
        $this->core_layout->setPrivilegeName("payroll_overtime");
        $this->core_layout->addJs("js/payroll/reports/overtime.script.js", true);
        $this->core_layout->addJs("js/buttons.print.min.js", true);

        $this->load->view("core/templates/header");
        $this->load->view("payroll/reports/overtime_report");
        $this->load->view("core/templates/footer");
    }

    function overtime_summary(){
        $this->load->model("payroll/payroll_m", "payroll");
        $this->core_layout->setPageTitle("Payroll - Overtime Summary Report");
        $this->core_layout->setPrivilegeName("payroll_overtime_summary");

        $tempData = array();
        $tempData["years"] = $this->payroll->getPostedPayrollSheetYearsData();
        $tempData["company"] = $this->payroll->select2CompanyData();
        $tempData['payout_mode'] = $this->payroll->getPaymentModeSelect2Data();
        $tempData["payout_schedule"] = $this->payroll->select2PayoutScheduleData();

        $version = filemtime(FCPATH.'assets/js/payroll/reports/overtime.script.js');

        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("global/js/jquery.table2excel.min.js", true);
        $this->core_layout->addJs("js/payroll/reports/overtime.script.js", true, $tempData, "?v={$version}");

        $this->load->view("core/templates/header");
        $this->load->view("payroll/reports/overtime_summary");
        $this->load->view("core/templates/footer");
    }

    function leave_credits(){
        $this->load->model("payroll/payroll_m", "payroll");
        $this->core_layout->setPageTitle("Payroll - Leave Credits Report");
        $this->core_layout->setPrivilegeName("payroll_leave_credits");

        $tempData = array();
        $tempData["company"] = $this->payroll->select2CompanyData();
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("global/js/jquery.table2excel.min.js", true);
        $this->core_layout->addJs("js/payroll/reports/leave_credits.script.js", true, $tempData);

        $this->load->view("core/templates/header");
        $this->load->view("payroll/reports/leave_credits_report");
        $this->load->view("core/templates/footer");
    }

    public function cash_advance() {
        $this->core_layout->setPageTitle("Payroll - Cash Advance Report");
        $this->core_layout->setPrivilegeName("payroll_cash_advance_report");

        $tempData = array();
        // $tempData["years"] = $this->payroll->getPostedPayrollSheetYearsData();
        // $tempData["company"] = $this->payroll->select2CompanyData();

        // $this->core_layout->addJs("js/buttons.print.min.js", true);
        // $this->core_layout->addJs("global/js/jquery.table2excel.min.js", true);
        $this->core_layout->addJs("js/payroll/reports/cash_advance_report.js", true, $tempData);

        $this->load->view("core/templates/header");
        $this->load->view("payroll/reports/cash_advance_report");
        $this->load->view("core/templates/footer");
    }

    public function cash_advance_report(){
        $data = $this->reports->cashAdvanceReport();
        $this->output->set_content_type('json')->set_output(json_encode($data));
    }

    function get_remittance_report_request(){
        $data = $this->reports->getRemittanceReportRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    function get_sss_remittance_report_request(){
        $data = $this->reports->getSssRemittanceReportRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    function get_phic_remittance_report_request(){
        $data = $this->reports->getPhicRemittanceReportRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    function get_hdmf_remittance_report_request(){
        $data = $this->reports->getHdmfRemittanceReportRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    function generate_remittance_report(){
        $data = $this->reports->generateRemittanceReport();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }



    function generate_sss_remittance_report(){
        $data = $this->reports->generateSssRemittanceReport("sss");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }
    function generate_phic_remittance_report(){
        $data = $this->reports->generateSssRemittanceReport("phic");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }
    function generate_hdmf_remittance_report(){
        $data = $this->reports->generateSssRemittanceReport("hdmf");
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    function generate_contribution_deduction(){
        $data = $this->reports->generateContributionDeduction();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    function generate_taxable_income_report(){
        $data = $this->reports->generateTaxableIncomeReport();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    function generate_taxable_income_report_month(){
        $data = $this->reports->generateTaxableIncomeReport_month();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    function printable_form(){
        $this->core_layout->addCss("css/printable.css");
        $this->load->view("core/templates/printable/header");
        $this->load->view("core/templates/printable/footer");
    }

    function generate_taxable_list(){
        $post = $this->input->post();
        if(isset($post) && $post){
            $visibleFields = isset($post["visible_fields"]) && $post["visible_fields"] ? $post["visible_fields"]: array();
            if(isset($post["params"]) && is_array($post["params"]) && count($post["params"]) > 0){
                if(isset($post["params"]["month_name"]) && $post["params"]["month_name"] == ''){
                    $post["params"]["month_name"] = null;
                }
                
                $this->db->order_by("employee_name", "ASC");
                $qData = $this->db->get_where("payroll.taxable_income", $post["params"]);
                if($qData->num_rows() > 0){
                    $tempColumns = array();
                    $arrFields = array("sss", "sss_prov", "ph", "hdmf", "tax");
                    $arrGrandTotalFields = array("regular_pay", "gross_pay", "gross_pay_taxable", "netpay_bonus", "sss", "sss_prov", "ph", "hdmf", "tax");

                    foreach ($qData->result() as $key => $value) {
                        foreach ($value as $kk => $vv) {
                            if(!in_array($kk, $arrFields)){
                                $tempHeader = strtoupper($kk);
                                if(!in_array($tempHeader, $tempColumns)){
                                    $tempColumns[] = $tempHeader;
                                }
                            }
                        }
                    }

                    $arrGroupedFields = array("biometricno" => "Biometric #", "company_description" => "Company", "tax_status" => "Tax Status", 
                    "tin_no" => "TIN #", "phealth_no" => "PHILHEALTH #", "pagibig_no" => "PAGIBIG #", "sss_no" => "SSS #", 
                    "employee_status" => "Employee Status", "13th_month" => "13th Month");

                    $arrGrandTotal = array();
                    $tempHtml = "<table border='1' id='tbl_taxable_income' cellpadding='5' cellspacing='0' style='font-family: roboto; font-size: 10px; width: 100% !important;'>";
                    $tempHtml .="<thead>";
                    $tempHtml .= "<tr>";
                    $tempHtml .= "<th>ID #</th>";
                    if(is_array($visibleFields) && count($visibleFields) > 0 && in_array("biometricno", $visibleFields)){
                        $tempHtml .= "<th>Biometric #</th>";
                    }
                    $tempHtml .= "<th>Employee Name</th>";
                    foreach ($arrGroupedFields as $key => $field) {
                        if(is_array($visibleFields) && count($visibleFields) > 0 && in_array($key, $visibleFields) 
                        && ($key !== "biometricno" && $key !== "13th_month")){
                            $tempHtml .= "<th>{$field}</th>";
                        }
                    }
                    $tempHtml .= "<th class='text-center'>Basic Pay</th>";
                    $tempHtml .= "<th class='text-center'>Gross Pay</th>";
                    $tempHtml .= "<th class='text-center'>Gross Pay Taxable</th>";
                    if(is_array($visibleFields) && count($visibleFields) > 0 && in_array("13th_month", $visibleFields)){
                        $tempHtml .= "<th>13th Month</th>";
                    }
                    $tempHtml .= "<th class='text-center'>SSS</th>";
                    $tempHtml .= "<th class='text-center'>SSS PROV</th>";
                    $tempHtml .= "<th class='text-center'>PHIC</th>";
                    $tempHtml .= "<th class='text-center'>HDMF</th>";
                    $tempHtml .= "<th class='text-center'>TAXABLE</th>";
                    $tempHtml .= "<th class='text-center'>TAX</th>";
                    $tempHtml .= "</tr>";
                    $tempHtml .="<thead>";
                    $tempHtml .="<tbody>";

                    foreach ($qData->result() as $key => $value) {
                        $rowTotal = 0;
                        $rowTotal = $value->sss + $value->sss_prov + $value->ph + $value->hdmf;

                        $rowTotal = round($rowTotal, 2);
                        $decimalRegPay = number_format($value->regular_pay, 2, ".", ",");
                        $decimalGrossPay = number_format($value->gross_pay, 2, ".", ",");
                        $decimalGrossPayTaxable = number_format($value->gross_pay_taxable, 2, ".", ",");
                        $decimalNetpayBonus = number_format($value->netpay_bonus, 2, ".", ",");
                        $decimalSss = number_format($value->sss, 2, ".", ",");
                        $decimalSss_prov = number_format($value->sss_prov, 2, ".", ",");
                        $decimalPhic = number_format($value->ph, 2, ".", ",");
                        $decimalHdmf = number_format($value->hdmf, 2, ".", ",");
                        $decimalTax = number_format($value->tax, 2, ".", ",");

                        $rowTaxable = $value->gross_pay_taxable - $rowTotal;
                        $rowTaxable = round($rowTaxable, 2);
                        $tempHtml .= "<tr>";
                        $tempHtml .= "<td>{$value->idno}</td>";
                        if(is_array($visibleFields) && count($visibleFields) > 0 && in_array("biometricno", $visibleFields)){
                            $tempHtml .= "<td>{$value->biometricno}</td>";
                        }
                        $tempHtml .= "<td>{$value->employee_name}</td>";
                        foreach ($arrGroupedFields as $key => $field) {
                            if(is_array($visibleFields) && count($visibleFields) > 0 && in_array($key, $visibleFields) 
                            && ($key !== "biometricno" && $key !== "13th_month")){
                                $tempHtml .= "<td>{$value->$key}</td>";
                            }
                        }
                        $tempHtml .= "<td align='right'>{$decimalRegPay}</td>";
                        $tempHtml .= "<td align='right'>{$decimalGrossPay}</td>";
                        $tempHtml .= "<td align='right'>{$decimalGrossPayTaxable}</td>";
                        if(is_array($visibleFields) && count($visibleFields) > 0 && in_array("13th_month", $visibleFields)){
                            $tempHtml .= "<td align='right'>{$decimalNetpayBonus}</td>";
                        }
                        $tempHtml .= "<td align='right'>{$decimalSss}</td>";
                        $tempHtml .= "<td align='right'>{$decimalSss_prov}</td>";
                        $tempHtml .= "<td align='right'>{$decimalPhic}</td>";
                        $tempHtml .= "<td align='right'>{$decimalHdmf}</td>";
                        $decimalRowTaxable = number_format($rowTaxable, 2, ".", ",");
                        $tempHtml .= "<td align='right'>{$decimalRowTaxable}</td>";
                        $tempHtml .= "<td align='right'>{$decimalTax}</td>";
                        $tempHtml .= "</tr>";

                        foreach ($arrGrandTotalFields as $grandValue) {
                            if(isset($arrGrandTotal[$grandValue]) && $arrGrandTotal[$grandValue]){
                                $arrGrandTotal[$grandValue] += $value->$grandValue;
                            }else{
                                $arrGrandTotal[$grandValue] = $value->$grandValue;
                            }
                        }

                        if(isset($arrGrandTotal["taxable"]) && $arrGrandTotal["taxable"]){
                            $arrGrandTotal["taxable"] += $rowTaxable;
                        }else{
                            $arrGrandTotal["taxable"] = $rowTaxable;
                        }
                    }
                    $tempHtml .="</tbody>";

                    if($arrGrandTotal){
                        $arrGrandTotal = (object) $arrGrandTotal;
                        $_decimalRegPay = number_format($arrGrandTotal->regular_pay, 2, ".", ",");
                        $_decimalGrossPay = number_format($arrGrandTotal->gross_pay, 2, ".", ",");
                        $_decimalGrossPayTaxable = number_format($arrGrandTotal->gross_pay_taxable, 2, ".", ",");
                        $_decimalNetpayBonus = number_format($arrGrandTotal->netpay_bonus, 2, ".", ",");
                        $_decimalSss = number_format($arrGrandTotal->sss, 2, ".", ",");
                        $_decimalSss_prov = number_format($arrGrandTotal->sss_prov, 2, ".", ",");
                        $_decimalPhic = number_format($arrGrandTotal->ph, 2, ".", ",");
                        $_decimalHdmf = number_format($arrGrandTotal->hdmf, 2, ".", ",");
                        $_decimalTaxable = number_format($arrGrandTotal->taxable, 2, ".", ",");
                        $_decimalTax = number_format($arrGrandTotal->tax, 2, ".", ",");
                        $maxColumnCount = 2;
                        foreach ($arrGroupedFields as $key => $field) {
                            if(is_array($visibleFields) && count($visibleFields) > 0 && 
                            in_array($key, $visibleFields) && $key !== "13th_month"){ $maxColumnCount++; }
                        }

                        $tempHtml .="<tfoot><tr><td colspan='{$maxColumnCount}' class='text-right m--font-boldest'>GRAND TOTAL</td>";
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalRegPay}</td>";
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalGrossPay}</td>";
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalGrossPayTaxable}</td>";
                        if(is_array($visibleFields) && count($visibleFields) > 0 && in_array("13th_month", $visibleFields)){
                            $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalNetpayBonus}</td>";
                        }
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalSss}</td>";
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalSss_prov}</td>";
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalPhic}</td>";
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalHdmf}</td>"; 
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalTaxable}</td>";
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalTax}</td>";
                        $tempHtml .="</tr></tfoot>";
                    }

                    $tempHtml .= "</table>";
                    $tempHtml .= "</table>";
                    $resultset["response"] = true;
                    $resultset["html"] = $tempHtml;
                    $resultset["grand_total"] = $arrGrandTotal;
                }else{
                    $resultset["response"] = false;
                    $resultset["state"] = "no data found!";
                }
            }else{
                $resultset["response"] = false;
                $resultset["state"] = "no params data found!";
            }
        }else{
            $resultset["response"] = false;
            $resultset["state"] = "no post data found!";
        }

        echo json_encode($resultset);
    }

    function generate_taxable_list____original_with_psId(){
        $post = $this->input->post();
        if(isset($post) && $post){
            $ids = $post["ps_id"];
            $visibleFields = isset($post["visible_fields"]) && $post["visible_fields"] ? $post["visible_fields"]: array();
            if(is_array($post["ps_id"]) && count($post["ps_id"]) > 0){
                $data = $this->reports->generatePayrollSheetContributionMultiple($ids);
                if($data){
                    $tempColumns = array();
                    $arrFields = array("sss", "sss_prov", "ph", "hdmf", "tax");
                    $arrGrandTotalFields = array("regular_pay", "gross_pay", "gross_pay_taxable", "netpay_bonus", "sss", "sss_prov", "ph", "hdmf", "tax");
                    foreach ($data as $key => $value) {
                        $value = (object) $value;
                        if(isset($value->row_columns) && $value->row_columns){
                            foreach ($value->row_columns as $kk => $vv) {
                                if(!in_array($kk, $arrFields)){
                                    $tempHeader = strtoupper($kk);
                                    if(!in_array($tempHeader, $tempColumns)){
                                        $tempColumns[] = $tempHeader;
                                    }
                                }
                            }
                        }
                    }
                    $arrGroupedFields = array("biometricno" => "Biometric #", "company" => "Company", "tax_status" => "Tax Status", 
                    "tin_no" => "TIN #", "phealth_no" => "PHILHEALTH #", "pagibig_no" => "PAGIBIG #", "sss_no" => "SSS #", 
                    "employee_status" => "Employee Status", "13th_month" => "13th Month");

                    $arrGrandTotal = array();
                    $tempHtml = "<table border='1' id='tbl_taxable_income' cellpadding='5' cellspacing='0' style='font-family: roboto; font-size: 10px; width: 100% !important;'>";
                    if($data){
                        $tempHtml .="<thead>";
                        $tempHtml .= "<tr>";
                        $tempHtml .= "<th>ID #</th>";
                        if(is_array($visibleFields) && count($visibleFields) > 0 && in_array("biometricno", $visibleFields)){
                            $tempHtml .= "<th>Biometric #</th>";
                        }
                        $tempHtml .= "<th>Employee Name</th>";
                        foreach ($arrGroupedFields as $key => $field) {
                            if(is_array($visibleFields) && count($visibleFields) > 0 && in_array($key, $visibleFields) 
                            && ($key !== "biometricno" && $key !== "13th_month")){
                                $tempHtml .= "<th>{$field}</th>";
                            }
                        }
                        $tempHtml .= "<th class='text-center'>Reg Pay</th>";
                        $tempHtml .= "<th class='text-center'>Gross Pay</th>";
                        $tempHtml .= "<th class='text-center'>Gross Pay Taxable</th>";
                        if(is_array($visibleFields) && count($visibleFields) > 0 && in_array("13th_month", $visibleFields)){
                            $tempHtml .= "<th>13th Month</th>";
                        }
                        $tempHtml .= "<th class='text-center'>SSS</th>";
                        $tempHtml .= "<th class='text-center'>SSS PROV</th>";
                        $tempHtml .= "<th class='text-center'>PHIC</th>";
                        $tempHtml .= "<th class='text-center'>HDMF</th>";
                        
                        /*** foreach ($tempColumns as $key => $value) {
                            $tempHtml .= "<th class='text-center'>{$value}</th>";
                        } ***/
                        $tempHtml .= "<th class='text-center'>TAXABLE</th>";
                        /*** $tempHtml .= "<th class='text-center'>TOTAL</th>"; ***/
                        $tempHtml .= "<th class='text-center'>TAX</th>";
                        $tempHtml .= "</tr>";
                        $tempHtml .="<thead>";
                        $tempHtml .="<tbody>";
                        foreach ($data as $key => $value) {
                            $tempValuex = (object) $value;
                            $rowTotal = 0;
                            /*** $rowTotal = $tempValuex->sss + $tempValuex->sss_prov + $tempValuex->ph + $tempValuex->hdmf + $tempValuex->tax; ***/
                            $rowTotal = $tempValuex->sss + $tempValuex->sss_prov + $tempValuex->ph + $tempValuex->hdmf;
                            /*** foreach ($tempColumns as $kkk => $vvv) {
                                $tempKey = strtolower($vvv);
                                $tempValue = isset($tempValuex->$tempKey) && $tempValuex->$tempKey ? $tempValuex->$tempKey: 0;
                                $rowTotal += $tempValue;
                            } ***/

                            $rowTotal = round($rowTotal, 2);
                            $decimalRegPay = number_format($tempValuex->regular_pay, 2, ".", ",");
                            $decimalGrossPay = number_format($tempValuex->gross_pay, 2, ".", ",");
                            $decimalGrossPayTaxable = number_format($tempValuex->gross_pay_taxable, 2, ".", ",");
                            $decimalNetpayBonus = number_format($tempValuex->netpay_bonus, 2, ".", ",");
                            $decimalSss = number_format($tempValuex->sss, 2, ".", ",");
                            $decimalSss_prov = number_format($tempValuex->sss_prov, 2, ".", ",");
                            $decimalPhic = number_format($tempValuex->ph, 2, ".", ",");
                            $decimalHdmf = number_format($tempValuex->hdmf, 2, ".", ",");
                            $decimalTax = number_format($tempValuex->tax, 2, ".", ",");

                            $rowTaxable = $tempValuex->gross_pay_taxable - $rowTotal;
                            $rowTaxable = round($rowTaxable, 2);
                            $tempHtml .= "<tr>";
                            $tempHtml .= "<td>{$tempValuex->idno}</td>";
                            if(is_array($visibleFields) && count($visibleFields) > 0 && in_array("biometricno", $visibleFields)){
                                $tempHtml .= "<td>{$tempValuex->biometricno}</td>";
                            }
                            $tempHtml .= "<td>{$tempValuex->employee_name}</td>";
                            foreach ($arrGroupedFields as $key => $field) {
                                if(is_array($visibleFields) && count($visibleFields) > 0 && in_array($key, $visibleFields) 
                                && ($key !== "biometricno" && $key !== "13th_month")){
                                    $tempHtml .= "<td>{$tempValuex->$key}</td>";
                                }
                            }
                            $tempHtml .= "<td align='right'>{$decimalRegPay}</td>";
                            $tempHtml .= "<td align='right'>{$decimalGrossPay}</td>";
                            $tempHtml .= "<td align='right'>{$decimalGrossPayTaxable}</td>";
                            if(is_array($visibleFields) && count($visibleFields) > 0 && in_array("13th_month", $visibleFields)){
                                $tempHtml .= "<td>{$decimalNetpayBonus}</td>";
                            }
                            $tempHtml .= "<td align='right'>{$decimalSss}</td>";
                            $tempHtml .= "<td align='right'>{$decimalSss_prov}</td>";
                            $tempHtml .= "<td align='right'>{$decimalPhic}</td>";
                            $tempHtml .= "<td align='right'>{$decimalHdmf}</td>";
                            /*** foreach ($tempColumns as $kkk => $vvv) {
                                $tempKey = strtolower($vvv);
                                $tempValue = isset($tempValuex->$tempKey) && $tempValuex->$tempKey ? $tempValuex->$tempKey: 0;
                                $decimalTempValue = number_format($tempValue, 2, ".", ",");
                                $tempHtml .= "<td align='right'>{$decimalTempValue}</td>";
                            } ***/
                            $decimalRowTaxable = number_format($rowTaxable, 2, ".", ",");
                            /** $decimalRowTotal = number_format($rowTotal, 2, ".", ","); ***/
                            /*** $tempHtml .= "<td align='right'>{$decimalRowTotal}</td>"; ***/
                            $tempHtml .= "<td align='right'>{$decimalRowTaxable}</td>";
                            $tempHtml .= "<td align='right'>{$decimalTax}</td>";
                            $tempHtml .= "</tr>";

                            foreach ($arrGrandTotalFields as $key => $value) {
                                if(isset($arrGrandTotal[$value]) && $arrGrandTotal[$value]){
                                    $arrGrandTotal[$value] += $tempValuex->$value;
                                }else{
                                    $arrGrandTotal[$value] = $tempValuex->$value;
                                }
                            }
                            /*** foreach ($tempColumns as $kkk => $vvv) {
                                $tempKey = strtolower($vvv);
                                $tempValue = isset($tempValuex->$tempKey) && $tempValuex->$tempKey ? $tempValuex->$tempKey: 0;
                                if(isset($arrGrandTotal[$tempKey]) && $arrGrandTotal[$tempKey]){
                                    $arrGrandTotal[$tempKey] += $tempValue;
                                }else{
                                    $arrGrandTotal[$tempKey] = $tempValue;
                                }
                            } ***/

                            if(isset($arrGrandTotal["taxable"]) && $arrGrandTotal["taxable"]){
                                $arrGrandTotal["taxable"] += $rowTaxable;
                            }else{
                                $arrGrandTotal["taxable"] = $rowTaxable;
                            }
                            /*** if(isset($arrGrandTotal["total"]) && $arrGrandTotal["total"]){
                                $arrGrandTotal["total"] += $rowTotal;
                            }else{
                                $arrGrandTotal["total"] = $rowTotal;
                            } ***/
                        }
                        $tempHtml .="</tbody>";
                    }
                    if($arrGrandTotal){
                        $arrGrandTotal = (object) $arrGrandTotal;
                        $_decimalRegPay = number_format($arrGrandTotal->regular_pay, 2, ".", ",");
                        $_decimalGrossPay = number_format($arrGrandTotal->gross_pay, 2, ".", ",");
                        $_decimalGrossPayTaxable = number_format($arrGrandTotal->gross_pay_taxable, 2, ".", ",");
                        $_decimalNetpayBonus = number_format($arrGrandTotal->netpay_bonus, 2, ".", ",");
                        $_decimalSss = number_format($arrGrandTotal->sss, 2, ".", ",");
                        $_decimalSss_prov = number_format($arrGrandTotal->sss_prov, 2, ".", ",");
                        $_decimalPhic = number_format($arrGrandTotal->ph, 2, ".", ",");
                        $_decimalHdmf = number_format($arrGrandTotal->hdmf, 2, ".", ",");
                        $_decimalTaxable = number_format($arrGrandTotal->taxable, 2, ".", ",");
                        $_decimalTax = number_format($arrGrandTotal->tax, 2, ".", ",");
                        $maxColumnCount = 2;
                        foreach ($arrGroupedFields as $key => $field) {
                            if(is_array($visibleFields) && count($visibleFields) > 0 && 
                            in_array($key, $visibleFields) && $key !== "13th_month"){ $maxColumnCount++; }
                        }

                        $tempHtml .="<tfoot><tr><td colspan='{$maxColumnCount}' class='text-right m--font-boldest'>GRAND TOTAL</td>";
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalRegPay}</td>";
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalGrossPay}</td>";
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalGrossPayTaxable}</td>";
                        if(is_array($visibleFields) && count($visibleFields) > 0 && in_array("13th_month", $visibleFields)){
                            $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalNetpayBonus}</td>";
                        }
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalSss}</td>";
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalSss_prov}</td>";
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalPhic}</td>";
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalHdmf}</td>"; 
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalTaxable}</td>";
                        $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalTax}</td>";
                        $tempHtml .="</tr></tfoot>";
                    }

                    $tempHtml .= "</table>";
                    $tempHtml .= "</table>";
                    $resultset["response"] = true;
                    $resultset["html"] = $tempHtml;
                    $resultset["grand_total"] = $arrGrandTotal;
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }

        echo json_encode($resultset);
    }

    function generate_contribution_deduction_list(){
        $post = $this->input->post();
        if(isset($post) && $post){
            if(is_array($post["ps_id"]) && count($post["ps_id"]) > 0){
                $data = $this->reports->generatePayrollSheetContributionDeduction($post["ps_id"]);
                if($data){
                    $this->db->select("LOWER(GROUP_CONCAT(DISTINCT(code))) as code");
                    $qCode = $this->db->get_where("payroll.loans", array("loan_type"=>1));
                    $tempCodes = $qCode->row_array()["code"] ? explode(",", $qCode->row_array()["code"]): array();

                    $tempColumns = array();
                    foreach ($data as $value) {
                        $value = (object) $value;
                        if(isset($value->row_columns) && $value->row_columns){
                            foreach ($value->row_columns as $kk => $vv) {
                                $tempHeader = strtoupper($kk);
                                if(!in_array($tempHeader, $tempColumns)){
                                    $tempColumns[] = $tempHeader;
                                }
                            }
                        }
                    }

                    $arrNoGrandTotal = array("biometricno", "company", "tax_status", "tin_no", "phealth_no", "pagibig_no", "sss_no", "employee_status");
                    $renderColumns = array();
                    foreach ($tempColumns as $value) {
                        $tempKey = strtolower($value);
                        if(!in_array($tempKey, $arrNoGrandTotal) && in_array($tempKey, $tempCodes)){
                            $renderColumns[] = $tempKey;
                        }
                    }

                    $groupedByStation = array();
                    foreach ($data as $value) {
                        $row = (object) $value;
                        $station = isset($row->station) && trim($row->station) ? strtoupper(trim($row->station)) : "NO ASSIGNED PROJECT";
                        if(!isset($groupedByStation[$station])){ $groupedByStation[$station] = array(); }
                        $groupedByStation[$station][] = $row;
                    }
                    ksort($groupedByStation);

                    $arrGrandTotal = array();
                    $tempHtml = "<table border='1' cellpadding='5' cellspacing='0' style='font-family: roboto; font-size: 10px; width: 100% !important;' id='monthly_contrib_deduct'>";
                    if($data){
                        $tempHtml .="<thead>";
                        $tempHtml .= "<tr>";
                        $tempHtml .= "<th>EMPLOYEE #</th>";
                        $tempHtml .= "<th>EMPLOYEE NAME</th>";

                        foreach ($renderColumns as $tempKey) {
                            $_tempHeader = strtoupper($tempKey);
                            if (strtolower($tempKey) === 'cal.'){ $_tempHeader = 'SSS CAL'; }
                            elseif (strtolower($tempKey) == 'cal'){ $_tempHeader = 'HDMF CAL'; }
                            $tempHtml .= "<th class='text-center'>{$_tempHeader}</th>";
                        }

                        $tempHtml .= "</tr>";
                        $tempHtml .="<thead>";
                        $tempHtml .="<tbody>";

                        $maxColumnCount = 2 + count($renderColumns);
                        foreach ($groupedByStation as $station => $rows) {
                            $tempHtml .= "<tr>";
                            $tempHtml .= "<td class='m--font-boldest'>PROJECT #: {$station}</td>";
                            for ($cx = 1; $cx < $maxColumnCount; $cx++) {
                                $tempHtml .= "<td></td>";
                            }
                            $tempHtml .= "</tr>";
                            $arrStationTotal = array();

                            foreach ($rows as $tempValuex) {
                                $tempHtml .= "<tr>";
                                $tempHtml .= "<td>{$tempValuex->idno}</td>";
                                $tempHtml .= "<td>{$tempValuex->employee_name}</td>";

                                foreach ($renderColumns as $tempKey) {
                                    $tempValue = isset($tempValuex->$tempKey) && $tempValuex->$tempKey ? $tempValuex->$tempKey: 0;
                                    $decimalTempValue = $tempValue;
                                    $tempHtml .= "<td align='right'>{$decimalTempValue}</td>";

                                    if(isset($arrStationTotal[$tempKey]) && $arrStationTotal[$tempKey]){ $arrStationTotal[$tempKey] += $tempValue; }
                                    else{ $arrStationTotal[$tempKey] = $tempValue; }

                                    if(isset($arrGrandTotal[$tempKey]) && $arrGrandTotal[$tempKey]){ $arrGrandTotal[$tempKey] += $tempValue; }
                                    else{ $arrGrandTotal[$tempKey] = $tempValue; }
                                }
                                $tempHtml .= "</tr>";
                            }

                            $tempHtml .= "<tr>";
                            $tempHtml .= "<td></td>";
                            $tempHtml .= "<td class='text-right m--font-boldest'>SUB TOTAL - {$station}</td>";
                            foreach ($renderColumns as $tempKey) {
                                $_stationTotal = isset($arrStationTotal[$tempKey]) ? number_format($arrStationTotal[$tempKey], 2, ".", ",") : "0.00";
                                $tempHtml .= "<td class='text-right m--font-boldest'>{$_stationTotal}</td>";
                            }
                            $tempHtml .= "</tr>";
                        }
                        $tempHtml .="</tbody>";
                    }

                    if($arrGrandTotal){
                        $arrGrandTotal = (object) $arrGrandTotal;
                        $tempHtml .="<tfoot><tr><td colspan='2' class='text-right m--font-boldest'>GRAND TOTAL</td>";
                        foreach ($renderColumns as $tempKey) {
                            $_decimalValue = (isset($arrGrandTotal->$tempKey) && $arrGrandTotal->$tempKey)? number_format($arrGrandTotal->$tempKey, 2, ".", ","): 0;
                            $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalValue}</td>";
                        }
                        $tempHtml .="</tr></tfoot>";
                    }

                    $tempHtml .= "</table>";

                    $summaryMplKey = in_array('mpl', $renderColumns) ? 'mpl' : null;
                    $summarySalKey = in_array('sal', $renderColumns) ? 'sal' : null;
                    $summaryGrandEmployee = 0;
                    $summaryGrandMpl = 0;
                    $summaryGrandSal = 0;

                    $tempHtml .= "<div class='mt-5'>";
                        $tempHtml .= "<div class='row mt-5'>";
                            $tempHtml .= "<div class='col-md-12'>";
                                $tempHtml .= "<h5>STATION SUMMARY</h5>";
                            $tempHtml .= "</div>";
                        $tempHtml .= "</div>";
                        $tempHtml .= "<table border='1' cellpadding='5' cellspacing='0' style='font-family: roboto; font-size: 10px; width: 100% !important;'>";
                            $tempHtml .= "<thead>";
                                $tempHtml .= "<tr>";
                                    $tempHtml .= "<th>PROJECT #</th>";
                                    $tempHtml .= "<th class='text-center'># OF EMPLOYEE(S)</th>";
                                    $tempHtml .= "<th class='text-center'>MPL</th>";
                                    $tempHtml .= "<th class='text-center'>SAL</th>";
                                $tempHtml .= "</tr>";
                            $tempHtml .= "</thead>";
                        $tempHtml .= "<tbody>";

                        foreach ($groupedByStation as $station => $rows) {
                            $employeeCounter = array();
                            $stationMpl = 0;
                            $stationSal = 0;
                            foreach ($rows as $row) {
                                $empId = isset($row->emp_id) ? intval($row->emp_id) : 0;
                                if ($empId > 0) { $employeeCounter[$empId] = true; }

                                if ($summaryMplKey) {
                                    $stationMpl += isset($row->$summaryMplKey) ? floatval($row->$summaryMplKey) : 0;
                                }
                                if ($summarySalKey) {
                                    $stationSal += isset($row->$summarySalKey) ? floatval($row->$summarySalKey) : 0;
                                }
                            }

                            $stationEmployeeCount = count($employeeCounter);
                            $summaryGrandEmployee += $stationEmployeeCount;
                            $summaryGrandMpl += $stationMpl;
                            $summaryGrandSal += $stationSal;

                            $tempHtml .= "<tr>";
                                $tempHtml .= "<td>{$station}</td>";
                                $tempHtml .= "<td class='text-center'>{$stationEmployeeCount}</td>";
                                $tempHtml .= "<td class='text-right'>".number_format($stationMpl, 2, ".", ",")."</td>";
                                $tempHtml .= "<td class='text-right'>".number_format($stationSal, 2, ".", ",")."</td>";
                            $tempHtml .= "</tr>";
                        }

                            $tempHtml .= "</tbody>";
                            $tempHtml .= "<tfoot>";
                                $tempHtml .= "<tr>";
                                    $tempHtml .= "<td class='text-right m--font-boldest' colspan='2'>GRAND TOTAL</td>";
                                    $tempHtml .= "<td class='text-right m--font-boldest'>".number_format($summaryGrandMpl, 2, ".", ",")."</td>";
                                    $tempHtml .= "<td class='text-right m--font-boldest'>".number_format($summaryGrandSal, 2, ".", ",")."</td>";
                                $tempHtml .= "</tr>";
                            $tempHtml .= "</tfoot>";
                        $tempHtml .= "</table>";
                    $tempHtml .= "</div>";


                    $resultset["response"] = true;
                    $resultset["html"] = $tempHtml;
                    $resultset["count"] = count($data);
                    $resultset["grand_total"] = $arrGrandTotal;
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }

        echo json_encode($resultset);
    }

    function generate_contribution_deduction_listv1(){
        $post = $this->input->post();
        if(isset($post) && $post){
            if(is_array($post["ps_id"]) && count($post["ps_id"]) > 0){
                $data = $this->reports->generatePayrollSheetContributionDeduction($post["ps_id"]);
                if($data){
                    $this->db->select("LOWER(GROUP_CONCAT(DISTINCT(code))) as code");
                    $qCode = $this->db->get_where("payroll.loans", array("loan_type"=>1));
                    $tempCodes = $qCode->row_array()["code"] ? explode(",", $qCode->row_array()["code"]): array();

                    $tempColumns = array();
                    foreach ($data as $value) {
                        $value = (object) $value;
                        if(isset($value->row_columns) && $value->row_columns){
                            foreach ($value->row_columns as $kk => $vv) {
                                $tempHeader = strtoupper($kk);
                                if(!in_array($tempHeader, $tempColumns)){
                                    $tempColumns[] = $tempHeader;
                                }
                            }
                        }
                    }

                    $arrNoGrandTotal = array("biometricno", "company", "tax_status", "tin_no", "phealth_no", "pagibig_no", "sss_no", "employee_status");
                    $arrGrandTotal = array();
                    $tempHtml = "<table border='1' cellpadding='5' cellspacing='0' style='font-family: roboto; font-size: 10px; width: 100% !important;' id='monthly_contrib_deduct'>";
                    if($data){
                        $tempHtml .="<thead>";
                        $tempHtml .= "<tr>";
                        $tempHtml .= "<th>EMPLOYEE #</th>";
                        $tempHtml .= "<th>EMPLOYEE NAME</th>";
                        
                        foreach ($tempColumns as $value) {
                            $tempKey = strtolower($value);
                            if(!in_array($tempKey, $arrNoGrandTotal) && in_array($tempKey, $tempCodes)){
                                $_tempHeader = strtoupper($tempKey);
                                if (strtolower($tempKey) === 'cal.'){ $_tempHeader = 'SSS CAL'; }
                                elseif (strtolower($tempKey) == 'cal'){ $_tempHeader = 'HDMF CAL'; }
                                $tempHtml .= "<th class='text-center'>{$_tempHeader}</th>";
                            }
                        }

                        $tempHtml .= "</tr>";
                        $tempHtml .="<thead>";
                        $tempHtml .="<tbody>";
                        foreach ($data as $value) {
                            $tempValuex = (object) $value;
                            $tempHtml .= "<tr>";
                            $tempHtml .= "<td>{$tempValuex->idno}</td>";
                            $tempHtml .= "<td>{$tempValuex->employee_name}</td>";

                            foreach ($tempColumns as $vvv) {
                                $tempKey = strtolower($vvv);
                                if(!in_array($tempKey, $arrNoGrandTotal) && in_array($tempKey, $tempCodes)){
                                    $tempValue = isset($tempValuex->$tempKey) && $tempValuex->$tempKey ? $tempValuex->$tempKey: 0;
                                    $decimalTempValue = $tempValue;
                                    $tempHtml .= "<td align='right'>{$decimalTempValue}</td>";
                                    if(isset($arrGrandTotal[$tempKey]) && $arrGrandTotal[$tempKey]){ $arrGrandTotal[$tempKey] += $tempValue; }
                                    else{ $arrGrandTotal[$tempKey] = $tempValue; }
                                }
                            }
                            $tempHtml .= "</tr>";
                        }
                        $tempHtml .="</tbody>";
                    }
                    if($arrGrandTotal){
                        $arrGrandTotal = (object) $arrGrandTotal;
                        $tempHtml .="<tfoot><tr><td colspan='2' class='text-right m--font-boldest'>GRAND TOTAL</td>";

                        foreach ($tempColumns as $key => $vvx) {
                            $tempKey = strtolower($vvx);
                            if(!in_array($tempKey, $arrNoGrandTotal) && in_array($tempKey, $tempCodes)){
                                $_decimalValue = (isset($arrGrandTotal->$tempKey) && $arrGrandTotal->$tempKey)? number_format($arrGrandTotal->$tempKey, 2, ".", ","): 0;
                                $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalValue}</td>";
                            }
                        }
                        $tempHtml .="</tr></tfoot>";
                    }

                    $tempHtml .= "</table>";
                    $tempHtml .= "</table>";
                    $resultset["response"] = true;
                    $resultset["html"] = $tempHtml;
                    $resultset["count"] = count($data);
                    $resultset["grand_total"] = $arrGrandTotal;
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }

        echo json_encode($resultset);
    }

    function generate_taxable_list_monthly(){
        $post = $this->input->post();
        if(isset($post) && $post){
            $ids = $post["ps_id"];
            if(is_array($post["ps_id"]) && count($post["ps_id"]) > 0){
                $data = $this->reports->generatePayrollSheetContributionDeduction($ids);
                if($data){
                    $this->db->select("LOWER(GROUP_CONCAT(DISTINCT(code))) as code");
                    $qCode = $this->db->get_where("payroll.loans", array("loan_type"=>1));
                    $tempCodes = $qCode->row_array()["code"] ? explode(",", $qCode->row_array()["code"]): array();

                    $tempColumns = array();
                    foreach ($data as $key => $value) {
                        $value = (object) $value;
                        if(isset($value->row_columns) && $value->row_columns){
                            foreach ($value->row_columns as $kk => $vv) {
                                $tempHeader = strtoupper($kk);
                                if(!in_array($tempHeader, $tempColumns)){
                                    $tempColumns[] = $tempHeader;
                                }
                            }
                        }
                    }

                    $arrNoGrandTotal = array("biometricno", "company", "tax_status", "tin_no", "phealth_no", "pagibig_no", "sss_no", "employee_status");
                    $arrGrandTotal = array();
                    $tempHtml = "<table border='1' cellpadding='5' cellspacing='0' style='font-family: roboto; font-size: 10px; width: 100% !important;' id='monthly_contrib_deduct'>";
                    if($data){
                        $tempHtml .="<thead>";
                        $tempHtml .= "<tr>";
                        $tempHtml .= "<th>ID #</th>";
                        $tempHtml .= "<th>EMPLOYEE NAME</th>";
                        
                        foreach ($tempColumns as $key => $value) {
                            $tempKey = strtolower($value);
                            if(!in_array($tempKey, $arrNoGrandTotal) && in_array($tempKey, $tempCodes)){
                                $_tempHeader = strtoupper($tempKey);
                                if(strtolower($tempKey) === 'cal.'){ $_tempHeader = 'SSS CAL'; }
                                else if(strtolower($tempKey) == 'cal'){ $_tempHeader = 'HDMF CAL'; }
                                $tempHtml .= "<th class='text-center'>{$_tempHeader}</th>";
                            }
                        }

                        $tempHtml .= "</tr>";
                        $tempHtml .="<thead>";
                        $tempHtml .="<tbody>";
                        foreach ($data as $key => $value) {
                            $tempValuex = (object) $value;
                            $tempHtml .= "<tr>";
                            $tempHtml .= "<td>{$tempValuex->idno}</td>";
                            $tempHtml .= "<td>{$tempValuex->employee_name}</td>";

                            foreach ($tempColumns as $kkk => $vvv) {
                                $tempKey = strtolower($vvv);
                                if(!in_array($tempKey, $arrNoGrandTotal) && in_array($tempKey, $tempCodes)){
                                    $tempValue = isset($tempValuex->$tempKey) && $tempValuex->$tempKey ? $tempValuex->$tempKey: 0;
                                    $decimalTempValue = $tempValue;
                                    $tempHtml .= "<td align='right'>{$decimalTempValue}</td>";
                                    if(isset($arrGrandTotal[$tempKey]) && $arrGrandTotal[$tempKey]){ $arrGrandTotal[$tempKey] += $tempValue; }
                                    else{ $arrGrandTotal[$tempKey] = $tempValue; }
                                }
                            }
                            $tempHtml .= "</tr>";
                        }
                        $tempHtml .="</tbody>";
                    }
                    if($arrGrandTotal){
                        $arrGrandTotal = (object) $arrGrandTotal;
                        $tempHtml .="<tfoot><tr><td colspan='2' class='text-right m--font-boldest'>GRAND TOTAL</td>";

                        foreach ($tempColumns as $key => $vvx) {
                            $tempKey = strtolower($vvx);
                            if(!in_array($tempKey, $arrNoGrandTotal) && in_array($tempKey, $tempCodes)){
                                $_decimalValue = (isset($arrGrandTotal->$tempKey) && $arrGrandTotal->$tempKey)? number_format($arrGrandTotal->$tempKey, 2, ".", ","): 0;
                                $tempHtml .= "<td class='text-right m--font-boldest'>{$_decimalValue}</td>";
                            }
                        }
                        $tempHtml .="</tr></tfoot>";
                    }

                    $tempHtml .= "</table>";
                    $tempHtml .= "</table>";
                    $resultset["response"] = true;
                    $resultset["html"] = $tempHtml;
                    $resultset["grand_total"] = $arrGrandTotal;
                }else{
                    $resultset["response"] = false;
                }
            }else{
                $resultset["response"] = false;
            }
        }else{
            $resultset["response"] = false;
        }

        echo json_encode($resultset);
    }

    function test_multiple(){
        echo "<pre>";
        $ids = "579,582,583,616,627,629,631,632,633,634,635,636,637,638,639,640,641,642,643,644,645,646,647,648,649,650,651,652,653,654,655,656,657,658,659,660,661,662,663,664,665,666,667,668,669,670,671,672,673,674,675,676,677,678,679,680,681,682,683,684,685,686,691,696,697,698,699,1148,1225,1226,1227,1229,1230,1231,1232,1233,1234,1235,1236,1237,1238,1239,1240,1241,1242,1243,1244,1245,1246,1247,1248,1249,1250,1251,1252,1253,1254,1255,1256,1257,1258,1259,1260,1261,1262,1263,1264,1265,1266,1267,1268,1312,1313,1314,1315,1316,1317,1318,1319,1320,1321,1322,1323,1324,1325,1326,1327,1328";
        $ids = explode(",", $ids);
        $datax = $this->reports->generateContributionDeductionAdjustments($ids);
        $data = $this->reports->generatePayrollSheetContributionMultiple($ids);
        $tempColumns = array();
        $arrFields = array("sss", "sss_prov", "ph", "hdmf", "tax");
        foreach ($data as $key => $value) {
            $value = (object) $value;
            if(isset($value->row_columns) && $value->row_columns){
                foreach ($value->row_columns as $kk => $vv) {
                    if(!in_array($kk, $arrFields)){
                        $tempHeader = strtoupper($kk);
                        if(!in_array($tempHeader, $tempColumns)){
                            $tempColumns[] = $tempHeader;
                        }
                    }
                }
            }
        }

        $tempHtml = "<table border='1' cellpadding='5' cellspacing='0'>";
        if($data){
            $tempHtml .="<thead>";
            $tempHtml .= "<tr>";
            $tempHtml .= "<th>ID #</th>";
            $tempHtml .= "<th>Employee Name</th>";
            $tempHtml .= "<th>Reg Pay</th>";
            $tempHtml .= "<th>Gross Pay</th>";
            $tempHtml .= "<th>SSS</th>";
            $tempHtml .= "<th>SSS PROV</th>";
            $tempHtml .= "<th>PHIC</th>";
            $tempHtml .= "<th>HDMF</th>";
            $tempHtml .= "<th>TAX</th>";
            foreach ($tempColumns as $key => $value) {
                $tempHtml .= "<th>{$value}</th>";
            }
            $tempHtml .= "<th>TOTAL</th>";
            $tempHtml .= "</tr>";
            $tempHtml .="<thead>";
            $tempHtml .="<tbody>";
            foreach ($data as $key => $value) {
                $rowTotal = 0;
                $tempValuex = (object) $value;
                $tempHtml .= "<tr>";
                $tempHtml .= "<td>{$tempValuex->idno}</td>";
                $tempHtml .= "<td>{$tempValuex->employee_name}</td>";
                $tempHtml .= "<td align='right'>{$tempValuex->regular_pay}</td>";
                $tempHtml .= "<td align='right'>{$tempValuex->gross_pay}</td>";
                $tempHtml .= "<td align='right'>{$tempValuex->sss}</td>";
                $tempHtml .= "<td align='right'>{$tempValuex->sss_prov}</td>";
                $tempHtml .= "<td align='right'>{$tempValuex->ph}</td>";
                $tempHtml .= "<td align='right'>{$tempValuex->hdmf}</td>";
                $tempHtml .= "<td align='right'>{$tempValuex->tax}</td>";
                $rowTotal = $tempValuex->sss + $tempValuex->sss_prov + $tempValuex->ph + $tempValuex->hdmf + $tempValuex->tax;
                foreach ($tempColumns as $kkk => $vvv) {
                    $tempKey = strtolower($vvv);
                    $tempValue = isset($tempValuex->$tempKey) && $tempValuex->$tempKey ? $tempValuex->$tempKey: 0;
                    $rowTotal += $tempValue;
                    $tempHtml .= "<td align='right'>{$tempValue}</td>";
                }
                $decimalRowTotal = number_format($rowTotal, 2, ".", "");
                $tempHtml .= "<td align='right'>{$decimalRowTotal}</td>";
                $tempHtml .= "</tr>";
            }
            $tempHtml .="</tbody>";
        }
        $tempHtml .= "</table>";
        echo $tempHtml;
    }

    function get_current_signatory_by_company_and_type($id=null, $type=null){
        $this->load->model("payroll/payroll_m", "payroll");
        $data = $this->payroll->getCurrentSignatoryByCompanyAndType($id, $type);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function update_printable_signatories(){
        $this->load->model("payroll/payroll_m", "payroll");
        $data = $this->payroll->updatePrintableSignatories();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function reset_printable_signatories(){
        $this->load->model("payroll/payroll_m", "payroll");
        $data = $this->payroll->resetPrintableSignatories();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function select_employee($type=null){
        $this->load->model("payroll/payroll_m", "payroll");
        $data = $this->payroll->selectEmployee($type);
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function generate_payroll_journal(){
        $data = $this->reports->generatePayrollJournal();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    function get_payroll_journal_request(){
        $data = $this->reports->getPayrollJournalRequest();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    function get_posted_payroll_sheet_years() {
        echo json_encode($this->reports->getPostedPayrollSheetYears());
    }

    function get_payroll_journal_column(){
        // echo json_encode($this->reports->getEmployeeContributions());
        $data = $this->reports->getEmployeeContributions();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    function get_loan_types(){
        $data = $this->reports->getLoanTypes();
        $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    function generate_posted_netpay_records(){
        $data = $this->reports->generatePostedNetpayRecords();
        $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    function generate_overtime_summary(){
        $data = $this->reports->generateOvertimeSummary();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }
    
    function get_overtime_summary(){
        $data = $this->reports->getOvertimeSummaryRequest();
        $this->output
        ->set_content_type('json')
        ->set_output(json_encode($data));
    }

    function generate_leave_credits_report(){
        $data = $this->reports->generateLeaveCreditsReport();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    public function custom_report() {
        $this->load->model("payroll/payroll_m", "payroll");
        $tempData = array(); 
        $tempData["years"] = $this->payroll->getPostedPayrollSheetYearsData();
        $tempData["company"] = $this->payroll->select2CompanyData();
        $tempData["payout_schedule"] = $this->payroll->select2PayoutScheduleData();
        $tempData['payment_mode'] = $this->payroll->getPaymentModeSelect2Data();
        
        $this->core_layout->setPageTitle("Payroll - Custom Payroll sheet Report");
        $this->core_layout->setPrivilegeName("payroll_custom_report");
        $this->core_layout->addJs("js/buttons.print.min.js", true);

        $version = filemtime(FCPATH.'assets/js/payroll/reports/custom_payrollsheet_report.js');
        $this->core_layout->addJs("js/payroll/reports/custom_payrollsheet_report.js", true, $tempData, "?v={$version}");

        $this->load->view("core/templates/header");
        $this->load->view("payroll/reports/custom_payrollsheet_report");
        $this->load->view("core/templates/footer");
    }

    public function generate_custom_posted_netpay_records(){
        $data = $this->reports->generateCustomPostedNetpayRecords();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }

    function update_print_payrollsheet_netpay(){
        $data = $this->reports->update_print_payrollsheet_netpay();
        $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    public function nightdiff_summary() {
        $this->load->model("payroll/payroll_m", "payroll");
        $tempData = array(); 
        $tempData["years"] = $this->payroll->getPostedPayrollSheetYearsData();
        $tempData["company"] = $this->payroll->select2CompanyData();
        $tempData["payout_schedule"] = $this->payroll->select2PayoutScheduleData();
        
        $this->core_layout->setPageTitle("Payroll - Night Differential Summary Report");
        $this->core_layout->setPrivilegeName("payroll_nightdiff_summary");
        $this->core_layout->addJs("js/buttons.print.min.js", true);

        $version = filemtime(FCPATH.'assets/js/payroll/reports/nightdiff_summary.js');
        $this->core_layout->addJs("js/payroll/reports/nightdiff_summary.js", true, $tempData, "?v={$version}");

        $this->load->view("core/templates/header");
        $this->load->view("payroll/reports/nightdiff_summary");
        $this->load->view("core/templates/footer");
    }

    function generate_nightdiff_summary(){
        $data = $this->reports->generateNightDiffSummary();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    public function payrollsheet_summary() {
        $this->load->model("payroll/payroll_m", "payroll");
        $tempData = array(); 
        $tempData["years"] = $this->payroll->getPostedPayrollSheetYearsData();
        $tempData["company"] = $this->payroll->select2CompanyData();
        $tempData["payout_schedule"] = $this->payroll->select2PayoutScheduleData();

        $version = filemtime(FCPATH.'assets/js/payroll/reports/payrollsheet_summary.js');
        
        $this->core_layout->setPageTitle("Payroll - Payroll Sheet Summary Report");
        $this->core_layout->setPrivilegeName("payroll_payrollsheet_summary");
        $this->core_layout->addJs("js/buttons.print.min.js", true);
        $this->core_layout->addJs("js/payroll/reports/payrollsheet_summary.js", true, $tempData,"?v={$version}");

        $this->load->view("core/templates/header");
        $this->load->view("payroll/reports/payrollsheet_summary");
        $this->load->view("core/templates/footer");
    }

    function generate_payrollsheet_summary(){
        $data = $this->reports->generate_payrollsheet_summary();
            $this->output
                ->set_content_type('json')
                ->set_output(json_encode($data));
    }

    function count_print(){
        $data = $this->reports->print_count();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
    
    function select2_station() {
        $data = $this->reports->select2_station();
        $this->output
            ->set_content_type('json')
            ->set_output(json_encode($data));
    }
}
