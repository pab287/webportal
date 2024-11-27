<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
      GC & C | Forgot Password
    </title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
          WebFont.load({
            google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
            active: function() {
                sessionStorage.fonts = true;
            }
          });
    </script>
    <!--end::Web font -->
        <!--begin::Base Styles -->
    <link href="<?php echo base_url(); ?>assets/vendors/base/vendors.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/demo/default/base/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Base Styles -->
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/favicon.ico" />

    <!--begin::Base Scripts -->
    <script src="<?php echo base_url(); ?>assets/vendors/base/vendors.bundle.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/demo/default/base/scripts.bundle.js" type="text/javascript"></script>
    <!--end::Base Scripts -->   
        <!--begin::Page Snippets -->
    <!-- <script src="<?php echo base_url(); ?>assets/snippets/pages/user/login.js" type="text/javascript"></script> -->
    <!--end::Page Snippets -->

    <!--begin::Page Vendors --> 

	<script src="<?php echo base_url();?>assets/js/jquery.form-validator.min.js"></script>
	<script src='https://www.google.com/recaptcha/api.js'></script>
    <!--end::Page Vendors --> 
  </head>

  	<body class="m--skin- m-header--fixed m-header--fixed-mobile">
		<div class="m-content">
			<!--begin::Portlet-->
			<div class="row">
				<div class="col-sm-1 col-xs-1 col-lg-1 col-xl-4"></div>
				<div class="m-portlet col-xs-9 col-sm-9 col-md-10 col-lg-10 col-xl-4 mt-5">
					<div class="m-portlet__head">
						<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<div class="m-portlet__head-icon">
									<img src="<?= base_url('assets/logo.png')?>" style="width: 20%;" alt>
									</img>
								</div>
							</div>
						</div>
						<div class="m-portlet__head-tools">
							<ul class="m-portlet__nav">
								<li class="m-portlet__nav-item">
									<h4 class="m-portlet__head-text mt-2">
										Success!
									</h4>
								</li>
							</ul>
						</div>
					</div>
					<div class="m-portlet__body">
						<div class="form-group m-form__group">
							<div class="text-center">
								<div class="alert alert-success" role="alert">
									<strong>
										Well done!
									</strong>
									Your account password has been successfully changed.
								</div>
							</div>
						</div>
						<div class="form-group m-form__group">
							<div class="text-center">
								Please login to GCC WEBPORTAL with your new password.
							</div>
						</div>
					</div>
					<div class="m-portlet__foot">
						<div class="form-group m-form__group">
							<div class="text-center">
								<button type="button" class="btn btn-success" onclick="location.href = '<?php echo base_url("login");?>';">
									Continue to login
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--end::Portlet-->
		</div>
	</body>
</html>