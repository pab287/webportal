let tblExpiringProjectBased = null;
let dropdownEl = null;

$(document)
    .ready(function () {
        tblExpiringProjectBased = $('#table-expiring-project-based')
            .DataTable({
                dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 exportDropdown'><'col-xl-6 col-lg-6 col-md-6 col-sm-12'f>>" +
                    "<'row'<'col-12'rt>>" +
                    "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'l><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'EXPIRING PROJECT BASED EMPLOYEES',
                        action: function (e, dt, node, config) {
                            const self = this;
                            getExportData(e, dt, node, config, self, baseUrl('hris/reports/get_expiring_employees/1/?work_status=Project Based'), 'excelHtml5')
                                .then(() => {
                                    dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                                });
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'EXPIRING PROJECT BASED EMPLOYEES',
                        action: function (e, dt, node, config) {
                            const self = this;
                            getExportData(e, dt, node, config, self, baseUrl('hris/reports/get_expiring_employees/1/?work_status=Project Based'), 'pdfHtml5')
                                .then(() => {
                                    dropdownEl.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
                                });
                        },
                        orientation: 'landscape'
                    },
                    {
                        extend: 'print',
                        title: 'EXPIRING PROJECT BASED EMPLOYEES',
                        action: function (e, dt, node, config) {
                            const self = this;
                            getExportData(e, dt, node, config, self, baseUrl('hris/reports/get_expiring_employees/1/?work_status=Project Based'), 'print')
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
                    url: baseUrl('hris/reports/get_expiring_employees/?work_status=Project Based'),
                    type: 'post',
                    dataType: 'json',
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
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
                        data: 'secondEvaluation',
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
                            return moment(data).format('MMM DD, YYYY')
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
                order: [[5, 'asc']],
                pageLength: 50,
                initComplete: function () {
                    $("#table-expiring-project-based_filter input[type='search']")
                        .removeClass("form-control-sm");

                    const dropdown = '' +
                        '       <div class="m-dropdown m-dropdown--inline m-dropdown--align-left" ' +
                        '             data-dropdown-toggle="hover" aria-expanded="true">' +
                        '            <button class="m-dropdown__toggle mb-2 btn btn-success dropdown-toggle export-as">' +
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

                    $(dropdown).appendTo("#table-expiring-project-based_wrapper .exportDropdown");
                    dropdownEl = $(".m-dropdown__toggle.export-as");
                },
            });
    });

function exportAs(type) {
    dropdownEl.addClass("m-btn--custom m-loader m-loader--light m-loader--left");
    let clearHere = false;

    setTimeout(() => {
        switch (type) {
            case "excel":
                tblExpiringProjectBased.button(".buttons-excel").trigger();
                break;
            case "pdf":
                tblExpiringProjectBased.button(".buttons-pdf").trigger();
                break;
            case "print":
                tblExpiringProjectBased.button(".buttons-print").trigger();
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
            dt.rows().remove();
            dt.rows.add(response.data).draw();
            $.fn.dataTable.ext.buttons[type].action.call(self, e, dt, node, config);
        }
    });

    return result;
}