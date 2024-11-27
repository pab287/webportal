<style>
    @media print {
        @page {
        size: landscape
    }
  body * {
    visibility: hidden;
  }
  #payroll_journal_table, #payroll_journal_table * {
    visibility: visible;
  }
  #payroll_journal_table {
    position: absolute;
    left: 0;
    top: 0;
    page-break-inside: inherit;
  }
  /* #tbl-journal{
        page-break-inside: avoid
    } */

    /* table tfoot {
    display: table-row-group;
} */
  
}
</style>
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        PAYROLL JOURNAL
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
                <div class="tbl-responsive-sm col-md-12" id="payroll_journal_table">
                    <div class='row'>
                        <div class="col-md-4" id="filter_details">
                            <h5>Payroll Journal Detail</h5>
                            <span>'
                            </span>
                        </div>
                        <div class="col-md-4 text-center">
                            <h4 class="company"></h4>
                        </div>
                    </div>
                    <table class="table table-bordered table-condensed table-sm" width="100%" style="font-family: roboto; padding: 0px !important;" id="tbl-journal">
                    <thead>
                        <tr>
                            <th class='text-center'>ID NO</th>
                            <th class='text-center'>Name</th>
                            <th>Pay Date</th>
                            <th>Net Due</th>
                            <?php foreach($data as $loans){ ?>
                                <th title="<?= strtoupper($this->reports->getLoanByCode($loans)); ?>"><?= $loans; ?></th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    
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
            <form id="frm-journal-report" method="post" action="<?php echo site_url("payroll/reports/generate_payroll_journal"); ?>">
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
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="company" class="m--font-bolder">Company <small>( Optional )</small></label>
                                <select id="company" class="form-control" name="company"><option></option></select>
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