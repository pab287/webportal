var search_val = "";
var query_builder = "";
var tblAccounts = $("#table-accounts").DataTable({
   dom: '<"toolbar">rtlip',
   serverSide: true,
   processing: true,
   aaSorting: [],
   ajax: {
        url: baseUrl("eforms/billing/get_datatable_request_disconnection/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function(d){
           d.csrf_token = _csrf_hash,
           d.search['value'] = search_val,
           d.query_builder = query_builder
       }
   },
   searching: true,
   columns: [
        {
          width: '1%',
          orderable: false,
          data: null,
          className: 'text-center',
          render: function (data, type, row) {
              return `<label class="m-checkbox m-checkbox--air m-checkbox--state-primary" title='Check to Print'> <input id="chckBoxPayment" type="checkbox" class="text-gray chckBox" value="`+row.id+`" name="selected"><span></span></label>`;
          }
        },
        { data: "name", width: "10%", render: function (data) {
                return "<strong style='color: #525252;'>"+data+"</strong>";
            }
        },
        { data: "accountno", width:"2%", className: "text-center"},
        { data: "meterno", width:"2%", className: "text-center"},
        { data: "subdivision", width:"5%", className: "text-center"},
        { data: "model", width:"2%", className: "text-center"},
        { data: "street", width:"20%"},
        { data: "balance", width:"2%"},
        { data: "block", width:"2%"},
        { data: "lot", width:"2%"},
        { data: "is_disconnected", width:"2%", className: "text-center", render: function (data) {
                  return renderWaterConnection(data)
              }
          },
        { data: "status", width:"5%", className: "text-center", render: function (data) {
                  return renderStatus(data)
              }
        },
        { data: null, width: "2%", className: "text-center"},
   ],
   columnDefs: [
       { targets: [0]},       
       {
           data: null,
           defaultContent: "",
           targets: -1,
           orderable: false,
         
           render: function ( data, type, row, meta ) { return itemDatatableActions(row); },
       }
   ],buttons: [
       { 
           extend: 'csv',
           exportOptions: {
               columns: "thead th:not(.notExport)"
           }
       }, { 
           extend: 'excel',
           exportOptions: {
               columns: "thead th:not(.notExport)"
           }
       }, { 
           extend: 'pdf',
           exportOptions: {
               columns: "thead th:not(.notExport)"
           }
       }
   ]
});

$("#cb-select-all").click(function () {
  $('#table-accounts tbody input[type="checkbox"]').prop('checked', this.checked);
});

$("#table-accounts").on("click", "tbody input[type='checkbox']", function () {
  const allCheckboxes = $("#table-accounts tbody input[type='checkbox']").length;
  const checkedCheckboxes = $("#table-accounts tbody input[type='checkbox']:checked").length;
  const checked = allCheckboxes <= checkedCheckboxes;
  $('#cb-select-all').prop('checked', checked);
});

let overdue_accounts = [];
function disconnectModal(){
  $(".chckBox").each(function(){
    var trig = $(this).is(":checked");
    if(trig){
      overdue_accounts.push($(this).attr("value"));
    }
  });
  if(overdue_accounts.length > 0){
    $("#modal_disconnect").modal('show');
  }else{
    toastr.error("Please select an account.", "No account selected.");
  }
}

function disconnectSelect(){
  $.ajax({
      url: baseUrl("eforms/billing/disconnect_selected"),
      type: "POST",
      data:{overdue_accounts: overdue_accounts, csrf_token: _csrf_hash},
      success: function(response){
        let html = "";
        $.each(response, function (key, value) {
          html += value.toUpperCase()+"<br>";
        });
        toastr.success(html, "Water disconnected on the ff:");
        $("#modal_disconnect").modal('hide');
        tblAccounts.ajax.reload();
        overdue_accounts = [];
      },
      error: function (request, status, error) {
        toastr.error("Error disconnecting water.", "Try again later.");
      }
  });
}

// $(document).ready(function () {
//     $('#query-builder').queryBuilder({
//         'bt-tooltip-errors': { delay: 100 },
//         filters: [
//             { id: 'a.id', label: 'ID #', type: 'integer' },
//             { id: 'a.accountno', label: 'Account #', type: 'string' },
//             { id: 'a.meterno_raw', label: 'Meter #', type: 'integer' },
//             { id: 'b.name', label: 'Subdivision', type: 'string' },
//             { id: 'a.model', label: 'House Model', type: 'string' },
//             { id: 'a.street', label: 'Street', type: 'string' },
//             { id: 'a.block', label: 'Block', type: 'integer' },
//             { id: 'a.lot', label: 'Lot', type: 'integer' },
//             { id: 'a.firstname', label: 'Firstname', type: 'string' },
//             { id: 'a.middlename', label: 'Middlename', type: 'string' },
//             { id: 'a.lastname', label: 'Lastname', type: 'string' },
//             {
//                 id: 'a.is_disconnected',
//                 label: 'Water Connection',
//                 type: 'string',
//                 input: 'select',
//                 plugin: 'select2',
//                 plugin_config: {
//                     placeholder: 'Select. .',
//                     width: '110%',
//                     data: [
//                         {
//                             id: "0",
//                             text: "Connected"
//                         }, {
//                             id: "1",
//                             text: "Disconnected"
//                         }
//                     ]
//                 },
//                 operators: ['equal', 'not_equal']
//             },
//             {
//                 id: 'a.status',
//                 label: 'Status',
//                 type: 'string',
//                 input: 'select',
//                 plugin: 'select2',
//                 plugin_config: {
//                     placeholder: 'Select. .',
//                     width: '110%',
//                     data: [
//                         {
//                             id: "Pending",
//                             text: "Pending"
//                         }, {
//                             id: "Approved",
//                             text: "Approved"
//                         }, {
//                             id: "Disapproved",
//                             text: "Disapproved"
//                         }, {
//                             id: "HR Noted",
//                             text: "HR Noted"
//                         }
//                     ]
//                 },
//                 operators: ['equal', 'not_equal']
//             },
//         ],
//     });

//     $("#query-builder_group_0").addClass("col-12");

//     $('#query-builder-btn').on('click', function () {
//         var result = $('#query-builder').queryBuilder('getSQL');
    
//         if (!$.isEmptyObject(result)) {
//             query_builder = result;
//             tblAccounts.ajax.reload();
//             $("#modal-query-builder").modal("hide");
//         }
//     });
// });

// function clear_query_builder() {
//     $('#query-builder').queryBuilder('reset');
//     query_builder = null;
//     tblAccounts.ajax.reload();
// }

function renderWaterConnection(data) {
    switch (data) {
        case "0":
            return '<div class="m-badge text-white m-badge--info m-badge--wide" role="alert"><strong>Connected</strong></div>';
        default:
            return '<div class="m-badge m-badge--danger m-badge--wide" role="alert"><strong>Disconnected</strong></div>';
    }
}

function renderStatus(data) {
    switch (data) {
        case "Active":
            return '<div class="m-badge text-white m-badge--success m-badge--wide" role="alert"><strong>Active</strong></div>';
        case "Inactive":
            return '<div class="m-badge text-white m-badge--warning m-badge--wide" role="alert"><strong>Inactive</strong></div>';
        default:
            return '<div class="m-badge m-badge--default m-badge--wide" role="alert"><strong>Archive</strong></div>';
    }
}

function itemDatatableActions(row){
	if(row){

        var tempHtml = "---";
        var tempActions = [];
        var currentActions = ["edit", "replace_meter"];
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
                    tempHtml += `<a class="dropdown-item" href="javascript:void(0);" onclick='redirectTo(` + row.id + `)'><i class="la la-edit"></i> Edit</a>`;
                    break;
                    case "replace_meter":
                    tempHtml += `<a class="dropdown-item " data-toggle='modal' data-target='#delete_modal' href="javascript:void(0);" onclick='showMeterReplaceModal(`+ row.id +`,\"`+ row.meterno +`\",`+`\"` + row.name + `\")'><i class="la la-tachometer"></i> Meter Replacement</a>`;
                    break;
                }
            });
            tempHtml += `</div></div>`;
        
        return tempHtml;

	}else{ return false; }
}

var _search_val = "";
var _query_builder = "";
function displayAccountsReadings(){
    var account_id = document.getElementById('account_id').value;
    
    $("#table-readings").DataTable({
        dom: '<"toolbar">rtlip',
        destroy: true,
        serverSide: true,
        processing: true,
        aaSorting: [],
        ajax: {
             url: baseUrl("eforms/billing/get_reading_accounts/"),
             type: "post",
             global: false,
             dataType: "json",
             data: function(d){
                d.csrf_token = _csrf_hash,
                d.search['value'] = _search_val,
                d.query_builder = _query_builder,
                d.account_id = account_id;
            },
            error: function (xhr, error, code){
                toastr.error("Failed to load list of reading", "Connection error");
                // toggle_switch_ui(false);
            }
        },
        searching: true,
        columns: [
             {
               width: '2%',
               orderable: false,
               data: null,
               className: 'text-center',
               render: function (data, type, row) {
                   return `<label class="m-checkbox m-checkbox--air m-checkbox--state-primary" title='Check to Print'> <input id="selectedReading" type="checkbox" class="text-gray chckBox" value="`+row.id+`" name="selected"><span></span></label>`;
               }
             },
            { data: "ref_no", render: function (data) {
                   return "<strong style='color: #525252;'>"+data+"</strong>";
                 }
             },
            { data: "meterno"},
            { data: "reading"},
            { data: "reading_date"},
            { data: "status", className: "text-center", render: function (data) {
                return renderReadingStatus(data);
              }
            },
        ],
        buttons: [
            { 
                extend: 'csv',
                exportOptions: {
                    columns: "thead th:not(.notExport)"
                }
            }, { 
                extend: 'excel',
                exportOptions: {
                    columns: "thead th:not(.notExport)"
                }
            }, { 
                extend: 'pdf',
                exportOptions: {
                    columns: "thead th:not(.notExport)"
                }
            }
        ]
     });
}

$("#cb-select-all").click(function () {
    $('#table-readings tbody input[type="checkbox"]').prop('checked', this.checked);
    setCheckbox();
});

$("#table-readings").on("click", "tbody input[type='checkbox']", function () {
    const allCheckboxes = $("#table-readings tbody input[type='checkbox']").length;
    const checkedCheckboxes = $("#table-readings tbody input[type='checkbox']:checked").length;
    const checked = allCheckboxes <= checkedCheckboxes;
    $('#cb-select-all').prop('checked', checked);
    setCheckbox();
});

function setCheckbox(){
    var selectedReading = [];
    $(".chckBox").each(function(){
       var trig = $(this).is(":checked");
       if(trig){
         selectedReading.push($(this).attr("value"));
       }
   });
   $("#m_meter_r input[name=selectedReading]").val(JSON.stringify(selectedReading));
}

// toggle_switch_ui(false);
// $("#toggle_switch").click(function () {
//     toggle_switch_ui($(this).is(":checked"));
// });

// function toggle_switch_ui(isChecked){
//     $("#m_meter_r input[name=selectedReading]").val("");
    
//     if(isChecked){
//         displayAccountsReadings();
//         $("#readings_table").fadeIn();
//         $("#toggle_title").empty().html("Disable Update Readings");
//         document.getElementById("toggle_switch").checked = true;
//         document.getElementById("m_meter_r_2").style.minWidth = "85%";
//     } else {
//         $("#readings_table").hide();
//         $("#toggle_title").empty().html("Enable Update Readings");
//         document.getElementById("toggle_switch").checked = false;
//         document.getElementById("m_meter_r_2").style.minWidth = "";
//     }
// }

function renderReadingStatus(data) {
    switch (data) {
        case "1":
            return '<div class="m-badge text-white m-badge--accent m-badge--wide" role="alert"><strong>Billed</strong></div>';
            break;
        default:
            return '<div class="m-badge text-white m-badge--warning m-badge--wide" role="alert"><strong>Unbilled</strong></div>';
            break;
    }
}

function redirectTo(id) {
    if (id) {
        window.open(baseUrl('eforms/billing/edit_account/') + id, "_blank");
    } else {
        return false;
    }
}

$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    tblAccounts.ajax.reload();
});

$("#ExportExcel").on("click", function() {
    tblAccounts.button( '.buttons-excel' ).trigger();
    saveExportLogs('Accounts - Export Excel');
});

$("#ExportCSV").on("click", function() {
    tblAccounts.button( '.buttons-csv' ).trigger();
    saveExportLogs('Accounts - Export CSV');
});

$("#ExportPDF").on("click", function() {
    tblAccounts.button( '.buttons-pdf' ).trigger();
    saveExportLogs('Accounts - Export PDF');
});

function saveExportLogs(export_){
    $.ajax({
        url: baseUrl("eforms/billing/save_export_logs"),
        type: 'post',
        data: { csrf_token: _csrf_hash, export_: export_ },
        success: function (data) {
            
        }
    });
}

function showMeterReplaceModal(id,meter,name){
    $("#m_meter_r #account_name").val(name);
    $("#m_meter_r input[name=old_meterno]").val(meter);
    $("#m_meter_r input[name=account_id]").val(id);
    $('#m_meter_r input[name=new_meterno]').attr('placeholder', meter);
    $("#m_meter_r").modal("show");
}

$('#m_meter_r').on('hidden.bs.modal', function () {
    $('#frm_meter_r').trigger("reset");
    toggle_switch_ui(false);
});

$.validate({
    form : '#frm_meter_r',
    lang: 'en',
    onSuccess : function(form) {
        $.ajax({
            url : $(form).attr("action"),
            type: "POST",
            data: $('#frm_meter_r').serialize(),
            dataType: "JSON",
            success: function(data){
                if(data.status){
                    $("#m_meter_r input[name=old_meterno]").val(data.new_meterno);
                    $("#m_meter_r input[name=new_meterno]").val("");
                    $("#m_meter_r input[name=selectedReading]").val("");
                    $("#m_meter_r textarea[name=remarks]").val("");
                    $("#m_meter_r").modal("hide");
                    tblAccounts.ajax.reload();
                    toastr.success(data.msg, "Notification");
                }else{
                    toastr.warning(data.msg, "Notification");
                }
            },
            error: function(data){
                toastr.error("Please check your internet connection.", "Connection error");
            }
        });
        return false;
    },
});