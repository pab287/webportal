let dtProtocol = $("#table-sms-protocol");
let dtTableProtocol;
let search_val = "";
let modalNewProtocol = $("#new_modal");
let modalEditProtocol = $("#edit_modal");
let modalRemoveProtocol = $("#delete_modal");
let _departments = [];
if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.department !== "undefined" && _tempContentData.department.length > 0){ _departments = _tempContentData.department; }
}

$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    dtTableProtocol.ajax.reload();
});

dtTableProtocol = dtProtocol.DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("configuration/sms_protocol_datatable_request"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
            return d;
        }
    },
    searching: false,
    columns: [
        { data: "sms_ip", width: "5%", orderable: false },
        { data: "sms_port", width: "5%", orderable: false },
        { data: "sms_user", width: "5%", orderable: false },
        { data: "modem", width: "5%", orderable: false },
        { data: "department", width: "25%", orderable: false },
        { data: "is_connected", width: "3%", orderable: false, className: "text-center", render: function (data) {
                return renderStatus(data)
            }
        },
        { data: null, width: "3%", className: "text-center" },
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                let tempHtml = "---";
				let tempActions = [];
				let currentActions = ["edit", "delete", "connect", "exclude"];
				$.each(currentActions, function(index, value){
                    tempActions.push(value);
                });

                tempHtml = `<div class="dropdown">
						<a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown"> 
							<i class="la la-ellipsis-h"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right">`;
					$.each(tempActions, function(ii, vv){
						switch(vv){
							case "edit":
							tempHtml += `<a class="dropdown-item " data-toggle='modal' data-target='#edit_modal' href="javascript:void(0);" onclick='edit_protocol(`+row.id+`)'><i class="la la-edit"></i> Edit</a>`;
							break;
							case "delete":
							tempHtml += `<a class="dropdown-item " data-toggle='modal' data-target='#delete_modal' href="javascript:void(0);" onclick='delete_protocol(`+row.id+`)'><i class="la la-trash"></i> Remove</a>`;
							break;
							case "connect":
								let tempLabel = "Deactivate";
								let tempIconClass = "la la-unlink";
								let tempEvent = 'toggle_connect_modal('+row.id +','+ row.is_connected +',\"'+ row.sms_ip+'\")';
								if(row.is_connected == 0){
									tempLabel = "Activate";
									tempIconClass = "la la-link";
								}
								tempHtml += `<div class='dropdown-divider'></div>`;
								tempHtml += `<a class="dropdown-item " href="javascript:void(0);" onclick='`+tempEvent+`'><i class="`+tempIconClass+`"></i> `+tempLabel+`</a>`;
							break;
                            case "exclude":
                                if(row.role == 1){
                                    let tempLabel = "Include (Admin only)";
                                    let tempIconClass = "la la-unlink";
                                    let tempEvent = 'toggle_exclude_modal('+row.id +','+ row.exclude +',\"'+ row.sms_ip+'\")';
                                    if(row.exclude == 0){
                                        tempLabel = "Exclude (Admin only)";
                                        tempIconClass = "la la-link";
                                    }
                                    tempHtml += `<div class='dropdown-divider'></div>`;
                                    tempHtml += `<a class="dropdown-item " href="javascript:void(0);" onclick='`+tempEvent+`'><i class="`+tempIconClass+`"></i> `+tempLabel+`</a>`;
                                }
                            break;
						}
					});
					tempHtml += `</div></div>`;
                return tempHtml;
            },
        }
    ]
});

function renderStatus(data) {
    switch (data) {
        case "1":
            return '<a class="btn btn-success m-btn m-btn--icon m-btn--icon-only btn-sm"><i class="fa-lg fa fa-check" style="color:white"></i></a>';
            break;
        default:
            return '<a class="btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm"><i class="fa-lg fa fa-remove" style="color:white"></i></a>';
            break;
    }
}

function toggle_exclude_modal(id,exclude,sms_ip){
    $("#modal-exclude").modal("show");
    $('#modal-exclude #exclude_protocol_id').val(id);
    $('#modal-exclude #exclude').val(exclude);

    const temp = `<p>Are you sure you want to `+(exclude ? `include` : `exclude`)+` <strong class='m--font-boldest'>${sms_ip}</strong>?</p>`;
    $('#modal-exclude .modal-title').html(exclude ? "Include Protocol" : "Exclude Protocol");
    $('#modal-exclude .modal-body').html(temp);
}

function toggle_exclude(){
    let id = document.getElementById('exclude_protocol_id').value;
    let exclude = document.getElementById('exclude').value;
    $.ajax({
        url: baseUrl("configuration/sms_protocol_exclude"),
        type: 'post',
        data: { csrf_token: _csrf_hash, id: id, exclude:exclude },
        success: function (data) {
            if(data.status){
                $('#modal-exclude').modal('hide');
                dtTableProtocol.ajax.reload();
            }
        }
    });
}

function toggle_connect_modal(id,is_connected,sms_ip){
    $("#modal-connect").modal("show");
    $('#modal-connect #protocol_id').val(id);
    $('#modal-connect #is_connected').val(is_connected);

    const temp = `<p>Are you sure you want to `+(is_connected ? `disconnect` : `connect`)+` <strong class='m--font-boldest'>${sms_ip}</strong>?</p>`;
    $('#modal-connect .modal-title').html(is_connected ? "Disconnect Protocol" : "Connect Protocol");
    $('#modal-connect .modal-body').html(temp);
}

function toggle_connect(){
    let id = document.getElementById('protocol_id').value;
    let is_connected = document.getElementById('is_connected').value;
    $.ajax({
        url: baseUrl("configuration/sms_protocol_connect"),
        type: 'post',
        data: { csrf_token: _csrf_hash, id: id, is_connected:is_connected },
        success: function (data) {
            if(data.status){
                $('#modal-connect').modal('hide');
                dtTableProtocol.ajax.reload();
            }
        }
    });
}

let delete_protocol = function (id) {
    if (id) {
        return $.ajax({
            url: siteUrl("configuration/get_sms_protocol_by_id/" + id),
            dataType: "json",
            success: function (json) {
                if (json.response) {
                    vmDeleteModal.row = Object.assign({}, json.row);
                }
            }
        });
    } else {
        return false;
    }
}

let edit_protocol = function (id) {
    if (id) {
        return $.ajax({
            url: siteUrl("configuration/get_sms_protocol_by_id/" + id),
            dataType: "json",
            success: function (json) {
                if (json.response) {
                    vmEditModal.row = JSON.parse(JSON.stringify(json.row));
                    _departments_edit = json.departments_select;
                    $('#select2_dprtmnt_edit').empty() 
                    .select2({
                        placeholder: 'SELECT AN OPTION',
                        dropdownParent: $("#edit_modal"),
                        width: '100%',
                        multiple: true,
                        dataType: "json",
                        data: _departments_edit
                    });
                    let dpartment = json.row.department;
                    $('#select2_dprtmnt_edit').val(dpartment).trigger('change');
                }
            }
        });
    } else {
        return false;
    }
}

$.validate({
    form: "#new_form",
    lang: 'en',
    onSuccess: function (form) {
        let currentForm = form[0];
        let formUrl = currentForm.action;
        let formData = $(currentForm).serialize();
        $.ajax({
            url: formUrl,
            type: "POST",
            dataType: "json",
            data: formData,
            beforeSend: function () {
                $(form[0])
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                if (json.response) {
                    let selectedIds = $('#select2_dprtmnt').val() || [];

                    if (Array.isArray(_departments) && selectedIds.length > 0) {
                        _departments = _departments.filter(function (dept) {
                            return !selectedIds.includes(dept.id.toString());
                        });
                    }

                    $("#select2_dprtmnt").empty().select2({
                        placeholder: 'SELECT AN OPTION',
                        dropdownParent: $("#new_modal"),
                        width: '100%',
                        data: _departments,
                    });
                    currentForm.reset();
                    dtTableProtocol.ajax.reload();
                    modalNewProtocol.modal("hide");
                    toastr.success(json.toastr_msg, "Protocol Settings", 5000);
                } else {
                    toastr.error(json.toastr_msg, "Protocol Settings", 5000);
                }
                $(form[0])
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            }
        });
        return false;
    },
});

$.validate({
    form: "#edit_form",
    lang: 'en',
    onSuccess: function (form) {
        let currentForm = form[0];
        let formUrl = currentForm.action;
        let formData = $(currentForm).serialize();
        $.ajax({
            url: formUrl,
            type: "POST",
            dataType: "json",
            data: formData,
            beforeSend: function () {
                $(form[0])
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                if (json.response) {
                    _departments = json.departments_select || [];
                    $("#select2_dprtmnt").empty().select2({
                        placeholder: 'SELECT AN OPTION',
                        dropdownParent: $("#new_modal"),
                        width: '100%',
                        data: _departments,
                    });
                    currentForm.reset();
                    dtTableProtocol.ajax.reload();
                    modalEditProtocol.modal("hide");
                    toastr.success(json.toastr_msg, "Protocol Settings", 5000);
                } else {
                    toastr.error(json.toastr_msg, "Protocol Settings", 5000);
                }

                $(form[0])
                    .find(".btn-submit")
                    .removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });

        return false;
    },
});

$(document).on("submit", "#remove_form", function (e) {
    e.preventDefault();
    $.ajax({
        url: e.target.action,
        dataType: "json",
        type: e.target.method,
        data: $(e.target).serialize(),
        beforeSend: function () {
            $(e.target)
                .find(".btn-submit")
                .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }, success: function (json) {
            if (json.response) {
                dtTableProtocol.ajax.reload();
                modalRemoveProtocol.modal("hide");
            }

            $(e.target)
                .find(".btn-submit")
                .removeClass(
                    "m-btn--custom m-loader m-loader--light m-loader--right"
                );
        }
    });
    console.log(e.target);
    console.log(e.target.action);
});

let vmEditModal = new Vue({
    el: "#edit-modal_body",
    data: { row: {} },
});

let vmDeleteModal = new Vue({
    el: "#delete-modal_body",
    data: { row: {} }
});

$("#select2_dprtmnt").select2({
    placeholder: 'SELECT AN OPTION',
    dropdownParent: $("#new_modal"),
    width: '100%',
    data: _departments,
});