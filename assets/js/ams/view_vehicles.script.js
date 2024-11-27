const path = window.location.pathname.split("/");
const id = path[path.length - 2];
const isComponent = path.pop();

const dtComponents = $("#vehicle-viewing-components-table")
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("ams/vehicles/get_vehicle_components/" + id),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = $("#search-vehicle-components").val();
            }
        },
        searching: false,
        columns: [
            {data: "asset_code", width: "13%"},
            {data: "description"},
            {data: "remarks", width: "20%"},
            {
                data: "status",
                width: "15%",
                render: function (data) {
                    const badgeClass = parseInt(data) === 1 ? " m-badge--metal" : " m-badge--success";
                    const status = parseInt(data) === 1 ? "Excluded" : "Included";
                    return "<span style='border-radius: 3em;' " +
                        "         class='px-3 py-1 m-badge" + badgeClass + "'>" + status + "</span>";
                }
            },
        ]
    });

$("#search-vehicle-components")
    .donetyping(function () {
        dtComponents.ajax.reload();
    });

const dtDocuments = $("#vehicle-viewing-documents-table")
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("ams/vehicles/get_vehicle_documents/" + id),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = $("#search-vehicle-document").val();
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
                width: "40%"
            },
            {
                data: "",
                defaultContent: "",
                orderable: false,
                width: "8%",
                className: "text-center",
                render: function (data, type, row) {
                    return "" +
                        "   <a href='" + baseUrl("uploads/files/" + row.asset_id + "/" + row.filename) + "'" +
                        "        download='" + row.filename + "' title='Download' " +
                        "        data-placement='bottom'" +
                        "        class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnDownloadFile'>" +
                        "       <i class='fa fa-download'></i>" +
                        "   </a>";
                }
            }
        ]
    });

$("#search-vehicle-document")
    .donetyping(function () {
        dtDocuments.ajax.reload();
    });

const dtCosting = $("#vehicle-viewing-costing-table").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("ams/vehicles/get_costing/" + id),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = $("#search-vehicle-costing").val();
        }
    },
    searching: false,
    columns: [
        {data: "description"},
        {
            data: "price",
            width: "15%",
            render: function (data, type, row) {
                return parseFloat(data).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        },
    ]
});

$("#search-vehicle-costing")
    .donetyping(function () {
        dtCosting.ajax.reload();
    });


const dtMaintenance = $("#vehicle-viewing-maintenance-table")
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("ams/vehicles/get_vehicle_maintenance_log/" + id),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = $("#search-vehicle-maintenance").val();
            }
        },
        searching: false,
        columns: [
            {
                data: "description"
            },
            {
                data: "last_value"
            },
            {
                data: "last_date_perform"
            },
            {
                data: "next_value"
            },
            {
                data: "intDate"
            },
            {
                data: "next_date_perform"
            }
        ]
    });

$("#search-vehicle-maintenance")
    .donetyping(function () {
        dtMaintenance.ajax.reload();
    });

const dtAccountability = $("#vehicle-viewing-accountability-table")
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("ams/vehicles/get_vehicle_accountability/" + id),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = $("#search-vehicle-accountability").val();
            }
        },
        searching: false,
        columns: [
            {
                data: "date_issued",
                width: "15%",
                render: function (data) {
                    return moment(new Date(data)).format("ll");
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
                        return row.acct_status;
                    }

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
        ]
    });

$("#search-vehicle-accountability")
    .donetyping(function () {
        dtAccountability.ajax.reload();
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
        } else if (inputId.indexOf('costing') >= 0) {
            dtCosting.ajax.reload();
        } else if (inputId.indexOf('maintenance') >= 0) {
            dtMaintenance.ajax.reload();
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
            url: baseUrl(`ams/assets/recover_asset/${id}/2`),
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
                        window.location.assign(baseUrl(`ams/vehicles/${parseInt(isComponent) === 1 ? 'edit_vehicle_component' : 'edit_vehicle'}/${response.id}`));
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