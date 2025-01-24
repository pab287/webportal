<div class="m-content">
    <div class="row">
        <div class="col-md-12">
            <div class="m-portlet m-portlet--mobile  m-portlet--tabs">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left m-tabs-line--primary"
                            role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link <?=$isNull ? 'active show' : ($type == 'absent' ? 'active show' : '') ?>" data-toggle="tab"
                                    href="#m_user_profile_tab_1" role="tab" aria-selected="false">Absent Report</a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link <?=!$isNull ? ($type == 'late' ? 'active show' : '') : '' ?>" data-toggle="tab"
                                   href="#late_report_tab" role="tab" aria-selected="false">Late Report</a>
                            </li>
                        </ul>
                        <ul class="m-portlet__nav" id="absentee-report-options">
                            <li class="m-portlet__nav-item">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                    <button type="button" class="m-btn btn btn-secondary btnSync"
                                            onclick="syncdata_on_devices()"><em
                                                class="m-nav__link-icon flaticon-share"></em> Sync Data
                                    </button>
                                    <button type="button" class="m-btn btn btn-secondary btnResend" data-toggle="modal"
                                            data-target="#modalResend"><em
                                                class="m-nav__link-icon flaticon-paper-plane"></em> Re-send Email
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane <?=$isNull ? 'active show' : ($type == 'absent' ? 'active show' : '') ?>" id="m_user_profile_tab_1">
                        <?php $this->load->view("reports/content/tabs/absentee_report"); ?>
                    </div>
                    <div class="tab-pane <?=!$isNull ? ($type == 'late' ? 'active show' : '') : '' ?>" id="late_report_tab">
                        <?php $this->load->view("reports/content/tabs/late_report"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalResend" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sending Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formResend">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="m-form__group form-group row">
                            <div class="col-md-2">
							<span class="m-switch m-switch--sm">
								<label><input type="checkbox" name="late_report"><span></span></label>
							</span>
                            </div>
                            <label class="col-md-10 col-form-label">Re-send email notification for late
                                employee(s)</label>
                        </div>
                        <div class="m-form__group form-group row">
                            <div class="col-md-2">
							<span class="m-switch m-switch--sm">
								<label><input type="checkbox" name="absentee_report"><span></span></label>
							</span>
                            </div>
                            <label class="col-md-10 col-form-label">Re-send email notification for absent
                                employee(s)</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info btnSend">Send</button>
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var syncdata_on_devices = function () {
            $.ajax({
                url: "<?php echo site_url("gcctime/curl_request/syncdata_device/true"); ?>",
                dataType: "json",
                beforeSend: function () {
                    mApp.blockPage({
                        overlayColor: '#000000',
                        type: 'loader',
                        state: 'primary',
                        message: 'Please wait...',
                    });

                    $(".blockUI.blockMsg .m-blockui").removeAttr("style");
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success("Sync data successful", "Done Sync");
                    } else {
                        toastr.error("Sync data failed", "Failed Sync");
                    }
                    mApp.unblockPage();
                }
            });
        }

        $(document).on("click", ".btnSend", function () {
            var form = $("#formResend").serialize();

            $.ajax({
                url: "<?php echo site_url("gcctime/curl_request/resend_email"); ?>",
                data: form,
                dataType: "json",
                type: "post",
                beforeSend: function () {
                    $("#modalResend").modal("hide");
                    mApp.blockPage({
                        overlayColor: '#000000',
                        type: 'loader',
                        state: 'primary',
                        message: 'Please wait...',
                    });

                    $(".blockUI.blockMsg .m-blockui").removeAttr("style");
                },
                success: function (json) {
                    if (json.response) {
                        if (typeof json.late_email !== "undefined") {
                            if (json.late_email) {
                                toastr.success("Late report, email sending successful.", "Reports");
                            } else {
                                toastr.error("Late report, email sending failed!", "Reports");
                            }
                        }
                        if (typeof json.absentee_email !== "undefined") {
                            if (json.absentee_email) {
                                toastr.success("Absentee report, email sending successful.", "Reports");
                            } else {
                                toastr.error("Absentee report, email sending failed!", "Reports");
                            }
                        }
                    }

                    mApp.unblockPage();
                }
            });
        });
    </script>
</div>