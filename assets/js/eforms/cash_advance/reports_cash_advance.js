let dropdownEl = null;
let dateRange = "";
let thisMonth = moment();
let _filter = 0;
let = tblCashAdvanceReport = $('#cash_advance_reports')
    .DataTable({
        dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 exportDropdown'><'col-xl-6 col-lg-6 col-md-6 col-sm-12 p-0 daterange'f>>" +
            "<'row'<'col-12'rt>>" +
            "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'><'col-xl-6 col-lg-6 col-md-6 col-sm-12'>>",
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'CASH ADVANCE REPORTS',
                    exportOptions: {
                        columns: [2, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15],
                        format: {
                            body: function(data, row, column, node) {
                                return data.toString().replace(/<[^>]*>/g, '').toUpperCase();
                            }
                        }
                    },
                    action: function(e, dt, node, config) {
                        const self = this;
                        getExportData(e, dt, node, config, self, baseUrl('eforms/cash_advance/export_report/') + 'excel', 'excelHtml5')
                            .then(() => {
                                dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                            });
                    },
                },
                {
                    extend: 'pdfHtml5',
                    title: 'CASH ADVANCE REPORTS',
                    exportOptions: {
                        columns: [2, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15],
                        format: {
                            body: function(data, row, column, node) {
                                return data.toString().replace(/<[^>]*>/g, '').toUpperCase();
                            }
                        }
                    },
                    customize: function(doc) {
                        doc.defaultStyle.fontSize = 6;  // Reduced from 8 to 6
                        doc.pageOrientation = 'landscape';
                        
                        doc.pageSize = 'A4';
                        
                        var table = doc.content[1].table;
                        var colCount = table.body[0].length;
                        
                        var columnWidths = new Array(colCount).fill('auto');
                        doc.content[1].table.widths = columnWidths;
                        
                        doc.pageMargins = [10, 10, 10, 10]; // [left, top, right, bottom]
                        
                        doc.styles.tableHeader = {
                            fontSize: 6,
                            bold: true,
                            fillColor: '#f3f3f3',
                            alignment: 'center'
                        };
                        
                        doc.styles.tableBodyEven = {
                            fontSize: 6
                        };
                        
                        doc.styles.tableBodyOdd = {
                            fontSize: 6
                        };
                        
                        doc.content[1].table.keepWithHeaderRows = 1;
                        doc.content[1].layout = {
                            hLineWidth: function(i, node) { return 0.1; },
                            vLineWidth: function(i, node) { return 0.1; },
                            fillColor: function(i, node) { return (i % 2 === 0) ? '#f3f3f3' : null; }
                        };
                    },
                    action: function(e, dt, node, config) {
                        const self = this;
                        getExportData(e, dt, node, config, self, baseUrl('eforms/cash_advance/export_report/') + 'pdf', 'pdfHtml5')
                            .then(() => {
                                dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                            });
                    },
                    orientation: 'landscape'
                },
                {
                    extend: 'print',
                    title: 'CASH ADVANCE REPORTS',
                    exportOptions: {
                        columns: [2, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15],
                        format: {
                            body: function(data, row, column, node) {
                                return data.toString().replace(/<[^>]*>/g, '').toUpperCase();
                            }
                        }
                    },
                    customize: function(win) { 
                        var css = '@page { size: landscape; }' +
                                  'table { font-size: 6pt; width: 100% }' +
                                  'table thead th { background-color: #f3f3f3; text-align: center; font-weight: bold; }' +
                                  'table tbody tr:nth-child(even) { background-color: #f3f3f3; }' +
                                  'h1 { font-size: 12pt; text-align: center; margin: 10px 0; }'  +
                                  'table th, table td { padding: 2px; border: 0.1pt solid #ddd; }';
                        
                        $(win.document.head).append('<style>' + css + '</style>');

                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', '6pt')
                            .css('border-collapse', 'collapse')
                            .css('width', '100%');
                        $(win.document.body).find('h1')
                            .css('text-align', 'center')
                            .css('font-size', '12pt')
                            .css('margin', '10px 0');
                    },
                    action: function(e, dt, node, config) {
                        const self = this;
                        getExportData(e, dt, node, config, self, baseUrl('eforms/cash_advance/export_report/') + 'print', 'print')
                            .then(() => {
                                dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                            });
                    },
                    orientation: 'landscape'
                }
            ],
        serverSide: true,
        ordering: true,
        searching: false,
        processing: true,
        order:[0,'desc'],
        ajax: {
            url: baseUrl('eforms/cash_advance/get_cash_advance_reports'),
            type: 'post',
            dataType: 'json',
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.dateRange = dateRange;
                d._filter = _filter;
            }
        },
        columns: [
            { data: 'id', visible: false },
            {
                data: 'name', title: 'employee', width: '15%',
                orderable: false,
                render: function (data, type, row) {
                    let tempHtml = `<p class='mb-0 m--font-bolder'>${data}</p>`;
                    tempHtml += `<p class='mb-1'><small><span class="m--font-bolder"></span> ${row.position} </small></p>`;
                    tempHtml += `<p class='m-0'><small><span class="m--font-bolder">Company:</span> ${row.company} </small></p>`;
                    tempHtml += `<p class='m-0'><small><span class="m--font-bolder">Department:</span> ${row.department}</small></p>`;
                    return tempHtml;
                }
            },
            { data: 'name', visible: false, title: 'EMPLOYEE NAME',},
            { data: 'lastname', visible: false,title: 'LASTNAME', },
            { data: 'department', visible: false, title: 'DEPARTMENT', },
            { data: 'position', visible: false, title: 'POSITION', },
            { data: 'company', visible: false, title: 'COMPANY', },
            { data: 'amt_approved', orderable: false, title: 'AMOUNT APPROVED' ,
                render: function(data) {
                    return Number(data).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
                }
            },
            // {
            //     data: 'total_charges', title: 'COMPANY LOAN',
            //     orderable: false,
            //     render: function(data) {
            //         return Number(data).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
            //     }
            // },
            { data: 'amt_to_b_deducted', title: 'DEDUCTION TYPE', 
                render: function(data, type, row, meta) {
                    return row.deduct_type == 'percentage' ? `${data}%` : Number(data).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
                }
            },
            {
                data: 'total_deduction', title: 'TOTAL DEDUCTION',
                orderable: false,
                render: function (data) {
                    return Number(data).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
                }
            },
            {
                data: 'rembalance', title: 'REMAINING BAL',
                orderable: false,
                render: function (data) {
                    return Number(data).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
                }
            },
            // { data: "med_loan", orderable: false,  title: 'MEDICAL LOAN',
            //     render: function(data) {
            //         return Number(data).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
            //     }
            // },
            // { data: 'sss_loan', orderable: false, title: 'SSS',
            //     render: function(data) {
            //         return Number(data).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
            //     }
            // },
            // { data: 'hdmf_loan', orderable: false, title: 'HDMF LOAN',
            //     render: function(data) {
            //         return Number(data).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
            //     }
            // },
            { data: 'purpose', orderable: false, title: 'PURPOSE', width: '15%' },
            { data: 'approved_by', orderable: false, width: '15%', class:"text-left", title: 'APPROVED BY',
                render: function (data, type, row, meta) {
                    let html = ``;

                    html += `<h6>${data}</h6>`;
                    html += `<p class="m--font_bolder">${moment(row.approved_dt).format('MMM DD, YYYY')}</p>`;

                    return html ;
                }
            },
            { data: 'approved_by', orderable: false, visible: false, width: '15%', class:"text-left", title: 'APPROVED BY' },
            { data: 'approved_dt', visible: false, orderable: false, width: '*', title: 'APPROVED DATE',
                render: function (data, type, row) {
                    return moment(data).format('MMM DD, YYYY')
                }
            },
            { data: 'date_created', orderable: false, width: '*', title: 'DATE CREATED',
                render: function (data, type, row) {
                    return moment(data).format('MMM DD, YYYY')
                }
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
            '                                <li class="m-nav__item">' +
            '                                    <a id="export-as-print" style="cursor:pointer;" ' +
            '                                       onclick="exportAs(\'print\'); return false;" class="m-nav__link">' +
            '                                        <i class="m-nav__link-icon fa fa-print m--font-info"></i>' +
            '                                        <span class="m-nav__link-text" style="text-transform: none;">' +
            '                                          Print' +
            '                                        </span>' +
            '                                    </a>' +
            '                                </li>' +
            '                            </ul>' +
            '                        </div>' +
            '                    </div>' +
            '                </div>' +
            '            </div>' +
            '        </div>';

        $(dropdown).appendTo("#cash_advance_reports_wrapper .exportDropdown");
        dropdownEl = $(".m-dropdown__toggle.export-as");

        const dateRangePicker = `
                        <div class="row align-items-center">
                            <div class="col-6 col-md-6 col-sm-12">
                                <div class="m-form__group row">
                                    <label class="col-3 col-form-label">Filter By: </label>
                                    <div class="col-9">
                                        <div class="m-radio-inline">
                                            <label class="m-radio">
                                                <input type="radio" name="filter" value="0" checked> All
                                                <span></span>
                                            </label>
                                            <label class="m-radio">
                                                <input type="radio" name="filter" value="1"> Active
                                                <span></span>
                                            </label>
                                            <label class="m-radio">
                                                <input type="radio" name="filter" value="2"> Inactive
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-md-4 col-sm-12">
                                <div id="filter-by-date-range">
                                    <div id="date-picker" class="input-group">
                                        <input type="text" readonly="readonly" placeholder="SELECT DATE RANGE" id="date-range" name="date_range" data-validation="required" class="form-control m-input valid"> 
                                            <span class="input-group-addon"><i class="la la-calendar-check-o"></i>
                                            </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2 col-md-2 col-sm-12">
                                <button class="btn btn-success btnView" onclick="generateTable()">Submit</button>
                            </div>
                        </div>`;
        $(dateRangePicker).appendTo("#cash_advance_reports_wrapper .daterange");

        $("#date-picker").daterangepicker({
            startDate: thisMonth.clone().startOf('month'),
            endDate: thisMonth,
            maxDate: moment().format("MM/DD/YYYY"),
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            locale: { format: 'MM/DD/YYYY' },
        }).on('apply.daterangepicker', function (ev, picker) {
            $("#date-range").val(picker.startDate.format('MMM. DD, YYYY') + ' - ' + picker.endDate.format('MMM. DD, YYYY')).trigger('change');
            dateRange = picker.startDate.format('YYYY-MM-DD') + ' | ' + picker.endDate.format('YYYY-MM-DD');
            // tblCashAdvanceReport.ajax.reload();
        }).on('cancel.daterangepicker', function(ev, picker) {
            $("#date-range").val('').trigger('change');
            dateRange = ''; 
            // tblCashAdvanceReport.ajax.reload();
        });

        },
    });

    function exportAs(type) {
        dropdownEl.addClass("m-btn--custom m-loader m-loader--light m-loader--left");
        let clearHere = false;
    
        setTimeout(() => {
            switch (type) {
                case "excel":
                    tblCashAdvanceReport.button(".buttons-excel").trigger();
                    break;
                case "pdf":
                    tblCashAdvanceReport.button(".buttons-pdf").trigger();
                    break;
                case "print":
                    tblCashAdvanceReport.button(".buttons-print").trigger();
                    break;
            }
        }, 150);
    }

    async function getExportData(e, dt, node, config, self, url, type) {
        const info = dt.page.info();
        const totalRecords = info.recordsTotal;
        const result = await $.ajax({
            url,
            type: "POST",
            dataType: "JSON",
            data: {
                csrf_token : _csrf_hash,
                dateRange : dateRange,
                total : totalRecords,
            },
            success: function (response) {
                $.fn.dataTable.ext.buttons[type].action.call(self, e, dt, node, config);
            }
        });
        return result;
    }

    function generateTable(){
        _filter = $("input[name='filter']:checked").val();
        
        tblCashAdvanceReport.ajax.reload();
    }