<div class="modal fade" tabindex="-1" role="dialog"
     id="confirm-regenerate-timesheet-modal">
    <div class="modal-dialog" role="document">
        <form class="modal-content" id="frm-confirm-regenerate-timesheet">
            <div class="modal-header">
                <h5 class="modal-title m--font-boldest">Confirm Timesheet Regeneration</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success m-alert">
                    <p class="m--regular-font-size-lg2 m--font-boldest m--margin-bottom-0">
                        Shift schedule was successfully updated!
                    </p>
                </div>
                <div class="m--margin-top-30">
                    <span class="m--font-danger m--font-boldest">IMPORTANT NOTE</span>
                    <p class="m--regular-font-size-lg1 m--font-bolder">
                        Timesheets/Time records affected with the changes of the schedule won't be affected unless you
                        click
                        <span class="m--font-boldest">"Yes, Generate"</span>.
                    </p>

                    <p class="m--regular-font-size-lg1 m--font-boldest">
                        Do you want to regenerate Timesheet/Time record to apply shift schedule changes in timesheet
                        calculation?
                    </p>

                    <div class="form-group m--margin-top-20">
                        <label for="" class="required m--font-bolder">IF YES, PLEASE SPECIFY INCLUSIVE DATES.</label>
                        <div class="input-group" id="date-range-picker">
                            <input type="text" class="form-control m-input" readonly=""
                                   autocomplete="off" data-validation="required" id="dates">
                            <span class="input-group-addon">
                                <i class="la la-calendar-check-o"></i>
                            </span>
                        </div>
                    </div>

                    <input type="hidden" id="employees" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnUpdate">Yes, Regenerate.</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">No, leave it as is.</button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $('#date-range-picker')
        .daterangepicker({
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            autoClose: true,
        }, function (start, end, label) {
            $('#date-range-picker .form-control')
                .val(start.format('MMM. DD, YYYY') + ' / ' + end.format('MMM. DD, YYYY'));
        });

    $.validate({
        form: $("#frm-confirm-regenerate-timesheet"),
        lang: "en",
        scrollToTopOnError: false,
        onSuccess: function (form) {
            const employees = JSON.parse($("#employees", form).val());
            const dates = $("#dates", form).val().split("/");
            const dateStart = moment(dates[0]).format("YYYY-MM-DD");
            const dateEnd = moment(dates[1]).format("YYYY-MM-DD");
            const remarks = "Regenerate after update of shift schedule";
            $(`:input`, form).prop("disabled", true);
            const btnSubmit = $('button[type="submit"]', form);

            $.ajax({
                url: baseUrl(`gcctime/timesheet_cron/create_multiple/0/1`),
                type: "POST",
                dataType: "JSON",
                data: {
                    employees,
                    dateStart,
                    dateEnd,
                    remarks,
                    csrf_token: _csrf_hash
                },
                beforeSend: function () {
                    btnSubmit.addClass('m-btn--custom m-loader m-loader--light m-loader--left');
                },
                success: function (response) {
                    if (!response.length) {
                        btnSubmit.removeClass('m-btn--custom m-loader m-loader--light m-loader--left');
                        $(`:input`, form).prop("disabled", false);
                        $(form).resetForm();
                        $('#date-range-picker').data('daterangepicker').setStartDate(moment());
                        $('#date-range-picker').data('daterangepicker').setEndDate(moment());
                        toastr.success("Shift schedule was updated & timesheets was successfully regenerated.", "Successfully Updated & Regenerated.", {timeOut: 10000});
                        confirmRegenerateTimesheetModal.modal("hide");
                        setTimeout(() => {
                            $("#modal-shift_schedule-assign").modal("hide");
                        }, 300);
                    }
                }
            });
            return false;
        }
    });
</script>