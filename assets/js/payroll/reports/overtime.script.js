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
            _this.show_by_date = (id == 1) ? true : false;
            _this.month_picker = (id == 2) ? true : false;
            _this.year_picker = (id == 3) ? true : false;
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
                    });
            } else {
                _this.renderSelect2Picker();
            }

            return _this;
        }, renderSelect2Picker: function () {
            var _this = this;
            var currentElement = _this.$el;
            var tempModal = $(currentElement).closest(".modal");
            setTimeout(function () {
                $(currentElement).find("select[name='filter_month']")
                    .select2({
                        width: '100%',
                        data: months,
                        placeholder: "SELECT MONTH",
                        allowClear: true,
                        dropdownParent: tempModal,
                    });

                $(currentElement).find("select[name='filter_year']")
                    .select2({
                        data: _years,
                        width: '100%',
                        placeholder: "SELECT YEAR",
                        allowClear: true,
                        dropdownParent: tempModal,
                    });

                $(currentElement).find("select#employee")
                    .select2({
                        width: '100%',
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                        ajax: {
                            url: baseUrl('payroll/select_employee'),
                            dataType: 'json',
                            global: false,
                            delay: 250,
                            data: function (params) {
                                /*** params.company_ids = _this.company_ids; ***/
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
                        var _thisSelect2 = this;
                        var selectedValues = $(_thisSelect2).select2("val");
                        _this.company_ids = selectedValues;
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");
                    }).on("select2:unselect", function (e) {
                        var _thisSelect2 = this;
                        var selectedValues = $(_thisSelect2).select2("val");
                        _this.company_ids = selectedValues;
                        $(currentElement)
                            .find("select#employee")
                            .val([])
                            .trigger("change");
                    });
            }, 200);
        }, resetFields: function () {
            var _this = this;
            var currentForm = $(_this.$el).find("#frm-journal-report");
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
                        console.log("trigger 1");
                        setTimeout(function(){
                            select2Multiple.val([]);
                            select2Multiple.trigger("change");
                            console.log("trigger 2");
                        }, 250);
                    }, 750);
                }
            }
        },
    }, mounted: function () {
        var _this = this;
        _this.renderSelect2Picker();
    }
});


var vmReportHeaders = new Vue({
    el: "#report-header",
    data: { show_header: false, filters: {} }
});

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
                                var tempOption = new Option(vv.text, vv.id, true, true);
                                tempEmployeeSelector.append(tempOption);
                            });
                            tempEmployeeSelector.prop("disabled", true);
                        }
                    }
                }
            }
        });
    } else {
        if (typeof employees == "object" && typeof employees !== "undefined") {
            const tempEmployeeSelector = $("form#frm-journal-report select#employee");
            if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                tempEmployeeSelector.empty();
                $.each(employees, function (ii, vv) {
                    var tempOption = new Option(vv.text, vv.id, true, true);
                    tempEmployeeSelector.append(tempOption);
                });
                tempEmployeeSelector.prop("disabled", true);
            }
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
                                var tempOption = new Option(vv.text, vv.id, true, true);
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

var column_names;
var column_rows;

$(document).ready(function(){
    // let dtJournal;
    // $.ajax({
    //     url: baseUrl("payroll/reports/get_overtime_report"),
    //     dataType: "json",
    //     success: function (json) {
    //         dtJournal = $('#tbl-journal').DataTable({
    //             serverSide: true,
    //             processing: true,
    //             destroy: true,
    //             paging: false,
    //             searching: false,
    //             bInfo : false,
    //             ordering: false,
    //             ajax: {
    //                 url: baseUrl('payroll/reports/get_overtime_report'),
    //                 type: 'POST',
    //                 dataType: 'JSON',
    //                 data: function (d) {
    //                     d.csrf_token = _csrf_hash;
    //                     d.ids = _tempIds;
    //                     d.clear_table = _clearTable;
    //                 }, 
    //             },
    //             columns: [
    //                 { data: 'employee_name', name: 'employee_name', width: '30%'},
    //                 { data: 'date_start'},
    //                 { data: 'date_end'},
    //                 { data: 'pay_date'},
    //                 { data: 'ot_hrs', className: 'text-center', render: function (data, meta, row) {
    //                     return numberFormat(data);
    //                 }},
    //                 { data: 'ot_amount', className: 'text-right', render: function (data, meta, row) {
    //                     return '₱ '+numberFormat(data);
    //                 }},
    //             ], footerCallback: function (row, data, start, end, display) {
    //                 var api = this.api(), data;
    //                 // Remove the formatting to get integer data for summation
    //                 var intVal = function (i) {
    //                     return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
    //                 };
            
    //                 let totalHrs = api
    //                     .column(4)
    //                     .data()
    //                     .reduce(function (a, b) {
    //                         return intVal(a) + intVal(b);
    //                     }, 0);
                    
    //                 let totalAmount = api
    //                     .column(5)
    //                     .data()
    //                     .reduce(function (a, b) {
    //                         return intVal(a) + intVal(b);
    //                     }, 0);
                
    //                 $(api.column(4).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalHrs) + "</span>");
    //                 $(api.column(5).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalAmount) + "</span>");
    //             }
    //         });
    //         dtJournal.ajax.reload();
    //     }
    // });  

    /*** $.ajax({
        url: baseUrl("payroll/reports/get_overtime_summary"),
        dataType: "json",
        success: function (json) {
            dtOTSummary = $('#tbl-overtime-summary').DataTable({
                serverSide: true,
                processing: true,
                destroy: true,
                paging: false,
                searching: false,
                bInfo : false,
                ordering: false,
                ajax: {
                    url: baseUrl('payroll/reports/get_overtime_summary'),
                    type: 'POST',
                    dataType: 'JSON',
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
                        d.ids = _tempIds;
                        d.clear_table = _clearTable;
                        d.filters = _tempFilter;
                    }, 
                },
                columns: [
                    { data: 'employee_name', name: 'employee_name' },
                    { data: 'overtime_in', className: 'text-center', width: '10%'},
                    { data: 'day', className: 'text-center', width: '5%'},
                    { data: 'daily_rate', className: 'text-right', width: '5%', render: function(data, type, row){
                        return '₱ '+data;
                    }},
                    { data: 'allowance', className: 'text-center', width: '5%'},
                    { data: 'ot_hrs', className:'text-center', render: function (data, type, row) {
                        if (data && parseFloat(data) > 0) {
                            const ot_hrs = parseFloat(data);
                            return ot_hrs.toFixed(2);
                        }
                        return data;
                    }},
                    { data: 'ot_pay', className: 'text-right', render: function (data, type, row) {
                        if (data && parseFloat(data) > 0) {
                            const ot_pay = parseFloat(data);
                            return '₱ '+ot_pay.toFixed(2);
                        }
                        return data;
                    }},
                    { data: 'ot_ndiff_hrs', className: 'text-center', render: function (data, type, row) {
                        if (data && parseFloat(data) > 0) {
                            const ot_ndiff_hrs = parseFloat(data);
                            return ot_ndiff_hrs.toFixed(2);
                        }
                        return data;
                    }},
                    { data: 'night_diff', className: 'text-right', render: function (data, type, row) {
                        if (data && parseFloat(data) > 0) {
                            const ot_ndiff_pay = parseFloat(data);
                            return ot_ndiff_pay.toFixed(2);
                        }
                        return data;
                    }},
                    { data: 'ot_adj', name: 'ot_adj', className: 'text-center',render: function (data, type, row) {

                        return dtOTSummary.cells(0).data();
                    }},
                    { data: 'amount', className: 'text-right', width: '10%', render: function (data, type, row) {
                        if (data && parseFloat(data) > 0) {
                            const amount = parseFloat(data);
                            return '₱ '+amount.toFixed(2);
                        }
                        return data;
                    }},
                    { data: 'total_pay', className: 'text-right', width: '10%', render: function (data, type, row) {
                        return "";
                    }}
                ],columnDefs: [ {
                    targets: [ 0, 9, 11 ],
                    visible: false,
                } ],rowGroup: {
                    startRender: function ( _rows, group ) {
                        return $('<tr><td colspan="11" class="bg-secondary"><i>' + group + '</i></td></tr>');
                    },
                    endRender: function ( rows, _group ) {
                        var OTadj = rows
                                .data()
                                .pluck('ot_adj')
                                .reduce( function (a, b) {
                                    return b ? numberFormat(b) : 0.00;
                                }, 0);
                        var totalAmount = rows
                                .data()
                                .pluck('amount')
                                .reduce( function (a, b) {
                                    var totalOTAmount = parseFloat(a) + parseFloat(b);
                                    return numberFormat(totalOTAmount);
                                }, 0);
                            
                        var total = parseFloat(totalAmount) + parseFloat(OTadj);
                        const uiAdjustment = parseFloat(OTadj) > 0 ? `<i>OT ADJ - ₱ ${numberFormat(OTadj)}</i>`: ``;
                        const uiTotal = `<i><strong>₱ ${numberFormat(total)}</strong></i>`;

                        const tempContainer = `<tr class="bg-secondary">
                            <td colspan="6" class="text-right"></td>
                            <td class="text-right"></td>
                            <td class="text-center">${uiAdjustment}</td>
                            <td class="text-right">${uiTotal}</td>
                            </tr>`;

                            return $(tempContainer);
                            
                        
                    },
                    dataSrc: [ 'employee_name' ],
                    
                }
                , footerCallback: function (row, data, start, end, display) {
                    var api = this.api(), data;
                    // Remove the formatting to get integer data for summation
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                    };
            
                    let totalAmount = api
                        .column(10)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                       
                    $(api.column(10).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalAmount) + "</span>");
                // }
                }
            });
            dtOTSummary.ajax.reload();
        }
    });  ***/


    dtOTSummary = $('#tbl-overtime-summary').DataTable({
        dom: "rt",
        serverSide: true,
        processing: true,
        destroy: true,
        paging: false,
        searching: false,
        ordering: false,
        footer: true,
        ajax: {
            url: baseUrl('payroll/reports/get_overtime_summary'),
            type: 'POST',
            dataType: 'JSON',
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.ids = _tempIds;
                d.clear_table = _clearTable;
                d.filters = _tempFilter;
            }, 
        }, buttons: [{
            extend: 'excel',
            footer: true,
            customize: function (xlsx) {
                const sheet = xlsx.xl.worksheets['sheet1.xml'];

                let numrows = $('row', sheet).length;
                let mergeCells = $('mergeCells', sheet);
                mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                    attr: { ref: 'A' + numrows + ':' + 'K' + numrows },
                }));

                function _createNode(doc, nodeName, opts) {
                    const tempNode = doc.createElement(nodeName);
                    if (opts) {
                        if (opts.attr) { $(tempNode).attr(opts.attr); }
                        if (opts.children) {
                            $.each(opts.children, function (key, value) {
                                tempNode.appendChild(value);
                            });
                        }
                        if (opts.text !== null && opts.text !== undefined) { tempNode.appendChild(doc.createTextNode(opts.text)); }
                    }
                    return tempNode;
                }
            },
        }], columns: [
            { visible: false, data: 'employee_name' },
            { data: 'overtime_in', width: '10%' },
            { data: 'day', width: '8%', className: "text-center" },
            { data: 'daily_rate', width: '6%', className: "text-right",
                render: function(data, type, row){
                    return '₱ '+data;
                }
            }, { data: 'allowance', width: '9%', className: "text-right", 
                render: function (data) {
                    if (data && parseFloat(data) > 0) {
                        const allw = parseFloat(data);
                        return allw.toFixed(2);
                    } else { return '-'; }
                }
            }, { data: 'ot_hrs', width: '5%', className: "text-right",
                render: function (data, type, row) {
                    if (data && parseFloat(data) > 0) {
                        const ot_hrs = parseFloat(data);
                        return ot_hrs.toFixed(2);
                    }
                    return data;
                }
            }, { data: 'ot_pay', width: '8%', className: "text-right",
                render: function (data) {
                    if (data && parseFloat(data) > 0) {
                        let ot_pay = parseFloat(data);
                        return '₱ '+ot_pay.toFixed(2);
                    }else{ return '-'; }
                }
            }, { data: 'ot_pay_20', className: "text-right", width: '8%', 
                render: function (data) {
                    /*** if (row.ot_pay && parseFloat(row.ot_pay) > 0 && parseInt(row.has_shift) === 1) {
                        const _25_ot_pay = parseFloat(row.ot_pay) * 0.25;
                        return '₱ '+_25_ot_pay.toFixed(2);
                    } else { return '-'; } ***/
                    if (data && parseFloat(data) > 0) {
                        let ot_pay_20 = parseFloat(data);
                        return '₱ '+ot_pay_20.toFixed(2);
                    }else{ return '-'; }
                }
            }, { data: 'ot_pay_30', className: "text-right", width: '8%', 
                render: function (data) {
                    /*** if (row.ot_pay && parseFloat(row.ot_pay) > 0 && parseInt(row.has_shift) === 0) {
                        const _30_ot_pay = parseFloat(row.ot_pay) * 0.30;
                        return '₱ '+_30_ot_pay.toFixed(2);
                    } else { return '-'; } ***/
                    if (data && parseFloat(data) > 0) {
                        let ot_pay_30 = parseFloat(data);
                        return '₱ '+ot_pay_30.toFixed(2);
                    }else{ return '-'; }
                }
            }, { data: 'ot_ndiff_hrs', width: '8%', className: "text-right",
                render: function (data) {
                    if (data && parseFloat(data) > 0) {
                        const ot_ndiff_hrs = parseFloat(data);
                        return ot_ndiff_hrs.toFixed(2);
                    }
                    return data;
                }
            }, { data: 'night_diff', className: "text-right", width: '8%', 
                render: function (data) {
                    if (data && parseFloat(data) > 0) {
                        const ot_ndiff_pay = parseFloat(data);
                        return '₱ '+ot_ndiff_pay.toFixed(2);
                    } else { return '-'; }
                }
            },
            { data: null, className: "text-right", width: '5%', 
                render: function () { return '-'; }
            }, 
            { data: 'amount', className: "text-right pr-3", width: '10%', 
                render: function (data, type, row) {
                    if (data && parseFloat(data) > 0) {
                        const amount = parseFloat(data);
                        return '₱ '+amount.toFixed(2);
                    }
                    return data;
                }
            }
        ], rowGroup: {
            startRender: function ( _rows, group ) {
                return $('<tr><td colspan="12" class="bg-secondary"><span class="m--font-boldest">' + group + '</span></td></tr>');
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
                /*** const uiAdjustment = parseFloat(OTadj) > 0 ? `<span class="m--font-boldest">OT ADJ</span>`: `&nbsp;`; ***/
                const uiAdjustmentAmount = parseFloat(OTadj) > 0 ? `<span class="m--font-boldest">₱ ${numberFormat(OTadj)}</span>`: `-`;
                const uiTotal = `<strong>₱ ${numberFormat(total)}</strong>`;

                const tempContainer = `<tr class="bg-secondary">
                    <td colspan="10" class="text-right">&nbsp;</td>
                    <td class="text-right">${uiAdjustmentAmount}</td>
                    <td class="text-right pr-3">${uiTotal}</td>
                    </tr>`;

                return $(tempContainer);
            },
            dataSrc: [ 'employee_name' ],
            
        }, drawCallback: function () {
            const api = this.api();
            const tempData = api.data();
            const _dtActions = $("#table-actions");
            const hasRowData = tempData.length > 0;
            if (hasRowData && typeof _dtActions !== "undefined" && _dtActions.length == 1) {
                if (_dtActions.hasClass("m--hide") === true) { _dtActions.removeClass("m--hide"); }
            } else {
                if (_dtActions.hasClass("m--hide") === false) { _dtActions.addClass("m--hide"); }
            }

            vmReportHeaders.show_header = hasRowData;

        }, footerCallback: function () {
            const api = this.api();
            const intVal = function (i) { return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : (typeof i === 'number') ? i : 0; };
            const otPayTotalIndex = 6;
            const otPay20TotalIndex = 7;
            const otPay30TotalIndex = 8;
            const nDiffTotalIndex = 10;
            const grandTotalIndex = 12;

            let otPayTotalAmount = api.column(otPayTotalIndex).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            let otPay20TotalAmount = api.column(otPay20TotalIndex).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            let otPay30TotalAmount = api.column(otPay30TotalIndex).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            let nDiffTotalAmount = api.column(nDiffTotalIndex).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            let totalAmount = api.column(grandTotalIndex).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            
            const footerLabelTotal = $(api.column(5).footer());
            footerLabelTotal.removeClass("text-center");
            footerLabelTotal.html(`<span class="m--font-boldest mr-3">GRAND TOTAL</span>`);

            $(api.column(otPayTotalIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(otPayTotalAmount) + "</span>");
            $(api.column(otPay20TotalIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(otPay20TotalAmount) + "</span>");
            $(api.column(otPay30TotalIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(otPay30TotalAmount) + "</span>");
            $(api.column(nDiffTotalIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(nDiffTotalAmount) + "</span>");
            $(api.column(grandTotalIndex).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalAmount) + "</span>");
        }
    });

    $.validate({
        form: "#frm-journal-report",
        lang: "en",
        scrollToTopOnError: false,
        onSuccess: function (form) {
            let propDisabled = false;
            
            var currentForm = form[0];
            var formUrl = currentForm.action;
            var formData = $(currentForm).serialize();

            const tempEmployeeFilter = $(currentForm).find("select#employee");
            if(typeof tempEmployeeFilter !== "undefined"){
                propDisabled = tempEmployeeFilter.is(":disabled");
                if(propDisabled){ tempEmployeeFilter.prop("disabled", false); }
            }

            const payrollGroup = $(currentForm).find("select#payroll_group").val();
            console.log(payrollGroup);
            
            var formData = $(currentForm).serialize();
            if(propDisabled){ tempEmployeeFilter.prop("disabled", true); }
            
            getScriptRendering(formUrl, formData, currentForm);
            return false;
        }
    });
    
    var getScriptRendering = function (formUrl, formData, currentForm) {
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
                _tempFilter = Object.assign({});
                if (json.response) {
                    _clearTable = false;
                    _tempIds = json.data;
                    _tempFilter = Object.assign({}, json.filters);
                    vmReportHeaders.filters = Object.assign({}, _tempFilter);

                    toastr.success(json.toastr_msg, "Filtered Overtime Summary Report");
                } else {
                    _clearTable = true;
                    toastr.error(json.toastr_msg, "Filtered Overtime Summary Report");
                }
    
                dtOTSummary.ajax.reload();
            }
        });
    }
});

const exportExcel = function(){
    dtOTSummary.button(".buttons-excel").trigger();
}