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
let _totalTaxable = 0;
var _tempData = {
    show_by_date: true, show_picker: false,
    year_picker: false, month_picker: false, company_ids: [],
};

var vmGeneratejournal = new Vue({
    el: "#generate-journal_content",
    data: _tempData,
    methods: {
        tempShowByDates: function (id) {
            var _this = this;
            var currentElement = _this.$el;
            _this.show_by_date = (id == 1) ? true : false;
            _this.month_picker = (id == 2) ? true : false;
            _this.year_picker = (id == 3) ? true : false;
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
                setTimeout(function () {
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
                }, 500);
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
                        width: '100%',
                        placeholder: "SELECT YEAR",
                        allowClear: true,
                        dropdownParent: tempModal,
                        ajax: {
                            url: baseUrl('payroll/reports/get_posted_payroll_sheet_years'),
                            dataType: 'JSON',
                            type: 'GET',
                            global: false,
                        }, language: { errorLoading: function () { return "Searching..." } }
                    });

                $(currentElement).find("select#employee")
                    .select2({
                        width: '100%',
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                        ajax: {
                            url: baseUrl('payroll/reports/select_employee'),
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
                        }, language: { errorLoading: function () { return "Searching..." } }
                    });

                $(currentElement).find("select#company")
                    .select2({
                        width: '100%',
                        placeholder: "SELECT AN OPTION",
                        dropdownParent: tempModal,
                        allowClear: true,
                        ajax: {
                            url: baseUrl('payroll/select_company'),
                            dataType: 'json',
                            global: false,
                            delay: 250,
                            processResults: function (data) {
                                return data;
                            },
                        }, language: { errorLoading: function () { return "Searching..." } }
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

// display datatable of payroll journal
let dtLedger = $('#tbl-ledger').DataTable({
    dom: "<'row'<'col-md-9 dtDetails'><'col-md-3 dtActions m--hide'B>>rt",
    // serverSide: true,
    // processing: true,
    // ordering: false,
    // destroy: true,
    serverSide: true,
    processing: true,
    destroy: true,
    paging: false,
    searching: false,
    bInfo : false,
    ajax: {
        url: baseUrl('payroll/reports/get_payroll_ledger_request'),
        type: 'POST',
        dataType: 'JSON',
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.ids = _tempIds;
            d.clear_table = _clearTable;
        }, error: function (xhr, error, code) {
            if (error == "parsererror") { dtLedger.ajax.reload(); }
        },
    },
    columns: [
        { data: 'loan_code'},
        { data: 'pay_date'},
        { data: 'advance_name'},
        { data: 'reference'},
        { data: 'interest'},
        { data: 'payment'},
        { data: 'balance', render: function (data, type, row, meta) {
            return data;
        }},


    ],
    rowGroup: {
        startRender: function ( rows, group ) {
            return $('<tr><td colspan="7" class="bg-secondary">'+group+'</td><td>test</td></tr>');
        },
        endRender: function ( rows, group ) {
            var last_row = dtLedger.row(':last').data();
            // console.log(last_row);
            return $('<tr/>').append( `<td colspan='5' class='text-right' style='background-color: #e0e0e0;'>Over all total:`+group+`</td><td>`+last_row+`</td>`);
        },
        dataSrc: [ 'loan_code' ],
        
    },
    columnDefs: [ {
        targets: [ 0 ],
        visible: false
    } ],
});
// end of datatable display for payroll ledger

$.validate({
    form: "#frm-journal-report",
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        var currentForm = form[0];
        var formUrl = currentForm.action;
        var formData = $(currentForm).serialize();
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

                let company_name = $("#company option:selected").text();
                let employee_name = $("#employee option:selected").text();
                let pay_coverage = $("#date-range").val();
                $(".company").html("");
                $(".company").html(company_name);
                $(".employee_name").html(employee_name);
                $(".employee_id").html(json.employee_id);
                _tempFilter = Object.assign({}, json.filters);
                toastr.success(json.toastr_msg, "Filtered journal");
            } else {
                _clearTable = true;
                toastr.error(json.toastr_msg, "Filtered journal");
            }

            dtLedger.columns.adjust();
            dtLedger.ajax.reload();
        }
    });
}