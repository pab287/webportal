<style>
tr.bg-danger.text-white td { font-weight: 500; }
</style>
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
                                <a href="javascript:void(0);" class="m-portlet__nav-link m-portlet__nav-link--icon" data-toggle="modal" data-target="#m_generate_app_attendance_modal">
                                    <i class="fa fa-mobile mr-2"></i> App Attendance
                                </a>
                            </li>
                            <li class="m-portlet__nav-item">
                                <a href="javascript:void(0);" class="m-portlet__nav-link m-portlet__nav-link--icon" data-toggle="modal" data-target="#m_generate_attendance_modal">
                                    <i class="fa fa-upload mr-2"></i> Upload
                                </a>
                            </li>
                        </ul>
					</div>
				</div>
				<div class="m-portlet__body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="statusFilter" class="mb-2 m-animate-fade-in" v-if="count > 0">
                                <div class="m-form__group form-group row">
                                    <label for="" class="col-2 col-form-label">
                                        Attendance Status
                                    </label>
                                    <div class="col-10">
                                        <div class="m-radio-inline">
                                            <label class="m-radio">
                                                <input type="radio" value="all" v-model="status">
                                                All
                                                <span></span>
                                            </label>
                                            <label class="m-radio">
                                                <input type="radio" value="true" v-model="status">
                                                <label for="" class="m--font-success">LATE</label>
                                                <span></span>
                                            </label>
                                            <label class="m-radio">
                                                <input type="radio" value="false" v-model="status">
                                                <label for="" class="m--font-danger">ON TIME</label>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="tbl_attendance_record">
                                    <colgroup>
                                        <col style="width: 8%"></col>
                                        <col></col>
                                        <col style="width: 15%"></col>
                                        <col style="width: 25%"></col>
                                        <col style="width: 12%"></col>
                                        <col style="width: 12%"></col>
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>Biometric #</th>
                                            <th>Name</th>
                                            <th>Company</th>
                                            <th>Department</th>
                                            <th>Shift Schedule</th>
                                            <th>Logged Time</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="m_generate_app_attendance_modal" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate App Attendance Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="frmGenerateAppAttendance" action="<?php echo site_url("gcctime/attendance/generate_app_attendance_logs"); ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group m-form__group">
                                <label for="">Filter by Date</label>
                                <div class="input-group date" id="datepicker_container">
                                    <input id="datepicker" type="text" name="search_date" class="form-control" required autocomplete="off" />
                                    <span class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <span class="m--font-danger"><span class="fa fa-exclamation-triangle"></span> <span class="m--font-bolder">Caution:</span> This will generate logs for all employees that are using GCCTIME Mobile App.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btnGenerateAppAttendance btnAdvance_search"><i class="m-nav__link-icon fa fa-gears mr-2"></i> Generate</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="m_generate_attendance_modal" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Attendance Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="frmGenerateAttendance" action="<?php echo site_url("gcctime/attendance/generate_attendance_logs"); ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group m-form__group">
                                <label for="">File to Upload</label>
                                <div>
                                    <input type="file" name="files[]" multiple data-validation="required" />
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
    const dpicker = $("#datepicker").datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        startDate: "-1d",
        endDate: new Date(),
        todayHighlight: true
    });

    const vmFilter = new Vue({
        el: "#statusFilter",
        data: { status: "all", count: 0 },
        watch: {
            status() {
                mapBlockUI();
                setTimeout(()=>{ 
                    dtTable.draw();
                    mapUnblockUI();
                }, 100);
            }
        }
    });

    const dtTable = $('#tbl_attendance_record').DataTable({
        dom: '<"row"<"lateNotification col-sm-12 col-md-8 col-lg-8"><"col-sm-12 col-md-4 col-lg-4"<"dt-buttons--custom btn-group float-right ml-2 m--hide"B>f>>rtlip',
        destroy: true,
        serverSide: false,
        processing: false,
        autoWidth: false,
        ordering: false,
        buttons: [{ extend: 'excelHtml5',
            text: 'Export Excel',
            title: 'Generated Attendance Log Record',
            exportOptions: { columns: [0,1,2,3,4,5] },
            action: function (e, dt, button, config) {
                // perform the default Excel export
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                Swal.fire({
                    icon: 'success',
                    type: 'success',
                    title: 'Excel file has been exported successfully.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        }],
        columns: [
            { data: "biometricno" },
            { data: "employee_name" },
            { data: "company" },
            { data: "department" },
            { data: "shift_start" },
            { data: "log_time" },
            { data: "is_late", visible: false }
        ], createdRow: function (row, data) {
            const { is_late } = data;
            if(is_late){ $(row).addClass('bg-danger text-white'); }
        }, initComplete: function () {
            $("#tbl_attendance_record_filter input[type='search']").removeClass("form-control-sm");
        }
    });

    $.fn.dataTable.ext.search.push(function(_settings, data, _dataIndex) {
        const filter = vmFilter.status;
        const isLate = data[6];
        if (filter === "all") return true;
        return isLate === filter;
    });

    $.validate({
        form: "#frmGenerateAppAttendance",
        lang: "en",
        onSuccess: function (form) {
            const currentForm = $(form[0]);
            $.ajax({
                url: form[0].action,
                type: "POST",
                data: currentForm.serialize(),
                dataType: "json",
                beforeSend: function () {
                    $(".btnGenerateAppAttendance").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    vmFilter.count = 0;
                },
                success: function (json) {
                    $(".btnGenerateAppAttendance").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    vmFilter.count = json.late_ctr;
                    if (json.response) {
                        $("#m_generate_app_attendance_modal").modal("hide");

                        $(".dt-buttons--custom").hasClass("m--hide") ? $(".dt-buttons--custom").removeClass("m--hide") : "";
                        vmFilter.status = "all";
                        toastr.success(json.message);
                        if(json.logs.length > 0){
                            const tempHtml = json.late_ctr > 0 ? `A total of <b>${json.late_ctr}</b> late attendance record(s) detected.`
                                : `A total of <b>${json.logs.length}</b> attendance record(s) found.`;
                            $(".lateNotification").html(tempHtml).addClass("text-uppercase m-animate-fade-in");
                        }
                        dtTable.clear();
                        dtTable.rows.add(json.logs).draw(false);
                    }else{
                        dtTable.clear();
                        dtTable.rows.add([]).draw(false);
                        $(".lateNotification").html("").removeClass("text-uppercase m-animate-fade-in");
                        $(".dt-buttons--custom").hasClass("m--hide") ? "" : $(".dt-buttons--custom").addClass("m--hide");
                        toastr.error(json.message);
                    }
                },
            });
            return false;
        }
    });

    $.validate({
        form: '#frmGenerateAttendance',
        lang: 'en',
        onSuccess: function (form) {
            const formData = new FormData(form[0]);
            $.ajax({
                url: form[0].action,
                type: "POST",
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $(".btnAdvance_search").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    vmFilter.count = 0;
                },
                success: function (json) {
                    $(".btnAdvance_search").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    vmFilter.count = json.late_ctr;
                    if (json.response) {
                        $("#m_generate_attendance_modal").modal("hide");
                        $(".dt-buttons--custom").hasClass("m--hide") ? $(".dt-buttons--custom").removeClass("m--hide") : "";
                        vmFilter.status = "all";
                        toastr.success(json.message);
                        if(json.logs.length > 0){
                            const tempHtml = json.late_ctr > 0 ? `A total of <b>${json.late_ctr}</b> late attendance record(s) detected.`
                                : `A total of <b>${json.logs.length}</b> attendance record(s) found.`;
                            $(".lateNotification").html(tempHtml).addClass("text-uppercase m-animate-fade-in");
                        }
                        dtTable.clear();
                        dtTable.rows.add(json.logs).draw(false);
                    } else {
                        dtTable.clear();
                        dtTable.rows.add([]).draw(false);
                        $(".lateNotification").html("").removeClass("text-uppercase m-animate-fade-in");
                        $(".dt-buttons--custom").hasClass("m--hide") ? "" : $(".dt-buttons--custom").addClass("m--hide");
                        toastr.error(json.message);
                    }
                }
            })
            return false;
        }
    });
</script>