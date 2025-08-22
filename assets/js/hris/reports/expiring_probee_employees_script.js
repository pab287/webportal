let tblExpiringProbees = null;
let dropdownEl = null;
let search_val = "";
tblExpiringProbees = $('#table-expiring-probees')
    .DataTable({
        dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 exportDropdown'><'col-xl-6 col-lg-6 col-md-6 col-sm-12 exportSearch'f>>" +
            "<'row'<'col-12'rt>>" +
            "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'l><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'EXPIRING PROBATIONARY EMPLOYEES',
                action: function (e, dt, node, config) {
                    const self = this;
                    getExportData(e, dt, node, config, self, baseUrl('hris/reports/get_expiring_employees/1/?work_status=PROBATIONARY'), 'excelHtml5')
                        .then(() => {
                            dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                        });
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'EXPIRING PROBATIONARY EMPLOYEES',
                action: function (e, dt, node, config) {
                    const self = this;
                    getExportData(e, dt, node, config, self, baseUrl('hris/reports/get_expiring_employees/1/?work_status=PROBATIONARY'), 'pdfHtml5')
                        .then(() => {
                            dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                        });
                },
                orientation: 'landscape',
                customize: function (doc) {
                    doc.defaultStyle.fontSize = 8;
                    doc.styles.tableHeader.fontSize = 10;
                    doc.pageMargins = [20, 20, 20, 20];
                },
            },
            {
                extend: 'print',
                title: 'EXPIRING PROBATIONARY EMPLOYEES',
                customize: function (win) {
                    const css = `
                        @page { 
                            size: A4 landscape; 
                            margin: 10mm; 
                        }
                        body {
                            font-size: 10px;
                        }
                        table {
                            font-size: 10px;
                        }
                    `;

                    const head = win.document.head || win.document.getElementsByTagName('head')[0];
                    const style = win.document.createElement('style');
                    style.type = 'text/css';
                    style.appendChild(win.document.createTextNode(css));
                    head.appendChild(style);
                },
                action: function (e, dt, node, config) {
                    const self = this;
                    getExportData(e, dt, node, config, self, baseUrl('hris/reports/get_expiring_employees/1/?work_status=PROBATIONARY'), 'print')
                        .then(() => {
                            dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                        });
                }
            }
        ],
        serverSide: true,
        processing: true,
        searching: false,
        ordering: true,
        destroy: true,
        ajax: {
            url: baseUrl('hris/reports/get_expiring_employees/?work_status=PROBATIONARY'),
            type: 'post',
            dataType: 'json',
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val
            },
            global: false
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: 'text-center'
            },
            {
                data: 'company',
                width: '10%'
            },
            {
                data: 'position',
                width: '10%'
            },
            {data: 'idno'},
            {data: 'name'},
            {data: 'head', orderable: false},
            {
                data: 'date_hired',
                render: function (data) {
                    return moment(data).format('MMM DD, YYYY')
                }
            },
            {
                data: 'firstEvaluation',
                render: function (data) {
                    return moment(data).format('MMM DD, YYYY')
                }
            },
            {
                data: 'finalEvaluation',
                render: function (data) {
                    return moment(data).format('MMM DD, YYYY')
                }
            },
            {
                data: 'end_of_contract',
                render: function (data) {
                    return data && data != "0000-00-00" ? moment(data).format('MMM DD, YYYY') : "";
                }
            },
            {
                data: 'daysBeforeEvaluation',
                className: 'text-right',
                render: function (data) {
                    return parseFloat(data) < 0 ? '<span class="text-danger m--font-bolder">' + data + '</span>' : '<span class="font-weight-bold">' + data + '</span>'
                }
            },
        ],
        columnDefs: [{
            searchable: false,
            orderable: false,
            targets: 0
        }],
        order: [[6, 'asc']],
        pageLength: 50,
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

            $(dropdown).appendTo("#table-expiring-probees_wrapper .exportDropdown");
            dropdownEl = $(".m-dropdown__toggle.export-as");
            const filterDiv = $('<div>').addClass('dataTables_filter');
            const searchInput = $('<input>')
                .attr('type', 'text')
                .addClass('form-control')
                .attr('placeholder', 'Search...')
                .attr('id', 'generalSearch');
            filterDiv.append(searchInput);
            $(filterDiv).appendTo("#table-expiring-probees_wrapper .exportSearch");
            $('#generalSearch').donetyping(function(callback) {
                search_val = $(this).val();
                tblExpiringProbees.ajax.reload();
              },1000,3);
              $("#generalSearch").on('keyup', function (e) {
                var val = $(this).val();
                if (val == ""){
                    search_val="";
                    tblExpiringProbees.ajax.reload();
                }
            });
        },
    });

function exportAs(type) {
    dropdownEl.addClass("m-btn--custom m-loader m-loader--light m-loader--left");
    let clearHere = false;

    setTimeout(() => {
        switch (type) {
            case "excel":
                tblExpiringProbees.button(".buttons-excel").trigger();
                break;
            case "pdf":
                tblExpiringProbees.button(".buttons-pdf").trigger();
                break;
            case "print":
                tblExpiringProbees.button(".buttons-print").trigger();
                break;
        }
    }, 150);
}

async function getExportData(e, dt, node, config, self, url, type) {
    const data = dt.ajax.params();
    data['exportType'] = type;
    const result = await $.ajax({
        url,
        type: "POST",
        dataType: "JSON",
        data,
        success: function (response) {
            $.fn.dataTable.ext.buttons[type].action.call(self, e, dt, node, config);
        }
    });

    return result;
}

