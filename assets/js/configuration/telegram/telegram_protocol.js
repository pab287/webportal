

let telegramProtocolTable = $("#table-telegram-protocol");
let dtTableProtocol;
let _owners = [];
let _module =[];
var modalEditProtocol = $("#edit_modal");
let modalNewProtocol = $("#new_modal");
if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.owner !== "undefined" && _tempContentData.owner.length > 0){ _owners = _tempContentData.owner; }
    if(typeof _tempContentData.module !== "undefined" && _tempContentData.module.length > 0){ _module = _tempContentData.module; }
}

let search_val = "";
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    dtTableProtocol.ajax.reload();
});

// $(document).ready(function() {
//     console.log("Hello WOrld");
//     dtTableProtocol.ajax.reload();
// });

dtTableProtocol = telegramProtocolTable.DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("configuration/telegram_protocol_datatable_request"),
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
        { data: "id", visible: false },
        { data: "bot_name"},
        { data: "bot_description"},
        {
            data: "owner",
            title: "Owner",
            render: function(data) {
                if (!Array.isArray(data) || !data.length) {
                    return '<span class="text-muted">No owner assigned</span>';
                }
        
                const owner = data[0];
                return `
                    <div class="owner-info">
                        ${owner.firstname} ${owner.lastname}
                        ${owner.middlename ? `${owner.middlename}<br>` : ''}
                    </div>
                `;
            }
        },
        { data: "status", className: "text-center", 
            render: function (data) {
                return renderStatus(data)
            }
        },
        { 
            data: "modules",
            render: function(data) {
                if (!Array.isArray(data)) return '';
                
                const labels = data.map(module => module.label);
                return `
                    <div class="module-tags">
                        ${labels.map(label => `<span>${label}</span>`).join(', ')}
                    </div>`;
            }
        },
        { data: "chat_id"},
        { data: "telegram_bot_token"},
        { data: "created_at"},
        { data: null, className: "text-center" },
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                var tempHtml = "---";
				var tempActions = [];
				var currentActions = ["edit", "delete", "connect", "exclude"];
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
								var tempLabel = "Deactivate";
								var tempIconClass = "la la-unlink";
								var tempEvent = 'toggle_connect_modal('+row.id +','+ row.is_connected +',\"'+ row.sms_ip+'\")';
								if(row.is_connected == 0){
									tempLabel = "Activate";
									tempIconClass = "la la-link";
								}
								tempHtml += `<div class='dropdown-divider'></div>`;
								tempHtml += `<a class="dropdown-item " href="javascript:void(0);" onclick='`+tempEvent+`'><i class="`+tempIconClass+`"></i> `+tempLabel+`</a>`;
							break;
                            case "exclude":
                                if(row.role == 1){
                                    var tempLabel = "Include (Admin only)";
                                    var tempIconClass = "la la-unlink";
                                    var tempEvent = 'toggle_exclude_modal('+row.id +','+ row.exclude +',\"'+ row.sms_ip+'\")';
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
};






var edit_protocol = function (id) {
    if (id) {
        $.ajax({
            url: siteUrl("configuration/get_telegram_bot_by_id/" + id),
            dataType: "json",
            success: function (json) {
                vmEditModal.row = Object.assign({}, json.response);
                initializeSelect2Elements(vmEditModal.row);
            }
        });
    } else {
        return false;
    }
};

var vmEditModal = new Vue({
    el: "#edit_modal",
    data: { row: {} }
});


$("#select2_owner").select2({
    placeholder: 'SELECT AN OPTION',
    dropdownParent: $("#new_modal"),
    width: '100%',
    data: _owners,
});

$("#select2_module").select2({
    placeholder: 'SELECT AN OPTION',
    dropdownParent: $("#new_modal"),
    width: '100%',
    data: _module,
});

let initializeSelect2Elements = (rowData) => {
    $("#select2_owner_edit").select2({
        placeholder: 'SELECT AN OPTION',
        dropdownParent: $("#edit_modal"),
        width: '100%',
        data: _owners,
    }).val(rowData.owner_id).trigger('change');

    $("#select2_module_edit").select2({
        placeholder: 'SELECT AN OPTION',
        dropdownParent: $("#edit_modal"),
        width: '100%',
        data: _module,
    }).val(rowData.modules).trigger('change');
};

$.validate({
    form: "#edit_form_telegram",
    lang: 'en',
    onSuccess: function (form) {
        let currentForm = form[0];
        let formData = $(currentForm).serialize();
        let url = siteUrl("configuration/update_telegram_protocol_settings");
        $.ajax({
            url: url,
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
                    currentForm.reset();
                    dtTableProtocol.ajax.reload();
                    $('#edit_modal').modal("hide");
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
    form: "#new_form",
    lang: 'en',
    onSuccess: function (form) {
        var currentForm = form[0];
        var formUrl = currentForm.action;
        var formData = $(currentForm).serialize();
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