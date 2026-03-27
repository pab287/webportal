<style>
@media print {
    table tfoot {
        display: table-row-group;
    }
}
</style>
<div class="m-content">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="m-portlet mb-0">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Payslip
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <span data-toggle="modal"
                              data-target="#payroll-payslip-modal">
                            <button class="btn btn-default m-btn m-btn--icon"
                                    data-toggle="m-tooltip" data-original-title="Generate Payroll Payslip"
                                    data-skin="dark"
                                    data-delay='{"show": 500}'>
                                <span>
                                    <i class="fa fa-gears pr-1"></i>
                                    Generate Payslip
                                </span>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="row" id="generated-payslip" v-if="has_request === true">
                        <div class="col-12 col-md-12 col-lg-12 col-xl-12">
                            <h6>PAY DATE: {{request.pay_date}}</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="table-responsive-sm">
                                <table class="table table-bordered" id="table-payroll-payslip" style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="no-sort">
                                            <label class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                                                <input type="checkbox" id="cb-select-all"><span></span>
                                            </label>
                                        </th>
                                        <th scope="col">EMPLOYEE</th>
                                        <th scope="col">RATE</th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="DAYS"
                                                data-skin="dark">
                                                DAYS
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="BASIC PAY"
                                                data-skin="dark">
                                                BASIC PAY
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="UNDERTIME"
                                                data-skin="dark">
                                                UT
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="OVERTIME"
                                                data-skin="dark">
                                                OT
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="OVERTIME NIGHT DIFFERENTIAL"
                                                data-skin="dark">
                                                OT.ND
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="HOLIDAY"
                                                data-skin="dark">
                                                HOL
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="REGULAR NIGHT DIFFERENTIAL"
                                                data-skin="dark">
                                                REG.ND
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="ADJUSTMENTS"
                                                data-skin="dark">
                                                ADJUSTMENTS
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="ALLOWANCE"
                                                data-skin="dark">
                                                ALLOWANCE
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="OT ALLOWANCE"
                                                data-skin="dark">
                                                OT.ALLW
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="GROSS PAY"
                                                data-skin="dark">
                                                GROSS PAY
                                            </span>
                                        </th>
                                        <th scope="col">SSS</th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="SSS Provident"
                                                data-skin="dark">
                                                SSS PROV
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="PHILHEALTH"
                                                data-skin="dark">
                                                PHIC
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="PAG-IBIG"
                                                data-skin="dark">
                                                HDMF
                                            </span>
                                        </th>
                                        <th scope="col">TAX</th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="CASH ADVANCE"
                                                data-skin="dark">
                                                C.A
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="CHARGES"
                                                data-skin="dark">
                                                CHRG
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="SSS LOAN"
                                                data-skin="dark">
                                                SSS LOAN
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="HDMF LOAN"
                                                data-skin="dark">
                                                HDMF LOAN
                                            </span>
                                        </th>
                                        <th scope="col">
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="NET PAY"
                                                data-skin="dark">
                                                NET PAY
                                            </span>
                                        </th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th scope="col"></th>
                                            <th scope="col" colspan='22' style='text-align: right !important;'></th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="payroll-payslip-modal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Payslip</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="frm-payroll-posted" method="post" action="<?php echo site_url("payroll/generate_payroll_payslip"); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mt-2">
                            <div class="form-group m-form__group">
                                <label for="" class="required mb-1" style="font-weight: 600;">Pay Date</label>
                                <div class="input-group date" id="pay-date">
                                    <input type="text" class="form-control m-input"
                                            name="pay_date" autocomplete="off"
                                            placeholder="MMM.DD, YYYY">
                                    <span class="input-group-addon">
                                        <i class="la la-calendar-check-o"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mt-2">
                            <div class="form-group m-form__group">
                                <label for="" class="mb-1" style="font-weight: 600;">&nbsp;</label>
                                <div class="m-checkbox-list">
                                    <label class="m-checkbox">
                                        <input type="checkbox" name="is_bonus" value="1" /><label for="" class="m--font-bolder">Bonus Filter</label><span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group m-form__group">
                                <label for="company" class="required mb-1" style="font-weight: 600;">Company</label>
                                <select name="company" id="company"
                                        class="form-control" data-validation="required"></select>
                                <p class="form-control m-0" id="has_privi_company-text" disabled></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-4">
                            <div class="form-group m-form__group">
                                <label for="payroll_group" class="mb-1" style="font-weight: 600;">PAYROLL GROUP
                                    <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">( Optional )</span>
                                </label>
                                <select class="form-control" name="payroll_group[]"  id="payroll_group" multiple="multiple"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-4">
                            <div class="form-group m-form__group">
                                <label for="employees" class="mb-1" style="font-weight: 600;">Employee <span class="m-form__help" style="text-transform: none; font-width: 600;">( Optional )</span></label>
                                <select name="employees[]" id="employees" class="form-control" multiple="multiple"></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning btnReset btnAdvance_search text-white" onclick="resetFilter(this)">
                            <span>
                                <i class="fa fa-refresh "></i>
                                RESET
                            </span>
                    </button>
                    <button type="submit" class="btn btn-info m-btn m-btn--icon btnAdvance_search">
                            <span>
                                <i class="fa fa-gears pr-2"></i>
                                GENERATE
                            </span>
                    </button>
                    
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="view-payroll-payslip-modal">
    <div class="modal-dialog">
        <div class="modal-content" id="temp-payslip_content">
            <div class="modal-header">
                <h5 class="modal-title">Payslip</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php $this->load->view("payroll/payroll/modals/content/payslip_content"); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary m-btn m-btn--icon btnPrint" @click="printCurrentPayslip(row.id)">
                    <span><i class="fa fa-print mr-1"></i> PRINT</span>
                </button>
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view("modals/view_timesheet_modal"); ?>
<style>
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
@media screen and (min-width: 1200px) {
    #view-timesheet-modal .modal-dialog {
        max-width: 60%;
    }
}
</style>