<!DOCTYPE html>
<html lang="en" id="printableArea">
<!-- begin::Head -->
<head>
    <meta charset="utf-8" />
    <title>Printable Form</title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url("assets/favicon.ico"); ?>">
    <script src="<?php echo base_url('assets/js/jquery-3.3.1.min.js'); ?>"></script>
    <!--begin::Base Scripts -->
    <script src="<?php echo base_url("assets/vendors/base/vendors.bundle.js"); ?>" type="text/javascript"></script>
    <script src="<?php echo base_url("assets/demo/demo3/base/scripts.bundle.js"); ?>" type="text/javascript"></script>
    <!--end::Base Scripts -->  
    
    <!-- to be remove -- script src="<?php echo base_url('assets/plugins/bootstrap/bootstrap.min.js'); ?>"></script -->
    <script src="<?php echo base_url('assets/js/jquery.dataTables.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/dataTables.bootstrap4.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/form-validator/jquery.form-validator.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/vue.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/currency.js'); ?>"></script>
    <?php echo $this->core_layout->getStoredJs(); ?>

    <link href="<?php echo base_url("assets/fonts/montserrat/montserrat.css"); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url("assets/fonts/roboto/roboto.css"); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url("assets/plugins/bootstrap/bootstrap.min.css"); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url("assets/vendors/custom/datatables/datatables.bundle.css"); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url("assets/demo/demo3/base/style.bundle.css"); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url("assets/vendors/base/vendors.bundle.css"); ?>" rel="stylesheet" type="text/css" />
    <?php echo $this->core_layout->getStoredCss(); ?>
</head>
<body class="m-page--wide m-header--fixed m-header--fixed-mobile m-footer--push m-aside--offcanvas-default m-header--minimize-off">
    <div class="m-grid m-grid--hor m-grid--root m-page">
            <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver m-container m-container--responsive m-container--xxl m-page__container">
                <div id="append_printable-container" class="m-grid__item m-grid__item--fluid m-wrapper">
                    <div id="wait_header" class="text-center m--margin-top-30">
                        <h5 class="text-uppercase">Please wait! <small class="m--font-bolder ml-3">System is loading printable data.</small></h5>
                    </div>