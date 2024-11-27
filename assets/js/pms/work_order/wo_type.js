var tableWoTypeList = $("#table-wo_type");
var modalAddWoType = $("#modal-addWoType");
const modalContainer = $("#modal-container");
const modalArchiveConfirmation = $("#confirm-archive-modal");

if (typeof tableWoTypeList !== "undefined") {
    var search_val = "";
    var dtWoType = tableWoTypeList.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("pms/work_order/get_wo_type_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                return d;
            }
        },
        columns: [
            {data: "code", width: "22%"},
            {data: "label", width: "50%"},
            {data: "series", width: "10%", className: "text-center"},
            {data: "is_active", width: "8%", className: "text-center"},
            {data: null, width: "10%", className: "text-center"}
        ],
        columnDefs: [{
            data: "is_active",
            defaultContent: "",
            targets: 3,
            orderable: false,
            className: "dt-column-center",
            render: function (data, type, row, meta) {
                return tempDatatableStatus(row.is_active);
            }
        }, {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return tempDataTableActions(row);
            }
        }, {
            targets: "_all",
            defaultContent: ""
        }
        ]
    });

    function tempDataTableActions(row) {
        var _actionButton = "";
        _actionButton +=
            " <button type='button' " +
            "         class='btn btn-default m-btn m-btn--hover-accent m-btn--icon " +
            "                m-btn--icon-only m-btn--pill btnEdit btnEditWoType' " +
            "         data-placement='bottom' data-toggle='m-tooltip' title='' " +
            "         data-original-title='Edit Sequence Item'" +
            "         onclick='openEditModal(" + JSON.stringify(row) + ")'>" +
            "   <i class='la la-edit'></i>" +
            " </button>";
        _actionButton +=
            " <button type='button' " +
            "         class='btn btn-default m-btn m-btn--hover-warning m-btn--icon " +
            "                m-btn--icon-only m-btn--pill btnRemoveWoType btnDelete' " +
            "         data-placement='bottom' data-toggle='m-tooltip' title='' " +
            "         data-original-title='Archive Sequence Item'" +
            "         onclick='openConfirmArchiveModal(" + JSON.stringify(row) + ")'>" +
            "   <i class='la la-file-archive-o'></i>" +
            " </button>";
        return _actionButton;
    }

    function tempDatatableStatus($isActive) {
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

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtWoType.ajax.reload();
    });

    $(document).on("click", ".btnEditWoType", function () {
    });
}

var validateWoTypeData = function () {
    $.validate({
        form: "#frmAddWoType",
        lang: "en",
        onSuccess: function (form) {
            var currentForm = form[0];
            var formUrl = currentForm.action;
            var formData = $(currentForm).serialize();

            $.ajax({
                url: formUrl,
                type: "post",
                dataType: "json",
                data: formData,
                beforeSend: function () {
                    $(currentForm)
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right")
                        .prop("disabled", true);
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(
                            json.toastr_msg,
                            "Work order type data has been added.",
                            5000
                        );
                        if (typeof modalAddWoType !== "undefined") {
                            modalAddWoType.modal("hide");
                        }
                        dtWoType.ajax.reload();
                    } else {
                        toastr.error(
                            json.toastr_msg,
                            "Error adding work order type data!",
                            5000
                        );
                    }

                    $(currentForm)
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        ).prop("disabled", true);
                }
            });
            return false;
        }
    });
}
jQuery(document).ready(function () {
    validateWoTypeData();
});


function openEditModal(data) {
    $.ajax({
        url: baseUrl("pms/task/open_modal"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: "pms/work_order/modal/edit_work_order_type_modal",
            function_name: "passDataToDialog",
            formData: data,
            // init_modal_data_function: "getCategory"
        },
        success: function (response) {
            const html = response.html;
            const info = response.info;

            modalContainer.empty().append(html);
            modalContainer.modal("show");
        }
    });
}

var validateForm = function () {
    $.validate({
        form: "#frm-edit-wo-type",
        lang: "en",
        scrollToTopOnError: false,
        onSuccess: function (frm) {
            const form = $(frm);
            const url = form.attr("action");
            const data = form.serialize();

            $.ajax({
                url,
                type: "POST",
                datType: "JSON",
                data,
                success: function (response) {
                    if (response) {
                        dtWoType.ajax.reload();
                        toastr.success("Location was successfully updated.", "Location Updated.", 10000);
                    } else {
                        toastr.success("An error occurred while updating.", "Update Error.", 10000);
                    }

                    modalContainer.modal("hide");
                }
            });

            return false;
        }
    });
}

modalContainer.on("show.bs.modal", function () {
    validateForm();
});

function openConfirmArchiveModal(data) {
    const form = $("#frm-archive-confirmation");
    form.attr("action", "pms/work_order/archive_work_order_type/" + data.id);
    $("#item-name").html(data.label);

    modalArchiveConfirmation.modal("show");
}

$("#frm-archive-confirmation")
    .on("submit", function (e) {
        e.preventDefault();
        const url = $(this).attr("action");

        $.ajax({
            url: baseUrl(url),
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                if (response) {
                    dtWoType.ajax.reload();
                    toastr.success("Location was successfully archived.", "Company Archived.", 10000);
                } else {
                    toastr.success("An error occurred while archiving.", "Archive Error.", 10000);
                }

                modalArchiveConfirmation.modal("hide");
            }
        });
    });