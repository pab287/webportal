let selected = {}; 
let dropdownEl = null;

let ca_report_table = $('#payroll-cash-advance-report').DataTable({
    dom: "<'row mb-3'<'col-xl-4 col-lg-4 col-md-4 col-sm-12 mt-3 exportDropdown'><'col-xl-9 col-lg-9 col-md-9 col-sm-12 p-0'f>>" +
    "<'row'<'col-12'rt>>" +
    "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'li><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
    buttons: [
        {
            extend: 'excelHtml5',
            title: 'CASH ADVANCE DEDUCTIONS REPORT',
            exportOptions: {
              columns: [1,2,3,4,5,6,7] ,
            },
          action: function (e, dt, button, config) {
            $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
            dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
            }
        },
        {
            extend: 'pdfHtml5',
            title: 'CASH ADVANCE DEDUCTIONS REPORT',
            className: 'btnPdfAction',
            orientation: 'landscape',
            pageSize: 'LEGAL',
            exportOptions: {
                columns: [1,2,3,4,5,6,7] ,
                stripHtml: false
            },
            customize: function (doc) {
                for (let i = 0; i < doc.content[1].table.body.length; i++) {
                    if (doc.content[1].table.body[i][0]) {
                        doc.content[1].table.body[i][0].text = doc.content[1].table.body[i][0].text.toUpperCase();
                    }
                }
                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length).fill('*');
                doc.pageMargins = [10, 10, 10, 10];
                doc.defaultStyle.fontSize = 8;
                doc.styles.tableHeader = {
                    fillColor: '#2c3e50',
                    color: '#ffffff',
                    fontSize: 10,
                    bold: true,
                    alignment: 'center'
                };
                doc.styles.tableBodyEven = {
                    fillColor: '#f8f9fa',
                    fontSize: 8,
                    alignment: 'center'
                };
                doc.styles.tableBodyOdd = {
                    fillColor: '#ffffff',
                    fontSize: 8,
                    alignment: 'center'
                };
                dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
            }
            
        },
    ],
    // rowId: 'id',
    serverSide: true,
    processing: true,
    language: {
        processing: "Loading data, please wait..."  
    },
    searching: false,
    ordering: true,
    order: [[0, 'desc']],
    ajax: {
        url: baseUrl('payroll/reports/cash_advance_report'),
        type: 'post',
        dataType: 'json',
        global: false,
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = $("#search-payroll-cash-advance-report").val();
            d.date_range = selected;
        },
    },
    columns: [
        { data: 'payment_id', visible: false },
        { 
            data: 'reference',
            render: function (data, type, row) {
                if (row.reference !== null && row.reference !== "") {
                    return row.reference;
                }
                return row.remarks ?? "NO REFERENCE";
            }
        },
        {
            data: 'fullname',
            render: data => data ? data.toUpperCase() : ''
        },
        {
            data: 'loan_amount',
            className: 'text-right',
            render: data => `₱${parseFloat(data).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
        },
        {
            data: 'total_deducted',
            className: 'text-right',
            render: data => `₱${parseFloat(data).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
        },
        {
            data: 'amount_due',
            className: 'text-right',
            render: data => `₱${parseFloat(data).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
        },
        {
            data: 'pay_date',
            render: function(data) {
                return moment(data).format("MMM D, YYYY");
            }
        },
        {
            data: 'remaining_balance',
            className: 'text-right',
            render: data => `₱${parseFloat(data).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
        },

    ],
    initComplete: function () {
        const dropdown = '' +
        '       <div class="m-dropdown m-dropdown--inline m-dropdown--align-left" ' +
        '             data-dropdown-toggle="hover" aria-expanded="true">' +
        '            <button class="m-dropdown__toggle btn btn-success dropdown-toggle export-as">' +
        '                EXPORT AS' +
        '            </button>' +
        '            <div class="m-dropdown__wrapper">' +
        '                <div class="m-dropdown__inner">' +
        '                    <div class="m-dropdown__body">' +
        '                        <div class="m-dropdown__content">' +
        '                            <ul class="m-nav">' +
        '                                <li class="m-nav__item">' +
        '                                    <a id="export-as-excel" style="cursor:pointer;" ' +
        '                                       onclick="exportAs(\'excel\'); return false;" class="m-nav__link">' +
        '                                        <i class="m-nav__link-icon fa fa-file-excel-o m--font-success"></i>' +
        '                                        <span class="m-nav__link-text" style="text-transform: none;">' +
        '                                           Excel File' +
        '                                        </span>' +
        '                                    </a>' +
        '                                </li>' +
        '                                <li class="m-nav__item">' +
        '                                    <a id="export-as-pdf" style="cursor:pointer;" ' +
        '                                       onclick="exportAs(\'pdf\'); return false;" class="m-nav__link">' +
        '                                        <i class="m-nav__link-icon fa fa-file-pdf-o m--font-danger"></i>' +
        '                                        <span class="m-nav__link-text" style="text-transform: none;">' +
        '                                          PDF File' +
        '                                        </span>' +
        '                                    </a>' +
        '                                </li>' +
        '                            </ul>' +
        '                        </div>' +
        '                    </div>' +
        '                </div>' +
        '            </div>' +
        '        </div>';

    $(dropdown).appendTo("#payroll-cash-advance-report_wrapper .exportDropdown");
    dropdownEl = $(".m-dropdown__toggle.export-as");
    // const filterDiv = $('<div>').addClass('dataTables_filter');
    // const searchInput = $('<input>').attr('type', 'text').addClass('form-control').attr('placeholder', 'Search...').attr('id', 'generalSearch');
    // filterDiv.append(searchInput);
    // $(filterDiv).appendTo("#payroll-cash-advance-report_wrapper .exportSearch");
    // $('#generalSearch').donetyping(function(callback) {
    //     search_val = $(this).val();
    //     tblHrisAgeReport.ajax.reload();
    //   },1000,3);
    //   $("#generalSearch").on('keyup', function (e) {
    //     var val = $(this).val();
    //     if (val == ""){
    //         search_val="";
    //         ca_report_table.ajax.reload();
    //     }
    // });
    },
});

$('#search-payroll-cash-advance-report')
.donetyping(function () {
    ca_report_table.ajax.reload();
});

$('#date_range').daterangepicker({
    showDropdowns: true,
    autoUpdateInput: false,
    minDate: moment().subtract(3, 'years').startOf('day'),
    maxDate: moment(),  
    locale: {
        format: 'MMM DD, YYYY',
        cancelLabel: 'Clear'
    }
});

$('#date_range').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(
        picker.startDate.format('MMM DD, YYYY') + ' - ' + picker.endDate.format('MMM DD, YYYY')
    );
    selected = {
        start: picker.startDate.format('YYYY-MM-DD'),
        end: picker.endDate.format('YYYY-MM-DD')
    };
    ca_report_table.ajax.reload();
});

$('#date_range').on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');  
    selected = {}; 
    ca_report_table.ajax.reload();
});

function exportAs(type) {
    dropdownEl.addClass("m-btn--custom m-loader m-loader--light m-loader--left");
    setTimeout(() => {
        switch (type) {
            case "excel":
                ca_report_table.button(".buttons-excel").trigger();
                break;
            case "pdf":
                ca_report_table.button(".buttons-pdf").trigger();
                break;
        }
    }, 150);
}