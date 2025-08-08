<style>
span.help-block.form-error {
    text-transform: uppercase;
}
</style>
<div class="m-content">
    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
            <div class="m-portlet m-portlet--bordered m-portlet--rounded">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-filter"></i>
                            </span>
                            <h3 class="m-portlet__head-text">Filter Options</h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">&nbsp;</div>
                </div>
                <form id="formFilter" class="m-form m-form--label-align-right">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div id="tempFilter" class="m-portlet__body">
                    <div  id="tempFilterBy" class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group has-success">
                                <label for="filter_by" class="m--font-bolder">FILTER BY</label>
                                <div class="m-checkbox-inline">
                                    <label class="m-checkbox">
                                        <input type="radio" id="all_employees" name="filter_by" value="all" data-validation="required" v-model="all_filter" checked />
                                        ALL<span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="radio" id="monthly_employees" name="filter_by" value="month" data-validation="required" v-model="all_filter" />
                                        MONTH <span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <input type="radio" id="yearly_employees" name="filter_by" value="year" data-validation="required" v-model="all_filter" />
                                        YEAR <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12" v-if="all_filter === 'month' || all_filter === 'year'">
                            <div class="row">
                                <div class="form-group m-form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 m-animate-fade-in" v-if="all_filter === 'month'">
                                    <label class="required" for="filter_month">MONTH</label>
                                    <select class="form-control" name="filter_month" id="filter_month" data-validation="required">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group m-form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 m-animate-fade-in" v-if="all_filter === 'month' || all_filter === 'year'">
                                    <label class="required" for="filter_year">YEAR</label>
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
                                <label for="company" class="m--font-bolder">COMPANY <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">( Optional )</span></label>
                                <select name="company" id="company" class="form-control"><option></option></select>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="employee" class="m--font-bolder required">Employee</label>
                                <select name="employee" id="employee" class="form-control" data-validation="required">
                                    <option></option>
                                </select>
                            </div>
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
                    <button type="submit" class="m-btn btn btn-success btnAdvance_search btn-submit">Search</button>
                </div>
                </form>
            </div>
        </div>
        <div class="col-9 col-md-9 col-lg-9 col-xl-9 col-sm-12">
            <div class="m-portlet m-portlet--bordered m-portlet--rounded">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-list"></i>
                            </span>
                            <h3 class="m-portlet__head-text">SSS Premium Contribution <small>Report</small></h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">&nbsp;</div>
                </div>
                <div id="filteredContent" class="m-portlet__body">
                    <template v-if="count == 0">
                        <div class="alert alert-danger m-alert m-alert--air m-alert--outline" role="alert">
                            <strong>SSS Premium Contribution Report</strong> No data / record(s) found.
                        </div>
                    </template>
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12 col-xl-12">
                            <table id="table-sss_premium_contribution" class="table table-striped table-bordered" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">Year</th>
                                        <th>Month</th>
                                        <th class="text-center">SSS Premium</th>
                                        <th class="text-center">SBR #</th>
                                        <th class="text-center">Date Paid</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
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
    </div>

    <div class="modal fade" tabindex="-1" id="modal-ps--signatory">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="signatory--content">
            <div class="modal-header">
                <h5 class="modal-title">SSS Contribution Signatories</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="updatePrintableSignatories" method="post" action="<?php echo site_url("hris/reports/update_printable_signatories"); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="id" v-model="row.id" />
                <input type="hidden" name="signatory_id" v-model="row.signatory_id" />
                <input type="hidden" name="user_id" v-model="row.user_id" />
                <div class="modal-body">
                    <div class="row">
                        
                    </div>
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
                                        <input type="checkbox" name="" :checked="item.is_active === true" @change="activeSignatory(event)">
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

<div class="modal fade" tabindex="-1" id="modal-ps--reset-signatory">
    <div class="modal-dialog" >
        <div class="modal-content" id="reset-signatory--content">
            <div class="modal-header">
                <h5 class="modal-title">Reset - SSS Contribution Signatories</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="resetPrintableSignatories" method="post" action="<?php echo site_url("hris/reports/reset_printable_signatories"); ?>">
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
</div>
