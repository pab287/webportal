<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Manage Shift
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <!--begin::Section-->
                    <div class="m-section">
                        <div class="m-section__content">
                            <!--begin: Datatable -->
                            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                                <table class="table table-striped table-bordered" id="table_shift">
                                    <col width="45%">
                                    <col width="10%">
                                    <col width="10%">
                                    <col width="10%">
                                    <col width="10%">
                                    <col width="5%">
                                    <col width="10%">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>AM Start</th>
                                        <th>AM End</th>
                                        <th>PM Start</th>
                                        <th>PM End</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <!--end: Datatable -->
                        </div>
                    </div>
                    <!--end::Section-->
                </div>
                <!--end::Form-->
            </div>
        </div>
    </div>
</div>

<!-- New shift begin::Modal-->
<div class="modal fade" id="modal-new" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    New
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_shift" action="<?php echo site_url('Shift/newshift'); ?>" method="POST">
                    <div class="form-group">
                        <label class="form-control-label">
                            Name:
                        </label>
                        <input type="text" name="name" class="form-control" data-validation="required">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            AM Start:
                        </label>
                        <input type="text" name="am_start" class="form-control" id="am_start"
                               data-validation="required">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            AM End:
                        </label>
                        <input type="text" name="am_end" class="form-control" id="am_end" data-validation="required">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            PM Start:
                        </label>
                        <input type="text" name="pm_start" class="form-control" id="pm_start"
                               data-validation="required">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            PM End:
                        </label>
                        <input type="text" name="pm_end" class="form-control" id="pm_end" data-validation="required">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary m-btn" data-dismiss="modal">
                    Close
                </button>
                <button type="submit" class="btn btn-brand m-btn btn-submit-shift btnSave">
                    Submit
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- New Shift end::Modal-->


<!-- Edit Shift begin::Modal-->
<div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Edit Department
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_edit_shift" action="<?php echo site_url("Shift/updateshift"); ?>" method="POST">
                    <input type="hidden" name="id" class="form-control" data-validation="required">
                    <div class="form-group">
                        <label class="form-control-label">
                            Name:
                        </label>
                        <input type="text" name="name" class="form-control" data-validation="required">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            AM Start:
                        </label>
                        <input type="text" name="am_start" class="form-control" id="am_start"
                               data-validation="required">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            AM End:
                        </label>
                        <input type="text" name="am_end" class="form-control" id="am_end" data-validation="required">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            PM Start:
                        </label>
                        <input type="text" name="pm_start" class="form-control" id="pm_start"
                               data-validation="required">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            PM End:
                        </label>
                        <input type="text" name="pm_end" class="form-control" id="pm_end" data-validation="required">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cancel
                </button>
                <button type="submit" class="btn btn-success btm-submit-edit btnUpdate">
                    Submit
                </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit Shift end::Modal-->

<!-- Delete begin::Modal-->
<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Delete Shift
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
                </button>
            </div>
            <div class="modal-body">
                <i class="la la-warning"></i> Are you sure you want to delete this Shift?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cancel
                </button>
                <button type="submit" class="btn btn-danger btm-submit-delete btnDelete" data-id="">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
<!--Delete end::Modal-->

<!-- Department List begin::Modal-->
<div class="modal fade" id="modal-select-dept" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Select Department
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="selected-dept-id" id="selected-dept-id">
                <table id="table_dept_select" class="table table-striped table-bordered" style="width:100%">
                    <col width="2%">
                    <col width="93%">
                    <col width="5%">
                    <thead>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>Name</th>
                    <th>Action</th>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cancel
                </button>
                <!--button type="button" class="btn btn-info btn-submit-multi-dept" >
                    Submit
                </button-->
                <div class="m-dropdown m-dropdown--inline  m-dropdown--arrow" data-dropdown-toggle="click">
                    <a href="#" class="m-dropdown__toggle btn btn-success dropdown-toggle btnMass_action">
                        Action
                    </a>
                    <div class="m-dropdown__wrapper">
                        <span class="m-dropdown__arrow m-dropdown__arrow--left"></span>
                        <div class="m-dropdown__inner">
                            <div class="m-dropdown__body">
                                <div class="m-dropdown__content">
                                    <ul class="m-nav">
                                        <li class="m-nav__item">
                                            <a href="javascript:void(0)" class="m-nav__link btn-submit-multi-dept">
												<span class="m-nav__link-text">
													Select
												</span>
                                            </a>
                                        </li>
                                        <li class="m-nav__item">
                                            <a href="javascript:void(0)"
                                               class="m-nav__link btn-submit-desmulti-dept btnDeselect">
												<span class="m-nav__link-text">
													Deselect
												</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Department end::Modal-->

<script type="text/javascript">
    var table = $("#table_shift").DataTable({
        "dom": '<"toolbar">frtlip',
        "ajax": "<?php echo base_url('gcctime/Shift/getCollection');?>",
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "serverSide": false,
        "processing": false,
    });

    var table_select = $("#table_dept_select").DataTable({
        ajax: {
            url: "<?php echo base_url('gcctime/Department/getDepartmentMin'); ?>",
            type: "post",
        },
        lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
        serverSide: true,
        processing: true,
        columns: [{
            data: "chkbox",
            width: "2%",
        }, {
            data: "department_name",
            width: "93%",
        }, {
            data: "action",
            width: "5%",
        }],
        columnDefs: [{
            targets: 0,
            orderable: false,
        }, {
            targets: 1,
            className: 'text-center',

        }, {
            targets: -1,
            orderable: false,
        }],
        paging: false,
        info: false,
        order: [[1, "asc"]],
        scrollY: '50vh',
        scrollCollapse: true,
    });

    $("div.toolbar").html('<button type="button" class="m-portlet__nav-link btn m-btn--square btn-success btnNew"  data-toggle="modal" data-target="#modal-new"><i class="fa fa-plus"></i> New </button>');

    // Add new department Validation
    $.validate({
        form: '#form_shift',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: form[0].action,
                type: "POST",
                data: $("#form_shift").serialize(),
                beforeSend: function () {
                    $(".btn-submit-shift").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    $(".btn-submit-shift").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    var result = $.parseJSON(data);
                    $("#modal-new").modal('hide');

                    if (result.status) {
                        toastr.success(result.message);
                        table.ajax.reload();
                        $('#form_shift')[0].reset();

                        /*** location.reload(); ***/
                    } else {
                        toastr.error(result.message);
                    }
                }
            });
            return false;
        },
    });

    var edit_shift = function (id) {
        $("#modal-edit").modal("show");

        $.ajax({
            url: "<?php echo site_url('gcctime/Shift/getshift')?>",
            type: "POST",
            data: {id: id},
            beforeSend: function () {
                mApp.blockPage({
                    overlayColor: '#000000',
                    type: 'loader',
                    state: 'primary',
                    message: 'Please wait...',
                });

                $(".blockUI.blockMsg .m-blockui").removeAttr("style");
            },
            success: function (data) {
                var result = $.parseJSON(data);
                mApp.unblockPage();
                $("#form_edit_shift input[name=id]").val(result.id);
                $("#form_edit_shift input[name=name]").val(result.name);
                $("#form_edit_shift input[name=am_start]").val(result.am_start);
                $("#form_edit_shift input[name=am_end]").val(result.am_end);
                $("#form_edit_shift input[name=pm_start]").val(result.pm_start);
                $("#form_edit_shift input[name=pm_end]").val(result.pm_end);

            }
        });
        return false;
    }

    // Edit shift Validation
    $.validate({
        form: '#form_edit_shift',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: form[0].action,
                type: "POST",
                data: $("#form_edit_shift").serialize(),
                beforeSend: function () {
                    $(".btn-submit-shift").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    $(".btn-submit-shift").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    var result = $.parseJSON(data);
                    $("#modal-edit").modal('hide');

                    if (result.status) {
                        toastr.success(result.message);
                        table.ajax.reload();
                        $('#form_edit_shift')[0].reset();

                        /*** location.reload(); ***/
                    } else {
                        toastr.error(result.message);
                    }
                }
            });
            return false;
        },
    });

    $("#table_shift").on("click", ".btn-delete", function () {
        var id = $(this).attr("data-id");

        $("#modal-delete").modal("show");

        $("#modal-delete .btm-submit-delete").attr("data-id", id);

    });

    $(".btm-submit-delete").on("click", function () {
        var id = $(this).attr("data-id");
        $.ajax({
            url: "<?php echo site_url("gcctime/Shift/deleteshift");?>",
            type: "POST",
            data: {id: id},
            beforeSend: function () {
                mApp.blockPage({
                    overlayColor: '#000000',
                    type: 'loader',
                    state: 'primary',
                    message: 'Please wait...',
                });

                $(".blockUI.blockMsg .m-blockui").removeAttr("style");
            },
            success: function (data) {
                var result = $.parseJSON(data);
                mApp.unblockPage();
                $("#modal-delete").modal("hide");

                if (result.status) {
                    toastr.success(result.message);
                } else {
                    toastr.error(result.message);
                }

                table.ajax.reload();
            }
        });
    });

    $("#table_shift").on("click", ".btn-dept", function () {
        var Shift_id = $(this).attr("data-id");
        $("#modal-select-dept #selected-dept-id").attr("data-id", Shift_id);
        $("#modal-select-dept").modal("show");
        setTimeout(function () {
            table_select.draw();
        }, 1000);

    });

    //check box start

    $("#checkAll").change(function () {
        if (this.checked) {
            $(".checkSingle").each(function () {
                this.checked = true;
            });
        } else {
            $(".checkSingle").each(function () {
                this.checked = false;
            });
        }
    });

    $(".checkSingle").click(function () {
        if ($(this).is(":checked")) {
            var isAllChecked = 0;

            $(".checkSingle").each(function () {
                if (!this.checked)
                    isAllChecked = 1;
            });

            if (isAllChecked == 0) {
                $("#checkedAll").prop("checked", true);
            }
        } else {
            $("#checkedAll").prop("checked", false);
        }
    });

    //check box end

    //submit multiple select
    $(".btn-submit-multi-dept").on("click", function () {
        var check_count = $('.checkSingle:checkbox:checked').length;
        var shift_id = $("#modal-select-dept #selected-dept-id").attr("data-id");
        if (check_count == 0) {
            toastr.error("No Data Selected!");
        } else {
            $('.checkSingle:checkbox:checked').each(function () {
                var dept_id = $(this).attr("data-id");

                $.ajax({
                    url: "<?php echo site_url('gcctime/Shift/selectDeptforshift');?>",
                    type: "POST",
                    data: {shift_id: shift_id, dept_id: dept_id},
                    beforeSend: function () {
                        mApp.block('#modal-select-dept .modal-content', {
                            overlayColor: '#000000',
                            state: 'primary'
                        });
                    },
                    success: function () {
                        mApp.unblock('#modal-select-dept .modal-content');
                        table_select.draw();
                    }
                })
            });
        }
    });

    //submit multiple deselect
    $(".btn-submit-desmulti-dept").on("click", function () {
        var check_count = $('.checkSingle:checkbox:checked').length;
        var shift_id = $("#modal-select-dept #selected-dept-id").attr("data-id");
        if (check_count == 0) {
            toastr.error("No Data Selected!");
        } else {
            $('.checkSingle:checkbox:checked').each(function () {
                var dept_id = $(this).attr("data-id");

                $.ajax({
                    url: "<?php echo site_url('gcctime/Shift/deselectDeptforshift');?>",
                    type: "POST",
                    data: {shift_id: shift_id, dept_id: dept_id},
                    beforeSend: function () {
                        mApp.block('#modal-select-dept .modal-content', {
                            overlayColor: '#000000',
                            state: 'primary'
                        });
                    },
                    success: function () {
                        mApp.unblock('#modal-select-dept .modal-content');
                        table_select.draw();
                    }
                })
            });
        }
    });

    //selet single dept
    $("#table_dept_select").on("click", ".btn-select-shift-trigg", function () {
        var dept_id = $(this).attr("data-id");
        var shift_id = $("#selected-dept-id").attr("data-id");

        $.ajax({
            url: "<?php echo site_url("gcctime/Shift/selectDeptforshift");?>",
            type: "POST",
            data: {shift_id: shift_id, dept_id: dept_id},
            beforeSend: function () {
                mApp.block('#modal-select-dept .modal-content', {
                    overlayColor: '#000000',
                    state: 'primary'
                });
            },
            success: function () {
                mApp.unblock('#modal-select-dept .modal-content');
                table_select.draw();
            }
        });

    });

    //deselet single dept
    $("#table_dept_select").on("click", ".btn-deselect-shift-trigg", function () {
        var dept_id = $(this).attr("data-id");
        var shift_id = $("#selected-dept-id").attr("data-id");

        $.ajax({
            url: "<?php echo site_url("gcctime/Shift/DEselectDeptforshift");?>",
            type: "POST",
            data: {shift_id: shift_id, dept_id: dept_id},
            beforeSend: function () {
                mApp.block('#modal-select-dept .modal-content', {
                    overlayColor: '#000000',
                    state: 'primary'
                });
            },
            success: function () {
                mApp.unblock('#modal-select-dept .modal-content');
                table_select.draw();
            }
        });

    });

    $(document).ready(function () {

        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        //time picker init start
        $('#am_start, #am_end, #pm_start, #pm_end').timepicker({
            minuteStep: 1,
            showMeridian: false,
            use24hours: false,
            defaultTime: null,
            onSelect: function (dateText, inst) {
                $('#' + inst.id).attr('value', dateText);
            }

        });
        //time picker init end
    });

</script>
