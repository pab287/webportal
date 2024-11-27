<?php $actions = $this->core_layout->getCurrentActions(); ?>
<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Manage Shift Schedule
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
                                <table class="table table-striped table-bordered" id="table-shift_schedule">
                                    <col width="25%">
                                    <col width="55%">
                                    <col width="10%">
                                    <col width="10%">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
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


<?php
    $this->load->view("confirm_regenerate_timesheet");
?>

<div class="modal fade" id="modal-shift_schedule" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">
            <form action="<?php echo base_url('gcctime/shift/add_shift_schedule'); ?>" method="POST"
                  id="form-shift_schedule">
                <div class="modal-header">
                    <h5 class="modal-title">New Shift Schedule</h5>
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-control-label">Name *</label>
                        <input type="text" name="name" class="form-control inptName" autocomplete=off
                               data-validation="required"/>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Description *</label>
                        <input type="text" name="description" class="form-control inptDescription" autocomplete=off
                               data-validation="required"/>
                    </div>
                    <div class="form-group">
                        <label for="">Status</label>
                        <div class="m-radio-inline">
                            <label class="m-radio"><input id="status1" type="radio" name="is_active" value="1" checked>Active<span></span></label>
                            <label class="m-radio"><input id="status0" type="radio" name="is_active"
                                                          value="0">Inactive<span></span></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btnSave btn-submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-edit_shift_schedule" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">
            <form action="<?php echo base_url('gcctime/shift/edit_shift_schedule'); ?>" method="POST"
                  id="form-edit_shift_schedule">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="id" v-model="row.id"/>
                <input type="hidden" name="current_name" v-model="row.name"/>
                <div class="modal-header">
                    <h5 class="modal-title">Update Shift Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-control-label">Name *</label>
                        <input type="text" name="name" class="form-control inptName" autocomplete=off
                               data-validation="required" v-model="row.name"/>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Description *</label>
                        <input type="text" name="description" class="form-control inptDescription" autocomplete=off
                               data-validation="required" v-model="row.description"/>
                    </div>
                    <div class="form-group">
                        <label for="">Status</label>
                        <div class="m-radio-inline">
                            <label class="m-radio"><input id="status1" type="radio" name="is_active" value="1"
                                                          v-model="row.is_active">Active<span></span></label>
                            <label class="m-radio"><input id="status0" type="radio" name="is_active" value="0"
                                                          v-model="row.is_active">Inactive<span></span></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnSave btn-submit">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-shift_schedule-assign" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form-shift_schedule-assign">
                <input id="shift_id" type="hidden" name="id" v-model="items.id" value="0"/>
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Assign - Shift Schedule Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">Name</label>
                                <p class="form-control" v-bind:text-content.prop="items.name"></p>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Description</label>
                                <p class="form-control" v-bind:text-content.prop="items.description"></p>
                            </div>
                            <div class="form-group">
                                <label for="">Status</label>
                                <p class="form-control" v-bind:text-content.prop="items.is_active"></p>
                            </div>
                            <div class="form-group">
                                <label for="">Late Settings</label>
                                <select class="form-control custom-select2" id="lates" v-model="items.late_id">
                                    <option value="0" selected disabled>Choose an option</option>
                                    <?php if ($list_list): foreach ($list_list as $rs): ?>
                                        <option value="<?php echo $rs->id; ?>"><?php echo $rs->name; ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Undertime Settings</label>
                                <select class="form-control custom-select2" id="undertime" v-model="items.undertime_id">
                                    <option value="0" selected disabled>Choose an option</option>
                                    <?php if ($list_undertime): foreach ($list_undertime as $rs): ?>
                                        <option value="<?php echo $rs->id; ?>"><?php echo $rs->name; ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="tree_shift-monday" class="custom-tree"></div>
                            <div id="tree_shift-tuesday" class="custom-tree"></div>
                            <div id="tree_shift-wednesday" class="custom-tree"></div>
                            <div id="tree_shift-thursday" class="custom-tree"></div>
                            <div id="tree_shift-friday" class="custom-tree"></div>
                            <div id="tree_shift-saturday" class="custom-tree"></div>
                            <div id="tree_shift-sunday" class="custom-tree"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btnSave btn-submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-assigned" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form-assigned">
                <input id="shift_id" type="hidden" name="id" v-model="items.id" value="0"/>
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Assigned Employee List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <table id="table_assigned" class="table table-striped table-bordered"
                           style="width:100% !important;">
                        <thead>
                        <th>Name</th>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-select-personnel_schedule" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Select Personnel
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						&times;
					</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="shift_id" value="0">
                <table id="table_personnel_select" class="table table-striped table-bordered"
                       style="width:100% !important;">
                    <!-- <thead>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>Name</th>
                    <th>Action</th>
                    </thead> -->

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
                <div class="m-dropdown m-dropdown--inline  m-dropdown--arrow" data-dropdown-toggle="click">
                    <a href="javascript:void(0);"
                       class="m-dropdown__toggle btn btn-success dropdown-toggle btnMass_action">
                        Action
                    </a>
                    <div class="m-dropdown__wrapper">
                        <span class="m-dropdown__arrow m-dropdown__arrow--left"></span>
                        <div class="m-dropdown__inner">
                            <div class="m-dropdown__body">
                                <div class="m-dropdown__content">
                                    <ul class="m-nav">
                                        <li class="m-nav__item">
                                            <a href="javascript:void(0)"
                                               class="m-nav__link btn-submit-multi-personnel btnSelect">
												<span class="m-nav__link-text">
													Select
												</span>
                                            </a>
                                        </li>
                                        <li class="m-nav__item">
                                            <a href="javascript:void(0)"
                                               class="m-nav__link btn-submit-desmulti-personnel btnDeselect">
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
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    const confirmRegenerateTimesheetModal = $("#confirm-regenerate-timesheet-modal");

    confirmRegenerateTimesheetModal.on('hidden.bs.modal', function () {
        $("#modal-shift_schedule-assign").modal("hide");
    });

    (function ($, undefined) {
        "use strict";
        $.jstree.plugins.noclose = function () {
            this.close_node = $.noop;
        };
    })(jQuery);
    var tempShiftId = 0;

    jQuery(function ($) {
        $(".custom-select2").select2({
            width: "100%",
            dropdownParent: $("#modal-shift_schedule-assign"),
        });
    });
    var _dtPersonnelSchedule;
    var _currentActions = <?php echo json_encode($actions); ?>;

    var _modalPersonnelSchedule = $("#modal-select-personnel_schedule");
    var _modalEditShiftSchedule = $("#modal-edit_shift_schedule");

    var _dtShiftSchedule = $("#table-shift_schedule").DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: "<?php echo base_url("gcctime/shift/get_shift_schedule_list"); ?>",
            type: "post",
            dataType: "json",
            data: {csrf_token: _csrf_hash}
        }, columns: [
            {data: "name", width: "25%"},
            {data: "description", width: "55%"},
            {data: "is_active", width: "10%"},
            {data: null, width: "10%"},
        ], columnDefs: [{
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return shiftDatatableActions(row.id);
            },
        }, {
            data: "is_active",
            defaultContent: "",
            targets: 2,
            orderable: false,
            className: "dt-column-center",
            render: function (data, type, row, meta) {
                return shiftDatatableStatus(row.is_active);
            },
        }, {
            targets: "_all",
            defaultContent: "",
        }], initComplete: function (settings, json) {
            if (typeof aclActionUpdate == "function") {
                aclActionUpdate();
            }
        }
    });
    var _htmlContent = '<button id="shift_schedule-new" type="button" class="m-portlet__nav-link btn m-btn--square btn-success btnNew"  data-toggle="modal" data-target="#modal-shift_schedule"><i class="fa fa-plus"></i> New </button>';
    $("div.toolbar").html(_htmlContent);

    function shiftDatatableActions($id) {
        if ($id) {
            var _actionButton = "";
            if (typeof _currentActions !== "undefined" && jQuery.inArray("assign", _currentActions) !== -1) {
                _actionButton += "<button type='button' class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnAssign btnAssignShift' data-id='" + $id + "'><i class='la la-key'></i></button>";
                _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnAssign btnAssignEmployeeShift' data-id='" + $id + "'><i class='la la-users'></i></button>";
                _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill btn-sm ' onclick='schedule_list(" + $id + ")'><i class='la la-list'></i></button>";
            }
            if (typeof _currentActions !== "undefined" && jQuery.inArray("edit", _currentActions) !== -1) {
                _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnEdit btnEditShift' data-id='" + $id + "'><i class='la la-edit'></i></button>";
            }
            if (typeof _currentActions !== "undefined" && jQuery.inArray("delete", _currentActions) !== -1) {
                _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnDelete btnRemoveShift' data-id='" + $id + "'><i class='la la-trash'></i></button>";
            }

            if (!_actionButton) {
                _actionButton = "---";
            }
            return _actionButton;
        } else {
            return false;
        }
    }

    function shiftDatatableStatus($isActive) {
        var _html = "";
        if ($isActive == 1) {
            _html = "<span class='btn btn-info m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-check'></i></span>";
        } else {
            _html = "<span class='btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-remove'></i></span>";
        }
        return _html;
    }

    $.validate({
        form: '#form-shift_schedule',
        lang: 'en',
        onSuccess: function (form) {
            var _url = form[0].action;
            var _data = jQuery(form[0]).serialize();
            var _btnSubmit = $(form[0]).find(".btn-submit");

            $.ajax({
                url: _url,
                type: "POST",
                data: _data,
                beforeSend: function () {
                    if (typeof _btnSubmit !== "undefined") {
                        _btnSubmit.addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                },
                success: function (data) {
                    if (data.response) {
                        toastr.success(data.toastr_msg, "Added Shift Detail", 5000);
                        $("#modal-shift_schedule").modal("hide");
                        _dtShiftSchedule.ajax.reload(null, false);
                    } else {
                        toastr.error(data.toastr_msg, "Error Shift Detail", 5000);
                    }
                    if (typeof _btnSubmit !== "undefined") {
                        _btnSubmit.removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                }
            });
            return false;
        }
    });

    $(document).on("click", ".btnAssignEmployeeShift", function () {
        var _dataId = $(this).data("id");
        reloadTablePersonnel(_dataId);
        _modalPersonnelSchedule.find("#shift_id").val(_dataId);
        _modalPersonnelSchedule.modal("show");
    });

    $(document).on("click", ".btnEditShift", function () {
        var _dataId = $(this).data("id");
        $.ajax({
            url: baseUrl("gcctime/shift/get_shift_schedule/" + _dataId),
            dataType: "json",
            success: function (json) {
                if (json.response) {
                    vmEditShiftData.row = json.row;
                    _modalEditShiftSchedule.modal("show");
                    $.validate({
                        form: '#form-edit_shift_schedule',
                        lang: 'en',
                        onSuccess: function (form) {
                            var _url = form[0].action;
                            var _data = jQuery(form[0]).serialize();
                            var _btnSubmit = $(form[0]).find(".btn-submit");

                            $.ajax({
                                url: _url,
                                type: "POST",
                                data: _data,
                                beforeSend: function () {
                                    if (typeof _btnSubmit !== "undefined") {
                                        _btnSubmit.addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                    }
                                },
                                success: function (data) {
                                    if (data.response) {
                                        toastr.success(data.toastr_msg, "Added Shift Detail", 5000);
                                        $("#modal-edit_shift_schedule").modal("hide");
                                        _dtShiftSchedule.ajax.reload(null, false);
                                    } else {
                                        toastr.error(data.toastr_msg, "Error Shift Detail", 5000);
                                    }
                                    if (typeof _btnSubmit !== "undefined") {
                                        _btnSubmit.removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                    }
                                }
                            });
                            return false;
                        }
                    });
                }
            }

        })
    });

    var vmEditShiftData = new Vue({
        el: "#form-edit_shift_schedule",
        data: {row: {}}
    });

    var reloadTablePersonnel = function (id) {
        tempShiftId = id;
        _dtPersonnelSchedule.ajax.reload();
    }

    _dtPersonnelSchedule = $("#table_personnel_select").DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: false,
        processing: false,
        ajax: {
            url: "<?php echo site_url("gcctime/shift/get_employee_shift"); ?>",
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.id = tempShiftId;
            }
        },
        columnDefs: [
            {data: "chkbox", targets: 0, orderable: false},
            {data: "name", targets: 1, className: 'text-center'},
            {data: "action", targets: -1, orderable: false},
            {targets: "_all", defaultContent: ""}
        ], paging: false,
        info: false,
        order: [[1, "asc"]],
        scrollY: '50vh',
        scrollCollapse: true,
    });

    function schedule_list(id) {
        $('#modal-assigned').modal('show');
        var _dtPersonnelAssigned = $("#table_assigned").DataTable({
            dom: '<"toolbar">frtlip',
            serverSide: false,
            processing: false,
            ajax: "<?php echo site_url("gcctime/shift/get_employee_assigned"); ?>" + "/" + id,
            columnDefs: [

                {data: "name", targets: 0},

            ], paging: false,
            info: false,
            order: [[0, "asc"]],
            scrollY: '50vh',
            scrollCollapse: true,
            destroy: true,
        });

        _dtPersonnelAssigned.ajax.reload();
    }

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

    //select multiple personnel
    $(".btn-submit-multi-personnel.btnSelect").on("click", function () {
        var check_count = _modalPersonnelSchedule.find('.checkSingle:checkbox:checked').length;
        var _shiftId = _modalPersonnelSchedule.find("#shift_id");
        if (check_count == 0) {
            toastr.error("No Data Selected!");
        } else {
            mApp.block('#modal-select-personnel_schedule .modal-content', {
                overlayColor: '#000000',
                state: 'primary'
            });
            _modalPersonnelSchedule.find('.checkSingle:checkbox:checked').each(function () {
                var cRow = $(this).closest("tr");
                var cButton = cRow.find("button.btn");
                var _dataId = $(this).attr("data-id");

                $.ajax({
                    url: "<?php echo site_url('gcctime/shift/select_personnel'); ?>",
                    type: "POST",
                    dataType: "json",
                    data: {id: _dataId, shift_id: _shiftId.val(), csrf_token: _csrf_hash},
                    beforeSend: function () {
                    },
                    success: function (data) {
                    }
                });
            });
            mApp.unblock('#modal-select-personnel_schedule .modal-content');
            _modalPersonnelSchedule.find('.checkSingle:checkbox:checked').prop("checked", false);
            _modalPersonnelSchedule.find('#checkAll:checkbox:checked').prop("checked", false);
            setTimeout(function () {
                reloadTablePersonnel(_shiftId.val());
            }, 500);
            /* table_select_personnel.draw(); */
        }
    });

    //select dis-multiple personnel
    $(".btn-submit-desmulti-personnel.btnDeselect").on("click", function () {
        var check_count = _modalPersonnelSchedule.find('.checkSingle:checkbox:checked').length;
        var _shiftId = _modalPersonnelSchedule.find("#shift_id");
        if (check_count == 0) {
            toastr.error("No Data Selected!");
        } else {
            mApp.block('#modal-select-personnel_schedule .modal-content', {
                overlayColor: '#000000',
                state: 'primary'
            });
            _modalPersonnelSchedule.find('.checkSingle:checkbox:checked').each(function () {
                var cRow = $(this).closest("tr");
                var cButton = cRow.find("button.btn");
                var _dataId = $(this).attr("data-id");

                $.ajax({
                    url: "<?php echo site_url('gcctime/shift/deselect_personnel'); ?>",
                    type: "POST",
                    dataType: "json",
                    data: {id: _dataId, shift_id: _shiftId.val(), csrf_token: _csrf_hash},
                    beforeSend: function () {
                    },
                    success: function (data) {
                    }
                });
            });
            mApp.unblock('#modal-select-personnel_schedule .modal-content');
            _modalPersonnelSchedule.find('.checkSingle:checkbox:checked').prop("checked", false);
            _modalPersonnelSchedule.find('#checkAll:checkbox:checked').prop("checked", false);
            setTimeout(function () {
                reloadTablePersonnel(_shiftId.val());
            }, 500);
            /* table_select_personnel.draw(); */
        }
    });

    $(document).on("click", ".btnSelect", function () {
        var _dataId = $(this).data("id");
        var _shiftId = _modalPersonnelSchedule.find("#shift_id");

        $.ajax({
            url: "<?php echo site_url("gcctime/shift/select_personnel"); ?>",
            type: "post",
            dataType: "json",
            data: {id: _dataId, shift_id: _shiftId.val(), csrf_token: _csrf_hash},
            success: function (json) {
                if (json.response) {
                    reloadTablePersonnel(_shiftId.val());
                }
            }
        });
    });

    $(document).on("click", ".btnDeselect", function () {
        var _dataId = $(this).data("id");
        var _shiftId = _modalPersonnelSchedule.find("#shift_id");

        $.ajax({
            url: "<?php echo site_url("gcctime/shift/deselect_personnel"); ?>",
            type: "post",
            dataType: "json",
            data: {id: _dataId, shift_id: _shiftId.val(), csrf_token: _csrf_hash},
            success: function (json) {
                if (json.response) {
                    reloadTablePersonnel(_shiftId.val());
                }
            }
        });
    });

    $(document).on("click", ".btnAssignShift", function () {
        var _dataId = $(this).data("id");

        $.ajax({
            url: "<?php echo site_url("gcctime/shift/get_shift_schedule_data"); ?>",
            type: "post",
            dataType: "json",
            data: {id: _dataId, csrf_token: _csrf_hash},
            success: function (json) {
                if (json.response) {
                    vm.items = json.data;
                    vm.$mount();

                    var _schedule = json.json_tree;
                    var _shiftResource = json.shift_resource;
                    _shiftResource = _shiftResource.map(Number);

                    if (_schedule.monday.length > 0) {
                        var resourceTree = $("#modal-shift_schedule-assign").find("#tree_shift-monday");
                        if (typeof resourceTree !== "undefined") {
                            getTreeResource(resourceTree, _schedule.monday, _shiftResource, "Monday");
                        }
                    }
                    if (_schedule.tuesday.length > 0) {
                        var resourceTree = $("#modal-shift_schedule-assign").find("#tree_shift-tuesday");
                        if (typeof resourceTree !== "undefined") {
                            getTreeResource(resourceTree, _schedule.tuesday, _shiftResource, "Tuesday");
                        }
                    }
                    if (_schedule.wednesday.length > 0) {
                        var resourceTree = $("#modal-shift_schedule-assign").find("#tree_shift-wednesday");
                        if (typeof resourceTree !== "undefined") {
                            getTreeResource(resourceTree, _schedule.wednesday, _shiftResource, "Wednesday");
                        }
                    }
                    if (_schedule.thursday.length > 0) {
                        var resourceTree = $("#modal-shift_schedule-assign").find("#tree_shift-thursday");
                        if (typeof resourceTree !== "undefined") {
                            getTreeResource(resourceTree, _schedule.thursday, _shiftResource, "Thursday");
                        }
                    }
                    if (_schedule.friday.length > 0) {
                        var resourceTree = $("#modal-shift_schedule-assign").find("#tree_shift-friday");
                        if (typeof resourceTree !== "undefined") {
                            getTreeResource(resourceTree, _schedule.friday, _shiftResource, "Friday");
                        }
                    }
                    if (_schedule.saturday.length > 0) {
                        var resourceTree = $("#modal-shift_schedule-assign").find("#tree_shift-saturday");
                        if (typeof resourceTree !== "undefined") {
                            getTreeResource(resourceTree, _schedule.saturday, _shiftResource, "Saturday");
                        }
                    }
                    if (_schedule.sunday.length > 0) {
                        var resourceTree = $("#modal-shift_schedule-assign").find("#tree_shift-sunday");
                        if (typeof resourceTree !== "undefined") {
                            getTreeResource(resourceTree, _schedule.sunday, _shiftResource, "Sunday");
                        }
                    }

                    $("#modal-shift_schedule-assign").modal("show");
                } else {
                    toastr.error(json.toastr_msg, "Error Assign Shift", 5000);
                }
            }
        });
    });

    jQuery(document).on("click", "#modal-shift_schedule-assign .btn-submit", function (e) {
        e.preventDefault();

        var _shiftId = $("#shift_id").val();
        var _lateId = $("#lates").val();
        var _undertimeId = $("#undertime").val();

        _lateId = (typeof _lateId !== "undefined") ? _lateId : 0;
        _undertimeId = (typeof _undertimeId !== "undefined") ? _undertimeId : 0;

        var jsonData = {};

        var treeMonday = $("#modal-shift_schedule-assign").find("#tree_shift-monday");
        var treeTuesday = $("#modal-shift_schedule-assign").find("#tree_shift-tuesday");
        var treeWednesday = $("#modal-shift_schedule-assign").find("#tree_shift-wednesday");
        var treeThursday = $("#modal-shift_schedule-assign").find("#tree_shift-thursday");
        var treeFriday = $("#modal-shift_schedule-assign").find("#tree_shift-friday");
        var treeSaturday = $("#modal-shift_schedule-assign").find("#tree_shift-saturday");
        var treeSunday = $("#modal-shift_schedule-assign").find("#tree_shift-sunday");

        if (typeof treeMonday !== "undefined") {
            var _treeJson = $(treeMonday).jstree(true).get_json("#", {flat: true});
            jsonData = Object.assign({}, jsonData, {monday: _treeJson});
        }
        if (typeof treeTuesday !== "undefined") {
            var _treeJson = $(treeTuesday).jstree(true).get_json("#", {flat: true});
            jsonData = Object.assign({}, jsonData, {tuesday: _treeJson});
        }
        if (typeof treeWednesday !== "undefined") {
            var _treeJson = $(treeWednesday).jstree(true).get_json("#", {flat: true});
            jsonData = Object.assign({}, jsonData, {wednesday: _treeJson});
        }
        if (typeof treeThursday !== "undefined") {
            var _treeJson = $(treeThursday).jstree(true).get_json("#", {flat: true});
            jsonData = Object.assign({}, jsonData, {thursday: _treeJson});
        }
        if (typeof treeFriday !== "undefined") {
            var _treeJson = $(treeFriday).jstree(true).get_json("#", {flat: true});
            jsonData = Object.assign({}, jsonData, {friday: _treeJson});
        }
        if (typeof treeSaturday !== "undefined") {
            var _treeJson = $(treeSaturday).jstree(true).get_json("#", {flat: true});
            jsonData = Object.assign({}, jsonData, {saturday: _treeJson});
        }
        if (typeof treeSunday !== "undefined") {
            var _treeJson = $(treeSunday).jstree(true).get_json("#", {flat: true});
            jsonData = Object.assign({}, jsonData, {sunday: _treeJson});
        }

        var _string = JSON.stringify(jsonData);
        $.ajax({
            url: "<?php echo site_url("gcctime/shift/save_shift_schedule"); ?>",
            type: "post",
            dataType: "json",
            data: {nodes: _string, id: _shiftId, late_id: _lateId, undertime_id: _undertimeId, csrf_token: _csrf_hash},
            success: function (json) {
                if (json.response) {
                    if (json.eid && json.eid.length) {
                        confirmRegenerateTimesheetModal.modal("show");
                        $("#employees", confirmRegenerateTimesheetModal).val(JSON.stringify(json.eid));
                    } else {
                        toastr.success(json.toastr_msg, "Shift Schedule");
                    }

                    // $("#modal-shift_schedule-assign").modal("hide");
                } else {
                    toastr.error(json.toastr_msg, "Shift Schedule");
                }
            }
        });
    });

    var getTreeResource = function (object, data, ids, title) {
        $(object).jstree("destroy");
        $(object).jstree({
            core: {
                data: data,
                themes: {icons: false},
                multiple: false,
            },
            checkbox: {
                cascade: "",
                three_state: false,
            },
            plugins: ["checkbox", "wholerow"],
        }).on('ready.jstree', function () {
            $(this).jstree('open_all');

            var jsTreeCheckbox = $(this).find("i.jstree-icon.jstree-checkbox");
            var jsTreeOcl = $(this).find("i.jstree-icon.jstree-ocl");
            if (typeof jsTreeOcl !== "undefined" && jsTreeOcl.length > 0) {
                jsTreeOcl.remove();
            }
            if (typeof jsTreeCheckbox !== "undefined" && jsTreeCheckbox.length > 0) {
                jsTreeCheckbox.css("margin-right", "15px");
            }

            var jsTreeParent = $(this).find(".no_checkbox");
            if (typeof jsTreeParent !== "undefined" && jsTreeParent.length > 0) {
                jsTreeParent.find(".jstree-checkbox").remove();
            }
            $(this).prepend("<span><i class='fa fa-calendar'></i> - " + title + "</span>");

            var _treeItem = $(this).find("li[role=treeitem]");
            if (typeof _treeItem !== "undefined") {
                _treeItem.each(function (i, v) {
                    var _id = $(v).attr("id");
                    _id = parseInt(_id);
                    if (jQuery.inArray(_id, ids) !== -1) {
                        $(this).jstree("select_node", this);
                    }
                });
            }
        });
    }

    var _items = {id: 0, name: "", description: "", is_active: "Inactive", late_id: 0, undertime_id: 0};
    var vm = new Vue({
        el: "#form-shift_schedule-assign",
        data: {items: _items},
        mounted: function () {
            $(this.$el).find("#lates").trigger("change");
            $(this.$el).find("#undertime").trigger("change");
        }
    });

    $(document)
        .on('show.bs.modal', '.modal', function () {
            // comment out for conflict zIndex
            // var zIndex = Math.max.apply(null, Array.prototype.map.call(document.querySelectorAll('*'), function (el) {
            //     return +el.style.zIndex;
            // })) + 200;

            // $(this).css('z-index', zIndex - 1);

            // setTimeout(function () {
            //     $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 2).addClass('modal-stack');
            // }, 0);
            // comment out for conflict zIndex
        });

    $(document).on('hidden.bs.modal', '.modal', function () {
        $('.modal:visible').length && $(document.body).addClass('modal-open');
    });
</script>