<div class="modal fade" id="force_change_modal"
     tabindex="-1">
    <div class="modal-dialog" role="dialog">
    <style>
        .form-group .password-container {
            position: relative;
        }

        .form-group .m-input {
            padding-right: 35px; /* Adjust based on the size of your icon */
        }

        .form-group .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Password Update Required</h5>
            </div>
            <div class="modal-body">
                <form class="m-form" id="changepasswordform">
                    <div class="m-portlet__body">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" name="username">
                        <div class="form-group m-form__group">
                        <label>New Password <span style="color: red;">*</span></label>
                            <div class="password-container">
                                <input id="newPasswordInput" type="password" class="form-control m-input" name="password_confirmation" data-validation="required length strength symbol" data-validation-length="min8" data-validation-strength="3" autocomplete="off" style="text-transform: none;">
                                <span id="newPasswordToggle" class="password-toggle"><i class="fa fa-eye"></i></span>
                            </div>
                        </div>
                        <span class="m-form__help">
                            <ul>
                                <li>Password must contain numbers.</li>
                                <li>Password must contain uppercase letters.</li>
                                <li>Password must have at least one symbol (e.g. !@#).</li>
                                <li>Password must be greater than 8 characters.</li>
                            </ul>
                        </span>
                        <div class="form-group m-form__group">
                            <label>Confirm Password <span style="color: red;">*</span></label>
                            <div class="password-container">
                                <input id="confirmPasswordInput" type="password" class="form-control m-input" name="password" data-validation="confirmation" style="text-transform: none;">
                                <span id="confirmPasswordToggle" class="password-toggle"><i class="fa fa-eye"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot mt-2">
                        <div class="form-group m-form__group">
                            <div class="text-center">
                                <button type="submit" class="btn btn-success btnSave">
                                    Change password
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    <?php $session = $this->session;?>
    let session = <?php echo json_encode($session->userdata("logged_in")) ?>;
	$('input[name="username"]').val(session.username || '');
    $('.password-toggle').on('click', function(e) {
        e.preventDefault();
        var $pwd = $(this).siblings('.m-input');
        $pwd.attr('type', $pwd.attr('type') === 'password' ? 'text' : 'password');
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });	

$(document).ready(function() {

    if (session.last_update == null) {
        $(".password-change-reminder").modal("show");
    } else {
        let lastUpdateDate = new Date(session.last_update);
        let sixtyDaysAgo = new Date();
        sixtyDaysAgo.setDate(sixtyDaysAgo.getDate() - 60);
        
        if (lastUpdateDate <= sixtyDaysAgo) {
            $(".password-change-reminder").modal("show");
            if(sesh.waive_update > 3){
                $("#changePasswordLater").hide();
            }
        }
    }

    const $passwordInput = $('#newPasswordInput');
    const $helpSection = $('.m-form__help');
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
            condition: (val) => /[^\w\s]/.test(val),
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

    $.formUtils.addValidator({
        name: 'symbol', // Name of the validator
        validatorFunction: function(value, $el, config, language, $form) {
            return /[^\w\s]/.test(value);
        },
        errorMessage: 'The input must contain at least one symbol (e.g., !, @, #, $, etc.).',
        errorMessageKey: 'missingSymbol'
    });


    $.validate({
        form : '#changepasswordform',
        modules: 'security'
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
                    // window.location.replace(response.redirect);
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