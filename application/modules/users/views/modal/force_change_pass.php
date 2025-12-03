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
                        <input type="hidden" name="username" value="<?php echo $this->session->userdata('logged_in')['username'] ?>">
                        <div class="form-group m-form__group">
                        <label>New Password <span style="color: red;">*</span></label>
                            <div class="password-container">
                                <input id="newPasswordInput" type="password" class="form-control m-input" name="password_confirmation" autocomplete="off" style="text-transform: none;">
                                <span id="newPasswordToggle" class="password-toggle"><i class="fa fa-eye"></i></span>
                            </div>
                            <div class="invalid-feedback" id="pass-invalid">
                                Please enter a valid password!
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
                            <div class="invalid-feedback" id="confirm-invalid">
                                Passwords do not match!
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
    $('.password-toggle').on('click', function(e) {
        e.preventDefault();
        var $pwd = $(this).siblings('.m-input');
        $pwd.attr('type', $pwd.attr('type') === 'password' ? 'text' : 'password');
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

        if (session.next_update == null || session.next_update == "0000-00-00 00:00:00") {
            $(".password-change-reminder").modal("show");
        }
        else {
            const next_update = new Date(session.next_update);
            const currentDate = new Date();
            if (next_update <= currentDate) {
                $(".password-change-reminder").modal("show");
                
                if (session.waive_count >= 4) {
                    $("#changePasswordLater").hide();
                    $("#waiveNull").show();
                }
                <?php
                    $included = array("id", "name");
                    $privCheckAction = $this->acl_model->getAccessControlMenu($included, 1);
                ?>
                const privCheckAction = <?php echo json_encode($privCheckAction); ?>;
                const hasPayroll = privCheckAction.some(item =>
                    item.name && item.name.toLowerCase().includes("payroll")
                );
                if (hasPayroll) {
                    $(document).ready(function () {
                        $("#changePasswordLater").remove();
                    });
                }
            }
        }

    // function checkPass(currr){
    //     if (session.next_update == null || session.next_update == "0000-00-00 00:00:00") {
    //         $(".password-change-reminder").modal("show");
    //     }
    //     else {
    //         const next_update = new Date(session.next_update);
    //         const currentDate = new Date(currr);
    //         console.log("Next: ",next_update, "Today: ",currentDate);
    //         if (next_update <= currentDate) {
    //             $(".password-change-reminder").modal("show");
                
    //             if (session.waive_count >= 4) {
    //                 $("#changePasswordLater").hide();
    //             }
    //         }
    //     }
    // } <-- This function is for development only


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
        $(".invalid-feedback").hide();
        const value = $(this).val();
        validationRules.forEach(rule => {
            conditions[rule.key] = rule.condition(value);
            rule.element.css('text-decoration', conditions[rule.key] ? 'line-through' : 'none');
        });
        $helpSection.toggle(!Object.values(conditions).every(Boolean));
    });

    $("#confirmPasswordInput").on('input', function() {
        $(".invalid-feedback").hide();
    })

	$('#changepasswordform').on('submit', function(e) {
        e.preventDefault();
        const allConditionsMet = Object.values(conditions).every(Boolean);
        if (!allConditionsMet) {
            $("#pass-invalid").show();
            return;
        }
        if ($passwordInput.val() !== $('#confirmPasswordInput').val()) {
            $("#confirm-invalid").show();
            return;
        }
		var formData = $(this).serialize();
		$.ajax({
            url: '<?php echo base_url('login/update_password'); ?>',
            type: 'POST',
            data: formData,
			dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#force_change_modal').modal('hide');
                    toastr.success("",response.message, 20000);
                } else {
                    toastr.error("",response.message, 20000);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Password Update Failed.", 20000);
            }
        });
	})

</script>