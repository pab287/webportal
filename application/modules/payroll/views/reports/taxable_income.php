<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printArea, #printArea * {
        visibility: visible;
    }
    #printArea {
        left: 0;
        top: 0;
    }

  
}
</style>
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Taxable Income
                        <small>
                            Masterfile
                        </small>
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                        <span>
                            <button id="generate_report" class="btn btn-default m-btn m-btn--icon"
                                    data-toggle="modal"
                                    data-target="#generate-report-modal" 
                                    data-original-title="Generate Payroll SSS Contribution"
                                    data-skin="dark"
                                    data-delay='{"show": 500}'>
                                <span>
                                    <i class="fa fa-gears pr-1"></i>
                                    Generate Report
                                </span>
                            </button>
                            <button class='btn btn-primary m-btn' onclick="printDivMonthly('printArea')">
                                <span>
                                <i class="fa fa-print pr-1"></i>
                                    Print
                                </span>
                            </button>
                            <button class='btn btn-success' id="excel_btn">
                                <span>
                                <i class="fa fa-file-excel-o pr-1"></i>
                                    Excel
                                </span>
                            </button>
                        </span>
                    </li>
                </ul>

            </div>
        </div>
        <div class="m-portlet__body">
            <div id="printArea">
                <div class="col-md-12">
                    <h5 class="text-center">TAXABLE INCOME REPORT</h5>
                    <h4 class="text-center" id="company">&nbsp;</h4>
                    <h6 class="text-center" id="month_year">&nbsp;</h6>
                </div>
                <div id="append--table_content">
                    
                </div>
            </div>
            <!--::dt begin::-->
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table class="table table-striped table-bordered table-condensed" width="100%" id="tbl-taxable_income" style="font-family: roboto; font-size: 10px; width: 100% !important;"></table>
                </div>
            <!--::dt end::-->
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="generate-report-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="generate-remittance_content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="frm-remittance-report" method="post" action="<?php echo site_url("payroll/reports/generate_taxable_income_report"); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="row mr-auto">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="m--font-bolder">FILTER BY</label>
                                <div class="m-checkbox-inline">
                                    <label class="m-checkbox">
                                        <input type="radio"
                                            name="group"
                                            data-validation="required"
                                            value="1" class="valid"
                                            @click="tempShowByDates(1)"
                                            checked />
                                        MONTH<span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="radio"
                                            name="group"
                                            data-validation="required"
                                            value="2" class="valid"
                                            @click="tempShowByDates(2)" />
                                        YEAR<span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="m--font-bolder">INCLUDE FILTER</label>
                                <div class="m-checkbox-inline">
                                    <label class="m-checkbox">
                                        <input type="checkbox" name="13th_month" value="1" class="valid" @click="toggle13thMonthFilter($event)" />
                                        13TH MONTH<span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div id="filter-by-month-year" class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="row mr-auto">
                                <template v-if="year_picker === true">
                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <label for="" class="required m--font-bolder">YEAR</label>
                                    <select class="form-control" name="filter_year" data-validation="required">
                                        <option></option>
                                    </select>
                                </div>
                                </template>
                                <template v-if="month_picker === true">
                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0">
                                    <label for="" class="required m--font-bolder">MONTH</label>
                                    <select class="form-control" name="filter_month" data-validation="required">
                                        <option></option>
                                    </select>
                                </div>
                                </template>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="company" class="required m--font-bolder">Company</label>
                                <select id="company" class="form-control" name="company" data-validation="required"><option></option></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div for="employee" class="form-group">
                                <label class="m--font-bolder">Employee <small>( Optional )</small></label>
                                <select id="employee" class="form-control" name="employee[]" multiple><option></option></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label class="m--font-bolder">Visible Fields</label>
                                <div class="m-checkbox-inline">
                                    <label class="m-checkbox mb-3">
                                        <input type="checkbox" value="biometricno" class="temp-visible_fields" @click="toggleCheckbox($event)">Biometric #<span></span>
                                    </label>
                                    <label class="m-checkbox mb-3">
                                        <input type="checkbox" value="company" class="temp-visible_fields" @click="toggleCheckbox($event)">Company<span></span>
                                    </label>
                                    <label class="m-checkbox mb-3">
                                        <input type="checkbox" value="tax_status" class="temp-visible_fields" @click="toggleCheckbox($event)">Tax Status<span></span>
                                    </label>
                                    <label class="m-checkbox mb-3">
                                        <input type="checkbox" value="tin_no" class="temp-visible_fields" @click="toggleCheckbox($event)">TIN #<span></span>
                                    </label>
                                    <label class="m-checkbox mb-3">
                                        <input type="checkbox" value="phealth_no" class="temp-visible_fields" @click="toggleCheckbox($event)">PHILHEALTH #<span></span>
                                    </label>
                                    <label class="m-checkbox mb-3">
                                        <input type="checkbox" value="pagibig_no" class="temp-visible_fields" @click="toggleCheckbox($event)">PAGIBIG #<span></span>
                                    </label>
                                    <label class="m-checkbox mb-3">
                                        <input type="checkbox" value="sss_no" class="temp-visible_fields" @click="toggleCheckbox($event)">SSS #<span></span>
                                    </label>
                                    <label class="m-checkbox mb-3">
                                        <input type="checkbox" value="employee_status" class="temp-visible_fields" @click="toggleCheckbox($event)">Employee Status<span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info m-btn m-btn--icon btnAdvance_search">
                            <span><i class="fa fa-gears pr-2"></i>GENERATE</span>
                    </button>
                    <button type="button" @click="resetFields" class="btn btn-danger m-btn m-btn--icon btnAdvance_search">
                            <span><i class="fa fa-refresh pr-2"></i>RESET FILTER</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>