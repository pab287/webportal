<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
      GC & C | Two Factor Authentication
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
    <style>

.radio-container {
    width: 100%;
    margin: 20px 0;
}

.radio-option {
    border: 1px solid #ebedf2;
    border-radius: 4px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.radio-option:hover {
    border-color: #716aca;
    background-color: #f7f6fc;
}

.radio-label {
    display: flex;
    align-items: flex-start;
    cursor: pointer;
    margin-bottom: 0.5rem;
}

.label-text {
    font-weight: 500;
    font-size: 1.1rem;
    margin-left: 0.5rem;
}

.radio-description {
    margin-left: 1.7rem;
    color: #666;
    font-size: 0.9rem;
    line-height: 1.5;
}

input[type="radio"] {
    margin-top: 0.3rem;
    cursor: pointer;
}

/* Custom radio button styling */
input[type="radio"] {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    border: 2px solid #716aca;
    border-radius: 50%;
    outline: none;
    position: relative;
}

input[type="radio"]:checked {
    background-color: #716aca;
}

input[type="radio"]:checked::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    background-color: white;
    border-radius: 50%;
}


    </style>
  </head>
    <body class="align-items-center justify-content-center">
        <div class="row">
            <div class="col" id="passkey">
                <div class="m-portlet m-login__signin">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<span class="m-portlet__head-icon"><img src="<?= base_url('assets/logo.png')?>" width="23%"></img></span>
							</div>
						</div>
                        <div class="m-portlet__head-tools">
                            <ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item">
                                    <h4 class="m-portlet__head-text mt-2">
                                    Two Factor Authentication
                                    </h4>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!--begin::Form-->
                    <form class="m-form" id="two_factor_auth" method="POST">
                        <div class="m-portlet__body">
                            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="form-group m-form__group">
                                <div class="radio-container">
                                    <div class="radio-option">
                                        <label class="radio-label">
                                            <input type="radio" name="method" value="sms" checked>
                                            <span class="label-text">SMS</span>
                                        </label>
                                        <div class="radio-description">
                                            Use your mobile number to receive a verification code. If you choose to use SMS, a verification code will be sent to the mobile number associated with your account.
                                        </div>
                                    </div>

                                    <div class="radio-option">
                                        <label class="radio-label">
                                            <input type="radio" name="method" value="email" disabled>
                                            <span class="label-text">Email</span>
                                        </label>
                                        <div class="radio-description">
                                            Use your email address to receive a verification code. If you choose to use email, a verification code will be sent to the email address associated with your account.
                                        </div>
                                    </div>

                                    <div class="radio-option">
                                        <label class="radio-label">
                                            <input type="radio" name="method" value="telegram" disabled>
                                            <span class="label-text">Telegram</span>
                                        </label>
                                        <div class="radio-description">
                                            Use telegram to receive a verification code. If you choose to use Telegram, a verification code will be sent to the Telegram number associated with your account.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-portlet__foot">
                            <div class="form-group m-form__group">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success">
                                        Send Code
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!--end::Form-->
                </div>
		    </div>
        </div>
<script type="text/javascript">

const sessionData = <?= json_encode($session  ?? []) ?>;
console.log(sessionData);
	// if (!sessionData.auth) {
	// 	window.location.href = '<?php echo base_url("login"); ?>';
	// }

 $('#two_factor_auth').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        
        $.ajax({
            type: 'POST',
            url: '<?= base_url('login/authenticate')?>',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Form submitted successfully:', response);
               
            },
            error: function(xhr, status, error) {
                console.error('Error submitting form:', error);
                // Handle error (e.g., show error message)
            }
        });
    });

</script>
	</body>
</html>