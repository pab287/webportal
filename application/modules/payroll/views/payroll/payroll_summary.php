<style>
    #payroll_summary_table .print_date {
        display: none;
    }
@media print {
    body * {
        visibility: hidden;
    }
    #payroll_summary_table, #payroll_summary_table * {
        visibility: visible;
    }
    #payroll_summary_table {
        position: absolute;
        left: 0;
        top: 0;
        page-break-inside: inherit;
    }
    #payroll_summary_table{
        page-break-inside: avoid
    }

    table tfoot {
        display: table-row-group;
    }
    #payroll_summary_table .print_date {
        display: inline;
    }
  
}
</style>
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        PAYROLL SUMMARY
                        <small>
                            Masterfile
                        </small>
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
                        <span>
                            <button class='btn btn-primary m-btn' onclick='window.print()'><span><i class="fa fa-print pr-1"></i>
                                    Print
                                </span></button>
                        </span>
                    </li>
                </ul>

            </div>
        </div>
        <div class="m-portlet__body">
            <!--::dt begin::-->
                <div class="tbl-responsive-sm col-md-12" id="payroll_summary_table">
                    <div class='text-center'>
                        <h4>PAYROLL SUMMARY</h4>
                    </div>
                    <div class='row'>
                        <div class="col-md-6">
                            <h5 id="period"></h5>
                        </div>
                        <div class="col-md-6 ps_legend text-right m--hide">    
                            <span><span class="m--font-success fa fa-check"></span> - PAID BONUS</span><br>
                            <span><span class="m--font-danger fa fa-times"></span> - UNPAID BONUS</span>
                        </div>
                    </div>
                   
                    
                    <table class="table table-striped table-bordered table-condensed table-sm" id="tbl-summary" width="100%" style="font-family: roboto; padding: 0px !important;">
                        <thead>
                            <!-- <tr>
                                <td colspan='6' class='text-center'><p style='text-align: center;'>
                                    <h5>PAYROLL SUMMARY PER PERIOD</h5>
                                    <h6 id="period"></h6>
                                    </p>
                                </td>
                            </tr> -->
                            <tr>
                                <th>Employee Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Pay Date</th>
                                <th>Basic Pay</th>
                                <th>grand total</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class='text-right'></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                    <span class="print_date pull-right"><?= date("m-d-Y H:i:s") ?></span>
                </div>
            <!--::dt end::-->
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="generate-report-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="generate-summary_content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- payroll summary filters -->
            <form id="frm-summary-report" method="post" action="<?php echo site_url("payroll/payroll/generate_payroll_summary"); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div id="">
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
                                        <label class="m-checkbox">
                                            <input type="radio" id="incentive"
                                                name="group"
                                                data-validation="required"
                                                value="4" class="valid"
                                                @click="tempShowByDates(4)">
                                            INCENTIVE<span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div id="filter-by-date-range" class="col-xl-6 col-lg-6 col-md-6 col-sm-12" v-if="show_by_date === true">
                                <div class="form-group">
                                    <label for="date-range" class="m--font-bolder">SELECT DATE RANGE(Pay Date)</label>
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
                                        <select class="form-control" name="filter_year" data-validation="required"></select>
                                    </div>
                                </div>
                            </div>
                            <div id="filter-by-year" class="col-xl-6 col-lg-6 col-md-6 col-sm-12 m-animate-fade-in" v-if="year_picker === true">
                                <div class="row">
                                    <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                        <label for="" class="required m--font-bolder">YEAR</label>
                                        <select class="form-control" name="filter_year" data-validation="required"></select>
                                    </div>
                                </div>
                            </div>
                            <div id="filter-by-incentive" class="col-xl-12 col-lg-12 col-md-12 col-sm-12 m-animate-fade-in" v-if="incentive_picker === true">
                                <div class="row mb-3">
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mt-2 pr-0">
                                        <div class="form-group m-form__group">
                                            <label class="required mb-1">Incentive Type</label>
                                            <select id="incentive_type"
                                            class="form-control" 
                                            data-validation="required" name="filter_incentive"></select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mt-2 pr-0">
                                        <div class="form-group m-form__group">
                                            <label class="mb-1">Coverage Date</label>
                                            <template v-if="has_coverage_date === true">
                                                <p class="mb-0 mt-2 m--font-boldest m--font-primary incentive_coverage">{{row.dt_from}} - {{row.dt_to}}</p>
                                                <input type="hidden" name="date_range" :value="getDateRange(row.date_from, row.date_to)" />
                                                <input id="bonus-code" type="hidden" name="bonus_code" v-model="row.name" />
                                            </template>
                                            <template v-else>
                                                <p>---</p>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <label for="" class="required m--font-bolder">YEAR</label>
                                        <select class="form-control" name="incentive_year" data-validation="required"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="company" class="m--font-bolder">Company <span style="color: red;">*</span></label>
                                    <select id="company" class="form-control" name="company" data-validation="required"><option></option></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group m-form__group">
                                    <label for="payroll_group" class="mb-1" style="font-weight: 600;">
                                        PAYROLL GROUP
                                        <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                                    </label>
                                    <select class="form-control" id="payroll_group" name="payroll_group[]" multiple="multiple"></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div for="employees" class="form-group">
                                    <label class="m--font-bolder">Employee <small>( Optional )</small></label>
                                    <select id="employees" class="form-control" name="employees[]" multiple></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12" v-if="show_by_date === true">
                                <label class="m-checkbox m--font-boldest" id="bonus_toggle">
                                    <input type="checkbox" id="thmonth_toggle" name="thirteenth_month">
                                    13th/MID PAY<span></span>
                                </label>
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
            <!-- end of payroll summary filters -->
        </div>
    </div>
</div>