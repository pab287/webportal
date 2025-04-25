let search_val = "";
let query_builder = "";
const tblPaymentsArchive = $("#table-payments-archive").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/billing/get_payment_archive_collection/"),
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
    order: [[9, "desc"]],
    columns: [
        { data: "payment_ref_no", render: function (data) { return "<strong style='color: #525252;'>"+data+"</strong>";} },
        { data: "name"},
        { data: "ref_no"},
        { data: "payment_type", className: "text-center"},
        { data: "due_date", className: "text-center"},
        { data: null, className: "text-right"},
        { data: "net_payment", className: "text-right", render: function(data) {
                return "<strong style='color: #525252;'>"+numberWithCommas(data)+"</strong>";
            }
        },
        { data: "received_amount", className: "text-right", render: function (data) {
                return "<strong style='color: #525252;'>"+numberWithCommas(data)+"</strong>";
            }
        },
        { data: "acknowledgement_receipt", className: "text-center"},
        { data: "payment_date", className: "text-center"},
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: 5,
            orderable: false,
            render: function (data, type, row, meta) {
                var tempHtml = "";
                if(row.is_penalty == 1){
                    if(row.penalties.length > 1){
                        tempHtml += "<a href='javascript:void(0);' data-toggle='tooltip' data-placement='top' title='Archive' onclick='viewPenalty("+row.id+")'>see more</a>";
                    } else {
                        var v = row.penalties[0];
                        var sum = parseFloat(v.overdue) + parseFloat(row.reconnection_fee);
                        tempHtml += '₱ ';
                        tempHtml += sum.toFixed(2);
                    }
                } else {
                    tempHtml += '₱ 0.00';
                }
                return tempHtml;
            },
        },
        {
            targets: "_all",
            className: "v-middle",
        }
   ],
});

function numberWithCommas(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    if (search_val.length >= 3 || search_val.length === 0) {
        tblPaymentsArchive.ajax.reload();
    }
});

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