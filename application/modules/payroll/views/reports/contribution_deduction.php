<div class="m-content">
    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
            <form id="frm-filter-payroll-contribution" class="m-form" method="post">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="m-portlet m-portlet--head-sm mb-2" data-portlet="true" id="m_portlet_tools-contribution_deduction">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-filter"></i>
                            </span>
                                <h3 class="m-portlet__head-text">Filter Options</h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item">
                                    <a href="javascript:void(0);"  data-portlet-tool="toggle" class="m-portlet__nav-link m-portlet__nav-link--icon">
                                        <i class="la la-angle-down"></i>
                                    </a>
                                </li>
                            </ul>
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
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="paydate-filter" id="paydate-filter">
                                    <div class="row">
                                        <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                            <label class="m--font-bolder" for="">PAY DATE *</label>
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
                                        <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12" id="filter-by-date-range">
                                            <label class="m--font-bolder" for="date-range">SELECT DATE RANGE *</label>
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
                                    </div>
                                </div>
                                <div id="filter-by-month-year" class="row m--hide">
                                    <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                        <label class="m--font-bolder required" for="">MONTH</label>
                                        <select class="form-control" name="filter_month" id="filter_month" data-validation="required">
                                            <option></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                        <label class="m--font-bolder required" for="">YEAR</label>
                                        <select class="form-control" name="filter_year" id="filter_year" data-validation="required">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label class="required m--font-bolder">COMPANY</label>
                                    <select name="company" id="company" class="form-control" data-validation="required">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label class="m--font-bolder">
                                        PAYOUT CLASSIFICATION
                                        <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                                    </label>
                                    <select name="payroll_sched" id="payout_schedule" class="form-control">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label class="m--font-bolder">
                                        PAYOUT MODE
                                        <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                                    </label>
                                    <select name="payout_mode" id="payout_mode" class="form-control">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="station" class="m--font-bolder">
                                        Project #
                                        <small class="m-form__help p-0">( Optional )</small>
                                    </label>
                                    <select id="station" class="form-control" name="station"><option></option></select>
                                </div>
                            </div>
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
                    </div>
                    <div class="m-portlet__foot text-right">
                        <button type="button" 
                            class="btn btn-warning m-btn btnAdvance_search m-btn--sm mr-1 text-white" 
                            onclick="resetFilter(this)">
                            <span>
                                <i class="fa fa-refresh"></i>
                                <span>Reset Filter</span>
                            </span>
                        </button>
                        <button type="submit"
                            class="m-btn btn btn-success btnAdvance_search btn-submit">
                            GO
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col">
            <div id="pay_date_data" class="m--hide">
                <?php $this->load->view("payroll/reports/printable/deduction_print_content_payroll_sheet"); ?>
            </div>
            <div id="monthly_data" class="m--hide">
                <?php $this->load->view("payroll/reports/printable/deduction_print_content_all"); ?>
            </div>
            <div id="portlet--signatories">
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
                                        <div class="col-4 col-md-4 col-lg-4 col-sm-12">
                                            <div class="form-group m-form__group text-center">
                                                <label for="" class="m--font-boldest">{{item.label}}</label>
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
    
    <div class="modal fade" tabindex="-1" id="modal-ps--signatory">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="signatory--content">
                <div class="modal-header">
                    <h5 class="modal-title">Contribution / Deduction Signatories</h5>
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
                    <h5 class="modal-title">Reset - Contribution / Deduction Signatories</h5>
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