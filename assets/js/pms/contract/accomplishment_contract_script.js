var tempModal = new accomplishmentListModal();
var vmNewAccomplishment;
var modalNewAccomplishment;
search_val = "";
var tableAccomplisment = $("#table-accomplishments");
var dtAccomplishment = tableAccomplisment.DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    searching: false,
    ordering: false,
    ajax: {
        url: baseUrl("pms/contract/do_post_event/get_contract_accomplishment_datatable_request"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
            return d;
        }, error: function (xhr, error, code) {
            if (error == "parsererror") {
                toastr.warning(code, "Data Table Reloading", 5000);
                dtAccomplishment.ajax.reload(null, false);
            }
        }
    },
    columns: [
        { data: "week_no", width: "15%" },
        { data: "week_duration", width: "*" },
        { data: "created_name", width: "15%", className: "text-center" },
        { data: "approved_name", width: "15%", className: "text-center" },
        { data: "status", width: "12%", className: "text-center" },
        { data: null, width: "8%", className: "text-center" }
    ],
    columnDefs: [{
        data: "status",
        defaultContent: "",
        targets: -2,
        orderable: false,
        render: function (data, type, row, meta) {
            return tempDatatableStatus(data);
        }
    }, {
        data: null,
        defaultContent: "",
        targets: -1,
        orderable: false,
        render: function (data, type, row, meta) {
            return tempDataTableActions(row.id);
        }
    }, {
        targets: "_all",
        defaultContent: ""
    }]
});

function tempDataTableActions($id) {
    if ($id) {
        var _actionButton = "";
        if (jQuery.inArray("edit", _currentActions) !== -1) {
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditAccomplishment' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='Edit Contract Accomplishment' data-id='" + $id + "'><i class='la la-edit'></i></button>";
        }
        if (jQuery.inArray("view", _currentActions) !== -1) {
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnView btnViewAccomplishment' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='View Contract Accomplishment' data-id='" + $id + "'><i class='la la-sliders'></i></button>";
        }
        if (jQuery.inArray("archive", _currentActions) !== -1) {
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnArchiveAccomplishment btnArchive' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='Archive Contract Accomplishment' data-id='" +
                $id +
                "'><i class='la la-file-archive-o'></i></button>";
        }
        return _actionButton;
    } else {
        return false;
    }
}

function tempDatatableStatus($status) {
    var _html = "<span class='m-badge m-badge--warning m-badge--wide'> PENDING </span>";
    switch ($status) {
        case "0": _html = "<span class='m-badge m-badge--custom-wide m-badge--warning m-badge--wide m--font-light'> PENDING </span>"; break;
        case "1": _html = "<span class='m-badge m-badge--custom-wide m-badge--success m-badge--wide'> APPROVED </span>"; break;
        case "2": _html = "<span class='m-badge m-badge--custom-wide m-badge--danger m-badge--wide'> CANCELLED </span>"; break;
        default: _html = "<span class='m-badge m-badge--custom-wide m-badge--warning m-badge--wide'> PENDING </span>"; break;
    }

    return _html;
}

$(document).on("click", ".btnNewAccomplishment", function () {
    window.location.replace(baseUrl("pms/contract/new_accomplishment/" + _tempContentData.id));
});

$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    dtAccomplishment.ajax.reload();
});

jQuery(document).ready(function () {
    modalNewAccomplishment = tempModal.new_accomplishment();
});