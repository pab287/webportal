const fromFilterPayrollHistory = $('#frm-filter-payroll-history');

var vmFilterHistory = new Vue({
    el: "#tempFilter",
    data: { show_by_date: true, show_picker: false },
    methods: {
        tempShowByDates: function (id) {
            var _this = this;
            var currentElement = _this.$el;
            _this.show_by_date = (id == 1) ? true : false;
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
                setTimeout(function () {
                    $(currentElement).find("select[name='filter_month']")
                        .select2({
                            width: '100%',
                            data: months,
                            placeholder: "SELECT MONTH",
                            allowClear: true
                        });

                    $(currentElement).find("select[name='filter_year']")
                        .select2({
                            width: '100%',
                            placeholder: "SELECT YEAR",
                            allowClear: true,
                            ajax: {
                                url: baseUrl('payroll/get_posted_payroll_sheet_years'),
                                dataType: 'JSON',
                                type: 'GET'
                            }
                        });
                }, 200);
            }
            return _this;
        }
    }
});

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

$("input[name='filter_by_date_range']")
    .on("change", function () {
        if (this.checked) {
            $("#filter-by-date-range").removeClass('m--hide');
            $("#filter-by-month-year").addClass('m--hide');
        } else {
            $("#filter-by-date-range").addClass('m--hide');
            $("#filter-by-month-year").removeClass('m--hide');
        }
    });

$("select[name='filter_month']")
    .select2({
        width: '100%',
        data: months,
        placeholder: "SELECT MONTH",
        allowClear: true
    });

$("select[name='filter_year']")
    .select2({
        width: '100%',
        placeholder: "SELECT YEAR",
        allowClear: true,
        ajax: {
            url: baseUrl('payroll/get_posted_payroll_sheet_years'),
            dataType: 'JSON',
            type: 'GET'
        }
    });

$("input[name='group']")
    .on("change", function () {
        const val = $(this).val();
        /*const filterYearSelectedOption = new Option(moment().format("YYYY"), moment().format("YYYY"), false, true);
        switch (parseInt(val)) {
            case 1:
                $("select[name='filter_year']").empty();
                break;
            case 2:
                $("select[name='filter_year']").append(filterYearSelectedOption).trigger("change");
                break;
            case 3:
                $("select[name='filter_year']").append(filterYearSelectedOption).trigger("change");
                break;
        }*/

        dtPayrollHistory.ajax.reload();
    });

$.validate({
    form: '#frm-filter-payroll-history',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        dtPayrollHistory.ajax.reload();
        return false;
    }
});

// TODO: STOPPED AT GROUP BY FUNCTION WORKING NEED TO CONTINUE ON MANIPULATING DATA TABLE WHEN GROUPING IS CHANGED

let dtPayrollHistory = $('#tbl-payroll-history')
    .DataTable({
        dom: 'rt',
        serverSide: true,
        processing: true,
        destroy: true,
        ajax: {
            url: baseUrl('payroll/get_payroll_history'),
            type: 'POST',
            dataType: 'JSON',
            data: function (d) {
                var _filterDate = $("#filter_date_range");
                d.csrf_token = _csrf_hash;
                d.group_by = $('input[name="group"]:checked').val();
                if (typeof _filterDate !== "undefined" && _filterDate.length == 1) { d.by_date_range = _filterDate[0].checked; }
                d.month = $('select[name="filter_month"]').val();
                d.year = $('select[name="filter_year"]').val();
                d.range = $('input[name="date_range"]').val();
            },
        },
        columns: [{
            data: 'pay_date',
            render: function (data, type, row, meta) {
                return moment(data).format('ll');
            }
        }, {
            data: 'pay_month',
            render: function (data, type, row, meta) {
                const month = months.find(({ id }) => parseInt(data) === id);
                return month.text;
            },
            visible: false,
        }, {
            data: 'date_start',
            render: function (data, type, row, meta) {
                return moment(data).format('ll') + ' - ' + moment(row.date_end).format('ll');
            }
        }, {
            data: 'sss_ee',
            render: function (data, type, row, meta) {
                return (parseFloat(data) + parseFloat(row.sss_er))
                    .toLocaleString('en-US', { maximumFractionDigits: 2 });
            },
            className: 'text-right'
        }, {
            data: 'phic_ee',
            render: function (data, type, row, meta) {
                return (parseFloat(data) + parseFloat(row.phic_er))
                    .toLocaleString('en-US', { maximumFractionDigits: 2 });
            },
            className: 'text-right'
        }, {
            data: 'hdmf_ee',
            render: function (data, type, row, meta) {
                return (parseFloat(data) + parseFloat(row.hdmf_er))
                    .toLocaleString('en-US', { maximumFractionDigits: 2 });
            },
            className: 'text-right'
        }, {
            data: 'tax_total',
            render: function (data, type, row, meta) {
                return parseFloat(data).toLocaleString('en-US', { maximumFractionDigits: 2 });
            },
            className: 'text-right'
        }, {
            data: 'total_net_pay',
            render: function (data, type, row, meta) {
                return parseFloat(data).toLocaleString('en-US', { maximumFractionDigits: 2 });
            },
            className: 'text-right'
        }]
    });