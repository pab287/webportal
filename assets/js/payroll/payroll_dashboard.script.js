am4core.ready(function () {
});

var loansData = [];

function getLoansGraph(graphsFilter) {
    am4core.useTheme(am4themes_material);
    $.ajax({
        type: "POST",
        url: baseUrl('payroll/payroll/get_loans'),
        dataType: "JSON",
        data: { 'csrf_token': _csrf_hash, graphsFilter},
        success: function (result) {
            var chart = am4core.create('chartdivLoans', am4charts.XYChart)
            

            chart.colors.list = [
                am4core.color("#F44336"),
                am4core.color("#E91E63"),
                am4core.color("#9C27B0"),
                am4core.color("#673AB7"),
                am4core.color("#3F51B5"),
                am4core.color("#2196F3"),
                am4core.color("#03A9F4"),
                am4core.color("#00BCD4"),
                am4core.color("#009688"),
                am4core.color("#4CAF50"),
                am4core.color("#8BC34A"),
                am4core.color("#CDDC39"),
                am4core.color("#FFEB3B"),
                am4core.color("#FFC107"),
                am4core.color("#FF9800"),
                am4core.color("#FF5722"),
                am4core.color("#795548"),
                am4core.color("#9E9E9E"),
                am4core.color("#607D8B"),
              ];
            chart.colors.step = 2;
            
            chart.legend = new am4charts.Legend()
            chart.legend.position = 'top'
            chart.legend.paddingBottom = 20
            chart.legend.labels.template.maxWidth = 95

            var company_title = $("#company_loans option:selected").text();
            var year_title = $("#filter_year_loans option:selected").text();
            var title = chart.titles.create();
            if(year_title == ""){
                var display_year = new Date().getFullYear();
            }else{
                var display_year = year_title;
            }
            if(company_title == ""){
                title.text = "ALL COMPANIES" + ' - '+display_year;
            }else{
                title.text = company_title+' - '+display_year;
            }
            
            title.fontSize = 20;
            title.marginBottom = 10;

            var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
            xAxis.dataFields.category = 'month'
            xAxis.renderer.grid.template.location = 100;
            xAxis.renderer.cellStartLocation = 0.2
            xAxis.renderer.cellEndLocation = 0.8
            xAxis.renderer.minGridDistance = 30;

            var label = xAxis.renderer.labels.template;
            label.wrap = true;
            label.maxWidth = 120;

            var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
            yAxis.min = 0;

            function createSeries(value, name) {
                var series = chart.series.push(new am4charts.ColumnSeries())
                series.dataFields.valueY = value
                series.dataFields.categoryX = 'month'
                series.name = name
                series.columns.template.width = am4core.percent(100);
                series.columns.template.tooltipText = "{name}\n[bold]{valueY}[/]";
                series.tooltip.pointerOrientation = "vertical";
                series.tooltip.background.strokeWidth = 2;
                series.tooltip.label.fill = series.stroke;
                return series;
            } 
            createSeries('internal', 'INTERNAL');
            createSeries('external', 'EXTERNAL');
            chart.data = result;
        }
    });
}

function getOvertimeGraph(graphsFilter) {
    am4core.useTheme(am4themes_material);
    $.ajax({
        type: "POST",
        url: baseUrl('payroll/payroll/get_overtime_graph'),
        dataType: "JSON",
        data: { 'csrf_token': _csrf_hash, graphsFilter},
        success: function (result) {
            var chart = am4core.create('overtime-graph', am4charts.XYChart)

            chart.colors.list = [
                am4core.color("#F44336"),
                am4core.color("#E91E63"),
                am4core.color("#9C27B0"),
                am4core.color("#673AB7"),
                am4core.color("#3F51B5"),
                am4core.color("#2196F3"),
                am4core.color("#03A9F4"),
                am4core.color("#00BCD4"),
                am4core.color("#009688"),
                am4core.color("#4CAF50"),
                am4core.color("#8BC34A"),
                am4core.color("#CDDC39"),
                am4core.color("#FFEB3B"),
                am4core.color("#FFC107"),
                am4core.color("#FF9800"),
                am4core.color("#FF5722"),
                am4core.color("#795548"),
                am4core.color("#9E9E9E"),
                am4core.color("#607D8B"),
              ];
            chart.colors.step = 3;

            chart.legend = new am4charts.Legend()
            chart.legend.position = 'top'

            var title = chart.titles.create();
            title.text = "OVERTIME";
            title.fontSize = 20;
            title.marginBottom = 10;

            var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
            xAxis.dataFields.category = 'month'
            xAxis.renderer.grid.template.location = 100;
            xAxis.renderer.cellStartLocation = 0.2
            xAxis.renderer.cellEndLocation = 0.8
            xAxis.renderer.minGridDistance = 30;

            var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
            yAxis.min = 0;

            var currentYr = new Date().getFullYear();
            var previousYr = currentYr - 1;

            chart.cursor = new am4charts.XYCursor();
            createSeries(currentYr, currentYr);
            createSeries(previousYr, previousYr);

            function createSeries(value, name){
                var seriesData = chart.series.push(new am4charts.LineSeries());
                seriesData.dataFields.valueY = value;
                seriesData.dataFields.categoryX = "month";
                seriesData.strokeWidth = 3;
                seriesData.tooltipText = "[bold]OT Hrs:[/] [bold]{valueY}[/]";
                seriesData.name = name.toString();
                seriesData.tooltip.background.strokeWidth = 2;
            }

            chart.data = result;
        }
    });
}

function getAllowanceGraph(graphsFilter) {
    am4core.useTheme(am4themes_material);
    $.ajax({
        type: "POST",
        url: baseUrl('payroll/payroll/get_allowance_graph'),
        dataType: "JSON",
        data: { 'csrf_token': _csrf_hash, graphsFilter},
        success: function (result) {
            var chart = am4core.create('allowance-graph', am4charts.XYChart)

            chart.colors.list = [
                am4core.color("#F44336"),
                am4core.color("#E91E63"),
                am4core.color("#9C27B0"),
                am4core.color("#673AB7"),
                am4core.color("#3F51B5"),
                am4core.color("#2196F3"),
                am4core.color("#03A9F4"),
                am4core.color("#00BCD4"),
                am4core.color("#009688"),
                am4core.color("#4CAF50"),
                am4core.color("#8BC34A"),
                am4core.color("#CDDC39"),
                am4core.color("#FFEB3B"),
                am4core.color("#FFC107"),
                am4core.color("#FF9800"),
                am4core.color("#FF5722"),
                am4core.color("#795548"),
                am4core.color("#9E9E9E"),
                am4core.color("#607D8B"),
              ];
            chart.colors.step = 9;

            chart.legend = new am4charts.Legend()
            chart.legend.position = 'top'

            var title = chart.titles.create();
            title.text = "TOTAL ALLOWANCES PAID";
            title.fontSize = 20;
            title.marginBottom = 10;

            var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
            xAxis.dataFields.category = 'month'
            xAxis.renderer.grid.template.location = 100;
            xAxis.renderer.cellStartLocation = 0.2
            xAxis.renderer.cellEndLocation = 0.8
            xAxis.renderer.minGridDistance = 30;

            var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
            yAxis.min = 0;

            var currentYr = new Date().getFullYear();
            var previousYr = currentYr - 1;

            chart.cursor = new am4charts.XYCursor();
            createSeries(currentYr, currentYr);
            createSeries(previousYr, previousYr);

            function createSeries(value, name){
                var seriesData = chart.series.push(new am4charts.LineSeries());
                seriesData.dataFields.valueY = value;
                seriesData.dataFields.categoryX = "month";
                seriesData.strokeWidth = 3;
                seriesData.tooltipText = "[bold]₱ {valueY}[/]";
                seriesData.name = name.toString();
                seriesData.tooltip.background.strokeWidth = 2;
            }

            chart.data = result;
        }
    });
}

function getTaxGraph(graphsFilter) {
    am4core.useTheme(am4themes_material);
    $.ajax({
        type: "POST",
        url: baseUrl('payroll/payroll/get_tax_graph'),
        dataType: "JSON",
        data: { 'csrf_token': _csrf_hash, graphsFilter},
        success: function (result) {
            var chart = am4core.create('tax-graph', am4charts.XYChart)

            chart.colors.list = [
                am4core.color("#F44336"),
                am4core.color("#E91E63"),
                am4core.color("#9C27B0"),
                am4core.color("#673AB7"),
                am4core.color("#3F51B5"),
                am4core.color("#2196F3"),
                am4core.color("#03A9F4"),
                am4core.color("#00BCD4"),
                am4core.color("#009688"),
                am4core.color("#4CAF50"),
                am4core.color("#8BC34A"),
                am4core.color("#CDDC39"),
                am4core.color("#FFEB3B"),
                am4core.color("#FFC107"),
                am4core.color("#FF9800"),
                am4core.color("#FF5722"),
                am4core.color("#795548"),
                am4core.color("#9E9E9E"),
                am4core.color("#607D8B"),
              ];
            chart.colors.step = 5;

            chart.legend = new am4charts.Legend()
            chart.legend.position = 'top'

            var title = chart.titles.create();
            title.text = "TOTAL TAX PAID";
            title.fontSize = 20;
            title.marginBottom = 10;

            var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
            xAxis.dataFields.category = 'month'
            xAxis.renderer.grid.template.location = 100;
            xAxis.renderer.cellStartLocation = 0.2
            xAxis.renderer.cellEndLocation = 0.8
            xAxis.renderer.minGridDistance = 30;

            var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
            yAxis.min = 0;

            var currentYr = new Date().getFullYear();
            var previousYr = currentYr - 1;

            chart.cursor = new am4charts.XYCursor();
            createSeries(currentYr, currentYr);
            createSeries(previousYr, previousYr);

            function createSeries(value, name){
                var seriesData = chart.series.push(new am4charts.LineSeries());
                seriesData.dataFields.valueY = value;
                seriesData.dataFields.categoryX = "month";
                seriesData.strokeWidth = 3;
                seriesData.tooltipText = "[bold]₱ {valueY}[/]";
                seriesData.name = name.toString();
                seriesData.tooltip.background.strokeWidth = 2;
            }

            chart.data = result;
        }
    });
}

function getSalaryHike(graphsFilter) {
    am4core.useTheme(am4themes_material);
    $.ajax({
        type: "POST",
        url: baseUrl('payroll/payroll/get_salary_hike'),
        dataType: "JSON",
        data: { 'csrf_token': _csrf_hash, graphsFilter},
        success: function (result) {
            var chart = am4core.create('chartdivAbsent', am4charts.XYChart)
            
            chart.colors.list = [
                am4core.color("#F44336"),
                am4core.color("#E91E63"),
                am4core.color("#9C27B0"),
                am4core.color("#673AB7"),
                am4core.color("#3F51B5"),
                am4core.color("#2196F3"),
                am4core.color("#03A9F4"),
                am4core.color("#00BCD4"),
                am4core.color("#009688"),
                am4core.color("#4CAF50"),
                am4core.color("#8BC34A"),
                am4core.color("#CDDC39"),
                am4core.color("#FFEB3B"),
                am4core.color("#FFC107"),
                am4core.color("#FF9800"),
                am4core.color("#FF5722"),
                am4core.color("#795548"),
                am4core.color("#9E9E9E"),
                am4core.color("#607D8B"),
              ];
            chart.colors.step = 6;

            var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
            xAxis.dataFields.category = 'year'
            xAxis.renderer.grid.template.location = 100;
            xAxis.renderer.cellStartLocation = 0.2
            xAxis.renderer.cellEndLocation = 0.8
            xAxis.renderer.minGridDistance = 30;

            var label = xAxis.renderer.labels.template;
            label.wrap = true;
            label.maxWidth = 120;

            var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
            yAxis.min = 0;

            function createSeries(value, name) {
                var series = chart.series.push(new am4charts.LineSeries())
                series.dataFields.valueY = value
                series.dataFields.categoryX = 'year'
                series.strokeWidth = 2;
                series.minBulletDistance = 10;
                series.tooltip.background.strokeWidth = 2;
                var bullet = series.bullets.push(new am4charts.CircleBullet());
                bullet.circle.strokeWidth = 1;
                bullet.tooltipText = "SALARY HIKE\n[bold]{valueY}[/]";
                return series;
            } 
            createSeries('hike', 'SALARY HIKE');
            chart.data = result;
        }
    });
}

function getContributionGraph(graphsFilter) {
    am4core.useTheme(am4themes_material);
    $.ajax({
        type: "POST",
        url: baseUrl('payroll/payroll/get_contribution_graph'),
        dataType: "JSON",
        data: { 'csrf_token': _csrf_hash, graphsFilter},
        success: function (result) {
            var chart = am4core.create('contribution-graph', am4charts.XYChart)
            
            chart.colors.list = [
                am4core.color("#F44336"),
                am4core.color("#E91E63"),
                am4core.color("#9C27B0"),
                am4core.color("#673AB7"),
                am4core.color("#3F51B5"),
                am4core.color("#2196F3"),
                am4core.color("#03A9F4"),
                am4core.color("#00BCD4"),
                am4core.color("#009688"),
                am4core.color("#4CAF50"),
                am4core.color("#8BC34A"),
                am4core.color("#CDDC39"),
                am4core.color("#FFEB3B"),
                am4core.color("#FFC107"),
                am4core.color("#FF9800"),
                am4core.color("#FF5722"),
                am4core.color("#795548"),
                am4core.color("#9E9E9E"),
                am4core.color("#607D8B"),
              ];
            chart.colors.step = 7;

            chart.legend = new am4charts.Legend()
            chart.legend.position = 'top'

            var title = chart.titles.create();
            title.text = "TOTAL CONTRIBUTION PAID";
            title.fontSize = 20;
            title.marginBottom = 10;

            var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
            xAxis.dataFields.category = 'year'
            xAxis.renderer.grid.template.location = 100;
            xAxis.renderer.cellStartLocation = 0.2
            xAxis.renderer.cellEndLocation = 0.8
            xAxis.renderer.minGridDistance = 30;

            var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
            yAxis.min = 0;

            chart.cursor = new am4charts.XYCursor();
            createSeries('sss', 'SSS');
            createSeries('phic', 'PHILHEALTH');
            createSeries('hdmf', 'PAGIBIG');

            function createSeries(value, name){
                var seriesData = chart.series.push(new am4charts.ColumnSeries());
                seriesData.dataFields.valueY = value;
                seriesData.dataFields.categoryX = "year";
                seriesData.strokeWidth = 1;
                seriesData.tooltipText = "[bold]{name}: ₱ {valueY}[/]";
                seriesData.name = name.toString();
                seriesData.tooltip.background.strokeWidth = 2;
            }
            chart.data = result;
        }
    });
}

$(document).ready(function(){
    getLoansGraph();
    getSalaryHike();
    getOvertimeGraph();
    getAllowanceGraph();
    getTaxGraph();
    getContributionGraph();
   $(".btn_report").click(function(){
       form_widget = $(this).data('id');
   });
   $('#generate-report-modal').on('hidden.bs.modal', function () {
       $(this).removeData();
   });
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
var _tempData = {
    show_by_date: true, show_picker: false,
    year_picker: false, month_picker: false, company_ids: [],
};

var tempValues = [];

var vue_head_data = new Vue({
    el: "#vue_head_data",
    data: { 
        filters: []
     },
});

var vue_head_data_ot = new Vue({
    el: "#vue_head_data_ot",
    data: { 
        filters: []
     },
});

var vue_head_data_allowance = new Vue({
    el: "#vue_head_data_allowance",
    data: { 
        filters: []
     },
});

var vue_head_data_tax = new Vue({
    el: "#vue_head_data_tax",
    data: { 
        filters: []
     },
});

var vue_head_data_contrib = new Vue({
    el: "#vue_head_data_contrib",
    data: { 
        filters: []
     },
});

var vue_portlet = new Vue({
    el: "#vue_portlet",
    data: { 
        contributions: [],
     },
});

var vue_portlet_ot = new Vue({
    el: "#vue_portlet_ot",
    data: { 
        contributions: [],
     },
});

var vue_portlet_allowance = new Vue({
    el: "#vue_portlet_allowance",
    data: { 
        contributions: [],
     },
});

var vue_portlet_tax = new Vue({
    el: "#vue_portlet_tax",
    data: { 
        contributions: [],
     },
});

var vue_portlet_contrib = new Vue({
    el: "#vue_portlet_contrib",
    data: { 
        contributions: [],
     },
});

let form_widget;

var _tempDataLoans = {
    company_ids: []
};

var vmGenerateLoans = new Vue({
    el: "#generate-report-modal-loans",
    data: _tempDataLoans,
    methods: {
        showPostedYears: function(){
            var _this = this;
            var currentElementLoans = _this.$el;
            var tempModalLoans = $(currentElementLoans).closest(".modal");
            
            setTimeout(function () {
                $(currentElementLoans).find("select#filter_year_loans")
                .select2({
                    width: '100%',
                    placeholder: "SELECT YEAR",
                    allowClear: true,
                    // dropdownParent: tempModalLoans,
                    ajax: {
                        url: baseUrl('payroll/reports/get_posted_payroll_sheet_years'),
                        dataType: 'JSON',
                        type: 'GET',
                        global: false,
                    }, language: { errorLoading: function () { return "Searching..." } }
                }).on('hidden.bs.modal', function () {
                    $(this).val([]).trigger("change");
                });
                $(currentElementLoans).find("select#company_loans")
                .select2({
                    width: '100%',
                    placeholder: "SELECT AN OPTION",
                    dropdownParent: tempModalLoans,
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
                }).on('hidden.bs.modal', function () {
                    $(this).val([]).trigger("change");
                });
            }, 500);
        }, generateDataLoans: function(){
            $.validate({
                form: "#frm-loans-report",
                lang: "en",
                scrollToTopOnError: false,
                onSuccess: function (form) {
                    var tempDataLoans = $("#frm-loans-report").serializeArray();
                    var graphsFilter = tempDataLoans;
                    getLoansGraph(graphsFilter);
                    return false;
                }
                
            });
        }
    },mounted: function () {
        this.showPostedYears();
    }
});

var vmGeneratejournal = new Vue({
    el: "#generate-report-modal",
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
            _this.filterBy();
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
                        }).on('hidden.bs.modal', function () {
                            $(this).val([]).trigger("change");
                        });
                }, 500);
            } else {
                this.filterBy();
            }
            return _this;
        },
        filterBy: function(){
            var _this = this;
            var currentElement = _this.$el;
            var tempModal = $(currentElement).closest(".modal");
            setTimeout(function () {
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
                }).on('hidden.bs.modal', function () {
                    $(this).val([]).trigger("change");
                });
                $(currentElement).find("select#filter_month")
                .select2({
                    width: '100%',
                    data: months,
                    placeholder: "SELECT MONTH",
                    allowClear: true,
                    // dropdownParent: tempModal,
                }).on('hidden.bs.modal', function () {
                    $(this).val([]).trigger("change");
                });

                $(currentElement).find("select#filter_year")
                .select2({
                    width: '100%',
                    placeholder: "SELECT YEAR",
                    allowClear: true,
                    // dropdownParent: tempModal,
                    ajax: {
                        url: baseUrl('payroll/reports/get_posted_payroll_sheet_years'),
                        dataType: 'JSON',
                        type: 'GET',
                        global: false,
                    }, language: { errorLoading: function () { return "Searching..." } }
                }).on('hidden.bs.modal', function () {
                    $(this).val([]).trigger("change");
                });
            },200);
        },resetFields: function (e) {
            e.preventDefault();
            var _this = this;
            var currentForm = $(_this.$el).find("#frm-journal-report");
            // if (typeof currentForm !== "undefined") {
                currentForm.find("input[name=group]")[0].click();
                currentForm.find("select").val("").trigger("change");
                currentForm.find("select[multiple]").val([]).trigger("change");
                currentForm[0].reset();
            // }
        },
        generateData: function(){
            if(form_widget == 'contribution'){
                $.validate({
                    form: "#frm-journal-report",
                    lang: "en",
                    scrollToTopOnError: false,
                    onSuccess: function (form) {
                        const formData = new FormData(form[0]);
                        var formDataC = $("#frm-journal-report").serializeArray();
                        formData.append('csrf_token', _csrf_hash);
                            $.ajax({
                                url: baseUrl('payroll/payroll/get_paid_contributions'),
                                type: "POST",
                                dataType: "JSON",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function (data) {
                                    vue_portlet_contrib.contributions = Object.assign({}, data);
                                    vue_head_data_contrib.filters = Object.assign({}, data.filters);
                                    let contributions_generate = $("#export_contributions").DataTable({
                                        dom: '<"toolbar">frtlip',
                                        destroy: true,
                                        paging: false,
                                        searching: false,
                                        bInfo : false,
                                        ordering: false,
                                        ajax: {
                                            url: baseUrl('payroll/payroll/get_paid_contributions'),
                                            type: 'POST',
                                            dataType: 'json',
                                            data: function (d) {
                                                $.each(formDataC, function(key, val) {
                                                    d[val.name] = val.value;
                                                });
                                            }
                                        },
                                        columns: [
                                            { data: 'company'},
                                            { data: 'sss', className:'text-right', render: function (data, meta, row) {
                                                return '₱ '+numberFormat(data);
                                            }},
                                            { data: 'phil', className:'text-right', render: function (data, meta, row) {
                                                return '₱ '+numberFormat(data);
                                            }},
                                            { data: 'hdmf', className:'text-right', render: function (data, meta, row) {
                                                return '₱ '+numberFormat(data);
                                            }},
                                        ],
                                        buttons: [ 
                                            {
                                                extend: 'excel',
                                                text: 'EXCEL',
                                                title: 'CONTRIBUTION REPORT BY COMPANY'+ vue_head_data_ot.filters.date,
                                                footer: true
                                    
                                            }
                                        ], footerCallback: function (row, data, start, end, display) {
                                            var api = this.api(), data;
                                            // Remove the formatting to get integer data for summation
                                            var intVal = function (i) {
                                                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                                            };
                                    
                                            let totalSSS = api
                                                .column(1)
                                                .data()
                                                .reduce(function (a, b) {
                                                    return intVal(a) + intVal(b);
                                                }, 0);

                                            let totalPHIL = api
                                                .column(2)
                                                .data()
                                                .reduce(function (a, b) {
                                                    return intVal(a) + intVal(b);
                                                }, 0);
                                            
                                            let totalHDMF = api
                                                .column(3)
                                                .data()
                                                .reduce(function (a, b) {
                                                    return intVal(a) + intVal(b);
                                                }, 0);
                                            $(api.column(1).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalSSS) + "</span>");
                                            $(api.column(2).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalPHIL) + "</span>");
                                            $(api.column(3).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalHDMF) + "</span>");
                                        }
                                    })
                                    $("#export_contrib").click(function(){
                                        contributions_generate.button(".buttons-excel").trigger();
                                    });
                                }
                            });
                        return false;
                    }
                });
            }else if(form_widget == 'overtime'){
                $.validate({
                    form: "#frm-journal-report",
                    lang: "en",
                    scrollToTopOnError: false,
                    onSuccess: function (form) {
                        var formDataC = $("#frm-journal-report").serializeArray();
                        const formData = new FormData(form[0]);
                        formData.append('csrf_token', _csrf_hash);
                            $.ajax({
                                url: baseUrl('payroll/payroll/get_overtime'),
                                type: "POST",
                                dataType: "JSON",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function (data) {
                                    vue_portlet_ot.contributions = Object.assign({}, data);
                                    vue_head_data_ot.filters = Object.assign({}, data.filters);
                                    let overtime_generate = $("#export_overtime").DataTable({
                                        dom: '<"toolbar">frtlip',
                                        destroy: true,
                                        paging: false,
                                        searching: false,
                                        bInfo : false,
                                        ordering: false,
                                        ajax: {
                                            url: baseUrl('payroll/payroll/get_overtime'),
                                            type: 'POST',
                                            dataType: 'json',
                                            data: function (d) {
                                                $.each(formDataC, function(key, val) {
                                                    d[val.name] = val.value;
                                                });
                                            }
                                        },
                                        columns: [
                                            { data: 'company'},
                                            { data: 'time', render: function (data, meta, row) {
                                                return numberFormat(data);
                                            }},
                                            { data: 'amount', className:'text-right', render: function (data, meta, row) {
                                                return '₱ '+numberFormat(data);
                                            }},
                                        ],
                                        buttons: [ 
                                            {
                                                extend: 'excel',
                                                text: 'EXCEL',
                                                title: 'OVERTIME REPORT BY COMPANY'+ vue_head_data_ot.filters.date,
                                                footer: true
                                    
                                            }
                                        ], footerCallback: function (row, data, start, end, display) {
                                            var api = this.api(), data;
                                            // Remove the formatting to get integer data for summation
                                            var intVal = function (i) {
                                                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                                            };
                                    
                                            let totalHRS = api
                                                .column(1)
                                                .data()
                                                .reduce(function (a, b) {
                                                    return intVal(a) + intVal(b);
                                                }, 0);
                                            
                                            let totalWAGE = api
                                                .column(2)
                                                .data()
                                                .reduce(function (a, b) {
                                                    return intVal(a) + intVal(b);
                                                }, 0);
                                            $(api.column(1).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalHRS) + "</span>");
                                            $(api.column(2).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalWAGE) + "</span>");
                                        }
                                    })
                                    $("#export_ot").click(function(){
                                        overtime_generate.button(".buttons-excel").trigger();
                                    });
                                }
                            });
                        return false;
                    }
                });
            }else if(form_widget == 'allowances'){
                $.validate({
                    form: "#frm-journal-report",
                    lang: "en",
                    scrollToTopOnError: false,
                    onSuccess: function (form) {
                        var formDataC = $("#frm-journal-report").serializeArray();
                        const formData = new FormData(form[0]);
                        formData.append('csrf_token', _csrf_hash);
                            $.ajax({
                                url: baseUrl('payroll/payroll/get_allowances'),
                                type: "POST",
                                dataType: "JSON",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function (data) {
                                    vue_portlet_allowance.contributions = Object.assign({}, data);
                                    vue_head_data_allowance.filters = Object.assign({}, data.filters);
                                    let allowance_generate = $("#export_allowances").DataTable({
                                        dom: '<"toolbar">frtlip',
                                        destroy: true,
                                        paging: false,
                                        searching: false,
                                        bInfo : false,
                                        ordering: false,
                                        ajax: {
                                            url: baseUrl('payroll/payroll/get_allowances'),
                                            type: 'POST',
                                            dataType: 'json',
                                            data: function (d) {
                                                $.each(formDataC, function(key, val) {
                                                    d[val.name] = val.value;
                                                });
                                            }
                                        },
                                        columns: [
                                            { data: 'company'},
                                            { data: 'amount', className:'text-right', render: function (data, meta, row) {
                                                return '₱ '+numberFormat(data);
                                            }},
                                        ],
                                        buttons: [ 
                                            {
                                                extend: 'excel',
                                                text: 'EXCEL',
                                                title: 'ALLOWANCE REPORT BY COMPANY'+ vue_head_data_ot.filters.date,
                                                footer: true
                                    
                                            }
                                        ], footerCallback: function (row, data, start, end, display) {
                                            var api = this.api(), data;
                                            // Remove the formatting to get integer data for summation
                                            var intVal = function (i) {
                                                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                                            };
                                            let totalAllowance = api
                                                .column(1)
                                                .data()
                                                .reduce(function (a, b) {
                                                    return intVal(a) + intVal(b);
                                                }, 0);
                                            $(api.column(1).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalAllowance) + "</span>");
                                        }
                                    })
                                    $("#export_allowance").click(function(){
                                        allowance_generate.button(".buttons-excel").trigger();
                                    });
                                }
                            });
                        return false;
                    }
                });
            }else if(form_widget == 'tax'){
                $.validate({
                    form: "#frm-journal-report",
                    lang: "en",
                    scrollToTopOnError: false,
                    onSuccess: function (form) {
                        var formDataC = $("#frm-journal-report").serializeArray();
                        const formData = new FormData(form[0]);
                        formData.append('csrf_token', _csrf_hash);
                            $.ajax({
                                url: baseUrl('payroll/payroll/get_tax'),
                                type: "POST",
                                dataType: "JSON",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function (data) {
                                    vue_portlet_tax.contributions = Object.assign({}, data);
                                    vue_head_data_tax.filters = Object.assign({}, data.filters);
                                    let tax_generate = $("#export_taxes").DataTable({
                                        dom: '<"toolbar">frtlip',
                                        destroy: true,
                                        paging: false,
                                        searching: false,
                                        bInfo : false,
                                        ordering: false,
                                        ajax: {
                                            url: baseUrl('payroll/payroll/get_tax'),
                                            type: 'POST',
                                            dataType: 'json',
                                            data: function (d) {
                                                $.each(formDataC, function(key, val) {
                                                    d[val.name] = val.value;
                                                });
                                            }
                                        },
                                        columns: [
                                            { data: 'company'},
                                            { data: 'amount', className:'text-right', render: function (data, meta, row) {
                                                return '₱ '+numberFormat(data);
                                            }},
                                        ],
                                        buttons: [ 
                                            {
                                                extend: 'excel',
                                                text: 'EXCEL',
                                                title: 'TAX REPORT BY COMPANY'+ vue_head_data_ot.filters.date,
                                                footer: true
                                    
                                            }
                                        ], footerCallback: function (row, data, start, end, display) {
                                            var api = this.api(), data;
                                            // Remove the formatting to get integer data for summation
                                            var intVal = function (i) {
                                                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                                            };
                                            let totalTAX = api
                                                .column(1)
                                                .data()
                                                .reduce(function (a, b) {
                                                    return intVal(a) + intVal(b);
                                                }, 0);
                                            $(api.column(1).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalTAX) + "</span>");
                                        }
                                    })
                                    $("#export_tax").click(function(){
                                        tax_generate.button(".buttons-excel").trigger();
                                    });
                                }
                            });
                        return false;
                    }
                });
                
            }
        },defaultData: function(){
            const formData = new FormData();
            formData.append('csrf_token', _csrf_hash);
            var formDataC = $("#frm-journal-report").serializeArray();
            $.ajax({
                url: baseUrl('payroll/payroll/get_paid_contributions'),
                type: "POST",
                dataType: "JSON",
                processData: false,
                contentType: false,
                data: formData,
                success: function (data) {
                    vue_portlet_contrib.contributions = Object.assign({}, data);
                    vue_head_data_contrib.filters = Object.assign({}, data.filters);
                    const contributions_default = $("#export_contributions").DataTable({
                        dom: '<"toolbar">frtlip',
                        destroy: true,
                        paging: false,
                        searching: false,
                        bInfo : false,
                        ordering: false,
                        ajax: {
                            url: baseUrl('payroll/payroll/get_paid_contributions'),
                            type: 'POST',
                            dataType: 'JSON',
                            data: function (d) {
                                $.each(formDataC, function(key, val) {
                                    d[val.name] = val.value;
                                });
                            }
                        },
                        columns: [
                            { data: 'company'},
                            { data: 'sss', className:'text-right', render: function (data, meta, row) {
                                return '₱ '+numberFormat(data);
                            }},
                            { data: 'phil', className:'text-right', render: function (data, meta, row) {
                                return '₱ '+numberFormat(data);
                            }},
                            { data: 'hdmf', className:'text-right', render: function (data, meta, row) {
                                return '₱ '+numberFormat(data);
                            }},
                        ],
                        buttons: [ 
                            {
                                extend: 'excel',
                                text: 'EXCEL',
                                title: 'CONTRIBUTIONS REPORT BY COMPANY'+ vue_head_data_ot.filters.date,
                                footer: true
                    
                            }
                        ], footerCallback: function (row, data, start, end, display) {
                            var api = this.api(), data;
                            // Remove the formatting to get integer data for summation
                            var intVal = function (i) {
                                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                            };
                    
                            let totalSSS = api
                                .column(1)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                            
                            let totalPHIL = api
                                .column(2)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                            
                            let totalHDMF = api
                                .column(3)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);    
                            $(api.column(1).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalSSS) + "</span>");
                            $(api.column(2).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalPHIL) + "</span>");
                            $(api.column(3).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalHDMF) + "</span>");
                        }
                    });
                    $("#export_contrib").click(function(){
                        contributions_default.button(".buttons-excel").trigger();
                    });
                }
            });
        },defaultDataOT: function(){
            const formData = new FormData();
            var formDataC = $("#frm-journal-report").serializeArray();
            formData.append('csrf_token', _csrf_hash);
            $.ajax({
                url: baseUrl('payroll/payroll/get_overtime'),
                type: "POST",
                dataType: "JSON",
                processData: false,
                contentType: false,
                data: formData,
                success: function (data) {
                    vue_portlet_ot.contributions = Object.assign({}, data);
                    vue_head_data_ot.filters = Object.assign({}, data.filters);
                    const overtime_default = $("#export_overtime").DataTable({
                        dom: '<"toolbar">frtlip',
                        destroy: true,
                        paging: false,
                        searching: false,
                        bInfo : false,
                        ordering: false,
                        ajax: {
                            url: baseUrl('payroll/payroll/get_overtime'),
                            type: 'POST',
                            dataType: 'JSON',
                            data: function (d) {
                                $.each(formDataC, function(key, val) {
                                    d[val.name] = val.value;
                                });
                            }
                        },
                        columns: [
                            { data: 'company'},
                            { data: 'time', render: function (data, meta, row) {
                                return numberFormat(data);
                            }},
                            { data: 'amount', className:'text-right', render: function (data, meta, row) {
                                return '₱ '+numberFormat(data);
                            }},
                        ],
                        buttons: [ 
                            {
                                extend: 'excel',
                                text: 'EXCEL',
                                title: 'OVERTIME REPORT BY COMPANY'+ vue_head_data_ot.filters.date,
                                footer: true
                    
                            }
                        ], footerCallback: function (row, data, start, end, display) {
                            var api = this.api(), data;
                            var intVal = function (i) {
                                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                            };
                            let totalHRS = api
                                .column(1)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                            let totalWAGE = api
                                .column(2)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                            $(api.column(1).footer()).html("<span class='m--font-boldest'>" + numberFormat(totalHRS) + "</span>");
                            $(api.column(2).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalWAGE) + "</span>");
                        }
                    });
                    $("#export_ot").click(function(){
                        overtime_default.button(".buttons-excel").trigger();
                    });
                }
            });
        },defaultDataAllowances: function(){
            const formData = new FormData();
            var formDataC = $("#frm-journal-report").serializeArray();
            formData.append('csrf_token', _csrf_hash);
            $.ajax({
                url: baseUrl('payroll/payroll/get_allowances'),
                type: "POST",
                dataType: "JSON",
                processData: false,
                contentType: false,
                data: formData,
                success: function (data) {
                    vue_portlet_allowance.contributions = Object.assign({}, data);
                    vue_head_data_allowance.filters = Object.assign({}, data.filters);
                    let allowance_default = $("#export_allowances").DataTable({
                        dom: '<"toolbar">frtlip',
                        processing: true,
                        destroy: true,
                        paging: false,
                        searching: false,
                        bInfo : false,
                        ordering: false,
                        ajax: {
                            url: baseUrl('payroll/payroll/get_allowances'),
                            type: 'POST',
                            dataType: 'JSON',
                            data: function (d) {
                                $.each(formDataC, function(key, val) {
                                    d[val.name] = val.value;
                                });
                            }
                        },
                        columns: [
                            { data: 'company'},
                            { data: 'amount', className:'text-right', render: function (data, meta, row) {
                                return '₱ '+numberFormat(data);
                            }},
                        ],
                        buttons: [ 
                            {
                                extend: 'excel',
                                text: 'EXCEL',
                                title: 'ALLOWANCE REPORT BY COMPANY',
                                footer: true
                    
                            }
                        ], footerCallback: function (row, data, start, end, display) {
                            var api = this.api(), data;
                            var intVal = function (i) {
                                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                            };
                            let totalAllowance = api
                                .column(1)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                            $(api.column(1).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalAllowance) + "</span>");
                        }
                    });
                    $("#export_allowance").click(function(){
                        allowance_default.button(".buttons-excel").trigger();
                    });
                }
            });
        },defaultDataTax: function(){
            const formData = new FormData();
            var formDataC = $("#frm-journal-report").serializeArray();
            formData.append('csrf_token', _csrf_hash);
            $.ajax({
                url: baseUrl('payroll/payroll/get_tax'),
                type: "POST",
                dataType: "JSON",
                processData: false,
                contentType: false,
                data: formData,
                success: function (data) {
                    vue_portlet_tax.contributions = Object.assign({}, data);
                    vue_head_data_tax.filters = Object.assign({}, data.filters);
                    let tax_default = $("#export_taxes").DataTable({
                        dom: '<"toolbar">frtlip',
                        processing: true,
                        destroy: true,
                        paging: false,
                        searching: false,
                        bInfo : false,
                        ordering: false,
                        ajax: {
                            url: baseUrl('payroll/payroll/get_tax'),
                            type: 'POST',
                            dataType: 'JSON',
                            data: function (d) {
                                $.each(formDataC, function(key, val) {
                                    d[val.name] = val.value;
                                });
                            }
                        },
                        columns: [
                            { data: 'company'},
                            { data: 'amount', className:'text-right', render: function (data, meta, row) {
                                return '₱ '+numberFormat(data);
                            }},
                        ],
                        buttons: [ 
                            {
                                extend: 'excel',
                                text: 'EXCEL',
                                title: 'TAX REPORT BY COMPANY',
                                footer: true
                    
                            }
                        ], footerCallback: function (row, data, start, end, display) {
                            var api = this.api(), data;
                            var intVal = function (i) {
                                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                            };
                            let totalAllowance = api
                                .column(1)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                            $(api.column(1).footer()).html("<span class='m--font-boldest'>" + '₱ '+numberFormat(totalAllowance) + "</span>");
                        }
                    });
                    $("#export_tax").click(function(){
                        tax_default.button(".buttons-excel").trigger();
                    });
                }
            });
        }
    },mounted: function () {
        this.defaultData();
        this.defaultDataOT();
        this.filterBy();
        this.defaultDataAllowances();
        this.defaultDataTax();
    }
});



        