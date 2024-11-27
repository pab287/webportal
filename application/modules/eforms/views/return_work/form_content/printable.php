<div class="m-content" style="font-size: 16px;">
    <div class="row m--margin-bottom-20">
        <div class="col-md-6 text-left">
            <h2 class="m--font-boldest"><?php echo $company; ?></h2>
        </div>
        <div class="col-md-6 text-right">
            <h2><small class="m--font-bold" style="margin-right: 20px;">CONTROL # </small><span class="m--font-boldest"><?php echo $reference_no; ?></span></h2>
        </div>
    </div>
    <div class="row m--margin-bottom-10">
        <div class="col-md-12 text-center" style="border-bottom: 1px dashed;">
            <h4 class="m--marginless">RETURN TO WORK FORM (RTWF)</h4>
            <p>GCC-431 REV 2 10/21/2020</p>
        </div>
    </div>
    <div class="row m--margin-bottom-10" style="border-bottom: 2px solid #565656;">
        <div class="col-md-6 text-left">
            <div class="row">
                <div class="col-md-2">
                    <span>NAME: </span>
                </div>
                <div class="col-md-10">
                    <span class="m--font-boldest m--margin-left-10"><?php echo $requested_name; ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-left">
            <div class="row">
                <div class="col-md-3">
                    <span>DATE: </span>
                </div>
                <div class="col-md-9">
                    <span class="m--font-boldest m--margin-left-10"><?php echo ($dt_created)? date("Y-m-d H:i", strtotime($dt_created)): "---"; ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-left m--margin-bottom-10">
            <div class="row">
                <div class="col-md-2">
                    <span>POSITION: </span>
                </div>
                <div class="col-md-10">
                    <span class="m--font-boldest m--margin-left-10"><?php echo ($position)? $position: "---"; ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-left m--margin-bottom-10">
            <div class="row">
                <div class="col-md-3">
                    <span>DEPARTMENT: </span>
                </div>
                <div class="col-md-9">
                    <span class="m--font-boldest m--margin-left-10"><?php echo ($department)? $department: "---"; ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="row m--margin-bottom-10" style="border-bottom: 2px solid #565656;">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12 m--margin-bottom-10">
                    <h5>
                    <?php if($return_type == 0): ?>
                        <i class="fa fa-lg fa-check-square-o m--margin-right-10"></i>
                    <?php else: ?>
                        <i class="fa fa-lg fa-square-o m--margin-right-10"></i>
                    <?php endif; ?>
                    UNAUTHORIZED ABSENCE / NO NOTIFICATION</h5>
                </div>
                <div class="col-md-12 text-left m--margin-bottom-10">
                    <div class="row">
                        <div class="col-md-4">
                            <span>DATE FROM: </span>
                        </div>
                        <div class="col-md-3">
                            <span class="m--font-boldest m--margin-left-10"><?php echo ($return_type == 0 && $dt_from)? $dt_from: "---"; ?></span>
                        </div>
                        <div class="col-md-1">
                            <span>TO: </span>
                        </div>
                        <div class="col-md-3">
                            <span class="m--font-boldest m--margin-left-10"><?php echo ($return_type == 0 && $dt_to)? $dt_to: "---"; ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-left">
                    <div class="row">
                        <div class="col-md-12">
                            <span>ADDRESS ON LEAVE: </span>
                        </div>
                        <div class="col-md-12">
                            <p class="m--marginless m--font-boldest"><?php echo ($return_type == 0 && $address)? $address: "---"; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-left m--margin-bottom-10">
                    <div class="row">
                        <div class="col-md-10">
                            <span>REASON FOR LEAVE / NO NOTIFICATION: </span>
                        </div>
                        <div class="col-md-12">
                            <p class="m--marginless m--font-boldest"><?php echo ($return_type == 0 && $reason)? $reason: "---"; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row m--margin-bottom-10">
                <div class="col-md-12">
                    <h5>
                        <?php if($return_type == 1): ?>
                            <i class="fa fa-lg fa-check-square-o m--margin-right-10"></i>
                        <?php else: ?>
                            <i class="fa fa-lg fa-square-o m--margin-right-10"></i>
                        <?php endif; ?>
                        RECALLED TO REPORT FOR DUTY
                    </h5>
                </div>
                <div class="col-md-12">
                    <h5>
                        <?php if($return_type == 2): ?>
                            <i class="fa fa-lg fa-check-square-o m--margin-right-10"></i>
                        <?php else: ?>
                            <i class="fa fa-lg fa-square-o m--margin-right-10"></i>
                        <?php endif; ?>
                        REQUESTED TO EXTEND DAYS OF WORK
                    </h5>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-left m--margin-bottom-10">
                    <div class="row">
                        <?php if(($return_type == 1 || $return_type == 2) && ($dt_to > $dt_from)): ?>
                        <div class="col-md-5">
                            <span>DATE OF DUTY FROM: </span>
                        </div>
                        <?php else: ?>
                        <div class="col-md-3 ">
                            <span>DATE OF DUTY: </span>
                        </div>
                        <?php endif; ?>
                        <?php if(($return_type == 1 || $return_type == 2) && ($dt_to > $dt_from)): ?>
                        <div class="col-md-3">
                            <span class="m--font-boldest"><?php echo (($return_type == 1 || $return_type == 2) && $dt_from)? $dt_from: "---"; ?></span>
                        </div>
                        <div class="col-md-1">
                            TO
                        </div>
                        <div class="col-md-3">
                            <span class="m--font-boldest"><?php echo (($return_type == 1 || $return_type == 2) && $dt_to)? $dt_to: "---"; ?></span>
                        </div>
                        <?php else: ?>
                        <div class="col-md-9">
                            <span class="m--font-boldest"><?php echo (($return_type == 1 || $return_type == 2) && $dt_from)? $dt_from: "---"; ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-12 text-left m--margin-bottom-10">
                    <div class="row">
                        <div class="col-md-12">
                            <span>REASON FOR REPORTING: </span>
                        </div>
                        <div class="col-md-12">
                            <p class="m--marginless m--font-boldest"><?php echo (($return_type == 1 || $return_type == 2) && $reason)? strtoupper($reason): "---"; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <h6 class="m--margin-bottom-50">PREPARED BY: </h6>
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="m--marginless m--font-boldest" style="border-bottom: 1px dashed; padding-bottom: 15px;"><?php echo $requested_name; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h6 class="m--margin-bottom-50">APPROVED BY: </h6>
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="m--font-boldest" style="border-bottom: 1px dashed; padding-bottom: 15px;"><?php echo $approved_name; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener('load', function() { 
        window.focus();
        window.print();
     }, false);
</script>