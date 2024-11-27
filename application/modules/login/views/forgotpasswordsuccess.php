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
    <link href="<?php echo base_url();?>assets/app/js/plugins/export/export.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url();?>assets/app/js/plugins/chartist/chartist.min.css" rel="stylesheet" type="text/css" />
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
    <script src="<?php echo base_url();?>assets/app/js/amcharts.js" type="text/javascript"></script>
    <script src="<?php echo base_url();?>assets/app/js/serial.js" type="text/javascript"></script>
    <script src="<?php echo base_url();?>assets/app/js/light.js" type="text/javascript"></script>
    <script src="<?php echo base_url();?>assets/app/js/plugins/export/export.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url();?>assets/app/js/plugins/chartist/chartist.min.js" type="text/javascript"></script>

	<script src="<?php echo base_url();?>assets/js/jquery.form-validator.min.js"></script>
	<script src='https://www.google.com/recaptcha/api.js'></script>
    <!--end::Page Vendors --> 
  </head>

  	<body class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default"  >
		<div class="m-content">
			<!--begin::Portlet-->
			<div class="m-portlet">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Success
							</h3>
						</div>
					</div>
				</div>
					<div class="m-portlet__body">

						<div class="m-form__content">
							<div class="m-alert m-alert--icon alert alert-danger m--hide" role="alert" id="m_form_1_msg">
								<div class="m-alert__icon">
									<i class="la la-warning"></i>
								</div>
								<div class="m-alert__text">
									Oh snap! Change a few things up and try submitting again.
								</div>
								<div class="m-alert__close">
									<button type="button" class="close" data-close="alert" aria-label="Close"></button>
								</div>
							</div>
						</div>
						<div class="form-group m-form__group row">
							<p class='col-form-label col-lg-4 col-sm-12 offset-4'></p>
							<div class="col-lg-4 col-md-4 col-sm-12">
								<div class="alert alert-success" role="alert">
									<strong>
										Well done!
									</strong>
									We have sent you an email with reset instructions.
								</div>
							</div>
						</div>
						<div class="form-group m-form__group row">
							<p class='col-form-label col-lg-4 col-sm-12'></p>
							<div class="col-lg-4 col-md-9 col-sm-12">
								If the email does not arrive soon, check your spam folder or contact the Dev Team.
							</div>
						</div>
					</div>
					<div class="m-portlet__foot">
						<div class="m-form__actions m-form__actions">
							<div class="row">
								<div class="col-lg-8 ml-lg-auto">
									<button type="button" class="btn btn-success" onclick="location.href = '<?php echo base_url("login");?>';" >
										Continue to login
									</button>
								</div>
							</div>
						</div>
					</div>
			</div>
			<!--end::Portlet-->
		</div>
		<script type="text/javascript">
			$.validate({
			    lang: 'en'
			  });
		</script>
	</body>
</html>