var search_val = "";
var query_builder = "";

const initReadingStartDate = moment();
const initReadingEndDate = moment();
let selectedReadingStartDate = moment();
let selectedReadingEndDate = moment();


var tbl_reports = $("#table-reports").DataTable({
   dom: '<"toolbar">rtlip',
   serverSide: true,
   processing: true,
   aaSorting: [],
   ajax: {
        url: baseUrl("eforms/billing/get_reports_soa/"),
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
       { data: "customer_name", width: "5%", render: function (data) {
                return "<strong style='color: #525252;'>"+data+"</strong>";
            }
        },
       { data: "accountno", width: "5%"},
       { data: "meterno", width: "5%"},
       { data: "subdivision_name", width: "15%"},
       { data: "overPayment", className: "text-right", width: "5%", render: function (data) {
                return "₱ <strong style='color: #525252;'>"+numberWithCommas(parseFloat(data).toFixed(2))+"</strong>";
            }
        },
        { data: "total_penalty", className: "text-right", width: "5%", render: function (data) {
                 return "₱ <strong style='color: #525252;'>"+numberWithCommas(parseFloat(data).toFixed(2))+"</strong>";
             }
         },
         { data: "total_balance", className: "text-right", width: "5%", render: function (data) {
                  return "₱ <strong style='color: #525252;'>"+numberWithCommas(parseFloat(data).toFixed(2))+"</strong>";
              }
          },
       { data: null, width: "2%", className: "text-center"},
   ],
   columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function ( data, type, row, meta ) { return itemDatatableActions(row); 
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

function itemDatatableActions(row){
	if(row){
		var _actionButton ="";
        
        _actionButton += "<a title='SOA' href='javascript:void(0);' data-toggle='tooltip' data-placement='top' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill tooltip-inner' onclick='modalSOA("+ row.id +","+"\"" + row.customer_name+ "\""+","+"\"" + row.accountno + "\","+"\"" + row.meterno + "\")'><i class='la la-eye'></i> </a>";

		return _actionButton;
	}else{ return false; }
}

$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    tbl_reports.ajax.reload();
});

function modalSOA(id,customer_name,accountno,meterno){
    // tbl_reports.ajax.reload();
    $("#m_soa #name").html(customer_name);
    $("#m_soa #account_no").html(accountno);
    $("#m_soa #meter_no").html(meterno);
    // $("#m_soa #balance").html('₱ '+numberWithCommas(total_balance));
    // $("#m_soa #total_penalty").html('₱ '+numberWithCommas(total_penalty));
    // $("#m_soa #overPayment").html('₱ '+numberWithCommas(overPayment));
    $('#customer_id').val(id);
    $('#account_name').val(customer_name);
    $('#m_soa').modal('show');
}

function getTotalBalanceEtc(){
    var customer_id = $('#customer_id').val();
    $.ajax({
        type: "POST",
        url: baseUrl('eforms/billing/get_total_balance_etc/'),
        dataType: "JSON",
        data: { csrf_token: _csrf_hash, id: customer_id },
        success: function (result) {
            var total_balance = (result.total_balance < 0) ? 0 : result.total_balance;
            // $("#m_soa #balance").html('₱ '+numberWithCommas(result.lastbill.total_balance.toFixed(2)));
            $("#m_soa #balance").html('₱ '+numberWithCommas(total_balance.toFixed(2)));
            $("#m_soa #total_penalty").html('₱ '+numberWithCommas(result.lastbill.total_penalty.toFixed(2)));
            $("#m_soa #overPayment").html('₱ '+numberWithCommas(result.overpayment));
            let final_bal = parseFloat(result.lastbill.total_amount) - parseFloat(result.overpayment);
            $("#m_soa #total_balance").html('₱ '+ numberWithCommas(total_balance.toFixed(2)));
        },
        error: function (request, status, error) {
            toastr.error("Please check your internet connection.", "Connection error");
        }
    });
}

var _customer_id = "", _selectedDate = "", _startDate = "", _endDate = "";

function initTableSOA(selectedDate, startDate = null, endDate = null){
    $('#selectedDate').val(selectedDate);
    $(".filter_year").html(selectedDate);
    _customer_id = document.getElementById("customer_id").value;
    _selectedDate = selectedDate;
    _startDate = startDate;
    _endDate = endDate;
    // tbl_reports_dialog.ajax.reload();
    // tbl_reports_dialog_billing.ajax.reload();
}

var tbl_reports_dialog = $("#table-reports_soa").DataTable({
    dom: '<"toolbar">t',
    destroy: true,
    serverSide: true,
    processing: true,
    ordering: false,
    aaSorting: [],
    ajax: {
         url: baseUrl("eforms/billing/get_reports_soa_details/"),
         type: "post",
         global: false,
         dataType: "json",
         data: function(d){
            d.csrf_token = _csrf_hash,
            d.id = _customer_id,
            d.selectedDate = _selectedDate,
            d.startDate = _startDate, 
            d.endDate = _endDate
        },
        error: function (xhr, error, code){
            $('#m_soa').modal('hide');
        }
    },
    searching: true,
    columns: [
        { data: "ref_no", render: function (data) { return "<strong style='color: #525252;'>"+data+"</strong>"; } },
        { data: "bill_ref", render: function (data) { return "<strong style='color: #525252;'>"+data+"</strong>";} },
        { data: "created_date", width: "20%", render: function (data) { return data; } },
        { data: "payment_type" },
        { data: "total_charges" },
        { data: "net_payment", className: "text-right", render: function (data) {
            return numberWithCommas(parseFloat(data).toFixed(2));
            }
        },
        { data: "balance_covered", className: "text-right", render: function (data) {
                return data > 0 ? '-'+numberWithCommas(parseFloat(data).toFixed(2)) : '';
            }
        },
        { data: "received_amount", className: "text-right", render: function (data) {
                return "<strong style='color: #525252;'>"+numberWithCommas(parseFloat(data).toFixed(2))+"</strong>";
            }
        },
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
    ],
    "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api(), data;

        var totalNetPayment = api
            .column(5)
            .data()
            .reduce(function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0);

        var totalBalance = api
            .column(6)
            .data()
            .reduce(function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0);
        
        var totalPayment = api
            .column(7)
            .data()
            .reduce(function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0);

        // Update footer by showing the total with the reference of the column index 
        $(api.column(0).footer()).html();
        $(api.column(1).footer()).html();
        $(api.column(2).footer()).html();
        $(api.column(3).footer()).html();
        $(api.column(4).footer()).html('Total');
        $(api.column(5).footer()).html('₱ '+numberWithCommas(totalNetPayment.toFixed(2)));
        $(api.column(6).footer()).html('₱ -'+numberWithCommas(totalBalance.toFixed(2)));
        $(api.column(7).footer()).html('₱ '+numberWithCommas(totalPayment.toFixed(2)));
    },
});

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

var tbl_reports_dialog_billing = $("#table-reports_billing").DataTable({
    dom: '<"toolbar">t',
    destroy: true,
    serverSide: true,
    processing: true,
    ordering: false,
    aaSorting: [],
    ajax: {
         url: baseUrl("eforms/billing/get_reports_soa_details_billing/"),
         type: "post",
         global: false,
         dataType: "json",
         data: function(d){
            d.csrf_token = _csrf_hash,
            d.id = _customer_id,
            d.selectedDate = _selectedDate,
            d.startDate = _startDate, 
            d.endDate = _endDate
        },
        error: function (xhr, error, code){
            // $('#m_soa').modal('hide');
        }
    },
    searching: true,
    columns: [
        { data: "ref_no", width: "10%", render: function (data) {
                 return "<strong style='color: #525252;'>"+data+"</strong>";
             }
         },
        { data: "billing_from", width: "15%", render: function (data) {
                 return data;
             }
         },
        { data: "billing_to", width: "15%", render: function (data) {
                return data;
            }
        },
        { data: "is_paid", class: 'text-center', render: function (data){
            let status;
            if(data == 0){
                status = "<div class='m-badge text-white m-badge--warning m-badge--wide' role='alert'><strong>Unpaid</strong></div>";
            }else{
                status = "<div class='m-badge text-white m-badge--success m-badge--wide' role='alert'><strong>Paid</strong></div>";
            }
            return status;
        }},
        { data: "usage", class: 'text-center'},
        { data: "total_charges", class: 'text-right', render: function(data){
                return "₱ "+data;
            }  
        },
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
    ],
    "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api(), data;

        var totalUsage = api
            .column( 4 )
            .data()
            .reduce( function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0 );

        var totalCharges = api
            .column( 5 )
            .data()
            .reduce( function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0 );

        $( api.column( 3 ).footer() ).html('Total');
        $( api.column( 4 ).footer() ).html(totalUsage.toFixed(2));
        $( api.column( 5 ).footer() ).html('₱ '+numberWithCommas(totalCharges.toFixed(2)));
    },
});

var tbl_reports_dialog_reading = $("#table-reports_reading").DataTable({
    dom: '<"toolbar">t',
    destroy: true,
    serverSide: true,
    processing: true,
    ordering: false,
    aaSorting: [],
    ajax: {
         url: baseUrl("eforms/billing/get_reports_soa_readings/"),
         type: "post",
         global: false,
         dataType: "json",
         data: function(d){
            d.csrf_token = _csrf_hash,
            d.id = _customer_id,
            d.selectedDate = _selectedDate,
            d.startDate = _startDate, 
            d.endDate = _endDate
        },
        error: function (xhr, error, code){
            // $('#m_soa').modal('hide');
        }
    },
    searching: true,
    columns: [
        { data: "ref_no", render: function (data) {
                 return "<strong style='color: #525252;'>"+data+"</strong>";
             }
         },
        { data: "reading_date", render: function (data) {
                 return data;
             }
         },
        { data: "reading", class: "text-center"},
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
    ],
    "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api(), data;

        var totalReading = api
            .column( 2 )
            .data()
            .reduce( function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0 );

        // Update footer by showing the total with the reference of the column index 
        $( api.column( 1 ).footer() ).html('Total');
        $( api.column( 2 ).footer() ).html("<span class='text-center'>"+totalReading+"</span>");
    },
});

var tbl_reports_dialog_ledger = $("#table-reports_ledger").DataTable({
    dom: '<"toolbar">t',
    destroy: true,
    serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
         url: baseUrl("eforms/billing/get_reports_soa_ledger/"),
         type: "post",
         global: false,
         dataType: "json",
         data: function(d){
            d.csrf_token = _csrf_hash,
            d.id = _customer_id,
            d.selectedDate = _selectedDate,
            d.startDate = _startDate, 
            d.endDate = _endDate
        },
        error: function (xhr, error, code){
            // $('#m_soa').modal('hide');
        }
    },
    searching: true,
    columns: [
        {
          data: "due_date", render: function (data) {
                return "<strong style='color: #525252;'>"+data+"</strong>";
            }
        },
        { data: "ref_no", render: function (data) {
                 return "<strong style='color: #525252;'>"+data+"</strong>";
             }
         },
        { data: "debit", class: 'text-right', render: function (data, type, row, meta) {
                 return '<span class="m--text-muted" style="font-size: 11px;"><small>'+numberWithCommas(parseFloat(row.total_charges).toFixed(2))+' + '+numberWithCommas(parseFloat(row.penalty).toFixed(2))+' + '+numberWithCommas(parseFloat(row.reconnection_fee).toFixed(2))+' =</small></span><br><span data-toggle="tooltip" data-placement="top">₱ ' +numberWithCommas(parseFloat(data).toFixed(2))+'</span><br>';
             }
         },
        { data: "credit", class: "text-right", render: function(data, type, row, meta){
                return row.payment_date+'<br><span data-toggle="tooltip" data-placement="top">₱ ' +numberWithCommas(parseFloat(data).toFixed(2))+'</span>';
            }
        },
        // { data: "balance_covered", class: "text-right", render: function(data){
        //     return '<span data-toggle="tooltip" data-placement="top" title="">₱ ' +numberWithCommas(parseFloat(data).toFixed(2))+'</span><br>';
        //     }
        // },
        { data: "balance", class: "text-right", render: function(data){
            return '<span data-toggle="tooltip" data-placement="top" title="">₱ ' +numberWithCommas(parseFloat(data).toFixed(2))+'</span>';
        }
    },
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
    ],
    "footerCallback": function ( row, data, start, end, display ) {
    },
});

function updateFooter() {
  if (tbl_reports_dialog_ledger.data().count() > 0) {
    const lastRowData = tbl_reports_dialog_ledger.row(':last').data();
    const lastValue = lastRowData['balance'];
    $("#remaining_balance").text('₱ ' +numberWithCommas(parseFloat(lastValue).toFixed(2)));
    $("#print_ledger").prop("disabled", false);
  } else {
    // If DataTable is empty, display a message or a default value in the footer
    $("#remaining_balance").text("₱ 0.00");
    $("#print_ledger").prop("disabled", true);
  }
}

// Call the function on page load and whenever the DataTable is redrawn (e.g., after sorting or filtering)
updateFooter();
tbl_reports_dialog_ledger.on('draw', function() {
  updateFooter();
});

$('#datePicker_soa').datepicker({
    format: 'yyyy',
    todayHighlight: true,
    autoclose: true,
    orientation: "bottom left",
    viewMode: "years",
    minViewMode: "years",
    endDate: getCurrentDate(),
});

function getCurrentDate(){
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();
    return yyyy+'-'+mm+'-'+dd;
}

var vm_waterUsage = new Vue({
    el: "#soa_modal",
    data: {row:{}, collection:{}, count:0},
    methods: {
        getUsagePerSubd:function(date){
            const _this = this;
            let elements = _this.$el;
            initTableSOA(date, null, null);
            $("#readings-date-range-picker").addClass("m--hide");
        },
        customRange: function(){
            $("#readings-date-range-picker").removeClass("m--hide");
        },
        getCollection:function(){
            const _this = this;
            var customer_id = document.getElementById("customer_id").value;
            $("#readings-date-range-picker").addClass("m--hide");
            $.ajax({
                type: "POST",
                url: baseUrl('eforms/billing/get_reports_soa_dates/'),
                dataType: "JSON",
                data: { csrf_token: _csrf_hash, id: customer_id },
                success: function (result) {
                    _this.count = result.length;
                    _this.collection = Object.assign({},result);
                },
                error: function (request, status, error) {
                  toastr.error("Please check your internet connection.", "Connection error");
                }
            });
        }
    }
});

$(".btnPrint").on("click", function(){
    var account_name = document.getElementById("account_name").value;
    var customer_id = document.getElementById("customer_id").value;
    var selectedDate = document.getElementById("selectedDate").value;
    var report_type = $("#report_type").val();
    $.ajax({
        url: baseUrl("eforms/billing/print_reports_soa"),
        type: "POST",
        data:{
            csrf_token: _csrf_hash, 
            customer_id: customer_id, 
            selectedDate: _selectedDate, 
            account_name: account_name, 
            startDate: _startDate, 
            endDate: _endDate,
            report_type: report_type
        },
        success: function(response){
            
            var w = window.open("about:blank");
            w.document.open();
            w.document.write(response);
            w.document.close();

            setTimeout(function(){
                w.print();
                w.close();
            }, 250);
        },
        error: function (request, status, error) {
        toastr.error("Please check your internet connection.", "Connection error");
        }
    });

});

$("#report_type").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
});

$("#date_filter").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
      url: baseUrl("eforms/billing/get_reports_soa_dates"),
      global: false,
      processResults: function (data) {
        return data;
      }
    }
}).on("change", function(){
    if($("#date_filter").val() == "custom"){
        $("#custom_range").removeClass("m--hide");
    }else{
        $("#custom_range").addClass("m--hide");
        $("#date-picker").val("");
    }
});

let tempRangeDates = {
    min_date: '',
    max_date: '',
};

var generateDateTimePicker = function (min = null, max = null) {
    $("#date-range").val("");
    $("#date-picker")
        .daterangepicker({
            // minDate: min,
            // maxDate: max,
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            locale: {
                format: 'MM/DD/YYYY'
            }
        }).on('apply.daterangepicker', function (ev, picker) {
            $("#date-range")
                .val(picker.startDate.format('MMM DD, YYYY') + ' - ' + picker.endDate.format('MMM DD, YYYY'))
        });
}
generateDateTimePicker(tempRangeDates.min_date, tempRangeDates.max_date);


function generateReport(e){
    _selectedDate = $("#date_filter").val();
    _customer_id = document.getElementById("customer_id").value;
    var date_range = $("#date-range").val();
    var dates = date_range.split("-");
    _startDate = dates[0];
    _endDate = dates[1];
    
    getTotalBalanceEtc();
    
    if($("#report_type").val() == "billing"){
        $("#table-reports_billing").removeClass("m--hide");
        $("#table-reports_reading").addClass("m--hide");
        $("#table-reports_soa").addClass("m--hide");
        $("#table-reports_ledger").addClass("m--hide");
        tbl_reports_dialog_billing.ajax.reload();
    }else if($("#report_type").val() == "reading"){
        $("#table-reports_reading").removeClass("m--hide");
        $("#table-reports_billing").addClass("m--hide");
        $("#table-reports_soa").addClass("m--hide");
        $("#table-reports_ledger").addClass("m--hide");
        tbl_reports_dialog_reading.ajax.reload();
    }else if($("#report_type").val() == "payment"){
        $("#table-reports_soa").removeClass("m--hide");
        $("#table-reports_reading").addClass("m--hide");
        $("#table-reports_billing").addClass("m--hide");
        $("#table-reports_ledger").addClass("m--hide");
        tbl_reports_dialog.ajax.reload();
    }else{
        $("#table-reports_soa").addClass("m--hide");
        $("#table-reports_billing").addClass("m--hide");
        $("#table-reports_reading").addClass("m--hide");
        $("#table-reports_ledger").removeClass("m--hide");
        tbl_reports_dialog_ledger.ajax.reload();
    }
}

$("#m_soa").on('hidden.bs.modal', function(){
    $("#report_type").val([]).trigger("change");
    $("#date_filter").val([]).trigger("change");
    $("#customer_id").val("");
    $("#table-reports_reading").addClass('m--hide');
    $("#table-reports_billing").addClass('m--hide');
    $("#table-reports_soa").addClass('m--hide');
    $("#table-reports_ledger").addClass('m--hide');
    $("#date-range").val("");
    generateDateTimePicker(null, null);
});