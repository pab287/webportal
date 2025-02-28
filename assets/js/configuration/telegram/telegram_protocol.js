

let telegramProtocolTable = $("#table-telegram-protocol");
let telegramProtocolArchiveTable = $("#table-telegram-protocol-archive");
let dtTableProtocol;
let dtTableProtocolArchive;
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

dtTableProtocolArchive = telegramProtocolArchiveTable.DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    searching: false,
    ajax: {
        url: baseUrl("configuration/telegram_protocol_datatable_request"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
            d.archive = 1;
            return d;
        }
    },
    columns: [
        { data: "id", visible: false },
        { data: "bot_name"},
        { data: "bot_description", orderable: false},
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
            data: "modules", orderable: false,
            render: function(data) {
                if (!Array.isArray(data)) return '';
                
                const labels = data.map(module => module.label);
                return `
                    <div class="module-tags">
                        ${labels.map(label => `<span>${label}</span>`).join(', ')}
                    </div>`;
            }
        },
        { data: "chat_id", orderable: false},
        { data: "telegram_bot_token",orderable: false},
        {
            data: "created_at",
            render: function(data) {
                const dateStr = data.trim();
                const dateParts = dateStr.split(' ');
                const dateComponents = dateParts[0].split('-');
                
                // Create Date object for proper month formatting
                const dateObj = new Date(dateComponents[0], parseInt(dateComponents[1])-1, dateComponents[2]);
                
                // Format the date parts
                const month = dateObj.toLocaleString('default', { month: 'long' });
                const day = dateComponents[2];
                const year = dateComponents[0];
        
                return `${month} ${day}, ${year}`;
            }
        },
        { data: null, className: "text-center" },
    ],
    columnDefs: [{
        data: null,
        defaultContent: "",
        targets: -1,
        orderable: false,
        render: function(data, type, row, meta) {
            return `
                <button type="button" class="btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRestore" onclick="restoreTelegramBot(${row.id})"
                        data-toggle="m-tooltip" 
                        data-placement="bottom" 
                        data-skin="dark" 
                        data-original-title="Restore Telegram Bot" 
                        data-delay="{\"show\": 300}">
                    <i class="la la-reply"></i>
                </button>
            `;
        }
    }]
});

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
            d.archive = 0;
            return d;
        }
    },
    searching: false,
    columns: [
        { data: "id", visible: false },
        { data: "bot_name"},
        { data: "bot_description", orderable: false},
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
            data: "modules", orderable: false,
            render: function(data) {
                if (!Array.isArray(data)) return '';
                
                const labels = data.map(module => module.label);
                return `
                    <div class="module-tags">
                        ${labels.map(label => `<span>${label}</span>`).join(', ')}
                    </div>`;
            }
        },
        { data: "chat_id", orderable: false},
        { data: "telegram_bot_token",orderable: false},
        {
            data: "created_at",
            render: function(data) {
                const dateStr = data.trim();
                const dateParts = dateStr.split(' ');
                const dateComponents = dateParts[0].split('-');
                
                // Create Date object for proper month formatting
                const dateObj = new Date(dateComponents[0], parseInt(dateComponents[1])-1, dateComponents[2]);
                
                // Format the date parts
                const month = dateObj.toLocaleString('default', { month: 'long' });
                const day = dateComponents[2];
                const year = dateComponents[0];
        
                return `${month} ${day}, ${year}`;
            }
        },
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
							tempHtml += `<a class="dropdown-item btnEdit" data-toggle='modal' data-target='#edit_modal' href="javascript:void(0);" onclick='edit_protocol(`+row.id+`)'><i class="la la-edit"></i> Edit</a>`;
							break;
							case "delete":
							tempHtml += `<a class="dropdown-item btnArchive" data-toggle='modal' data-target='#delete_modal' href="javascript:void(0);" onclick='delete_telegram_bot(`+row.id+`)'><i class="la la-trash"></i> Remove</a>`;
							break;
							case "connect":
								var tempLabel = "Deactivate";
								var tempIconClass = "la la-unlink";
								if(row.status == 0){
									tempLabel = "Activate";
									tempIconClass = "la la-link";
								}
								tempHtml += `<div class='dropdown-divider'></div>`;
								tempHtml += `<a class="dropdown-item " href="javascript:void(0);" onclick='toggle_connect_modal(`+row.id+`,`+ row.status +`)'><i class="`+tempIconClass+`"></i> `+tempLabel+`</a>`;
							break;
                            // case "exclude":
                            //     if(row.role == 1){
                            //         var tempLabel = "Include (Admin only)";
                            //         var tempIconClass = "la la-unlink";
                            //         var tempEvent = 'toggle_exclude_modal('+row.id +','+ row.exclude'")';
                            //         if(row.exclude == 0){
                            //             tempLabel = "Exclude (Admin only)";
                            //             tempIconClass = "la la-link";
                            //         }
                            //         tempHtml += `<div class='dropdown-divider'></div>`;
                            //         tempHtml += `<a class="dropdown-item " href="javascript:void(0);" onclick='`+tempEvent+`'><i class="`+tempIconClass+`"></i> `+tempLabel+`</a>`;
                            //     }
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
        let url = siteUrl("configuration/update_telegram_protocol_settings");
        const select2Values = $("#select2_module_edit").val();
        const select2Data = $("#select2_module_edit").select2('data');
        const combinedData = select2Values.map((id, index) => {
            return select2Data[index].text;
        });
        const combinedDataString = JSON.stringify(combinedData);
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: {
                csrf_token: _csrf_hash,
                id: $("#id").val(),
                bot_name: $("input[id='bot_name_edit']").val(),
                bot_description: $("input[id='bot_description_edit']").val(),
                owner_id: $("#select2_owner_edit").val(),
                chat_id: $("input[id='chat_id_edit']").val(),
                telegram_bot_token: $("input[id='telegram_bot_token_edit']").val(),
                modules_array: combinedDataString,
                modules: select2Values,
            },
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
        let currentForm = form[0];
        let formUrl = currentForm.action;
        const select2Values = $("#select2_module").val();
        const select2Data = $("#select2_module").select2('data');
        const combinedData = select2Values.map((id, index) => {
            return select2Data[index].text;
        });
        const combinedDataString = JSON.stringify(combinedData);
        $.ajax({
            url: formUrl,
            type: "POST",
            dataType: "json",
            data: {
                csrf_token: _csrf_hash,
                bot_name: $("input[name='bot_name']").val(),
                bot_description: $("input[name='bot_description']").val(),
                owner_id: $("#select2_owner").val(),
                chat_id: $("input[name='chat_id']").val(),
                telegram_bot_token: $("input[name='telegram_bot_token']").val(),
                modules_array: combinedDataString,
                modules: select2Values,
            },
            beforeSend: function () {
                $(form[0])
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                if (json.response) {
                    currentForm.reset();
                    $("#select2_owner").val(null).trigger('change');
                    $("#select2_module").val(null).trigger('change');
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

function delete_telegram_bot(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        reverseButtons: true,
        position: 'top',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: siteUrl("configuration/delete_telegram_bot/" + id),
                type: "POST", // Ensure the request is POST
                dataType: "json",
                data: {
                    csrf_token: _csrf_hash
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(json.message, "Protocol Settings", 5000);
                    } else {
                        toastr.error(json.message, "Protocol Settings", 5000);
                    }
                    dtTableProtocol.ajax.reload();
                },
                error: function (xhr, status, error) {
                    toastr.error("An error occurred while deleting the Telegram bot.", "Protocol Settings", 5000);
                }
            });
        }
    });
}
// function toggle_connect_modal(id,is_connected){
//     console.log(id,is_connected);
// }

// function toggle_connect_modal(id,is_connected){
//     $.ajax({
//         url: siteUrl("configuration/toggle_telegram_bot_status/"+id),
//         type: "POST", 
//         dataType: "json",
//         data: {
//             csrf_token: _csrf_hash,
//             status: is_connected
//         },
//         success: function (json) {
//             if (json.response) {
//                 toastr.success(json.toastr_msg, "Protocol Settings", 5000);
//                 dtTableProtocol.ajax.reload();
//             } else {
//                 toastr.error(json.toastr_msg, "Protocol Settings", 5000);
//             }
//         }
//     });
// }

function toggle_connect_modal(id, is_connected) {
    Swal.fire({
        title: is_connected ? 'Deactivate Bot' : 'Activate Bot',
        text: `Are you sure you want to ${is_connected ? 'deactivate' : 'activate'} this bot?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed',
        confirmButtonColor: '#3085d6',
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#d33',
        reverseButtons: true,
        position: 'top',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: siteUrl("configuration/toggle_telegram_bot_status/" + id),
                type: "POST",
                dataType: "json",
                data: {
                    csrf_token: _csrf_hash,
                    status: is_connected
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(json.message, "Protocol Settings", 5000);
                        dtTableProtocol.ajax.reload();
                    } else {
                        toastr.error(json.message, "Protocol Settings", 5000);
                    }
                }
            });
        }
    });
}

function restoreTelegramBot(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to restore this Telegram bot. This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, restore it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        position: 'top',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: siteUrl("configuration/restore_telegram_bot/" + id),
                type: "POST",
                dataType: "json",
                data: {
                    csrf_token: _csrf_hash, 
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(json.message, "Protocol Settings", 5000);
                        dtTableProtocolArchive.ajax.reload();
                    } else {
                        toastr.error(json.message, "Protocol Settings", 5000);
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error("An error occurred while restoring the Telegram bot.", "Protocol Settings", 5000);
                }
            });
        }
    });
}