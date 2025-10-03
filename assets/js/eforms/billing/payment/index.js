const initReadingStartDate = moment();
const initReadingEndDate = moment();
let selectedReadingStartDate = null;
let selectedReadingEndDate = null;

let search_val = "";
let query_builder = "";
const tblPayment = $("#table-payment").DataTable({
   dom: '<"toolbar">rtlip',
   serverSide: true,
   processing: true,
//    aaSorting: [],
   ajax: {
        url: baseUrl("eforms/billing/get_billing_payment/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function(d) {
            d.csrf_token = _csrf_hash,
            d.search['value'] = search_val,
            d.query_builder = query_builder

            if (selectedReadingStartDate && selectedReadingEndDate) {
                d.startDate = moment(selectedReadingStartDate).format("YYYY-MM-DD");
                d.endDate = moment(selectedReadingEndDate).format("YYYY-MM-DD");
            } else {
                d.startDate = '';
                d.endDate = '';
            }
       }
   },
   order: [[11, "desc"]],
   searching: true,
   columns: [
        { data: "checkbox"},
        { data: "payment_ref_no", render: function (data) { return "<span class='m--font-boldest'>"+data+"</span>";} },
        { data: "name"},
        { data: "ref_no"},
        { data: "payment_type", className: "text-center"},
        { data: "due_date", className: "text-center"},
        { data: null, className: "text-right"},
        { data: "net_payment", className: "text-right", render: function(data) {
                return "<span class='m--font-boldest'>"+numberWithCommas(data)+"</span>";
            }
        },
        { data: "received_amount", className: "text-right", render: function (data) {
                return "<span class='m--font-boldest'>"+numberWithCommas(data)+"</span>";
            }
        },
        { data: "acknowledgement_receipt", className: "text-center"},
        { data: "payment_date", className: "text-center"},
        { data: "created_date", className: "text-center"},
        { data: null, className: "text-center"},
   ],
   columnDefs: [
        {
            orderable: false,
            className: 'select-checkbox',
            targets: 0
        },
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function ( data, type, row, meta ) { 
              return itemDatatableActions(row); 
            },
        }, 
        {
            data: null,
            defaultContent: "",
            targets: 6,
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
   select: {
        style:    'multi',
        selector: 'td:first-child'
   },
   buttons: [
       { 
            extend: 'csv',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            },
            customize: function(csv) {
                let data = csv.split('\n');

                let targetUppercase = [1, 3]; // Columns to make uppercase
                let removeSpecialChar = [5, 6, 7]; // Remove special characters from these columns like peso sign
                let removeComma = [5, 6, 7]; // Column to remove commas

                // Loop through each row
                data = data.map((row, rowIndex) => {  
                    // Split row into columns, considering quoted fields
                    let columns = row.match(/(".*?"|[^",\s]+)(?=\s*,|\s*$)/g);

                    columns = columns.map((col, columnIndex) => {
                        col = col.trim(); // Remove extra spaces
                
                        if (rowIndex === 0) { 
                            return col.replace(/\b\w/g, char => char.toUpperCase());
                        }
                
                        // Convert to uppercase for specific columns
                        if (targetUppercase.includes(columnIndex)) {
                            col = col.toUpperCase();
                        }

                        // Remove special characters from specific columns
                        if (removeSpecialChar.includes(columnIndex)) {
                            col = col.replace(/[^\w\s.]/gi, '');
                        }
                
                         // Remove commas from specific columns
                        if (columnIndex === removeComma) {
                            col = col.replace(/,/g, '');
                        }
                
                        return col;
                    });

                    return columns.join(","); // Join modified columns
                });

                // Add UTF-8 BOM to the beginning of the CSV data for letter "ñ" to appear correctly
                const utf8BOM = '\uFEFF';
                return utf8BOM + data.join("\n"); // Reassemble CSV
            }
       },
       { 
            extend: 'excel',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            },
            customize: function(xlsx) {
                let sheet = xlsx.xl.worksheets['sheet1.xml'];

                // Convert Column B to Uppercase
                $('row:not(:nth-child(2)) c[r^="B"], row:not(:nth-child(2)) c[r^="D"]', sheet).each(function () {
                    let cell = $(this).find('is t, v'); // Find the text inside
                    let text = cell.text().trim(); // Get the existing text

                    if (text) {
                        cell.text(text.toUpperCase()); // Convert to uppercase
                    }
                });

                // Remove special characters from specific columns
                $('row:not(:nth-child(2)) c[r^="F"], row:not(:nth-child(2)) c[r^="G"], row:not(:nth-child(2)) c[r^="H"]', sheet).each(function () {
                    let cell = $(this).find('is t, v'); // Find the text inside
                    let text = cell.text().trim(); // Get the existing text

                    if (text) {
                        let numericValue = parseFloat(text.replace(/[^\d.-]/g, ''));
                        cell.text(numericValue);
                    }
                });
            }
       },
       { 
            extend: 'pdf',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            },
            orientation: 'landscape',
            pageSize: 'LEGAL',
            customize: function(doc) {
                // Set dynamic widths for all columns
                let columnWidths = new Array(doc.content[1].table.body[0].length).fill('*');

                // Define custom widths for specific columns (adjust index as needed)
                columnWidths[1] = '18%';
                
                // Set font size for header row
                doc.styles = doc.styles || {};
                doc.styles.tableHeader = doc.styles.tableHeader || {};
                doc.styles.tableHeader.fontSize = 9; 
                doc.styles.tableHeader.fillColor = '#2d4154'; // Set header background color

                // Apply column widths
                doc.content[1].table.widths = columnWidths;

                // Loop through table body and target specific column
                doc.content[1].table.body.forEach(function (row, rowIndex) {

                    // Skip header row from all styles
                    if (rowIndex === 0) { return; }

                    let targetUppercase = [1, 3, 5]; // Columns to make uppercase
                    let targetCenter = [0, 2, 3, 4, 8, 9]; // Columns to center align
                    let targetRight = [5, 6, 7]; // Column to right align
                    let removeSpecialChar = [5, 6, 7]; // Remove special characters from these columns like peso sign

                    row.forEach((cell, columnIndex) => {
                        if (!cell.text) { return; }

                        // Set font size for other rows
                        cell.style = { fontSize: 9 }; 

                        // Background color for even and odd rows
                        if (rowIndex % 2 === 0) {
                            cell.fillColor = '#f9f9f9'; // Light gray for even rows
                        } else {
                            cell.fillColor = '#ffffff'; // White for odd rows
                        }

                        // Set text to uppercase for specific columns
                        if (targetUppercase.includes(columnIndex)) {
                            cell.text = cell.text.toUpperCase();
                        }

                        // Center align specific columns
                        if (targetCenter.includes(columnIndex)) {
                            cell.alignment = 'center';
                        } 
                        
                        // Right align specific columns
                        if (targetRight.includes(columnIndex)) {
                            cell.alignment = 'right';
                        }

                        // Remove special characters from specific columns
                        if (removeSpecialChar.includes(columnIndex)) {
                            cell.text = cell.text.replace(/[^\w\s,.]/gi, '');
                        }
                    });
                });
            }
       }
   ],
   createdRow: function(row, data, dataIndex) {
        $(row).find('td').addClass('v-middle').attr('data-id-print', data.id);

        const is_archive = data.is_archive;
                console.log(is_archive);
        if ( is_archive == 1 ) {
            $(row).addClass('table-danger');
        }
   }
});

// =============== Payment Date Range Picker ===============

$('#payment-date-picker').daterangepicker({
    buttonClasses: 'm-btn btn',
    applyClass: 'btn-primary',
    cancelClass: 'btn-secondary',
    startDate: initReadingStartDate,
    endDate: initReadingEndDate,
    format: "MMM. DD, YYYY"
}, function (start, end, label) {
    selectedReadingStartDate = start;
    selectedReadingEndDate = end;

    let _label = `<strong>${start.format("MMM. DD, YYYY")}</strong> to <strong>${end.format("MMM. DD, YYYY")}</strong>`;

    $(".selected-filter", $('#payment-date-picker')).html(_label);
    tblPayment.ajax.reload();
});

$('#cb-select-all').on('change', function() {
    if (this.checked) {
        tblPayment.rows().select();
    } else {
        tblPayment.rows().deselect();
    }
});

tblPayment.on('select deselect', function() {
    if (tblPayment.rows({ selected: true }).count() !== tblPayment.rows().count()) {
        $('#cb-select-all').prop('checked', false);  
    } else {
        $('#cb-select-all').prop('checked', true);
    }
});
  
// =============== Payment Date Range Picker ===============

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

function itemDatatableActions(row) {
	if(row) {
        var tempHtml = "---";
        var tempActions = [];
        var currentActions = ["view", "delete"];
        $.each(currentActions, function(index, value) {
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
                                    if(!row.isArchiveHide && row.is_archive != 1) {
                                        tempHtml += `<a class="dropdown-item" style="color: #FF8383;" data-toggle='modal' data-target='#delete_modal' href="javascript:void(0);" onclick='modalArchive(`+ row.id +`,`+`\"` + row.account_name + `\",`+`\"` + row.payment_ref_no + `\")'><i class="la la-trash" style="color: #FF8383;"></i> Archive</a>`;
                                    }
                                break;
                            }
                        });

            tempHtml += `</div>
                    </div>`;
        return tempHtml;

	} else { 
        return false; 
    }
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

$('#table-payment').on("click","#viewPayment",function() {
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

function modalArchive(id,name,payment_ref_no) {
    const temp = `<p>Are you sure you wan't to archive <strong class='m--font-boldest'>${payment_ref_no}</strong>?</p>`;
    $('#m_archived').modal('show');
    $('#archive_text').empty().html(temp);
    $("#m_archived input[name=id]").val(id);
    $("#m_archived input[name=payment_ref_no]").val(payment_ref_no);
}

function archivePayment() {
    var id = document.getElementById('archive_id').value;
    var payment_ref_no = document.getElementById('payment_ref_no').value;
    $.ajax({
        url: baseUrl("eforms/billing/archive_payment"),
        type: 'post',
        data: { csrf_token: _csrf_hash, id: id, payment_ref_no: payment_ref_no },
        success: function (data) {
            if(data.status) {
                $('#m_archived').modal('hide');
                tblPayment.ajax.reload();
            }
        },
        error: function (request, status, error) {
          toastr.error("Please check your internet connection.", "Connection error");
        }
    });
}

$(".massPrint").on("click", function() {
    var selectedPayment = [];

    $("#table-payment tr.selected").each(function(){
        selectedPayment.push($(this).find('td').attr("data-id-print"));
    });

    if(selectedPayment.length > 0) {
        $.ajax({
            url: baseUrl("eforms/billing/print_payment"),
            type: "POST",
            data:{selectedPayment: selectedPayment, csrf_token: _csrf_hash},
            success: function(response) {
              
                var w = window.open("about:blank");
                w.document.open();
                w.document.write(response.html);
                w.document.close();

                setTimeout(function(){
                    w.print();
                    w.close();
                }, 10);

                w.onafterprint = function() {
                    savePrintLogs(selectedPayment);
                };
            },
            error: function (request, status, error) {
              toastr.error("Please check your internet connection.", "Connection error");
            }
        });
    }
});

function savePrintLogs(selectedPayment) {
    $.ajax({
        url: baseUrl("eforms/billing/save_print_logs"),
        type: 'post',
        data: { csrf_token: _csrf_hash, selectedPayment: selectedPayment },
        success: function (data) {
            console.log(data)
        }
    });
}

$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    if (search_val.length >= 3 || search_val.length === 0) {
        tblPayment.ajax.reload();
    }
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

function saveExportLogs(export_) {
    $.ajax({
        url: baseUrl("eforms/billing/save_export_logs"),
        type: 'post',
        data: { csrf_token: _csrf_hash, export_: export_ },
        success: function (data) {
            
        }
    });
}