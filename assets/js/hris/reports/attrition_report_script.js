let _companies = [];
let _departments = [];
let _year = [];
let table;
let colorSet = new am4core.ColorSet();
let attritionChart;
let filtered = {};
let count = 0;
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


if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.company !== "undefined" && !$.isEmptyObject(_tempContentData.company)){
        _companies = _tempContentData.company;
    }

    if(typeof _tempContentData.departments !== "undefined" && !$.isEmptyObject(_tempContentData.departments)){
        _departments = _tempContentData.departments;
    }
    
    if(typeof _tempContentData.year !== "undefined" && !$.isEmptyObject(_tempContentData.year)){
        _year = _tempContentData.year;
    }
}

var generateAttrition = new Vue({
    el: '#attrition-report',
    data: {
        company_picker: true,
        department_picker: false,
        month_picker: true,
        year_picker: true,
        colmn: [],
        row: {},
        hasDepartment: false,
        isLabelVisible: true,
        chartData: [],
        year: [],
        company: [],
        department: [],
        chart_company: null,
        chart_year: '2024',
        chartRow: {}
    },
    mounted: function() {
        var instance = this;
        instance.renderSelect2();
        instance.renderTable();
        instance.generateInitialChart();

        instance.year = _year;
        instance.company = _companies;
        instance.department = _departments;
    },
    methods: {
        tempShowByType(id) {
            var instance = this;
            if(id === 1){
                instance.company_picker = true;
                instance.department_picker = false;
            }else{
                instance.company_picker = true;
                instance.department_picker = true;
            }

            instance.renderSelect2();
            return instance;
        }, tempShowByDates(id){
            var instance = this;
            if(id === 1){
                instance.month_picker = true;
                instance.year_picker = true;
            }else{
                instance.month_picker = false;
            }

            instance.renderSelect2();
            return instance;
        }, resetFields(){
            const instance = this;
            const element = instance.$el;

            var currentForm = $(element).find("#frm-attrition-report");
            if (typeof currentForm !== "undefined") {
                currentForm.find("select").val("").trigger("change");
                currentForm.find("input[name='filter_year']").val("");
                currentForm[0].reset();

                instance.department_picker = false;
                instance.month_picker = true;

                instance.renderSelect2();
            }
        }, renderSelect2(){
            const instance = this;
            var currentElement = '#generate-report-modal';

            setTimeout( function(){
                $(currentElement).find('#company').select2({
                    width: '100%',
                    placeholder: "SELECT AN OPTION",
                    data: _companies,
                    allowClear: instance.department_picker ? false : true,
                    dropdownParent: $('#generate-report-modal'),
                    language: { errorLoading: function () { return "Searching..." } },
                }).on("select2:select, change", function(e){
                    const currentTarget = e.target;
                    if(typeof currentTarget !== "undefined"){ $(currentTarget).validate(); }
                });
                
                $(currentElement).find('#department').select2({
                    width: '100%',
                    placeholder: "SELECT AN OPTION",
                    data: _departments,
                    allowClear: true,
                    dropdownParent: $('#generate-report-modal'),
                    language: { errorLoading: function () { return "Searching..." } },
                }).on("select2:select, change", function(e){
                    const currentTarget = e.target;
                    if(typeof currentTarget !== "undefined"){ $(currentTarget).validate(); }
                });
                
                $(currentElement).find("select[name='filter_month']").select2({
                    width: '100%',
                    data: months,
                    placeholder: "SELECT MONTH",
                    allowClear: true,
                    dropdownParent: $('#generate-report-modal'),
                    language: { errorLoading: function () { return "Searching..." } },
                }).on("select2:select, change", function(e){
                    const currentTarget = e.target;
                    if(typeof currentTarget !== "undefined"){ $(currentTarget).validate(); }
                });

                $(currentElement).find("select[name='filter_year']").select2({
                    width: '100%',
                    placeholder: "SELECT YEAR",
                    allowClear: true,
                    data: _year,
                    dropdownParent: $('#generate-report-modal'),
                    language: { errorLoading: function () { return "Searching..." } },
                }).on("select2:select, change", function(e){
                    const currentTarget = e.target;
                    if(typeof currentTarget !== "undefined"){ $(currentTarget).validate(); }
                });

                $('#chart-company').select2({
                    width: '100%',
                    placeholder: "SELECT A COMPANY",
                    data: _companies,
                    allowClear: instance.department_picker ? false : true,
                    language: { errorLoading: function () { return "Searching..." } },
                }).on("select2:select, change", function(e){
                    const currentTarget = e.target;

                    if(typeof currentTarget !== "undefined"){ 
                        instance.loadByCompany($(currentTarget).val()); 
                    }
                });
            });
        }, getScriptRendering(formUrl, formData, currentForm){
            const instance = this;
            const element = this.$el;
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
                success: function (response) {
                    const result = response;

                    instance.colmn.push(result.data);
                    // instance.chartData = Object.assign({}, result.chartData);
                    // instance.chartData = result.chartData;

                    instance.row = Object.assign({}, {
                        'company' : result.company,
                        'coverage' : result.coverage
                    });

                    if(instance.department_picker){
                        instance.hasDepartment = true;
                    }else{
                        instance.hasDepartment = false;
                    }

                    instance.isLabelVisible = response.generated.length > 1 ? false : true;
                    setTimeout( function(){
                        table = instance.renderTable();
                        table.rows().invalidate();
                        table.clear().rows.add(result.data).draw();
                    }, 200);

                    $(element).find('#generate-report-modal').modal('hide');
                }
            });
        }, attrition(cur, prev, type){
            const instance = this;
            var badge = "";

            var total = ((cur - prev ) / prev) * 100;
            const num = $.isNumeric(total) ? total.toFixed(2) : 0;

            if(type == 'Hired'){
                badge = num > 0 ? 'success' : (num == 0) ? '' : 'danger';
            }else{
                badge = num < 0 ? 'success' : (num == 0) ? '' : 'danger';
            }

            return `<span class="m--regular-font-size-sm2 m--font-${badge}">${num}%</span>`;
        }, renderTable(){
            const instance = this;
            const element = this.$el;

            if(typeof table != 'undefined' && !instance.isEmpty(table)){
                $(element).find('#tbl-attrition-report').empty();
                table.clear().destroy();
            }

            table = $(element).find('#tbl-attrition-report').DataTable({
                dom: 'rt',
                serverSide: false,
                processing: false,
                paging: false,
                searching: false,
                bInfo : false,
                ordering: false,
                retrieve: true,
                data: [],
                columns: [
                    { data: 'label', defaultContent: "", visible: instance.isLabelVisible },
                    { data: 'type', defaultContent: "", },
                    { data: null, defaultContent: "", className: 'text-center',
                        render: function(data, type, row, meta){
                            return typeof row.january !== 'undefined' ? row.january : 0;
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center', //jan - feb attrition
                        render: function(data, type, row, meta){
                            var jan = typeof row.january !== 'undefined' ? row.january : 0;
                            var feb = typeof row.february !== 'undefined' ? row.february : 0;

                            return instance.attrition(feb, jan, row.type);
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center',
                        render: function(data, type, row, meta){
                            return typeof row.february !== 'undefined' ? row.february : 0;
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center', //feb - march attrition
                        render: function(data, type, row, meta){
                            var feb = typeof row.february !== 'undefined' ? row.february : 0;
                            var mar = typeof row.march !== 'undefined' ? row.march : 0;

                            return instance.attrition(mar, feb, row.type);
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center',
                        render: function(data, type, row, meta){
                            return typeof row.march !== 'undefined' ? row.march : 0 ;
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center', // march - april attrition
                        render: function(data, type, row, meta){
                            var mar = typeof row.march !== 'undefined' ? row.march : 0;
                            var apr = typeof row.april !== 'undefined' ? row.april : 0;

                            return instance.attrition(apr, mar, row.type);
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center',
                        render: function(data, type, row, meta){
                            return typeof row.april !== 'undefined' ? row.april : 0 ;
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center', // april - may attrition
                        render: function(data, type, row, meta){
                            var apr = typeof row.april !== 'undefined' ? row.april : 0;
                            var may = typeof row.may !== 'undefined' ? row.may : 0;

                            return instance.attrition(may, apr, row.type);
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center',
                        render: function(data, type, row, meta){
                            return typeof row.may !== 'undefined' ? row.may : 0 ;
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center', // may - june attrition
                        render: function(data, type, row, meta){
                            var may = typeof row.may !== 'undefined' ? row.may : 0;
                            var jun = typeof row.june !== 'undefined' ? row.june : 0;

                            return instance.attrition(jun, may, row.type);
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center',
                        render: function(data, type, row, meta){
                            return typeof row.june !== 'undefined' ? row.june : 0 ;
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center', // june - july attrition
                        render: function(data, type, row, meta){
                            var jun = typeof row.june !== 'undefined' ? row.june : 0;
                            var jul = typeof row.july !== 'undefined' ? row.july : 0;

                            return instance.attrition(jul, jun, row.type);
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center',
                        render: function(data, type, row, meta){
                            return typeof row.july !== 'undefined' ? row.july : 0 ;
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center', // july - august attrition
                        render: function(data, type, row, meta){
                            var jul = typeof row.july !== 'undefined' ? row.july : 0;
                            var aug = typeof row.august !== 'undefined' ? row.august : 0;

                            return instance.attrition(aug, jul, row.type);
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center',
                        render: function(data, type, row, meta){
                            return typeof row.august !== 'undefined' ? row.august : 0 ;
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center', // august - september attrition
                        render: function(data, type, row, meta){
                            var aug = typeof row.august !== 'undefined' ? row.august : 0;
                            var sep = typeof row.september !== 'undefined' ? row.september : 0;

                            return instance.attrition(sep, aug, row.type);
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center',
                        render: function(data, type, row, meta){
                            return typeof row.september !== 'undefined' ? row.september : 0 ;
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center', // september - october attrition
                        render: function(data, type, row, meta){
                            var sep = typeof row.september !== 'undefined' ? row.september : 0;
                            var oct = typeof row.october !== 'undefined' ? row.october : 0;

                            return instance.attrition(oct, sep, row.type);
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center',
                        render: function(data, type, row, meta){
                            return typeof row.october !== 'undefined' ? row.october : 0 ;
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center', // october - november attrition
                        render: function(data, type, row, meta){
                            var oct = typeof row.october !== 'undefined' ? row.october : 0;
                            var nov = typeof row.november !== 'undefined' ? row.november : 0;

                            return instance.attrition(nov, oct, row.type);
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center',
                        render: function(data, type, row, meta){
                            return typeof row.november !== 'undefined' ? row.november : 0 ;
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center', // november - december attrition
                        render: function(data, type, row, meta){
                            var nov = typeof row.november !== 'undefined' ? row.november : 0;
                            var dec = typeof row.december !== 'undefined' ? row.december : 0;

                            return instance.attrition(dec, nov, row.type);
                        }
                    },
                    { data: null, defaultContent: "", className: 'text-center',
                        render: function(data, type, row, meta){
                            return typeof row.december !== 'undefined' ? row.december : 0 ;
                        }
                    },
                    { data: null, defaultContent: 0, className: 'text-center', 
                        render: function(data, type, row, meta){
                            var jan = typeof row.january !== 'undefined' ? row.january : 0;
                            var feb = typeof row.february !== 'undefined' ? row.february : 0;
                            var mar = typeof row.march !== 'undefined' ? row.march : 0;
                            var apr = typeof row.april !== 'undefined' ? row.april : 0;
                            var may = typeof row.may !== 'undefined' ? row.may : 0;
                            var jun = typeof row.june !== 'undefined' ? row.june : 0;
                            var jul = typeof row.july !== 'undefined' ? row.july : 0;
                            var aug = typeof row.august !== 'undefined' ? row.august : 0;
                            var sep = typeof row.september !== 'undefined' ? row.september : 0;
                            var oct = typeof row.october !== 'undefined' ? row.october : 0;
                            var nov = typeof row.november !== 'undefined' ? row.november : 0;
                            var dec = typeof row.december !== 'undefined' ? row.december : 0;

                            var total = parseInt(jan) + parseInt(feb) + parseInt(mar) + parseInt(apr) + parseInt(may) + parseInt(jun) + parseInt(jul) + parseInt(aug) + parseInt(sep) + parseInt(oct) + parseInt(nov) + parseInt(dec);
                            count = total;
                            return total;
                        }
                    }
                ], 
                rowGroup: {
                    dataSrc: ['label'],
                    startRender: function ( rows, group ) {
                        if(!instance.isLabelVisible){
                            var comp = rows.data().pluck('label');
                            return group;
                        }
                    }
                },
                drawCallback: function(settings){
                    if(!instance.isEmpty(instance.colmn)){
                        if(instance.isLabelVisible){
                            $(element).find("#tbl-attrition-report tfoot").removeAttr('hidden');
                        }else{
                            $(element).find("#tbl-attrition-report tfoot").attr('hidden', true);
                        }
                    }
                },
                footerCallback: function(row, data, start, end, display){
                    var api = this.api(), data;
                    // Remove the formatting to get integer data for summation
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                    };

                    if(instance.isLabelVisible){
                        totalJan = api.column(3).data().reduce(function (a, b) {
                            var _jan = typeof b.january != 'undefined' ? b.january : 0;
                            return intVal(a) + intVal(_jan);
                        }, 0);

                        totalFeb = api.column(5).data().reduce(function (a, b) {
                            var _feb = typeof b.february != 'undefined' ? b.february : 0;
                            return intVal(a) + intVal(_feb);
                        }, 0);

                        totalMar = api.column(7).data().reduce(function (a, b) {
                            var _mar = typeof b.march != 'undefined' ? b.march : 0;
                            return intVal(a) + intVal(_mar);
                        }, 0);
                        
                        totalApr = api.column(9).data().reduce(function (a, b) {
                            var _apr = typeof b.april != 'undefined' ? b.april : 0;
                            return intVal(a) + intVal(_apr);
                        }, 0);
                        
                        totalMay = api.column(11).data().reduce(function (a, b) {
                            var _may = typeof b.may != 'undefined' ? b.may : 0;
                            return intVal(a) + intVal(_may);
                        }, 0);

                        totalJun = api.column(13).data().reduce(function (a, b) {
                            var _jun = typeof b.june != 'undefined' ? b.june : 0;
                            return intVal(a) + intVal(_jun);
                        }, 0);

                        totalJul = api.column(15).data().reduce(function (a, b) {
                            var _jul = typeof b.july != 'undefined' ? b.july : 0;
                            return intVal(a) + intVal(_jul);
                        }, 0);

                        totalAug = api.column(17).data().reduce(function (a, b) {
                            var _aug = typeof b.august != 'undefined' ? b.august : 0;
                            return intVal(a) + intVal(_aug);
                        }, 0);

                        totalSep = api.column(19).data().reduce(function (a, b) {
                            var _sep = typeof b.september != 'undefined' ? b.september : 0;
                            return intVal(a) + intVal(_sep);
                        }, 0);

                        totalOct = api.column(21).data().reduce(function (a, b) {
                            var _oct = typeof b.october != 'undefined' ? b.october : 0;
                            return intVal(a) + intVal(_oct);
                        }, 0);

                        totalNov = api.column(23).data().reduce(function (a, b) {
                            var _nov = typeof b.november != 'undefined' ? b.november : 0;
                            return intVal(a) + intVal(_nov);
                        }, 0);

                        totalDec = api.column(25).data().reduce(function (a, b) {
                            var _dec = typeof b.december != 'undefined' ? b.december : 0;
                            return intVal(a) + intVal(_dec);
                        }, 0);

                        GrandTotal = totalJan + totalFeb + totalMar + totalApr + totalMay + totalJun + totalJul + totalAug + totalSep + totalOct + totalNov + totalDec;

                        $(api.column(2).footer()).html("<span class='m--font-boldest'>" + totalJan + "</span>");
                        $(api.column(4).footer()).html("<span class='m--font-boldest'>" + totalFeb + "</span>");
                        $(api.column(6).footer()).html("<span class='m--font-boldest'>" + totalMar + "</span>");
                        $(api.column(8).footer()).html("<span class='m--font-boldest'>" + totalApr + "</span>");
                        $(api.column(10).footer()).html("<span class='m--font-boldest'>" + totalMay + "</span>");
                        $(api.column(12).footer()).html("<span class='m--font-boldest'>" + totalJun + "</span>");
                        $(api.column(14).footer()).html("<span class='m--font-boldest'>" + totalJul + "</span>");
                        $(api.column(16).footer()).html("<span class='m--font-boldest'>" + totalAug + "</span>");
                        $(api.column(18).footer()).html("<span class='m--font-boldest'>" + totalSep + "</span>");
                        $(api.column(20).footer()).html("<span class='m--font-boldest'>" + totalOct + "</span>");
                        $(api.column(22).footer()).html("<span class='m--font-boldest'>" + totalNov + "</span>");
                        $(api.column(24).footer()).html("<span class='m--font-boldest'>" + totalDec + "</span>");
                        $(api.column(25).footer()).html("<span class='m--font-boldest'>" + GrandTotal + "</span>");
                    }
                }
            });

            return table;
        }, isEmpty(arr){
            return $.isEmptyObject(arr);
        }, generateAttritionChart(data = []){
            am4core.ready(function () {
                attritionChart = am4core.create("attrition-chart-container", am4charts.XYChart);
                attritionChart.data = data;

                var dateAxis = attritionChart.xAxes.push(new am4charts.DateAxis());
                dateAxis.renderer.minGridDistance = 50;

                var valueAxis = attritionChart.yAxes.push(new am4charts.ValueAxis());

                var seriesNewlyHired = attritionChart.series.push(new am4charts.LineSeries());
                seriesNewlyHired.dataFields.valueY = "hired";
                seriesNewlyHired.dataFields.dateX = "date";
                seriesNewlyHired.strokeWidth = 2;
                seriesNewlyHired.minBulletDistance = 10;
                seriesNewlyHired.stroke = am4core.color("#43A047");
                seriesNewlyHired.propertyFields.dummyData = "breakdown";
                seriesNewlyHired.tooltipText = "[bold]{name}:[/] [bold #43A047]{valueY}[/]";
                seriesNewlyHired.tooltip.pointerOrientation = "vertical";
                seriesNewlyHired.name = "Hired";
                seriesNewlyHired.tooltip.getFillFromObject = false;
                seriesNewlyHired.tooltip.getStrokeFromObject = true;
                seriesNewlyHired.tooltip.background.fill = am4core.color("#fff");
                seriesNewlyHired.tooltip.background.strokeWidth = 2;
                seriesNewlyHired.tooltip.label.fill = seriesNewlyHired.stroke;

                var seriesResigned = attritionChart.series.push(new am4charts.LineSeries());
                seriesResigned.dataFields.valueY = "seperated";
                seriesResigned.dataFields.dateX = "date";
                seriesResigned.strokeWidth = 2;
                seriesResigned.strokeDasharray = "3,4";
                seriesResigned.minBulletDistance = 10;
                seriesResigned.stroke = am4core.color("#D84315");
                seriesNewlyHired.propertyFields.dummyData = "breakdown";
                seriesResigned.tooltipText = "[bold]{name}:[/] [bold #D84315]{valueY}[/]";
                seriesResigned.tooltip.pointerOrientation = "vertical";
                seriesResigned.name = "Seperated";
                seriesResigned.tooltip.getFillFromObject = false;
                seriesResigned.tooltip.getStrokeFromObject = true;
                seriesResigned.tooltip.background.fill = am4core.color("#fff");
                seriesResigned.tooltip.background.strokeWidth = 2;
                seriesResigned.tooltip.label.fill = seriesResigned.stroke;

                attritionChart.cursor = new am4charts.XYCursor();
                attritionChart.cursor.xAxis = dateAxis;
                attritionChart.legend = new am4charts.Legend();
            });
        }, generateInitialChart(){
            const instance = this;
            $.ajax({
                url: baseUrl('hris/reports/generate_attrition_chart')+'?t=' + new Date().getTime(),
                type: "POST",
                dataType: "json",
		        data: {  
                    csrf_token : _csrf_hash,
                    company : instance.chart_company,
                    filter_year : instance.chart_year
                },
                success: function(response){
                    const result = response.data;

                    instance.chartData = result;
                    instance.chartRow = Object.assign({
                        'company': response.company,
                        'coverage' : response.coverage
                    })

                    instance.generateAttritionChart(result);
                }
            });
        }, loadAttritionByYear(year){
            const instance = this;

            instance.chart_year = year;
            filtered.year = year;
            instance.generateInitialChart();
        }, loadByCompany(id){
            const instance = this;

            instance.chart_company = id;
            filtered .company = id;
            instance.generateInitialChart();
        }, printReport(el, type){
            export_log(filtered,"Attrition Report", "print",count,type);
            const instance = this;

            const divToPrint = document.getElementById(el);
            var newWin=window.open('','Print-Window');

            let html = ``;

            html += '<html>';
                html += '<head>';
                    html += '<style>';
                        html += ` @media print{
                                @page { 
                                    size: landscape;
                                    -webkit-transform: rotate(-90deg); 
                                    -moz-transform:rotate(-90deg);
                                    filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3)
                                }
                                
                                @page land { 
                                    size: landscape;
                                    -webkit-transform: rotate(-90deg); 
                                    -moz-transform:rotate(-90deg);
                                    filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3)
                                }

                                @page port { 
                                    size: landscape;
                                    -webkit-transform: rotate(-90deg); 
                                    -moz-transform:rotate(-90deg);
                                    filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3)
                                }
                            }`;
                    html += '</style>';
                html += '</head>';
                html += '<style>';
                    if(type == 'chart'){
                        html += `body { transform: scale(0.63) }
                            .col-md-12 { position: relative }
                            .col-8 { text-align: center; position: relative }
                            h2 { position: absolute; bottom: -15px; left: 200px; font-size: 30px; }
                            h5 { font-size: 30px; position: absolute; left: -295px; }`;
                    }
                    html += `
                            table { font-size: 12px; } 
                            .print-size-auto { width: auto }
                            .print-size-8 { width: 8% }
                            .print-size-10 { width: 10% }
                            .print-size-25 { width: 25% }
                            .row { display: flex; flex-wrap: wrap }
                            .col-2 { flex: 0 0 25%; max-width: 25% }
                            .col-8 { flex: 0 0 74%; max-width: 74% }
                            table thead th:last-child { width: 15% }
                            tfoot th:first-child { text-align: right !important }
                            .col-md-12 { flex: 0 0 100%; max-width: 100%; }
                            .m--font-bolder { font-weight: 800 }
                            .col-4 { flex: 0 0 33%; max-width: 33% }
                            table.dataTable { clear: both; margin-top: 6px !important; margin-bottom: 6px !important; max-width: none !important; border-collapse: separate !important; border-spacing: 0; }
                            .table-bordered { border: 1px solid #f4f5f8; }
                            .table-striped tbody tr:nth-of-type(odd) { background-color: #f4f5f8; }
                            #tbl-attrition-report .dtrg-group.dtrg-start{ background: #c1c1c1 !important; color: #fff; font-size: 14px; }
                            #tbl-attrition-report .dtrg-group.dtrg-start td{ font-weight: 700 !important; }
                            .table-striped tbody tr:nth-of-type(odd) { background-color: #f4f5f8; }
                            .m-datatable.m-datatable--default.m-datatable--loaded { display: block; }
                            p { font-size: 14px; margin: 0 !important }
                            .table-bordered th, .table-bordered td { border: 1px solid #f4f5f8; padding: 5px }
                            .m--regular-font-size-sm2 { text-align: center }
                            .text-center { text-align: center }
                            .attrition-chart-container { position: absolute; width: 100%; margin-right: 0; margin-left: 0; display: flex; right: 325px; top: 100px; }
                            table tbody .dtrg-group td{ padding: 7px !important; }`;
                html += '</style>';
                html += '<body onload="window.print()">';
                    html += '<div class="row" style="justify-content: center">';
                        html += '<div class="col-8">';
                            html += '<h2 style="text-align: center">ATTRITION REPORT</h2>';
                        html += '</div>';
                    html += '</div>';
                    html += divToPrint.innerHTML;
                html += '</body>';
            html += '</html>';

            newWin.document.write(html);
            newWin.print();
            newWin.close();

        }
    }
});

$.validate({
    form: "#frm-attrition-report",
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        var currentForm = form[0];
        var formUrl = currentForm.action;
        var formData = $(currentForm).serialize();
        filtered = formData;
        generateAttrition.getScriptRendering(formUrl, formData, currentForm);

        return false;
    }
});

// for validation of checkbox
$("[name='to_generate_group[]']:eq(0)").valAttr('','validate_checkbox_group').valAttr('qty','1-2').valAttr('error-msg','ch0ose atleast 1 to generate');

async function export_log(datas, name, type, count,chart) {
    let filters = {};
    const exportName = name+' '+chart;
    if (datas && chart != 'chart') {
        filters = {};
        datas.split('&').forEach(pair => {
            const [key, value] = pair.split('=');
            filters[key] = decodeURIComponent(value);
        });
    }else{
        filters = datas;
    }

    filters.filter_type = $('input[name="to_generate_group[]"]:checked').val();
    try {
        const response = await $.ajax({
            url: siteUrl("hris/reports/log_export") + '?t=' + new Date().getTime(),
            type: "POST",
            data: { 
                filters,
                type: exportName,
                name: type,
                count: count,
                csrf_token: _csrf_hash 
            },
            // dataType: 'json'
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            },
        });
        return response;
    } catch (error) {
        console.error('Error exporting log:', error);
        throw error;
    }
}