<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Remittances
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
                            <button class="btn btn-default m-btn m-btn--icon"
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
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table class="table table-striped table-bordered" id="tbl-remittances" width="100%" style="font-family: roboto;">
                        <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Company</th>
                            <th>Reg. Pay</th>
                            <th>Gross Pay</th>
                            <th>ADJ 1</th>
                            <th>ADJ 2</th>
                            <th>ADJ 3</th>
                            <th>ADJ 4</th>
                            <th>ADJ 5</th>
                            <th>SSS</th>
                            <th>HDMF</th>
                            <th>Philhealth</th>
                            <th>Taxable</th>
                            <th>Tax</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align:right">GRAND TOTAL</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <!--::dt end::-->
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="generate-report-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="generate-remittance_content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="frm-remittance-report" method="post" action="<?php echo site_url("payroll/reports/generate_remittance_report"); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="visible_fields" v-model="visible_fields" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label class="m--font-bolder">FILTER BY</label>
                                <div class="m-checkbox-inline">
                                    <label class="m-checkbox">
                                        <input type="radio"
                                            name="group"
                                            data-validation="required"
                                            value="1" class="valid"
                                            @click="tempShowByDates(1)"
                                            checked>
                                        PAY DATE<span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="radio"
                                            name="group"
                                            data-validation="required"
                                            value="2" class="valid"
                                            @click="tempShowByDates(2)">
                                        MONTH<span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="radio"
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
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 m-animate-fade-in" v-if="show_by_date === true">
                            <div class="form-group">
                                <label class="m--font-bolder">Range Filter</label>
                                <div class="m-checkbox-list">
                                    <label class="m-checkbox">
                                        <input id="filter_date_range" type="checkbox"
                                            name="filter_by_date_range"
                                            class="valid" @click="tempShowPicker()">
                                            BY DATE RANGE<span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="filter-by-date-range" class="col-xl-6 col-lg-6 col-md-6 col-sm-12" v-if="show_picker === true">
                            <div class="form-group">
                                <label for="date-range" class="m--font-bolder">SELECT DATE RANGE</label>
                                <div class="input-group" id="date-picker">
                                    <input type="text" class="form-control m-input"
                                        placeholder="MMM DD, YYYY - MMM DD, YYYY" id="date-range"
                                        name="date_range" data-validation="required" readonly>
                                    <span class="input-group-addon">
                                        <i class="la la-calendar-check-o"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div id="filter-by-month-year" class="col-xl-6 col-lg-6 col-md-6 col-sm-12 m-animate-fade-in" v-if="show_picker === false">
                            <div class="row">
                                <template v-if="year_picker === true">
                                    <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                        <label for="" class="required m--font-bolder">YEAR</label>
                                        <select class="form-control" name="filter_year" data-validation="required"></select>
                                    </div>
                                </template>
                                <template v-else>
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
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="company" class="m--font-bolder">Company <small>( Optional )</small></label>
                                <select id="company" class="form-control" name="company[]" multiple><option></option></select>
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
                                    <label class="m-checkbox">
                                        <input type="checkbox" value="adjustment" class="temp-visible_fields" checked @click="toggleCheckbox($event)">Adjustment<span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="checkbox" value="sss" class="temp-visible_fields" checked @click="toggleCheckbox($event)">SSS<span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="checkbox" value="hdmf" class="temp-visible_fields" checked @click="toggleCheckbox($event)">HDMF<span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="checkbox" value="ph" class="temp-visible_fields" checked @click="toggleCheckbox($event)">PHILHEALTH<span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="checkbox" value="tax" class="temp-visible_fields" checked @click="toggleCheckbox($event)">TAX<span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="checkbox" value="taxable" class="temp-visible_fields" checked @click="toggleCheckbox($event)">TAXABLE<span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="checkbox" value="total" class="temp-visible_fields" checked @click="toggleCheckbox($event)">TOTAL<span></span>
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