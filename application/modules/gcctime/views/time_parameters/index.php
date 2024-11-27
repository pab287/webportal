<div class="m-content m--full-height">
    <div class="row m--full-height" style="margin-top: 48px;">
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12
                    offset-xl-2 offset-lg-2 offset-md-2 offset-sm-0">
            <form class="m-form" id="frm-night-diff-config">
                <div class="m-portlet">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <span class="m-portlet__head-icon">
                                    <i class="fa fa-cogs"></i>
                                </span>
                                <h3 class="m-portlet__head-text">
                                    NIGHT DIFFERENTIAL HOURS
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                                <label class="m--font-bold" for="">Start Time</label>
                                <input type="time" class="form-control"
                                       value="<?= isset($night_diff->start_time) ? $night_diff->start_time : null ?>"
                                       name="start_time">
                                <span class="m-form__help pt-1">for current day</span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                                <label class="m--font-bold" for="">End Time</label>
                                <input type="time" class="form-control"
                                       value="<?= isset($night_diff->end_time) ? $night_diff->end_time : null ?>"
                                       name="end_time">
                                <span class="m-form__help pt-1">for next day</span>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot m--align-right">
                        <button type="submit" class="btn btn-brand m-btn m-btn--pill btnUpdate">
                            Apply Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
            <form class="m-form" id="frm-ts-ot-config">
                <div class="m-portlet">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <span class="m-portlet__head-icon">
                                    <i class="fa fa-cogs"></i>
                                </span>
                                <h3 class="m-portlet__head-text">
                                    OVERTIME & ATTENDANCE TIME PARAMETERS
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                                <label class="m--font-bold" for="">Start Time</label>
                                <input type="time" class="form-control"
                                       value="<?= isset($ts_ot->start_time) ? $ts_ot->start_time : null ?>"
                                       name="start_time">
                                <span class="m-form__help pt-1">for current day</span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                                <label class="m--font-bold" for="">End Time</label>
                                <input type="time" class="form-control"
                                       value="<?= isset($ts_ot->end_time) ? $ts_ot->end_time : null ?>"
                                       name="end_time">
                                <span class="m-form__help pt-1">for next day</span>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot m--align-right">
                        <button type="submit" class="btn btn-brand m-btn m-btn--pill btnUpdate">
                            Apply Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    $.validate({
        form: $('#frm-night-diff-config'),
        lang: 'en',
        scrollToTopOnError: false,
        onSuccess: function (form) {
            const formData = new FormData($(form)[0]);
            formData.append('csrf_token', _csrf_hash);

            $.ajax({
                url: baseUrl(`gcctime/time_parameters/update_night_diff_config`),
                type: 'POST',
                dataType: 'JSON',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    if (response) {
                        const toast = response.success ? 'success' : 'error';
                        toastr[toast](response.message, response.title, {timeOut: 10000});
                    }
                }
            });
            return false;
        }
    });

    $.validate({
        form: $('#frm-ts-ot-config'),
        lang: 'en',
        scrollToTopOnError: false,
        onSuccess: function (form) {
            const formData = new FormData($(form)[0]);
            formData.append('csrf_token', _csrf_hash);

            $.ajax({
                url: baseUrl(`gcctime/time_parameters/update_ts_ot_config`),
                type: 'POST',
                dataType: 'JSON',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    if (response) {
                        const toast = response.success ? 'success' : 'error';
                        toastr[toast](response.message, response.title, {timeOut: 10000});
                    }
                }
            });
            return false;
        }
    });
</script>