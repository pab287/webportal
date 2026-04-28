<!DOCTYPE html>
<html lang="en" id="printableArea">
<!-- begin::Head -->
<head>
    <meta charset="utf-8" />
    <title><?php echo ($this->core_layout->getPageTitle())? "GC&amp;C | ".$this->core_layout->getPageTitle(): "GC&amp;C | External"; ?></title>
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
    <style>
        #regestration .file_label{
            background-color: #0d6efd;
            color: white;
            padding: 0.5rem;
            border-radius: 0.3rem;
            cursor: pointer;
            margin-top: 1rem;
        }
        #regestration label{
            font-size: 15px;
            font-weight: 600;
            color: #000000;
            text-transform: uppercase;
        }
        #regestration .file_label{
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            padding: 5px 10px;
        }
        #regestration .select2-selection--multiple{
            border: 1px solid #ebedf2;
        }
        #regestration .select2-selection.select2-selection--multiple{
            padding: 5px 10px;
        }
        #regestration #Status,
        #regestration #Recruitement{
            width: 100%;
        }
        #regestration #Remarks{
            resize:none;
        }
        #regestration .form-group.has-error .select2-selection, 
        #regestration .form-group .has-error .select2-selection {
            border-color: rgb(185, 74, 72);
        }
        #regestration input {
            font-size: 18px;
        }
    </style>
</head>
<body class="m-page--wide m-header--fixed m-header--fixed-mobile m-footer--push m-aside--offcanvas-default m-header--minimize-off">
    <div id="background_motion" class="m-grid m-grid--hor m-grid--root m-page">
            <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver m-container m-container--responsive m-container--xxl m-page__container">
                <div class="m-grid__item m-grid__item--fluid m-wrapper">