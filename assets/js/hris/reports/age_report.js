let dropdownEl = null;
let search_val = "";
let thisMonth = moment();
let _companies = [], _stations = [], _departments =[];
let station = 0;
let company = 0;
let department = 0;
let dateRange ="";
if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){ _companies = _tempContentData.company; }
    if(typeof _tempContentData.station !== "undefined" && _tempContentData.station.length > 0){ _stations = _tempContentData.station; }
    if(typeof _tempContentData.department !== "undefined" && _tempContentData.department.length > 0){ _departments = _tempContentData.department; }
}

const tblHrisAgeReport = $('#hris_age_reports')
    .DataTable({
        dom: "<'row mb-3'<'col-xl-3 col-lg-3 col-md-3 col-sm-12 exportDropdown'><'col-xl-9 col-lg-9 col-md-9 col-sm-12 p-0 exportSearch'f>>" +
            "<'row'<'col-12'rt>>" +
            "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'l><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'CASH ADVANCE REPORTS',
                    exportOptions: {
                        columns: [1, 2,],
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
                        columns: [1, 2,],
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
                        columns: [1, 2,],
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
            url: baseUrl('hris/reports/get_age_report'),
            type: 'post',
            dataType: 'json',
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                d.company = company;
                d.station = station;
                d.department = department;
                d.dateRange = dateRange;
            }
        },
        columns: [
            { data: 'empid', visible: false },
            {
                data: 'lastname', width: '15%',
                title: 'EMPLOYEE NAME',
                render: function(data, type, row, meta) {
                    const middleInitial = row['middlename'] ? 
                        row['middlename'].charAt(0) + '.' : '';
                    
                    const suffix = row['suffix'] ? 
                        ' ' + row['suffix'] : '';
            
                    return `${row['firstname']} ${middleInitial}${middleInitial ? ' ' : ''}${row['lastname']}${suffix}`;
                }
            },
            { data: 'company', title: 'COMPANY',},
            { data: 'department', title: 'DEPARTMENT',},
            { data: 'position', title: 'Position',},
            { data: 'station', title: 'Station',},
            {
                data: 'birthday',
                title: 'Age',
                render: function(data, type, row, meta) {
                    return calculateAge(data);
                }
            },            
            {
                data: 'birthday',
                title: 'Birthdate',
                render: function(data, type, row, meta) {
                    return formatDate(data);
                }
            },
            {
                data: 'hired_date',
                title: 'Date hired',
                render: function(data, type, row, meta) {
                    return formatDate(data);
                }
            },
            { data: 'tin_no', title: 'TIN',},
            { data: 'sss_no', title: 'SSS',},
            { data: 'pagibig_no', title: 'PAG-IBIG',},
            { data: 'phealth_no', title: 'PhilHealth',},
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

        $(dropdown).appendTo("#hris_age_reports_wrapper .exportDropdown");
        dropdownEl = $(".m-dropdown__toggle.export-as");
        const filterDiv = $('<div>').addClass('dataTables_filter');
        const searchInput = $('<input>').attr('type', 'text').addClass('form-control').attr('placeholder', 'Search...').attr('id', 'generalSearch');
        filterDiv.append(searchInput);
        $(filterDiv).appendTo("#hris_age_reports_wrapper .exportSearch");
        $('#generalSearch').donetyping(function(callback) {
            search_val = $(this).val();
            tblHrisAgeReport.ajax.reload();
          },1000,3);
          $("#generalSearch").on('keyup', function (e) {
            var val = $(this).val();
            if (val == ""){
                search_val="";
                tblHrisAgeReport.ajax.reload();
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
                    tblHrisAgeReport.button(".buttons-excel").trigger();
                    break;
                case "pdf":
                    tblHrisAgeReport.button(".buttons-pdf").trigger();
                    break;
                case "print":
                    tblHrisAgeReport.button(".buttons-print").trigger();
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
                total : totalRecords,
            },
            success: function (response) {
                $.fn.dataTable.ext.buttons[type].action.call(self, e, dt, node, config);
            }
        });
        return result;
    }


    function formatDate(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '';
    
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        
        return date.toLocaleDateString('en-US', options);
    }

    function calculateAge(birthdate) {
        if (!birthdate) return '';
        
        const birthDate = new Date(birthdate);
        if (isNaN(birthDate.getTime())) return '';
    
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        
        // Adjust age if birthday hasn't occurred this year
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        return `${age} Years Old`;
    }


    $("#company").select2({
        width: '100%',
        data: _companies,
        placeholder: 'Select an option',
        allowClear: true,
    }).on("select2:select", function(e){
        const { id } = e.params.data;
        company = id;
    });

    $("#station").select2({
        width: "100%",
        placeholder: "Select an option",
        data: _stations,
        allowClear: true,
    }).on("select2:select", function(e){
        const { id } = e.params.data;
        station = id;
    });

    $("#department").select2({
        width: "100%",
        placeholder: "Select an option",
        data: _departments,
        allowClear: true,
    }).on("select2:select", function(e){
        const { id } = e.params.data;
        department = id;
    });