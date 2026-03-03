let search_val = "";
let query_builder = "";

const initReadingStartDate = moment();
const initReadingEndDate = moment();
let selectedReadingStartDate = moment();
let selectedReadingEndDate = moment();

let reportGenerated = false;
let modalReset = false;


const tbl_reports = $("#table-reports").DataTable({
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
        { data: "accountno", width: "7%"},
        { data: "meterno", width: "5%"},
        { data: "subdivision_name", width: "13%"},
        { data: "overPayment", className: "text-right", width: "5%", render: function (data) {
                return "₱ <strong style='color: #525252;'>"+numberWithCommas(parseFloat(data).toFixed(2))+"</strong>";
            }
        },
        { data: "total_penalty", className: "text-right", width: "5%", render: function (data) {
                return "₱ <strong style='color: #525252;'>"+numberWithCommas(parseFloat(data).toFixed(2))+"</strong>";
            }
        },
        { data: "overdue_charges", className: "text-right", width: "7%", render: function (data) {
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
            render: function ( data, type, row, meta ) { return itemDatatableActions(row); },
        }
    ],
    select: {
        style: 'os',
        selector: 'td:first-child'
    },
    buttons: [
        { 
            extend: 'csv',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, 
        { 
            extend: 'excel',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, 
        { 
            extend: 'pdf',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }
    ]
});

function itemDatatableActions(row){
	if(row){
		let _actionButton ="";
        
        _actionButton += "<a title='SOA' href='javascript:void(0);' data-toggle='tooltip' data-placement='top' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill tooltip-inner' onclick='modalSOA("+ row.id +","+"\"" + row.customer_name+ "\""+","+"\"" + row.accountno + "\","+"\"" + row.meterno + "\")'><i class='la la-eye'></i> </a>";

		return _actionButton;
	}else{ return false; }
}

$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    if (search_val.length >= 3 || search_val.length === 0) {
        tbl_reports.ajax.reload();
    }
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
    const customer_id = $('#customer_id').val();
    $.ajax({
        type: "POST",
        url: baseUrl('eforms/billing/get_total_balance_etc/'),
        dataType: "JSON",
        data: { 
            csrf_token: _csrf_hash, 
            id: customer_id,
            selectedDate: _selectedDate,
            startDate: _startDate,
            endDate: _endDate
        },
        success: function (result) {
            // $("#m_soa #balance").html('₱ '+numberWithCommas(result.total_charges));
            // $("#m_soa #total_penalty").html('₱ '+numberWithCommas(result.total_penalty));
            // $("#m_soa #overPayment").html('₱ '+numberWithCommas(result.overpayment));
            // $("#m_soa #total_balance").html('₱ '+ numberWithCommas(result.total_balance));

            statement_details.total_charges = result.total_charges;
            statement_details.total_penalty = result.total_penalty;
            statement_details.overpayment = result.overpayment;
            statement_details.total_balance = result.total_balance;
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
            // $('#m_soa').modal('hide');
            console.log(error);
        }
    },
    searching: true,
    columns: [
        { data: "payment_date", width: "20%", 
            render: function (data) { 
                return moment(data).format("MMM DD, YYYY");
            } 
        },
        { data: "created_date", width: "20%", 
            render: function (data) { 
                return moment(data).format("MMM DD, YYYY");
            } 
        },
        { data: "ref_no", render: function (data) { return "<strong>"+data+"</strong>"; } },
        { data: "bill_ref", render: function (data) { return "<strong>"+data+"</strong>";} },
        { data: "payment_type" },
        { data: "total_charges", className: "text-right", render: function (data) {
                return "₱ " + numberWithCommas(parseFloat(data).toFixed(2));
            } 
        },
        { data: "penalty", className: "text-right", render: function (data) {
                return "₱ " + numberWithCommas(parseFloat(data).toFixed(2));
            } 
        },
        { data: "balance_covered", className: "text-right", render: function (data) {
                return (data > 0) ? "₱ " + numberWithCommas(parseFloat(data).toFixed(2)) : '₱ 0.00';
            }
        },
        { data: "net_payment", className: "text-right", render: function (data) {
                return "₱ " + numberWithCommas(parseFloat(data).toFixed(2));
            }
        },
        { data: "received_amount", className: "text-right", render: function (data) {
                return "<strong>₱ "  +numberWithCommas(parseFloat(data).toFixed(2))+"</strong>";
            }
        },
    ],
    select: {
     style:    'os',
     selector: 'td:first-child'
    },
    createdRow: function(row, data, dataIndex){
        const is_archive = data.is_archive;
        if (is_archive == 1) {
            $(row).addClass('is_archived_text').find('strong').addClass('is_archived_text');
        } else {
            $(row).find('strong').css('color', '#525252');
        }
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
        var totalPayment = api
            .column(9)
            .data()
            .reduce(function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0);

        // Update footer by showing the total with the reference of the column index 
        $(api.column(8).footer()).html('Total');
        $(api.column(9).footer()).html('₱ '+numberWithCommas(totalPayment.toFixed(2)));
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
            console.log(error);
        }
    },
    searching: true,
    columns: [
        { data: "ref_no", width: "10%", render: function (data) {
                 return "<strong style='color: #525252;'>"+data+"</strong>";
             }
         },
        { data: "billing_from", width: "15%", render: function (data) {
                return moment(data).format("MMM DD, YYYY");
             }
         },
        { data: "billing_to", width: "15%", render: function (data) {
                return moment(data).format("MMM DD, YYYY");
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
                return moment(data).format("MMM DD, YYYY");
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

function load_ledger_report() {
    $.ajax({
        url: baseUrl("eforms/billing/get_reports_soa_ledger/"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
            csrf_token: _csrf_hash,
            id: _customer_id,
            selectedDate: _selectedDate,
            startDate: _startDate,
            endDate: _endDate
        },
        success: function(resp) {
            vm_reports_soa_ledger.ledger_data = resp.data;
            vm_reports_soa_ledger.totalBalance = resp.total_balance;
        },
        error: function(xhr, err, code) {
            console.log(err);
        }
    });
}

const vm_reports_soa_ledger = new Vue({
    el: "#soa_ledger_vue_wrap",
    data: {
        ledger_data: [],
        totalBalance: 0,
    },
    methods: {
        formatDate(dateStr) {
            return moment(dateStr).format("MMM DD, YYYY");
        }
    }
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
    if (!reportGenerated) {
        toastr.warning("Please click Generate first before printing.", "Action Required");
        return;
    }

    const account_name = document.getElementById("account_name").value;
    const customer_id = document.getElementById("customer_id").value;
    const selectedDate = document.getElementById("selectedDate").value;
    const report_type = $("#report_type").val();
    
    $.ajax({
        url: baseUrl("eforms/billing/print_reports_soa"),
        type: "POST",
        data:{
            csrf_token: _csrf_hash, 
            id: customer_id, 
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
}).on("change", function(){
    if (modalReset) return; // Stop here to prevent double report generation

    const selectedDate = $("#selectedDate").val();

    if (selectedDate) {
        generateReport(); // generate report onchange if not custom    
        reportGenerated = true; // mark as generated
        enablePrintButton(reportGenerated);
    }
});;

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
    if (modalReset) return; // Stop here to prevent double report generation

    $("#selectedDate").val($(this).val());

    if ($(this).val() == "custom") {
        $("#custom_range").removeClass("m--hide");
    } else {
        generateReport(); // generate report onchange if not custom    
        reportGenerated = true; // mark as generated
        enablePrintButton(reportGenerated);

        let date_picker = $("#date-picker").data('daterangepicker');
        date_picker.setStartDate(moment());
        date_picker.setEndDate(moment());

        $("#custom_range").addClass("m--hide");
        $("#date-range").val("");
        _startDate = "";
        _endDate = "";
    }
});

let tempRangeDates = {
    min_date: '',
    max_date: '',
};

var generateDateTimePicker = function (min = null, max = null) {
    $("#date-range").val("");
    $("#date-picker").daterangepicker({
        buttonClasses: 'm-btn btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',
        locale: {
            format: 'MM/DD/YYYY'
        }
    }).on('apply.daterangepicker', function (ev, picker) {
        $("#date-range").val(picker.startDate.format('MMM DD, YYYY') + ' - ' + picker.endDate.format('MMM DD, YYYY'));
        _startDate = picker.startDate.format('MMM DD, YYYY');
        _endDate = picker.endDate.format('MMM DD, YYYY');

        generateReport(); // generate report onchange of date range picker
        reportGenerated = true; // mark as generated
    }).on('cancel.daterangepicker', function(ev, picker) {
        $("#date-range").val("");

        // Reset the internal dates
        picker.setStartDate(moment());
        picker.setEndDate(moment());

        generateReport(); // generate report onchange of date range picker
        reportGenerated = true; // mark as generated
        enablePrintButton(reportGenerated);
    });
}
generateDateTimePicker(tempRangeDates.min_date, tempRangeDates.max_date);


function generateReport(e){
    if (modalReset) return; // Stop here to prevent double report generation

    const date_filter = $("#date_filter").val();
    const report_type = $("#report_type").val();

    if (date_filter == '' || report_type == '') {
        toastr.error("Please select a type & year.", "Error");
        return; 
    }

    _selectedDate = $("#date_filter").val();
    _customer_id = document.getElementById("customer_id").value;

    var date_range = $("#date-range").val().split("-");
    _startDate = date_range[0];
    _endDate = date_range[1]; 
    
    getTotalBalanceEtc();

    let selected = $("#report_type").val();

    // Hide all report blocks
    $(".report-wrapper").addClass("m--hide");

    // Show selected report block
    $(`.report-wrapper[data-type="${selected}"]`).removeClass("m--hide");

    // Reload only the correct table
    switch (selected) {
        case "billing":
            tbl_reports_dialog_billing.ajax.reload();
            break;
        case "reading":
            tbl_reports_dialog_reading.ajax.reload();
            break;
        case "payment":
            tbl_reports_dialog.ajax.reload();
            break;
        case "ledger":
            load_ledger_report();
            break;
    }

    // mark as generated
    reportGenerated = true;
    enablePrintButton(reportGenerated);
}

function enablePrintButton(reportGenerated){
    if (reportGenerated) {
        $(".btnPrint").prop("disabled", false);
    }
}

$("#m_soa").on('hidden.bs.modal', function(){
    modalReset = true; // reset modal then prevent double report generation of trigger change in report type and date_filter

    $("#report_type").val([]).trigger("change");
    $("#date_filter").val([]).trigger("change");
    $("#customer_id").val("");
    $(".report-wrapper").addClass("m--hide");
    $("#selectedDate").val("");

    $("#report-tbl-wrapper table>tbody").empty();
    $("#payment_footer_total").html('₱ 0.00');

    $("#date-range").val("");
    generateDateTimePicker(null, null);

    $("div.report-wrapper").addClass("m--hide");
    vm_reports_soa_ledger.ledger_data = [];
    vm_reports_soa_ledger.totalBalance = 0;

    $(".btnPrint").prop("disabled", true);

    Object.assign(statement_details.$data, {
        total_charges: 0,
        total_penalty: 0,
        overpayment: 0,
        total_balance: 0
    });

    modalReset = false; 
    reportGenerated = false;
});

const statement_details = new Vue({
    el: "#statement_details",
    data: {
        total_charges: 0,
        total_penalty: 0,
        overpayment: 0,
        total_balance: 0
    },
    methods: {
        numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    }
});