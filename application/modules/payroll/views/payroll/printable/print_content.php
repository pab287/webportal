<div class="printable-content">
<style>
.m-form__seperator.m-form__seperator--line-thickness-2x{ border-top: 2px solid #000018; }
.m-form__seperator.m-form__seperator--line{ border-top: 1px solid #0000186B; }
.m-form .m-form__seperator.m-form__seperator--dashed{ border-top: 1px dashed #000018; }
.m-portlet.m-portlet--bordered{ border: 1px solid #000018; }
.row.m-row--col-separator-xl > div:first-child{ border-right: 1px solid #000018; }
.m--printable-line { border-top: 1px solid #000018; padding-top: 5px; }
.mt-3 { margin-top: 1rem !important; }
</style>
<style type="text/css" media="print">
@media print {
    @page {
        size: auto;
        size: portrait;
        margin: 0.25cm;
	}
    .printable-width-1 {
        flex: 0 0 8.33333%;
        max-width: 8.33333%;
    }
    .printable-width-2 {
        flex: 0 0 16.66667%;
        max-width: 16.66667%;
    }

    .printable-width-3{
        flex: 0 0 25%;
        max-width: 25%;
    }
    .printable-width-4{
        flex: 0 0 33.33333%;
        max-width: 33.33333%;
    }
    .printable-width-5{
        flex: 0 0 41.66667%;
        max-width: 41.66667%;
    }
    .printable-width-5--5{
        flex: 0 0 43.66667%;
        max-width: 43.66667%;
    }
    .printable-width-6{
        flex: 0 0 50%;
        max-width: 50%;
    }
    .printable-width-7{
        flex: 0 0 58.33333%;
        max-width: 58.33333%;
    }
    .printable-width-8{
        flex: 0 0 66.66667%;
        max-width: 66.66667%;
    }
    .printable-width-9{
        flex: 0 0 75%;
        max-width: 75%;
    }
    .printable-width-10{
        flex: 0 0 83.33333%;
        max-width: 83.33333%;
    }
    .printable-width-11{
        flex: 0 0 91.66667%;
        max-width: 91.66667%;
    }
    .printable-width-12{
        flex: 0 0 100%;
        max-width: 100%;
    }

    .mt-3 { 
        margin-top: 1rem !important; 
    }
}

</style>
<?php if(isset($data) && count($data) > 0): ?>
<?php foreach ($data as $key => $item): ?>
    <?php $_temp_total_others = 0; $temp_totalLoan = 0; $_tempDeductions = 0; ?>
<div class="row justify-content-md-center">
    <div class="col-md-6 col-lg-6 col-xl-6 printable-width-5--5">
        <!-- div class="m-portlet m-portlet--full-height m--padding-10" -->
        <div class="m-portlet m--padding-10 m--marginless">
            <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="m-form m-form--fit m-form--label-align-right">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h3><?php echo strtoupper($item->company_description); ?></h3>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 printable-width-6 text-left">
                            <h5 class="m--font-boldest">PAY PERIOD</h5>
                        </div>
                        <div class="col-md-6 printable-width-6 text-right">
                            <h5 class="m--font-boldest"><?php echo $item->date_start; ?> - <?php echo $item->date_end; ?></h5>
                        </div>
                    </div>

                    <div class="m-form__seperator m-form__seperator--line-thickness-2x m-form__seperator--space-1x m--marginless"></div>

                    <div class="row m--margin-top-5">
                        <div class="col-md-12 printable-width-12 text-center">
                            <h5 class="m--font-boldest"><?php echo strtoupper($item->employee_name); ?></h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 printable-width-12 text-center">
                            <h5 class="m--font-boldest"><?php echo $item->idno; ?></h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 printable-width-12 text-center">
                            <span class="m--font-boldest"><?php echo strtoupper($item->position_description); ?></span>
                        </div>
                    </div>
                    <div class="row m--margin-bottom-5">
                        <div class="col-md-12 printable-width-12 text-center">
                            <span class="m--font-boldest"><?php echo strtoupper($item->department_description); ?></span>
                        </div>
                    </div>

                    <div class="m-form__seperator m-form__seperator--line-thickness-2x m-form__seperator--space-1x m--marginless"></div>

                    <div class="row m--margin-top-5">
                        <div class="col-md-8 printable-width-8">
                            <h5 class="m--font-bolder m--marginless"><?php echo intval($item->is_bonus) == 1 && $item->bonus_code ? "BONUS / {$item->bonus_code}":"BASIC PAY"; ?> </h5>
                        </div>
                        <div class="col-md-4 printable-width-4 text-right">
                            <h5 class="m--font-boldest m--marginless"><?php echo $item->target_payrate; ?></h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 printable-width-6 text-right">
                            <h5 class="m--marginless"><span class="m--font-bolder">REG DAYS:</span>&nbsp;<span class="m--font-bolder"><?php echo $item->ewd_decimal; ?></span></h5>
                        </div>
                        <div class="col-md-6 printable-width-6 text-left">
                            <h5 class="m--marginless"><span class="m--font-bolder">REG HRS:</span>&nbsp;<span class="m--font-bolder"><?php echo $item->target_hours; ?></span></h5>
                        </div>
                    </div>

                    <?php if(floatval($item->holiday_hours) > 0): ?>
                        <div class="row m--margin-top-5">
                            <div class="col-md-8 printable-width-8">
                                <h5 class="m--font-bolder m--marginless">HOLIDAY PAY</h5>
                            </div>
                            <div class="col-md-4 printable-width-4 text-right">
                                <h5 class="m--font-boldest m--marginless"><?php echo $item->total_holiday_amount; ?></h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 printable-width-6 text-right">
                                <h5 class="m--marginless"><span class="m--font-bolder">HOL HRS:</span>&nbsp;<span class="m--font-bolder"><?php echo $item->holiday_hours; ?></span></h5>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if(intval($item->is_bonus) == 0): ?>
                        <div class="m-form__seperator m-form__seperator--line m-form__seperator--space-1x m--marginless"></div>
                        <?php if(floatval($item->total_unrendered_amount) > 0): ?>
                        <div class="row m--margin-top-5">
                            <div class="col-md-8 printable-width-8">
                                <h5 class="m--font-bolder m--marginless">LATES/ABSENCES</h5>
                            </div>
                            <div class="col-md-4 printable-width-4 text-right">
                                <h5 class="m--font-boldest m--marginless">( <?php echo $item->total_unrendered_amount; ?> )</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 printable-width-6 text-right">
                                <h5 class="m--marginless"><span class="m--font-bolder">ABSENT HRS:</span>&nbsp;<span class="m--font-bolder"><?php echo floatval($item->absent_hours) > 0 ? $item->absent_hours : "0.00"; ?></span></h5>
                            </div>
                            <div class="col-md-6 printable-width-6 text-left">
                                <h5 class="m--marginless"><span class="m--font-bolder">UT HRS:</span>&nbsp;<span class="m--font-bolder"><?php echo floatval($item->undertime_hours) > 0 ? $item->undertime_hours : "0.00"; ?></span></h5>
                            </div>
                        </div>
                        <div class="m-form__seperator m-form__seperator--line m-form__seperator--space-1x m--marginless"></div>
                        <?php endif; ?>

                        <?php if(floatval($item->total_allowances) > 0): ?>
                        <div class="row m--margin-top-5">
                            <div class="col-md-8 printable-width-8">
                                <h5 class="m--font-bolder m--marginless">ALLOWANCES </h5>
                            </div>
                            <div class="col-md-4 printable-width-4 text-right">
                                <h5 class="m--font-boldest m--marginless"><?php echo number_format($item->total_allowances, 2); ?></h5>
                            </div>
                        </div>
                        <div class="m-form__seperator m-form__seperator--line m-form__seperator--space-1x m--marginless"></div>
                        <?php endif; ?>

                        <?php if($item->ot_amount > 0): ?>
                            <div class="row m--margin-top-5">
                                <div class="col-md-6 printable-width-6">
                                    <h5 class="m--font-bolder m--marginless">OVERTIME </h5>
                                </div>
                                <div class="col-md-6 printable-width-6 text-right">
                                    <?php if($item->ot_ndiff_amount > 0) { ?>
                                        <h5 class="m--font-boldest m--marginless"><?php echo number_format($item->ot_amount + $item->ot_ndiff_amount,2); ?></h5>
                                    <?php }else{ ?>
                                        <h5 class="m--font-boldest m--marginless"><?php echo $item->ot_amount; ?></h5>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 printable-width-6 text-right">
                                    <h5 class="m--marginless"><span class="m--font-bolder">OT HRS:</span>&nbsp;<span class="m--font-bolder"><?php echo number_format($item->ot_hours,2); ?></span></h5>
                                </div>
                                <?php if(floatval($item->ot_ndiff_hours) > 0): ?>
                                <div class="col-md-6 printable-width-6 text-left">
                                    <h5 class="m--marginless"><span class="m--font-bolder">NDIFF HRS:</span>&nbsp;<span class="m--font-bolder"><?php echo number_format($item->ot_ndiff_hours,2); ?></span></h5>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="m-form__seperator m-form__seperator--line m-form__seperator--space-1x m--marginless"></div>
                        <?php endif; ?>
                        
                        <?php if($item->total_ndiff_amount > 0): ?>
                            <div class="row m--margin-top-5">
                                <div class="col-md-6 printable-width-6">
                                    <h5 class="m--font-bolder m--marginless">REGULAR NDIFF. </h5>
                                </div>
                                <div class="col-md-6 printable-width-6 text-right">
                                    <h5 class="m--font-boldest m--marginless"><?php echo number_format($item->total_ndiff_amount, 2); ?></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 printable-width-6 text-right">
                                    <h5 class="m--marginless"><span class="m--font-bolder">NDIFF HRS:</span>&nbsp;<span class="m--font-bolder"><?php echo number_format($item->total_ndiff_minutes / 60, 2); ?></span></h5>
                                </div>
                            </div>
                            <div class="m-form__seperator m-form__seperator--line m-form__seperator--space-1x m--marginless"></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if($item->adjustment_e_count > 0): ?>
                        <div class="row m--margin-top-5">
                            <div class="col-md-6 printable-width-6">
                                <h5 class="m--font-bolder m--marginless">ADJUSTMENTS </h5>
                            </div>
                        </div>

                        <?php foreach ($item->adjustment_earnings as $kk => $vv): ?>
                        
                            <div class="row text-right">
                                <div class="col-md-5 printable-width-5">
                                    <h5 class="m--marginless m--font-boldest"><?php echo strtoupper($vv->label); ?></h5>
                                </div>
                                <div class="col-md-7 printable-width-7 text-right">
                                    <h5 class="m--marginless m--font-boldest"><?php echo $vv->display_value; ?></h5>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="m-form__seperator m-form__seperator--line m-form__seperator--space-1x m--marginless"></div>
                    <?php endif; ?>

                    <div class="row m--margin-top-5 <?php echo intval($item->is_bonus) == 0 ? "m--margin-bottom-5":""; ?>">
                        <div class="col-md-8 printable-width-8">
                            <h5 class="m--font-bolder m--marginless">GROSS PAY </h5>
                        </div>
                        <div class="col-md-4 printable-width-4 text-right">
                            <h5 class="m--font-boldest m--marginless"><?php echo $item->gross_pay; ?></h5>
                        </div>
                    </div>

                    <?php if(intval($item->is_bonus) == 1 && (is_numeric($item->adjustment_d_count) && intval($item->adjustment_d_count) > 0)): ?>
                        <div class="m-form__seperator m-form__seperator--line m-form__seperator--space-1x m--margin-bottom-5"></div>
                        <h5 class="m--marginless mt-3"><span class="">DEDUCTIONS</span></h5>
                    <?php endif; ?>

                    <?php if(intval($item->is_bonus) == 0): ?>
                        <div class="m-form__seperator m-form__seperator--line m-form__seperator--space-1x m--margin-bottom-5"></div>
                        
                        <?php if(floatval($item->sss) != 0 || floatval($item->sss_prov) != 0 || floatval($item->ph) != 0 || floatval($item->hdmf) != 0 || floatval($item->tax) != 0 || floatval($item->sss_loan) != 0 || floatval($item->hdmf_loan) != 0) { ?>
                            <!-- commented out as loans is seperated to deductions -->
                            <?php //if(floatval($item->sss) != 0 || floatval($item->sss_prov) != 0 || floatval($item->ph) != 0 || floatval($item->hdmf) != 0 || floatval($item->tax) != 0 || floatval($item->total_loans) != 0 || floatval($item->sss_loan) != 0 || floatval($item->hdmf_loan) != 0) { ?>
                            <!-- commented out as loans is seperated to deductions -->

                            <!-- || parseFloat(row.total_loans) > 0 || parseFloat(row.sss_loan) > 0 || parseFloat(row.hdmf_loan) > 0 || row.loans.length > 0 -->
                            <h5 class="m--marginless mt-3"><span class="">DEDUCTIONS</span></h5>
                        <?php } ?>
                    
                        <?php if($item->sss && floatval($item->sss) > 0): ?>
                            <div class="row text-right">
                                <div class="col-md-5 printable-width-5">
                                    <h5 class="m--font-bolder m--marginless">SSS </h5>
                                </div>
                                <div class="col-md-7 printable-width-7 text-left">
                                    <h5 class="m--font-bolder m--marginless"><?php echo $item->sss; ?></h5>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($item->sss_prov && floatval($item->sss_prov) > 0): ?>
                            <div class="row text-right">
                                <div class="col-md-5 printable-width-5">
                                    <h5 class="m--font-bolder m--marginless">SSS PROVIDENT</h5>
                                </div>
                                <div class="col-md-7 printable-width-7 text-left">
                                    <h5 class="m--font-bolder m--marginless"><?php echo $item->sss_prov; ?></h5>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($item->ph && floatval($item->ph) > 0): ?>
                            <div class="row text-right">
                                <div class="col-md-5 printable-width-5">
                                    <h5 class="m--font-bolder m--marginless">PHILHEALTH </h5>
                                </div>
                                <div class="col-md-7 printable-width-7 text-left">
                                    <h5 class="m--font-bolder m--marginless"><?php echo $item->ph; ?></h5>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($item->hdmf && floatval($item->hdmf) > 0): ?>
                            <div class="row text-right">
                                <div class="col-md-5 printable-width-5">
                                    <h5 class="m--font-bolder m--marginless">HDMF </h5>
                                </div>
                                <div class="col-md-7 printable-width-7 text-left">
                                    <h5 class="m--font-bolder m--marginless"><?php echo $item->hdmf; ?></h5>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($item->tax && floatval($item->tax) > 0): ?>
                            <div class="row text-right">
                                <div class="col-md-5 printable-width-5">
                                    <h5 class="m--font-bolder m--marginless">TAX </h5>
                                </div>
                                <div class="col-md-7 printable-width-7 text-left">
                                    <h5 class="m--font-bolder m--marginless"><?php echo $item->tax; ?></h5>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php
                            $arrDeductions = array("sss", "sss_prov", "ph", "hdmf", "tax");
                            foreach ($arrDeductions as $nrec) {
                                if ($item->$nrec && floatval($item->$nrec) > 0) {
                                    $_tempDeductions += floatval(preg_replace('/[^\d\.\-]/', '', $item->$nrec));
                                }
                            }
                        ?>

                        <?php if($item->deductions && floatval($item->deductions) > 0): ?>
                            <div class="row m--margin-top-5 m--margin-bottom-5">
                                <div class="col-md-8 printable-width-8">
                                    <h5 class="m--font-bolder m--marginless">TOTAL DEDUCTIONS</h5>
                                </div>
                                <div class="col-md-4 printable-width-4 text-right">
                                    <h5 class="m--font-boldest" style="margin: 0 2px 0 0">
                                        ( <?=number_format($_tempDeductions, 2) ?> )
                                    </h5>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($item->total_loans && (floatval($item->total_loans) > 0 || (is_array($item->loans) && count($item->loans) > 0))): ?>
                            <?php if($item->deductions && floatval($item->deductions) > 0): ?>
                                <div class="m-form__seperator m-form__seperator--line m-form__seperator--space-1x m--margin-bottom-5"></div>
                            <?php endif; ?>

                            <h5 class="m--marginless mt-3"><span class="">LOANS</span></h5>
                            <?php $created_adjustment = $item->created_adjustments; ?>

                            <!-- for seperating charges, under deductions to loans -->
                            <?php foreach ($item->loans as $kk => $vv): ?>
                                <?php if($vv->amount_due > 0): ?>
                                    <?php
                                        if (strtolower($vv->loan_name) == 'charges' || strtolower($vv->loan_name) == 'under deduction' || strtolower($vv->loan_name) == 'medical loan') {
                                            $_data = array(
                                                'label' => $vv->loan_name,
                                                'display_value' => $vv->amount_due
                                            );
                                            $item->adjustment_deductions[] = (object) $_data;
                                            $item->adjustment_d_count++;
                                            unset($item->loans[$kk]);
                                        }

                                        if (strtolower($vv->loan_name) != 'charges' && strtolower($vv->loan_name) != 'under deduction' && strtolower($vv->loan_name) != 'medical loan') {
                                            $temp_amountDue = floatval(preg_replace('/[^\d\.\-]/', '', $vv->amount_due));

                                            if (isset($created_adjustment) && $created_adjustment) {
                                                $_tempCreated = explode(",", $created_adjustment);
                                                $tempAdj = 0;

                                                foreach ($_tempCreated as $_key => $_value) {
                                                    $temp_adjustment = explode("||", $_value);
                                                    $adj_type = isset($temp_adjustment[2]) ? intval($temp_adjustment[2]) : 0;
                                                    $temp_status = isset($temp_adjustment[3]) ? intval($temp_adjustment[3]) : 0;

                                                    $_temp = floatval(preg_replace('/[^\d\.\-]/', '', $vv->amount_due));

                                                    $_temp = $adj_type == 1 ? floatval($temp_amountDue) + floatval($temp_adjustment[1]) : floatval($temp_amountDue) - floatval($temp_adjustment[1]);
                                                    $tempAdj = $_temp;

                                                    $temp_amountDue = ($temp_adjustment[0] == "LOAN" && $temp_status === 1) ? $_temp : $temp_amountDue;

                                                    if (isset($vv->loan_name) && strtolower($vv->loan_name) == 'cash advance') {
                                                        if ($temp_adjustment[0] == "LOAN" && $temp_status === 1) {
                                                            $item->loans[$kk]->amount_due = number_format($temp_amountDue, 2);
                                                        }
                                                    } else {
                                                        if ($temp_adjustment[0] == "LOAN" && $temp_status === 1) {
                                                            $loan = array_column($item->loans, 'loan_name');
                                                            
                                                            if (!in_array('CASH ADVANCE', $loan)) {
                                                                $item->loans[] = (object) array(
                                                                    'loan_name' => 'CASH ADVANCE',
                                                                    'amount_due' => number_format($temp_adjustment[1], 2),
                                                                    'loan_type' => $adj_type
                                                                );
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    ?>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <?php foreach ($item->loans as $kk => $vv): ?>
                                <div class="row text-right">
                                    <?php if($vv->amount_due > 0): ?>
                                        <?php ?>
                                        <div class="col-md-5 printable-width-5">
                                            <h5 class="m--font-bolder m--marginless"><?php echo strtoupper($vv->loan_name); ?></h5>
                                        </div>
                                        <div class="col-md-7 printable-width-7 text-left">
                                            <h5 class="m--font-bolder m--marginless"><?php echo $vv->amount_due; ?></h5>
                                        </div>
                                        <?php $temp_totalLoan += floatval(preg_replace('/[^\d\.\-]/', '', $vv->amount_due)); ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            <div class="row m--margin-top-5 m--margin-bottom-5">
                                <div class="col-md-8 printable-width-8">
                                    <h5 class="m--font-bolder m--marginless">TOTAL LOANS</h5>
                                </div>
                                <div class="col-md-4 printable-width-4 text-right">
                                    <h5 class="m--font-boldest" style="margin: 0 2px 0 0">( <?php echo number_format($temp_totalLoan, 2); ?> )</h5>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if($item->total_loans_interest && floatval($item->total_loans_interest) > 0): ?>
                        <div class="m-form__seperator m-form__seperator--line m-form__seperator--space-1x m--margin-bottom-5"></div>
                        <div class="row m--margin-top-5 m--margin-bottom-5 mt-3">
                            <div class="col-md-8 printable-width-8">
                                <h5 class="m--font-bolder m--marginless">TOTAL LOANS INTEREST</h5>
                            </div>
                            <div class="col-md-4 printable-width-4 text-right">
                                <h5 class="m--font-boldest" style="margin: 0 2px 0 0">( <?php echo $item->total_loans_interest; ?> )</h5>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(is_numeric($item->adjustment_d_count) && intval($item->adjustment_d_count) > 0): ?>
                        <div class="m-form__seperator m-form__seperator--line m-form__seperator--space-1x m--margin-bottom-5"></div>
                        <h5 class="m--marginless mt-3"><span class="mt-3">OTHERS</span></h5>

                        <?php foreach ($item->adjustment_deductions as $kk => $vv): ?>
                            <div class="row text-right">
                                <div class="col-md-5 printable-width-5">
                                    <h5 class="m--font-bolder m--marginless"><?php echo strtoupper($vv->label); ?></h5>
                                </div>
                                <div class="col-md-7 printable-width-7 text-left">
                                    <h5 class="m--font-bolder m--marginless"><?php echo $vv->display_value; ?></h5>
                                </div>
                            </div>
                            <?php $_temp_total_others += floatval(preg_replace('/[^\d\.\-]/', '', $vv->display_value)); ?>
                        <?php endforeach; ?>

                        <div class="row m--margin-top-5 m--margin-bottom-5">
                            <div class="col-md-8 printable-width-8">
                                <h5 class="m--font-bolder m--marginless">TOTAL OTHERS DEDUCTIONS</h5>
                            </div>
                            <div class="col-md-4 printable-width-4 text-right">
                                <h5 class="m--font-boldest" style="margin: 0 2px 0 0">( <?php echo number_format($_temp_total_others, 2); ?> )</h5>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($item->total_loans && (floatval($item->total_loans) > 0 || (is_array($item->loans) && count($item->loans) > 0))): ?>
                        <div class="m-form__seperator m-form__seperator--line m-form__seperator--space-1x m--margin-bottom-5"></div>
                        <div class="row m--margin-top-5 m--margin-bottom-5">
                            <div class="col-md-8 printable-width-8">
                                <h5 class="m--font-bolder m--marginless">TOTAL LOANS & DEDUCTIONS</h5>
                            </div>
                            <div class="col-md-4 printable-width-4 text-right">
                                <h5 class="m--font-boldest m--marginless">
                                    <?php $overall_total_loans = floatval($_tempDeductions) + floatval($item->total_loans_interest) + floatval($temp_totalLoan) + floatval($_temp_total_others); ?>
                                    ( <?= number_format($overall_total_loans, 2); ?> )
                                </h5>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="m-portlet m-portlet--bordered m--margin-bottom-5 mt-3">
                        <div class="m-portlet__body m-portlet__body--no-padding">
                            <div class="row m-row--col-separator-xl">
                                <div class="col-md-4 printable-width-4">
                                    <h4 class="m--font-bolder m--marginless"><span class="m--margin-left-5 m--margin-right-5">NET PAY</span></h4>
                                </div>
                                <div class="col-md-8 printable-width-8 text-right">
                                    <h3 class="m--font-boldest m--marginless"><span class="m--margin-left-5 m--margin-right-5"><span>&#8369;</span>&nbsp;<?php echo $item->net_pay; ?></span></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x m--margin-top-5"></div>
                    <div class="row m--margin-top-5 m--font-boldest">
                        <div class="col-md-12 printable-width-12 text-center">
                            <h5 class="m--marginless">RECEIPT FOR PAY</h5>
                        </div>
                        <div class="col-md-12 printable-width-12 text-center">
                            <h5 class="m--marginless">
                                PAY PERIOD <?php echo $item->date_start; ?> - <?php echo $item->date_end; ?>
                            </h5>
                            <?php if(intval($item->is_bonus) == 1): ?>
                                <h5 class="m--marginless m--font-boldest">[ BONUS / 13TH MONTH ]</h5>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row m--margin-top-5 m--margin-bottom-5">
                        <div class="col-md-12 printable-width-12">
                            <h5 class="m--marginless">
                            <p class="m--marginless text-justify">
                                <span>I ACKNOWLEDGE TO HAVE RECEIVED THE AMOUNT OF</span>
                                <span class="m--font-boldest" style="font-size: 16px;"><?php echo $item->net_to_text; ?> (<span>&#8369;</span> <?php echo $item->net_pay; ?>)</span>
                                <span>AND HAVE NO FURTHER CLAIMS FOR SERVICE RENDERED</small></p>
                            </h5>
                        </div>
                    </div>
                    <div class="row m--margin-top-25 m--margin-bottom-5">
                        <div class="col-md-12 printable-width-12">
                            <h6 class="m--font-bolder">RECEIVED BY:</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 printable-width-3">&nbsp;</div>
                        <div class="col-md-9 printable-width-9 text-center">
                            <h6 class="m--marginless m--printable-line"><span class="m--font-bolder"><?php echo $item->employee_name; ?></span></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $tempKey = $key + 1; ?>
<?php if($tempKey !== count($data)): ?>
<div class="row justify-content-md-center">
    <div class="col-md-6 col-lg-6 col-xl-6">
        <div class="m-form__seperator m-form__seperator--line m-form__seperator--space-1x m--marginless"></div>
    </div>
</div>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
</div>