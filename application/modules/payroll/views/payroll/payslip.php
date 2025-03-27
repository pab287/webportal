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
                                <table class="table table-bordered" id="table-payroll-payslip"
                                    width="100%">
                                    <thead>
                                    <tr>
                                        <th class="no-sort">
                                            <label class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                                                <input type="checkbox" id="cb-select-all"><span></span>
                                            </label>
                                        </th>
                                        <th>EMPLOYEE</th>
                                        <th>RATE</th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="DAYS"
                                                data-skin="dark">
                                                DAYS
                                            </span>
                                        </th>
                                        <!-- th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="PAY HOURS"
                                                data-skin="dark">
                                                HOURS
                                            </span>
                                        </th -->
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="BASIC PAY"
                                                data-skin="dark">
                                                BASIC PAY
                                            </span>
                                        </th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="UNDERTIME"
                                                data-skin="dark">
                                                UT
                                            </span>
                                        </th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="OVERTIME"
                                                data-skin="dark">
                                                OT
                                            </span>
                                        </th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="OVERTIME NIGHT DIFFERENTIAL"
                                                data-skin="dark">
                                                OT.ND
                                            </span>
                                        </th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="HOLIDAY"
                                                data-skin="dark">
                                                HOL
                                            </span>
                                        </th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="ADJUSTMENTS"
                                                data-skin="dark">
                                                ADJUSTMENTS
                                            </span>
                                        </th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="ALLOWANCE"
                                                data-skin="dark">
                                                ALLOWANCE
                                            </span>
                                        </th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="GROSS PAY"
                                                data-skin="dark">
                                                GROSS PAY
                                            </span>
                                        </th>
                                        <th>SSS</th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="SSS Provident"
                                                data-skin="dark">
                                                SSS PROV
                                            </span>
                                        </th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="PHILHEALTH"
                                                data-skin="dark">
                                                PHIC
                                            </span>
                                        </th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="PAG-IBIG"
                                                data-skin="dark">
                                                HDMF
                                            </span>
                                        </th>
                                        <th>TAX</th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="CASH ADVANCE"
                                                data-skin="dark">
                                                C.A
                                            </span>
                                        </th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="CHARGES"
                                                data-skin="dark">
                                                CHRG
                                            </span>
                                        </th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="SSS LOAN"
                                                data-skin="dark">
                                                SSS LOAN
                                            </span>
                                        </th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="HDMF LOAN"
                                                data-skin="dark">
                                                HDMF LOAN
                                            </span>
                                        </th>
                                        <th>
                                            <span data-toggle="m-tooltip"
                                                data-placement="top"
                                                data-original-title="NET PAY"
                                                data-skin="dark">
                                                NET PAY
                                            </span>
                                        </th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th colspan='20' style='text-align: right !important;'></th>
                                            <!-- <th></th>
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
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th> -->
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
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="payroll-payslip-modal">
    <div class="modal-dialog modal-md" role="document">
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
                                <label class="required mb-1" style="font-weight: 600;">Pay Date</label>
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
                                <label class="mb-1" style="font-weight: 600;">&nbsp;</label>
                                <div class="m-checkbox-list">
                                    <label class="m-checkbox">
                                        <input type="checkbox" name="is_bonus" value="1" /><label class="m--font-bolder">Bonus Filter</label><span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group m-form__group">
                                <label class="required mb-1" style="font-weight: 600;">Company</label>
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
                                <label class="mb-1" style="font-weight: 600;">Employee <span class="m-form__help" style="text-transform: none; font-width: 600;">( Optional )</span></label>
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

<div class="modal fade" tabindex="-1" role="dialog" id="view-payroll-payslip-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="temp-payslip_content">
            <div class="modal-header">
                <h5 class="modal-title">Payslip</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="m-form m-form--fit m-form--label-align-right">

                    <div class="row">
                        <div class="col-md-12 text-left"><h5>{{row.company_description}}</h5></div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="m--font-boldest">Pay Period</h6>
                        </div>
                        <div class="col-md-8 text-right">
                            <h6 class="m--font-boldest">{{row.date_start}} - {{row.date_end}}</h6>
                        </div>
                    </div>

                    <div class="m-form__seperator m-form__seperator--thickness-2x m-form__seperator--space-1x m--margin-bottom-5"></div>

                    <div class="row">
                        <div class="col-md-9">
                            <span class="m--font-boldest">{{row.employee_name}}</span>
                        </div>
                        <div class="col-md-3 text-right">
                            <span class="m--font-boldest">{{row.idno}}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <span class="m--font-boldest">{{row.position_description}}</span>
                        </div>
                        <div class="col-md-6 text-right">
                            <span class="m--font-boldest">{{row.department_description}}</span>
                        </div>
                    </div>

                    <div class="m-form__seperator m-form__seperator--thickness-2x m-form__seperator--space-1x m--margin-bottom-5 m--margin-top-5"></div>

                    <div class="row">
                        <div class="col-md-8">
                            <span class="m--font-bolder">{{parseInt(row.is_bonus) === 1 && row.bonus_code ? "BONUS / " + row.bonus_code:"BASIC PAY"}} </span>
                        </div>
                        <div class="col-md-4 text-right">
                            <span class="m--font-boldest">{{row.target_payrate}}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 text-right">
                            <small class="m--font-bold">REG DAYS:</small>&nbsp;<span class="m--font-bolder">{{row.ewd_decimal}}</span>
                        </div>
                        <div class="col-md-6 text-left">
                            <small class="m--font-bold">REG HRS:</small>&nbsp;<span class="m--font-bolder">{{row.target_hours}}</span>
                        </div>
                    </div>
                    <template v-if="parseFloat(row.holiday_hours) > 0">
                        <div class="row">
                            <div class="col-md-8">
                                <span class="m--font-bolder">HOLIDAY PAY</span>
                            </div>
                            <div class="col-md-4 text-right">
                                <span class="m--font-boldest">{{row.total_holiday_amount}}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-right">
                                <small class="m--font-bold">HOL HRS:</small>&nbsp;<span class="m--font-bolder">{{row.holiday_hours}}</span>
                            </div>
                        </div>
                    </template>
                    <!-- div class="row">
                        <div class="col-md-5 text-right">
                            <small class="m--font-bold">WORKING DAYS </small>
                        </div>
                        <div class="col-md-7 text-left">
                            <span class="m--font-bold">{{row.ewd_decimal}}</span>
                        </div>
                    </div -->

                    <!-- div class="row">
                        <div class="col-md-5 text-right">
                            <small class="m--font-bold">WORKING HOURS </small>
                        </div>
                        <div class="col-md-7 text-left">
                            <span class="m--font-bold">{{row.target_hours}}</span>
                        </div>
                    </div -->
                    <template v-if="parseInt(row.is_bonus) === 0">
                        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>
                        <div class="row m--margin-bottom-5 m--margin-top-5">
                            <div class="col-md-8">
                                <span class="m--font-bolder">LATES/ABSENCES</span>
                            </div>
                            <div class="col-md-4 text-right">
                                <span class="m--font-boldest">( {{row.total_unrendered_amount}} )</span>
                                <span class="m--font-boldest">&nbsp;</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-right">
                                <small class="m--font-bold">ABSENT HRS:</small>&nbsp;<span class="m--font-bolder">{{row.absent_hours}}</span>
                            </div>
                            <div class="col-md-6 text-left">
                                <small class="m--font-bold">UT HRS:</small>&nbsp;<span class="m--font-bolder">{{row.undertime_hours}}</span>
                            </div>
                        </div>

                        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>

                        <div class="row">
                            <div class="col-md-8">
                                <span class="m--font-bolder">ALLOWANCES </span>
                            </div>
                            <div class="col-md-4 text-right">
                                <!-- span class="m--font-boldest">{{row.psa_total}}</span -->
                                <span class="m--font-boldest">{{row.total_allowances}}</span>
                            </div>
                        </div>

                        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5 m--margin-top-5"></div>

                        <div class="row text-right">
                            <div class="col-md-5">
                                <small class="m--font-bold">UT HOURS</small>
                            </div>
                            <div class="col-md-7 text-left">
                                <span class="m--font-bold">{{row.undertime_hours}}</span>
                            </div>
                        </div>

                        <div class="row text-right">
                            <div class="col-md-5">
                                <small class="m--font-bold">ABSENT HOURS</small>
                            </div>
                            <div class="col-md-7 text-left">
                                <span class="m--font-bold">{{row.absent_hours}}</span>
                            </div>
                        </div>
                    
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>

                        <template v-if="row.ot_amount > 0">
                        <div class="row m--margin-top-5">
                            <div class="col-md-5">
                                <span class="m--font-bolder">OVERTIME </span>
                            </div>
                            <div class="col-md-3 text-left">
                                <span class="m--font-bold">{{ ot_hrs }}</span>
                            </div>
                            <div class="col-md-4 text-right">
                                <span class="m--font-boldest">{{ ot_computation }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-right">
                                <small class="m--font-bold">OT HRS:</small>&nbsp;<span class="m--font-bolder">{{ ot_hrs }}</span>
                            </div>
                            <div class="col-md-6 text-left">
                                <small class="m--font-bold">OT NDIFF HRS:</small>&nbsp;<span class="m--font-bolder">{{ ot_ndiff_hrs }}</span>
                            </div>
                        </div>
                        </template>
                    </template>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>

                    <template v-if="row.adjustment_e_count > 0">
                    <h6>ADJUSTMENTS</h6>
                    <div class="row text-right" v-for="(item, index) in row.adjustment_earnings">
                        <div class="col-md-5">
                            <small class="m--font-bold">{{item.label}} </small>
                        </div>
                        <div class="col-md-7 text-right">
                            <span class="m--font-boldest">{{item.display_value}}</span>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-top-5 m--margin-bottom-5"></div>
                    </template>
                    <div class="row m--margin-bottom-5">
                        <div class="col-md-8">
                            <span class="m--font-bolder">GROSS PAY </span>
                        </div>
                        <div class="col-md-4 text-right">
                            <span class="m--font-boldest">{{row.gross_pay}}</span>
                        </div>
                    </div>
                    <template v-if="parseInt(row.is_bonus) === 1 && row.adjustment_d_count > 0">
                        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>
                        <h6 class="m--margin-left-15 mt-3">DEDUCTIONS</h6>
                    </template>
                    
                    <template v-if="parseInt(row.is_bonus) === 0">
                        <!-- <template v-if="parseFloat(row.sss_prov) != 0 || parseFloat(row.tax) != 0 || parseFloat(row.sss) != 0 || parseFloat(row.ph) != 0 || parseFloat(row.hdmf) != 0 || parseFloat(row.total_loans) > 0 || parseFloat(row.sss_loan) > 0 || parseFloat(row.hdmf_loan) > 0 || row.loans.length > 0"> -->
                        <template v-if="parseFloat(row.sss_prov) != 0 || parseFloat(row.tax) != 0 || parseFloat(row.sss) != 0 || parseFloat(row.ph) != 0 || parseFloat(row.hdmf) != 0 || parseFloat(row.sss_loan) > 0 || parseFloat(row.hdmf_loan) > 0">
                            <!-- <h6 class="m--margin-left-15">DEDUCTIONS</h6> -->
                            <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>
                            <div class="row mt-3">
                                <div class="col-md-8">
                                    <span class="m--font-bolder">DEDUCTIONS </span>
                                </div>
                            </div>
                        </template>
                        <div class="row text-right" v-if="row.sss && parseFloat(row.sss) > 0">
                            <div class="col-md-5">
                                <small class="m--font-bold">SSS </small>
                            </div>
                            <div class="col-md-7 text-left">
                                <span class="m--font-bold">{{row.sss}}</span>
                            </div>
                        </div>

                        <div class="row text-right" v-if="row.sss_prov && parseFloat(row.sss_prov) > 0">
                            <div class="col-md-5">
                                <small class="m--font-bold">SSS PROVIDENT</small>
                            </div>
                            <div class="col-md-7 text-left">
                                <span class="m--font-bold">{{row.sss_prov}}</span>
                            </div>
                        </div>

                        <div class="row text-right" v-if="row.ph && parseFloat(row.ph) > 0">
                            <div class="col-md-5">
                                <small class="m--font-bold">PHILHEALTH </small>
                            </div>
                            <div class="col-md-7 text-left">
                                <span class="m--font-bold">{{row.ph}}</span>
                            </div>
                        </div>

                        <div class="row text-right" v-if="row.hdmf && parseFloat(row.hdmf) > 0">
                            <div class="col-md-5">
                                <small class="m--font-bold">HDMF </small>
                            </div>
                            <div class="col-md-7 text-left">
                                <span class="m--font-bold">{{row.hdmf}}</span>
                            </div>
                        </div>

                        <div class="row text-right" v-if="row.tax && parseFloat(row.tax) > 0">
                            <div class="col-md-5">
                                <small class="m--font-bold">TAX </small>
                            </div>
                            <div class="col-md-7 text-left">
                                <span class="m--font-bold">{{row.tax}}</span>
                            </div>
                        </div>

                        <template v-if="parseInt(row.is_bonus) === 0 || parseFloat(row.deductions) > 0">
                            <template v-if="parseFloat(row.deductions) > 0">
                                <div class="row m--margin-top-10 m--margin-bottom-5">
                                    <div class="col-md-8">
                                        <span class="m--font-bolder">TOTAL DEDUCTIONS</span>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <span class="m--font-boldest" style="margin-right: 8px">( {{row.deductions}} )</span>
                                    </div>
                                </div>
                            </template>
                            <!-- <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div> -->
                        </template>

                        <!-- <div class="row text-right" v-if="row.total_loans && parseFloat(row.total_loans) > 0">
                            <div class="col-md-5">
                                <small class="m--font-bold">LOAN </small>
                            </div>
                            <div class="col-md-7 text-left">
                                <span class="m--font-bold">{{row.total_loans}}</span>
                            </div>
                        </div> -->

                        <!-- <template v-if="parseFloat(row.total_loans) > 0 || parseFloat(row.sss_loan) > 0 || parseFloat(row.hdmf_loan) > 0 || row.loans.length > 0"> -->
                        <template v-if="parseFloat(row.total_loans) > 0 || row.loans.length > 0">
                            <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>

                            <div class="row mt-3">
                                <div class="col-md-8">
                                    <span class="m--font-bolder">LOANS </span>
                                </div>
                            </div>

                            <div class="row text-right" v-for="(item, index) in row.loans">
                                <template v-if="parseFloat(item.amount_due) > 0">
                                    <div class="col-md-5">
                                        <small class="m--font-bold">{{ item.loan_name }} </small>
                                    </div>
                                    <div class="col-md-7 text-left">
                                        <span class="m--font-bold">{{ item.amount_due }}</span>
                                    </div>
                                </template>
                            </div>

                            <div class="row m--margin-top-10 m--margin-bottom-5">
                                <div class="col-md-8">
                                    <span class="m--font-bolder">TOTAL LOANS</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <span class="m--font-boldest" style="margin-right: 8px">( {{row.totalLoan}} )</span>
                                </div>
                            </div>
                        </template>
                    </template>
                    <template v-if="row.total_loans_interest && parseFloat(row.total_loans_interest) > 0">
                        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>
                        <div class="row m--margin-top-10 m--margin-bottom-5 mt-3">
                            <div class="col-md-8">
                                <span class="m--font-bolder">TOTAL LOAN INTEREST</span>
                            </div>
                            <div class="col-md-4 text-right">
                                <span class="m--font-boldest">( {{row.total_loans_interest}} )</span>
                            </div>
                        </div>
                    </template>
                    <template v-if="row.adjustment_d_count > 0">
                        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>
                        <h6 class="mt-3">OTHERS</h6>
                        <div class="row text-right" v-for="(item, index) in row.adjustment_deductions">
                            <div class="col-md-5">
                                <small class="m--font-bold">{{item.label}} </small>
                            </div>
                            <div class="col-md-7 text-left">
                                <span class="m--font-bold">{{item.display_value}}</span>
                            </div>
                        </div>
                        <div class="row m--margin-top-10 m--margin-bottom-5">
                            <div class="col-md-8">
                                <span class="m--font-bolder">TOTAL OTHERS DEDUCTIONS</span>
                            </div>
                            <div class="col-md-4 text-right">
                                <span class="m--font-boldest" style="margin-right: 8px">( {{row.total_others_deductions}} )</span>
                            </div>
                        </div>
                    </template>

                    <template v-if="row.overall_total_deductions && parseFloat(row.overall_total_deductions) > 0">
                        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>  
                        <div class="row m--margin-top-10 m--margin-bottom-5 mt-3">
                            <div class="col-md-8">
                                <span class="m--font-bolder">TOTAL LOANS & DEDUCTIONS</span>
                            </div>
                            <div class="col-md-4 text-right">
                                <span class="m--font-boldest">( {{row.overall_total_deductions}} )</span>
                            </div>
                        </div>
                    </template>
                    <!-- commented out as loans and cash advance is seperated -->
                    <!-- <template v-if="parseInt(row.is_bonus) === 0 || parseFloat(row.deductions) > 0">
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>
                        <template v-if="parseFloat(row.deductions) > 0">
                            <div class="row m--margin-top-10 m--margin-bottom-5">
                                <div class="col-md-8">
                                    <span class="m--font-bolder">TOTAL LOANS & DEDUCTIONS</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <span class="m--font-boldest">( {{row.deductions}} )</span>
                                </div>
                            </div>
                        </template>
                    </template> -->
                    <!-- commented out as loans and cash advance is seperated -->
                    <div class="m-portlet m-portlet--bordered mt-3">
                        <div class="m-portlet__body m-portlet__body--no-padding">
                            <div class="row m-row--col-separator-xl">
                                <div class="col-md-4">
                                    <h3 class="m--font-bolder m--padding-5 m--margin-top-5 m--margin-bottom-5">NET PAY </h3>
                                </div>
                                <div class="col-md-8 text-right">
                                    <h3 class="m--font-boldest m--padding-5 m--margin-top-5 m--margin-bottom-5">{{row.net_pay}}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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