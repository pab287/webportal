const tempTable = $("#table-payroll-type--incentive");
const modalAddIncentive = $("#modal-add-incentive-type");
const modalUpdateIncentive = $("#modal-update-incentive-type");
const modalArchiveIncentive = $("#modal-archive-incentive-type");

let dtTable, search_val = "";;
if (typeof tempTable !== "undefined" && tempTable.length == 1) {
    dtTable = tempTable.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ajax: {
            url: baseUrl("payroll/payroll_type/get_incentive_payroll_type_datatable_request"),
            type: "POST",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                return d;
            }
        },
        columns: [
            { data: "name", width: "15%" },
            { data: "description", width: "*" },
            { data: "date_from", width: "15%", render: function (data) { return moment(data).format("MMMM Do"); } },
            { data: "date_to", width: "15%", render: function (data) { return moment(data).format("MMMM Do"); } },
            { data: "is_active", width: "5%", orderable: false, className: "dt-column-center", render: function (data) { return privDatatableStatus(data); } },
            { data: null, width: "8%" }
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                className: "dt-column-center",
                render: function (data, type, row, meta) {
                    return tempDatatableActions(row.id);
                }
            }, {
                targets: "_all",
                defaultContent: ""
            }
        ]
    });
    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtTable.ajax.reload();
    });

}

$(document).on("click", ".btnArchiveIncentive", function () {
    const id = $(this).data("id");
    $.ajax({
        url: siteUrl("payroll/payroll_type/get_payroll_incentive_type/" + id),
        dataType: "json",
        success: function (json) {
            vmArchiveIncentive.row = {};
            if (json.response) {
                const tempData = json.data;
                vmArchiveIncentive.row = Object.assign({}, tempData);
                modalArchiveIncentive.modal("show");
            }
        }
    });
});

$(document).submit("#frm-archive-incentive-type", function (e) {
    e.preventDefault();
    var currentForm = $(e.target);
    var url = currentForm.attr("action");
    var formData = currentForm.serialize();

    $.ajax({
        url: url,
        type: "post",
        dataType: "json",
        data: formData,
        success: function (json) {
            if (json.response) {
                toastr.warning(json.toastr_msg, "Incentive Payroll Type");
                modalArchiveIncentive.modal("hide");
                dtTable.ajax.reload(null, true);
            } else {
                toastr.error(json.toastr_msg, "Incentive Payroll Type");
            }
        }
    });
});

$(document).on("click", ".btnEditIncentive", function () {
    const id = $(this).data("id");
    $.ajax({
        url: siteUrl("payroll/payroll_type/get_payroll_incentive_type/" + id),
        dataType: "json",
        success: function (json) {
            vmIncentive.row = {};
            if (json.response) {
                const tempData = json.data;
                vmIncentive.row = Object.assign({}, tempData);
                if (typeof modalUpdateIncentive !== "undefined" && modalUpdateIncentive.length === 1) {
                    modalUpdateIncentive.find("#dt-picker_from").datepicker({
                        todayHighlight: false,
                        orientation: "bottom left",
                        autoclose: true,
                        format: 'MM dd',
                        weekStart: 1,
                        startView: 1,
                        maxViewMode: 1,
                        changeYear: false,
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        }
                    }).on("changeDate", function (e) {
                        $(e.target).validate();
                    });

                    modalUpdateIncentive.find("#dt-picker_to").datepicker({
                        todayHighlight: false,
                        orientation: "bottom left",
                        autoclose: true,
                        format: 'MM dd',
                        weekStart: 1,
                        startView: 1,
                        maxViewMode: 1,
                        changeYear: false,
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        }
                    }).on("changeDate", function (e) {
                        $(e.target).validate();
                    });

                    modalUpdateIncentive.modal("show");
                    vmIncentive.$mount();
                }
            }
        }
    });
});

if (typeof modalAddIncentive !== "undefined" && modalAddIncentive.length == 1) {
    modalAddIncentive.find("#dt-picker_from").datepicker({
        todayHighlight: true,
        orientation: "bottom left",
        autoclose: true,
        format: 'MM dd',
        weekStart: 1,
        startView: 1,
        maxViewMode: 1,
        changeYear: false,
        templates: {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        }
    }).on("changeDate", function (e) {
        $(e.target).validate();
    });
    modalAddIncentive.find("#dt-picker_to").datepicker({
        todayHighlight: true,
        orientation: "bottom left",
        autoclose: true,
        format: 'MM dd',
        weekStart: 1,
        startView: 1,
        maxViewMode: 1,
        changeYear: false,
        templates: {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        }
    }).on("changeDate", function (e) {
        $(e.target).validate();
    });
}

function privDatatableStatus($isActive) {
    var _html = "";
    if ($isActive == 1) {
        _html =
            "<span class='btn btn-info m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-check'></i></span>";
    } else {
        _html =
            "<span class='btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-remove'></i></span>";
    }
    return _html;
}

function tempDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        if (
            typeof _currentActions !== "undefined" &&
            jQuery.inArray("edit", _currentActions) !== -1
        ) {
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnEdit btnEditIncentive' data-id='" +
                $id +
                "'><i class='la la-edit'></i></button>";
        }
        if (
            typeof _currentActions !== "undefined" &&
            jQuery.inArray("archive", _currentActions) !== -1
        ) {
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btn-sm btnArchive btnArchiveIncentive' data-id='" +
                $id +
                "'><i class='la la-archive'></i></button>";
        }

        if (!_actionButton) {
            _actionButton = "---";
        }
        return _actionButton;
    } else {
        return false;
    }
}

$.validate({
    form: "#frm-add-incentive-type",
    lang: "en",
    onSuccess: function (form) {
        var currentForm = form[0];
        var formUrl = currentForm.action;
        var formData = $(currentForm).serialize();
        const tempModal = $(currentForm).closest(".modal");
        $.ajax({
            url: formUrl,
            type: "POST",
            dataType: "json",
            data: formData,
            beforeSend: function () {
                $(currentForm)
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.response) {
                    currentForm.reset();
                    dtTable.ajax.reload();
                    if (typeof tempModal !== "undefined" && tempModal.length == 1) { tempModal.modal("hide"); }
                    toastr.success(data.toastr_msg, "Incentive Payroll Type");
                } else {
                    toastr.error(data.toastr_msg, "Incentive Payroll Type");
                }
                $(currentForm)
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            }
        });
        return false;
    }
});

var vmArchiveIncentive = new Vue({
    el: "#archiveIncentiveContainer",
    data: { row: {} }
});

var vmIncentive = new Vue({
    el: "#incentiveContainer",
    data: { row: {} },
    methods: {
        validateFields: function () {
            const _this = this;
            const _currentElement = _this.$el;
            const _currentForm = $(_currentElement).find("form#frm-update-incentive-type");
            if (typeof _currentForm !== "undefined" && _currentForm.length === 1) {
                $.validate({
                    form: _currentForm,
                    lang: "en",
                    onSuccess: function (form) {
                        console.log("test");
                        var currentForm = form[0];
                        var formUrl = currentForm.action;
                        var formData = $(currentForm).serialize();
                        const tempModal = $(currentForm).closest(".modal");
                        $.ajax({
                            url: formUrl,
                            type: "POST",
                            dataType: "json",
                            data: formData,
                            beforeSend: function () {
                                $(currentForm)
                                    .find(".btn-submit")
                                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            },
                            success: function (data) {
                                if (data.response) {
                                    currentForm.reset();
                                    dtTable.ajax.reload();
                                    if (typeof tempModal !== "undefined" && tempModal.length == 1) { tempModal.modal("hide"); }
                                    toastr.success(data.toastr_msg, "Incentive Payroll Type");
                                } else {
                                    toastr.error(data.toastr_msg, "Incentive Payroll Type");
                                }
                                $(currentForm)
                                    .find(".btn-submit")
                                    .removeClass(
                                        "m-btn--custom m-loader m-loader--light m-loader--right"
                                    );
                            }
                        });
                        return false;
                    }
                });
            }
        }
    }, mounted: function () {
        this.validateFields();
    }
});