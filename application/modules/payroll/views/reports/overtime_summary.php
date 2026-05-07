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
    #portlet--signatories { page-break-inside: inherit; }

    #print-counter {
        display: block !important;
    }
}

#print-counter {
    display: none;
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
                    <li id="table-actions" class="m-portlet__nav-item text-right m-animate-fade-in m--hide">
                        <button class='btn btn-brand m-btn' onclick="printSummary()">
                            <span><i class="fa fa-print pr-1"></i> Print</span>
                        </button>
                        <button class='btn btn-warning m-btn text-white' onclick='exportExcel()'>
                            <i class="fa fa-download pr-1"></i>
                            <span>Export Excel</span>
                        </button>
                    </li>
                    <li id="actionSignatories" class="m-portlet__nav-item text-right m-animate-fade-in" v-if="show_signatories === true">
                        <div class="btn-group">
                            <button type="button" class="btn btn-accent btnEdit">Signatories</button>
                            <button type="button" class="btn btn-accent btnEdit dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu" x-placement="bottom-start">
                            <a class="dropdown-item" href="javascript:void(0);" @click="editSignatories"><i class="la la-pencil"></i> Edit Signatories</a>
                            <a class="dropdown-item" href="javascript:void(0);" @click="resetSignatories"><i class="la la-refresh"></i> Reset Signatories</a>
                            </div>
                        </div>
                    </li>
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
            <div id="overtime_summary_table">
                <div class="tbl-responsive-sm">
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
                                <div class="col-md-12">
                                    <div class="row justify-content-between align-items-start">
                                        <div class="col-md-8 printable-top--filter">
                                            <div>FILTERED BY: {{ filters.filter_by ? filters.filter_by: '---' }}</div>
                                            <div>COVERAGE DATE: {{ filters.coverage_date ? filters.coverage_date: '---' }}</div>
                                            <div>PAYROLL GROUP: {{ filters.payroll_group ? filters.payroll_group: '---' }}</div>
                                        </div>
                                        <div id="print-counter" class="col-md-3">
                                            <div>PRINT #: <b>{{ print_counter.count }}</b></div>
                                            <div v-if="print_counter.last_printed">LAST PRINTED BY: <b>{{ print_counter.last_printed }}</b></div>
                                            <div v-if="print_counter.last_printed_at">LAST PRINTED AT: <b>{{ print_counter.last_printed_at }}</b></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
    
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                        <table class="table table-bordered" style="font-family: roboto; width: 100%;" id="tbl-overtime-summary">
                            <thead>
                                <tr>
                                    <th class="m--hide">EMPLOYEE NAME</th>
                                    <th>DATE</th>
                                    <th>SPECIFIED DAY</th>
                                    <th>DAILY RATE</th>
                                    <th>DAILY ALLOWANCE</th>
                                    <th>NO. HRS</th>
                                    <th>OT PAY</th>
                                    <th>25% OT PAY</th>
                                    <th>30% OT PAY</th>
                                    <th>NDIFF HRS.</th>
                                    <th>NDIFF PAY</th>
                                    <th>ALLOWANCE PAY</th>
                                    <th>ADJUSTMENT</th>
                                    <th>AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <th class="text-right" colspan="6">&nbsp;</th>
                                <th class="text-right"><span class='m--font-boldest'>₱ 0.00</span></th>
                                <th class="text-right"><span class='m--font-boldest'>₱ 0.00</span></th>
                                <th class="text-right"><span class='m--font-boldest'>₱ 0.00</span></th>
                                <th class="text-right"><span class='m--font-boldest'>-</span></th>
                                <th class="text-right"><span class='m--font-boldest'>₱ 0.00</span></th>
                                <th class="text-right"><span class='m--font-boldest'>₱ 0.00</span></th>
                                <th class="text-right"><span class='m--font-boldest'>-</span></th>
                                <th class="text-right"><span class='m--font-boldest'>₱ 0.00</span></th>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div id="portlet--signatories">
                    <template v-if="count > 0">
                        <table style='margin-top: 60px; width: 100%;'>
                            <thead>
                                <tr><th>&nbsp;</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                    <template v-for="(item, index) in row.meta_field" v-if="item.is_active === true">
                                        <div style='display: inline-block; position: relative; width: 25%; margin-top: 30px;'>
                                            <p style='font-weight: bold; margin-left: 10px;'>{{item.label}}:</p>
                                            <p style='font-weight: 600; margin-left: 10px; margin-right: 50px; margin-top: 50px; padding-top: 10px; border-top: 1px solid #000000;'>{{item.value}}</p>
                                        </div>
                                    </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </template>
                </div>
            </div>
            <!--::dt end::-->
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="generate-report-modal">
    <div class="modal-dialog modal-lg">
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
                                <label for="" class="m--font-bolder">FILTER BY</label>
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
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="company" class="m--font-bolder">Company *</label>
                                <select id="company" class="form-control" name="company" data-validation="required"><option></option></select>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="company" class="m--font-bolder">
                                    Payout Mode
                                    <small class="m-form__help p-0">( Optional )</small>
                                </label>
                                <select id="payout_mode" class="form-control" name="payout_mode"><option></option></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
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

<div class="modal fade" tabindex="-1" id="modal-ps--signatory">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="signatory--content">
            <div class="modal-header">
                <h5 class="modal-title">Overtime Summary Signatories</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="updatePrintableSignatories" method="post" action="<?php echo site_url("payroll/reports/update_printable_signatories"); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="id" v-model="row.id" />
                <input type="hidden" name="signatory_id" v-model="row.signatory_id" />
                <input type="hidden" name="user_id" v-model="row.user_id" />
                <div class="modal-body">
                    <template v-if="count > 0">
                        <template v-for="(item, index) in row.meta_field">
                        <div class="form-group m-form__group row">
                            <label for="" class="col-3 col-form-label">{{item.label}}</label>
                            <div class="col-8">
                                <select class="form-control m-input select2--value" 
                                    data-validation="required" 
                                    :name="'value['+index+']'" 
                                    :disabled="item.is_active === false">
                                    <option :value="item.value" selected>{{item.value}}</option>
                                </select>
                            </div>
                            <div class="col-1 text-right">
                                <span class="m-switch m-switch--sm">
                                    <label>
                                        <input type="checkbox" :checked="item.is_active === true" @click="activeSignatory(event)" />
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                        </template>
                    </template>
                    <template v-else>
                        <div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="m-alert__icon">
                                <i class="flaticon-exclamation-1"></i>
                                <span></span>
                            </div>
                            <div class="m-alert__text">
                                <strong>NO ASSIGNED SIGNATORIES!</strong>Please add/update the signatory.
                            </div>
                        </div>
                    </template>
                </div>
                <template v-if="count > 0">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnSave">Update</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
                    </div>
                </template>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal-ps--reset-signatory">
    <div class="modal-dialog">
        <div class="modal-content" id="reset-signatory--content">
            <div class="modal-header">
                <h5 class="modal-title">Reset - Overtime Summary Signatories</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="resetPrintableSignatories" method="post" action="<?php echo site_url("payroll/reports/reset_printable_signatories"); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="id" v-model="row.id" />
                <div class="modal-body">
                    <h4>Are you sure you want to reset the current signatories?</h4>
                    <template v-if="count > 0">
                        <template v-for="(item, index) in row.meta_field">
                        <div class="form-group m-form__group row m--marginless" v-if="item.is_active === true">
                            <label for="" class="col-4 col-form-label">{{item.label}}</label>
                            <label for="" class="col-8 col-form-label m--font-bolder">{{item.value}}</label>
                        </div>
                        </template>
                    </template>
                    <template v-else>
                        <div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="m-alert__icon">
                                <i class="flaticon-exclamation-1"></i>
                                <span></span>
                            </div>
                            <div class="m-alert__text">
                                <strong>
                                    NO ASSIGNED SIGNATORIES!
                                </strong>
                                Please add/update the signatory.
                            </div>
                        </div>
                    </template>
                </div>
                <template v-if="count > 0">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnSave">Reset</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
                    </div>
                </template>
            </form>
        </div>
    </div>
</div>
