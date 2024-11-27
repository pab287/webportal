<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        SSS Contribution
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
                                    data-toggle="m-tooltip" data-original-title="Generate Payroll SSS Contribution"
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
                    <table class="table table-striped table-bordered" id="tbl-sss_remittances" width="100%" style="font-family: roboto;">
                        <thead>
                        <tr>
                            <th>SSS Number</th>
                            <th>Employee #</th>
                            <th>Employee Name</th>
                            <th>Basic Pay</th>
                            <th>Gross Pay</th>
                            <th>EE-SSS</th>
                            <th>ER-SSS</th>
                            <th>ER-EC</th>
                            <th>EE-PROV</th>
                            <th>ER-PROV</th>
                            <th>PROV SSS TOTAL</th>
                            <th>ER SSS Total</th>
                            <th>SSS Total Comp</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th class="text-right" colspan="3"><span class="m--font-boldest">GRAND TOTAL</span></th>
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
    <div class="row">
        <div id="portlet--signatories" class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <template v-if="count > 0">
            <div class="m-portlet m-portlet--bordered m-portlet--rounded m-portlet--unair m-portlet--head-sm mb-0 mt-2">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Printable Signatories
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item">
                                <a href="javascript:void(0);" class="btnEdit m-portlet__nav-link m-portlet__nav-link--icon" @click="openModalSignatory()">
                                    <i class="la la-pencil"></i>
                                </a>
                            </li>
                            <template v-if="row.allow_reset === true">
                                <li class="m-portlet__nav-item">
                                    <a href="javascript:void(0);" class="btnEdit m-portlet__nav-link m-portlet__nav-link--icon" @click="resetModalSignatory()">
                                        <i class="la la-refresh"></i>
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <template v-if="count > 0">
                        <div class="row justify-content-center">
                            <template v-for="(item, index) in row.meta_field">
                                <template v-if="item.is_active === true">
                                    <div class="col-2 col-md-2 col-lg-2 col-sm-12">
                                        <div class="form-group m-form__group text-center">
                                            <label class="m--font-boldest">{{item.label}}</label>
                                            <p class="m--font-bolder mb-0">{{item.value}}</p>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>
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
            </div>
            </template>
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

            <form id="frm-remittance-report" method="post" action="<?php echo site_url("payroll/reports/generate_sss_remittance_report"); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label class="m--font-bolder">FILTER BY</label>
                                <div class="m-checkbox-inline">
                                    <!-- label class="m-checkbox">
                                        <input type="radio"
                                            name="group"
                                            data-validation="required"
                                            value="1" class="valid"
                                            @click="tempShowByDates(1)"
                                            checked>
                                        PAY DATE<span></span>
                                    </label -->
                                    <label class="m-checkbox">
                                        <input type="radio"
                                            name="group"
                                            data-validation="required"
                                            value="2" class="valid"
                                            @click="tempShowByDates(2)" checked>
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
                        <!-- div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 m-animate-fade-in" v-if="show_by_date === true">
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
                        </div -->
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
                                        <select class="form-control" name="filter_year" data-validation="required">
                                            <option></option>
                                        </select>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                                <select class="form-control" id="payroll_group" multiple="multiple"></select>
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

<div class="modal fade" tabindex="-1" role="dialog" id="modal-ps--signatory">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="signatory--content">
            <div class="modal-header">
                <h5 class="modal-title">SSS Contribution Signatories</h5>
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
                            <label class="col-3 col-form-label">{{item.label}}</label>
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
                        <button type="submit" class="btn btn-primary btnSave">Update</button>
                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
                    </div>
                </template>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-ps--reset-signatory">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="reset-signatory--content">
            <div class="modal-header">
                <h5 class="modal-title">Reset - SSS Contribution Signatories</h5>
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
                            <label class="col-4 col-form-label">{{item.label}}</label>
                            <label class="col-8 col-form-label m--font-bolder">{{item.value}}</label>
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

<div class="modal fade" tabindex="-1" role="dialog" id="modal-ps--data">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" id="ps-data--content">
            <div class="modal-header">
                <h5 class="modal-title">Payroll Sheet Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h3 class="m--margin-bottom-30">{{record.name}}</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Coverage Date<th>
                            <th>Pay Date<th>
                            <th class="text-center">Sequence<th>
                            <th>EE-SSS<th>
                            <th>EE-PROV<th>
                            <th>SSS ADJ<th>
                            <th>PROV ADJ<th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="count > 0" v-for="(item, index) in record.rows">
                            <td>{{formattedDate(item.date_start)}} - {{formattedDate(item.date_end)}}<td>
                            <td>{{formattedDate(item.pay_date)}}<td>
                            <td class="text-center">{{item.payroll_seq}}<td>
                            <td>{{item.sss}}<td>
                            <td>{{item.sss_prov}}<td>
                            <td>{{formattedAdjustments(item.adjustment, 'sss')}}<td>
                            <td>{{formattedAdjustments(item.adjustment, 'sss_prov')}}<td>
                        </tr>
                        <tr v-else>
                            <td colspan="3">No record/s found!<td>
                        </tr>
                    </tbody>
                </table>
                <table class="table mt-5">
                    <thead>
                        <tr>
                            <th>Contribution Basis</th>
                            <th>Amount</th>
                            <th class="text-center">SSS Table Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="m--font-bolder">{{record.contribution_basis}}</span></td>
                            <td><span class="m--font-bolder">{{formattedAmount(record.amount_basis)}}</span></td>
                            <td class="p-0">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Range</th>
                                            <th>EE</th>
                                            <th>ER</th>
                                            <th>EE-EC</th>
                                            <th>ER-EC </th>
                                            <th>EE-PROV</th>
                                            <th>ER-PROV</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="m--font-bolder">{{formattedAmount(sss_table.from)}} - {{formattedAmount(sss_table.to)}}</span></td>
                                            <td>{{formattedAmount(sss_table.ee)}}</td>
                                            <td>{{formattedAmount(sss_table.er)}}</td>
                                            <td>{{formattedAmount(sss_table.ec_ee)}}</td>
                                            <td>{{formattedAmount(sss_table.ec_er)}}</td>
                                            <td>{{formattedAmount(sss_table.prov_ee)}}</td>
                                            <td>{{formattedAmount(sss_table.prov_er)}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>