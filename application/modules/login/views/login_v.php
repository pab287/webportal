<!DOCTYPE html>
<html>
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
    <style type="text/css">
        @font-face {
            font-family: "Flaticon";
            src: url("fonts/flaticon/Flaticon.eot");
            src: url("fonts/flaticon/Flaticon.eot?#iefix") format("embedded-opentype"), url("fonts/flaticon/Flaticon.woff") format("woff"), url("fonts/flaticon/Flaticon.ttf") format("truetype"), url("fonts/flaticon/Flaticon.svg#Flaticon") format("svg");
            font-weight: normal;
            font-style: normal;
        }
        .passwordgroup {
            position: relative;
            margin-bottom: 50px;
            width: 100%;
            height: 62px;
            font-family: sans-serif, Arial;
        }

        .passwordgroup input:focus {
            outline: none;
        }

        ::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
            color: #9699a2;
            opacity: 1; /* Firefox */
        }

        .passwordgroup input {
            padding: 1.5rem;
            height: 100%;
            width: 100%;
            border: none;
            background-color: #f7f6f9;
            -webkit-border-radius: 50px;
            border-radius: 50px;
            color: #91899f;
        }

        .passwordgroup text {
            position: absolute;
            top: 6px;
            right: 6px;
            z-index: 1;
            padding: 0 30px;
            height: 48px;
            text-transform: uppercase;
            line-height: 48px;
            color: #91899f;
            -webkit-border-radius: 50px;
            border-radius: 50px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 24px;
        }
        #gcc_logo{
            width: 100%;
        }

    </style>
    <!--end::Web font -->
    <!--begin::Base Styles -->
    <link href="<?php echo base_url("assets/vendors/base/vendors.bundle.min.css"); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url("assets/demo/default/base/style.bundle.min.css"); ?>" rel="stylesheet" type="text/css"/>

    <!--end::Base Styles -->
    <link rel="shortcut icon" href="<?php echo base_url("assets/favicon.ico"); ?>"/>

    <!--begin::Base Scripts -->
    <script src="<?php echo base_url('assets/js/jquery-3.3.1.min.js'); ?>"></script>
    
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
                    );
                    $this->session->set_userdata('logged_in', $sess_array);
                    redirect('portal/index', 'refresh');
                }
            } else {
                $this->form_validation->set_message('check_database', 'Invalid username or password');
            }
        }
    ?>
</head>

<body class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
<div class="m-grid m-grid--hor m-grid--root m-page">
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--singin m-login--2 m-login-2--skin-2"
         id="m_login">
        <div class="m-grid__item m-grid__item--fluid  m-login__wrapper">
            <div class="m-login__container">
                <div class="m-login__logo">
                    <a href="#">
                        <img id="gcc_logo" src="<?php echo base_url("assets/logo.png"); ?>">
                    </a>
                </div>
                <div class="m-login__signin">
                    <div class="m-login__head">
                        <h3 class="m-login__title"> Sign In To GC&amp;C </h3>
                    </div>
                    <?php echo(validation_errors()); ?>
                    <form class="m-login__form m-form" method="post"
                          action="<?php echo site_url("login/verifylogin/index"); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="form-group m-form__group passwordgroup">
                            <input class="form-control m-input" type="text" placeholder="Username" name="username"
                                   autocomplete="off">
                        </div>
                        <br>
                        <div class="passwordgroup">
                            <input id="password-field" type="password" name="password" placeholder="Password">
                            <text class="glyph-icon flaticon-visible" id="showpassword" onmousedown="showpass()"
                                  onclick="togglepass()"></text>
                        </div>
                        <div class="row m-login__form-sub">
                            <div class="col m--align-left m-login__form-left">
                                <label class="m-checkbox  m-checkbox--focus">
                                    <input type="checkbox"
                                           name="remember" <?= $tokenMatched ? "checked" : "" ?>>
                                    Remember me
                                    <span></span>
                                </label>
                            </div>
                            <div class="col m--align-right m-login__form-right">
                                <a href="<?php echo base_url('login/forgotpassword'); ?>" id="m_login_forget_password"
                                   class="m-link">
                                    Forgot Password ?
                                </a>
                            </div>
                        </div>
                        <div class="m-login__form-action">
                            <button id="m_login_signin_submit"
                                    class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary">
                                Sign In
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>    
</div>

<script type="text/javascript">
    const sessionData = <?= json_encode($session  ?? []) ?>;
	if (sessionData.auth) {
		window.location.href = '<?php echo base_url("login/authentication"); ?>';
        console.log(sessionData.auth);
	}
    function togglepass() {
        var x = document.getElementById("password-field");
        var y = document.getElementById("showpassword");
        if (x.type === "password") { x.type = "text"; y.style.color = "#bbb5c6"; } 
        else { x.type = "password"; y.style.color = "#91899f"; }
    }
    const tempData = "<?php echo sha1($sh1); ?>";
</script>
</html>