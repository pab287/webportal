<style>
@media print {
    @page { size: auto; margin: 0.75cm; }
    body * { visibility: hidden; }
    #overtime_summary_table, #overtime_summary_table * { visibility: visible; }
    #overtime_summary_table {
        position: absolute;
        left: 0;
        top: 0;
        page-break-inside: inherit;
    }
    table { font-size: 12px; }
    table thead th { font-size: 11px; }
    .table th, .table td { padding: 0.25rem; }
    table.dataTable tfoot tr:first-child th{ border-top: 1px solid #000000; }
    table.dataTable tfoot tr:first-child th{ border-bottom: 4px double #000000; }
}
</style>
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        OVERTIME SUMMARY <small>Report</small>
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                        <span data-toggle="modal"
                            data-target="#generate-report-modal">
                            <button class="btn btn-success m-btn m-btn--icon"
                                    data-toggle="m-tooltip" data-original-title="Generate Payroll Payslip"
                                    data-skin="dark"
                                    data-delay='{"show": 500}'>
                                <span>
                                    <i class="fa fa-gears pr-1"></i>
                                    Generate Report
                                </span>
                            </button>
                        </span>
                    </li>
                </ul>

            </div>
        </div>
        <div class="m-portlet__body">
            <!--::dt begin::-->
                <div class="row">
                    <div id="table-actions" class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12 text-right m-animate-fade-in m--hide">
                        <button class='btn btn-brand m-btn' onclick='window.print()'>
                            <span><i class="fa fa-print pr-1"></i> Print</span>
                        </button>
                        <button class='btn btn-warning m-btn text-white' onclick='exportExcel()'>
                            <i class="fa fa-download pr-1"></i>
                            <span>Export Excel</span>
                        </button>
                    </div>
                </div>
                <div class="tbl-responsive-sm" id="overtime_summary_table">
                    <div id="report-header">
                        <template v-if="show_header">
                            <div class="row mb-3">
                                <div class="col-md-12 filter-title text-center">
                                    <h5>OVERTIME SUMMARY REPORT</h5>
                                    <div class="printable-top--header">
                                        <h6 v-if="filters.company_description">{{ filters.company_description }}</h6>
                                        <p v-if="filters.company_address">{{ filters.company_address }}</p>
                                    </div>
                                </div>
                                <div class="printable-top--filter">
                                    <div class="col-md-12">FILTERED BY: {{ filters.filter_by ? filters.filter_by: '---' }}</div>
                                    <div class="col-md-12">COVERAGE DATE: {{ filters.coverage_date ? filters.coverage_date: '---' }}</div>
                                    <div class="col-md-12">PAYROLL GROUP: {{ filters.payroll_group ? filters.payroll_group: '---' }}</div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                        <table class="table table-bordered" width="100%" style="font-family: roboto;" id="tbl-overtime-summary">
                            <thead>
                                <tr>
                                    <th class="m--hide">EMPLOYEE NAME</th>
                                    <th>DATE</th>
                                    <th>SPECIFIED DAY</th>
                                    <th>DAILY RATE</th>
                                    <th>DAILY ALLOWANCE</th>
                                    <th>NO. OF HOURS</th>
                                    <th>OT PAY</th>
                                    <th>ND HRS WORKED</th>
                                    <th>NIGHT DIFFERENTIAL</th>
                                    <th>AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <th class="text-right" colspan="9">&nbsp;</th>
                                <th class="text-right"><span class='m--font-boldest'>₱ 0.00</span></th>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <!--::dt end::-->
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="generate-report-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="generate-journal_content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- payroll summary filters -->
            <form id="frm-journal-report" method="post" action="<?php echo site_url("payroll/reports/generate_overtime_summary"); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div  class="modal-body">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label class="m--font-bolder">FILTER BY</label>
                                <div class="m-checkbox-inline">
                                    <label class="m-checkbox">
                                        <input type="radio" id="date_range_period"
                                            name="group"
                                            data-validation="required"
                                            value="1" class="valid"
                                            @click="tempShowByDates(1)"
                                            checked>
                                        DATE RANGE<span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="radio" id="monthly_period"
                                            name="group"
                                            data-validation="required"
                                            value="2" class="valid"
                                            @click="tempShowByDates(2)">
                                        MONTH<span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="radio" id="yearly_period"
                                            name="group"
                                            data-validation="required"
                                            value="3" class="valid"
                                            @click="tempShowByDates(3)">
                                        YEAR<span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div id="filter-by-date-range" class="col-xl-6 col-lg-6 col-md-6 col-sm-12" v-if="show_by_date === true">
                            <div class="form-group">
                                <label for="date-range" class="m--font-bolder">SELECT DATE RANGE</label>
                                <div class="input-group" id="date-picker">
                                    <input type="text" class="form-control m-input"
                                        placeholder="MMM DD, YYYY - MMM DD, YYYY" id="date-range"
                                        name="date_range" data-validation="required" autocomplete='off' @click="tempShowPicker()">
                                    <span class="input-group-addon">
                                        <i class="la la-calendar-check-o"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div id="filter-by-month-year" class="col-xl-6 col-lg-6 col-md-6 col-sm-12 m-animate-fade-in" v-if="month_picker === true">
                            <div class="row">
                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <label for="" class="required m--font-bolder">MONTH</label>
                                    <select class="form-control" name="filter_month" data-validation="required">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0">
                                    <label for="" class="required m--font-bolder">YEAR</label>
                                    <select class="form-control" name="filter_year" data-validation="required">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="filter-by-year" class="col-xl-6 col-lg-6 col-md-6 col-sm-12 m-animate-fade-in" v-if="year_picker === true">
                            <div class="row">
                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <label for="" class="required m--font-bolder">YEAR</label>
                                    <select class="form-control" name="filter_year" data-validation="required">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="company" class="m--font-bolder">Company *</label>
                                <select id="company" class="form-control" name="company" data-validation="required"><option></option></select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
                            <div class="form-group m-form__group">
                                <label for="payroll_group" class="mb-1 m--font-bolder">
                                    PAYROLL GROUP
                                    <small class="m-form__help p-0">( Optional )</small>
                                </label>
                                <select class="form-control" id="payroll_group" name="payroll_group[]" multiple></select>
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
                </div>
                <div class="modal-footer">
                    <button class="btn btn-info m-btn m-btn--icon btnView">
                            <span><i class="fa fa-gears pr-2"></i>GENERATE</span>
                    </button>
                    <button type="button" @click="resetFields" class="btn btn-danger m-btn m-btn--icon btnAdvance_search">
                            <span><i class="fa fa-refresh pr-2"></i>RESET FILTER</span>
                    </button>
                </div>
            </form>
            <!-- end of payroll summary filters -->
        </div>
    </div>
</div>