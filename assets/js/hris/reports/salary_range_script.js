let dropdownEl = null;
let search_val = "";
let tblEmployeeSalaryRange = $('#table-employee-salary-range')
    .DataTable({
        dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 exportDropdown'><'col-xl-6 col-lg-6 col-md-6 col-sm-12'f>>" +
            "<'row'<'col-12'rt>>" +
            "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'l><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'EMPLOYEES SALARY RANGE REPORT',
            },
            {
                extend: 'pdfHtml5',
                title: 'EMPLOYEES SALARY RANGE REPORT',
            },
            {
                extend: 'print',
                title: 'EMPLOYEES SALARY RANGE REPORT',
            }
        ],
        serverSide: false,
        ordering: true,
        searching: false,
        initComplete: function () {
            $("#table-employee-salary-range_filter input[type='search']")
                .removeClass("form-control-sm");

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
                '                                       class="m-nav__link">' +
                '                                       <i class="m-nav__link-icon fa fa-file-excel-o m--font-success"></i>' +
                '                                       <span class="m-nav__link-text" style="text-transform: none;">' +
                '                                           Excel File' +
                '                                       </span>' +
                '                                    </a>' +
                '                                </li>' +
                '                                <li class="m-nav__item">' +
                '                                    <a id="export-as-pdf" style="cursor:pointer;" ' +
                '                                       class="m-nav__link">' +
                '                                       <i class="m-nav__link-icon fa fa-file-pdf-o m--font-danger"></i>' +
                '                                       <span class="m-nav__link-text" style="text-transform: none;">' +
                '                                          PDF File' +
                '                                       </span>' +
                '                                    </a>' +
                '                                </li>' +
                '                                <li class="m-nav__item">' +
                '                                    <a id="export-as-print" style="cursor:pointer;" ' +
                '                                       class="m-nav__link">' +
                '                                       <i class="m-nav__link-icon fa fa-print m--font-info"></i>' +
                '                                       <span class="m-nav__link-text" style="text-transform: none;">' +
                '                                          Print' +
                '                                       </span>' +
                '                                    </a>' +
                '                                </li>' +
                '                            </ul>' +
                '                        </div>' +
                '                    </div>' +
                '                </div>' +
                '            </div>' +
                '        </div>';

            $(dropdown).appendTo("#table-employee-salary-range_wrapper .exportDropdown");
            dropdownEl = $(".m-dropdown__toggle.export-as");
        }
    });

$('.money').maskMoney({allowZero: true});

$('#employee_status')
    .select2({
        width: '100%',
        placeholder: 'Select Status'
    });


function filterEmployeesOfSalaryRange(form) {
    const _form = $(form);
    const formValues = _form.serializeArray();
    const objFormValues = formValues.reduce((acc, item) => {
        acc[item.name] = item.value;
        return acc;
    }, {});
    const url = baseUrl('hris/reports/get_employees_for_salary_range');

    if (_form.isValid()) {
        $('#table-employee-salary-range').DataTable().destroy();
        tblEmployeeSalaryRange = $('#table-employee-salary-range')
            .DataTable({
                dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 exportDropdown'><'col-xl-6 col-lg-6 col-md-6 col-sm-12 exportSearch'f>>" +
                    "<'row'<'col-12'rt>>" +
                    "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'l><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'EMPLOYEES SALARY RANGE REPORT',
                        action: function (e, dt, node, config) {
                            const self = this;
                            getExportData(e, dt, node, config, self, `${url}/1`, 'excelHtml5')
                                .then(() => {
                                    dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                                });
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'EMPLOYEES SALARY RANGE REPORT',
                        action: function (e, dt, node, config) {
                            const self = this;
                            getExportData(e, dt, node, config, self, `${url}/1`, 'pdfHtml5')
                                .then(() => {
                                    dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                                });
                        }
                    },
                    {
                        extend: 'print',
                        title: 'EMPLOYEES SALARY RANGE REPORT',
                        action: function (e, dt, node, config) {
                            const self = this;
                            getExportData(e, dt, node, config, self, `${url}/1`, 'print')
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
                ajax: {
                    url,
                    type: 'post',
                    dataType: 'json',
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
                        d.filter = objFormValues;
                        d.search['value'] = search_val
                    },
                    global: false
                },
                columns: [
                    {
                        data: 'company'
                    },
                    {
                        data: 'department'
                    },
                    {
                        data: 'employees_name'
                    },
                    {
                        data: 'position'
                    },
                    {
                        data: 'sal_rate',
                        width: '12%',
                        className: 'text-right'
                    },
                    {
                        data: 'sal_remarks'
                    },
                    {
                        data: 'date_start',
                    },
                    {
                        data: 'date_start',
                        render: function(data, type, row, meta){
                            return row.tenureship;
                        }
                    }
                ],
                order: [[2, 'asc']],
                initComplete: function () {
                    $("#table-employee-salary-range_filter input[type='search']")
                        .removeClass("form-control-sm");

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

                    $(dropdown).appendTo("#table-employee-salary-range_wrapper .exportDropdown");
                    dropdownEl = $(".m-dropdown__toggle.export-as");
                    const filterDiv = $('<div>').addClass('dataTables_filter');
                    const searchInput = $('<input>')
                        .attr('type', 'text')
                        .addClass('form-control')
                        .attr('placeholder', 'Search...')
                        .attr('id', 'generalSearch');
                    filterDiv.append(searchInput);
                    $(filterDiv).appendTo("#table-employee-salary-range_wrapper .exportSearch");
                    $('#generalSearch').donetyping(function(callback) {
                        search_val = $(this).val();
                        tblEmployeeSalaryRange.ajax.reload();
                      },1000,3);
                      $("#generalSearch").on('keyup', function (e) {
                        var val = $(this).val();
                        if (val == ""){
                            search_val="";
                            tblEmployeeSalaryRange.ajax.reload();
                        }
                    });
                },
            });
    }
}

function exportAs(type) {
    dropdownEl.addClass("m-btn--custom m-loader m-loader--light m-loader--left");
    let clearHere = false;

    setTimeout(() => {
        switch (type) {
            case "excel":
                tblEmployeeSalaryRange.button(".buttons-excel").trigger();
                break;
            case "pdf":
                tblEmployeeSalaryRange.button(".buttons-pdf").trigger();
                break;
            case "print":
                tblEmployeeSalaryRange.button(".buttons-print").trigger();
                break;
        }
    }, 150);
}

async function getExportData(e, dt, node, config, self, url, type) {
    const data = dt.ajax.params();
    const result = await $.ajax({
        url,
        type: "POST",
        dataType: "JSON",
        data,
        success: function (response) {
            const capitalizedData = response.data.map(row => {
                return Object.keys(row).reduce((acc, key) => {
                    acc[key] = typeof row[key] === 'string' ? row[key].toUpperCase() : row[key];
                    return acc;
                }, {});
            });
            dt.rows().remove();
            dt.rows.add(capitalizedData).draw();
            $.fn.dataTable.ext.buttons[type].action.call(self, e, dt, node, config);
        }
    });

    return result;
}

if (typeof _tempContentData !== "undefined") {
    var tempDropdownData = _tempContentData.dropdown_data;
}

$("#position").select2({
    data: tempDropdownData.dropdown_position,
    placeholder: "SELECT POSITION",
    width: '100%'
})

$("#department").select2({
    data: tempDropdownData.dropdown_department,
    placeholder: "SELECT DEPARTMENT",
    width: '100%'
})
$("#company").select2({
    data: tempDropdownData.dropdown_company,
    placeholder: "SELECT COMPANY",
    width: '100%'
})