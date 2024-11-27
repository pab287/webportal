<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
      GC & C | Change Password
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

	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
    <!--end::Page Vendors --> 
  </head>

  	<body class="m--skin- m-header--fixed m-header--fixed-mobile"  >
  		<?php

			$CI=&get_instance();

			$checkkey = $this->Login_m->checkkey($_GET["key"]);
			
			
		?>
		<div class="m-content" id="passkey" style="display: none;">
			<!--begin::Portlet-->
			<div class="row">
				<div class="col-lg-4 col-sm-12 col-xs-12"></div>
				<div class="m-portlet col-lg-4 col-md-6 col-sm-12 col-xs-12 mt-5">
					<div class="m-portlet__head">
						<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<span class="m-portlet__head-icon"><img src="<?= base_url('assets/logo.png')?>" width="20%"></img></span>
							</div>
						</div>
						<div class="m-portlet__head-tools">
							<ul class="m-portlet__nav">
								<li class="m-portlet__nav-item">
									<h4 class="m-portlet__head-text mt-2">
										Change Password
									</h4>
								</li>
							</ul>
						</div>
					</div>
					<!--begin::Form-->
					<form class="m-form" id="changepasswordform" method="POST" action="<?php echo base_url('login/Forgotpassword/processchangepassword')?>">
						<div class="m-portlet__body">
						<input type="hidden" name="email" value="<?php echo $checkkey["email"]?>">
						<input type="hidden" name="redirectlink" value="<?php echo $checkkey["redirectlink"]?>">
						<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
							<div class="form-group m-form__group">
								<label>
									New Password *
								</label>
								<div> 
									<input type="password" class="form-control m-input" name="password_confirmation" data-validation="required length strength" data-validation-length="min8" data-validation-strength="3">
									<span class="m-form__help">
										<ul>
											<li>Password must contain numbers.</li>
											<li>Password must contain uppercase letters.</li>
											<li>Password must have at least one @#$ symbol.</li>
											<li>Length must be greater than 8 characters.</li>
										</ul>
									</span>
								</div>
							</div>
							<div class="form-group m-form__group">
								<label>
									Confirm Password *
								</label>
								<div> 
									<input type="password" class="form-control m-input" name="password" data-validation="confirmation" >
								</div>
							</div>
						</div>
						<div class="m-portlet__foot">
							<div class="form-group m-form__group">
								<div class="text-center">
									<button type="submit" class="btn btn-success">
										Change password
									</button>
								</div>
							</div>
						</div>
					</form>
					<!--end::Form-->
				</div>
			</div>
			<!--end::Portlet-->
		</div>

		<div class="m-content" id="failkey" style="display: none;">
			<!--begin::Portlet-->
			<div class="m-portlet">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Change your password
							</h3>
						</div>
					</div>
				</div>
				<!--begin::Form-->
				<div class="m-portlet__body">
					<div class="form-group m-form__group row">
						<div class="alert alert-danger" role="alert">
							<strong>
								Error!
							</strong>
								Your link has expired.
						</div>
					</div>
				</div>
				<!--end::Form-->
			</div>
			<!--end::Portlet-->
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
				$.validate({
					form : '#changepasswordform',
					modules: 'security'
				});
			});
		</script>
		<?php 
			if($checkkey){
				echo "<script>$('#passkey').show();</script>";
			}else{
				echo "<script>$('#failkey').show();</script>";
			}
		?>
	</body>
</html>