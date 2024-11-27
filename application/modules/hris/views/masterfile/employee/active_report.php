<div class="printable-area">
    <div class="row mb-2 page-break-now">
        <?php if(isset($employees) && count($employees) > 0): ?>
            <?php foreach ($employees as $key => $employee): ?>
                <?php $tempSSS = preg_replace(array('/-/','/_/'), "", $employee->sss_no); ?>
                <?php $tempPHIC = preg_replace(array('/-/','/_/'), "", $employee->phealth_no); ?>
                <?php $tempHDMF = preg_replace(array('/-/','/_/'), "", $employee->pagibig_no); ?>
                <?php $tempTIN = preg_replace(array('/-/','/_/'), "", $employee->tin_no); ?>
                <div class="col-6 col-md-6 col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row row-border-bottom">
                                <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                                    <p class="mb-0" style="min-height: 20px; font-weight: 600; font-style: italic; color: #ff0000;">TO INSURE THAT YOU ARE COMPENSATED PROPERLY, PLEASE UPDATE YOUR GOVERNMENT MEMBERSHIP NUMBERS AND REQUIRED DATA.</p>
                                </div>
                            </div>
                            <div class="row row-border-bottom">
                                <div class="col-4 col-md-4 col-lg-4 col-sm-12">
                                    <p class="mb-0 row--header">NAME</p>
                                </div>
                                <div class="col-8 col-md-8 col-lg-8 col-sm-12">
                                    <h4 class="mb-0 text-right"><?php echo $employee->company; ?></h4>
                                </div>
                                <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                                <p class="mb-0" style="min-height: 20px; font-weight: 400;"><?php echo $employee->employee_name; ?></p>
                                </div>
                            </div>
                            <div class="row row-border-bottom">
                                <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                                <p class="mb-0 row--header">CONTACT NUMBER</p>
                                <p class="mb-0" style="min-height: 20px; font-weight: 400;"><?php echo $employee->mobile_no; ?></p>
                                </div>
                            </div>
                            <div class="row row-border-bottom">
                                <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                                    <p class="mb-0 row--header">SSS NUMBER</p>
                                    <p class="mb-0" style="min-height: 20px; font-weight: 400;"><?php echo $employee->sss_no && floatval($tempSSS) > 0 ? $employee->sss_no: ""; ?></p>
                                </div>
                            </div>
                            <div class="row row-border-bottom">
                                <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                                    <p class="mb-0 row--header">PHILHEALTH NUMBER</p>
                                    <p class="mb-0" style="min-height: 20px; font-weight: 400;"><?php echo $employee->phealth_no && floatval($tempPHIC) > 0 ? $employee->phealth_no: ""; ?></p>
                                </div>
                            </div>
                            <div class="row row-border-bottom">
                                <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                                    <p class="mb-0 row--header">PAGIBIG NUMBER</p>
                                    <p class="mb-0" style="min-height: 20px; font-weight: 400;"><?php echo $employee->pagibig_no && floatval($tempHDMF) ? $employee->pagibig_no: ""; ?></p>
                                </div>
                            </div>
                            <div class="row row-border-bottom">
                                <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                                    <p class="mb-0 row--header">TIN NUMBER</p>
                                    <p class="mb-0" style="min-height: 20px; font-weight: 400;"><?php echo $employee->tin_no && floatval($tempTIN) ? $employee->tin_no: ""; ?></p>
                                </div>
                            </div>
                            <div class="row row-border-bottom">
                                <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                                    <p class="mb-0 row--header">DEPARTMENT</p>
                                    <p class="mb-0" style="min-height: 20px; font-weight: 400;"><?php echo $employee->department && is_numeric($employee->department) == false ? $employee->department: "" ; ?></p>
                                </div>
                            </div>
                            <div class="row row-border-bottom">
                                <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                                    <p class="mb-2 row--header">DO YOU HAVE AN ATM ?</p>
                                    <div class="form-check form-check-inline mb-0">
                                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                        <label class="form-check-label" for="exampleCheck1">YES</label>
                                    </div>
                                    <div class="form-check form-check-inline mb-0">
                                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                        <label class="form-check-label" for="exampleCheck1">NO</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-6 col-lg-6 col-sm-12">
                                    <p class="mb-0" style="font-weight: bold;font-size: 10px;margin-top: 35px;border-top: 1px solid #000000; text-align: center; padding-top: 2px;">EMPLOYEE SIGNATURE</p>
                                </div>
                                <div class="col-6 col-md-6 col-lg-6 col-sm-12">
                                    <p class="mb-0" style="font-weight: bold;font-size: 10px;margin-top: 35px;border-top: 1px solid #000000; text-align: center; padding-top: 2px;">HEAD/SUPERVISOR SIGNATURE</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if($key % 2 == 1 && $key != 0): ?>
                    </div><div class="row mb-2 page-break-now">
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<style>
    .printable-area{ font-size: 12px; }
    .row--header{ font-weight: 600; }
    .row-border-bottom{
        margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px solid #cccccc;
    }
    .row .col-4.col-md-4.col-lg-4.col-sm-12:nth-child(1) {
        padding-right: 1px;
    }
    .row .col-4.col-md-4.col-lg-4.col-sm-12:nth-child(2) {
        padding-left: 0.5px;
        padding-right: 0.5px;
    }
    .row .col-4.col-md-4.col-lg-4.col-sm-12:nth-child(3) {
        padding-left: 1px;
    }
    @media print {
        @page {
            size: auto;
            size: portrait;
            margin: 0.25cm;
        }
        .page-break-now {
            page-break-inside: avoid;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        .col-lg {
            flex-basis: 0;
            flex-grow: 1;
            max-width: 100%; }
        .col-lg-auto {
            flex: 0 0 auto;
            width: auto;
            max-width: none; }
        .col-lg-1 {
            flex: 0 0 8.33333%;
            max-width: 8.33333%; }
        .col-lg-2 {
            flex: 0 0 16.66667%;
            max-width: 16.66667%; }
        .col-lg-3 {
            flex: 0 0 25%;
            max-width: 25%; }
        .col-lg-4 {
            flex: 0 0 33.33333%;
            max-width: 33.33333%; }
        .col-lg-5 {
            flex: 0 0 41.66667%;
            max-width: 41.66667%; }
        .col-lg-6 {
            flex: 0 0 50%;
            max-width: 50%; }
        .col-lg-7 {
            flex: 0 0 58.33333%;
            max-width: 58.33333%; }
        .col-lg-8 {
            flex: 0 0 66.66667%;
            max-width: 66.66667%; }
        .col-lg-9 {
            flex: 0 0 75%;
            max-width: 75%; }
        .col-lg-10 {
            flex: 0 0 83.33333%;
            max-width: 83.33333%; }
        .col-lg-11 {
            flex: 0 0 91.66667%;
            max-width: 91.66667%; }
        .col-lg-12 {
            flex: 0 0 100%;
            max-width: 100%; }
    }
</style>