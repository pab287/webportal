
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
                <span class="m--font-bolder">HOLIDAY PAY <small class="m--font-boldest">(BASIC PAY INC)</small></span>
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
        <template v-if="parseFloat(row.total_unrendered_amount) > 0">
            <div class="row m--margin-top-5">
                <div class="col-md-8">
                    <span class="m--font-bolder">LATES/ABSENCES</span>
                </div>
                <div class="col-md-4 text-right">
                    <span class="m--font-boldest">( {{row.total_unrendered_amount}} )</span>
                    <span class="m--font-boldest">&nbsp;</span>
                </div>
            </div>
            <div class="row" v-if="parseFloat(row.absent_hours) > 0 || parseFloat(row.undertime_hours) > 0">
                <div class="col-md-6 text-right" v-if="parseFloat(row.absent_hours) > 0">
                    <small class="m--font-bold">ABSENT HRS:</small>&nbsp;<span class="m--font-bolder">{{parseFloat(row.absent_hours) > 0 ? row.absent_hours : "0.00"}}</span>
                </div>
                <div class="col-md-6 text-left" v-if="parseFloat(row.undertime_hours) > 0">
                    <small class="m--font-bold">UT HRS:</small>&nbsp;<span class="m--font-bolder">{{parseFloat(row.undertime_hours) > 0 ? row.undertime_hours : "0.00"}}</span>
                </div>
            </div>
            <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>

            {{ row.unpaid_holiday_amount }}
        </template>
        <template v-if="parseFloat(row.total_allowances) > 0">
            <div class="row">
                <div class="col-md-8">
                    <span class="m--font-bolder">ALLOWANCES </span>
                </div>
                <div class="col-md-4 text-right">
                    <span class="m--font-boldest">{{row.total_allowances}}</span>
                </div>
            </div>
            <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>
        </template>
        <template v-if="row.ot_amount > 0">
        <div class="row m--margin-top-5">
            <div class="col-md-5">
                <span class="m--font-bolder">OVERTIME </span>
            </div>
            <div class="col-md-7 text-right">
                <span class="m--font-boldest">{{ ot_computation }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-right" v-if="parseFloat(row.ot_minutes) > 0">
                <small class="m--font-bold">OT HRS:</small>&nbsp;<span class="m--font-bolder">{{ ot_hrs }}</span>
            </div>
            <div class="col-md-6 text-left" v-if="parseFloat(row.ot_ndiff_minutes) > 0">
                <small class="m--font-bold">OT NDIFF HRS:</small>&nbsp;<span class="m--font-bolder">{{ ot_ndiff_hrs }}</span>
            </div>
        </div>
        <div class="row m--margin-top-5" v-if="parseFloat(row.ot_allowance_amount) > 0">
            <div class="col-md-5">
                <span class="m--font-bolder">OT ALLOWANCE </span>
            </div>
            <div class="col-md-7 text-right">
                <span class="m--font-boldest">{{ row.ot_allowance_amount }}</span>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>
        </template>
        <template v-if="parseFloat(row.total_ndiff_amount) > 0">
        <div class="row m--margin-top-5">
            <div class="col-md-5">
                <span class="m--font-bolder">REGULAR NDIFF. </span>
            </div>
            <div class="col-md-7 text-right">
                <span class="m--font-boldest">{{ total_ndiff_computation }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-right" v-if="parseFloat(row.total_ndiff_minutes) > 0">
                <small class="m--font-bold">NDIFF HRS:</small>&nbsp;<span class="m--font-bolder">{{ total_ndiff_hrs }}</span>
            </div>
        </div>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-bottom-5"></div>
        </template>
    </template>
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
        <template v-if="parseFloat(row.sss_prov) != 0 || parseFloat(row.tax) != 0 || parseFloat(row.sss) != 0 || parseFloat(row.ph) != 0 || parseFloat(row.hdmf) != 0 || parseFloat(row.sss_loan) > 0 || parseFloat(row.hdmf_loan) > 0">
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
        </template>
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
    <template v-if="row.overall_total_deductions && parseFloat(row.overall_total_deductions) > 0 && (raw_tl > 0 || raw_tod > 0 || raw_tli > 0)">
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