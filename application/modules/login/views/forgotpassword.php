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
	<script src="https://www.google.com/recaptcha/api.js"></script>

	<script src="<?php echo base_url();?>assets/js/jquery.min.js"></script>
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
    <!--end::Page Vendors --> 
	<style>
		.g-recaptcha {
        	display: inline-block;
    	}
	</style>
  </head>

  	<body class="m--skin- m-header--fixed m-header--fixed-mobile">
		<div class="m-content">
			<!--begin::Portlet-->
			<div class="row">
				<div class="col-lg-4 col-sm-12 col-xs-12"></div>
				<div class="m-portlet col-lg-4 col-md-6 col-sm-12 col-xs-12 mt-5">
					<div class="m-portlet__head text-center">
						<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<span class="m-portlet__head-icon"><img src="<?= base_url('assets/logo.png')?>" width="20%"></img></span>
							</div>
						</div>
						<div class="m-portlet__head-tools">
							<ul class="m-portlet__nav">
								<li class="m-portlet__nav-item">
									<h4 class="m-portlet__head-text mt-2">
										Forgot Password
									</h4>
								</li>
							</ul>
						</div>
					</div>
					<!--begin::Form-->
					<form class="m-form" id="m_form_1" method="POST" action="<?php echo base_url('login/Forgotpassword/process')?>">
					<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
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
							<div class="form-group m-form__group">
								<p class="">
									Please enter your email address below to receive a password reset link.
								</p>
							</div>
							<div class="form-group m-form__group">
								<label class="col-form-label">
									Email *
								</label>
								<div class="text-center"> 
									<input type="text" class="form-control m-input" name="email" data-validation="email" placeholder="Enter your email">
									<p>
										<span class="m--font-danger">
											<?php echo $this->session->err_msg; $this->session->unset_userdata("err_msg");?>
										</span>
									</p>
									<span class="m-form__help">
										We'll never share your email with anyone else.
									</span>
								</div>
							</div>
							<div class="form-group m-form__group">
								<div class="text-center">
									<div id="captcha" class="g-recaptcha" data-sitekey="6LfYy9QlAAAAAHNzof6dl4R4O_GkFrFeyRc42zdU"></div>
								</div>
							</div>
						</div>
						<div class="m-portlet__foot">
							<div class="form-group m-form__group">
								<div class="text-center">
									<button type="submit" class="btn btn-success mt-1">
										Reset my password
									</button>
									<button type="button" id="go_to_login" class="btn btn-primary mt-1">
										Go back to login
									</button>
								</div>
							</div>
						</div>
					</form>
					<!--end::Form-->
				</div>
				<div class="col-lg-4 col-sm-12 col-xs-12"></div>
			</div>
			<!--end::Portlet-->
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
				$.validate({
			    	lang: 'en',
			  	});
				grecaptcha.ready(() => {
					grecaptcha.render(document.getElementById('captcha'), {
						'sitekey' : '6LfYy9QlAAAAAHNzof6dl4R4O_GkFrFeyRc42zdU'
					});
				});

				$("#m_form_1").submit(function(){
					if (grecaptcha.getResponse() == ""){
					    alert("Verify the captcha to proceed.");
					    return false;
					} else {
					    return true;
					}
				});

				$("#go_to_login").click(function(){
					window.location.replace('<?php echo site_url("login")?>');
				});

			});
		</script>
	</body>
</html>