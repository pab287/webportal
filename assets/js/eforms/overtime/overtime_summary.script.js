let globalPrintableSignatory = [];
const modalGenerateReport = $("#generate-report-modal");

let _years = [];
let _companies = [];
let psEmployeeGroup = [];

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
let dtOTSummary = null;

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.years !== "undefined" && _tempContentData.years.length > 0){
        _years = _tempContentData.years;
    }
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){
        _companies = _tempContentData.company;
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
                    });;

                $(currentElement).find("select#employee")
                    .select2({
                        width: '100%',
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                        ajax: {
                            url: baseUrl('eforms/overtime/select2_employee'),
                            dataType: 'json',
                            global: false,
                            delay: 250,
                            data: function (params) {
                                /*** params.company_ids = _this.company_ids; ***/
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
                        setTimeout(function(){
                            select2Multiple.val([]);
                            select2Multiple.trigger("change");
                        }, 250);
                    }, 750);
                }
                currentForm[0].reset(); 
            }
        },
    }, mounted: function () {
        const _this = this;
        _this.renderSelect2Picker();
    }
});


const vmReportHeaders = new Vue({
    el: "#report-header",
    data: { show_header: false, filters: {} }
});

$("#payroll_group").select2({
    placeholder: 'Select an option',
    width: '100%',
    ajax: {
        url: baseUrl("eforms/overtime/select_payroll_group"),
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
            url: baseUrl("eforms/overtime/get_payroll_group_multiple"),
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
    dtOTSummary = $('#tbl-overtime-summary').DataTable({
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
            exportOptions: {
                columns: [0, 1, 2, 3,4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 16, 17],
            },
            footer: true,
            customize: function (xlsx) {
                const sheet = xlsx.xl.worksheets['sheet1.xml'];

                let numrows = $('row', sheet).length;
                let mergeCells = $('mergeCells', sheet);
                mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                    attr: { ref: 'A' + numrows + ':' + 'F' + numrows },
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
            { data: 'overtime_in', width: '10%',
                render: function (data, type, row) {
                    return moment(data).format('YYYY-MM-DD');
                }
            },
            { data: 'day', width: '8%', className: "text-center" },
            { data: 'daily_rate', width: '6%', className: "text-right",
                render: function(data, type, row){
                    return data > 0 ? '₱ '+data : '-';
                }
            }, 
            { data: 'allowance', width: '9%', className: "text-right", 
                render: function (data) {
                    $allowance = '-';
                    if (data && parseFloat(data) > 0) {
                        const allw = parseFloat(data);
                        $allowance = '₱ '+ allw.toFixed(2);
                    }

                    return $allowance;
                }
            }, 
            { data: 'ot_hrs', width: '5%', className: "text-right",
                render: function (data, type, row) {
                    $ot_hrs = '-';
                    if (data && parseFloat(data) > 0) {
                        const ot_hrs = parseFloat(data);
                        $ot_hrs = ot_hrs.toFixed(2);
                    }
                    return $ot_hrs;
                }
            }, 
            { data: 'ot_pay', width: '8%', className: "text-right",
                render: function (data) {
                    $ot_pay = '-';
                    if (data && parseFloat(data) > 0) {
                        let ot_pay = parseFloat(data);
                        $ot_pay = '₱ '+ot_pay.toFixed(2);
                    }

                    return $ot_pay;
                }
            }, 
            { data: 'ot_pay_20', className: "text-right", width: '8%', 
                render: function (data) {
                    $_ot_pay_20 = '-';
                    if (data && parseFloat(data) > 0) {
                        let ot_pay_20 = parseFloat(data);
                        $_ot_pay_20 = '₱ '+ot_pay_20.toFixed(2);
                    }

                    return $_ot_pay_20;
                }
            }, 
            { data: 'ot_pay_30', className: "text-right", width: '8%', 
                render: function (data) {
                    $_ot_pay_30 = '-';
                    if (data && parseFloat(data) > 0) {
                        let ot_pay_30 = parseFloat(data);
                        $_ot_pay_30 = '₱ '+ot_pay_30.toFixed(2);
                    }

                    return $_ot_pay_30;
                }
            },
            { data: 'ot_ndiff_hrs', width: '8%', className: "text-right",
                render: function (data) {
                    $_ot_ndiff_hrs = '-';
                    if (data && parseFloat(data) > 0) {
                        const ot_ndiff_hrs = parseFloat(data);
                        $_ot_ndiff_hrs = ot_ndiff_hrs.toFixed(2);
                    }
                    return $_ot_ndiff_hrs;
                }
            }, 
            { data: 'night_diff', className: "text-right", width: '8%', 
                render: function (data) {
                    $_night_diff = '-';
                    if (data && parseFloat(data) > 0) {
                        const ot_ndiff_pay = parseFloat(data);
                        $_night_diff = '₱ '+ot_ndiff_pay.toFixed(2);
                    }

                    return $_night_diff;
                }
            },
            { data: 'ot_allowance', className: "text-right", width: '8%', 
                render: function (data) {
                    $_ot_allowance = '-';
                    if (data && parseFloat(data) > 0) {
                        const ot_allowance = parseFloat(data);
                        $_ot_allowance = '₱ '+ot_allowance.toFixed(2);
                    }

                    return $_ot_allowance;
                }
            },
            { data: null, className: "text-right", width: '5%', 
                render: function () { return '-'; }
            }, 
            { data: 'amount', className: "text-right pr-3", width: '10%', 
                render: function (data, type, row) {
                    $_amount = '-';
                    if (data && parseFloat(data) > 0) {
                        const amount = parseFloat(data);
                        $_amount = '₱ '+amount.toFixed(2);
                    }
                    return $_amount;
                }
            },
            { data: 'status', width: '5%', className: "text-center", 
                render: function (data, type, row, meta) { 
                    let html = '';

                    let status = 'fa-times-circle text-danger';
                    let title = 'No Overtime Request Found.';

                    if (data) {
                        status = data.toLowerCase() == 'approved' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger';
                        title = `Status: ${data.toUpperCase()}&#013;Ref #: ${row.reference_no}&#013;Created at: ${moment(row.created_at).format('LL')}`;
                    }

                    html = `<span class="fa ${status}" style="font-size: 18px" title="${title}" onclick="copyToClipboard('${row.reference_no}')"></span>`;
                    return html;
                }
            },
            { data: 'is_paid', width: '5%', className: "text-center",
                render: function (data) {
                    let html = '';
                    const status = data && parseInt(data) == 1 ? 'fa-check-circle text-success' : 'fa-times-circle text-danger';
                    const title = data && parseInt(data) == 1 ? 'Paid' : 'Unpaid';
                    html = `<span class="fa ${status}" style="font-size: 18px" title="${title}"></span>`;
                    return html;
                }
            },
            { visible: false, title: 'APPROVED', data: 'status',
                render: function (data) {
                    let html = '-';

                    if (data) {
                        html = data.toUpperCase();
                    } else {
                        html = 'NO REQUEST';
                    }

                    return html;
                }
            },
            { visible: false, title: 'PAID', data: 'is_paid',
                render: function (data) {
                    return data && parseInt(data) == 1 ? 'PAID' : 'UNPAID';
                }
            },
        ], rowGroup: {
            startRender: function ( _rows, group ) {
                return $('<tr><td colspan="15" class="bg-secondary"><span class="m--font-boldest">' + group + '</span></td></tr>');
            },
            endRender: function ( rows, _group ) {
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
                const uiAdjustmentAmount = parseFloat(OTadj) > 0 ? `<span class="m--font-boldest">₱ ${numberFormat(OTadj)}</span>`: `-`;
                const uiTotal = `<strong>₱ ${numberFormat(total)}</strong>`;

                const tempContainer = `<tr class="bg-secondary">
                    <td colspan="11" class="text-right">&nbsp;</td>
                    <td class="text-right">${uiAdjustmentAmount}</td>
                    <td class="text-right pr-3">${uiTotal}</td>
                    <td colspan="2">&nbsp;</td>
                    </tr>`;

                return $(tempContainer);
            },
            dataSrc: [ 'employee_name' ],
            
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

            const intVal = i => {
                if (typeof i === 'string') return parseFloat(i.replace(/[^0-9.-]/g, '').trim()) || 0;
                if (typeof i === 'number') return i;
                return 0;
            };

            const otPayTotalIndex = 6;
            const otPay20TotalIndex = 7;
            const otPay30TotalIndex = 8;
            const nDiffTotalHrsIndex = 9;
            const nDiffTotalIndex = 10;
            const otAllowanceIndex = 11;
            const adjustmentIndex = 12;
            const grandTotalIndex = 13;

            // get filtered rows only
            const filteredRows = api.rows({ filter: 'applied' }).data();

            // compute OT adjustment totals per employee (filtered rows only)
            let arrAdjustments = {};
            $.each(filteredRows, function (_i, row) {
                if (row.ot_adj && parseFloat(row.ot_adj) > 0) {
                    if (!arrAdjustments[row.emp_id]) arrAdjustments[row.emp_id] = [];
                    if ($.inArray(row.ot_adj, arrAdjustments[row.emp_id]) === -1) {
                        arrAdjustments[row.emp_id].push(row.ot_adj);
                    }
                }
            });

            let totalAdjustmentAmount = 0;
            $.each(arrAdjustments, function (_i, adjAmount) {
                totalAdjustmentAmount += parseFloat(adjAmount);
            });

            const sumColumn = (index) => filteredRows.reduce((a, b) => a + intVal(b[api.column(index).dataSrc()]), 0);

            const otPayTotalAmount = sumColumn(otPayTotalIndex);
            const otPay20TotalAmount = sumColumn(otPay20TotalIndex);
            const otPay30TotalAmount = sumColumn(otPay30TotalIndex);
            const nDiffTotalHrsAmount = sumColumn(nDiffTotalHrsIndex);
            const nDiffTotalAmount = sumColumn(nDiffTotalIndex);
            const otAllowanceAmount = sumColumn(otAllowanceIndex);
            const totalAmount = sumColumn(grandTotalIndex);

            const grandTotalAmount = totalAmount + totalAdjustmentAmount;
            const footerLabelTotal = $(api.column(5).footer());
            footerLabelTotal.removeClass("text-center").html(`<span class="m--font-boldest mr-3">GRAND TOTAL</span>`);

            $(api.column(otPayTotalIndex).footer()).html("<span class='m--font-boldest'>₱ "+numberFormat(otPayTotalAmount)+"</span>");
            $(api.column(otPay20TotalIndex).footer()).html("<span class='m--font-boldest'>₱ "+numberFormat(otPay20TotalAmount)+"</span>");
            $(api.column(otPay30TotalIndex).footer()).html("<span class='m--font-boldest'>₱ "+numberFormat(otPay30TotalAmount)+"</span>");
            $(api.column(nDiffTotalHrsIndex).footer()).html("<span class='m--font-boldest'>"+numberFormat(nDiffTotalHrsAmount)+"</span>");
            $(api.column(nDiffTotalIndex).footer()).html("<span class='m--font-boldest'>₱ "+numberFormat(nDiffTotalAmount)+"</span>");
            $(api.column(otAllowanceIndex).footer()).html("<span class='m--font-boldest'>₱ "+numberFormat(otAllowanceAmount)+"</span>");
            $(api.column(adjustmentIndex).footer()).html("<span class='m--font-boldest'>₱ "+numberFormat(totalAdjustmentAmount)+"</span>");
            $(api.column(grandTotalIndex).footer()).html("<span class='m--font-boldest'>₱ "+numberFormat(grandTotalAmount)+"</span>");
        }, createdRow: function (rowEl, rowData, _index) {
            const isPaid = rowData.is_paid && parseInt(rowData.is_paid) == 1 ? true : false;

            if (!isPaid) { $(rowEl).addClass('unpaid-ot'); }
        }
    });

    // for filtering by column without create new request or altering array
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'tbl-overtime-summary') {
            return true;
        }

        let checked = $('input[name="filter"]:checked')
            .map(function () { return this.value; })
            .get();

        if (!checked.length || checked.includes('all')) {
            return true;
        }

        let row = settings.aoData[dataIndex]._aData;

        let approved = (row.status || '').toLowerCase();
        let paid     = parseInt(row.is_paid, 10);

        return checked.some(val =>
            (val === 'approved'   && approved === 'approved') ||
            (val === 'unapproved' && approved !== 'approved') ||
            (val === 'paid'       && paid === 1) ||
            (val === 'unpaid'     && paid === 0)
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

                    if (json.data.length > 0) {
                        vmReportHeaders.filters = { ...json.filters };
                        toastr.success(json.toastr_msg, "Filtered Overtime Summary Report");

                        dtOTSummary.clear().rows.add(json.data).draw();

                        setTimeout( function () { 
                            modalGenerateReport.modal("hide"); 
                        }, 750);
                    } else {
                        toastr.error(json.toastr_msg, "Filtered Overtime Summary Report");
                    }
                }
            });

            return false;
        }
    });

    $('input[name="filter"]').on('change', function () {
        dtOTSummary.draw();
    });
});

const exportExcel = function(){
    dtOTSummary.button(".buttons-excel").trigger();
}

function copyToClipboard(text){
    navigator.clipboard.writeText(text)
    .then(() => {
        toastr.success("OT copied to clipboard!");
    })
    .catch(err => {
        console.error('Error in copying text: ', err);
    });
}