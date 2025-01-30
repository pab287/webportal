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
                                <span class="m--font-bold">{{ row.ot_hrs }}</span>
                            </div>
                            <div class="col-md-4 text-right">
                                <span class="m--font-boldest">{{ row.ot_computation }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-right">
                                <small class="m--font-bold">OT HRS:</small>&nbsp;<span class="m--font-bolder">{{ row.ot_hrs }}</span>
                            </div>
                            <div class="col-md-6 text-left">
                                <small class="m--font-bold">OT NDIFF HRS:</small>&nbsp;<span class="m--font-bolder">{{ row.ot_ndiff_hrs }}</span>
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
                        <h6 class="m--margin-left-15">DEDUCTIONS</h6>
                    </template>
                    
                    <template v-if="parseInt(row.is_bonus) === 0">
                        <!-- <template v-if="parseFloat(row.sss_prov) != 0 || parseFloat(row.tax) != 0 || parseFloat(row.sss) != 0 || parseFloat(row.ph) != 0 || parseFloat(row.hdmf) != 0 || parseFloat(row.total_loans) > 0 || parseFloat(row.sss_loan) > 0 || parseFloat(row.hdmf_loan) > 0 || row.loans.length > 0"> -->
                        <template v-if="parseFloat(row.sss_prov) != 0 || parseFloat(row.tax) != 0 || parseFloat(row.sss) != 0 || parseFloat(row.ph) != 0 || parseFloat(row.hdmf) != 0 || parseFloat(row.sss_loan) > 0 || parseFloat(row.hdmf_loan) > 0">
                            <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>
                            <div class="row">
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
                                        <span class="m--font-boldest">( {{row.deductions}} )</span>
                                    </div>
                                </div>
                            </template>
                        </template>

                        <template v-if="parseFloat(row.total_loans) > 0 || row.loans.length > 0">
                            <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>

                            <div class="row">
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
                                    <span class="m--font-boldest">( {{row.totalLoan}} )</span>
                                </div>
                            </div>
                        </template>

                        <!-- <template v-if="parseFloat(row.total_loans) > 0 || parseFloat(row.sss_loan) > 0 || parseFloat(row.hdmf_loan) > 0 || row.loans.length > 0">
                            <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>
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
                        </template> -->
                    </template>
                    <template v-if="row.adjustment_d_count > 0">
                        <h6 class="m--margin-left-15">OTHERS</h6>
                        <div class="row text-right" v-for="(item, index) in row.adjustment_deductions">
                        <div class="col-md-5">
                            <small class="m--font-bold">{{item.label}} </small>
                        </div>
                        <div class="col-md-7 text-left">
                            <span class="m--font-bold">{{item.display_value}}</span>
                        </div>
                    </div>
                    </template>
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
                    <div class="m-portlet m-portlet--bordered">
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
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>