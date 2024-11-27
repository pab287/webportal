var search_val = "";
var query_builder = "";
var tblPayment = $("#table-payment").DataTable({
   dom: '<"toolbar">rtlip',
   serverSide: true,
   processing: true,
   aaSorting: [],
   ajax: {
        url: baseUrl("eforms/billing/get_billing_payment/"),
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
       { data: "payment_ref_no", width: "10%", render: function (data) {
                return "<strong style='color: #525252;'>"+data+"</strong>";
            }
        },
       { data: "account_name", width: "12%"},
       { data: "bill", width: "10%"},
       { data: "payment_type", className: "text-center", width: "5%"},
       { data: "due_date", className: "text-center", width: "8%"},
       { data: null, className: "text-right", width: "8%"},
       { data: "net_payment", className: "text-right", width: "8%"},
       { data: "received_amount", className: "text-right", width: "8%", render: function (data) {
                return "<strong style='color: #525252;'>"+numberWithCommas(data)+"</strong>";
            }
        },
       { data: "acknowledgement_receipt", className: "text-center", width: "8%"},
       { data: "payment_date", className: "text-center", width: "8%"},
       { data: null, width: "4%", className: "text-center"},
   ],
   columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function ( data, type, row, meta ) { 
              console.log(row.isArchiveHide);
              return itemDatatableActions(row); 
        },
    }, 
    // {
    //     data: null,
    //     defaultContent: "",
    //     targets: 5,
    //     orderable: false,
    //     render: function (data, type, row, meta) {
    //         var tempHtml = "";
    //         if(row.is_penalty == '1'){
    //             if(row.penalties.length > 1){
    //                 tempHtml += "<a href='javascript:void(0);' data-toggle='tooltip' data-placement='top' title='Archive' onclick='viewPenalty("+row.id+")'>see more</a>";
    //             } else {
    //                 var v = row.penalties[0];
    //                 tempHtml += v.dueDate;
    //             }
    //         } else {
    //             tempHtml += "None";
    //         }
    //         return tempHtml;
    //     },
    // }, 
    {
        data: null,
        defaultContent: "",
        targets: 6,
        orderable: false,
        render: function (data, type, row, meta) {
            var tempHtml = "";
            if(row.is_penalty == '1'){
                if(row.penalties.length > 1){
                    tempHtml += "<a href='javascript:void(0);' data-toggle='tooltip' data-placement='top' title='Archive' onclick='viewPenalty("+row.id+")'>see more</a>";
                } else {
                    var v = row.penalties[0];
                    var sum = parseFloat(v.overdue) + parseFloat(row.reconnection_fee);
                    tempHtml += '₱ ';
                    tempHtml += sum.toFixed(2);
                }
            } else {
                tempHtml += "None";
            }
            return tempHtml;
        },
    }
   ],
   select: {
    style:    'os',
    selector: 'td:first-child'
   },
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

Inputmask.extendAliases({
    pesos: {
              prefix: "₱ ",
              groupSeparator: ".",
              alias: "numeric",
              placeholder: "0",
              autoGroup: !0,
              digits: 2,
              digitsOptional: !1,
              clearMaskOnLostFocus: !1
          }
});

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function viewPenalty(id){
    if(id != undefined){
        $.ajax({
            url: baseUrl("eforms/billing/view_penalties"),
            type: 'post',
            data: { csrf_token: _csrf_hash, id: id },
            success: function (data) {
                var list = "", total = 0, penalties = data.penalties;
                for (var i = 0; i < penalties.length; i++) {
                    var overdue = penalties[i].overdue;
                    var total_amount = penalties[i].total_amount;
                    var dueDate = penalties[i].dueDate;
                    total = (parseFloat(total) + parseFloat(overdue));
                    list = list + 
                    "<div class='col-md'>"+
                    "<div class='row'>"+
                        "<div class='form-group form__group'><label><b>Due Date "+(i+1)+"</b></label><input value="+dueDate+" class='form-control m-input text-right' readonly type='text'></div>&nbsp;&nbsp;&nbsp;&nbsp;"+
                        "<div class='form-group form__group'><label>Penalty</label><input value="+overdue+" class='form-control m-input overdue text-right' readonly type='text'></div>&nbsp;&nbsp;&nbsp;&nbsp;"+
                    "</div>"+
                    "</div>";
                }
                list = list + 
                "<hr><div class='col-md'>"+
                "<div class='row'>"+
                    "<div class='form-group form__group'><label><strong>Total Penalties</strong></label><input class='form-control m-input overdue text-right' value="+total+" readonly type='text'></div>&nbsp;&nbsp;&nbsp;&nbsp;"+
                "</div>"+
                "</div>";
                $("#title_penalties").html('Penalties of '+data.ref_no);
                $("#view_penalties").html(list);
                $(".overdue").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
            },
            error: function (request, status, error) {
              toastr.error("Please check your internet connection.", "Connection error");
            }
        });
        $('#m_view_penalty').modal('show');
    }
}

function itemDatatableActions(row){
	if(row){
        var tempHtml = "---";
        var tempActions = [];
        var currentActions = ["view", "delete"];
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
                    case "view":
                        tempHtml += `<a class="dropdown-item " data-toggle='modal' data-target='#m_viewPayment' href="javascript:void(0);" id='viewPayment' data-id='`+row.id+`'><i class="la la-eye"></i> View</a>`;
                    break;
                    case "delete":
                        if(!row.isArchiveHide){
                            tempHtml += `<a class="dropdown-item" style="color: #FF8383;" data-toggle='modal' data-target='#delete_modal' href="javascript:void(0);" onclick='modalArchive(`+ row.id +`,`+`\"` + row.account_name + `\",`+`\"` + row.payment_ref_no + `\")'><i class="la la-trash" style="color: #FF8383;"></i> Archive</a>`;
                        }
                    break;
                }
            });
            tempHtml += `</div></div>`;
        return tempHtml;
	}else{ return false; }
}

Inputmask.extendAliases({
    pesos: {
              prefix: "₱ ",
              groupSeparator: ".",
              alias: "numeric",
              placeholder: "0",
              autoGroup: !0,
              digits: 2,
              digitsOptional: !1,
              clearMaskOnLostFocus: !1
          }
  });
  
  $(".net_payment").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
  $(".balance_covered").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
  $(".sub_total").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
  $(".reconnection_fee").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
  $(".received_amount").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
  $(".bill_amount").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
  $(".overdue_fee").inputmask({ alias : "pesos", removeMaskOnSubmit: true });

$('#table-payment').on("click","#viewPayment",function(){
    var payment_id = $(this).attr("data-id");
    $.ajax({
        url: baseUrl("eforms/billing/get_payment"),
        type: 'post',
        data: {csrf_token: _csrf_hash, payment_id: payment_id},
        success: function(response){
            $("#m_viewPayment .created_date").val(response.created_date);
            $("#m_viewPayment .created_by").val(response.created_by);
            $("#m_viewPayment .overdue_fee").val(response.is_penalty=='1' ? response.penalties[0].overdue : 0);
            $("#m_viewPayment .account_no").val(response.accountno);
            $("#m_viewPayment .bill").val(response.bill_ref_no);
            $("#m_viewPayment .meter_no").val(response.meter_no);
            $("#m_viewPayment .block_no").val(response.block_no);
            $("#m_viewPayment .lot_no").val(response.lot_no);
            $("#m_viewPayment .payment_type").val(response.payment_type);
            $("#m_viewPayment .payment_details").val(response.payment_details);
            $("#m_viewPayment .reconnection_fee").val(response.reconnection_fee);
            $("#m_viewPayment .sub_total").val(response.sub_total);
            $("#m_viewPayment .balance_covered").val(response.balance_covered);
            $("#m_viewPayment .net_payment").val(response.net_payment);
            $("#m_viewPayment .received_amount").val(response.received_amount);
            $("#m_viewPayment .payment_date").val(response.payment_date);
            $("#m_viewPayment .customer_name").val(response.customer_name);
            $("#m_viewPayment .bill_amount").val(response.total_charges);
            $("#m_viewPayment .acknowledgement_receipt").val(response.acknowledgement_receipt);
        },
        error: function (request, status, error) {
            $('#m_viewPayment').modal('hide');
            toastr.error("Please check your internet connection.", "Connection error");
        }
    });
});

$('#m_viewPayment').on('hide.bs.modal', function () {
    $(this).find("input").val('').end();
});

function modalArchive(id,name,payment_ref_no){
    const temp = `<p>Are you sure you wan't to archive <strong class='m--font-boldest'>${payment_ref_no}</strong>?</p>`;
    $('#m_archived').modal('show');
    $('#archive_text').empty().html(temp);
    $("#m_archived input[name=id]").val(id);
    $("#m_archived input[name=payment_ref_no]").val(payment_ref_no);
}

function archivePayment(){
    var id = document.getElementById('archive_id').value;
    var payment_ref_no = document.getElementById('payment_ref_no').value;
    $.ajax({
        url: baseUrl("eforms/billing/archive_payment"),
        type: 'post',
        data: { csrf_token: _csrf_hash, id: id, payment_ref_no: payment_ref_no },
        success: function (data) {
            if(data.status){
                $('#m_archived').modal('hide');
                tblPayment.ajax.reload();
            }
        },
        error: function (request, status, error) {
          toastr.error("Please check your internet connection.", "Connection error");
        }
    });
}

$(".massPrint").on("click",function(){
    
     var selectedPayment = [];
     $(".chckBox").each(function(){
        var trig = $(this).is(":checked");
        if(trig){
            selectedPayment.push($(this).attr("value"));
        }
    });

    if(selectedPayment.length > 0){
        $.ajax({
            url: baseUrl("eforms/billing/print_payment"),
            type: "POST",
            data:{selectedPayment: selectedPayment, csrf_token: _csrf_hash},
            success: function(response){
              
                var w = window.open("about:blank");
                w.document.open();
                w.document.write(response.html);
                w.document.close();

                setTimeout(function(){
                    w.print();
                    w.close();
                }, 10);

                w.onafterprint = function(){
                    savePrintLogs(selectedPayment);
                };
            },
            error: function (request, status, error) {
              toastr.error("Please check your internet connection.", "Connection error");
            }
        });
    }
});

function savePrintLogs(selectedPayment){
    $.ajax({
        url: baseUrl("eforms/billing/save_print_logs"),
        type: 'post',
        data: { csrf_token: _csrf_hash, selectedPayment: selectedPayment },
        success: function (data) {
            console.log(data)
        }
    });
}

$("#cb-select-all").click(function () {
    $('#table-payment tbody input[type="checkbox"]').prop('checked', this.checked);
});

$("#table-payment").on("click", "tbody input[type='checkbox']", function () {
    const allCheckboxes = $("#table-payment tbody input[type='checkbox']").length;
    const checkedCheckboxes = $("#table-payment tbody input[type='checkbox']:checked").length;
    const checked = allCheckboxes <= checkedCheckboxes;
    $('#cb-select-all').prop('checked', checked);
});

$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    tblPayment.ajax.reload();
});

$("#ExportExcel").on("click", function() {
    tblPayment.button( '.buttons-excel' ).trigger();
    saveExportLogs('Payments - Export Excel');
});

$("#ExportCSV").on("click", function() {
    tblPayment.button( '.buttons-csv' ).trigger();
    saveExportLogs('Payments - Export CSV');
});

$("#ExportPDF").on("click", function() {
    tblPayment.button( '.buttons-pdf' ).trigger();
    saveExportLogs('Payments - Export PDF');
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