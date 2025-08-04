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
                            <th>Month</th>
                            <th>Year</th>
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
</div>
