<div id="printableArea">
<style>
    #printableArea table tr td, #printableArea table tr th { font-size: 14px; }
    @media print { 
    .page-break-now {
        page-break-before:always; 
    } 
}
</style>
<div class="mb-3">
    <h4 class="text-center">PAYROLL NET PAY SUMMARY REPORT</h4>
    <?php if(intval($filter["group"]) === 1): ?>
        <h6 class="text-center">PAYROLL DATE: <?php echo date("Y/m/d", strtotime($filter["pay_date"])); ?></h6>
        <h6 class="text-center"> CUT-OFF PERIOD: <?php echo date("Y/m/d", strtotime($filter["date_start"])); ?> - <?php echo date("Y/m/d", strtotime($filter["date_end"])); ?></h6>
    <?php else: ?>
        <h6 class="text-center">PAYROLL MONTH: <?php echo strtoupper($filter["month_name"]); ?> <?php echo strtoupper($filter["year"]); ?></h6>
    <?php endif; ?>
    <?php if(isset($filter["payout_schedule"]) && $filter["payout_schedule"]): ?>
        <h5 class="text-center m--font-boldest">PAYOUT SCHEDULE: <?php echo strtoupper($filter["payout_schedule"]); ?></h5>
    <?php endif; ?>
    <?php if(isset($filter["company_description"]) && $filter["company_description"]): ?>
        <h5 class="text-center m--font-boldest"><?php echo strtoupper($filter["company_description"]); ?></h5>
    <?php endif; ?>
</div>
<?php if(isset($data) && $data): ?>
    <?php
        $allowNextRow = true;
        $lastEntry = false;
        $tempKeyDivider = 60;
        $temp = floor(count($data) / 120) * 120;
        $tempDivider = ceil((count($data) - $temp) / 2);
        $tempNewKey = 0;
    ?>
    <?php $tempColIndex = 1; ?>
    <div class="row">
        <div class="col-md-6 col-lg-6">
            <table width="100%">
                <colgroup>
                    <col width="8%">
                    <col width="*">
                    <col width="20%">
                    <col width="14%">
                    <col width="12%">
                    <col width="12%">
                    <col width="14%">
                </colgroup>
            <thead>
                <tr>
                    <th>#</th>
                    <th>EMPLOYEE NAME</th>
                    <th>COMPANY</th>
                    <th>WORK STATUS</th>
                    <th>DATE HIRED</th>
                    <th>BANK INFO</th>
                    <th align="right" style="text-align: right;">NET PAY</th>
                </tr>
            </thead>
            </table>
            <?php foreach ($data as $key => $value): ?>
                <?php $value = (object) $value; ?>
                <?php $tempKey = $key + 1; ?>
                <?php if($lastEntry){ $tempNext = $tempNewKey % $tempKeyDivider; }
                else{ $tempNext = $tempKey % $tempKeyDivider; } ?>
                <?php if(intval($key) === intval($temp) && intval($tempKeyDivider) !== intval($tempDivider)){
                    $tempKeyDivider = $tempDivider;
                    $allowNextRow = false;
                    $lastEntry = true;
                } ?>
                <table width="100%">
                    <colgroup>
                        <col width="8%">
                        <col width="*">
                        <col width="20%">
                        <col width="14%">
                        <col width="12%">
                        <col width="12%">
                        <col width="14%">
                    </colgroup>
                    <tbody>
                        <tr>
                            <td><?php echo $tempKey; ?></td>
                            <td><?php echo $value->employee_name; ?></td>
                            <td><?php echo $value->company_description; ?></td>
                            <td><?php echo $value->work_status; ?></td>
                            <td><?php echo $value->date_start; ?></td>
                            <td><?php echo $value->atm_info ? $value->atm_info : ' --- '; ?></td>
                            <td align="right" style="font-family: 'Lucida Console';"><?php echo $value->net_pay_decimal; ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php if($tempKey > 1 && $tempNext == 0): ?>
            </div>
            <?php if($tempColIndex % 2 == 0 && $tempKey > 1 && $allowNextRow): ?>
            </div><div class="row page-break-now">
            <?php endif; ?>
            <div class="col-md-6 col-lg-6">
                <table width="100%">
                    <colgroup>
                        <col width="8%">
                        <col width="*">
                        <col width="20%">
                        <col width="14%">
                        <col width="12%">
                        <col width="12%">
                        <col width="14%">
                    </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>EMPLOYEE NAME</th>
                        <th>COMPANY</th>
                        <th>WORK STATUS</th>
                        <th>DATE HIRED</th>
                        <th>BANK INFO</th>
                        <th align="right" style="text-align: right;">NET PAY</th>
                    </tr>
                </thead>
                </table>
                <?php $tempColIndex++; ?>
            <?php endif; ?>
            <?php if($lastEntry){ $tempNewKey++; } ?>
        <?php endforeach; ?>
        </div>
    </div>
    <div class="row mt-5 printable-row_content">
        <div class="col-md-9 col-lg-9 col-sm-12">&nbsp;</div>
        <div class="col-md-3 col-lg-3 col-sm-12 text-right">
            <h3 style="font-family: 'Lucida Console';"><?php echo number_format($grand_total, 2, ".", ","); ?></h3>
            <h5 style="border-top: 5px double; font-weight: bold; padding-top: 10px;">GRAND TOTAL</h5>
        </div>
    </div>
<?php endif; ?>
</div>