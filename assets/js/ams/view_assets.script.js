const path = window.location.pathname.split("/");
const id = path[path.length - 2];
const isComponent = path.pop();

const dtComponents = $("#asset-viewing-components-table")
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("ams/assets/get_asset_components/" + id),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = $("#search-asset-component").val();
            }
        },
        searching: false,
        columns: [
            {data: "asset_code", width: "13%"},
            {
                data: "description",
                render: function (data, type, row) {
                    if (row.name) {
                        return row.name + "<br/>" + "<span class='text-muted m--regular-font-size-sm1'>" + data + "</span>";
                    } else {
                        return data;
                    }
                }
            },
            {data: "remarks", width: "20%"},
            {
                data: "isExcluded",
                width: "15%",
                render: function (data) {
                    const badgeClass = parseInt(data) === 1 ? " m-badge--metal" : " m-badge--success";
                    const status = parseInt(data) === 1 ? "Excluded" : "Included";
                    return "<span style='border-radius: 3em;' " +
                        "         class='px-3 py-1 m-badge" + badgeClass + "'>" + status + "</span>";
                }
            }
        ]
    });


const dtDocuments = $("#asset-viewing-documents-table")
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("ams/assets/get_asset_documents/" + id),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = $("#search-asset-document").val();
            }
        },
        searching: false,
        autoWidth: false,
        columns: [
            {
                data: "description"
            },
            {
                data: "filename",
            },
            {
                data: "",
                defaultContent: "",
                orderable: false,
                width: "8%",
                className: "text-center",
                render: function (data, type, row) {
                    return "" +
                        "   <a href='" + baseUrl("uploads/files/asset_documents/" + row.asset_id + "/" + row.filename) + "'" +
                        "        download='" + row.filename + "'" +
                        "        class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnDownloadFile'" +
                        "        data-toggle='m-tooltip' data-skin='dark' data-placement='bottom' data-original-title='Click to Download'" +
                        "        data-delay='{\"show\": 300}'>" +
                        "       <i class='fa fa-download'></i>" +
                        "   </a>";
                }
            }
        ]
    });


const dtAccountability = $("#asset-viewing-accountability-table")
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("ams/assets/get_asset_accountability/" + id),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = $("#search-asset-accountability").val();
            },
        },
        searching: false,
        columns: [
            {
                data: "date_issued",
                width: "15%",
                render: function (data) {
                    return moment(data).format("MMM. DD, YYYY");
                }
            },
            {
                data: "reference_no",
                width: "20%"
            },
            {
                data: "employee",
                render: function (data, type, row) {
                    return "" +
                        " <p class='mb-0'>" + data + "</p>" +
                        " <p class='text-muted mb-0'>" + row.company + "</p>";
                }
            },
            {
                data: "status",
                width: "30%",
                render: function (data, type, row) {
                    if (row.acct_status === "Cancelled") {
                        return "Cancelled";
                    } else {
                        switch (parseInt(data)) {
                            case 1:
                                return "Returned";
                                break;
                            case 2:
                                return "Partially Returned";
                                break;
                            default:
                                return "Current";
                                break;
                        }
                    }
                }
            }
        ]
    });

const dtBorrowing = $("#asset-viewing-borrowing-table")
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("ams/assets/get_asset_borrowing_history/" + id),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = $("#search-asset-borrowing-history").val();
            }
        },
        searching: false,
        columns: [
            {
                data: "date_trans",
                width: "15%",
                render: function (data) {
                    return dateFormatter(new Date(data));
                }
            },
            {
                data: "reference_no",
                width: "20%"
            },
            {
                data: "employee",
                render: function (data, type, row) {
                    return "" +
                        " <p class='mb-0'>" + data + "</p>" +
                        " <p class='text-muted mb-0'>" + row.company + "</p>";
                }
            },
            {
                data: "status",
                width: "15%",
                render: function (data) {
                    return parseInt(data) === 0 ? 'Current' : 'Returned';
                }
            }, {
                data: 'days_overdue',
                width: '15%',
                className: 'text-center',
                render: function (data, type, row) {
                    if (parseInt(row.status) === 1) {
                        return 0;
                    } else {
                        return parseInt(data) <= 0 ? 0 : data;
                    }
                }
            }
        ]
    });

$("#search-asset-component")
    .donetyping(function () {
        dtComponents.ajax.reload();
    });

$("#search-asset-document")
    .donetyping(function () {
        dtDocuments.ajax.reload();
    });

$("#search-asset-accountability")
    .donetyping(function () {
        dtAccountability.ajax.reload();
    });

$("#search-asset-borrowing-history")
    .donetyping(function () {
        dtBorrowing.ajax.reload();
    });

$(".input-group-btn > .tab-clear-search")
    .on("click", function () {
        const input = $(this).parent().siblings('input');
        const inputId = input.attr('id');
        input.val('');

        if (inputId.indexOf('component') >= 0) {
            dtComponents.ajax.reload();
        } else if (inputId.indexOf('document') >= 0) {
            dtDocuments.ajax.reload();
        } else if (inputId.indexOf('borrowing') >= 0) {
            dtBorrowing.ajax.reload();
        } else if (inputId.indexOf('accountability') >= 0) {
            dtAccountability.ajax.reload();
        }
    });

$("#recover-status")
    .select2({
        width: "100%",
        placeholder: "SELECT AN OPTION",
        dropdownParent: $("#recover-modal")
    });


$.validate({
    form: $("#recover-form"),
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        let data = form.serializeArray();
        data.push({name: "csrf_token", value: _csrf_hash});
        const button = $("[type='submit']", form);

        $.ajax({
            url: baseUrl(`ams/assets/recover_asset/${id}/1`),
            type: "POST",
            data,
            dataType: "JSON",
            beforeSend: function () {
                button.addClass("m-btn--custom m-loader m-loader--light m-loader--left");
            },
            success: function (response) {
                button.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                const toast = response.success ? "success" : "error";

                $("#recover-modal").modal("hide");
                toastr[toast](`<div class="m--font-boldest">${response.message}</div>`, `<div class="m--font-bolder">${response.title}</div>`, {timeOut: 10000});

                if (response.success) {
                    setTimeout(() => {
                        window.location.assign(baseUrl(`ams/assets/${parseInt(isComponent) === 1 ? 'edit_asset_component' : 'edit_fixed_asset'}/${response.id}`));
                    }, 1500);
                }
            }
        });
        return false;
    }
});

$("#datepicker").datepicker({
    todayHighlight: true,
    orientation: "bottom left",
    templates: {
        leftArrow: '<i class="la la-angle-left"></i>',
        rightArrow: '<i class="la la-angle-right"></i>'
    },
    format: "MM dd, yyyy",
    autoclose: true
});

$("#datepicker").datepicker("setDate", new Date());