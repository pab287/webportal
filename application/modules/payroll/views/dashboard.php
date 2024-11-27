<style>
#employee-status-chart-container{
    width: 100%;
    height: 100%;
}

#export_contributions th {
    text-align: right !important;
}
</style>
<div class="m-content">
    <div class="row">
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
            <div class="m-portlet m-portlet--head-sm m-portlet--bordered-semi">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title" id="vue_head_data_ot">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-clock-o"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                OVERTIME
                                <small class='date_range_filter'>{{filters.date}}</small>
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                                data-dropdown-toggle="hover" aria-expanded="true">
                                <a href="#"
                                   class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl">
                                    <i class="la la-ellipsis-v m--font-brand"></i>
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"
                                          style="left: auto; right: 11px;"></span>
                                    <div class="m-dropdown__inner">
                                        <div class="m-dropdown__body">
                                            <div class="m-dropdown__content">
                                                <ul class="m-nav">
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void()" class="m-nav__link btn_report btnView"
                                                           data-toggle="modal" data-id="overtime" data-target="#generate-report-modal">
                                                            <i class="m-nav__link-icon la la-search"></i>
                                                            <span class="m-nav__link-text">
																Filter
															</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item" id="export_ot">
                                                        <a href="#" class="m-nav__link btnView">
                                                            <i class="m-nav__link-icon la la-file-excel-o"></i>
                                                            <span class="m-nav__link-text">
																Export
															</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="#" class="m-nav__link btnView" data-toggle="modal" data-target="#overtime-modal" >
                                                            <i class="m-nav__link-icon la la-th-list"></i>
                                                            <span class="m-nav__link-text">
																Report
															</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div id="overtime-graph" style="min-height: 300px;">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
            <div class="m-portlet m-portlet--head-sm m-portlet--bordered-semi">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title" id="vue_head_data_contrib">
                            <span class="m-portlet__head-icon">₱</span>
                            <h3 class="m-portlet__head-text">
                                CONTRIBUTION
                                <small>{{filters.date}}</small>
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                                data-dropdown-toggle="hover" aria-expanded="true">
                                <a href="#"
                                   class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl">
                                    <i class="la la-ellipsis-v m--font-brand"></i>
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"
                                          style="left: auto; right: 11px;"></span>
                                    <div class="m-dropdown__inner">
                                        <div class="m-dropdown__body">
                                            <div class="m-dropdown__content">
                                                <ul class="m-nav">
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void()" class="m-nav__link btn_report btnView"
                                                           data-toggle="modal" data-id="contribution" data-target="#generate-report-modal">
                                                            <i class="m-nav__link-icon la la-search"></i>
                                                            <span class="m-nav__link-text">
																Filter
															</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item" id="export_contrib">
                                                        <a href="#" class="m-nav__link btnView">
                                                            <i class="m-nav__link-icon la la-file-excel-o"></i>
                                                            <span class="m-nav__link-text">
																Export
															</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="#" class="m-nav__link btnView" data-toggle="modal" data-target="#contribution-modal" >
                                                            <i class="m-nav__link-icon la la-th-list"></i>
                                                            <span class="m-nav__link-text">
																Report
															</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div id="contribution-graph" style="min-height: 300px;">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
            <div class="m-portlet m-portlet--head-sm m-portlet--bordered-semi">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title" id="vue_head_data_allowance">
                            <h3 class="m-portlet__head-text m-align-center">
                                ALLOWANCES
                                <small class='date_range_filter'>{{filters.date}}</small>
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                                data-dropdown-toggle="hover" aria-expanded="true">
                                <a href="#"
                                   class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl">
                                    <i class="la la-ellipsis-v m--font-brand"></i>
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"
                                          style="left: auto; right: 11px;"></span>
                                    <div class="m-dropdown__inner">
                                        <div class="m-dropdown__body">
                                            <div class="m-dropdown__content">
                                                <ul class="m-nav">
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void()" class="m-nav__link btn_report btnView"
                                                           data-toggle="modal" data-id="allowances" data-target="#generate-report-modal">
                                                            <i class="m-nav__link-icon la la-search"></i>
                                                            <span class="m-nav__link-text">
																Filter
															</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item" id="export_allowance">
                                                        <a href="#" class="m-nav__link btnView">
                                                            <i class="m-nav__link-icon la la-file-excel-o"></i>
                                                            <span class="m-nav__link-text">
																Export
															</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="#" class="m-nav__link btnView" data-toggle="modal" data-target="#allowance-modal" >
                                                            <i class="m-nav__link-icon la la-th-list"></i>
                                                            <span class="m-nav__link-text">
                                                                Report
															</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div id="allowance-graph" style="min-height: 300px;">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
            <div class="m-portlet m-portlet--head-sm m-portlet--bordered-semi">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title" id="vue_head_data_tax">
                            <h3 class="m-portlet__head-text m-align-center">
                                TAX
                                <small class='date_range_filter'>{{filters.date}}</small>
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                                data-dropdown-toggle="hover" aria-expanded="true">
                                <a href="#"
                                   class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl">
                                    <i class="la la-ellipsis-v m--font-brand"></i>
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"
                                          style="left: auto; right: 11px;"></span>
                                    <div class="m-dropdown__inner">
                                        <div class="m-dropdown__body">
                                            <div class="m-dropdown__content">
                                                <ul class="m-nav">
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void()" class="m-nav__link btn_report btnView"
                                                           data-toggle="modal" data-id="tax" data-target="#generate-report-modal">
                                                            <i class="m-nav__link-icon la la-search"></i>
                                                            <span class="m-nav__link-text">
																Filter
															</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item" id="export_tax">
                                                        <a href="#" class="m-nav__link btnView">
                                                            <i class="m-nav__link-icon la la-file-excel-o"></i>
                                                            <span class="m-nav__link-text">
																Export
															</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="#" class="m-nav__link btnView" data-toggle="modal" data-target="#tax-modal" >
                                                            <i class="m-nav__link-icon la la-th-list"></i>
                                                            <span class="m-nav__link-text">
                                                                Report
															</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body mb-1">
                    <div id="tax-graph" style="min-height: 300px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-xl-7 col-lg-12 col-md-12 col-sm-12">
            <div class="m-portlet m-portlet--head-sm m-portlet--bordered-semi">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text m-align-center">
                                <i class="flaticon-graphic-2"></i>
                                LOAN PAYMENTS
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                                data-dropdown-toggle="hover" aria-expanded="true">
                                <a href="#"
                                   class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl">
                                    <i class="la la-ellipsis-v m--font-brand"></i>
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"
                                          style="left: auto; right: 11px;"></span>
                                    <div class="m-dropdown__inner">
                                        <div class="m-dropdown__body">
                                            <div class="m-dropdown__content">
                                                <ul class="m-nav">
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void()" class="m-nav__link btnView"
                                                           data-toggle="modal" data-id="loans" data-target="#generate-report-modal-loans">
                                                            <i class="m-nav__link-icon la la-search"></i>
                                                            <span class="m-nav__link-text">
																Filter
															</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item" id="export_loans">
                                                        <a href="#" class="m-nav__link btnView">
                                                            <i class="m-nav__link-icon la la-th-list"></i>
                                                            <span class="m-nav__link-text">
                                                                Report
															</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                   <div id="chartdivLoans" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-5 col-lg-12 col-md-12 col-sm-12">
            <div class="m-portlet m-portlet--head-sm m-portlet--bordered-semi">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text m-align-center">
                                <i class="flaticon-line-graph"></i>
                                SALARY HIKE
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div id="chartdivAbsent" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade" tabindex="-1" role="dialog" id="expand_portlet">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" id="generate-journal_content">
            <div class="modal-header">
                <h5 class="modal-title">Filter By</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div  class="modal-body">
                test
            </div>
            <div class="modal-footer">
                test footer
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="generate-report-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="generate-journal_content">
            <div class="modal-header">
                <h5 class="modal-title">Filter By</h5>
                <button type="button" class="close" data-dismiss="modal" @click='resetFields' aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- payroll summary filters -->
            <form id="frm-journal-report" method="post">
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
                        <div id="filter-by-date-range" class="col-xl-8 col-lg-8 col-md-8 col-sm-12" v-if="show_by_date === true">
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
                        <div id="filter-by-month-year" class="col-xl-12 col-lg-12 col-md-12 col-sm-12 m-animate-fade-in" v-if="month_picker === true">
                            <div class="row">
                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <label for="" class="required m--font-bolder">MONTH</label>
                                    <select class="form-control" id="filter_month" name="filter_month" data-validation="required">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <label for="" class="required m--font-bolder">YEAR</label>
                                    <select class="form-control" id="filter_year" name="filter_year" data-validation="required"></select>
                                </div>
                            </div>
                        </div>
                        <div id="filter-by-year" class="col-xl-6 col-lg-6 col-md-6 col-sm-12 m-animate-fade-in" v-if="year_picker === true">
                            <div class="row">
                                <div class="form-group col-xl-12 col-lg-12 col-md-6 col-sm-12">
                                    <label for="" class="required m--font-bolder">YEAR</label>
                                    <select class="form-control" id="filter_year" name="filter_year" data-validation="required"></select>
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
                </div>
                <div class="modal-footer">
                    <button @click='generateData' class="btn btn-info m-btn m-btn--icon btnDelete">
                            <span><i class="fa fa-gears pr-2"></i>GENERATE</span>
                    </button>
                    <button @click='resetFields' class="btn btn-danger m-btn m-btn--icon btnView">
                            <span><i class="fa fa-refresh pr-2"></i>RESET FILTER</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="generate-report-modal-loans">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="generate-journal_content">
            <div class="modal-header">
                <h5 class="modal-title">Filter By</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- payroll summary filters -->
            <form id="frm-loans-report" method="post">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                     <div class="row">
                        <div id="filter-by-year" class="col-xl-6 col-lg-6 col-md-6 col-sm-12 m-animate-fade-in">
                            <div class="row">
                                <div class="form-group col-xl-12 col-lg-12 col-md-6 col-sm-12">
                                    <label for="" class="required m--font-bolder">YEAR</label>
                                    <select class="form-control" id="filter_year_loans" name="filter_year_loans" data-validation="required"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="company" class="m--font-bolder">Company <small>( Optional )</small></label>
                                <select id="company_loans" class="form-control" name="company"><option></option></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="generateDataLoans" class="btn btn-info m-btn m-btn--icon btnView">
                            <span><i class="fa fa-gears pr-2"></i>GENERATE</span>
                    </button>
                    <button class="btn btn-danger m-btn m-btn--icon btnView">
                            <span><i class="fa fa-refresh pr-2"></i>RESET FILTER</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="overtime-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="generate-journal_content">
            <div class="modal-body">
                <table id="export_overtime" class="cell-border compact stripe table table-condensed table-sm" width="100%" style='font-size: 11px;'>
                    <thead>
                        <th>Company</th>
                        <th>OVERTIME(HR)</th>
                        <th class="text-right">WAGE</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger btn-sm m-btn m-btn--icon btnView" data-dismiss="modal">
                    <span><i class="fa fa-times"></i>&nbsp;Close</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="contribution-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="generate-journal_content">
            <div class="modal-body">
                <table id="export_contributions" class="table table-condensed table-sm" width="100%" style='font-size: 11px;'>
                    <thead>
                        <th>Company</th>
                        <th>SSS</th>
                        <th>PHIC</th>
                        <th>HDMF</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger btn-sm m-btn m-btn--icon btnView" data-dismiss="modal">
                    <span><i class="fa fa-times"></i>&nbsp;Close</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="allowance-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="generate-journal_content">
            <div class="modal-body">
                <table id="export_allowances" class="table table-condensed table-sm" width="100%" style='font-size: 11px;'>
                    <thead>
                        <th>Company</th>
                        <th>Amount</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger btn-sm m-btn m-btn--icon btnView" data-dismiss="modal">
                    <span><i class="fa fa-times"></i>&nbsp;Close</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="tax-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="generate-journal_content">
            <div class="modal-body">
                <table id="export_taxes" class="table table-condensed table-sm" width="100%" style='font-size: 11px;'>
                    <thead>
                        <th>Company</th>
                        <th>Amount</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger btn-sm m-btn m-btn--icon btnView" data-dismiss="modal">
                    <span><i class="fa fa-times"></i>&nbsp;Close</span>
                </button>
            </div>
        </div>
    </div>
</div>