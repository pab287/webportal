let dropdownEl = null;
let _years = [];

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
                dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 exportDropdown'><'col-xl-6 col-lg-6 col-md-6 col-sm-12'f>>" +
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
                searching: true,
                ordering: true,
                ajax: {
                    url,
                    type: 'post',
                    dataType: 'json',
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
                        d.filter = objFormValues;
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

    if(typeof _tempContentData.years !== "undefined" && !$.isEmptyObject(_tempContentData.years)){
        _years = _tempContentData.years;
    }
}

var vmData = new Vue({
    el: "#salary-report",
    lang: 'en',
    data: {
        filter: 1,
        company: 0,
        department: 0,
        position: 0,
        employeeStatus: null,
        salaryRangeTable: {},
        formValues: null,
        isGeneratedRange: false
    },
    mounted(){
        var instance = this;
        instance.maskInput();
        instance.employeeStatusSelect2("#employee_status");
        instance.companySelect2("#company", false, tempDropdownData.dropdown_company);
        instance.departmentSelect2("#department", false, tempDropdownData.dropdown_department);
        instance.positionSelect2("#position", false, tempDropdownData.dropdown_position);

        instance.salaryRangeDataTable();
    },
    methods: {
        filterBy(val){
            var instance = this;
            instance.filter = val

            if (val === 1){
                setTimeout(() => {
                    instance.maskInput();
                    instance.salaryRangeDataTable();

                    if (typeof instance.salaryRangeTable !== "undefined" && instance.salaryRangeTable) {
                        var _table = instance.salaryRangeTable;
                        _table.ajax.reload(); 
                    } else {
                        instance.salaryRangeDataTable();
                    }
                }, 100);
            } else {
                setTimeout( function (){
                    instance.yearSelect2("#yearFrom", true, _years);
                    instance.yearSelect2("#yearTo", true, _years);
                    instance.employeeSelect2("#employee", true);
                }, 100);
            }
        }, maskInput(){
            $('.money').maskMoney({allowZero: true});
        }, yearSelect2(targetElement, destroy = false, data = []){
            var instance = this;
            var currentElement = $(targetElement);

            if (destroy) {
                if (currentElement.data("select2")) {
                    currentElement.select2("destroy");
                }
                
                currentElement.empty();
            }

            currentElement.select2({
                width: '100%',
                placeholder: 'Year',
                data: data
            });
        }, employeeStatusSelect2(targetElement, destroy = false){
            var instance = this;
            var currentElement = $(targetElement);

            if (destroy) {
                if (currentElement.data("select2")) {
                    currentElement.select2("destroy");
                }
                currentElement.empty();
            }

            currentElement.select2({
                width: '100%',
                placeholder: 'Select Status',
            }).on('select2:select', function (e) {
                var data = e.params.data;

                instance.employeeStatus = data.id;
            });
        }, companySelect2(targetElement, destroy = false, data = []){
            var instance = this;
            var currentElement = $(targetElement);

            if (destroy) {
                if (currentElement.data("select2")) {
                    currentElement.select2("destroy");
                }
                currentElement.empty();
            }

            currentElement.select2({
                width: '100%',
                placeholder: 'Select Company',
                data: data
            }).on('select2:select', function (e) {
                var data = e.params.data;

                instance.company = data.id;
            });
        }, departmentSelect2(targetElement, destroy = false, data = []){
            var instance = this;
            var currentElement = $(targetElement);

            if (destroy) {
                if (currentElement.data("select2")) {
                    currentElement.select2("destroy");
                }
                currentElement.empty();
            }

            currentElement.select2({
                width: '100%',
                placeholder: 'Select Department',
                data: data
            }).on('select2:select', function (e) {
                var data = e.params.data;

                instance.department = data.id;
            });
        }, positionSelect2(targetElement, destroy = false, data = []){
            var instance = this;
            var currentElement = $(targetElement);

            if (destroy) {
                if (currentElement.data("select2")) {
                    currentElement.select2("destroy");
                }
                currentElement.empty();
            }

            currentElement.select2({
                width: '100%',
                placeholder: 'Select Position',
                data: data
            }).on('select2:select', function (e) {
                var data = e.params.data;

                instance.position = data.id;
            });
        }, employeeSelect2(targetElement, destroy = false){
            var instance = this;
            var currentElement = $(targetElement);

            if (destroy) {
                if (currentElement.data("select2")) {
                    currentElement.select2("destroy");
                }
                currentElement.empty();
            }

            currentElement.select2({
                width: '100%',
                placeholder: 'Select Employee',
                minimumInputLength: 3,
                ajax: {
                    url: baseUrl('hris/reports/get_employee_select2_data'),
                    global: false,
                    data: function(term, page) {
                        return {
                            search: term,
                            company_id: instance.company,
                            department_id: instance.department,
                            position_id: instance.position,
                            employee_status: instance.employeeStatus
                        };
                    },
                    processResults: function (data) {
                        return data;
                    },
                    delay: 500
                },
            });
        }, salaryRangeDataTable(){
            var instance = this;

            var url = baseUrl('hris/reports/get_employees_for_salary_range');

            var table = $('#table-employee-salary-range').DataTable({
                dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 exportDropdown'><'col-xl-6 col-lg-6 col-md-6 col-sm-12'f>>" +
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
                ordering: true,
                retrieve: true,
                deferLoading: 0,
                searching: false,
                ajax: {
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
                        d.filter = instance.formValues;
                        d.isGenerated = instance.isGeneratedRange;
                    },
                    global: false
                },
                columns: [
                    { data: 'company' },
                    { data: 'department'},
                    { data: 'employees_name' },
                    { data: 'position' },
                    { data: 'sal_rate', width: '12%', className: 'text-right' },
                    { data: 'sal_remarks' },
                    { data: 'date_start' },
                    {
                        data: 'date_start',
                        render: function(data, type, row, meta){
                            return row.tenureship;
                        }
                    }
                ],
                order: [[2, 'asc']],
                initComplete: function (settings) {
                    const nTable = settings.nTable;

                    $("#table-employee-salary-range_filter input[type='search']").removeClass("form-control-sm");

                    const dropdown = `` +
                        `       <div class="m-dropdown m-dropdown--inline m-dropdown--align-left" data-dropdown-toggle="hover" aria-expanded="true">` +
                        `            <button class="m-dropdown__toggle btn btn-success dropdown-toggle export-as">  EXPORT AS </button>` +
                        `            <div class="m-dropdown__wrapper">` +
                        `                <div class="m-dropdown__inner">` +
                        `                    <div class="m-dropdown__body">` +
                        `                        <div class="m-dropdown__content">` +
                        `                            <ul class="m-nav">` +
                        `                                <li class="m-nav__item">` +
                        `                                    <div id="export-as-excel" style="cursor:pointer;" class="m-nav__link">` +
                        `                                        <i class="m-nav__link-icon fa fa-file-excel-o m--font-success"></i>` +
                        `                                        <span class="m-nav__link-text" style="text-transform: none;">Excel File</span>` +
                        `                                    </div>` +
                        `                                </li>` +
                        `                                <li class="m-nav__item">` +
                        `                                    <div href="javascript:void(0)" id="export-as-pdf" style="cursor:pointer;" class="m-nav__link">` +
                        `                                        <i class="m-nav__link-icon fa fa-file-pdf-o m--font-danger"></i>` +
                        `                                        <span class="m-nav__link-text" style="text-transform: none;">PDF File</span>` +
                        `                                    </div>` +
                        `                                </li>` +
                        `                                <li class="m-nav__item">` +
                        `                                    <div href="javascript:void(0)" id="export-as-print" style="cursor:pointer;" class="m-nav__link">` +
                        `                                        <i class="m-nav__link-icon fa fa-print m--font-info"></i>'` +
                        `                                        <span class="m-nav__link-text" style="text-transform: none;">Print</span>` +
                        `                                    </div>` +
                        `                                </li>` +
                        `                            </ul>` +
                        `                        </div>` +
                        `                    </div>` +
                        `                </div>` +
                        `            </div>` +
                        `       </div>`;

                    $(dropdown).appendTo("#table-employee-salary-range_wrapper .exportDropdown");
                    dropdownEl = $(".m-dropdown__toggle.export-as");


                    const tempExcel = $("#export-as-excel");
                    const tempPdf = $("#export-as-pdf");
                    const tempPrint = $("#export-as-print");

                    if (typeof tempExcel != 'undefined' && tempExcel.length > 0) {
                        tempExcel.on("click", function () {
                            table.button(".buttons-excel").trigger();

                        });
                    }

                    if (typeof tempPdf != 'undefined' && tempPdf.length > 0) {
                        tempPdf.on("click", function () {
                            table.button(".buttons-pdf").trigger();

                        });
                    }
                    if (typeof tempPrint != 'undefined' && tempPrint.length > 0) {
                        tempPrint.on("click", function () {
                            table.button(".buttons-print").trigger();

                        });
                    }
                }
            });
            instance.salaryRangeTable = table;
        }, generateReport(){
            var instance = this;

            var _form = $('#generate-report');

            if (_form.isValid()) {
                const formValues = _form.serializeArray();
                let _data = {};

                $.each(formValues, (acc, item) => {
                    if (item.name != 'sal_range_from' || item.name != 'sal_range_to')
                        _data[item.name] = item.value ? item.value : 0;
                });

                instance.formValues = _data;

                if (instance.filter === 1) {
                    var salaryTable = instance.salaryRangeTable;
                    instance.isGeneratedRange = true;
                    salaryTable.ajax.reload();
                } else {

                }
            }
        }, salaryHistoryDatatable(){
            var instance = this;
            instance.salaryHistoryTable = $('#table-employee-salary-history').DataTable({
                dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 exportDropdown'><'col-xl-6 col-lg-6 col-md-6 col-sm-12'f>>" +
                    "<'row'<'col-12'rt>>" +
                    "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'l><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'EMPLOYEES SALARY HISTORY REPORT',
                        action: function (e, dt, node, config) {
                            const self = this;
                            getExportData(e, dt, node, config, self, `${baseUrl('hris/reports/get_employees_for_salary_history')}/1`, 'excelHtml5')
                                .then(() => {
                                    dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                                });
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'EMPLOYEES SALARY HISTORY REPORT',
                        action: function (e, dt, node, config) {
                            const self = this;
                            getExportData(e, dt, node, config, self, `${baseUrl('hris/reports/get_employees_for_salary_history')}/1`, 'pdfHtml5')
                                .then(() => {
                                    dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                                });
                        }
                    }]
            });
        }
    }
});