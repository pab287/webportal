<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Leave Credits
                        <small>
                            Reports
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
                                    data-toggle="m-tooltip" data-original-title="Generate Leave Credits"
                                    data-skin="dark" data-delay='{"show": 500}'>
                                <span><i class="fa fa-gears pr-1"></i> Generate Report</span>
                            </button>
                        </span>
                    </li>
                </ul>

            </div>
        </div>
        <div class="m-portlet__body">
            <!--::dt begin::-->
            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                <table class="table table-striped table-bordered" id="tbl-leave_credits" width="100%" style="font-family: roboto;">
                    <thead>
                    <tr>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Date Employed</th>
                        <th>SIL</th>
                        <th>Daily Rate</th>
                        <th>Allowance Rate</th>
                        <th>13th Month</th>
                        <th>Leave Rate</th>
                        <th>Leave Credits</th>
                        <th>CA / Charges</th>
                        <th>Net Leave Credits</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
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

    <div class="modal fade" tabindex="-1" role="dialog" id="generate-report-modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="generate-leave_credits_content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="frm-leave_credits-report" method="post" action="<?php echo site_url("payroll/reports/generate_leave_credits_report"); ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="modal-body" id="leave_credits_content">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="m--font-bolder">FILTER BY</label>
                                    <div class="m-checkbox-inline">
                                        <label class="m-checkbox">
                                            <input type="radio" id="all_filter" name="filter_by" data-validation="required" value="1" class="valid" v-model="filter_by" checked />
                                            ALL<span></span>
                                        </label>
                                        <label class="m-checkbox">
                                            <input type="radio" id="month_filter" name="filter_by" value="2" data-validation="required" class="valid" v-model="filter_by" />
                                            MONTH<span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                <label class="m--font-bolder">Employee Status</label>
                                <div class="m-radio-inline">
                                    <label class="m-radio">
                                        <input type="radio" id="emp-status" name="emp_status" value="All" checked>
                                        All
                                        <span></span>
                                    </label>
                                    <label class="m-radio">
                                        <input type="radio" id="emp-status" name="emp_status" value="Active">
                                        Active
                                        <span></span>
                                    </label>
                                    <label class="m-radio">
                                        <input type="radio" id="emp-status" name="emp_status" value="Inactive">
                                        Inactive
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="company" class="m--font-bolder">Company *</label>
                                    <select id="company" class="form-control" name="company" data-validation="required"><option></option></select>
                                </div>
                            </div>
                            <div class="col-4 col-xl-4 col-lg-4 col-md-4 col-sm-12" v-if="filter_by == '2'">
                                <div id="month_filter_option" class="form-group m--hide">
                                    <label for="month" class="m--font-bolder">Month *</label>
                                    <select id="month" class="form-control" name="month" data-validation="required"><option></option></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
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
                            <div class="col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
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
                        <button type="button" onclick="resetFields(this)" class="btn btn-danger m-btn m-btn--icon btnAdvance_search">
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
                    <h5 class="modal-title">Leave Credits Signatories</h5>
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
                    <h5 class="modal-title">Reset - Leave Credits Signatories</h5>
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

</div>
