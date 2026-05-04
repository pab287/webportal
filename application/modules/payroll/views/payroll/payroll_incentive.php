<style>
    @media (min-width: 768px) {
        .modal-full {
            width: 100%;
            max-width: 90%;
        }
    }

    .table-cb {
        display: inline;
        padding-left: 16px;
    }

    .table-cb span {
        height: 12px;
        width: 12px;
        display: inline;
    }

    .table-cb span:after {
        width: 3px;
        height: 8px;
    }

    .no-sort:before, .no-sort:after {
        display: none !important;
    }

    .custom-adjustment-total-divider {
        border-bottom: 1px dotted grey;
        display: inline-block;
        width: auto;
        padding-bottom: 2px;
    }

    .m-portlet .input-group .form-control{ z-index: auto; }
    .modal .m-dropdown { position: absolute; }

    @media screen and (min-width: 1200px) {
        #view-timesheet-modal .modal-dialog {
            max-width: 60%;
        }
    }

</style>
<div class="m-content">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="m-portlet m-portlet--head-sm mb-2" data-portlet="true" id="m_portlet_tools-payroll_sheet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Action Tools
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item">
                                <a href="javascript:void(0);"  data-portlet-tool="toggle" class="m-portlet__nav-link m-portlet__nav-link--icon">
                                    <i class="la la-angle-down"></i>
                                </a>
                            </li>
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover">
                                <a href="javascript:void(0);" class="m-portlet__nav-link m-portlet__nav-link--icon m-dropdown__toggle">
                                    <i class="la la-ellipsis-v"></i>
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                    <div class="m-dropdown__inner">
                                        <div class="m-dropdown__body">
                                            <div class="m-dropdown__content">
                                                <ul class="m-nav">
                                                    <li class="m-nav__section m-nav__section--first">
                                                        <span class="m-nav__section-text">
                                                            Quick Actions
                                                        </span>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0);" class="m-nav__link btnAdvance_search" onclick="tempRegeneratePayroll()">
                                                            <i class="m-nav__link-icon la la-search pr-1"></i>
                                                            <span class="m-nav__link-text">
                                                                Generate
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
                <div class="m-form m-form--label-align-right m--margin-bottom-30">
                        <div class="align-items-center">
                            <form action="" class="m-form has-validation-callback" id="frm-filter">
                                <div id="temp-selector">
                                    <div class="row mb-3">
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mt-2 pr-0">
                                            <div class="form-group m-form__group">
                                                <label class="required mb-1" style="font-weight: 600;">Incentive Type</label>
                                                <select id="incentive_type"
                                                class="form-control" 
                                                data-validation="required">
                                                <option></option>
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mt-2 pr-0">
                                            <div class="form-group m-form__group ml-3">
                                                <label class="mb-1" style="font-weight: 600;">Coverage Date</label>
                                                <template v-if="has_coverage_date === true">
                                                    <p class="mb-0 mt-2 m--font-boldest m--font-primary">{{row.dt_from}} - {{row.dt_to}}</p>
                                                    <input type="hidden" name="date_range" :value="getDateRange(row.date_from, row.date_to)" />
                                                    <input id="bonus-code" type="hidden" name="bonus_code" v-model="row.name" />
                                                </template>
                                                <template v-else>
                                                    <p>---</p>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 text-left d-flex flex-row">
                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mt-2 pr-0">
                                        <div class="form-group m-form__group">
                                            <label class="required mb-1" style="font-weight: 600;">Company</label>
                                            <select name="company" id="company"
                                                    class="form-control" data-validation="required">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mt-2 pr-0">
                                        <div class="form-group m-form__group">
                                            <label for="" class="required mb-1" style="font-weight: 600;">
                                                Payout Classification
                                            </label>
                                            <select class="form-control" id="payout_schedule"
                                                    data-validation="required" name="payout_schedule">
                                                    <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 pr-0 mt-2">
                                        <div class="form-group m-form__group">
                                            <label class="required mb-1" style="font-weight: 600;">Pay Date</label>
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
                                    </div>
                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mt-2">
                                        <div class="m-form__group form-group row pt-0 pl-3">
                                            <label class="col-8 col-form-label text-left" style="font-weight: 600;">
                                                Active Employees<br>
                                                <span class="m-form__help p-0" style="text-transform: none; font-weight: 600;">Filter Employee Status</span>
                                            </label>
                                            <div class="col-3">
                                                <span class="m-switch m-switch--outline m-switch--sm m-switch--icon m-switch--success">
                                                    <label>
                                                        <input type="checkbox" checked="checked" id="active_employees" />
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-xl-4 col-lg-4 col-md-4 text-right">
                                        <button type="submit"
                                            class="btn btn-info m-btn m-btn--icon btnAdvance_search m--margin-top-25">
                                            <span><i class="fa fa-search"></i><span>GENERATE</span></span>
                                        </button><button type="button" 
                                            class="btn btn-warning btnReset btnAdvance_search pull-right m--margin-top-25 m--margin-left-5 text-white" 
                                            onclick="resetFilter(this)">
                                                <span>
                                                    <i class="fa fa-refresh "></i>
                                                    RESET
                                                </span>
                                        </button> 
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
                                        <div class="form-group m-form__group">
                                            <label for="payroll_group" class="mb-1" style="font-weight: 600;">
                                                PAYROLL GROUP
                                                <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                                            </label>
                                            <select class="form-control" id="payroll_group" multiple="multiple"></select>
                                        </div>
                                    </div>
                                    <div class="col-xl-1 col-lg-1 col-md-1 col-sm-1 ">
                                    
                                        
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-group m-form__group">
                                            <label class="mb-1" style="font-weight: 600;">
                                                Employee 
                                                <span class="m-form__help" style="text-transform: none; font-width: 600;">(Optional)</span>
                                            </label>
                                            <select name="employees[]" id="employees" class="form-control"
                                            multiple="multiple"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="m-portlet m-portlet--head-sm mb-0" data-portlet="true">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Payroll Sheet - Incentive
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul id="ps--notification" class="m-portlet__nav">
                            <li class="m-portlet__nav-item">
                                <a href="javascript:void(0);" data-portlet-tool="fullscreen" class="m-portlet__nav-link m-portlet__nav-link--icon">
                                    <i class="la la-expand"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-bottom-30">
                        <div class="align-items-center">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 text-left d-flex flex-row">
                                    <div class="m-form__group form-group">
                                        <label for="">Show Entries</label>
                                        <div class="m-checkbox-inline">
                                            <label class="m-checkbox">
                                                <input type="radio" class="show_posted_record" name="show_posted" value="_all" checked="checked" />ALL
                                                <span></span>
                                            </label>
                                            <label class="m-checkbox">
                                                <input type="radio" class="show_posted_record" name="show_posted" value="posted">POSTED
                                                <span></span>
                                            </label>
                                            <label class="m-checkbox">
                                                <input type="radio" class="show_posted_record" name="show_posted" value="unposted">UNPOSTED
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 text-right d-flex flex-row align-items-end justify-content-end">
                                    <div class="flex-grow-0 flex-shrink-0 mr-2">
                                        <button type="button" class="btn btn-success btnSave m-btn m-btn--icon"
                                                onclick="confirmPosting()">
                                            <span>
                                                <i class="fa fa-check"></i>
                                                <span class="m--font-boldest">Post</span>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="flex-grow-0 flex-shrink-0 mr-2">
                                        <button type="button" class="btn btn-brand btnPrint m-btn m-btn--icon"
                                                onclick="printPayrollSheet(this)">
                                            <span>
                                                <i class="fa fa-print"></i>
                                                <span class="m--font-boldest">Print</span>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="flex-grow-0 flex-shrink-0">
                                        <button type="button" class="btn btn-brand btnPrint m-btn m-btn--icon"
                                                onclick="exportExcelPayrollSheet(this)">
                                            <span>
                                                <i class="fa fa-download"></i>
                                                <span class="m--font-boldest">Export Excel</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="table-responsive-sm">
                            <table class="table table-bordered" id="table-payroll-sheet"
                                   width="100%">
                                <thead>
                                <tr>
                                    <th class="no-sort align-middle">
                                        <label class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                                            <input type="checkbox" id="cb-select-all"><span></span>
                                        </label>
                                    </th>
                                    <th class="text-center align-middle">#</th>
                                    <th class="text-center align-middle">EMPLOYEE</th>
                                    <!-- th class="text-center align-middle">RATE</th>
                                    <th class="text-center align-middle">
                                        <span data-toggle="m-tooltip" id="allow"
                                            data-placement="top"
                                            data-original-title="STANDARD ALLOWANCE"
                                            data-skin="dark">
                                            ALLOW
                                        </span></th --->
                                    <th class="text-center align-middle">
                                        <span data-toggle="m-tooltip"
                                            data-placement="top"
                                            data-original-title="DAYS WORKED"
                                            data-skin="dark">
                                            DAYS
                                        </span>
                                    </th>
                                    <th class="text-center align-middle">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="BASIC PAY"
                                              data-skin="dark">
                                            BASIC
                                        </span>
                                    </th>
                                    <!-- th class="text-center align-middle">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="ALLOWANCE"
                                              data-skin="dark" id="allowance">
                                            ALLOWANCE
                                        </span>
                                    </th -->
                                    <th class="text-center align-middle">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="ADJUSTMENTS"
                                              data-skin="dark" id="adjustment">
                                            ADJUSTMENTS
                                        </span>
                                    </th>
                                    <th class="text-center align-middle">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="GROSS PAY"
                                              data-skin="dark">
                                            GROSS
                                        </span>
                                    </th>
                                    <th class="text-center align-middle">
                                        <span data-toggle="m-tooltip"
                                              data-placement="top"
                                              data-original-title="NET PAY"
                                              data-skin="dark">
                                            NET PAY
                                        </span>
                                    </th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot class="m--hide">
                                    <tr>
                                        <th class="m--font-boldest text-right" colspan="4">GRAND TOTAL</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

<div class="modal fade" tabindex="-1" role="dialog" id="confirm-payroll-posting">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Posting</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0 m--regular-font-size-lg3 m--font-bolder">Are you sure to post selected record?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btnSave" onclick="postPayrollSheet()">Yes</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="confirm-undo-posting">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('payroll/confirm_undo_posting') ?>"
                data-url="<?= base_url('payroll/confirm_undo_posting') ?>"
                id="frm-undo-posting">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Undo Posting</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-0 m--regular-font-size-lg3 m--font-bolder"></p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnUndo_posting">Yes</button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-confirm-approval-created-adjustment">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="frm-approval-created-adjustments" action="<?= base_url('payroll/confirm_adjustment_approval') ?>">
                <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="id" value="0" id="adj_id" />
                <input type="hidden" name="status" value="0" id="adj_status" />
                <div class="modal-header">
                    <h5 class="modal-title">Payroll Sheet Adjustment Approval</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-0 m--regular-font-size-lg3 m--font-bolder">Are you sure you want to <span id="temp_status">approve</span> this adjustment?</p>
                    <div class="form-group mt-1">
                        <label for="">Remarks *</label>
                        <textarea name="approval_remarks" rows="2" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnSave">Yes</button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-ps--signatory">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="signatory--content">
            <div class="modal-header">
                <h5 class="modal-title">Payroll Sheet Signatories</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="updatePrintableSignatories" method="post" action="<?php echo site_url("payroll/update_printable_signatories"); ?>">
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
                <h5 class="modal-title">Reset - Payroll Sheet Signatories</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="resetPrintableSignatories" method="post" action="<?php echo site_url("payroll/reset_printable_signatories"); ?>">
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
<?php
    $this->load->view("modals/create_custom_adj_modal");
    $this->load->view("modals/view_timesheet_modal");
?>
