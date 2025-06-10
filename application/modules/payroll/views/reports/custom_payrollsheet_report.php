<div class="m-content">
    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
            <form id="frm-filter-payroll-neypay_report" class="m-form" action="<?php echo site_url("payroll/reports/generate_custom_posted_netpay_records"); ?>" method="post">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="m-portlet">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-filter"></i>
                            </span>
                                <h3 class="m-portlet__head-text">Filter</h3>
                            </div>
                        </div>
                    </div>
                    <div id="tempFilter" class="m-portlet__body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label class="m--font-bolder">FILTER BY</label>
                                    <div class="m-checkbox-inline">
                                        <label class="m-checkbox">
                                            <input type="radio" id="date_range_period" name="group" data-validation="required" class="valid" value="1" checked>
                                            PAY DATE<span></span>
                                        </label>
                                        <label class="m-checkbox">
                                            <input type="radio" id="monthly_period" name="group" value="2" data-validation="required" class="valid">
                                            MONTH<span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="form-group m-form-group col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <label>COMPANY</label>
                                <select name="company" id="company" class="form-control">
                                    <option></option>
                                </select>
                            </div>
                            <div class="form-group m-form-group col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <label for="payout_schedule">PAYOUT CLASSIFICATION <small class="m-form__help p-0">( Optional )</small></label>
                                <select class="form-control" name="payroll_sched" id="payout_schedule">
                                    <option></option>
                                </select>
                            </div>
                        </div>

                        <!-- added for payroll group -->
                        <div class="row mt-2">
                            <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <label class="m--font-bolder" for="payroll_group">PAYROLL GROUP <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                                </label>
                                <select class="form-control" id="payroll_group" multiple="multiple"></select>
                            </div>
                            <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <label class="m--font-bolder" for="employees">EMPLOYEE/S <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                                </label>
                                <select name="employees[]" id="employees" class="form-control"
                                multiple="multiple"></select>
                            </div>
                        </div>
                        <!-- added for payroll group -->

                        <div class="paydate-filter" id="paydate-filter">
                            <div class="form-group m-form__group">
                                <label class="required">PAY DATE</label>
                                <div class="input-group date" id="pay-date">
                                    <input type="text" class="form-control m-input"
                                        data-validation="required"
                                        name="pay_date" autocomplete="off"
                                        placeholder="MMM.DD, YYYY">
                                    <span class="input-group-addon">
                                        <i class="la la-calendar-check-o"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group m-form__group pt-0" id="filter-by-date-range">
                                <label class="required" for="date-range">SELECT DATE RANGE</label>
                                <div class="input-group" id="date-picker">
                                    <input type="text" class="form-control m-input" readonly=""
                                        placeholder="MMM DD, YYYY - MMM DD, YYYY"
                                        id="date-range"
                                        name="date_range" data-validation="required" />
                                    <span class="input-group-addon">
                                        <i class="la la-calendar-check-o"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group m-form-group">
                                <label for="">PAYROLL TYPE</label>
                                <div class="m-checkbox-inline">
                                    <label class="m-checkbox">
                                        <input type="checkbox" name="is_bonus" value="1" />
                                        13TH MONTH BONUS
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="filter-by-month-year" class="row mt-2 m--hide">
                                <div class="form-group m-form-group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <label class="required" for="filter_month">MONTH</label>
                                    <select class="form-control" name="filter_month" id="filter_month" data-validation="required">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group m-form-group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <label class="required" for="filter_year">YEAR</label>
                                    <select class="form-control" name="filter_year" id="filter_year" data-validation="required">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group m-form-group col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                    <label for="">PAYROLL TYPE</label>
                                    <div class="m-checkbox-inline">
                                        <label class="m-checkbox">
                                            <input type="checkbox" name="is_bonus" value="1" />
                                            13TH MONTH BONUS
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                        </div>
                        <div class="text-right mt-4">
                            <button type="submit"
                                class="m-btn btn btn-success btnAdvance_search btn-submit">
                                GO
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                        <span class="m-portlet__head-icon">
                            <i class="flaticon-clipboard"></i>
                        </span>
                            <h3 class="m-portlet__head-text">Custom Payroll sheet Report</h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul id="tempActions" class="m-portlet__nav">
                            <template v-if="set_printable === true">
                                <li class="m-portlet__nav-item">
                                    <button type="button" class="btn btn-success m-btn text-white btnPrint" @click="exportNetpayReport"><i class="la la-file-excel-o"></i> EXPORT REPORT</button>
                                </li>
                                <li class="m-portlet__nav-item">
                                    <button type="button" class="btn btn-warning m-btn text-white btnPrint" @click="printNetpayReport"><i class="fa fa-print"></i> PRINT REPORT</button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <!--::dt begin::-->

                    <div class="row justify-content-end">
                        <div class="col-md-3 text-right">
                            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                <div class="btn-group" role="group">
                                    <span data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <button type="button"
                                                data-toggle="m-tooltip" data-original-title="Show/Hide Columns"
                                                data-skin="dark"
                                                data-delay='{"show": 300}'
                                                class="m-btn btn btn-success dropdown-toggle btnAdvance_search">
                                            <i class="fa fa-th"></i>
                                        </button>
                                    </span>
                                    <ul class="dropdown-menu dropdown-menu-right" id="column-options" aria-labelledby="btnGroupDrop1" x-placement="bottom-start">
                                        <li class="dropdown-item pt-1 pb-1">
                                            <label class="m-checkbox mb-0" onclick="event.stopPropagation()">
                                                <input type="checkbox" checked="checked" oninput="showOrHideColumn(2, this)">
                                                COMPANY
                                                <span></span>
                                            </label>
                                        </li>
                                        <li class="dropdown-item pt-1 pb-1">
                                            <label class="m-checkbox mb-0" onclick="event.stopPropagation()">
                                                <input type="checkbox" checked="checked" oninput="showOrHideColumn(3, this)">
                                                DEPARTMENT
                                                <span></span>
                                            </label>
                                        </li>
                                        <li class="dropdown-item pt-1 pb-1">
                                            <label class="m-checkbox mb-0" onclick="event.stopPropagation()">
                                                <input type="checkbox" checked="checked" oninput="showOrHideColumn(4, this)">
                                                POSITION
                                                <span></span>
                                            </label>
                                        </li>
                                        <li class="dropdown-item pt-1 pb-1">
                                            <label class="m-checkbox mb-0" onclick="event.stopPropagation()">
                                                <input type="checkbox" checked="checked" oninput="showOrHideColumn(5, this)">
                                                WORK STATUS
                                                <span></span>
                                            </label>
                                        </li>
                                        <li class="dropdown-item pt-1 pb-1">
                                            <label class="m-checkbox mb-0" onclick="event.stopPropagation()">
                                                <input type="checkbox" oninput="showOrHideColumn(6, this)">
                                                RATE
                                                <span></span>
                                            </label>
                                        </li>
                                        <li class="dropdown-item pt-1 pb-1">
                                            <label class="m-checkbox mb-0" onclick="event.stopPropagation()">
                                                <input type="checkbox" oninput="showOrHideColumn(7, this)">
                                                DAYS
                                                <span></span>
                                            </label>
                                        </li>
                                        <li class="dropdown-item pt-1 pb-1">
                                            <label class="m-checkbox mb-0" onclick="event.stopPropagation()">
                                                <input type="checkbox" oninput="showOrHideColumn(8, this)">
                                                BASIC PAY
                                                <span></span>
                                            </label>
                                        </li>
                                        <li class="dropdown-item pt-1 pb-1">
                                            <label class="m-checkbox mb-0" onclick="event.stopPropagation()">
                                                <input type="checkbox" oninput="showOrHideColumn(9, this)">
                                                UT
                                                <span></span>
                                            </label>
                                        </li>
                                        <li class="dropdown-item pt-1 pb-1">
                                            <label class="m-checkbox mb-0" onclick="event.stopPropagation()">
                                                <input type="checkbox" checked="checked" oninput="showOrHideColumn(10, this)">
                                                OT
                                                <span></span>
                                            </label>
                                        </li>
                                        <li class="dropdown-item pt-1 pb-1">
                                            <label class="m-checkbox mb-0" onclick="event.stopPropagation()">
                                                <input type="checkbox" checked="checked" oninput="showOrHideColumn(11, this)">
                                                OT.ND
                                                <span></span>
                                            </label>
                                        </li>
                                        <li class="dropdown-item pt-1 pb-1">
                                            <label class="m-checkbox mb-0" onclick="event.stopPropagation()">
                                                <input type="checkbox" oninput="showOrHideColumn(12, this)">
                                                HOL
                                                <span></span>
                                            </label>
                                        </li>
                                        <li class="dropdown-item pt-1 pb-1">
                                            <label class="m-checkbox mb-0" onclick="event.stopPropagation()">
                                                <input type="checkbox" checked="checked" oninput="showOrHideColumn(13, this)">
                                                ALLOWANCE
                                                <span></span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                        <table class="table table-striped table-bordered" id="tbl-neypay_report" width="100%" style="font-family: roboto;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Company</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Work Status</th>
                                    <th>Rate</th>
                                    <th>Days</th>
                                    <th>Basic Pay</th>
                                    <th>UT</th>
                                    <th>OT</th>
                                    <th>OT.ND</th>
                                    <th>HOL</th>
                                    <th>Allowance</th>
                                    <th class="text-right">Gross Pay</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="14" style="text-align:right; font-weight: 600;">GRAND TOTAL</th>
                                    <th class="text-right" style="font-weight: 600;">0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <!--::dt end::-->
                </div>
            </div>
        </div>
    </div>
</div>