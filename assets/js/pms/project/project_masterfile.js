var tableProjectList = $("#table-project");
var modalAddProject = $("#modal-add_project");
var modalUpdateProject = $("#modal-update_project");
var modalDeleteProject = $("#modal-delete_project");

if (typeof tableProjectList !== "undefined") {
    var search_val = "";
    var dtProject = tableProjectList.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("pms/project/get_project_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                return d;
            }
        },
        columns: [
            { data: "project_code", width: "15%" },
            { data: "company", width: "30%" },
            { data: "location", width: "35%" },
            { data: "is_active", width: "8%", className: "text-center" },
            { data: null, width: "12%", className: "text-center" }
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
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnView btnViewProjectUnit' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='View Development Site Units' data-id='" + $id + "'><i class='la la-cubes'></i></button>";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditProject' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='Edit Development Site' data-id='" + $id + "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveProject btnDelete' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='Archive Development Site' data-id='" +
                $id +
                "'><i class='la la-file-archive-o'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
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
        dtProject.ajax.reload(null, false);
    });

    $(document).on("click", ".btnViewProjectUnit", function () {
        var dataId = $(this).data("id");
        window.location.href = baseUrl("pms/project/units/"+dataId);
    });

    $(document).on("click", ".btnEditProject", function () {
        var dataId = $(this).data("id");
        $.ajax({
            url: baseUrl("pms/project/get_project_data/"+dataId),
            dataType: "json",
            success: function(json){
                if(json.response){
                    vmProjectData.row = Object.assign({}, json.row);
                    vmProjectData.$mount();
                    modalUpdateProject.modal("show");
                    validateProjectkDataUpdate();
                }
            }
        });
    });
    $(document).on("click", ".btnRemoveProject", function () {
        var dataId = $(this).data("id");
        $.ajax({
            url: baseUrl("pms/project/get_project_data/"+dataId),
            dataType: "json",
            success: function(json){
                if(json.response){
                    vmArchiveProjectData.row = Object.assign({}, json.row);
                    modalDeleteProject.modal("show");
                }
            }
        });
    });
}

var validateProjectkData = function () {
    $.validate({
        form: "#frmAddProject",
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
                            "Project data has been added.",
                            { timeOut: 5000 }
                        );
                        currentForm.reset();
                        if (typeof modalAddProject !== "undefined") {
                            modalAddProject.modal("hide");
                        }
                        dtProject.ajax.reload();
                    } else {
                        toastr.error(
                            json.toastr_msg,
                            "Error adding project data!",
                            { timeOut: 5000 }
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

    if(typeof modalAddProject !== "undefined"){
        var select2ProjectName = modalAddProject.find("select#company_id").select2({
            width: "100%",
            placeholder: "Select an option",
            dropdownParent: modalAddProject,
            ajax: {
                url: baseUrl("pms/project/get_project_name_select2_data"),
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return data;
                }
            }
        });
        
        var select2ProjectLocation = modalAddProject.find("select#location_id").select2({
            width: "100%",
            placeholder: "Select an option",
            dropdownParent: modalAddProject,
            ajax: {
                url: baseUrl("pms/project/get_project_location_select2_data"),
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return data;
                }
            }
        });
    }
}

var validateProjectkDataUpdate = function () {
    $.validate({
        form: "#frmUpdateProject",
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
                            "Project data has been updated.",
                            { timeOut: 5000 }
                        );
                        currentForm.reset();
                        if (typeof modalUpdateProject !== "undefined") {
                            modalUpdateProject.modal("hide");
                        }
                        dtProject.ajax.reload();
                    } else {
                        toastr.error(
                            json.toastr_msg,
                            "Error adding project data!",
                            { timeOut: 5000 }
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

    if(typeof modalUpdateProject !== "undefined"){
        var select2ProjectName = modalUpdateProject.find("select#company_id").select2({
            width: "100%",
            placeholder: "Select an option",
            dropdownParent: modalUpdateProject,
            ajax: {
                url: baseUrl("pms/project/get_project_name_select2_data"),
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return data;
                }
            }
        });
        
        var select2ProjectLocation = modalUpdateProject.find("select#location_id").select2({
            width: "100%",
            placeholder: "Select an option",
            dropdownParent: modalUpdateProject,
            ajax: {
                url: baseUrl("pms/project/get_project_location_select2_data"),
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return data;
                }
            }
        });
    }
}

var vmArchiveProjectData = new Vue({
    el: "#frmDeleteProject",
    data: { row: {} }
});

$(document).on("submit", "#frmDeleteProject", function(e){
    e.preventDefault();

    var currentTarget = e.target;
    var currentForm = currentTarget;
    var formUrl = currentForm.action;
    var formType = currentForm.method;
    var formData = $(currentForm).serialize();

    $.ajax({
        url: formUrl,
        type: formType,
        dataType: "json",
        data: formData,
        beforeSend: function () {
            $(currentForm)
                .find(".btn-submit")
                .addClass("m-btn--custom m-loader m-loader--light m-loader--right")
                .prop("disabled", true);
        },
        success: function(json){
            if (json.response) {
                toastr.success(
                    json.toastr_msg,
                    "Development site archived.",
                    { timeOut: 5000 }
                );
                currentForm.reset();
                if (typeof modalDeleteProject !== "undefined") {
                    modalDeleteProject.modal("hide");
                }
                dtProject.ajax.reload();
            } else {
                toastr.error(
                    json.toastr_msg,
                    "Error archiving development site data!",
                    { timeOut: 5000 }
                );
            }

            $(currentForm)
                .find(".btn-submit")
                .removeClass(
                    "m-btn--custom m-loader m-loader--light m-loader--right"
                ).prop("disabled", true);

        }
    });
});

var vmProjectData = new Vue({
    el: "#frmUpdateProject",
    data: { row: {} },
    methods: {
        renderCompany: function(){
            var vmTemp = this;
            var tempRow = vmTemp.row;
            var vmElement = vmTemp.$el;
            var currentTarget = $(vmElement).find("select#company_id");
            if(typeof tempRow.company_id !== "undefined" && tempRow.company_name !== "undefined"){
                var tempOption = new Option(tempRow.company_name, tempRow.company_id, false, true);
                currentTarget.append(tempOption).trigger("change");
            }
        },
        renderLocation: function(){
            var vmTemp = this;
            var tempRow = vmTemp.row;
            var vmElement = vmTemp.$el;
            var currentTarget = $(vmElement).find("select#location_id");
            if(typeof tempRow.location_id !== "undefined" && tempRow.location_name !== "undefined"){
                var tempOption = new Option(tempRow.location_name, tempRow.location_id, false, true);
                currentTarget.append(tempOption).trigger("change");
            }
        },
    }, mounted: function(){
        var vmTemp = this;
        setTimeout(function(){
            vmTemp.renderCompany();
            vmTemp.renderLocation();
        }, 200);
    }
});

jQuery(document).ready(function(){
    validateProjectkData();
});
