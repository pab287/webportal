<form id="frmEditPayrollData" class="m-form m-form--fit m-form--label-align-right" action="<?php echo site_url("payroll/payroll/update_employee_payroll_data"); ?>">
    <div id="frmEditPayrollData-container" class="m-portlet__body">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="id" value="<?php echo $data->id; ?>">
        <div class="row m--margin-bottom-10">
            <div class="col-5 col-md-5 col-lg-5 col-xl-5 col-sm-12">
                <div class="form-group m-form__group row">
                    <label class="col-5 col-form-label text-right">Payroll Type:</label>
                    <div class="col-7">
                        <select class="form-control m-input" name="payroll_type"
                                placeholder="Select an option" id="payroll_type" data-validation="required"
                                v-model="vmpayinfo.payroll_type">
                            <option value=""></option>
                            <?php foreach ($payroll_types as $payroll_type): ?>
                                <option value="<?= strtolower($payroll_type->name) ?>">
                                    <?= $payroll_type->name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="payroll_type_desc" name="payroll_type_desc" v-model="edited_content.payroll_type" />
                    </div>
                </div>
            </div>
            <div class="col-5 col-md-5">
                <div class="form-group m-form__group row">
                    <label for="position" class="col-5 col-form-label text-right">Basic rate:</label>
                    <div class="col-7">
                        <input type="text" name="basic_rate" v-model="vmpayinfo.basic_rate"
                            autocomplete="off"
                            class="form-control m-input" 
                            data-validation="required" />
                    </div>
                </div>
            </div>
            <div class="col-2 col-md-2 text-right">
                <div class="form-group m-form__group">
                    <a class="btn btn-warning m-btn m-btn--icon m-btn--icon-only m-btn--pill text-white btnView" 
                        @click="forApprovalModal()" v-if="forApprovalCtr > 0">
                        <i class="fa flaticon-bell"></i>
                    </a>
                    <a class="btn btn-info m-btn m-btn--icon m-btn--icon-only m-btn--pill text-white btnView" 
                        @click="scrollToBottom()" v-if="psInfoCtr > 0">
                        <i class="fa flaticon-clipboard"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="row m--margin-bottom-10">
            <div class="col-5 col-md-5">
                <div class="form-group m-form__group row">
                    <label class="col-5 col-form-label text-right">Payout Schedule:</label>
                    <div class="col-7">
                        <select class="form-control m-input" name="payout_sched"
                                id="payout_sched"
                                placeholder="Select an option" data-validation="required"
                                v-model="vmpayinfo.payout_sched">
                            <option value=""></option>
                            <?php foreach ($payout_scheds as $payout_sched): ?>
                                <option value="<?= $payout_sched->id ?>"><?= $payout_sched->name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="payout_sched_desc" name="payout_sched_desc" v-model="edited_content.payout_sched" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="m-portlet__foot m-portlet__foot--fit m-portlet__no-border">
        <div class="m-form__actions">
            <div class="row">
                <div class="col-12 text-right" id="saveAndApproveAction">
                    <template v-if="approving_authority">
                        <button type="submit" class="btn btnSave btn-success m-btn m-btn--air m-btn--custom btn-submit">
                            <i class="la la-thumbs-up mr-2"></i>Save and Approve
                        </button>
                    </template>
                    <template v-else>
                        <button type="submit" class="btn btnSave btn-primary m-btn m-btn--air m-btn--custom btn-submit">
                            <i class="la la-check mr-2"></i>Save
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</form>

<form id="frmEditBankData" class="m-form m-form--fit m-form--label-align-right" method="POST" action="<?php echo site_url("payroll/employee/update_employee_bank_information"); ?>">
    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x m-0"></div>
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="id" value="<?php echo $data->id; ?>">
    <div id="frmEditBankData-container" class="m-portlet__body">
        <div class="row m--margin-bottom-25">
            <div class="col-10 ml-auto"><h3 class="m-form__header m-form__section">Bank Information</h3></div>
        </div>
        <div class="row">
            <?php if (in_array('edit_bankinfo', $this->core_layout->getCurrentActions())): ?>
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="date_start" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label">Bank Name: </label>
                        <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                            <input type="text" id="bank_name" name="bank_name" class="form-control m-input" autocomplete="off" data-validation="required" v-model="row.bank_name"/>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="date_start" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label">ATM INFO: </label>
                        <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                            <input type="text" id="atm_info" name="atm_info" class="form-control m-input" autocomplete="off" data-validation="required" :value="row.atm_info"/>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="date_start" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label">Bank Name: </label>
                        <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                            <p class="form-control m-0" disabled>{{ row.bank_name }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-5">
                    <div class="form-group m-form__group row">
                        <label for="date_start" class="col-sm-6 col-md-5 col-lg-5 col-xl-5 col-form-label">ATM Info: </label>
                        <div class="col-sm-6 col-md-7 col-lg-7 col-xl-7">
                            <p class="form-control m-0" disabled>{{ row.atm_info }}</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if(in_array("edit_bankinfo", $this->core_layout->getCurrentActions())): ?>
        <div class="m-portlet__foot m-portlet__foot--fit m-portlet__no-border">
            <div class="m-form__actions">
                <div class="row">
                    <div class="col-12 text-right" id="saveAndApproveAction">
                        <button type="submit" class="btn btnSave btn-primary m-btn m-btn--air m-btn--custom btn-submit">
                            <i class="la la-check mr-2"></i>Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x mt-0"></div>
</form>

<div class="m-form m-form--fit">
    <div class="m-portlet__body pt-0">
        <div class="form-group m-form__group row mb-0 pb-0">
            <div class="col-12 col-md-12">
                <h4 class="m-form__header m-form__section">Allowances</h4>
            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-12 col-md-12">
                <div class="row align-items-center">
                    <div class="col-xl-8 order-2 order-xl-1">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <button class="btn btn-sm btn-success btnNew" 
                                    data-toggle="modal" data-target="#mdl-newAllowance">
                                    <i class="fa fa-plus"></i> <span>New</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                        <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                            <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearchAllowances">
                            <span class="m-input-icon__icon m-input-icon__icon--left">
                                <span>
                                    <i class="la la-search"></i>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table id="tbl-allowances" class="table display table-bordered table-striped dataTable no-footer"
                           width="100%">
                        <thead>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Active</th>
                            <th>Action</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="form-group m-form__group row mb-0 pb-0">
            <div class="col-12 ml-auto">
                <h4 class="m-form__header m-form__section">Benefits</h4>
            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-12 col-md-12">
                <div class="row align-items-center">
                    <div class="col-xl-8 order-2 order-xl-1">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <button class='btn btn-sm btn-success btnNew' 
                                    data-toggle='modal' 
                                    data-target='#mdl-newBenefit'>
                                    <i class='fa fa-plus'></i> <span>New</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                        <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                            <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearchBenefits">
                            <span class="m-input-icon__icon m-input-icon__icon--left">
                                <span>
                                    <i class="la la-search"></i>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table id="tbl-benefits" class="table display table-bordered table-striped dataTable no-footer"
                           width="100%">
                        <thead>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <div class="row form-group m-form__group mb-0 pb-0">
            <div class="col-12 ml-auto">
                <h4 class="m-form__header m-form__section mb-3">Deductions</h4>
            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-12">
                <ul class="nav nav-tabs nav-fill mb-0">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab_deduction" data-toggle="tab" href="#tab_deductions">
                            Loans
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab_paid" data-toggle="tab" href="#tab_paid_loan">
                            Paid Loans / Deductions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab_cancelled" data-toggle="tab" href="#tab_cancelled_loan">
                            Cancelled Loans / Deductions
                        </a>
                    </li>
                </ul>
                <div class="tab-content" style="border: 1px solid #dddddd; border-top: 0; padding: 15px">
                    <div class="tab-pane active" id="tab_deductions">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-xl-8 order-2 order-xl-1">
                                        <button class='btn btn-sm btn-success btnNew' data-toggle='modal' data-target='#mdl-newLoan'><i class='fa fa-plus'></i> <span>New</span></button>
                                    </div>
                                    <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                        <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                            <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearchLoans">
                                            <span class="m-input-icon__icon m-input-icon__icon--left">
                                                <span>
                                                    <i class="la la-search"></i>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                                    <table id="tbl-loans" class="table display table-bordered table-striped dataTable no-footer"
                                        width="100%">
                                        <thead>
                                        <th>Loan Name</th>
                                        <th>Loaned Amount</th>
                                        <th>Amt. Pd.</th>
                                        <th>Bal.</th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="DEDUCTION TYPE"
                                                data-skin="dark">
                                                TYPE
                                            </span>
                                        </th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="PERCENTAGE VALUE OR FIXED AMOUNT VALUE"
                                                data-skin="dark">
                                                VALUE
                                            </span>
                                        </th>
                                        <!-- th>Created By</th>
                                        <th>Created At</th -->
                                        <th>Status</th>
                                        <th>Action</th>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_paid_loan">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">&nbsp;</div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearchPaidLoans">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <table id="tbl-deduction-history" class="table display table-bordered table-striped dataTable no-footer" width="100%">
                            <thead>
                                <th>Loan Name</th>
                                <th>Loaned Amount</th>
                                <th>Amt. Pd.</th>
                                <th>Bal.</th>
                                <th>
                                    <span data-toggle="m-tooltip"
                                        data-placement="top"
                                        data-original-title="DEDUCTION TYPE"
                                        data-skin="dark">
                                        TYPE
                                    </span>
                                </th>
                                <th>
                                    <span data-toggle="m-tooltip"
                                        data-placement="top"
                                        data-original-title="PERCENTAGE VALUE OR FIXED AMOUNT VALUE"
                                        data-skin="dark">
                                        VALUE
                                    </span>
                                </th>
                                <!-- th>Created By</th>
                                <th>Created At</th -->
                                <th>Status</th>
                                <th>Action</th>
                            </thead>
                        </table>
                    </div>
                    <div class="tab-pane" id="tab_cancelled_loan">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">&nbsp;</div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearchCancelledLoans">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <table id="tbl-deduction-cancelled" class="table display table-bordered table-striped dataTable no-footer" width="100%">
                            <thead>
                                <th>Loan Name</th>
                                <th>Loaned Amount</th>
                                <th>Amt. Pd.</th>
                                <th>Bal.</th>
                                <th>
                                    <span data-toggle="m-tooltip"
                                        data-placement="top"
                                        data-original-title="DEDUCTION TYPE"
                                        data-skin="dark">
                                        TYPE
                                    </span>
                                </th>
                                <th>
                                    <span data-toggle="m-tooltip"
                                        data-placement="top"
                                        data-original-title="PERCENTAGE VALUE OR FIXED AMOUNT VALUE"
                                        data-skin="dark">
                                        VALUE
                                    </span>
                                </th>
                                <!-- th>Created By</th>
                                <th>Created At</th -->
                                <th>Status</th>
                                <th>Action</th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>

        <div class="form-group m-form__group row mb-0 pb-0">
            <div class="col-12 col-md-12">
                <h4 id="payroll_info-history" class="m-form__header m-form__section">History <small>Payroll Information</small></h4>
            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-12 col-md-12">
                <div class="row align-items-center">
                    <div class="col-xl-8 order-2 order-xl-1">
                        <div class="row align-items-center">
                            <div class="col-md-12">&nbsp;</div>
                        </div>
                    </div>
                    <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                        <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                            <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearchHistory">
                            <span class="m-input-icon__icon m-input-icon__icon--left">
                                <span>
                                    <i class="la la-search"></i>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table id="tbl-payroll_history" class="table display table-bordered table-striped dataTable no-footer"
                           width="100%">
                        <thead>
                            <th>Logs</th>
                            <th>Action</th>
                            <th>Last Edited By</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- <div class="form-group m-form__group row">
            <div class="col-12 ml-auto">
                <h4 class="m-form__header m-form__section">Deductions</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table id="tbl-loans" class="table display table-bordered table-striped dataTable no-footer"
                           width="100%">
                        <thead>
                        <th>Loan Name</th>
                        <th>Loaned Amount</th>
                        <th>Amt. Pd.</th>
                        <th>Bal.</th>
                        <th>
                            <span data-toggle="m-tooltip"
                                  data-placement="top"
                                  data-original-title="DEDUCTION TYPE"
                                  data-skin="dark">
                                TYPE
                            </span>
                        </th>
                        <th>
                            <span data-toggle="m-tooltip"
                                  data-placement="top"
                                  data-original-title="PERCENTAGE VALUE OR FIXED AMOUNT VALUE"
                                  data-skin="dark">
                                VALUE
                            </span>
                        </th>
                        th>Created By</th> -> hidden
                        <th>Created At</th> -> hidden
                        <th>Status</th>
                        <th>Action</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div> -->
    </div>
</div>

<div class="modal fade" id="mdl-newAllowance" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="frm-newAllowance" action="<?php echo site_url("payroll/employee/save_employee_allowance"); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="emp_id" value="<?php echo $data->id; ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Allowance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="allowance" class="form-control-label required">
                            Allowance
                        </label>
                        <select name="allowance_id" id="allowance_id" class="form-control"
                                data-validation="required">
                        </select>
                    </div>

                    <div class="form-group mt-4">
                        <div class="row">
                            <label class="form-control-label col-6 required">
                                Amount
                            </label>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <input type="number" name="rate" class="form-control"
                                       data-validation="required" autocomplete="off"/>
                            </div>
                        </div>
                    </div>

                    <div class="m-form__group form-group mt-4">
                        <label for="" class="required">Frequency</label>
                        <div class="m-radio-inline">
                            <label class="m-radio">
                                <input type="radio" name="frequency" value="day" checked>
                                DAILY
                                <span></span>
                            </label>
                            <label class="m-radio">
                                <input type="radio" name="frequency" value="month">
                                MONTHLY
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-submit btnSave">Save</button>
                    <button class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="mdl-newBenefit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="frm-newBenefit" action="<?php echo site_url("payroll/employee/save_employee_benefit"); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="emp_id" value="<?php echo $data->id; ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Benefit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="allowance" class="form-control-label">
                            Benefit :
                        </label>
                        <select name="benefit_id" id="benefit_id" class="form-control">
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label class="form-control-label col-6">
                                Rate*
                            </label>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <input type="number" name="rate" class="form-control"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-submit btnSave">Save</button>
                    <button class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="mdl-newLoan" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="frm-newLoan" action="<?php echo site_url("payroll/employee/save_employee_loan"); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="emp_id" value="<?php echo $data->id; ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Loan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-xl-8 col-lg-8 col-md-8 col-sm-12">
                            <label for="allowance" class="form-control-label required">Loan</label>
                            <select name="loan_id" id="loan_id" class="form-control"
                                    data-validation="required">
                                    <option></option>
                            </select>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 form-group">
                            <label for="required" class="required">Amount</label>
                            <input type="text" id="loan_amount" name="amount" autocomplete="off" class="form-control text-right"
                                   data-validation="required">
                        </div>
                    </div>
                    
                    <div id="hasReferenceLoans">
                        <div class="row m--hide m-animate-fade-in-up" v-if="hasrefs === true">
                            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 form-group">
                                <input type="hidden" name="reference" v-model="reference" />
                                <label for="required" class="">CA Reference #</label>
                                <select name="reference_id" id="ca_reference" class="form-control m--hide">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 row">
                        <div class="form-group col-xl-8 col-lg-8 col-md-8 col-sm-12">
                            <label class="form-control-label required">Deduction Type</label>
                            <div class="m-radio-inline mt-2">
                                <label class="m-radio mb-0">
                                    <input type="radio" name="deduction_type" value="1">
                                    Fix amount<span></span>
                                </label>
                                <label class="m-radio mb-0">
                                    <input type="radio" name="deduction_type" value="0" checked>
                                    Percentage<span></span>
                                </label>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 form-group">
                            <label for="required" class="required" id="deduct_type_value_label">Value</label>
                            <input type="text" name="deduct_type_value" data-validation="required"
                                   autocomplete="off" class="form-control text-right">
                        </div>
                    </div>

                    <div class="mt-4 row">
                        <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 form-group">
                            <label for="required">DN Reference #</label>
                            <input type="text" name="debit_note" autocomplete="off" class="form-control" maxlength="12" />
                        </div>
                        <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <label class="form-control-label">Remarks</label>
                            <textarea name="remarks" cols="20" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-submit btnSave">Save</button>
                    <button class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="mdl-removeAllowance" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="frm-removeAllowance" action="<?php echo site_url("payroll/employee/remove_employee_allowance"); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="emp_id" value="<?php echo $data->id; ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Allowance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            <b>Removing</b> this data will delete it permanently. Do you wish to proceed?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-submit btnSave">Save</button>
                    <button class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="mdl-removeBenefit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="frm-removeBenefit" action="<?php echo site_url("payroll/employee/remove_employee_benefit"); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="emp_id" value="<?php echo $data->id; ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Benefits</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            <b>Removing</b> this data will delete it permanently. Do you wish to proceed?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-submit btnSave">Save</button>
                    <button class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="mdl-removeLoan" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="frm-removeLoan" action="<?php echo site_url("payroll/employee/remove_employee_loan"); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="emp_id" value="<?php echo $data->id; ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Archive Loan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            <b>Archiving</b> this data will remove it from table. Do you wish to proceed?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-submit btnSave">Save</button>
                    <button class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="mdl-mergeLoan" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form id="frm-mergeLoan" action="<?php echo site_url("payroll/employee/merge_employee_loan"); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div id="mergeLoan-content" class="modal-content">
                <input type="hidden" name="id" v-model="row.id" />
                <div class="modal-header">
                    <h5 class="modal-title">Mergeable Loan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-5 col-md-5 col-lg-5 col-sm-12">
                            <div class="m-widget14 p-0">
                                <div class="m-widget14__header m-0 p-0">
                                    <h3 class="m-widget14__title">{{row.loan_name}}</h3>
                                    <span class="m-widget14__desc">Loan Type</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-5 col-md-5 col-lg-5 col-sm-12" v-if="row.reference">
                            <div class="m-widget14 p-0">
                                <div class="m-widget14__header m-0 p-0">
                                    <h3 class="m-widget14__title">{{row.reference}}</h3>
                                    <span class="m-widget14__desc">Reference #</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-widget4 mb-3">
                        <template v-if="row.deduction_type == '1'">
                        <div class="m-widget4__item pb-1">
                            <div class="m-widget4__info">
                                <p class="m-widget4__title m-0">Fixed Amount</p>
                            </div>
                            <span class="m-widget4__ext">
                                <span class="m-widget4__number m--font-brand">{{row.fixed_amt_formatted}}</span>
                            </span>	
                        </div>
                        </template>
                        <template v-else>
                            <div class="m-widget4__item pb-1">
                                <div class="m-widget4__info">
                                    <p class="m-widget4__title m-0">Percentage ( % )</p>
                                </div>
                                <span class="m-widget4__ext">
                                    <span class="m-widget4__number m--font-brand">{{row.percentage_formatted}}</span>
                                </span>	
                            </div>
                        </template>
                        <div class="m-widget4__item pt-1 pb-1">
                            <div class="m-widget4__info">
                                <p class="m-widget4__title m-0">Amount</p>
                            </div>
                            <span class="m-widget4__ext">
                                <span class="m-widget4__number m--font-brand">{{row.amount_formatted}}</span>
                            </span>	
                        </div>
                    </div>
                    <template v-if="countMergeableLoan() > 0">
                    <div class="m-alert m-alert--outline alert alert-danger" role="alert">
                        <strong>Note!</strong><small class="ml-3">Merging of loans will create a new loan data and will cancel the previous loan(s)!</small>
                    </div>
                    <div class="m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded m-portlet--head-sm">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">Mergeable Loan/s</h3>
                                </div>			
                            </div>
                        </div>
                        <div class="m-portlet__body pt-0">
                            <div class="m-widget4">
                                <div class="m-widget4__item pb-1 pt-1" v-for="loan in row.to_merge_loans">
                                    <input type="hidden" :name="'balance_amt['+loan.id+']'" :value="loan.balance_amt" />
                                    <div class="m-widget4__ext">							 
                                        <span class="m-widget4__icon m--font-brand">
                                            <i class="flaticon-coins"></i>
                                        </span>
                                    </div>
                                    <div class="m-widget4__info">
                                    <p class="m-widget4__text m--marginless">
                                        {{loan.loan_name}} - <small>Current Balance</small> <span class="m-widget4__number m--font-info m--margin-left-15 m--font-boldest">{{loan.balance_amt_formatted}}</span><span class="mr-5 m-badge m-badge--wide m--regular-font-size-sm5 pull-right" :class="loan.active === '0' ? 'm-badge--warning':'m-badge--info text-white'">{{ loan.active === '0' ? 'Suspended':'Active' }}</span>
                                    </p>
                                    <p class="m-widget4__sub m--font-danger m--font-bolder m--marginless">
                                        Deduction: <span>{{ loan.deduction_type == 0 ? loan.percentage +' ( % )':loan.fixed_deduction_amt + 'Fixed Amount' }}</span>
                                    </p>						 		 
                                    </div>
                                    <div class="m-widget4__ext">
                                        <span class="m-switch m-switch--sm">
                                        <label data-toggle="m-tooltip" data-placement="bottom" title="" data-original-title="Allow Mergeable Loan">
                                            <input type="checkbox" class="loans-mergeable" name="merge_id[]" :value="loan.id">
                                            <span></span>
                                        </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </template>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-submit btnSave">Merge</button>
                    <button class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="forApprovalModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">For Approval</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table id="tbl-approval_history" class="table display table-bordered table-striped dataTable no-footer" width="100%">
                        <colgroup>
                            <col width="*">
                            <col width="20%">
                            <col width="15%">
                            <col width="20%">
                            <col width="8%">
                        </colgroup>
                        <thead>
                            <th>Description</th>
                            <th>Value</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Action</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link:focus, .nav-tabs .nav-link:hover{
        background-color: #f9f9f9;
    }
</style>

<?php
    $this->load->view("../../modals/edit_employee_allowance");
    $this->load->view("../../modals/edit_employee_loan");
    $this->load->view("../../modals/loan_payment_history_modal");
    $this->load->view("../../modals/loan_attachment_modal");
    $this->load->view("../../modals/edit_employee_benefits");
?>

<script>
    const editEmployeeAllowanceModal = $("#edit-employee-allowance-modal");
    const addEmployeeLoan = $("#mdl-newLoan");
    const editEmployeeLoan = $("#edit-employee-loan");
    const loanPaymentHistoryModal = $("#loan-payment-history-modal");
    const loanAttachmentModal = $("#loan-attachment-modal");
    const editEmployeeBenefitsModal = $("#edit-employee-benefits-modal");

    const modalMergeLoan = $("#mdl-mergeLoan");
    const modalForApproval = $("#forApprovalModal");

    const loansDropdown = <?php echo json_encode($loans_dropdown); ?>;
    let loansCAReference = <?php echo json_encode($loans_ca_reference); ?>;

    let dtAllowance = null, dtLoans = null, dtHistoryPayrollInfo = null, dtCancelledLoanHistory = null, dtBenefits = null, dtPaidLoanHistory = null;
    let generalSearchAllowances = null, generalSearchHistory = null, generalCancelledSearchHistory = null, generalLoanSearchHistory = null,
    generalSearchBenefits = null, generalPaidLoanSearchHistory = null;

    $.validate({
        form: '#frmEditPayrollData',
        lang: 'en',
        scrollToTopOnError: false,
        onSuccess: function (form) {
            let formData = $(form).serialize();
            const basic = $("input[name=basic_rate]").val();

            if (parseFloat(basic) > 0) {
                const approvingAuthority = typeof _tempContentData.approving_authority !== "undefined" && _tempContentData.approving_authority ? 
                    _tempContentData.approving_authority: false;
                
                formData += "&approving_authority="+approvingAuthority;
                $.ajax({
                    url: $(form).attr("action"),
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    beforeSend: function () {
                        $(".btn-submit", form).addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function (json) {
                        if (json.status) { toastr.success(json.response, "Notice", 5000); } 
                        else { toastr.error(json.response, "Notice", 5000); }
                        $(".btn-submit", form).removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        if(json.for_approval){  toastr.info(json.approval_notification, "For Approval", 5000); }
                        dtHistoryPayrollInfo.ajax.reload();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'The basic rate is invalid.'
                })
            }

            return false;
        },
    });

    const dtTableAllowance = function(){
       return $("#tbl-allowances").DataTable({
            dom: 'frtlip',
            serverSide: true,
            processing: true,
            autoWidth: false,
            searching: false,
            width: "100%",
            ajax: {
                url: "<?php echo base_url("payroll/employee/get_employee_allowance");?>",
                type: "post",
                dataType: "json",
                global: false,
                data: function(d){ 
                    d.csrf_token = _csrf_hash, 
                    d.emp_id = <?php echo $data->id; ?>, 
                    d.search['value'] = generalSearchAllowances 
                }
            },
            columns: [
                {
                    width: "*",
                    data: "allowance_name",
                    render: function (data) {
                        return `<span>${data}</span>`;
                    }
                },
                {
                    width: "15%",
                    data: "rate",
                    className: "text-right",
                    render: function (data, type, row) {
                        return `<span>${parseFloat(data).toFixed(2)} / ${row.frequency}</span>`;
                    }
                },
                {
                    width: "15%",
                    data: "is_active",
                    className: "text-center",
                    orderable: false,
                    render: function (data, type, row) {
                        if (parseInt(data) === 1) {
                            return `<span class="m-badge m-badge--success m-badge--wide m--font-boldest">YES</span>`;
                        } else {
                            return `<span class="m-badge m-badge--danger m-badge--wide m--font-boldest">NO</span>`;
                        }
                    }
                },
                {
                    width: "12%",
                    data: null,
                    className: "text-center",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        let btnStr = ``;
                        if (typeof _currentActions != "undefined" && _currentActions.includes('edit')) {
                            btnStr += ` <button class="btn btn-sm btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-primary"
                                                onclick="openEditEmployeeAllowanceModal(${row.id}, '${row.allowance_name}', ${row.rate}, '${row.frequency}', '${row.is_active}')">
                                            <i class="fa fa-pencil"></i>
                                        </button>`;
                        }

                        if (typeof _currentActions != "undefined" && _currentActions.includes('archive')) {
                            btnStr += ` <button class="btn btn-sm btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-danger"
                                                onclick="remove_allowance(${row.id})">
                                            <i class="fa fa-archive"></i>
                                        </button>`;
                        }

                        return btnStr;
                    }
                },
            ], initComplete: function () {
                $('#generalSearchAllowances').donetyping(function (callback) {
                    generalSearchAllowances = $(this).val();
                    dtAllowance.ajax.reload();
                });
            }
        });
    }

    function openEditEmployeeAllowanceModal(id, allowance_name, amount, frequency, is_active) {
        $("#id", editEmployeeAllowanceModal).val(id);
        $("input[name='rate']", editEmployeeAllowanceModal).val(amount);
        $("#allowance-name", editEmployeeAllowanceModal).val(allowance_name);
        $("input[name='frequency'][value='" + frequency + "']", editEmployeeAllowanceModal).prop('checked', true);
        $("input[name='is_active']", editEmployeeAllowanceModal).val(is_active).prop('checked', (parseInt(is_active) === 1));
        editEmployeeAllowanceModal.modal("show");
    }

    const dtTableBenefits = function(){
        return $("#tbl-benefits").DataTable({
            dom: 'frtlip',
            serverSide: true,
            processing: true,
            autoWidth: false,
            searching: false,
            width: "100%",
            ajax: {
                url: "<?php echo base_url("payroll/employee/get_employee_benefits"); ?>",
                type: "post",
                dataType: "json",
                global: false,
                data: function(d){
                    d.csrf_token = _csrf_hash,
                    d.emp_id = <?php echo $data->id; ?>,
                    d.search['value'] = generalSearchBenefits
                }
            },
            columns: [
                {data: "benefit_name", width: "*"},
                {data: "rate", className: "text-right", width: "15%"},
                {data: null, className: "text-center", orderable: false, width: "12%", 
                    render: function (data, type, row) {
                        let btn = ``;
                        if (typeof _currentActions != "undefined" && _currentActions.includes("edit")) {
                            btn += ` <button title="Edit"
                                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill m-btn--hover-primary"
                                    onclick="openEditEmployeeBenefitModal(${row.id}, '${row.rate}', '${row.benefit_id}')">
                            <i class="fa fa-pencil"></i>
                            </button>`;
                        }
                        if (typeof _currentActions != "undefined" && _currentActions.includes("archive")) {
                                btn += ` <button title="Archive"
                                                class="btn btn-default m-btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill m-btn--hover-danger" onclick="remove_benefit(${row.id})">
                                            <i class="fa fa-archive"></i>
                                        </button>`;
                            }
                        return btn;
                    }
                },
            ], initComplete: function () {
                $('#generalSearchBenefits').donetyping(function (callback) {
                    generalSearchBenefits = $(this).val();
                    dtBenefits.ajax.reload();
                });
            }
        });
    }

    const dtTableLoans = function(){
        return $("#tbl-loans").DataTable({
            dom: 'frtlip',
            serverSide: true,
            processing: true,
            autoWidth: false,
            ordering: false,
            searching:false,
            width: "100%",
            ajax: {
                url: "<?php echo base_url("payroll/employee/get_employee_loans");?>",
                type: "post",
                dataType: "json",
                global: false,
                data: function (d) {d.csrf_token = _csrf_hash, d.emp_id = <?php echo $data->id; ?>, d.search['value'] = generalLoanSearchHistory }
            },
            columns: [
                {
                    data: "loan_name",
                    width: "*",
                    render: function (data, type, row) {
                        const allowMerge = row.allow_merge;

                        var ref, mergeState = ``, dnRefs = ``;
                        if(row.reference === '' || row.reference === null){ ref = ``; }
                        else{ ref = `<p class='m-0'><small><span class="m--font-bolder">Reference:</span> ${row.reference}</small></p>`; }
                        if(parseInt(row.merged_count) > 0){
                            mergeState = `<span class="m-badge m-badge--wide m-badge--warning text-white m--margin-left-15 m--regular-font-size-sm5">Merged</span>`;
                        }else if(allowMerge){
                            mergeState = `<span class="m-badge m-badge--wide m-badge--primary text-white m--margin-left-15 m--regular-font-size-sm5">Mergeable</span>`;
                        }

                        if(typeof row.debit_note != "undefined" && row.debit_note){
                            dnRefs = `<span class='m--font-primary m--font-boldest m--margin-left-15 m--regular-font-size-lg1'>${row.debit_note}</span>`;
                        }

                        let tempHtml = `<p class="mb-1 m--font-bolder">${data} ${dnRefs} ${mergeState}</p>`+ref+`
                        <p class='m-0'><small><span class="m--font-bolder">Created By:</span> ${row.created_by}</small></p>
                        <p class='m-0'><small><span class="m--font-bolder">Created Date:</span> ${row.created_at}</small></p>`;

                        return tempHtml;
                    }
                },
                {
                    data: "amount",
                    className: "text-right",
                    width: "14%",
                    render: function (data) {
                        return `<span class="m--font-boldest">
                                    ${parseFloat(data).toLocaleString('en-US', {maximumFractionDigits: 2})}
                                </span>`;
                    }
                },
                {
                    data: "total_amount_paid",
                    className: "text-right m--padding-right-30",
                    width: "10%",
                    render: function (data) {
                        return `<span class="m--font-boldest">
                                    ${parseFloat(data).toLocaleString('en-US', {maximumFractionDigits: 2})}
                                </span>`;
                    }
                },
                {
                    data: null,
                    className: "text-right m--padding-right-30",
                    width: "10%",
                    render: function (data, type, row) {
                        const balance = parseFloat(row.amount) - parseFloat(row.total_amount_paid);
                        return `<span class="m--font-boldest">
                                    ${parseFloat(balance).toLocaleString('en-US', {maximumFractionDigits: 2})}
                                </span>`;
                    }
                },
                {
                    data: "deduction_type",
                    width: "10%",
                    render: function (data, type, row) {
                        return parseInt(data) === 0 ? "Percentage" : "Fix Amount";
                    }
                },
                {
                    data: null,
                    width: "8%",
                    render: function (data, type, row) {
                        if (parseInt(row.deduction_type) === 0) {
                            return parseFloat(row.percentage).toLocaleString('en-US', {maximumFractionDigits: 2}) + "" + "%";
                        } else {
                            return parseFloat(row.fixed_deduction_amt).toLocaleString('en-US', {maximumFractionDigits: 2});
                        }
                    }
                },{
                    data: "active",
                    className: "text-center",
                    width: "10%",
                    render: function (data, type, row) {
                        let tempStatus = parseInt(data);
                        let badgeColor = "m-badge--warning";
                        let badgeText = "Suspended";
                        if(row.paid == 1 && tempStatus !== 2){ tempStatus = 2; }

                        const balance = parseFloat(row.amount) - parseFloat(row.total_amount_paid);
                        if(balance <= 0){ tempStatus = 2; }

                        switch (tempStatus) {
                            case 1:
                                badgeColor = "m-badge--info";
                                badgeText = "Active";
                                break;
                            case 2:
                                badgeColor = "m-badge--success";
                                badgeText = "Paid";
                                break;
                            default:
                                badgeColor = "m-badge--warning";
                                badgeText = "Suspended";
                                break;
                        }
                        return `<span class="m-badge m-badge--wide m--font-bolder ${badgeColor}" style="width: 75%;">${badgeText}</span>`;
                    }
                },
                {
                    width: "7%",
                    data: null,
                    className: "text-center",
                    orderable: false,
                    render: function (data, type, row) {
                        let btn = ``;
                        let ctrActions = 0;
                        let listActions = ``;
                        const isPaid = parseInt(row.paid);
                        const allowMerge = row.allow_merge;
                        
                        let tempIsPaid = false;
                        const balance = parseFloat(row.amount) - parseFloat(row.total_amount_paid);
                        if(balance <= 0){ tempIsPaid = true; }

                        const rawData = JSON.stringify(row);
                        if (typeof _currentActions != "undefined" && _currentActions.includes("new") && (isPaid !== 1 && tempIsPaid === false)) {
                            if(allowMerge){
                                btn += `<button title="Mergeable Loan"
                                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill m-btn--hover-primary" 
                                    data-raw_data="${rawData}" onclick="openMergeEmployeeLoanModal(${row.id})">
                                <i class="fa fa-layer-group"></i>
                                </button> `;

                                listActions += `<li class="m-nav__item">
                                    <a href="javascript:void(0)" class="m-nav__link" 
                                    data-raw_data="${rawData}" onclick="openMergeEmployeeLoanModal(${row.id})">
                                        <i class="m-nav__link-icon flaticon-layers"></i>
                                        <span class="m-nav__link-text">MERGEABLE LOAN</span>
                                    </a>
                                </li>`;
                                ctrActions++;
                            }
                        }
                        
                        if (typeof _currentActions != "undefined" && _currentActions.includes("edit") && (isPaid !== 1 && tempIsPaid === false)) {
                            btn += `<button title="Edit"
                                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill m-btn--hover-primary"
                                    onclick="openEditEmployeeLoanModal(${row.id})">
                            <i class="fa fa-pencil"></i>
                            </button> `;
                            listActions += `<li class="m-nav__item">
                                <a href="javascript:void(0)" class="m-nav__link"
                                onclick="openEditEmployeeLoanModal(${row.id})">
                                    <i class="m-nav__link-icon flaticon-coins"></i>
                                    <span class="m-nav__link-text">EDIT LOAN</span>
                                </a>
                            </li>`;
                            ctrActions++;
                        }

                        if(typeof _currentActions != "undefined" && _currentActions.includes('view') && (row.image != 0)){
                            btn += `<button title="View payment history"
                                             class="btn btn-default m-btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill m-btn--hover-primary"
                                             onclick="openLoanAttachmentModal(${row.id})">
                                        <i class="la la-image"></i>
                                     </button> `;
                            listActions += `<li class="m-nav__item">
                                <a href="javascript:void(0)" class="m-nav__link"
                                onclick="openLoanAttachmentModal(${row.id})">
                                    <i class="m-nav__link-icon la la-image"></i>
                                    <span class="m-nav__link-text">Attachments</span>
                                </a>
                            </li>`;
                            ctrActions++;
                        }_currentActions

                        if (typeof _currentActions != "undefined" && _currentActions.includes("view")) {
                            btn += `<button title="View payment history"
                                             class="btn btn-default m-btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill m-btn--hover-primary"
                                             onclick="openLoanPaymentHistoryModal(${row.id})">
                                        <i class="fa fa-list-ol"></i>
                                     </button> `;
                            listActions += `<li class="m-nav__item">
                                <a href="javascript:void(0)" class="m-nav__link"
                                onclick="openLoanPaymentHistoryModal(${row.id})">
                                    <i class="m-nav__link-icon flaticon-list"></i>
                                    <span class="m-nav__link-text">PAYMENT HISTORY</span>
                                </a>
                            </li>`;
                            ctrActions++;
                        }

                        if (typeof _currentActions != "undefined" && _currentActions.includes("archive")) {
                            btn += `<button title="Archive"
                                        onclick="remove_loan(${row.id})"
                                             class="btn btn-default m-btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill m-btn--hover-danger">
                                         <i class="fa fa-archive"></i>
                                     </button> `;
                            listActions += `<li class="m-nav__item">
                                <a href="javascript:void(0)" class="m-nav__link"
                                onclick="remove_loan(${row.id})">
                                    <i class="m-nav__link-icon flaticon-interface-2"></i>
                                    <span class="m-nav__link-text">ARCHIVE LOAN</span>
                                </a>
                            </li>`;
                            ctrActions++;
                        }

                        const _tempAction = `<div class="m-dropdown m-dropdown--inline m-dropdown--align-right m-dropdown--large"
                                data-dropdown-toggle="click" aria-expanded="true">
                            <a href="#" class="m-dropdown__toggle btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill"
                                data-toggle="m-tooltip" data-original-title="More Options" data-skin="dark"
                                data-delay='{"show": 500}'>
                                <i class="fa fa-ellipsis-v"></i>
                            </a>
                            <div class="m-dropdown__wrapper">
                                <span class="m-dropdown__arrow m-dropdown__arrow--right"></span>
                                <div class="m-dropdown__inner">
                                    <div class="m-dropdown__body">
                                        <div class="m-dropdown__content">
                                            <ul class="m-nav">
                                                <li class="m-nav__section m-nav__section--first">
                                                    <span class="m-nav__section-text">OPTIONS</span>
                                                </li>
                                                ${listActions}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                        if(ctrActions > 1){ btn = _tempAction; }
                        if(ctrActions == 0){ btn = '--'; }
                        return btn;
                    }
                },
            ],
            initComplete: function () {
                $('#generalSearchLoans').donetyping(function(callback) {
                    generalLoanSearchHistory = $(this).val();
                    dtLoans.ajax.reload();
                });
            }, drawCallback: function(){
                setTimeout(getCAReferences(), 750);
            }
        });
    } 

    const dtTablePaidLoanHitory = function(){
        return $("#tbl-deduction-history").DataTable({
            dom: 'frtlip',
            serverSide: true,
            processing: true,
            autoWidth: false,
            ordering: false,
            searching: false,
            ajax: {
                url: "<?php echo base_url("payroll/employee/get_employee_loans_history");?>",
                type: "post",
                dataType: "json",
                global: false,
                data: function (d) {d.csrf_token = _csrf_hash, d.emp_id = <?php echo $data->id; ?>, d.search['value'] = generalPaidLoanSearchHistory }
            },
            columns: [
                {
                    data: "loan_name",
                    width: "*",
                    render: function (data, type, row) {
                        var ref, mergeState=``, dnRefs=``;
                        if(row.reference === '' || row.reference === null){ ref = ``; }
                        else{ ref = `<p class='m-0'><small><span class="m--font-bolder">Reference:</span> ${row.reference}</small></p>`; }

                        if(parseInt(row.merged_count) > 0){
                            mergeState = `<span class="m-badge m-badge--wide m-badge--warning text-white m--margin-left-15 m--regular-font-size-sm5">Merged</span>`;
                        }

                        if(typeof row.debit_note != "undefined" && row.debit_note){
                            dnRefs = `<span class='m--font-primary m--font-boldest m--margin-left-15 m--regular-font-size-lg1'>${row.debit_note}</span>`;
                        }

                        let tempHtml = `<p class="mb-1 m--font-bolder">${data} ${dnRefs} ${mergeState}</p>`+ref+`
                        <p class='m-0'><small><span class="m--font-bolder">Created By:</span> ${row.created_by}</small></p>
                        <p class='m-0'><small><span class="m--font-bolder">Created Date:</span> ${row.created_at}</small></p>`;

                        return tempHtml;
                    }
                },
                {
                    data: "amount",
                    className: "text-right",
                    width: "14%",
                    render: function (data) {
                        return `<span class="m--font-boldest">
                                    ${parseFloat(data).toLocaleString('en-US', {maximumFractionDigits: 2})}
                                </span>`;
                    }
                },
                {
                    data: "total_amount_paid",
                    className: "text-right m--padding-right-30",
                    width: "10%",
                    render: function (data) {
                        return `<span class="m--font-boldest">
                                    ${parseFloat(data).toLocaleString('en-US', {maximumFractionDigits: 2})}
                                </span>`;
                    }
                },
                {
                    data: null,
                    className: "text-right m--padding-right-30",
                    width: "10%",
                    render: function (data, type, row) {
                        const balance = parseFloat(row.amount) - parseFloat(row.total_amount_paid);
                        return `<span class="m--font-boldest">
                                    ${parseFloat(balance).toLocaleString('en-US', {maximumFractionDigits: 2})}
                                </span>`;
                    }
                },
                {
                    data: "deduction_type",
                    width: "10%",
                    render: function (data, type, row) {
                        return parseInt(data) === 0 ? "Percentage" : "Fix Amount";
                    }
                },
                {
                    data: null,
                    width: "8%",
                    render: function (data, type, row) {
                        if (parseInt(row.deduction_type) === 0) {
                            return parseFloat(row.percentage).toLocaleString('en-US', {maximumFractionDigits: 2}) + "" + "%";
                        } else {
                            return parseFloat(row.fixed_deduction_amt).toLocaleString('en-US', {maximumFractionDigits: 2});
                        }
                    }
                },{
                    data: "active",
                    className: "text-center",
                    width: "10%",
                    render: function (data, type, row) {
                        let tempStatus = parseInt(data);
                        let badgeColor = "m-badge--warning";
                        let badgeText = "Suspended";
                        if(row.paid == 1 && tempStatus !== 2){ tempStatus = 2; }

                        const balance = parseFloat(row.amount) - parseFloat(row.total_amount_paid);
                        if(balance <= 0){ tempStatus = 2; }

                        switch (tempStatus) {
                            case 1:
                                badgeColor = "m-badge--info";
                                badgeText = "Active";
                                break;
                            case 2:
                                badgeColor = "m-badge--success";
                                badgeText = "Paid";
                                break;
                            default:
                                badgeColor = "m-badge--warning";
                                badgeText = "Suspended";
                                break;
                        }
                        return `<span class="m-badge m-badge--wide m--font-bolder ${badgeColor}" style="width: 75%;">${badgeText}</span>`;
                    }
                },
                {
                    width: "7%",
                    data: null,
                    className: "text-center",
                    orderable: false,
                    render: function (data, type, row) {
                        let btn = ``;
                        let ctrActions = 0;
                        let listActions = ``;
                        const isPaid = parseInt(row.paid);

                        let tempIsPaid = false;
                        const balance = parseFloat(row.amount) - parseFloat(row.total_amount_paid);
                        if(balance <= 0){ tempIsPaid = true; }

                        if (typeof _currentActions != "undefined" && _currentActions.includes("edit") && (isPaid !== 1 && tempIsPaid === false)) {
                            btn += `<button title="Edit"
                                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill m-btn--hover-primary"
                                    onclick="openEditEmployeeLoanModal(${row.id})">
                            <i class="fa fa-pencil"></i>
                            </button> `;
                            listActions += `<li class="m-nav__item">
                                <a href="javascript:void(0)" class="m-nav__link"
                                onclick="openEditEmployeeLoanModal(${row.id})">
                                    <i class="m-nav__link-icon flaticon-coins"></i>
                                    <span class="m-nav__link-text">EDIT LOAN</span>
                                </a>
                            </li>`;
                            ctrActions++;
                        }

                        if(typeof _currentActions != "undefined" && _currentActions.includes('view')){
                            btn += `<button title="View payment history"
                                                class="btn btn-default m-btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill m-btn--hover-primary"
                                                onclick="openLoanAttachmentModal(${row.id})">
                                        <i class="la la-image"></i>
                                        </button> `;
                            listActions += `<li class="m-nav__item">
                                <a href="javascript:void(0)" class="m-nav__link"
                                onclick="openLoanAttachmentModal(${row.id})">
                                    <i class="m-nav__link-icon la la-image"></i>
                                    <span class="m-nav__link-text">Attachments</span>
                                </a>
                            </li>`;
                            ctrActions++;
                        }

                        if (typeof _currentActions != "undefined" && _currentActions.includes("view")) {
                            btn += `<button title="View payment history"
                                                class="btn btn-default m-btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill m-btn--hover-primary"
                                                onclick="openLoanPaymentHistoryModal(${row.id})">
                                        <i class="fa fa-list-ol"></i>
                                        </button> `;
                            listActions += `<li class="m-nav__item">
                                <a href="javascript:void(0)" class="m-nav__link"
                                onclick="openLoanPaymentHistoryModal(${row.id})">
                                    <i class="m-nav__link-icon flaticon-list"></i>
                                    <span class="m-nav__link-text">PAYMENT HISTORY</span>
                                </a>
                            </li>`;
                            ctrActions++;
                        }

                        if (typeof _currentActions != "undefined" && _currentActions.includes("archive")) {
                            btn += `<button title="Archive"
                                        onclick="remove_loan(${row.id})"
                                                class="btn btn-default m-btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill m-btn--hover-danger">
                                            <i class="fa fa-archive"></i>
                                        </button> `;
                            listActions += `<li class="m-nav__item">
                                <a href="javascript:void(0)" class="m-nav__link"
                                onclick="remove_loan(${row.id})">
                                    <i class="m-nav__link-icon flaticon-interface-2"></i>
                                    <span class="m-nav__link-text">ARCHIVE LOAN</span>
                                </a>
                            </li>`;
                            ctrActions++;
                        }

                        const _tempAction = `<div class="m-dropdown m-dropdown--inline m-dropdown--align-right m-dropdown--large"
                                data-dropdown-toggle="click" aria-expanded="true">
                            <a href="#" class="m-dropdown__toggle btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill"
                                data-toggle="m-tooltip" data-original-title="More Options" data-skin="dark"
                                data-delay='{"show": 500}'>
                                <i class="fa fa-ellipsis-v"></i>
                            </a>
                            <div class="m-dropdown__wrapper">
                                <span class="m-dropdown__arrow m-dropdown__arrow--right"></span>
                                <div class="m-dropdown__inner">
                                    <div class="m-dropdown__body">
                                        <div class="m-dropdown__content">
                                            <ul class="m-nav">
                                                <li class="m-nav__section m-nav__section--first">
                                                    <span class="m-nav__section-text">OPTIONS</span>
                                                </li>
                                                ${listActions}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                        if(ctrActions > 1){ btn = _tempAction; }
                        if(ctrActions == 0){ btn = '--'; }
                        return btn;
                    }
                },

            ],
            initComplete: function () {
                $('#generalSearchPaidLoans').donetyping(function(callback) {
                    generalPaidLoanSearchHistory = $(this).val();
                    dtPaidLoanHistory.ajax.reload();
                });
            }
        });
    }

    const dtTableCancelledLoanHistory = function(){
        return $("#tbl-deduction-cancelled").DataTable({
            dom: 'frtlip',
            serverSide: true,
            processing: true,
            autoWidth: false,
            ordering: false,
            searching: false,
            ajax: {
                url: "<?php echo base_url("payroll/employee/get_employee_loans_history"); ?>",
                type: "post",
                dataType: "json",
                global: false,
                data: function (d) {
                    d.csrf_token = _csrf_hash; 
                    d.emp_id = <?php echo $data->id; ?>; 
                    d.search['value'] = generalCancelledSearchHistory;
                    d.loan_status = 3; }
            },
            columns: [
                {
                    data: "loan_name",
                    width: "*",
                    render: function (data, type, row) {
                        var ref, mergeState=``, mergedAmount = ``, dnRefs = ``;
                        if(row.reference === '' || row.reference === null){
                            ref = ``;
                        }else{
                            ref = `<p class='m-0'><small><span class="m--font-bolder">Reference:</span> ${row.reference}</small></p>`;
                        }

                        if(parseInt(row.merged_id) > 0){
                            mergeState = `<span class="m-badge m-badge--wide m-badge--warning text-white m--margin-left-15 m--regular-font-size-sm5">Merged</span>`;
                            if(parseFloat(row.merged_amount) > 0){
                                const tempAmount = numberFormat(row.merged_amount);
                                mergedAmount = `<p class='mt-2 mb-2'><small><span class="m--font-bolder">Merged to Loan Amount:</span></small>
                                    <span class="m--font-danger m--font-boldest m--margin-left-15 m--regular-font-size-lg2">${tempAmount}</span></p>`;
                            }
                        }

                        if(typeof row.debit_note != "undefined" && row.debit_note){
                            dnRefs = `<span class='m--font-primary m--font-boldest m--margin-left-15 m--regular-font-size-lg1'>${row.debit_note}</span>`;
                        }

                        let tempHtml = `<p class="mb-1 m--font-bolder">${data} ${dnRefs} ${mergeState}</p>
                            ${mergedAmount}${ref}
                            <p class='m-0'><small><span class="m--font-bolder">Created By:</span> ${row.created_by}</small></p>
                            <p class='m-0'><small><span class="m--font-bolder">Created Date:</span> ${row.created_at}</small></p>`;

                        return tempHtml;
                    }
                },
                {
                    data: "amount",
                    className: "text-right",
                    width: "14%",
                    render: function (data) {
                        return `<span class="m--font-boldest">
                                    ${parseFloat(data).toLocaleString('en-US', {maximumFractionDigits: 2})}
                                </span>`;
                    }
                },
                {
                    data: "total_amount_paid",
                    className: "text-right m--padding-right-30",
                    width: "10%",
                    render: function (data) {
                        return `<span class="m--font-boldest">
                                    ${parseFloat(data).toLocaleString('en-US', {maximumFractionDigits: 2})}
                                </span>`;
                    }
                },
                {
                    data: null,
                    className: "text-right m--padding-right-30",
                    width: "10%",
                    render: function (data, type, row) {
                        const balance = parseFloat(row.amount) - parseFloat(row.total_amount_paid);
                        return `<span class="m--font-boldest">
                                    ${parseFloat(balance).toLocaleString('en-US', {maximumFractionDigits: 2})}
                                </span>`;
                    }
                },
                {
                    data: "deduction_type",
                    width: "10%",
                    render: function (data, type, row) {
                        return parseInt(data) === 0 ? "Percentage" : "Fix Amount";
                    }
                },
                {
                    data: null,
                    width: "8%",
                    render: function (data, type, row) {
                        if (parseInt(row.deduction_type) === 0) {
                            return parseFloat(row.percentage).toLocaleString('en-US', {maximumFractionDigits: 2}) + "" + "%";
                        } else {
                            return parseFloat(row.fixed_deduction_amt).toLocaleString('en-US', {maximumFractionDigits: 2});
                        }
                    }
                },{
                    data: "active",
                    className: "text-center",
                    width: "10%",
                    render: function (data, type, row) {
                        let tempStatus = parseInt(data);
                        let badgeColor = "m-badge--warning";
                        let badgeText = "Suspended";
                        if(row.paid == 1 && tempStatus < 2){ tempStatus = 2; }

                        const balance = parseFloat(row.amount) - parseFloat(row.total_amount_paid);
                        if(balance <= 0){ tempStatus = 2; }

                        switch (tempStatus) {
                            case 1:
                                badgeColor = "m-badge--info";
                                badgeText = "Active";
                                break;
                            case 2:
                                badgeColor = "m-badge--success";
                                badgeText = "Paid";
                                break;
                            case 3:
                                badgeColor = "m-badge--metal text-white";
                                badgeText = "Cancelled";
                                break;
                            default:
                                badgeColor = "m-badge--warning";
                                badgeText = "Suspended";
                                break;
                        }
                        return `<span class="m-badge m-badge--wide m--font-bolder ${badgeColor}" style="width: 75%;">${badgeText}</span>`;
                    }
                },
                {
                    width: "7%",
                    data: null,
                    className: "text-center",
                    orderable: false,
                    render: function (data, type, row) {
                        let btn = ``;
                        let ctrActions = 0;
                        let listActions = ``;
                        const isPaid = parseInt(row.paid);

                        let tempIsPaid = false;
                        const balance = parseFloat(row.amount) - parseFloat(row.total_amount_paid);
                        if(balance <= 0){ tempIsPaid = true; }

                        if (typeof _currentActions != "undefined" && _currentActions.includes("view")) {
                            btn += `<button title="View payment history"
                                                class="btn btn-default m-btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill m-btn--hover-primary"
                                                onclick="openLoanPaymentHistoryModal(${row.id})">
                                        <i class="fa fa-list-ol"></i>
                                        </button> `;
                            listActions += `<li class="m-nav__item">
                                <a href="javascript:void(0)" class="m-nav__link"
                                onclick="openLoanPaymentHistoryModal(${row.id})">
                                    <i class="m-nav__link-icon flaticon-list"></i>
                                    <span class="m-nav__link-text">PAYMENT HISTORY</span>
                                </a>
                            </li>`;
                            ctrActions++;
                        }

                        if (typeof _currentActions != "undefined" && _currentActions.includes("archive")) {
                            btn += `<button title="Archive"
                                        onclick="remove_loan(${row.id})"
                                                class="btn btn-default m-btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill m-btn--hover-danger">
                                            <i class="fa fa-archive"></i>
                                        </button> `;
                            listActions += `<li class="m-nav__item">
                                <a href="javascript:void(0)" class="m-nav__link"
                                onclick="remove_loan(${row.id})">
                                    <i class="m-nav__link-icon flaticon-interface-2"></i>
                                    <span class="m-nav__link-text">ARCHIVE LOAN</span>
                                </a>
                            </li>`;
                            ctrActions++;
                        }

                        const _tempAction = `<div class="m-dropdown m-dropdown--inline m-dropdown--align-right m-dropdown--large"
                                data-dropdown-toggle="click" aria-expanded="true">
                            <a href="#" class="m-dropdown__toggle btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill"
                                data-toggle="m-tooltip" data-original-title="More Options" data-skin="dark"
                                data-delay='{"show": 500}'>
                                <i class="fa fa-ellipsis-v"></i>
                            </a>
                            <div class="m-dropdown__wrapper">
                                <span class="m-dropdown__arrow m-dropdown__arrow--right"></span>
                                <div class="m-dropdown__inner">
                                    <div class="m-dropdown__body">
                                        <div class="m-dropdown__content">
                                            <ul class="m-nav">
                                                <li class="m-nav__section m-nav__section--first">
                                                    <span class="m-nav__section-text">OPTIONS</span>
                                                </li>
                                                ${listActions}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                        if(ctrActions > 1){ btn = _tempAction; }
                        if(ctrActions == 0){ btn = '--'; }
                        return btn;
                    }
                },

            ],
            initComplete: function () {
                $('#generalSearchCancelledLoans').donetyping(function(callback) {
                    generalCancelledSearchHistory = $(this).val();
                    dtCancelledLoanHistory.ajax.reload();
                });
            }
        });
    }

    const dtTableHistoryPayrollInfo = function () {
        return $("#tbl-payroll_history").DataTable({
            dom: 'frtlip',
            serverSide: true,
            processing: true,
            autoWidth: false,
            ordering: false,
            searching: false,
            ajax: {
                url: "<?php echo base_url("payroll/employee/get_history_payroll_information"); ?>",
                type: "post",
                dataType: "json",
                global: false,
                data: function (d) {
                    d.csrf_token = _csrf_hash; 
                    d.emp_id = <?php echo $data->id; ?>;
                    d.search["value"] = generalSearchHistory;
                }
            },
            columns: [{
                    data: "log_message",
                    width: "*",
                },{
                    data: "user_action",
                    width: "15%",
                },{
                    data: "employee_name",
                    width: "25%",
                    render: function (data, meta, row) {
                        const html = `<p class='mb-0'>${data}</p>
                        <small><span class='m--font-boldest'>${row.created_at_formatted}</span></small>`;
                        return html;
                    }
                },

            ],
            initComplete: function (_settings, json) {
                vmPayInfo.psInfoCtr = json.recordsTotal;

                $('#generalSearchHistory').donetyping(function(callback) {
                    generalSearchHistory = $(this).val();
                    dtHistoryPayrollInfo.ajax.reload();
                });
            }
        });
    }

    function openLoanPaymentHistoryModal(id) {
        loanPaymentHistoryModal.attr("data-id", id);
        loanPaymentHistoryModal.modal("show");
    }

    function openLoanAttachmentModal(id){
        loanAttachmentModal.attr("data-id", id);
        loanAttachmentModal.modal("show");
    }

    loanAttachmentModal.on("show.bs.modal", function () {
        const id = $(this).attr("data-id");
        $("table", this).DataTable({
            dom : "frtlp",
            serverSide: false,
            destroy: true,
            ajax:{
                url: baseUrl(`payroll/employee/get_employee_attachments/${id}`),
                type: "GET",
                dataType: "JSON",
                data: { emp_id : <?=$data->id ?> }
            },
            autoWidth: false,
            columns:[
                {
                    width: "30%",
                    data: null,
                    render: function(data, type, row){
                        return `<a href="${row.image}" data-lightbox="photos">
                                <img class="img-fluid" src="${row.thumbnail}">`;
                    }
                },
                {
                    data: "name"
                },
                {
                    data: "uploaded_dt"
                }
            ]
        });
    });

    loanPaymentHistoryModal.on("show.bs.modal", function () {
        const id = $(this).attr("data-id");
        $("#tab_payments table", this)
            .DataTable({
                dom: "frtlp",
                serverSide: false,
                destroy: true,
                ordering: false,
                ajax: {
                    url: baseUrl(`payroll/employee/get_employee_loan_payment_history/${id}`),
                    type: "GET",
                    dataType: "JSON"
                },
                autoWidth: false,
                columns: [
                    {
                        data: "pay_date",
                        render: function (data, type, row) {
                            return `<span class="m--font-boldest">${moment(row.date_start).format("MMM. DD, YYYY")}</span>`
                                + " - " + `<span class="m--font-boldest">${moment(row.date_end).format("MMM. DD, YYYY")}</span>`;
                        }
                    },
                    {
                        width: "30%",
                        data: null,
                        render: function (data, type, row) {
                            return `<div class="m--font-bolder">${row.firstname} ${row.lastname}</div>
                                    <div class="m--regular-font-size-sm1 text-muted">${moment(row.posted_at).format("lll")}</div>`;
                        }
                    },
                    {
                        width: "20%",
                        data: "amount_due",
                        className: "text-right",
                        render: function (data, type, row) {
                            return `<span class="m--font-boldest">
                                        ${parseFloat(data).toLocaleString("en-US", {maximumFractionDigits: 2})}
                                    </span>`;
                        }
                    },
                ],
                footerCallback: function (row, data, start, end, display) {
                    const api = this.api();
                    const total = api
                        .column(2)
                        .data()
                        .reduce(function (a, b) {
                            return parseFloat(a) + parseFloat(b);
                        }, 0);

                    $(api.column(2).footer()).html(
                        `<span class="m--font-boldest m--regular-font-size-lg1">
                            ${parseFloat(total).toLocaleString("en-US", {maximumFractionDigits: 2})}
                        </span>`
                    );
                }
            });
        
        $("#tab_interest_charges table", this)
            .DataTable({
                dom: "frtlp",
                serverSide: false,
                destroy: true,
                ordering: false,
                ajax: {
                    url: baseUrl(`payroll/employee/get_employee_loan_iterest_charge_history/${id}`),
                    type: "GET",
                    dataType: "JSON"
                },
                autoWidth: false,
                columns: [
                    {
                        data: "pay_date",
                        render: function (data, type, row) {
                            return `<span class="m--font-boldest">${moment(row.date_start).format("MMM. DD, YYYY")}</span>`
                                + " - " + `<span class="m--font-boldest">${moment(row.date_end).format("MMM. DD, YYYY")}</span>`;
                        }
                    },
                    {
                        width: "30%",
                        data: null,
                        render: function (data, type, row) {
                            return `<div class="m--font-bolder">${row.firstname} ${row.lastname}</div>
                                    <div class="m--regular-font-size-sm1 text-muted">${moment(row.posted_at).format("lll")}</div>`;
                        }
                    },
                    {
                        width: "15%",
                        data: "amount_due",
                        className: "text-right",
                        render: function (data, type, row) {
                            return `<span class="m--font-boldest">
                                        ${parseFloat(data).toLocaleString("en-US", {maximumFractionDigits: 2})}
                                    </span>`;
                        }
                    },
                    {
                        width: "15%",
                        data: "total_interest_amount",
                        className: "text-right",
                        render: function (data, type, row) {
                            return `<span class="m--font-boldest">
                                        ${parseFloat(data).toLocaleString("en-US", {maximumFractionDigits: 2})}
                                    </span>`;
                        }
                    },
                ],
                footerCallback: function (row, data, start, end, display) {
                    const api = this.api();
                    const total = api
                        .column(3)
                        .data()
                        .reduce(function (a, b) {
                            return parseFloat(a) + parseFloat(b);
                        }, 0);

                    $(api.column(3).footer()).html(
                        `<span class="m--font-boldest m--regular-font-size-lg1">
                            ${parseFloat(total).toLocaleString("en-US", {maximumFractionDigits: 2})}
                        </span>`
                    );
                }
            });

        $.ajax({
            url : baseUrl(`payroll/employee/get_employee_loan_remarks/${id}`),
            type: "GET",
            dataType: "JSON",
            success: function(response){
                $("#_for_remarks").text(response.remarks);
            }
        });
    });

    function openEditEmployeeBenefitModal(id, rate, benefit_id) {
        $("#id", editEmployeeBenefitsModal).val(id);
        $("input[name='rate']", editEmployeeBenefitsModal).val(rate);
        $("#update_benefit_id", editEmployeeBenefitsModal).val(benefit_id);
        editEmployeeBenefitsModal.modal("show");
    }

    var vmMergeLoan = new Vue({
        el: "#mergeLoan-content",
        data: { row: {} }, 
        methods: {
            countMergeableLoan(){
                const { to_merge_loans } = this.row;
                return typeof to_merge_loans !== "undefined" && to_merge_loans.length > 0 ? to_merge_loans.length: 0;
            }
        }
    });

    function openMergeEmployeeLoanModal(id){
        if(id){
            $.ajax({
                url: baseUrl(`payroll/employee/get_employee_to_merge_loan/${id}`),
                type: "GET",
                dataType: "JSON",
                success: function (json) {
                    vmMergeLoan.row = Object.assign({});
                    if(json.response){
                        if(typeof modalMergeLoan !== "undefined" && modalMergeLoan.length == 1){
                            vmMergeLoan.row = Object.assign({}, json.data);
                            const tempForm = modalMergeLoan.find("frm-mergeLoan");
                            modalMergeLoan.modal("show");
                        }
                    }
                }
            });
        }else{

        }
    }

    function openEditEmployeeLoanModal(id) {
        $.ajax({
            url: baseUrl(`payroll/employee/get_employee_loan/${id}`),
            type: "GET",
            dataType: "JSON",
            beforeSend: function(){
                $("#frm-edit-employee-loan").trigger('reset');
                $("input[name='active']", editEmployeeLoan).prop("checked", false);
            },
            success: function (response) {
                $("#frm-edit-employee-loan").attr("action", baseUrl(`payroll/employee/update_employee_loan/${response.id}`));
                /*** const option = new Option(response.loan_name, response.loan_id, false, true);
                $("#loan_id-edit").append(option); ***/
                
                if(typeof response.loan_id != "undefined" && response.loan_id){
                    $("#loan_id-edit").val(response.loan_id).trigger("change");
                }

                /*** if(response.reference != null && response.ca_id != 0){
                    const option_ref = new Option(response.reference, response.ca_id, false, true);
                    $("#ca_ref-edit").append(option_ref);
                } ***/

                const caHasReference = response.has_ref != 0 || response.reference != '' && response.reference != null;
                vmEditLoanRefs.hasrefs = caHasReference;
                vmEditLoanRefs.reference = response.reference;
                vmEditLoanRefs.reference_id = response.reference_id;
                if(caHasReference){
                    setTimeout(function(){ vmEditLoanRefs.setReferenceCA(); }, 250);
                }

                const deduction_type = parseInt(response.deduction_type);
                const amount = parseFloat(response.amount).toLocaleString('en-US', {maximumFractionDigits: 2});
                $("input[name='amount']", editEmployeeLoan)
                    .maskMoney({
                        prefix: '',
                        allowNegative: false,
                        thousands: ',',
                        decimal: '.',
                        affixesStay: false
                    })
                    .val(amount);


                const deduct_type_value = parseFloat(deduction_type === 0 ? response.percentage : response.fixed_deduction_amt).toLocaleString('en-US', {maximumFractionDigits: 2});
                if (deduction_type == 0) {
                    $("input[name='deduct_type_value']", editEmployeeLoan)
                    .maskMoney({
                        prefix: '',
                        allowNegative: false,
                        thousands: ',',
                        affixesStay: false,
                        decimal: '.',
                        precision: 0
                    })
                    .val(deduct_type_value);
                }else{
                    $("input[name='deduct_type_value']", editEmployeeLoan)
                    .maskMoney({
                        prefix: '',
                        allowNegative: false,
                        thousands: ',',
                        affixesStay: false,
                        decimal: '.',
                        precision: 2
                    })
                    .val(deduct_type_value);
                }

                
                const has_interest = parseFloat(response.interest_percentage) > 0;
                if(has_interest){  $("#has_interest_charge", editEmployeeLoan).removeClass("m--hide"); }
                else{
                    const hasClassHidden = $("#has_interest_charge", editEmployeeLoan).hasClass("m--hide");
                    if(!hasClassHidden){ $("#has_interest_charge", editEmployeeLoan).addClass("m--hide"); }
                }
                $("input[name='deduction_type'][value='" + response.deduction_type + "']", editEmployeeLoan).prop('checked', true).closest('label').addClass('m--checked');
                $("input[name='active'][value='" + response.active + "']", editEmployeeLoan).prop('checked', true);
                $("input[name='last_interest_charge'][value='" + response.last_interest_charge + "']", editEmployeeLoan).prop('checked', true);
                $("#for_remarks").text(response.remarks);
                if(typeof response.debit_note != "undefined" && response.debit_note){
                    $("#debit_note", editEmployeeLoan).val(response.debit_note);
                }
                editEmployeeLoan.modal("show");
            }
        });
    }

    $.validate({
        form: "#frm-edit-employee-loan",
        lang: 'en',
        scrollToTopOnError: false,
        onSuccess: function (form) {
            const url = $(form).attr("action");
            const formData = new FormData(form[0]);
            formData.append("csrf_token", _csrf_hash);
            formData.append("emp_id", <?php echo $data->id; ?>);
            $.ajax({
                url,
                type: "POST",
                dataType: "JSON",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response) {
                        if (response.success) {
                            const toast = response.toast;
                            toastr[toast](response.message, response.title, {timeOut: 10000});
                            dtLoans.ajax.reload();
                            $(".gallery").html('');
                            $("#file2").val('');
                        }
                    }

                    vmEditLoanRefs.hasrefs = false;
                    vmEditLoanRefs.reference = null;
                    vmEditLoanRefs.reference_id = 0;

                    editEmployeeLoan.modal("hide");
                }
            });

            return false;
        }
    });

    $("#allowance_id")
        .select2({
            width: "100%",
            placeholder: "Select option",
            dropdownParent: $("#mdl-newAllowance"),
            ajax: {
                url: baseUrl("payroll/employee/get_allowance_collection/") +<?php echo $data->id; ?>,
                dataType: "json",
                delay: 500,
                global: false,
                processResults: function (data) {
                    return data;
                }
            }
        });

    $("#benefit_id").select2({
        width: "100%",
        placeholder: "Select option",
        dropdownParent: $("#mdl-newBenefit"),
        ajax: {
            url: baseUrl("payroll/employee/get_benefit_collection/") +<?php echo $data->id; ?>,
            dataType: "json",
            delay: 500,
            global: false,
            processResults: function (data) {
                return data;
            }
        }
    });

    $("#update_benefit_id").select2({
        width: "100%",
        placeholder: "Select option",
        dropdownParent: $("#edit-employee-benefits-modal"),
        ajax: {
            url: baseUrl("payroll/employee/get_benefit_collection/")+<?php echo $data->id; ?>,
            dataType: "json",
            delay: 500,
            global: false,
            processResults: function (data) {
            return data;
            }
        }
    });

    addEmployeeLoan.on("show.bs.modal", function () {
        $("#loan_id", addEmployeeLoan).val("").trigger("change");
        vmNewLoanRefs.hasrefs = false;
        vmNewLoanRefs.reference = null;

        $("#frm-newLoan", addEmployeeLoan)[0].reset();

        $("#loan_amount")
        .maskMoney({
            prefix: '',
            allowNegative: true,
            thousands: ',',
            decimal: '.',
            affixesStay: false
        });

        $("input[name='deduct_type_value']", addEmployeeLoan).maskMoney({
            prefix: '',
            allowNegative: false,
            affixesStay: false,
            decimal: '.',
            precision: 0
        }).val(20);
    });

    const vmNewLoanRefs = new Vue({
        el: "#hasReferenceLoans",
        data: { hasrefs: false, reference: null },
        methods: {
            setReferenceCA: function(){
                const _this = this;
                const { $el } = _this;
                const { results } = loansCAReference;

                const currentElement = $($el);
                _this.reference = null;

                const caRefs = currentElement.find("#ca_reference");
                if(typeof caRefs != "undefined" && caRefs.length == 1){
                    if(caRefs.hasClass("select2-hidden-accessible") == true){ caRefs.select2("destroy"); }

                    caRefs.select2({
                        width: "100%",
                        placeholder: "Select option",
                        dropdownParent: addEmployeeLoan,
                        data: results,
                        escapeMarkup: function (markup) { return markup; }, 
                        templateResult: function (data) { return data.html; }, 
                        templateSelection: function (data) { return data.text; }
                    }).on('select2:select', function(e){
                        const data = e.params.data;
                        _this.reference = data.refnum;
                    });

                    currentElement.find(".row.m--hide").removeClass("m--hide");
                }
            }
        }
    });

    const vmEditLoanRefs = new Vue({
        el: "#hasReferenceLoansEdit",
        data: { hasrefs: false, reference: null, reference_id: 0 },
        methods: {
            setReferenceCA: function(){
                const _this = this;
                const { $el, reference, reference_id } = _this;
                const { results } = loansCAReference;
                const currentElement = $($el);

                const caRefs = currentElement.find("#ca_reference");
                if(typeof caRefs != "undefined" && caRefs.length == 1){
                    if(caRefs.hasClass("select2-hidden-accessible") == true){ 
                        caRefs.empty().select2("destroy"); 
                    }

                    caRefs.select2().empty();
                    caRefs.select2({
                        width: "100%",
                        placeholder: "Select option",
                        dropdownParent: editEmployeeLoan,
                        data: results,
                        escapeMarkup: function (markup) { return markup; }, 
                        templateResult: function (data) { return data.html; }, 
                        templateSelection: function (data) { return data.text; }
                    }).on('select2:select', function(e){
                        const data = e.params.data;
                        _this.reference = data.refnum;
                    });

                    if(reference_id){ caRefs.val(reference_id).trigger("change"); }
                    currentElement.find(".row.m--hide").removeClass("m--hide");
                }

            }
        }
    });

    $("#loan_id").select2({
        width: "100%",
        placeholder: "Select option",
        dropdownParent: $("#mdl-newLoan"),
        data: loansDropdown.results,
        /*** ajax: {
            url: baseUrl("payroll/employee/get_loan_collection/") +<?php echo $data->id; ?>,
            dataType: "json",
            delay: 500,
            global: false,
            processResults: function (data) {
                return data;
            }
        } ***/
    }).on("select2:select", function(e){
        const { data } = e.params;
        vmNewLoanRefs.hasrefs = parseInt(data.has_ref) == 1;
        setTimeout(function(){
            vmNewLoanRefs.setReferenceCA();
        }, 250);
    });

    /*** $("#loan_id").on('change', function(){
        var id = $(this).val();
        $.ajax({
            url : baseUrl("payroll/employee/loan_has_ref/") + id,
            dataType: 'json',
            type: 'get',
            success: function(data){
                if(data.has_ref == 1){
                    $("#has_ref").show();
                }else{
                    $("#has_ref").hide();
                }
            }
        });
    }); ***/

    /*** $("#loan_id-edit").on('change', function(){
        var id = $(this).val();
        $.ajax({
            url : baseUrl("payroll/employee/loan_has_ref/") + id,
            dataType: 'json',
            type: 'get',
            success: function(data){
                if(data.has_ref == 1){
                    $("#has_ref-edit").show();
                }else{
                    $("#has_ref-edit").hide();
                }
            }
        })
    }); ***/

    $("#loan_id-edit").select2({
        width: "100%",
        placeholder: "Select option",
        dropdownParent: editEmployeeLoan,
        data: loansDropdown.results,
    }).on("select2:select", function(e){
        const { data } = e.params;
        vmEditLoanRefs.hasrefs = parseInt(data.has_ref) == 1;
        setTimeout(function(){
            vmEditLoanRefs.setReferenceCA();
        }, 250);
    });

    $.validate({
        form: '#frm-newAllowance',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: form[0].action,
                type: "POST",
                data: $("#frm-newAllowance").find("input,select").serialize(),
                beforeSend: function () {
                    $(".btn-submit", form).addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data.status) {
                        toastr.success(data.response, "Notice", 5000);
                    } else {
                        toastr.error(data.response, "Notice", 5000);
                    }
                    $("#mdl-newAllowance").modal("hide");

                    $(form).resetForm();
                    $("#allowance_id").empty();
                    dtAllowance.ajax.reload();
                    $(".btn-submit", form).removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    dtHistoryPayrollInfo.ajax.reload();

                }
            });
            return false;
        },
    });

    $.validate({
        form: '#frm-edit-allowance',
        lang: 'en',
        onSuccess: function (form) {
            const approvingAuthority = typeof _tempContentData.approving_authority !== "undefined" && _tempContentData.approving_authority ? 
            _tempContentData.approving_authority: false;

            const checkbox = $(form).find("input[type='checkbox'][name='is_active']").is(":checked");
            const isActiveState = checkbox === true ? 1: 0;

            const formData = new FormData($(form)[0]);
            formData.append("approving_authority", approvingAuthority);
            formData.set("is_active", isActiveState);

            $.ajax({
                url: $(form).attr('action'),
                type: "POST",
                dataType: "JSON",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $(".btn-submit", form).addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (response) {
                    dtAllowance.ajax.reload();
                    editEmployeeAllowanceModal.modal("hide");
                    toastr[response.toast](response.message, response.title, 10000);
                    $(".btn-submit", form).removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    if(response.for_approval){  toastr.info(response.approval_notification, "For Approval", 5000); }
                    dtHistoryPayrollInfo.ajax.reload();
                }
            });
            return false;
        },
    });

    $.validate({
        form: '#frm-edit-benefits',
        lang: 'en',
        onSuccess: function (form) {
            const formData = new FormData($(form)[0]);
            $.ajax({
                url: baseUrl("payroll/employee/update_employee_benefits"),
                type: "POST",
                dataType: "JSON",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $(".btn-submit", form).addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (response) {
                    dtBenefits.ajax.reload();
                    editEmployeeBenefitsModal.modal("hide");
                    toastr[response.toast](response.message, response.title, 10000);
                    $(".btn-submit", form).removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });

    $.validate({
        form: '#frm-newBenefit',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: form[0].action,
                type: "POST",
                data: $("#frm-newBenefit").find("input,select").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data.status) {
                        toastr.success(data.response, "Notice", 5000);
                    } else {
                        toastr.error(data.response, "Notice", 5000);
                    }
                    $("#mdl-newBenefit").modal("hide");
                    dtBenefits.ajax.reload();
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });

    $.validate({
        form: '#frm-newLoan',
        lang: 'en',
        scrollToTopOnError: false,
        onSuccess: function (form) {
            const formData = $(form).serialize();

            $.ajax({
                url: form[0].action,
                type: "POST",
                data: formData,
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data.status) {
                        $("#frm-newLoan")[0].reset();
                        toastr.success(data.response, "Notice", 5000);
                    } else {
                        toastr.error(data.response, "Notice", 5000);
                    }
                    
                    vmNewLoanRefs.hasrefs = false;
                    vmNewLoanRefs.reference = null;

                    dtLoans.ajax.reload();

                    $("#mdl-newLoan").modal("hide");
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });

    $.validate({
        form: '#frm-mergeLoan',
        lang: 'en',
        scrollToTopOnError: false,
        onSuccess: function (form) {
            const currentForm = form[0];
            const tempUrl = currentForm.action;
            const formData = $(currentForm).serialize();

            $.ajax({
                url: tempUrl,
                type: "POST",
                data: formData,
                dataType: "json",
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (json) {
                    if(json.response){
                        modalMergeLoan.modal("hide");
                        dtLoans.ajax.reload(null, false);
                        dtCancelledLoanHistory.ajax.reload(null, false);
                        toastr.success(json.toastr_msg, "Mergeable Loan", 5000);
                    }else{ toastr.error(json.toastr_msg, "Mergeable Loan", 5000); }

                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        }
    });

    function remove_allowance(id) {
        $("#mdl-removeAllowance").modal("show");
        $.validate({
            form: '#frm-removeAllowance',
            lang: 'en',
            onSuccess: function (form) {
                $.ajax({
                    url: form[0].action + '/' + id,
                    type: "POST",
                    data: $("#frm-removeAllowance").find("input").serialize(),
                    beforeSend: function () {
                        $(".btn-submit", form).addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function (data) {
                        if (data.status) {
                            toastr.success(data.response, "Notice", 5000);
                        } else {
                            toastr.error(data.response, "Notice", 5000);
                        }
                        $("#mdl-removeAllowance").modal("hide");
                        dtAllowance.ajax.reload();
                        $(".btn-submit", form).removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        dtHistoryPayrollInfo.ajax.reload();
                    }
                });
                return false;
            },
        });
    }

    function remove_benefit(id) {
        $("#mdl-removeBenefit").modal("show");
        $.validate({
            form: '#frm-removeBenefit',
            lang: 'en',
            onSuccess: function (form) {
                $.ajax({
                    url: form[0].action + '/' + id,
                    type: "POST",
                    data: $("#frm-removeBenefit").find("input").serialize(),
                    beforeSend: function () {
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function (data) {
                        if (data.status) {
                            toastr.success(data.response, "Notice", 5000);
                        } else {
                            toastr.error(data.response, "Notice", 5000);
                        }
                        $("#mdl-removeBenefit").modal("hide");
                        dtBenefits.ajax.reload();
                        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                });
                return false;
            },
        });
    }

    function remove_loan(id) {
        $("#mdl-removeLoan").modal("show");
        $.validate({
            form: '#frm-removeLoan',
            lang: 'en',
            onSuccess: function (form) {
                $.ajax({
                    url: form[0].action + '/' + id,
                    type: "POST",
                    data: $("#frm-newLoan").find("input,select").serialize(),
                    beforeSend: function () {
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function (data) {
                        if (data.status) {
                            toastr.success(data.response, "Notice", 5000);
                        } else {
                            toastr.error(data.response, "Notice", 5000);
                        }
                        $("#mdl-removeLoan").modal("hide");
                        dtLoans.ajax.reload();
                        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                });
                return false;
            },
        });
    }

    $("#frm-newLoan, #frm-edit-employee-loan")
        .on("change", "input[name='deduction_type']", function () {
            const field = $("input[name='deduct_type_value']");
            if (parseInt($(this).val()) === 0) {
                // $("#deduct_type_value_label").html("Value");
            
                $(field).maskMoney({
                    prefix: '',
                    allowNegative: false,
                    affixesStay: false,
                    decimal: '.',
                    precision: 0
                }).val(20);
                // field.val(20);
            } else {
                // $("#deduct_type_value_label").html("Amount");
                $(field).maskMoney({
                    prefix: '',
                    allowNegative: false,
                    thousands: ',',
                    decimal: '.',
                    affixesStay: false
                });
                field.val('');
            }
        });

    $(".nav-link").on('click', function(){
        var item = $(this).attr('id');
        if(item == 'tab_deduction'){
            $("#tab_deductions").addClass('active');
            $("#tab_paid_loan").removeClass('active');
            $("#tab_cancelled_loan").removeClass('active');
        }else if(item == 'tab_cancelled'){
            $("#tab_cancelled_loan").addClass('active');
            $("#tab_paid_loan").removeClass('active');
            $("#tab_deductions").removeClass('active');
        }else{
            $("#tab_paid_loan").addClass('active');
            $("#tab_deductions").removeClass('active');
            $("#tab_cancelled_loan").removeClass('active');
        }
    });

    $(".modal .nav-link").on('click', function(){
        var item = $(this).attr('id');
        if(item == 'tab_payment'){
            $("#tab_payments").addClass('active');
            $("#tab_interest_charges").removeClass('active');
        }else if(item == 'tab_interest_charge'){
            $("#tab_interest_charges").addClass('active');
            $("#tab_payments").removeClass('active');
        }
    });
    var imagesPreview = function(input, placeToInsertImagePreview) {

        if (input.files) {
            var filesAmount = input.files.length;

            $(".gallery").html('');

            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();

                reader.onload = function(event) {
                    $($.parseHTML('<a href="'+event.target.result+'" class="col-lg-4 text-center" style="margin-bottom: 10px" data-lightbox="photos"><img class="img-fluid" src="'+event.target.result+'"><a/>')).appendTo(placeToInsertImagePreview);

                    $(".remove").click(function(){
                        $(this).parent("#pip").remove();
                    });
                }

                reader.readAsDataURL(input.files[i]);
            }
        }
    };

    $('#file2').on('change', function() {
        imagesPreview(this, 'div.gallery');
    });

    /*** $("#ca_ref").select2({
        width: "100%",
        placeholder: "Select option",
        dropdownParent: addEmployeeLoan,
        ajax: {
            url: baseUrl("payroll/employee/get_ca_reference/") +<?php echo $data->id; ?>,
            dataType: "json",
            delay: 500,
            global: false,
            processResults: function (data) {
                return data;
            }
        }, escapeMarkup: function (markup) {
            return markup;
        }, templateResult: function (data) {
            return data.html;
        }, templateSelection: function (data) {
            return data.text;
        }
    }).on('select2:select', function(e){
        const data = e.params.data;
        $("#add-reference").val(data.refnum);
    });

    $("#ca_ref-edit").select2({
        width: "100%",
        placeholder: "Select option",
        dropdownParent: editEmployeeLoan,
        ajax: {
            url: baseUrl("payroll/employee/get_ca_reference/") +<?php echo $data->id; ?>,
            dataType: "json",
            delay: 500,
            global: false,
            processResults: function (data) {
                return data;
            }
        }, escapeMarkup: function (markup) {
            return markup;
        }, templateResult: function (data) {
            return data.html;
        }, templateSelection: function (data) {
            return data.text;
        }
    }).on('select2:select', function(e){
        const data = e.params.data;
        $("#edit-reference").val(data.refnum);
    }); ***/

    const getCAReferences = function(){
        $.ajax({
            url: baseUrl("payroll/employee/get_ca_reference/<?php echo $data->id; ?>"),
            dataType: "json",
            global: true,
            success: function(json){
                loansCAReference.results = [];
                $.each(json.results, function(k, v){ loansCAReference.results.push(v); });
            }
        });
    }

    const approvalPayrollData = function(id, description, value, type){
        if(id && description && value && type){
            const nType = parseInt(type);
            let approvalDesc = 'cancel';
            switch(nType){
                case 1: approvalDesc = 'approve'; break;
                case 2: approvalDesc = 'disapprove'; break;
                case 3: approvalDesc = 'cancel'; break;
                default: approvalDesc = 'cancel'; break;
            }

            const tempTitle = approvalDesc.substr(0,1).toUpperCase() + approvalDesc.substr(1);

            Swal.fire({
                title: tempTitle+'?',
                html: "Are you sure you want to "+approvalDesc+" this <strong class='m--font-danger'>`"+description+"`</strong> with value of <strong class='m--font-dark'>`"+value+"`</strong>?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, '+tempTitle+' it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: siteUrl("payroll/employee/approval_updated_payroll_data/"+nType),
                        type: "post",
                        data: { csrf_token: _csrf_hash, id: id },
                        dataType: "json",
                        success: function(json){
                            if(json.response){ 
                                toastr.success(json.toastr_msg, "For Approval");
                                dtForApproval.clear().rows.add(json.data).draw();
                            }else{ toastr.error(json.toastr_msg, "For Approval"); }
                        }
                    });
                }
            });
        }
        
    }

    const dtForApproval = $("#tbl-approval_history").DataTable({
        dom: "frtlp",
        columns: [
            { data: "field_description" },
            { data: "table_value"},
            { data: "is_approved", className: "text-center", 
                orderable: false, 
                render: function(data){
                let _status = "Pending";
                switch(data){
                    case "1": _status = "Approved"; break;
                    case "2": _status = "Disapproved"; break;
                    case "3": _status = "Cancelled"; break;
                    default: _status = "Pending"; break;
                }
                return _status;
            }},
            { data: "created_by_name",
                render: function(data, _type, row){
                    const _tempDate = moment(new Date(row.created_at), "YYYY-MM-DD H:i:s").format("LLL");
                    const _html = `<p class='mb-0'>${data}</p><p class='mb-0'><small><span class="m--font-boldest">${_tempDate}</span></small></p>`;
                    return _html;
                }
            },
            { data: "is_owner", className: "text-center", 
                orderable: false, 
                render: function(data, _type, row){
                const isOwner = parseInt(data);
                const rawData = JSON.stringify(row);
                
                let _actionCtr = 0;
                let _html = '';

                const approvingAuthority = typeof _tempContentData.approving_authority !== "undefined" && _tempContentData.approving_authority ? 
                _tempContentData.approving_authority: false;
                
                if(approvingAuthority){
                    _html += `<button id="approve_payroll_data_${row.id}" class="btn btn-sm btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-success" onclick="approvalPayrollData(${row.id}, '${row.field_description}', '${row.table_value}', '1')">
                            <i class="la la-thumbs-up"></i>
                        </button>
                        <button id="disapprove_payroll_data_${row.id}" class="btn btn-sm btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-danger" onclick="approvalPayrollData(${row.id}, '${row.field_description}', '${row.table_value}', '2')">
                            <i class="la la-thumbs-down"></i>
                        </button>`;
                }else{
                    if(isOwner){
                        _html += `<button id="cancel_payroll_data_${row.id}" class="btn btn-sm btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-warning" onclick="approvalPayrollData(${row.id}, '${row.field_description}', '${row.table_value}', '3')">
                            <i class="la la-trash"></i>
                        </button>`;
                        _actionCtr++;
                    }else{
                        _html += `---`;
                    }
                }

                return _html;
            }}
        ], drawCallback: function(settings){
            var api = new $.fn.dataTable.Api( settings );
            const isModalShown = modalForApproval.hasClass("show");
            if(isModalShown){
                const dataLength = api.data().length;
                if(dataLength === 0){ 
                    modalForApproval.modal("hide"); 
                    vmPayInfo.forApprovalCtr = 0;
                }
            }
        }
    });

    const vmSaveAction = new Vue({
        el: "#saveAndApproveAction",
        data: { approving_authority: false }
    });

    const vmSaveEditAllowanceAction = new Vue({
        el: "#editAllowanceApprovalOption",
        data: { approving_authority: false }
    });

    jQuery(document).ready(function(){
        if(typeof _tempContentData.for_approval_history !== "undefined" && _tempContentData.for_approval_history.length > 0){
            vmPayInfo.forApprovalCtr = _tempContentData.for_approval_history.length;
            dtForApproval.clear().rows.add(_tempContentData.for_approval_history).draw();
        }

        if(typeof _tempContentData.approving_authority !== "undefined" && _tempContentData.approving_authority){
            vmSaveAction.approving_authority = _tempContentData.approving_authority;
            vmSaveEditAllowanceAction.approving_authority = _tempContentData.approving_authority;
        }

        dtAllowance = dtTableAllowance();
        dtHistoryPayrollInfo = dtTableHistoryPayrollInfo();
        dtLoans = dtTableLoans();
        dtCancelledLoanHistory = dtTableCancelledLoanHistory();
        dtBenefits = dtTableBenefits();
        dtPaidLoanHistory = dtTablePaidLoanHitory();
    });

    $.validate({
        form: "#frmEditBankData",
        lang: "en",
        scrollToTopOnError: false,
        onSuccess: function(form){
            let formData = $(form).serialize();
            
            $.ajax({
                url: $(form).attr("action"),
                type: "POST",
                data: formData,
                beforeSend: function () {
                    $(".btn-submit", form).addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data.state) {
                        toastr.success(data.msg, "Notice", 5000);
                    } else {
                        if (typeof data.data != 'undefined' && data.data) {
                            const _data = data.data;
                            let html = "";
                            html += '<div>';
                                html += `<h5>Duplicate entry detected. The ATM number <b>"${_data.atm_info}"</b> is already associated with an existing account holder <b>"${_data.employee_name.toUpperCase()}"</b>.</h5>`;
                            html += '<div>';

                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                html: html
                            });

                            $("#atm_info").val($("#atm_info").val() === '' ? '' : vmBankInfo.row.atm_info);
                        }
                        toastr.error(data.msg, "Notice", 5000);
                    }
                    $(".btn-submit", form).removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });

            return false;
        }
    });
</script>