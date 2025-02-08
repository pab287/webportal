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
        salaryHistoryTable: {},
        instanceColumn: [],
        instanceData: [],
        selectedFrom: moment().format("YYYY"),
        selectedTo: moment().format("YYYY"),
        generatedCompany: null,
        generatedPosition: null,
        generatedDepartment: null,
        generatedEmployee: null
    },
    mounted(){
        var instance = this;
        instance.maskInput();
        instance.employeeStatusSelect2("#employee_status");
        instance.companySelect2("#company", false, tempDropdownData.dropdown_company);
        instance.departmentSelect2("#department", false, tempDropdownData.dropdown_department);
        instance.positionSelect2("#position", false, tempDropdownData.dropdown_position);

        instance.salaryRangeDataTable();

        // var _temp = [
        //     { title: 'Name', data: 'name' },
        //     { title: 'Biometric #', data: 'biometricno' },
        //     { title: 'Year', data: 'year' },
        //     { title: 'Salary', data: 'salary' },
        //     { title: 'Eff Date', data: 'effdate' },
        //     { title: 'Year', data: 'year' },
        //     { title: 'Salary', data: 'salary' },
        //     { title: 'Eff Date', data: 'effdate' },
        // ];

        // instance.instanceColumn = _temp;
        // instance.salaryHistoryDataTable();     
        // instance.employeeSelect2("#employee", true);
        // instance.yearSelect2("#yearFrom", true, _years);

        // $("#yearTo").prop('disabled', true);
        // instance.yearSelect2("#yearTo", true, _years);
    },
    methods: {
        filterBy(val){
            var instance = this;
            instance.filter = val

            if (val === 1){
                setTimeout(() => {
                    instance.maskInput();
                    instance.salaryRangeDataTable();

                    if (typeof instance.salaryRangeTable !== "undefined" && instance.salaryRangeTable.data().length > 0) {
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

                    var _temp = [
                        { title: 'Name', data: 'name' },
                        { title: 'Biometric #', data: 'biometricno' },
                        { title: 'Year', data: 'year' },
                        { title: 'Salary', data: 'salary' },
                        { title: 'Eff Date', data: 'effdate' },
                        { title: 'Year', data: 'year' },
                        { title: 'Salary', data: 'salary' },
                        { title: 'Eff Date', data: 'effdate' },
                    ];

                    instance.instanceColumn = _temp;
                    instance.salaryHistoryDataTable();
                    instance.employeeSelect2("#employee", true);
                    instance.yearSelect2("#yearFrom", true, _years);

                    $("#yearTo").prop('disabled', true);
                    instance.yearSelect2("#yearTo", true, _years);
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
                
                currentElement.off('select2:select');
                currentElement.empty();
            }

            currentElement.select2({
                width: '100%',
                placeholder: 'Year',
                data: data
            }).on('select2:select', function (e) {
                var _data = e.params.data;

                var tempYear = [];
                if (targetElement === "#yearFrom") {
                    instance.selectedFrom = _data.id;

                    $.each(data, function (key, value) {
                        if (value.id >= _data.id)  {
                            tempYear.push(value);
                        }
                    });

                    $("#yearTo").prop('disabled', false);
                    instance.yearSelect2("#yearTo", true, tempYear);
                } else {
                    instance.selectedTo = _data.id;
                }
                
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
                data: data,
                allowClear: true,
            }).on('select2:select', function (e) {
                var data = e.params.data;

                instance.company = data.id;
            }).on('select2:unselect', function (e) {
                instance.position = 0;
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
                data: data,
                allowClear: true,
            }).on('select2:select', function (e) {
                var data = e.params.data;

                instance.department = data.id;
            }).on('select2:unselect', function (e) {
                instance.position = 0;
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
                data: data,
                allowClear: true,
            }).on('select2:select', function (e) {
                var data = e.params.data;

                instance.position = data.id;

                if (typeof $('#employee') != 'undefined') {
                    instance.employeeSelect2("#employee", true);
                }
            }).on('select2:unselect', function (e) {
                instance.position = 0;
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
                allowClear: true,
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
            }).on('select2:select', function (e) {
                var data = e.params.data;

                var temp = [];
                temp.push(data.id);

                instance.generatedEmployee = temp;
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
                            getExportData(e, dt, node, config, self, `${url}/1`, 'excelHtml5')
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
                            getExportData(e, dt, node, config, self, `${url}/1`, 'excelHtml5')
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
                searching: true,
                ajax: {
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
                        d.filter = instance.formValues;
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
                    var _tableData = settings.aoData.length;

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
                            if (_tableData > 0) { 
                                table.button(".buttons-excel").trigger();
                            } else {
                                toastr.warning('Generate range report first before exporting it to excel.', 'Salary Range Export to Excel', 10000);
                            }
                        });
                    }

                    if (typeof tempPdf != 'undefined' && tempPdf.length > 0) {
                        tempPdf.on("click", function () {
                            if (_tableData > 0) {
                                table.button(".buttons-pdf").trigger();
                            } else {
                                toastr.warning('Generate range history report first before exporting it to pdf.', 'Salary Range Export to PDF', 10000);
                            }
                        });
                    }

                    if (typeof tempPrint != 'undefined' && tempPrint.length > 0) {
                        tempPrint.on("click", function () {
                            if (_tableData > 0) {
                                table.button(".buttons-print").trigger();
                            } else {
                                toastr.warning('Generate range history report first before printing it.', 'Salary Range Print', 10000);
                            }
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
                
                // Convert to object using reduce
                const obj = formValues.reduce((acc, item) => {
                    // Handle duplicate keys (like employee) by creating an array
                    if (acc[item.name]) {
                        if (Array.isArray(acc[item.name])) {
                            acc[item.name].push(item.value);
                        } else {
                            acc[item.name] = [acc[item.name], item.value];
                        }
                    } else {
                        acc[item.name] = item.value ? item.value : 0;
                    }
                    return acc;
                }, {});

                instance.formValues = obj;

                if (instance.filter === 1) {
                    var salaryTable = instance.salaryRangeTable;
                    salaryTable.ajax.reload();
                } else {
                    $.ajax({
                        url: baseUrl('hris/reports/get_employees_history'),
                        type: "POST",
                        dataType: "json",
                        data: {
                            csrf_token : _csrf_hash,
                            filter : obj
                        },
                        success: function(response){
                            var colmn = [];
                    
                            colmn.push(
                                { title: 'Name', data: 'name', orderable: true }, 
                                { title: 'Biometric #', data: 'biometricno', orderable: true },
                            );
                    
                            $.each(response.generated_years, function(index, value){
                                colmn.push(
                                    { title: 'Year', data: null, orderable: false, 
                                        render: function (data, type, row, meta) {
                                            return value;
                                        }
                                    },
                                    { title: 'Salary', data: null, orderable: false,
                                        render: function (data, type, row, meta) {
                                            var _year = row[value];
                                            return typeof _year !== 'undefined' ? _year.sal_rate : '0.00';
                                        }
                                    }, 
                                    { title: 'Eff Date', data: null, orderable: false, 
                                        render: function (data, type, row, meta) {
                                            var _year = row[value];
                                            return typeof _year !== 'undefined' ? _year.sal_date : '---';
                                        }
                                    }
                                );
                            });
                    
                            instance.instanceColumn = colmn;
                            instance.generatedCompany = response.company;
                            instance.generatedDepartment = response.department;
                            instance.generatedPosition = response.position;

                            $('#table-employee-salary-history').DataTable().destroy();
                            $("#table-employee-salary-history").empty();
                            instance.instanceData = response.data;
                            var table = instance.salaryHistoryDataTable();
                            var salaryHistoryTable = instance.salaryHistoryTable;
                    
                            salaryHistoryTable.clear();
                            salaryHistoryTable.rows.add(response.data).draw();
                            salaryHistoryTable.columns.adjust().draw();
                        }
                    })
                }
            }
        }, salaryHistoryDataTable(){
            var instance = this;

            var url = baseUrl('hris/reports/get_employees_history');

            var table = $('#table-employee-salary-history').DataTable({
                dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 exportDropdown'><'col-xl-6 col-lg-6 col-md-6 col-sm-12'f>>" +
                    "<'row'<'col-12'rt>>" +
                    "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'l><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'EMPLOYEES SALARY HISTORY GENERATED REPORT FROM ' + instance.selectedFrom + ' - ' + instance.selectedTo + '',
                        customize: function (xlsx) {
                            console.log(xlsx);
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            var downrows = 5;

                            var company = instance.generatedCompany ? instance.generatedCompany : 'All';
                            var department = instance.generatedDepartment ? instance.generatedDepartment : 'All';
                            var position = instance.generatedPosition ? instance.generatedPosition : 'All';

                            var $sheet = $(sheet);
                            var clRow = $('row', sheet);
                            //update Row
                            clRow.each(function () {
                                var attr = $(this).attr('r');
                                var ind = parseInt(attr);
                                ind = ind + downrows;
                                $(this).attr("r",ind);
                            });
                    
                            // Update  row > c
                            $('row c ', sheet).each(function () {
                                var attr = $(this).attr('r');
                                var pre = attr.substring(0, 1);
                                var ind = parseInt(attr.substring(1, attr.length));
                                ind = ind + downrows;
                                $(this).attr("r", pre + ind);
                            });

                            var cellA6 = $sheet.find('c[r="A6"] is t');
                            cellA6.text('')
                    
                            function Addrow(index,data) {
                                msg='<row r="'+index+'">'
                                for(i=0;i<data.length;i++){
                                    var key=data[i].k;
                                    var value=data[i].v;
                                    if (index == 1) {
                                        msg += '<c t="inlineStr" r="' + key + index + '" s="51">';
                                    } else {
                                        msg += '<c t="inlineStr" r="' + key + index + '" s="2">';
                                    }
                                    msg += '<is>';
                                    msg +=  '<t>'+value+'</t>';
                                    msg+=  '</is>';
                                    msg+='</c>';
                                }
                                msg += '</row>';
                                return msg;
                            }

                            //insert
                            var r1 = Addrow(1, [{ k: 'A', v: 'EMPLOYEES SALARY HISTORY GENERATED REPORT FROM ' + instance.selectedFrom + ' - ' + instance.selectedTo + '' }, { k: 'B', v: '' }, { k: 'C', v: '' }]);
                            var r2 = Addrow(2, [{ k: 'A', v: 'Generated By' }, { k: 'B', v: '' }, { k: 'C', v: '' }]);
                            var r3 = Addrow(3, [{ k: 'A', v: 'Company' }, { k: 'B', v: company }, { k: 'C', v: '' }]);
                            var r4 = Addrow(4, [{ k: 'A', v: 'Department' }, { k: 'B', v: department }, { k: 'C', v: '' }]);
                            var r5 = Addrow(5, [{ k: 'A', v: 'Position' }, { k: 'B', v: position }, { k: 'C', v: '' }]);
                            
                            sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + r5 + sheet.childNodes[0].childNodes[1].innerHTML;
                        }
                    },
                    {
                        extend: 'print',
                        title: function () {
                            let html = ``;

                            html += `<div class="mb-3 mt-3">`;
                                html += '<h4 class="text-center">EMPLOYEES SALARY HISTORY GENERATED REPORT FROM ' + instance.selectedFrom + ' - ' + instance.selectedTo + '</h4>';
                            html += `</div>`;

                            html += `<div class="row ml-3">`;
                                html += `<div class="col-3">`;
                                    html += `<h6><b>GENERATED BY</b>${ instance.generatedEmployee && instance.generatedEmployee.length > 0 && !instance.generatedCompany && !instance.generatedDepartment && !instance.generatedPosition  ? ': EMPLOYEE NAME' : '' }</h6>`;
                                html += `</div>`;
                            html += `</div>`;

                            html += `<div class="row justify-content-start ml-3">`;
                                if (instance.generatedCompany || instance.generatedDepartment || instance.generatedPosition){
                                    if (instance.generatedDepartment){
                                        html += `<div class="col-4">`;
                                            html += `<h6><b>COMPANY:</b> ${ instance.generatedCompany }</h6>`;
                                        html += `</div>`;
                                    }
    
                                    if (instance.generatedDepartment){
                                        html += `<div class="col-4">`;
                                            html += `<h6><b>DEPARTMENT:</b> ${ instance.generatedDepartment }</h6>`;
                                        html += `</div>`;
                                    }
    
                                    if (instance.generatedPosition){
                                        html += `<div class="col-4">`;
                                            html += `<h6><b>POSITION:</b> ${ instance.generatedPosition }</h6>`;
                                        html += `</div>`;
                                    }
                                } else {
                                    html += `<div class="col-3">`;
                                        html += `<h6><b>COMPANY:</b> All</h6>`;
                                    html += `</div>`;
    
                                    html += `<div class="col-3">`;
                                        html += `<h6><b>DEPARTMENT:</b> All</h6>`;
                                    html += `</div>`;
    
                                    html += `<div class="col-3">`;
                                        html += `<h6><b>POSITION:</b> All</h6>`;
                                    html += `</div>`;
                                }
                            html += `</div>`;

                            return html;
                        },
                        exportOptions: {
                            orientation: 'landscape',
                            stripHtml: false,
                        },
                        customize: function(win){
                            var css = `@page { size: landscape; margin: 0.5cm; } 
                                .dt-print-view table { font-size: 12px; } 
                                .dt-print-view table.dataTable tfoot tr:first-child th{ border-top: 1px solid #000000; }
                                .dt-print-view table.dataTable tfoot tr:first-child th{ border-bottom: 4px double #000000; }`,
                                head = win.document.head || win.document.getElementsByTagName('head')[0],
                                body = win.document.body || win.document.getElementsByTagName('body')[0],
                                style = win.document.createElement('style'),
                                tempDiv2 = win.document.createElement('div');

                            style.type = 'text/css';
                            style.media = 'print';

                            if (style.styleSheet) {
                                style.styleSheet.cssText = css;
                            } else {
                                style.appendChild(win.document.createTextNode(css));
                            }

                            head.appendChild(style);
                        }
                    }
                ],
                columns: instance.instanceColumn,
                processing: false,
                serverSide: false,
                retrieve: true,
                destroy: true,
                deferLoading: 0,
                searching: true,
                data: instance.instanceData,
                scrollX: true,
                autoWidth: false,
                initComplete: function (settings, json) {
                    const nTable = settings.nTable;
                    var _tableData = settings.aoData.length;

                    $("#table-employee-salary-history_filter input[type='search']").removeClass("form-control-sm");

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

                    $(dropdown).appendTo("#table-employee-salary-history_wrapper .exportDropdown");
                    dropdownEl = $(".m-dropdown__toggle.export-as");

                    const tempExcel = $("#export-as-excel");
                    const tempPdf = $("#export-as-pdf");
                    const tempPrint = $("#export-as-print");

                    if (typeof tempExcel != 'undefined' && tempExcel.length > 0) {

                        tempExcel.on("click", function () {
                            if (_tableData > 0) {
                                dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                                table.button(".buttons-excel").trigger();
                            } else {
                                toastr.warning('Generate salary history report first before exporting it to excel.', 'Salary History Export to Excel', 10000);
                            }
                        });
                    }

                    if (typeof tempPrint != 'undefined' && tempPrint.length > 0) {

                        tempPrint.on("click", function () {
                            if (_tableData > 0) {
                                dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                                table.button(".buttons-print").trigger();
                            } else {
                                toastr.warning('Generate salary history report first before printing it.', 'Salary History Print', 10000);
                            }
                        });
                    }
                }
            });

            instance.salaryHistoryTable = table;
        }
    }
});