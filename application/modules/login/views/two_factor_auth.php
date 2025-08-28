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
    cursor: pointer;
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

.login-link {
    color: #007bff;
    text-decoration: none;
    cursor: pointer;
    margin-left: 10px;
}

.login-link:hover {
    text-decoration: underline;
    color: #0056b3;
}

.otp-error {
    display: none; 
    color: red;
    text-align: center;
    margin-top: 5px;
    font-size: 14px;
    font-weight: bold;
}

.otp-error.show {
    display: block; 
}

.otp-input.error {
    border: 1px solid red; /* Red border */
    outline: none; /* Remove default outline */
}

.login-link.disabled {
    display: none;
}

#timer {
    color: red; /* Style the timer text */
    font-weight: bold;
}

.radio-option.disabled {
    opacity: 0.6; /* Reduce opacity to indicate disabled state */
    pointer-events: none; /* Disable all pointer events (hover, click, etc.) */
}

.radio-option.disabled .radio-label,
.radio-option.disabled .label-text,
.radio-option.disabled .radio-description {
    cursor: not-allowed; /* Disable hover hand cursor */
    color: #999; /* Optional: Change text color to gray */
}

#two_factor_auth.loading {
    opacity: 0.8;
    pointer-events: none;
}

#two_factor_auth.loading button[type="submit"] {
    background-color: #ccc;
}

.timer-text {
    margin-top: 1rem;
    color: #999;
    font-size: 0.85em;
}
.resend-action{
    margin-top: 1rem;
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
                    <form class="m-form" id="two_factor_auth">
                        <div class="m-portlet__body">
                            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="form-group m-form__group">
                                <div class="radio-container">
                                    <div class="radio-option">
                                        <label class="radio-label">
                                            <input type="radio" name="method" value="sms">
                                            <span class="label-text">SMS</span>
                                        </label>
                                        <div class="radio-description">
                                            Use your mobile number to receive a verification code. If you choose to use SMS, a verification code will be sent to the mobile number associated with your account.
                                        </div>
                                    </div>

                                    <div class="radio-option">
                                        <label class="radio-label">
                                            <input type="radio" name="method" value="email">
                                            <span class="label-text">Email</span>
                                        </label>
                                        <div class="radio-description">
                                            Use your email address to receive a verification code. If you choose to use email, a verification code will be sent to the email address associated with your account.
                                        </div>
                                    </div>

                                    <div class="radio-option">
                                        <label class="radio-label">
                                            <input type="radio" name="method" value="telegram">
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
                                    <button type="submit" id="send_otp" class="btn btn-success" disabled>
                                        Send Code
                                    </button>
                                    <div class="text-right">
                                        <a href="#" id="backToLogin" class="login-link backToLogin">Back to login</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!--end::Form-->
                </div>
		    </div>
        </div>

        <div class="modal fade" id="m_modal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <form method="post" id="verify_otp">
                            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="form-group m-form__group text-center">
                                <img src="<?= base_url('assets/otp_icon.png')?>" width="23%">
                                <h2 class="m-portlet__head-text p-2">OTP Verification</h2>
                                <label id="otp_info">A verification code has been sent to your registered mobile number </label>
                            </div>
                            <div class="form-group m-form__group">
                                <input type="text" id="otp" name="key_code" class="form-control m-input text-center otp-input" maxlength="10" placeholder="Enter OTP">
                                <span class="otp-error col">Invalid One Time Password! You have (<span class="otp-attempts"></span>) remaining tries left before your account is locked.</span>
                                <div id="resend_tag">
                                    <div class="resend-info">
                                        <p class="text-center timer-text">
                                            Request new code in: <span id="timer"></span>
                                        </p>
                                    </div>
                                    <div class="text-center resend-action">
                                        <a href="javascript:void(0)" id="resend_otp" class="login-link disabled">
                                            <i class="la la-refresh"></i> Request New OTP
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group text-center">
                                <button type="submit" class="btn btn-primary m-btn m-btn--custom" style="width: 200px;" disabled>Verify</button>
                            </div>
                            <div class="text-right">
                                <a href="#" id="backToLogin" class="login-link backToLogin">Back to login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="otp-error" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        Error
                    </div>
                    <div class="modal-body">
                       <p id="otp-error-message">Cannot send otp at this time please try again later.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

<script type="text/javascript">
const sessionData = <?= json_encode($session  ?? []) ?>;
if (!sessionData.auth) {
    window.location.href = '<?php echo base_url("login"); ?>';
}
let request_id = "";
let timerSpan = $('#timer');
let timerInterval = 0;
let method = '';
$(document).ready(function() {
    $.ajax({
        type: 'POST',
        url: '<?= base_url('login/otp_check')?>',
        data: {
            csrf_token: '<?php echo $this->security->get_csrf_hash(); ?>',
            emp_id: sessionData.emp_id
        },
        dataType: 'json',
        success: function(response) {
            if (response.status == 'true') {
                request_id = response.request_id;
                method = response.method;
                send_to = response.send_to;
                $('#two_factor_auth button[type="submit"]').prop('disabled', true);
                if(response.method == 'sms'){
                    const mobileLastFourDigits = send_to.replace(/\D/g, '').slice(-4);
                    $('#otp_info').text(`A verification code has been sent to *** **** ${mobileLastFourDigits}`);
                    }else if(response.method == 'email'){
                        $('#otp_info').text('A verification code has been sent to your registered email address');
                    }
                $('#m_modal_1').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                checkForExistingTimer();
            }
        },
    });

    const smsRadio = $('input[value="sms"]');
    const emailRadio = $('input[value="email"]');
    const telegramRadio = $('input[value="telegram"]');

    // Disable SMS radio and add disabled class if mobile_no is not available
    if (!sessionData.contacts.mobile_no) {
        smsRadio.prop('disabled', true);
        smsRadio.prop('checked', true);
        smsRadio.closest('.radio-option').addClass('disabled');
    }

    // Disable Email radio and add disabled class if email is not available
    if (!sessionData.contacts.email) {
        emailRadio.prop('disabled', true);
        emailRadio.closest('.radio-option').addClass('disabled');
    }

    // Disable Telegram radio and add disabled class if telegram_chat_id is not available
    if (!sessionData.contacts.telegram_chat_id) {
        telegramRadio.prop('disabled', true);
        telegramRadio.closest('.radio-option').addClass('disabled');
    }

    $('.radio-option').on('click', function() {
        const radio = $(this).find('input[type="radio"]');
        radio.prop('checked', true);
        $('#send_otp').prop('disabled', false);
    });

    $('#two_factor_auth').on('submit', function(e) {
        $(this).find('button[type="submit"]')
        .prop('disabled', true)
        .html('<i class="fa fa-spinner fa-spin"></i> Authenticating...');
        e.preventDefault();
        let formData = $(this).serialize();
        formData += '&' + $.param({ sessionData: sessionData });
        $.ajax({
            type: 'POST',
            global: true,
            url: '<?= base_url('login/authenticate')?>',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
            // Show loading state
            $('#two_factor_auth').addClass('loading');
            },
            complete: function() {
            // Remove loading state
            $('#two_factor_auth')
                .removeClass('loading')
                .find('button[type="submit"]')
                .html('Send Code')
                .prop('disabled', false);
            },
            success: function(response) {
                $('#two_factor_auth button[type="submit"]').prop('disabled', true);
                if (response.status) {
                    request_id = response.request_id;
                    method = response.method;
                    if(response.method == 'sms'){
                        const mobileLastFourDigits = sessionData.contacts.mobile_no.toString().slice(-4);
                        $('#otp_info').text(`A verification code has been sent to *** **** ${mobileLastFourDigits}`);
                    }else if(response.method == 'email'){
                        $('#otp_info').text('A verification code has been sent to your registered email address');
                    }
                    $('#m_modal_1').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    startTimer(300);
                }
                else{
                    if(response.error){
                        $('#otp-error-message').text(response.error);
                    }
                    $('#otp-error').modal('show');
                }
            },
            error: function(xhr, status, error) {
                $('#otp-error').modal('show');
            }
        });
    });

    $('#m_modal_1').on('show.bs.modal', function(e) {
    $('.resend-action').hide();
});

});

// Function to start the timer
function startTimer(seconds) {
    let currentTime = Math.floor(Date.now() / 1000);
    let storedEndTime = localStorage.getItem('timerEndTime');
    if (storedEndTime && currentTime < storedEndTime) {
        timeLeft = storedEndTime - currentTime;
    } else {
        timeLeft = seconds;
        localStorage.setItem('timerEndTime', currentTime + seconds);
    }
    $('.timer-text').show();
    timerInterval = setInterval(function() {
        timeLeft--;
        $('#timer').text(timeLeft + ' seconds');
        if (timeLeft <= 0) {
            clearInterval(timerInterval); 
            $('.login-link').removeClass('disabled');
            $('.resend-action').show();
            $('.timer-text').hide();
            $('#timer').text("");
            localStorage.removeItem('timerEndTime');
        }
    }, 1000);
}
function checkForExistingTimer() {
    let storedEndTime = localStorage.getItem('timerEndTime');
    let currentTime = Math.floor(Date.now() / 1000);
    if (storedEndTime && currentTime < storedEndTime) {
        let remainingTime = storedEndTime - currentTime;
        startTimer(remainingTime);
    }else{
        startTimer(300);
    }
}


$('#verify_otp').on('submit', function(e) {
    e.preventDefault();
    let formData = $(this).serialize();
    formData += '&' + $.param({ emp_id: sessionData.emp_id,username: sessionData.username,password: sessionData.password });
    $.ajax({
        type: 'POST',
        url: '<?= base_url('login/verify_otp')?>',
        data: formData,
        dataType: 'json',
        success: function(response) {
            localStorage.removeItem('timerEndTime');
            if (response.status == 'true') {
                $('.otp-error').removeClass('show');
                $('#otp').removeClass('error');
                 window.location.replace(response.redirect);
            }
            else if(response.status == 'locked'){
                window.location.replace(response.redirect);
            }
            else{
                $('.otp-attempts').text(4-response.attempts);
                $('.otp-error').addClass('show');
                $('#otp').addClass('error');
                $('#otp').val('');
            }
        },
        });
});

    $('.backToLogin').on('click', function(e) {
        e.preventDefault();

        $.ajax({
            url: '<?= base_url('login/destroySession')?>',
            type: 'POST',
            data: {
                csrf_token: $('input[name="csrf_token"]').val()
            },
            success: function(response) {
                window.location.href = '<?php echo base_url("login"); ?>'
            },
        });
    });

    $('#otp').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        value = value.split('').join(' ');
        $(this).val(value);
        const otpLength = value.replace(/\s/g, '').length;
        if (otpLength === 6) {
            $('#verify_otp button[type="submit"]').prop('disabled', false);
        } else {
            $('#verify_otp button[type="submit"]').prop('disabled', true);
            $('.otp-error').removeClass('show');
            $('#otp').removeClass('error');
        }
    });

    $('#resend_otp').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= base_url('login/resend_otp')?>',
            method: 'POST',
            data: {
                csrf_token: $('input[name="csrf_token"]').val(),
                request_id: request_id,
                emp_id:sessionData.emp_id,
            },
            success: function(response) {
                if (response) {
                    $('#m_modal_1').modal('hide');
                }
            },
        });
    });



</script>
	</body>
</html>