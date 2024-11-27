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

var _clearTable = true;
var _tempFilter = {};
var _globalFooterHtml = null;
let globalCoverageDate = null;
let globalIncentiveType = null;
let _totalTaxable = 0;
var _tempData = {
    show_by_date: true, show_picker: false,
    year_picker: false, month_picker: false, company_ids: [],
    incentive_picker : false, row: [], has_coverage_date: false,
};
let psEmployeeGroup = [];
let _years = [];
let _companies = [];

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.years !== "undefined" && _tempContentData.years.length > 0){
        _years = _tempContentData.years;
    }
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){
        _companies = _tempContentData.company;
    }
}
var select2IncentiveType = function () {
    $("#incentive_type")
        .select2({
            placeholder: 'Select an option',
            width: '100%',
            ajax: {
                url: baseUrl("payroll/select_incentive_type"),
                dataType: "json",
                delay: 250,
                global: false,
                processResults: function (data) {
                    return data;
                }
            }
        }).on("select2:select", function (e) {
            const data = e.params.data;
            const tempCount = Object.keys(data).length;
            vmGenerateSummary.has_coverage_date = tempCount > 0 ? true : false;
            vmGenerateSummary.row = Object.assign({}, data);
            vmGenerateSummary.setIncentiveType();
        });
}

var vmGenerateSummary = new Vue({
    el: "#generate-summary_content",
    data: _tempData,
    methods: {
        tempShowByDates: function (id) {
            var _this = this;
            var currentElement = _this.$el;
            _this.show_by_date = (id == 1) ? true : false;
            _this.month_picker = (id == 2) ? true : false;
            _this.year_picker = (id == 3) ? true : false;
            _this.incentive_picker = (id == 4) ? true : false;
            _this.show_picker = false;

            if (id == 1) {
                var filterDateRange = $(currentElement).find("#filter_date_range");
                if (typeof filterDateRange !== "undefined" && filterDateRange.length == 1) {
                    filterDateRange.on("change", function () {
                        var thisFilter = this;
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
            var _this = this;
            var currentElement = _this.$el;
            _this.show_picker = (_this.show_picker == true) ? false : true;
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
                            var tempStartDate = picker.startDate.format('MMM DD, YYYY');
                            var tempEndDate = picker.endDate.format('MMM DD, YYYY');
                            var tempFormat = tempStartDate + ' - ' + tempEndDate;
                            $(currentElement).find("#date-range").val(tempFormat);
                        });
            } else {
                _this.renderSelect2Picker();
            }

            return _this;
        },getDateRange: function (from, to) {
            const tempRange = moment(from).format("MM/DD/YYYY") + "-" + moment(to).format("MM/DD/YYYY");
            return tempRange;
        }, setIncentiveType: function () {
            const _this = this;
            const currentRow = _this.row;
            let incentiveType = null;
            if (typeof currentRow.name !== "undefined" && currentRow.name) {
                const tempName = currentRow.name;
                incentiveType = tempName.replace(/_/g, " ");
            }
            globalIncentiveType = incentiveType;
            return incentiveType;
        }, renderSelect2Picker: function () {
            var _this = this;
            var currentElement = _this.$el;
            var tempModal = $(currentElement).closest(".modal");
            setTimeout(function () {
                select2IncentiveType();
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
                        width: '100%',
                        placeholder: "SELECT YEAR",
                        allowClear: true,
                        dropdownParent: tempModal,
                        data: _years
                        /*** ajax: {
                            url: baseUrl('payroll/get_posted_payroll_sheet_years'),
                            dataType: 'JSON',
                            type: 'GET',
                            global: false,
                        }, language: { errorLoading: function () { return "Searching..." } } ***/
                    });
                //payroll incentive year
                $(currentElement).find("select[name='incentive_year']")
                    .select2({
                        width: '100%',
                        placeholder: "SELECT YEAR",
                        allowClear: true,
                        dropdownParent: tempModal,
                        data: _years
                        /*** ajax: {
                            url: baseUrl('payroll/get_posted_payroll_sheet_years'),
                            dataType: 'JSON',
                            type: 'GET',
                            global: false,
                        }, language: { errorLoading: function () { return "Searching..." } } ***/
                });

                $(currentElement).find("select#employees")
                    .select2({
                        width: '100%',
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                        ajax: {
                            url: baseUrl('payroll/select_employee_pssummary'),
                            dataType: 'json',
                            global: false,
                            delay: 250,
                            data: function (params) {
                                params.company_ids = _this.company_ids;
                                params.q = params.term;
                                return params;
                            },
                            processResults: function (data) {
                                return data;
                            }
                        }
                    });

                $(currentElement).find("select#company")
                    .select2({
                        width: '100%',
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                        allowClear: true,
                        data: _companies
                        /*** ajax: {
                            url: baseUrl('payroll/select_company'),
                            dataType: 'json',
                            global: false,
                            delay: 250,
                            processResults: function (data) {
                                return data;
                            },
                        }, language: { errorLoading: function () { return "Searching..." } } ***/
                    }).on("select2:select", function (e) {
                        var _thisSelect2 = this;
                        var selectedValues = $(_thisSelect2).select2("val");
                        _this.company_ids = selectedValues;
                        $(currentElement)
                            .find("select#employees")
                            .val([])
                            .trigger("change");
                    }).on("select2:unselect", function (e) {
                        _this.company_ids = 0;
                        $(currentElement)
                            .find("select#employees")
                            .val([])
                            .trigger("change");
                        $(currentElement)
                            .find("select#payroll_group")
                            .val([])
                            .trigger("change");
                        $("#employees").prop("disabled", false);
                    });
            }, 200);
        }, resetFields: function () {
            var _this = this;
            var currentForm = $(_this.$el).find("#frm-summary-report");
            if (typeof currentForm !== "undefined") {
                $("#employees").empty();
                currentForm.find("input[name=group]")[0].click();
                /*** currentForm.find("select").val("").trigger("change"); ***/
                currentForm.find("select[multiple]").val([]).trigger("change");
                currentForm[0].reset();
                
            }
        },
    }, mounted: function () {
        var _this = this;
        _this.renderSelect2Picker();
    }
});

let tempColumns = [
    
];
// display datatable of payroll summary
let dtSummary = $('#tbl-summary').DataTable({
    serverSide: true,
    processing: true,
    destroy: true,
    paging: false,
    searching: false,
    bInfo : false,
    ajax: {
        url: baseUrl('payroll/get_payroll_summary_request'),
        type: 'POST',
        dataType: 'JSON',
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.ids = _tempIds;
            d.clear_table = _clearTable;
            d.filters = _tempFilter;
        },
        error: function (xhr, error, code) {
            if (error == "parsererror") { dtSummary.ajax.reload(); }
        },
    },
    columns: [
        { 
            title: 'employee_name',
            name: 'employee_name',
            data: 'employee_name', 
            width: "*",
        },
        { 
            data: 'date_start',
            orderable: false, 
            width: "20%",
            render: function(data){
                return moment(data).format("MM/DD/YYYY");
            }
        },
        { 
            data: 'date_end', 
            orderable: false,
            width: "20%",
            render: function(data){
                return moment(data).format("MM/DD/YYYY");
            }
        },
        { 
            data: 'pay_date', 
            orderable: false,
            width: "20%",
            render: function(data){
                return data;
            }
        },
        {
            name: 'basic_rate',
            width: "20%",
            data: 'basic_rate', orderable: false, className: "text-right print-size-10", defaultContent: "0.00", render: function (data) {
                return numberFormat(data);
            }
        },
        {
            name: 'grandtotal',
            data: 'grandtotal', orderable: false, className: "text-right print-size-10", defaultContent: "0.00", render: function (data) {
                return numberFormat(data);
            }
        },
    ],
    rowGroup: {
        startRender: function ( rows, group ) {
            // Group rows based on dataSrc
            var yearly_period = $('#yearly_period').is(":checked");
            var monthly_period = $('#monthly_period').is(":checked");
            var incentive = $('#incentive').is(":checked");
            var date_range_period = $('#date_range_period').is(":checked");
            var company = $("#company option:selected").text();
            if(yearly_period){
                $("#period").html("COMPANY: "+company+"<br>COVERAGE: " + $("select[name='filter_year']").val());
            }else if(monthly_period){
                $("#period").html("COMPANY: "+company+"<br>COVERAGE: " + moment($("select[name='filter_month']").val()).format("MMM") +' '+ $("select[name='filter_year']").val());
            }else if(incentive){
                $("#period").html("COMPANY: "+company+"<br>COVERAGE: " + $(".incentive_coverage").text() + "<br>INCENTIVE: " + $("#incentive_type option:selected").text());
            }else{
                $("#period").html("COMPANY: "+company+"<br>COVERAGE: " + $("#date-range").val());
            }
            
            return group;
        },
        endRender: function ( rows, group ) {
            var totalBasic = rows
                    .data()
                    .pluck('basic_rate')
                    .reduce( function (a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);
            
            var thirteenth_bonus = rows
                .data()
                .pluck('basic_rate')
                .reduce( function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0) / 12;

            var bonus_label;
            if($("#incentive_type").val() == null){
                bonus_label = "13TH/MID PAY";
            }else{
                bonus_label = $("#incentive_type option:selected").text();
            }

            if($('#incentive').is(":checked") || $("#thmonth_toggle").is(":checked")){
                return $('<tr/>').append( `<td colspan='3' class='text-right' style='background-color: #e0e0e0; font-weight: bold;'>Subtotal</td><td class='text-right' style='border-top: 4px double black; background-color: #e0e0e0; font-weight: bold;'>${numberFormat(totalBasic)}<p class='thirteenth' style='text-align: right !important; border-top: 3px solid black; width: 100%; font-weight: bold; margin-top: 1px;'>${bonus_label} : <span>${numberFormat(thirteenth_bonus)}</span></p></td>`);
            }else{
                return $('<tr/>').append( `<td colspan='3' class='text-right' style='background-color: #e0e0e0; font-weight: bold;'>Subtotal</td><td class='text-right' style='border-top: 4px double black; background-color: #e0e0e0; font-weight: bold;'>${numberFormat(totalBasic)}</td>` );
            }
            
        },
        dataSrc: [ 'employee_name' ],
        
    },
    columnDefs: [ {
        targets: [ 0, 5 ],
        visible: false
    } ],
    footerCallback: function (row, data, start, end, display) {
        // Display data in grand total
        var api = this.api(), data;
        var intVal = function (i) {
            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
        };

        let totalRegPay = api
            .column(4)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        let grandTotal = api
            .column(5)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);    
        
        let tempTotal = api
        .column(5)
        .data();

        console.log(tempTotal);

        $(api.column(3).footer()).html("<span class='m--font-boldest'>GRANDTOTAL</span>");

        var incentive_type = $("#incentive_type option:selected").text();

        if(!incentive_type){
            $(api.column(4).footer()).html("<span class='m--font-boldest'>"+numberFormat(grandTotal)+"</span>");
        }else{
            $(api.column(4).footer()).html("<span class='m--font-boldest'>"+$("#incentive_type option:selected").text()+" = "+numberFormat(grandTotal)+"</span>");
        }

    }
});
// end of datatable display for payroll summary

$.validate({
    form: "#frm-summary-report",
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        var currentForm = form[0];
        var formUrl = currentForm.action;
        var formData = $(currentForm).serialize();
        if($('#incentive').is(":checked") || $("#thmonth_toggle").is(":checked")){
            $('.ps_legend').removeClass('m--hide');
        }else{
            $('.ps_legend').addClass('m--hide');
        }
        getScriptRendering(formUrl, formData, currentForm);
        return false;
    }
});

$("#payroll_group").select2({
    placeholder: 'Select',
    width: '100%',
    ajax: {
        url: baseUrl("payroll/select_payroll_group"),
        dataType: "json",
        type: 'get',
        delay: 250,
        global: false,
        data: function (params) {
            params.company_id = $("form#frm-summary-report select#company").val();
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
                        const tempEmployeeSelector = $("form#frm-summary-report select#employees");
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
            const tempEmployeeSelector = $("form#frm-summary-report select#employees");
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
        const tempEmployeeSelector = $("form#frm-summary-report select#employees");
        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
            tempEmployeeSelector.empty();
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
                        const tempEmployeeSelector = $("form#frm-summary-report select#employees");
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

var getScriptRendering = function (formUrl, formData, currentForm) {
    let sum = 0;
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
                toastr.success(json.toastr_msg, "Filtered Summary");
            } else {
                _clearTable = true;
                toastr.error(json.toastr_msg, "Filtered Summary");
            }
            dtSummary.columns.adjust();
            dtSummary.ajax.reload();
        }
    });
}

$(document).ready(function(){
    select2IncentiveType();
});