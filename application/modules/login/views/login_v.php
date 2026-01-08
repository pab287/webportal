<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>GC&amp;C Inc.</title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--begin::Web font -->
    <?php $sh1 = "direct_access-".date("Ymd"); ?>
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
        WebFont.load({
            google: {"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]},
            active: function () {
                sessionStorage.fonts = true;
            }
        });
    </script>
    <!--end::Base Styles -->
    <link rel="shortcut icon" href="<?php echo base_url("assets/favicon.ico"); ?>"/>

    <!--begin::Base Scripts -->
    <script src="<?php echo base_url('assets/js/jquery-3.3.1.min.js'); ?>"></script>
    <script src="<?php echo base_url(); ?>assets/vendors/base/vendors.bundle.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/demo/default/base/scripts.bundle.js" type="text/javascript"></script>
    <link href="<?php echo base_url("assets/vendors/base/vendors.bundle.min.css"); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url("assets/demo/default/base/style.bundle.min.css"); ?>" rel="stylesheet" type="text/css"/>
    <!--end::Base Scripts -->
    <!--begin::Page Vendors -->
    <!--end::Page Vendors -->
    <?php $session = $this->session->userdata("logged_in"); ?>
    <?php $redirectUrl = base_url("portal/index"); ?>
    <?php if ($session) {
        header("location: {$redirectUrl}");
    } ?>

    <?php
        $remember_token = $this->input->cookie('remember_me', TRUE);
        $tokenMatched = false;
        if (isset($remember_token) && $remember_token) {
            $this->load->model('Login_m', "login_m");
            $row = $this->login_m->loginUsingRememberToken($remember_token);
            if ($row) {
                $tokenMatched = true;
                $id = $row->id;
                $privileges = $this->login_m->get_privileges_by_id($id);
                $sess_array = array();

                if ($row->is_suspended == 1) {
                    $this->form_validation->set_message('check_database', 'This user account is suspended.');
                } else {
                    $sess_array = array(
                        'id' => $row->id,//tbluser_id
                        'emp_id' => $row->emp_id,
                        'username' => $row->username,
                        'firstname' => $row->firstname,
                        'middlename' => $row->middlename,
                        'lastname' => $row->lastname,
                        'privileges' => $privileges,
                        'suffix' => $row->suffix,
                        'group_id' => $row->group_id,
                        'email' => $row->email,
                        'company' => $row->company_id,
                        'department' => $row->department_id,
                        'TwoFactorAuth' => $row->auth,
                        'next_update' => $row->next_update,
                        'waive_count' => $row->waive_password_update,
                        'is_important' => $row->is_important,
                    );
                    $this->session->set_userdata('logged_in', $sess_array);
                    redirect('portal/index', 'refresh');
                }
            } else {
                $this->form_validation->set_message('check_database', 'Invalid username or password');
            }
        }
    ?>

<style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    .login-container {
      width: 100%;
      max-width: 500px;
      padding: 30px;
      text-align: center;
    }

    .login-container img {
      width: 400px;
      margin-bottom: 20px;
    }

    .login-container h2 {
      font-size: 18px;
      color: #333;
      margin-bottom: 25px;
    }

    .login-container form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .input-group {
      position: relative;
    }

    .input-group input {
      width: 100%;
      padding: 12px 15px;
      border: none;
      border-radius: 25px;
      background: #f5f5f5;
      outline: none;
      font-size: 14px;
    }

    .input-group input[type="password"] {
      padding-right: 40px;
    }

    .input-group .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 16px;
      color: #888;
    }

    .options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 13px;
      color: #555;
    }

    .options label {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .options a {
      color: #8e2de2;
      text-decoration: none;
    }
    @media (max-width: 550px) {
      .login-container {
        padding: 20px;
      }
    }
  </style>

</head>
<body id="m_login">
    <div class="login-container">
        <img src="<?php echo base_url("assets/logo.png"); ?>" alt="GC&C Logo"> 
        <h2>SIGN IN TO GC&C</h2>
        <form method="post" action="<?php echo site_url('login/verifylogin/index'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <?php echo(validation_errors()); ?>
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" autocomplete="off" maxlength="50">
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" autocomplete="off" maxlength="50" id="password">
                <span class="toggle-password" onclick="togglepass()">👁</span>
            </div>
            
            <div class="options">
                <label class="m-checkbox  m-checkbox--focus"><input type="checkbox" name="remember" <?= $tokenMatched ? "checked" : "" ?> > Remember me  <span></span></label>
                <a href="<?php echo base_url('login/forgotpassword'); ?>" class="m-link" id="m_login_forget_password">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary">Sign In</button>
        </form>
    </div>
    <div class="modal fade" id="m_modal_unlock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="unlock-form" onsubmit="handleUnlockSubmit(event);" onkeydown="return event.key !== 'Enter';">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            ACCOUNT LOCKED OUT
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    ×
                                </span>
                        </button>
                    </div>
                    <div class="modal-body" style="font-size: 16px;">
                        <p>Your account has been locked out. Click 'UNLOCK' to unlock your account. If you are still having trouble unlocking your account, please contact your direct supervisor for further assistance.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary">
                            UNLOCK
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="m_modal_contact" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        ACCOUNT LOCKED OUT
                    </h5>
                    <a class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.href='<?php echo site_url('/'); ?>'">
                        <span aria-hidden="true"></span>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="contact-details">
                        <div class="contact-item mb-3">
                            <label class="font-weight-bold d-block mb-1">Mobile Number:</label>
                            <span class="text-muted" id="contact_mobile">Not available</span>
                        </div>
                        <div class="contact-item mb-3">
                            <label class="font-weight-bold d-block mb-1">Email Address:</label>
                            <span class="text-muted" id="contact_email">Not available</span>
                        </div>
                        <div class="alert alert-info mt-3 mb-0" style="font-size: 16px;">
                            <i class="fa fa-info-circle"></i> 
                            A temporary password has been sent to your registered contact information.<br/> If you don't receive it within a few minutes, please contact IT Support.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript">
    const sessionData = <?= json_encode($session  ?? []) ?>;
    if (sessionData.auth) {
        window.location.href = '<?php echo base_url("login/authentication"); ?>';
    }
    function togglepass() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
    const tempData = "<?php echo sha1($sh1); ?>";

    $(document).ready(function() {
        function getCookie(name) {
            let matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([.$?*|{}()\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            return matches ? decodeURIComponent(matches[1]) : undefined;
        }

        const isLockedOut = getCookie('lockout_user');
        if (isLockedOut) {
            $('input[name="username"]').val(getCookie('username'));
            $('#m_modal_unlock').modal('show');
            $('#m_login_forget_password').hide();
            document.cookie = "lockout_user=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "username=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        }
    });

    function handleUnlockSubmit(event) {
        event.preventDefault();
        const username = $('input[name="username"]').val();
        const $submitButton = $('#unlock-form button[type="submit"]');
        $submitButton.prop('disabled', true).html('UNLOCKING');
        var unlockUrl = "<?php echo base_url('login/verifylogin/unlock_account'); ?>";
        $.ajax({
            url: unlockUrl,
            type: 'POST',
            data: {
                    csrf_token: '<?php echo $this->security->get_csrf_hash(); ?>',
                    username: username
                },
            dataType: 'json', 
            success: function(response) {
                if (response.status) {
                    $('#m_modal_unlock').modal('hide');
                    toastr.success('Account unlocked successfully!');
                    $('#contact_mobile').text(response.mobile || 'Not provided');
                    $('#contact_email').text(response.email || 'Not provided');
                    $('#m_modal_contact').modal('show');
                } else {
                    toastr.error('Failed to unlock account: ' + response.message);
                }
            },
            complete: function() {
                $submitButton.prop('disabled', false).html('Unlock');
            }
        });
    }

</script>
</html>