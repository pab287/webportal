let globalPrintableSignatory = [];
const psSignatoryModal = $("#modal-ps--signatory");
const psResetSignatoryModal = $("#modal-ps--reset-signatory");
const modalGenerateReport = $("#generate-report-modal");
let total_amount = 0;
let _filter = [];

let _years = [];
let _companies = [];
let psEmployeeGroup = [];
let _payout_mode = [];
let _payoutSchedule = [];

let _tempIds = [];
const months = [
    { id: 1, text: "January" },
    { id: 2, text: "February" },
    { id: 3, text: "March" },
    { id: 4, text: "April" },
    { id: 5, text: "May" },
    { id: 6, text: "June" },
    { id: 7, text: "July" },
    { id: 8, text: "August" },
    { id: 9, text: "September" },
    { id: 10, text: "October" },
    { id: 11, text: "November" },
    { id: 12, text: "December" }
];

let _clearTable = true;
let _tempFilter = {};
let _globalFooterHtml = null;
let _totalTaxable = 0;
let _tempData = {
    show_by_date: true, show_picker: false,
    year_picker: false, month_picker: false, company_ids: 0,
};
let dtNighDiffTable = null;
let dtSummary = null;

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.years !== "undefined" && _tempContentData.years.length > 0){
        _years = _tempContentData.years;
    }
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){
        _companies = _tempContentData.company;
    }
    if(typeof _tempContentData.payout_mode !== "undefined" && _tempContentData.payout_mode.length > 0){
        _payout_mode = _tempContentData.payout_mode;
    }
    if(typeof _tempContentData.payout_schedule !== "undefined" && _tempContentData.payout_schedule.length > 0){
        _payoutSchedule = _tempContentData.payout_schedule;
    }
}

const vmGeneratejournal = new Vue({
    el: "#generate-journal_content",
    data: _tempData,
    methods: {
        tempShowByDates: function (id) {
            const _this = this;
            let currentElement = _this.$el;
            _this.show_by_date = (id == 1);
            _this.month_picker = (id == 2);
            _this.year_picker = (id == 3);
            _this.show_picker = false;
            if (id == 1) {
                const filterDateRange = $(currentElement).find("#filter_date_range");
                if (typeof filterDateRange !== "undefined" && filterDateRange.length == 1) {
                    filterDateRange.on("change", function () {
                        const thisFilter = this;
                        if (thisFilter.checked) {
                            $("#filter-by-date-range").removeClass('m--hide');
                            $("#filter-by-month-year").addClass('m--hide');
                        } else {
                            $("#filter-by-date-range").addClass('m--hide');
                            $("#filter-by-month-year").removeClass('m--hide');
                        }
                    });
                }
            }
            _this.renderSelect2Picker();
            return _this;
        }, tempShowPicker: function () {
            const _this = this;
            const currentElement = _this.$el;
            _this.show_picker = !_this.show_picker;
            if (_this.show_picker === true) {
                $(currentElement).find("#date-picker")
                    .daterangepicker({
                        buttonClasses: 'm-btn btn',
                        applyClass: 'btn-primary',
                        cancelClass: 'btn-secondary',
                        locale: {
                            format: 'MM/DD/YYYY'
                        }
                    })
                    .on('apply.daterangepicker', function (ev, picker) {
                        const tempStartDate = picker.startDate.format('MMM DD, YYYY');
                        const tempEndDate = picker.endDate.format('MMM DD, YYYY');
                        const tempFormat = tempStartDate + ' - ' + tempEndDate;
                        $(currentElement).find("#date-range").val(tempFormat);

                        const self = $(currentElement).find("#date-range");
                        self.validate();
                    });
            } else {
                _this.renderSelect2Picker();
            }

            return _this;
        }, renderSelect2Picker: function () {
            const _this = this;
            const currentElement = _this.$el;
            const tempModal = $(currentElement).closest(".modal");
            setTimeout(function () {
                $(currentElement).find("select[name='filter_month']")
                    .select2({
                        width: '100%',
                        data: months,
                        placeholder: "SELECT MONTH",
                        allowClear: true,
                        dropdownParent: tempModal,
                    }).on("select2:select", function (e) {
                        const self = $(this);
                        self.validate();
                    });

                $(currentElement).find("select[name='filter_year']")
                    .select2({
                        data: _years,
                        width: '100%',
                        placeholder: "SELECT YEAR",
                        allowClear: true,
                        dropdownParent: tempModal,
                    }).on("select2:select", function (e) {
                        const self = $(this);
                        self.validate();
                    });

                $(currentElement).find("select#employee")
                    .select2({
                        width: '100%',
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                        ajax: {
                            url: baseUrl('payroll/select_employee_by_company'),
                            dataType: 'json',
                            global: false,
                            delay: 250,
                            data: function (params) {
                                params.company_ids = [$(currentElement).find("select#company").val()] || 0;
                                params.q = params.term;
                                return params;
                            },
                            processResults: function (data) {
                                return data;
                            }
                        }, language: { errorLoading: function () { return "Searching..." } }
                    });

                $(currentElement).find("select#company")
                    .select2({
                        allowClear: true,
                        width: '100%',
                        data: _companies,
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                    }).on("select2:select", function (e) {
                        const _thisSelect2 = this;
                        const selectedValues = $(_thisSelect2).select2("val");
                        _this.company_ids = selectedValues;
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");
                        
                        const self = $(this);
                        self.validate();
                    }).on("select2:unselect", function (e) {
                        const _thisSelect2 = this;
                        const selectedValues = $(_thisSelect2).select2("val");
                        _this.company_ids = selectedValues;
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");
                    });
                
                $(currentElement).find("select#payout_mode")
                    .select2({
                        allowClear: true,
                        width: '100%',
                        data: _payout_mode,
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                    }).on("select2:select", function (e) {
                        const _thisSelect2 = this;
                        const selectedValues = $(_thisSelect2).select2("val");
                        _this.payout_mode = selectedValues;
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");
                    }).on("select2:unselect", function (e) {
                        const _thisSelect2 = this;
                        const selectedValues = $(_thisSelect2).select2("val");
                        _this.payout_mode = selectedValues;
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");
                    });

                $(currentElement).find("select#station")
                    .select2({
                        allowClear: true,
                        width: '100%',
                        ajax: {
                            url: baseUrl('payroll/reports/select2_station'),
                            dataType: 'json',
                            global: false,
                            delay: 250,
                            data: function (params) {
                                params.q = params.term;
                                return params;
                            },
                            processResults: function (data) {
                                return data;
                            }
                        }, language: { errorLoading: function () { return "Searching..." } },
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                    }).on("select2:select", function (e) {
                        const _thisSelect2 = this;
                        const selectedValues = $(_thisSelect2).select2("val");
                        _this.station = selectedValues;
                    }).on("select2:unselect", function (e) {
                        const _thisSelect2 = this;
                        const selectedValues = $(_thisSelect2).select2("val");
                        _this.station = selectedValues;
                    });

                $(currentElement).find("select#payout_sched").select2({
                    width: "100%",
                    data: _payoutSchedule,
                    placeholder: "SELECT AN OPTION",
                    allowClear: true,
                });
            }, 200);
        }, resetFields: function () {
            const _this = this;
            const currentForm = $(_this.$el).find("#frm-journal-report");
            if (typeof currentForm !== "undefined") {
                _this.company_ids = 0;
                psEmployeeGroup = [];

                currentForm.find("input[name=group]")[0].click();
                currentForm.find("select").val("").trigger("change");
                const select2Multiple = currentForm.find("select[multiple]");
                if(typeof select2Multiple != "undefined" && select2Multiple.length > 0){
                    select2Multiple.prop("disabled", false);
                    setTimeout(function(){ 
                        currentForm[0].reset(); 
                        setTimeout(function(){
                            select2Multiple.val([]);
                            select2Multiple.trigger("change");
                        }, 250);
                    }, 750);
                }
            }
        },
    }, mounted: function () {
        const _this = this;
        _this.renderSelect2Picker();
    }
});


const vmReportHeaders = new Vue({
    el: "#report-header",
    data: { show_header: false, filters: {}, print_counter: { count: 0, last_printed: null, last_printed_at: null } }
});

const vmActionSignatories = new Vue({
    el: "#actionSignatories",
    data: { show_signatories: false, signatories: {} },
    methods: {
        editSignatories: function () {
            return psSignatoryModal.modal("show");
        }, resetSignatories: function () {
            return psResetSignatoryModal.modal("show");
        }
    }
});

const vmPortletSummary = new Vue({
    el: "#portlet--summary",
    data: { count: 0 }
});

const buildProjectSummaryRows = function (rows) {
    const list = Array.isArray(rows) ? rows : [];
    const grouped = {};
    list.forEach(function (item) {
        const station = (item && item.station && String(item.station).trim()) ? String(item.station).trim() : 'No assigned Project';
        if (!grouped[station]) {
            grouped[station] = { station: station, employees: {}, amount: 0, otAdjByEmp: {} };
        }

        const empId = item && item.emp_id ? parseInt(item.emp_id, 10) : 0;
        if (empId > 0) { grouped[station].employees[empId] = true; }

        const amount = item && item.amount ? parseFloat(item.amount) : 0;
        grouped[station].amount += Number.isNaN(amount) ? 0 : amount;

        if (empId > 0 && item && item.ot_adj) {
            const adj = parseFloat(item.ot_adj);
            if (!Number.isNaN(adj) && !grouped[station].otAdjByEmp[empId]) {
                grouped[station].otAdjByEmp[empId] = adj;
            }
        }
    });

    return Object.keys(grouped).sort().map(function (key) {
        const row = grouped[key];
        let stationAdj = 0;
        Object.keys(row.otAdjByEmp).forEach(function (empId) {
            stationAdj += parseFloat(row.otAdjByEmp[empId]) || 0;
        });
        return {
            station: row.station,
            employee_count: Object.keys(row.employees).length,
            total_amount: row.amount + stationAdj
        };
    });
};

$("#payroll_group").select2({
    placeholder: 'Select an option',
    width: '100%',
    ajax: {
        url: baseUrl("payroll/select_payroll_group"),
        dataType: "json",
        type: 'get',
        delay: 250,
        global: false,
        data: function (params) {
            params.company_id = $("form#frm-journal-report select#company").val();
            return params;
        },
        processResults: function (data) {
            return data;
        }
    }
}).on("select2:select", function (e) {
    const _this = this;
    const tempVal = $(_this).val();
    const data = e.params.data;
    let employees = [];
    if (typeof data.employees == "object" && typeof data.employees !== "undefined") { employees = data.employees; }
    if (tempVal.length > 1) {
        $.ajax({
            url: baseUrl("payroll/get_payroll_group_multiple"),
            type: "post",
            dataType: "json",
            data: { group_id: tempVal, [_csrf_token]: _csrf_hash },
            success: function (json) {
                if (json.response) {
                    const tempData = json.data;
                    if (typeof tempData == "object" && typeof tempData !== "undefined") {
                        const tempEmployeeSelector = $("form#frm-journal-report select#employee");
                        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                            tempEmployeeSelector.empty();
                            $.each(tempData, function (ii, vv) {
                                const tempOption = new Option(vv.text, vv.id, true, true);
                                tempEmployeeSelector.append(tempOption);
                            });
                            tempEmployeeSelector.prop("disabled", true);
                        }
                    }
                }
            }
        });
    } else if (typeof employees == "object" && typeof employees !== "undefined") {
        const tempEmployeeSelector = $("form#frm-journal-report select#employee");
        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
            tempEmployeeSelector.empty();
            $.each(employees, function (ii, vv) {
                const tempOption = new Option(vv.text, vv.id, true, true);
                tempEmployeeSelector.append(tempOption);
            });
            tempEmployeeSelector.prop("disabled", true);
        }
    }
    if (typeof data.text !== "undefined" && data.text) {
        const tempEmpGroup = data.text;
        let tempIsInArray = $.inArray(tempEmpGroup, psEmployeeGroup);
        if (tempIsInArray == -1) {
            psEmployeeGroup.push(tempEmpGroup);
        }
    }

}).on("select2:unselect", function (e) {
    const _this = this;
    const tempValUnselected = $(_this).val();
    const data = e.params.data;
    if (tempValUnselected.length == 0) {
        const tempEmployeeSelector = $("form#frm-journal-report select#employee");
        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
            tempEmployeeSelector.prop("disabled", false);
        }
    } else {
        $.ajax({
            url: baseUrl("payroll/get_payroll_group_multiple"),
            type: "post",
            dataType: "json",
            data: { group_id: tempValUnselected, [_csrf_token]: _csrf_hash },
            success: function (json) {
                if (json.response) {
                    const tempData = json.data;
                    if (typeof tempData == "object" && typeof tempData !== "undefined") {
                        const tempEmployeeSelector = $("form#frm-journal-report select#employee");
                        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                            tempEmployeeSelector.empty();
                            $.each(tempData, function (ii, vv) {
                                const tempOption = new Option(vv.text, vv.id, true, true);
                                tempEmployeeSelector.append(tempOption);
                            });
                            tempEmployeeSelector.prop("disabled", true);
                        }
                    }
                }
            }
        });
    }

    if (typeof data.text !== "undefined" && data.text) {
        const tempEmpGroup = data.text;
        let tempIsInArray = $.inArray(tempEmpGroup, psEmployeeGroup);
        if (tempIsInArray !== -1) {
            const index = psEmployeeGroup.indexOf(tempEmpGroup);
            if (index > -1) { psEmployeeGroup.splice(index, 1); }
        }

    }
});

$(document).ready(function(){
    dtNighDiffTable = $('#tbl-nightdiff-summary').DataTable({
        dom: "rt",
        serverSide: false,
        processing: false,
        destroy: true,
        paging: false,
        searching: true,
        ordering: false,
        footer: true,
        buttons: [{
            extend: 'excel',
            footer: true,
            exportOptions: {
                // Include hidden employee_name and station in export output.
                columns: [0, 1, 2, 3, 4, 5, 6, 7]
            },
            customizeData: function (data) {
                const normalize = function (value) {
                    return String(value == null ? '' : value).toLowerCase().trim();
                };

                // Sort body rows alphabetically by employee_name column (index 0).
                data.body.sort(function (a, b) {
                    return normalize(a[0]).localeCompare(normalize(b[0]));
                });
            },
            customize: function (xlsx) {
                const sheet = xlsx.xl.worksheets['sheet1.xml'];

                let numrows = $('row', sheet).length;
                let mergeCells = $('mergeCells', sheet);
                mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                    attr: { ref: 'A' + numrows + ':' + 'E' + numrows },
                }));

                function _createNode(doc, nodeName, opts) {
                    const tempNode = doc.createElement(nodeName);
                    if (opts) {
                        if (opts.attr) { $(tempNode).attr(opts.attr); }
                        if (opts.children) {
                            $.each(opts.children, function (_key, value) {
                                tempNode.appendChild(value);
                            });
                        }
                        if (opts.text !== null && opts.text !== undefined) { tempNode.appendChild(doc.createTextNode(opts.text)); }
                    }
                    return tempNode;
                }
            }
        }], columns: [
            { visible: false, data: 'employee_name' },
            { visible: false, data: 'station',
                render: function (data){
                    return data ? data.toUpperCase() : 'NO ASSIGNED PROJECT';
                }
            },
            { data: 'date', width: '10%' },
            { data: 'day', width: '8%', className: "text-center",
                render: function(data) {
                    return data.toUpperCase();
                }
            },
            { data: 'daily_rate', width: '6%', className: "text-right",
                render: function(data, type, row){
                    return '₱ '+data;
                }
            }, 
            {
                data: 'ndiff_hrs', className: "text-center", width: '6%',
                render: function (data, type, row) {
                    return data ? data : ' - ';
                }
            }, 
            { data: 'night_diff', className: "text-right", width: '8%', 
                render: function (data) {
                    if (data && parseFloat(data) > 0) {
                        const ot_ndiff_pay = parseFloat(data);
                        return '₱ '+ot_ndiff_pay.toFixed(2);
                    } else { return '-'; }
                }
            }, { data: 'amount', className: "text-right pr-3", width: '10%', 
                render: function (data, type, row) {
                    if (data && parseFloat(data) > 0) {
                        const amount = parseFloat(data);
                        return '₱ '+amount.toFixed(2);
                    }
                    return data;
                }
            }
        ], rowGroup: {
            startRender: function ( _rows, group, level ) {
                if (level === 0) {
                    const station = group || 'No assigned Project';
                    return $('<tr class="text-center"><td colspan="7" class="bg-secondary"><span class="m--font-boldest">Project #: ' + station + '</span></td></tr>');
                }

                if (!group || String(group).toUpperCase() === 'NO GROUP') {
                    return null;
                }

                return $('<tr><td colspan="7" class="bg-secondary"><span class="m--font-boldest">' + group + '</span></td></tr>');
            },
            endRender: function ( rows, _group, level ) {
                if (level !== 0 && level !== 1) {
                    return null;
                }

                let OTadj = rows
                    .data()
                    .pluck('ot_adj')
                    .reduce( function (a, b) {
                        return b ? toNumber(b) : 0.00;
                    }, 0);
                
                let totalAmount = rows
                .data()
                .pluck('amount')
                .reduce( function (a, b) {
                    let totalOTAmount = parseFloat(a) + parseFloat(b);
                    return toNumber(totalOTAmount);
                }, 0);
                    
                let total = parseFloat(totalAmount) + parseFloat(OTadj);
                const uiTotal = `<strong>₱ ${numberFormat(total)}</strong>`;

                const rowClass = level === 0 ? 'bg-secondary' : '';
                const tempContainer = `<tr class="${rowClass}">
                    <td colspan="5" class="text-right">&nbsp;</td>
                    <td class="text-right pr-3">${uiTotal}</td>
                    </tr>`;

                return $(tempContainer);
            },
            dataSrc: [ 'station', 'employee_name' ],
            emptyDataGroup: '',
            
        }, drawCallback: function () {
            const api = this.api();
            const tempData = api.data();
            const _dtActions = $("#table-actions");
            const hasRowData = tempData.length > 0;
            if (_dtActions.hasClass("m--hide") === false) { _dtActions.addClass("m--hide"); }
            if (hasRowData && typeof _dtActions !== "undefined" && _dtActions.length == 1 && _dtActions.hasClass("m--hide") === true) {
                _dtActions.removeClass("m--hide");
            }
            vmReportHeaders.show_header = hasRowData;

            const _filter = $("#filter-table");
            if (_filter.hasClass("m--hide") === false) { _filter.addClass("m--hide"); }
            if (hasRowData && typeof _filter !== "undefined" && _filter.length == 1 && _filter.hasClass("m--hide") === true) {
                _filter.removeClass("m--hide");
            }

        }, footerCallback: function () {
            const api = this.api();
            const tempData = api.data();
            
            const intVal = function (i) {
                if (typeof i === 'string') {
                    return parseFloat(i.replace(/[^0-9.-]/g, '').trim()) || 0;
                }
                return typeof i === 'number' ? i : 0;
            };

            const nDiffTotalIndex = 5;
            const grandTotalIndex = 6;

            const filteredRows = api.rows({ filter: 'applied' }).data();

            const sumColumn = (index) => filteredRows.reduce((a, b) => a + intVal(b[api.column(index).dataSrc()]), 0);

            let nDiffTotalAmount = sumColumn(nDiffTotalIndex);
            const grandTotalAmount = sumColumn(parseFloat(grandTotalIndex));
            const footerLabelTotal = $(api.column(3).footer());
            footerLabelTotal.removeClass("text-center");
            footerLabelTotal.html(`<span class="m--font-boldest mr-3">GRAND TOTAL</span>`);

            $(api.column(nDiffTotalIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(nDiffTotalAmount) + "</span>");
            $(api.column(grandTotalIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(grandTotalAmount) + "</span>");

            total_amount = grandTotalAmount;
        }, createdRow: function (rowEl, rowData, _index) {
            const isPosted = rowData.posted && parseInt(rowData.posted) == 1 ? true : false;

            if (!isPosted) { $(rowEl).addClass('unpaid-ndiff'); }
        }
    });

    // for filtering by column without create new request or altering array
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'tbl-nightdiff-summary') {
            return true;
        }

        let checked = $('input[name="filter"]:checked')
            .map(function () { return this.value; })
            .get();

        if (!checked.length || checked.includes('all')) {
            return true;
        }

        let row = settings.aoData[dataIndex]._aData;
        let paid = parseInt(row.posted);

        return checked.some(val =>
            (val == 1      && paid === 1) ||
            (val == 0     && paid === 0)
        );
    });
    // for filtering by column without create new request or altering array

    $.validate({
        form: "#frm-journal-report",
        lang: "en",
        scrollToTopOnError: false,
        onSuccess: function (form) {
            let propDisabled = false;
            
            const currentForm = form[0];
            const formUrl = currentForm.action;

            const tempEmployeeFilter = $(currentForm).find("select#employee");
            if(typeof tempEmployeeFilter !== "undefined"){
                propDisabled = tempEmployeeFilter.is(":disabled");
                if(propDisabled){ tempEmployeeFilter.prop("disabled", false); }
            }
            
            let formData = $(currentForm).serialize();
            if(propDisabled){ tempEmployeeFilter.prop("disabled", true); }
            
            getScriptRendering(formUrl, formData, currentForm);
            return false;
        }
    });
    
    const getScriptRendering = function (formUrl, formData, currentForm) {
        $.ajax({
            url: formUrl,
            type: "post",
            dataType: "json",
            data: formData,
            beforeSend: function () {
                $(currentForm)
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right")
                    .prop("disabled", true);
            },
            success: function (json) {
                _tempFilter = {};
                vmActionSignatories.show_signatories = false;

                if (json.data.length > 0) {
                    vmActionSignatories.show_signatories = true;
                    vmReportHeaders.filters = { ...json.filters };
                    toastr.success('Regular Night Differential entries found!', "Filtered Night Differential Summary Report");

                    const sortedRows = json.data.sort(function (a, b) {
                        const stationA = (a && a.station) ? String(a.station).toLowerCase() : '';
                        const stationB = (b && b.station) ? String(b.station).toLowerCase() : '';
                        if (stationA !== stationB) { return stationA.localeCompare(stationB); }

                        const employeeA = (a && a.employee_name) ? String(a.employee_name).toLowerCase() : '';
                        const employeeB = (b && b.employee_name) ? String(b.employee_name).toLowerCase() : '';
                        if (employeeA !== employeeB) { return employeeA.localeCompare(employeeB); }

                        const dateA = (a && a.date) ? String(a.date) : '';
                        const dateB = (b && b.date) ? String(b.date) : '';
                        return dateA.localeCompare(dateB);
                    });

                    dtNighDiffTable.clear().rows.add(sortedRows).draw();
                    const summaryRows = buildProjectSummaryRows(sortedRows);
                    vmPortletSummary.count = summaryRows.length;
                    Vue.nextTick(function () {
                        dtSummary.clear().rows.add(summaryRows).draw();
                        dtSummary.columns.adjust().draw(false);
                    });

                    setTimeout( function () { 
                        modalGenerateReport.modal("hide"); 
                    }, 750);
                } else {
                    toastr.error('No regular night differential entries available!', "Filtered Night Differential Summary Report");
                    vmPortletSummary.count = 0;
                    dtSummary.clear().draw();
                }
    
                const currentSelectCompanyId = $(currentForm).find("#company").val();
                if (typeof currentSelectCompanyId !== "undefined" && parseInt(currentSelectCompanyId) > 0) {
                    $.ajax({
                        url: siteUrl("payroll/reports/get_current_signatory_by_company_and_type/" + currentSelectCompanyId + "/2"),
                        global: false,
                        dataType: "json",
                        success: function (json) {
                            let tempRow = {};
                            let ctr = json.count ? json.count : 0;
                            if (json.response) { tempRow = { ...json.data }; }
                            
                            vmActionSignatories.signatories = { ...tempRow };
                            vmTempSignatory.row = { ...tempRow };
                            vmTempSignatory.count = ctr;
                            vmTempSignatory.$mount();

                            vmPortletSignatories.row = { ...tempRow };
                            vmPortletSignatories.count = ctr;
                        }
                    });

                    _filter = formData;
                }
            }
        });
    }

    $('input[name="filter"]').on('change', function () {
        dtNighDiffTable.draw();
    });

    dtSummary = $("#tbl-summary").DataTable({
        dom: "rt",
        serverSide: false,
        processing: false,
        destroy: true,
        paging: false,
        searching: false,
        ordering: false,
        footer: true,
        autoWidth: false,
        columns: [
            {
                data: "station",
                width: "60%",
                render: function (data) {
                    const val = (data === null || typeof data === "undefined" || String(data).trim() === "") ? "No assigned Project" : String(data);
                    return val.toUpperCase();
                }
            },
            {
                data: "employee_count",
                width: "15%",
                className: "text-center",
                render: function (data) {
                    const count = parseInt(data, 10);
                    return Number.isNaN(count) ? 0 : count;
                }
            },
            {
                data: "total_amount",
                width: "25%",
                className: "text-right pr-3",
                render: function (data) {
                    const amount = parseFloat(data);
                    return '₱ ' + numberFormat(Number.isNaN(amount) ? 0 : amount);
                }
            }
        ],
        footerCallback: function () {
            const api = this.api();
            const intVal = function (i) {
                if (typeof i === 'string') {
                    return parseFloat(i.replace(/[^0-9.-]/g, '').trim()) || 0;
                }
                return typeof i === 'number' ? i : 0;
            };

            const grandTotalIndex = 2;
            const totalAmount = api.column(grandTotalIndex).data().reduce(function (a, b) {
                return parseFloat(intVal(a).toFixed(2)) + parseFloat(intVal(b).toFixed(2));
            }, 0);

            const footerLabelTotal = $(api.column(1).footer());
            footerLabelTotal.removeClass("text-center");
            footerLabelTotal.html(`<span class="m--font-boldest mr-3">GRAND TOTAL</span>`);
            $(api.column(grandTotalIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalAmount) + "</span>");
        }
    });
});

const exportExcel = function(){
    dtNighDiffTable.button(".buttons-excel").trigger();
}

const vmPortletSignatories = new Vue({
    el: "#portlet--signatories",
    data: { row: {}, count: 0 }
});

const vmTempSignatory = new Vue({
    el: "#signatory--content",
    data: { row: {}, count: 0 },
    methods: {
        setGlobalSignatories: function () {
            const _this = this;
            const currentRow = _this.row;
            globalPrintableSignatory = [];
            if (typeof currentRow.meta_field !== "undefined" && typeof currentRow.meta_field == "object") {
                $.each(currentRow.meta_field, function (i, v) {
                    const tempData = { label: v.label, value: v.value, is_active: v.is_active };
                    globalPrintableSignatory.push(tempData);
                });
            }
            return globalPrintableSignatory;
        },
        activeSignatory: function (e) {
            const currentTarget = e.target;
            const formGroup = $(currentTarget).closest(".form-group.m-form__group.row");
            if (typeof formGroup !== "undefined" && formGroup.length == 1) {
                let isChecked = $(currentTarget).is(":checked");
                const select2Container = formGroup.find(".select2--value");
                if (typeof select2Container !== "undefined" && select2Container.length == 1) {
                    if (isChecked) {
                        if (select2Container.is(":disabled") === true) {
                            select2Container.prop("disabled", false);
                        }
                    } else {
                        if (select2Container.is(":disabled") === false) {
                            select2Container.prop("disabled", true);
                        }
                    }
                }
            }
        }, setModalSelect2: function () {
            const _this = this;
            const _currentElement = _this.$el;
            const psModalSignatory = $(_currentElement)
                .closest("#modal-ps--signatory");
            if (typeof psModalSignatory !== "undefined" && psModalSignatory.length == 1) {
                initSelect2Employee(psModalSignatory);
            }
        }, validateFields: function () {
            const _this = this;
            const currentElement = _this.$el;
            const tempForm = $(currentElement).find("form#updatePrintableSignatories");
            if (typeof tempForm !== "undefined") {
                $.validate({
                    form: tempForm,
                    lang: 'en',
                    onSuccess: function (form) {
                        const tempUrl = form[0].action;
                        const tempType = form[0].method;
                        const formData = $(form[0]).serialize();

                        $.ajax({
                            url: tempUrl,
                            type: tempType,
                            dataType: "json",
                            data: formData,
                            beforeSend: function () {
                                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            },
                            success: function (json) {
                                const currentModal = $(currentElement).closest(".modal");
                                if (json.response) {
                                    const currentData = json.data;
                                    if (Object.keys(currentData).length > 0) {
                                        const metaFields = currentData.meta_field;
                                        const ctr = metaFields.length;

                                        _this.row = { ...currentData };
                                        _this.count = ctr;
                                        _this.setGlobalSignatories();

                                        vmPortletSignatories.row = { ...currentData };
                                        vmPortletSignatories.count = ctr;

                                        vmResetSignatories.row = { ...currentData };
                                        vmResetSignatories.count = ctr;

                                        if (typeof currentModal !== "undefined" && currentModal.length == 1) {
                                            currentModal.modal("hide");
                                        }
                                    }
                                } else {
                                    toastr.error("Payroll Signatory", json.toastr_msg);
                                }
                                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            }
                        });
                        return false;
                    },

                });
            }
        }
    }, mounted: function () {
        const _this = this;
        setTimeout(function () {
            _this.setModalSelect2();
            _this.setGlobalSignatories();
            _this.validateFields();
        }, 500);
    }
});

const vmResetSignatories = new Vue({
    el: "#reset-signatory--content",
    data: { row: {}, count: 0 },
    methods: {
        validateFields: function () {
            const _this = this;
            const currentElement = _this.$el;
            const tempForm = $(currentElement).find("form#resetPrintableSignatories");
            if (typeof tempForm !== "undefined") {
                $.validate({
                    form: tempForm,
                    lang: 'en',
                    onSuccess: function (form) {
                        const tempUrl = form[0].action;
                        const tempType = form[0].method;
                        const formData = $(form[0]).serialize();

                        $.ajax({
                            url: tempUrl,
                            type: tempType,
                            dataType: "json",
                            data: formData,
                            beforeSend: function () {
                                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            },
                            success: function (json) {
                                let tempRow = {};
                                let ctr = 0;

                                if (json.response) {
                                    tempRow = { ...json.data };
                                    ctr = json.count;
                                }
                                vmTempSignatory.row = { ...tempRow };
                                vmTempSignatory.count = ctr;
                                vmTempSignatory.$mount();

                                vmPortletSignatories.row = { ...tempRow };
                                vmPortletSignatories.count = ctr;

                                _this.row = { ...tempRow };
                                _this.count = ctr;
                                const currentModal = $(currentElement).closest(".modal");
                                currentModal.modal("hide");

                                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            }
                        });
                        return false;
                    },

                });
            }
        }
    }, mounted: function () {
        const _this = this;
        _this.validateFields();
    }
});

const initSelect2Employee = function (tempModal, portlet) {
    if (typeof tempModal !== "undefined" && tempModal.length == 1) {
        let tempSelector = tempModal.find("select.select2--value");
        if (typeof portlet !== "undefined") { tempSelector = portlet.find("select.select2--value"); }
        if (typeof tempSelector !== "undefined") {
            tempSelector.select2({
                tags: true,
                allowClear: true,
                placeholder: 'Select an option',
                width: '100%',
                dropdownParent: tempModal,
                ajax: {
                    url: baseUrl("payroll/reports/select_employee"),
                    dataType: "json",
                    delay: 250,
                    global: false,
                    processResults: function (data) {
                        let tempData = [];
                        $.each(data.results, function (i, v) {
                            const dd = { id: v.text, text: v.text };
                            tempData.push(dd);
                        });
                        return { results: tempData };
                    }
                }
            });
        }
    }
}

function printSummary() {
    const parseSerializedFilter = function (serialized) {
        const parsed = {};
        if (!serialized) { return parsed; }

        $.each(serialized.split("&"), function (_i, pair) {
            if (!pair) { return; }

            const parts = pair.split("=");
            const rawKey = decodeURIComponent((parts.shift() || "").replace(/\+/g, " "));
            const rawValue = decodeURIComponent((parts.join("=") || "").replace(/\+/g, " "));
            if (!rawKey) { return; }

            if (rawKey.slice(-2) === "[]") {
                const arrayKey = rawKey.slice(0, -2);
                if (!Array.isArray(parsed[arrayKey])) { parsed[arrayKey] = []; }
                parsed[arrayKey].push(rawValue);
            } else {
                parsed[rawKey] = rawValue;
            }
        });

        return parsed;
    };

    const payload = parseSerializedFilter(_filter);
    delete payload.csrf_token;

    payload[_csrf_token] = _csrf_hash;
    payload.amount = total_amount;
    payload.module = "nightdiff_summary";

    $.ajax({
        url: baseUrl('payroll/reports/count_print'),
        data: payload,
        dataType: "json",
        type: 'post',
        success: function (response) {
            const data = response.data;
            const isDisplayValue = function (value) {
                if (value === null || typeof value === "undefined") { return false; }
                const tempVal = String(value).trim();
                if (!tempVal) { return false; }
                if (tempVal.toLowerCase() === "null") { return false; }
                if (tempVal.toLowerCase() === "no assigned name") { return false; }
                return true;
            };

            vmReportHeaders.print_counter.count = (data && typeof data.count !== "undefined") ? data.count : 0;
            vmReportHeaders.print_counter.last_printed = (data && isDisplayValue(data.last_printed_by)) ? data.last_printed_by : null;
            vmReportHeaders.print_counter.last_printed_at = (data && isDisplayValue(data.last_printed_at)) ? moment(data.last_printed_at).format('lll').toUpperCase() : null;

            Vue.nextTick(function () {
                window.requestAnimationFrame(function () {
                    window.requestAnimationFrame(function () {
                        window.print();
                    });
                });
            });
        }
    });
}
