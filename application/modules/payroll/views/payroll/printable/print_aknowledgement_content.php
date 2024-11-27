<div class="printable-content">
<style>
.m-form__seperator.m-form__seperator--line-thickness-2x{ border-top: 2px solid #000018; }
.m-form__seperator.m-form__seperator--line{ border-top: 1px solid #0000186B; }
.m-form .m-form__seperator.m-form__seperator--dashed{ border-top: 1px dashed #000018; }
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
    .page-break-now {
        page-break-before:always; 
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
<?php if(isset($data) && count($data) > 0): ?>
<div class="row justify-content-md-center">
    <?php foreach ($data as $key => $item): $key += 1; ?>
    <div class="col-md-6 col-lg-6 col-xl-6 printable-width-6">
        <!-- div class="m-portlet m-portlet--full-height m--padding-10" -->
        <div class="m-portlet m--padding-10 m--margin-bottom-5">
            <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="m-form m-form--fit m-form--label-align-right">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h3><?php echo strtoupper($item->company_description); ?></h3>
                        </div>
                    </div>
                    <div class="row m--margin-top-5 m--font-boldest">
                        <div class="col-md-12 printable-width-12 text-center">
                            <h5 class="m--marginless">RECEIPT FOR PAY</h5>
                        </div>
                        <div class="col-md-12 printable-width-12 text-center">
                            <h5 class="m--marginless">
                                PAY PERIOD <?php echo $item->date_start; ?> - <?php echo $item->date_end; ?>
                                <?php echo intval($item->is_bonus) == 1 ? " <strong>[ BONUS / 13TH MONTH ]</strong>":""; ?>
                            </h5>
                        </div>
                    </div>
                    <div class="row m--margin-top-5 m--margin-bottom-5">
                        <div class="col-md-12 printable-width-12">
                            <h5 class="m--marginless">
                            <p class="m--marginless text-justify">
                                <span>I ACKNOWLEDGE TO HAVE RECEIVED THE AMOUNT OF</span>
                                <span class="m--font-boldest" style="font-size: 16px;"><?php echo $item->net_to_text; ?> ( <span>&#8369;</span> <?php echo $item->net_pay; ?> )</span>
                                <span>AND HAVE NO FURTHER CLAIMS FOR SERVICE RENDERED</small></p>
                            </h5>
                        </div>
                    </div>
                    <div class="row m--margin-top-25 m--margin-bottom-5">
                        <div class="col-md-12 printable-width-12">
                            <h5 class="m--font-bolder">RECEIVED BY:</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 printable-width-3">&nbsp;</div>
                        <div class="col-md-9 printable-width-9 text-center">
                            <h5 class="m--marginless m--printable-line"><span class="m--font-bolder"><?php echo $item->employee_name; ?></span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $tempKey = $key % 2;
        $tenthRow = $key % 10;
        $tenthClass = $tenthRow == 0 ? "page-break-now": "";
    ?>
    <?php if($tempKey == 0): ?>
    </div>
    <div class="row justify-content-md-center <?php echo $tenthClass; ?>" >
    <?php  endif; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
</div>