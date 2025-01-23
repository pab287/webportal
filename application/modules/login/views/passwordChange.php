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
		<div class="m-content" id="passkey">
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
					<form class="m-form" id="changepasswordform">
						<div class="m-portlet__body">
						<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
						<input type="hidden" name="username">
						<input type="hidden" name="old_password" >
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
<script type="text/javascript">
	const sessionData = <?= json_encode($session  ?? []) ?>;
	if (!sessionData.modal) {
		window.location.href = '<?php echo base_url("login"); ?>';
	}
	$('input[name="username"]').val(sessionData.post.username || '');
	$('input[name="old_password"]').val(sessionData.post.password || '');
	
			$(document).ready(function(){
				$.validate({
					form : '#changepasswordform',
					modules: 'security'
				});
			});

$(document).ready(function() {
    const $passwordInput = $('input[name="password_confirmation"]');
    const $helpSection = $passwordInput.next('.m-form__help');
    const conditions = {
        length: false,
        numbers: false,
        uppercase: false,
        symbols: false
    };

    const validationRules = [
        { 
            condition: (val) => val.length >= 8, 
            key: 'length',
            element: $helpSection.find('li:nth-child(4)')
        },
        { 
            condition: (val) => /\d/.test(val), 
            key: 'numbers',
            element: $helpSection.find('li:nth-child(1)')
        },
        { 
            condition: (val) => /[A-Z]/.test(val), 
            key: 'uppercase',
            element: $helpSection.find('li:nth-child(2)')
        },
        { 
            condition: (val) => /[@#$]/.test(val), 
            key: 'symbols',
            element: $helpSection.find('li:nth-child(3)')
        }
    ];

    $passwordInput.on('input', function() {
        const value = $(this).val();
        
        validationRules.forEach(rule => {
            conditions[rule.key] = rule.condition(value);
            rule.element.css('text-decoration', conditions[rule.key] ? 'line-through' : 'none');
        });

        $helpSection.toggle(!Object.values(conditions).every(Boolean));
    });

	$('#changepasswordform').on('submit', function(e) {
        e.preventDefault();
		var formData = $(this).serialize();
		$.ajax({
            url: '<?php echo base_url('login/update_password'); ?>',
            type: 'POST',
            data: formData,
			dataType: 'json',
            success: function(response) {
                if (response.status) {
                    window.location.href = '<?php echo "portal/index"; ?>';
                } else {
                    alert('Error updating password. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX error
                console.error("AJAX Error:", error);
                alert('An error occurred while updating password.');
            }
        });
	})


});

</script>
	</body>
</html>