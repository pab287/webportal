<div class="printable-content">
<style>
.m-form__seperator.m-form__seperator--line-thickness-2x{ border-top: 2px solid #000018; }
.m-form__seperator.m-form__seperator--line{ border-top: 1px solid #0000186B; }
.m-form .m-form__seperator.m-form__seperator--dashed{ border-top: 1px dashed #000018; }
.m-form .m-form__seperator.m-form__seperator--double{ border-top: 4px double #000018; }
.m-portlet.m-portlet--bordered{ border: 1px solid #000018; }
.row.m-row--col-separator-xl > div:first-child{ border-right: 1px solid #000018; }
.m--printable-line { border-top: 1px solid #000018; padding-top: 5px; }
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
}

</style>
<div class="row justify-content-md-center">
    <div class="col-md-12 col-lg-12 col-xl-12 printable-width-12">
        <div class="m-portlet m--padding-10 m--marginless">
            <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="m-form m-form--fit m-form--label-align-right">
                <?php if(isset($other_data) && count((array)$other_data) > 0): ?>
                    <div class="row justify-content-md-center">
                        <div class="col-md-12 col-lg-12 col-xl-12 printable-width-12 text-center">
                        <h5><?php echo strtoupper($other_data->company_description); ?><h5>
                        <h5>PAYROLL SHEET SUMMARY - NETPAY<h5>
                        <h6 class="m--font-boldest"><?php echo "PAY DATE: ".$other_data->pay_date; ?><h6>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if(isset($data) && count($data) > 0): ?>
                        <div class="row mt-2">
                        <?php foreach ($data as $key => $item): ?>
                            <div class="col-md-6 printable-width-6">
                                <div class="row">
                                    <div class="col-md-8 printable-width-8 text-left">
                                        <h6><?php echo strtoupper($item->employee_name); ?></h6>
                                    </div>
                                    <div class="col-md-4 printable-width-4 text-right">
                                        <h6><?php echo $item->net_pay; ?></h6>
                                    </div>
                                </div>
                                <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-1x mb-1"></div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                <?php endif; ?>
                <?php if(isset($other_data) && count((array)$other_data) > 0): ?>
                    <div class="row justify-content-md-center mt-5">
                        <div class="col-md-6 col-lg-6 col-xl-6 printable-width-6 text-right">&nbsp;</div>
                        <div class="col-md-6 col-lg-4 col-xl-6 printable-width-6 text-right">
                            <div class="m-form__seperator m-form__seperator--double m-form__seperator--space-1x mb-2"></div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6 col-xl-6 printable-width-6 text-left">
                                    <h5 class="m--font-boldest">GRAND TOTAL<h5>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6 printable-width-6 text-right">
                                    <h3><span>&#8369;</span>&nbsp;<?php echo number_format($other_data->grand_total_netpay, 2, ".", ","); ?><h3>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>