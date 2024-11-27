var tableProjectUnit = $("#table-project_unit");
var modalAddProjectUnit = $("#modal-add_project_unit");
var modalEditProjectUnit = $("#modal-edit_project_unit");
var tempId = (_tempContentData.id) ? _tempContentData.id : 0;
if (typeof tableProjectUnit !== "undefined") {
    var search_val = "";
    var dtProjectUnit = tableProjectUnit.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("pms/project/get_project_unit_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.project_id = tempId;
                d.search['value'] = search_val;
                return d;
            }, error: function (xhr, error, code) {
                if (error == "parsererror") {
                    toastr.warning(code, "Data Table Reloading", 5000);
                    dtProjectUnit.ajax.reload(null, false);
                }
            }
        },
        columns: [
            { data: "code", width: "17%" },
            { data: "block", width: "5%", className: "text-center" },
            { data: "lot", width: "5%", className: "text-center" },
            { data: "description", width: "*" },
            { data: "checklist_template", width: "18%" },
            { data: "sf_status", width: "12%", className: "text-center align-middle" },
            { data: "is_active", width: "5%", className: "text-center" },
            { data: null, width: "10%", className: "text-center" }
        ],
        columnDefs: [{
            data: "sf_status",
            defaultContent: "",
            targets: -3,
            orderable: false,
            className: "dt-column-center",
            render: function (data, type, row, meta) {
                var tempStatus;
                switch (data) {
                    case "1": tempStatus = "<span class='m-badge m-badge--success m-badge--wide'> Ongoing </span>"; break;
                    case "2": tempStatus = "<span class='m-badge m-badge--warning m-badge--wide'> On Hold </span>"; break;
                    case "3": tempStatus = "<span class='m-badge m-badge--primary m-badge--wide'> Complete </span>"; break;
                    default: tempStatus = "<span class='m-badge m-badge--default m-badge--wide'> Awaiting </span>"; break;
                }
                return tempStatus;
            }
        }, {
            data: "is_active",
            defaultContent: "",
            targets: -2,
            orderable: false,
            className: "dt-column-center",
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
        }
        ]
    });

    function tempDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditSequenceForm' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='View Checklist Form' data-id='" + $id + "'><i class='la la-list-alt'></i></button>";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditProjectUnit' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='Edit Project Unit' data-id='" + $id + "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveProjectUnit btnDelete' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='Archive Project Unit' data-id='" +
                $id +
                "'><i class='la la-file-archive-o'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    function tempDatatableStatus($tempStatus) {
        var _html = "";
        if ($tempStatus == 1) {
            _html = "<span class='btn btn-info m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-check'></i></span>";
        } else {
            _html = "<span class='btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-remove'></i></span>";
        }
        return _html;
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtProjectUnit.ajax.reload();
    });

    $(document).on("click", ".btnEditSequenceForm", function () {
        var dataId = $(this).data("id");
        location.href = baseUrl("pms/project/task/" + dataId);
    });

    $(document).on("click", ".btnEditProjectUnit", function () {

    });
}

var getCurrentProjectData = function () {
    $.ajax({
        url: baseUrl("pms/project/get_current_data/" + tempId),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                vmProjectData.row = Object.assign({}, json.row);
                vmProjectData.row_count = parseInt(json.row_count);
            }
        }
    });
}

var vmProjectData = new Vue({
    el: "#temp_portlet-head",
    data: { row: {}, row_count: 0 }
});

var validateProjectUnitData = function () {
    getCurrentProjectData();
    $.validate({
        form: "#frmAddProjectUnit",
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
                            "Unit data has been added.",
                            5000
                        );
                        if (typeof modalAddProjectUnit !== "undefined") {
                            modalAddProjectUnit.modal("hide");
                        }
                        dtProjectUnit.ajax.reload(null, false);
                    } else {
                        toastr.error(
                            json.toastr_msg,
                            "Error adding unit data!",
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

var getSelect2TemplateData = function () {
    var tempId = (typeof _tempContentData.id !== "undefined") ? _tempContentData.id : 0;
    $.ajax({
        url: baseUrl("pms/task/get_select2_template_data/" + tempId),
        dataType: "json",
        success: function (json) {
            _tempOptions = Object.assign({}, json.data);

            var tempSelect2 = modalAddProjectUnit.find("#checklist_id").select2({
                data: json.data,
                placeholder: {
                    id: 0,
                    text: "Select an option"
                },
                dropdownParent: modalAddProjectUnit,
                width: '100%'
            });

            tempSelect2.on("change", function (e) {
                var self = $(e.target);
                self.validate();
            });
        }
    });
}

var _tempOptions = {};
var vmCheckTemplate = new Vue({
    el: "#select2template",
    data: { options: _tempOptions }
});

jQuery(document).ready(function () {
    getSelect2TemplateData();
    validateProjectUnitData();
});