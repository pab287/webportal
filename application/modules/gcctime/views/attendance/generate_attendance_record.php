<div class="m-content">
	<!--Begin::Main Portlet-->
	<div class="row">
		<div class="col-md-12 col-lg-12 col-xl-12">
			<div class="m-portlet">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">Attendance Record</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item">
                                <a href="javascript:void(0);" class="m-portlet__nav-link m-portlet__nav-link--icon" data-toggle="modal" data-target="#m_generate_attendance_modal">
                                    <i class="fa fa-upload"></i>
                                </a>
                            </li>
                        </ul>
					</div>
				</div>
				<div class="m-portlet__body">
                    test
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="m_generate_attendance_modal" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Generate Attendance Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="frmGenerateAttendance" action="<?php echo site_url("gcctime/attendance/generate_attendance_logs"); ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Select Date Range</label>
                                <div class="input-group" id="date-picker">
                                    <input type="text" class="form-control m-input" readonly="" placeholder=""
                                            name="date-range" id="date-range" data-validation="required">
                                    <span class="input-group-addon">
                                        <i class="la la-calendar-check-o"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group m-form__group">
                                <label for="">File to Upload</label>
                                <div>
                                    <input type="file" name="files" data-validation="required" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btnAdvance_search"><i class="m-nav__link-icon fa fa-gears mr-2"></i> Generate</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const defaultDate = moment();
    const datePicker = $('#date-picker');
    datePicker.daterangepicker({
        buttonClasses: 'm-btn btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',
        endDate: defaultDate,
        maxDate: defaultDate,
        /*startDate: defaultDate,
        endDate: defaultDate*/
    }, function (start, end, label) {
        $('#cut-offs').val(null).trigger('change');
        $('.form-control', datePicker).val(start.format('MMM DD, YYYY') + ' / ' + end.format('MMM DD, YYYY'));
    });

    $.validate({
        form: '#frmGenerateAttendance',
        lang: 'en',
        onSuccess: function (form) {
            const formData = new FormData(form[0]);
            console.log(formData);

            $.ajax({
                url: form[0].action,
                type: "POST",
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $(".btnAdvance_search").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    $(".btnAdvance_search").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    var result = $.parseJSON(data);
                    if (result.status) {
                        toastr.success(result.message);
                    } else {
                        toastr.error(result.message);
                    }
                }
            })
            return false;
        }
    });
</script>