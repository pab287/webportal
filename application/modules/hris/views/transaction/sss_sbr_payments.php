<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        SSS Premium <small>SBR Payments</small>
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-8 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">
                            <div class="col-md-12">
                                <button type="button" data-toggle="modal"
                                data-target="#modal-sbr_payment"
                                class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew">
                                    <span>
                                        <i class="la la-plus"></i>
                                        <span>
                                            SBR Payment
                                        </span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                        <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                            <input type="text" class="form-control m-input m-input--solid"
                                    placeholder="Search..." id="generalSearch">
                            <span class="m-input-icon__icon m-input-icon__icon--left">
                                <span>
                                    <i class="la la-search"></i>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!--begin: Datatable -->
            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive">
                <table class="table table-striped table-bordered" id="table-sbr_payments" style="width:100%">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>SBR #</th>
                            <th>Payment Date</th>
                            <th>Year</th>
                            <th>Month</th>
                            <th>Employee Count</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <!--end: Datatable -->
        </div>
    </div>

    <div class="modal fade" id="modal-sbr_payment" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">SBR Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="form-sbr_payment" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6 col-md-6 col-lg-6 col-xl-6 col-sm-12">
                                <div class="form-group m-form__group">
                                    <label for="sbr_number" class="col-form-label">SBR #</label>
                                    <input type="text" class="form-control" id="sbr_number" name="sbr_no" autocomplete="off" maxlength="22" />
                                </div>
                            </div>
                            <div class="col-6 col-md-6 col-lg-6 col-xl-6 col-sm-12">
                                <div class="form-group m-form__group">
                                    <label for="payment_date" class="col-form-label">Payment Date</label>
                                    <div class="input-group" id="date-picker">
                                        <input type="text" class="form-control" id="payment_date" name="payment_date" autocomplete="off" />
                                        <span class="input-group-addon">
                                            <i class="la la-calendar-check-o"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 col-md-6 col-lg-6 col-xl-6 col-sm-12">
                                <div class="form-group m-form__group">
                                    <label for="month" class="col-form-label">Month</label>
                                    <select name="month_name" id="month" class="form-control">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-md-6 col-lg-6 col-xl-6 col-sm-12">
                                <div class="form-group m-form__group">
                                    <label for="year" class="col-form-label">Year</label>
                                    <select name="year" id="year" class="form-control">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group m-form__group">
                            <label for="company" class="col-form-label">Company</label>
                            <select name="company_id" id="company" class="form-control">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnSave" id="btn-save-sbr_payment">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-edit_sbr_payment" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit - SBR Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="form-edit_sbr_payment" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                    <div id="sbr_payment-content" class="modal-body">
                        <input type="hidden" name="id" v-model="row.id" />
                        <div class="row">
                            <div class="col-6 col-md-6 col-lg-6 col-xl-6 col-sm-12">
                                <div class="form-group m-form__group">
                                    <label for="edit_sbr_number" class="col-form-label">SBR #</label>
                                    <input type="text" class="form-control" id="edit_sbr_number" name="sbr_no" autocomplete="off" maxlength="22" v-model="row.sbr_no" />
                                </div>
                            </div>
                            <div class="col-6 col-md-6 col-lg-6 col-xl-6 col-sm-12">
                                <div class="form-group m-form__group">
                                    <label for="edit_payment_date" class="col-form-label">Payment Date</label>
                                    <div class="input-group" id="date-picker">
                                        <input type="text" class="form-control" id="edit_payment_date" name="payment_date" autocomplete="off" v-model="row.payment_date" />
                                        <span class="input-group-addon">
                                            <i class="la la-calendar-check-o"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 col-md-6 col-lg-6 col-xl-6 col-sm-12">
                                <div class="form-group m-form__group">
                                    <label for="edit_month" class="col-form-label">Month</label>
                                    <template v-if="row.contribution_count == '0'">
                                    <select name="month_name" id="edit_month" class="form-control">
                                        <option value=""></option>
                                    </select>
                                    </template>
                                    <template v-else>
                                    <p class="form-control mb-0" disabled v-text="row.month_name.toUpperCase()">&nbsp;</p>
                                    </template>
                                </div>
                            </div>
                            <div class="col-6 col-md-6 col-lg-6 col-xl-6 col-sm-12">
                                <div class="form-group m-form__group">
                                    <label for="edit_year" class="col-form-label">Year</label>
                                    <template v-if="row.contribution_count == '0'">
                                    <select name="year" id="edit_year" class="form-control">
                                        <option value=""></option>
                                    </select>
                                    </template>
                                    <template v-else>
                                    <p class="form-control mb-0" disabled v-text="row.year">&nbsp;</p>
                                    </template>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group m-form__group">
                            <label for="edit_company" class="col-form-label">Company</label>
                            <template v-if="row.contribution_count == '0'">
                            <select name="company_id" id="edit_company" class="form-control">
                                <option value=""></option>
                            </select>
                            </template>
                            <template v-else>
                            <p class="form-control mb-0" disabled v-text="row.company_code">&nbsp;</p>
                            </template>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btnSave" id="btn-update-sbr_payment">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-sbr_employee_details" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">SBR Employee Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="contribution_content" class="row">
                        <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-sm-12 mb-5">
                            <div class="m-widget12">
                                <div class="m-widget12__item">
                                    <span class="m-widget12__text1">
                                        <label for="" class="m--font-bolder mb-0">SBR #</label><br>
                                        <span v-text="info.sbr_no">&nbsp;</span>
                                    </span>
                                    <span class="m-widget12__text2">
                                        <label for="" class="m--font-bolder mb-0">Payment Date</label><br>
                                        <span v-text="info.payment_date">&nbsp;</span>
                                    </span>
                                </div>
                            </div>
                            <table class="table table-striped table-bordered">
                                <colgroup>
                                    <col style="width: *">
                                    <col style="width: 25%">
                                    <col style="width: 25%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Month</th>
                                        <th>Year</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td v-text="info.company_code"></td>
                                        <td v-text="info.month_name"></td>
                                        <td v-text="info.year"></td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-striped table-bordered">
                                <colgroup>
                                    <col style="width: 50%">
                                    <col style="width: 50%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>Created By</th>
                                        <th>Last Updated By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><p class="mb-0">
                                            <span v-text="info.created_by_name"></span><br>
                                            <small v-text="info.created_at"></small>
                                        </p></td>
                                        <td>
                                            <p class="mb-0">
                                                <template v-if="info.last_updated_by != 0">
                                                <span v-text="info.updated_by_name"></span><br>
                                                <small v-text="info.last_updated_at"></small>
                                                </template>
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12 col-md-6 col-lg-12 col-xl-12 col-sm-12">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>SSS #</th>
                                        <th>Employee Name</th>
                                        <th class="text-right">Total Contribution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="count > 0">
                                    <tr v-for="(row, index) in rows" :key="index">
                                        <td v-text="index + 1"></td>
                                        <td v-text="row.employee_name"></td>
                                        <td v-text="row.sss_no"></td>
                                        <td class="text-right" v-text="row.sss_total"></td>
                                    </tr>
                                    </template>
                                    <template v-else>
                                        <tr>
                                            <td colspan="3" class="text-center">No data found.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
