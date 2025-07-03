//const { Socket } = require("engine.io");

let select2_company_id = 0;
let selectedPieItem;
let container_width;
let colorSet = new am4core.ColorSet();
let retentionChart;

am4core.useTheme(am4themes_animated);

am4core.ready(function () {
    // Themes begin
    // Themes end  

           
        var arrFunctions = [];
        // var getEmployeeStatusChart = getEmployeeStatusChart();
        // var getGenderChart = getGenderChart();
        // var getPersonnelRequestSummaryChart = getPersonnelRequestSummaryChart();
        
        getEmployeePerCompanyStatusChartV2();
        /*** getEmployeePerCompanyStatusChart(); ***/
        $(window).scroll(function(){
            
            var position = $(window).height();

            var employee_status = $("#employee_status").offset();
            var gender_chart = $("#gender_chart").offset();
            var personnel_chart = $("#personnel_chart").offset();
 
            if(arrFunctions.includes("emp_status") && position != employee_status){
                return false;
            }else{
                arrFunctions.push(getEmployeeStatusChart());
            }
            if(arrFunctions.includes("personnel") && position != personnel_chart){
                return false;
            }else{
                arrFunctions.push(getPersonnelRequestSummaryChart());
            }
            if(arrFunctions.includes("gender") && position != gender_chart){
                return false;
            }else{
                arrFunctions.push(getGenderChart());
            }

            // getEmployeeStatusChart(false);
            // var isCalled = false;
            // var position = $(window).height();
            
            // var employee_status = $("#employee_status").offset();
            // var gender_chart = $("#gender_chart").offset();
            // var personnel_chart = $("#personnel_chart").offset();
            // var employee_status_by_company = $("#employee_status_by_company").offset();
            // if(employee_status_by_company != position && getEmployeePerCompanyStatusChart()){
            //     getEmployeePerCompanyStatusChart(false);
            // }else{
            //     getEmployeePerCompanyStatusChart(true);
                
            // }
        });
    
    /* START GENDER CHART */
function getGenderChart(){
    var genderChartContainer = am4core.create("gender-chart-container", am4core.Container);
    genderChartContainer.width = am4core.percent(100)
    genderChartContainer.height = am4core.percent(100);
  
    // Create chart instance
    let chartGender = new am4charts.XYChart3D();
    chartEmployeeStatus.responsive.enabled = true;
    chartGender.parent = genderChartContainer;
    
    // Add data
    getGenderDemographics
        .then((data) => {
            chartGender.data = data.data;
            $("#gender-graph-total span").html(data.total);
    
            var valueAxisGender = chartGender.yAxes.push(new am4charts.ValueAxis());
            valueAxisGender.dataFields.category = "cnt";
            valueAxisGender.renderer.minGridDistance = 100;
            

            var categoryAxis = chartGender.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "gender";
            categoryAxis.renderer.minGridDistance = 1;
            categoryAxis.renderer.cellStartLocation = 0.2
            categoryAxis.renderer.cellEndLocation = 0.8

            // if(screen.width <= 450){
            //     categoryAxis.renderer.labels.template.horizontalCenter = "right";
            //     categoryAxis.renderer.labels.template.verticalCenter = "middle";
            //     categoryAxis.renderer.labels.template.rotation = 300;
            // }
            
            // Add and configure Series
            var series = chartGender.series.push(new am4charts.ColumnSeries3D());
            series.dataFields.categoryX = "gender";
            series.dataFields.valueY = "cnt";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.tooltipText = "{valueY}";
            series.columns.template.column3D.stroke = am4core.color("#fff");
            series.columns.template.column3D.strokeOpacity = 0.2;
            series.columns.template.adapter.add("fill", function(fill, target){
                return chartGender.colors.getIndex(target.dataItem.index);
              });   

            series.columns.template.events.on("hit", function (e) {
                const data = e.target.dataItem.dataContext;
                const key = data.key;
                window.open(baseUrl('hris/masterfile/employee?sex=' + key), "_blank");
            }, this);
            
        })
        .catch((err) => {
            console.log('some error handler here ' + err);
        });

        return "gender";
    }        
    /* END OF GENDER CHART */



    /* START EMPLOYEE STATUS CHART */
function getEmployeePerCompanyStatusChartV2(){
    var activeEmployeesPerCompany = am4core.create("active-employees-per-company-container2", am4core.Container);
    activeEmployeesPerCompany.width = am4core.percent(100);
    activeEmployeesPerCompany.height = am4core.percent(100);

    let chartActiveEmployeesPerCompany = new am4charts.XYChart3D();
    chartActiveEmployeesPerCompany.responsive.enabled = true;
    chartActiveEmployeesPerCompany.parent = activeEmployeesPerCompany;

    // Add data
    getActiveEmployeesOnEachCompany
        .then((done) => {
            chartActiveEmployeesPerCompany.data = done.data;

            // Set inner radius
            var categoryAxis_empStatus = chartActiveEmployeesPerCompany.yAxes.push(new am4charts.CategoryAxis());
            categoryAxis_empStatus.dataFields.category = "company";
            categoryAxis_empStatus.renderer.minGridDistance = 10;
            
            var valueAxis_empStatus = chartActiveEmployeesPerCompany.xAxes.push(new am4charts.ValueAxis());
            valueAxis_empStatus.dataFields.category = "cnt";
            valueAxis_empStatus.renderer.minGridDistance = 100;

            var series = chartActiveEmployeesPerCompany.series.push(new am4charts.ColumnSeries3D());
            
            series.dataFields.categoryY = "company";
            series.dataFields.valueX = "cnt";

            console.log(series.dataFields);

            series.columns.template.propertyFields.fill = "color";
            series.columns.template.tooltipText = "{valueX.value}";
            series.columns.template.column3D.stroke = am4core.color("#fff");
            series.columns.template.column3D.strokeOpacity = 0.2;  
            series.columns.template.adapter.add("fill", function(fill, target){
                return chartActiveEmployeesPerCompany.colors.getIndex(target.dataItem.index);
              });   
              series.columns.template.events.on("hit", function (e) {
                const data = e.target.dataItem.dataContext;
                const key = data.key_str;

                window.open(baseUrl('hris/masterfile/employee?company=' + key), "_blank");
            }, this);

        }).catch((err) => {
            console.log('some error handler here ' + err);
        });
        return "company";
    }

function getEmployeeStatusChart(company_id){
    var employeeStatusChartContainer = am4core.create("employee-status-chart-container", am4core.Container);
    employeeStatusChartContainer.width = am4core.percent(100);
    employeeStatusChartContainer.height = am4core.percent(100);

    /*var employeeStatusChartLegendContainer = am4core.create("employee-status-chart-legend", am4core.Container);
    employeeStatusChartLegendContainer.width = am4core.percent(100);
    employeeStatusChartLegendContainer.height = am4core.percent(100);*/

    let chartEmployeeStatus = new am4charts.XYChart3D();
    chartEmployeeStatus.responsive.enabled = true;
    chartEmployeeStatus.parent = employeeStatusChartContainer;

    // Add data
    getEachEmployeeStatusDemographics(company_id).then((data) => {
        $("#employee-status-graph-total span").html(data.total);
        chartEmployeeStatus.data = data.data;

        // Set inner radius
        var categoryAxis_empStatus = chartEmployeeStatus.yAxes.push(new am4charts.CategoryAxis());
        categoryAxis_empStatus.dataFields.category = "employee_status";
        categoryAxis_empStatus.renderer.minGridDistance = 20;

        var valueAxis_empStatus = chartEmployeeStatus.xAxes.push(new am4charts.ValueAxis());
        valueAxis_empStatus.dataFields.category = "cnt";
        valueAxis_empStatus.renderer.minGridDistance = 100;

        valueAxis_empStatus.strictMinMax = true;
        valueAxis_empStatus.min = 0;
        valueAxis_empStatus.numberFormatter = new am4core.NumberFormatter();
        valueAxis_empStatus.numberFormatter.numberFormat = "#";

        // Ensure only whole numbers are displayed on the x-axis
        valueAxis_empStatus.renderer.labels.template.adapter.add("text", function(text) {
            return Number.isInteger(parseFloat(text)) ? text : "";
        });

        // Keep grid lines only at whole numbers
        valueAxis_empStatus.renderer.grid.template.location = 0;
        valueAxis_empStatus.renderer.ticks.template.disabled = true;

        // Add and chartEmployeeStatus Series
        var series = chartEmployeeStatus.series.push(new am4charts.ColumnSeries3D());
        series.dataFields.categoryY = "employee_status";
        series.dataFields.valueX = "cnt";
        series.columns.template.propertyFields.fill = "color";
        series.columns.template.tooltipText = "{valueX.value}";
        series.columns.template.column3D.stroke = am4core.color("#fff");
        series.columns.template.column3D.strokeOpacity = 0.2;  
        series.columns.template.adapter.add("fill", function(fill, target){
            return chartEmployeeStatus.colors.getIndex(target.dataItem.index);
        });   

        series.columns.template.events.on("hit", function (e) {
            const data = e.target.dataItem.dataContext;
            const key = data.key;

            window.open(baseUrl('hris/masterfile/employee?status=' + key), "_blank");
        }, this);  
        
    }).catch((err) => {
        console.log('some error handler here ' + err);
    });
    return "emp_status";
}

// Employee Status Company Filter Start
if ($('#select2_company').length > 0) {
    $('#select2_company').select2({
        placeholder: 'Select a company',
        width: '100%',
        allowClear: true,
        ajax: {
            url: baseUrl("hris/dashboard/get_company_select2_data"),
            dataType: 'json',
            delay: 250,
            global: false,
            processResults: function (data) {
                return data;
            },
        }
    }).on('select2:select', function (e) {
        select2_company_id = e.params.data.id;
        getEmployeeStatusChart(select2_company_id);
        $('#employee-status-graph-company span').html(e.params.data.text);
    }).on('select2:unselect', function (e) {
        $('#employee-status-graph-company span').html('');
        select2_company_id = 0;
        getEmployeeStatusChart(select2_company_id);

        // Close the dropdown after 3 seconds        
        setTimeout(() => {
            $(this).select2('close');
        }, 3000); 
    });
} else {
    console.error('#select2_company element not found.');
}
// Employee Status Company Filter End

/* END START EMPLOYEE STATUS CHART */

/* START EMPLOYEE STATUS PIE CHART */
var employeeStatusChartContainer = am4core.create("active-employees-per-company-container-pie", am4core.Container);
employeeStatusChartContainer.width = am4core.percent(100);
employeeStatusChartContainer.height = am4core.percent(100);

/*var employeeStatusChartLegendContainer = am4core.create("employee-status-chart-legend", am4core.Container);
employeeStatusChartLegendContainer.width = am4core.percent(100);
employeeStatusChartLegendContainer.height = am4core.percent(100);*/

let chartEmployeeStatus = new am4charts.PieChart();
chartEmployeeStatus.responsive.enabled = true;
chartEmployeeStatus.parent = employeeStatusChartContainer;
chartEmployeeStatus.legend = new am4charts.Legend();

// Add data
getEachEmployeeStatusDemographics(company_id).then((data) => {
        $("#employee-status-graph-total span").html(data.total);
        chartEmployeeStatus.data = data.data;

        // Set inner radius
        chartEmployeeStatus.innerRadius = am4core.percent(40);
        /*chartEmployeeStatus.legend = new am4charts.Legend();
        chartEmployeeStatus.legend.parent = employeeStatusChartLegendContainer;*/
        chartEmployeeStatus.responsive.enabled = true;

        // Add and configure Series
        var pieEmployeeStatus = chartEmployeeStatus.series.push(new am4charts.PieSeries());
        pieEmployeeStatus.dataFields.value = "cnt";
        pieEmployeeStatus.dataFields.category = "employee_status";
        pieEmployeeStatus.slices.template.stroke = am4core.color("#fff");
        pieEmployeeStatus.slices.template.strokeWidth = 2;
        pieEmployeeStatus.slices.template.strokeOpacity = 1;

        pieEmployeeStatus.labels.template.text = "{category}: {value}";
        pieEmployeeStatus.slices.template.tooltipText = "[bold]{category}: {value}[/]";

        // This creates initial animation
        pieEmployeeStatus.hiddenState.properties.opacity = 1;
        pieEmployeeStatus.hiddenState.properties.endAngle = -90;
        pieEmployeeStatus.hiddenState.properties.startAngle = -90;

        if(screen.width <= 450){
            pieEmployeeStatus.labels.template.radius = am4core.percent(-98);
        }else if(screen.width > 450 && screen.width <= 1280){
            pieEmployeeStatus.labels.template.radius = am4core.percent(-40);
        }else if(screen.width > 1280 && screen.width <= 1441){
            pieEmployeeStatus.labels.template.radius = am4core.percent(-20);
        }else{
            pieEmployeeStatus.labels.template.radius = am4core.percent(10);
        }
        $(window).resize(function(){
            if(screen.width <= 450){
                pieEmployeeStatus.labels.template.radius = am4core.percent(-98);
            }else if(screen.width > 450 && screen.width <= 1280){
                pieEmployeeStatus.labels.template.radius = am4core.percent(-40);
            }else if(screen.width > 1280 && screen.width <= 1441){
                pieEmployeeStatus.labels.template.radius = am4core.percent(-20);
            }else{
                pieEmployeeStatus.labels.template.radius = am4core.percent(10);
            }
        });
        //make labels show in the middle
        pieEmployeeStatus.ticks.template.disabled = false;
        pieEmployeeStatus.alignLabels = true;

        pieEmployeeStatus.labels.template.wrap = true;
        //

        pieEmployeeStatus.slices.template.states.getKey("hover").properties.scale = 1;
        pieEmployeeStatus.slices.template.states.getKey("active").properties.shiftRadius = 0;
        pieEmployeeStatus.slices.template.cursorOverStyle = am4core.MouseCursorStyle.pointer;

        pieEmployeeStatus.slices.template.events.on("hit", function (e) {
            const data = e.target.dataItem.dataContext;
            const key = data.key;

            window.open(baseUrl('hris/masterfile/employee?status=' + key), "_blank");
        }, this);
    })
    .catch((err) => {
        console.log('some error handler here ' + err);
    });
/* END START EMPLOYEE STATUS PIE CHART */

    /* START ACTIVE EMPLOYEES PER COMPANY */
    function getEmployeePerCompanyStatusChart(){
    var activeEmployeesPerCompany = am4core.create("active-employees-per-company-container", am4core.Container);
    activeEmployeesPerCompany.width = am4core.percent(100);
    activeEmployeesPerCompany.height = am4core.percent(100);

    let chartActiveEmployeesPerCompany = new am4charts.XYChart3D();
    chartActiveEmployeesPerCompany.parent = activeEmployeesPerCompany;

    // Add data
    getActiveEmployeesOnEachCompany
        .then((done) => {
            $("#active-employees-graph-total span").html(done.total);
            chartActiveEmployeesPerCompany.data = done.data;

            // Set inner radius
            var categoryAxis_empStatus = chartActiveEmployeesPerCompany.yAxes.push(new am4charts.ValueAxis());
            categoryAxis_empStatus.dataFields.category = "cnt";
            categoryAxis_empStatus.renderer.minGridDistance = 50;
            

            var valueAxis_empStatus = chartActiveEmployeesPerCompany.xAxes.push(new am4charts.CategoryAxis());
            valueAxis_empStatus.dataFields.category = "company";
            valueAxis_empStatus.renderer.minGridDistance = 10;
            

            if(screen.width <= 450){
                valueAxis_empStatus.renderer.labels.template.horizontalCenter = "right";
                valueAxis_empStatus.renderer.labels.template.verticalCenter = "middle";
                valueAxis_empStatus.renderer.labels.template.rotation = 300;
            }else if(screen.width >= 450 && screen.width <= 800){
                valueAxis_empStatus.renderer.labels.template.horizontalCenter = "right";
                valueAxis_empStatus.renderer.labels.template.verticalCenter = "middle";
                valueAxis_empStatus.renderer.labels.template.rotation = 320;
            }else if(screen.width >= 1280 && screen.width <= 1600){
                valueAxis_empStatus.renderer.labels.template.horizontalCenter = "right";
                valueAxis_empStatus.renderer.labels.template.verticalCenter = "middle";
                valueAxis_empStatus.renderer.labels.template.rotation = 320;
            }

            $(window).resize(function(){
                if(screen.width <= 450){
                    valueAxis_empStatus.renderer.labels.template.horizontalCenter = "right";
                    valueAxis_empStatus.renderer.labels.template.verticalCenter = "middle";
                    valueAxis_empStatus.renderer.labels.template.rotation = 300;
                }else if(screen.width >= 450 && screen.width <= 800){
                    valueAxis_empStatus.renderer.labels.template.horizontalCenter = "right";
                    valueAxis_empStatus.renderer.labels.template.verticalCenter = "middle";
                    valueAxis_empStatus.renderer.labels.template.rotation = 320;
                }else if(screen.width >= 1280 && screen.width <= 1600){
                    valueAxis_empStatus.renderer.labels.template.horizontalCenter = "right";
                    valueAxis_empStatus.renderer.labels.template.verticalCenter = "middle";
                    valueAxis_empStatus.renderer.labels.template.rotation = 320;
                }
            });

            // Add and chartEmployeeStatus Series
            var series = chartActiveEmployeesPerCompany.series.push(new am4charts.ColumnSeries3D());
            series.dataFields.categoryX = "company";
            series.dataFields.valueY = "cnt";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.tooltipText = "{cnt}";
            series.columns.template.column.stroke = am4core.color("#fff");
            series.columns.template.column.strokeOpacity = 0.2;

            
            series.columns.template.adapter.add("fill", function(fill, target){
                return chartActiveEmployeesPerCompany.colors.getIndex(target.dataItem.index);
              });
            
            series.columns.template.events.on("hit", function (e) {
                const data = e.target.dataItem.dataContext;
                const key = data.key;

                window.open(baseUrl('hris/masterfile/employee?company=' + key), "_blank");
            }, this);
            

        })
        .catch((err) => {
            console.log('some error handler here ' + err);
        });
        return "company";

        
    }
    /* START ACTIVE EMPLOYEES PER COMPANY */

    /* EMPLOYEE RETENTION GRAPH */
    // Create chart instance
    /*** var chart = am4core.create("chartdiv", am4charts.XYChart); ***/
    retentionChart = am4core.create("retention-chart-container", am4charts.XYChart);
    // Add data
    getRetentionRate
    .then((done) => {
        retentionChart.data = done;

        // Create axes
        var dateAxis = retentionChart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.minGridDistance = 50;

        var valueAxis = retentionChart.yAxes.push(new am4charts.ValueAxis());

        // Create series
        var seriesNewlyHired = retentionChart.series.push(new am4charts.LineSeries());
        seriesNewlyHired.dataFields.valueY = "newly_hired";
        seriesNewlyHired.dataFields.dateX = "date";
        seriesNewlyHired.strokeWidth = 2;
        seriesNewlyHired.minBulletDistance = 10;
        seriesNewlyHired.stroke = am4core.color("#43A047");
        seriesNewlyHired.propertyFields.dummyData = "breakdown";
        seriesNewlyHired.tooltipText = "[bold]{name}:[/] [bold #43A047]{valueY}[/]";
        seriesNewlyHired.tooltip.pointerOrientation = "vertical";
        seriesNewlyHired.name = "Newly Hired";
        seriesNewlyHired.tooltip.getFillFromObject = false;
        seriesNewlyHired.tooltip.getStrokeFromObject = true;
        seriesNewlyHired.tooltip.background.fill = am4core.color("#fff");
        seriesNewlyHired.tooltip.background.strokeWidth = 2;
        seriesNewlyHired.tooltip.label.fill = seriesNewlyHired.stroke;

        // Create series
        var seriesResigned = retentionChart.series.push(new am4charts.LineSeries());
        seriesResigned.dataFields.valueY = "resigned";
        seriesResigned.dataFields.dateX = "date";
        seriesResigned.strokeWidth = 2;
        seriesResigned.strokeDasharray = "3,4";
        seriesResigned.minBulletDistance = 10;
        seriesResigned.stroke = am4core.color("#D84315");
        seriesNewlyHired.propertyFields.dummyData = "breakdown";
        seriesResigned.tooltipText = "[bold]{name}:[/] [bold #D84315]{valueY}[/]";
        seriesResigned.tooltip.pointerOrientation = "vertical";
        seriesResigned.name = "Resigned";
        seriesResigned.tooltip.getFillFromObject = false;
        seriesResigned.tooltip.getStrokeFromObject = true;
        seriesResigned.tooltip.background.fill = am4core.color("#fff");
        seriesResigned.tooltip.background.strokeWidth = 2;
        seriesResigned.tooltip.label.fill = seriesResigned.stroke;

        // Add cursor
        retentionChart.cursor = new am4charts.XYCursor();
        retentionChart.cursor.xAxis = dateAxis;
        retentionChart.legend = new am4charts.Legend();
    })
    .catch((err) => {
        console.log('some error handler here ' + err);
    });
    /* END EMPLOYEE RETENTION GRAPH */

    /* START PERSONNEL REQUEST SUMMARY */
    function getPersonnelRequestSummaryChart(){
    var personnelRequestSummary = am4core.create("personnel-request-summary-container", am4core.Container);
    personnelRequestSummary.width = am4core.percent(100);
    personnelRequestSummary.height = am4core.percent(100);

    let chartPersonnelRequestSummary = new am4charts.XYChart3D();
    chartPersonnelRequestSummary.parent = personnelRequestSummary;

    getPersonnelRequestSummary
        .then((data) => {
            $("#personnel-request-graph-total span").html(data.total);
            chartPersonnelRequestSummary.data = data.data;

            // Set inner radius
            var categoryAxis_personnel = chartPersonnelRequestSummary.yAxes.push(new am4charts.CategoryAxis());
            categoryAxis_personnel.dataFields.category = "category";
            categoryAxis_personnel.renderer.minGridDistance = 15;
            

            var valueAxis_personnel = chartPersonnelRequestSummary.xAxes.push(new am4charts.ValueAxis());
            valueAxis_personnel.dataFields.category = "value";
            valueAxis_personnel.renderer.minGridDistance = 100;

            let label = categoryAxis_personnel.renderer.labels.template;
            
            

            // Add and chartEmployeeStatus Series
            var series = chartPersonnelRequestSummary.series.push(new am4charts.ColumnSeries3D());
            series.dataFields.categoryY = "category";
            series.dataFields.valueX = "value";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.tooltipText = "{value}";
            series.columns.template.column.stroke = am4core.color("#fff");
            series.columns.template.column.strokeOpacity = 0.2;
            if(screen.width <= 450){
                label.wrap = true;
                label.maxWidth = 120;
            }
            $(window).resize(function(){
                if(screen.width <= 450){
                    label.wrap = true;
                    label.maxWidth = 120;
                }
            });
            
            series.columns.template.adapter.add("fill", function(fill, target){
                return chartPersonnelRequestSummary.colors.getIndex(target.dataItem.index);
            });
            

            series.columns.template.events.on("hit", function (e) {
                const data = e.target.dataItem.dataContext;
                const key = data.data.key;
                window.open(baseUrl('hris/masterfile/personnel_request/?filter=' + key), "_blank");
            }, this);
        })
        .catch((err) => {
            console.log('some error handler here ' + err);
        });

        return "personnel";
    /* END PERSONNEL REQUEST SUMMARY */
    }

}); // end am4core.ready()

const getGenderDemographics = new Promise((resolve, reject) => {
    $.ajax({
        url: baseUrl('hris/dashboard/get_gender_demographics'),
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            resolve(response);
        },
        error: function (response) {
            reject(response);
        }
    });

    // socket.emit('get_gender_demographics');
    // socket.on('getGenderDemographics', function(d){
    //     resolve(d);
    // });
});

// const getEachEmployeeStatusDemographics = new Promise((resolve, reject) => {
//     $.ajax({
//         url: baseUrl('hris/dashboard/get_each_employee_status_demographics'),
//         type: "GET",
//         // type: "POST",
//         dataType: "JSON",
//         data: {
//             company_id: select2_company_id
//         },
//         success: function (response) {
//             resolve(response);
//             console.log(response);
//         },
//         error: function (response) {
//             reject(response);
//         }
//     });

//     // socket.emit("get_each_employee_status_demographics");
//     // socket.on('getEachEmployeeStatusDemographics', function(d){
//     //     resolve(d);
//     // });
// });

function getEachEmployeeStatusDemographics(company_id) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: baseUrl('hris/dashboard/get_each_employee_status_demographics/' + company_id),
            type: "GET",
            dataType: "JSON",
            global: false,
            // data: { company_id: company_id },
            success: function (response) {
                resolve(response);
            },
            error: function (response) {
                reject(response);
            }
        });
    });
}

const getActiveEmployeesOnEachCompany = new Promise((resolve, reject) => {
    $.ajax({
        url: baseUrl('hris/dashboard/get_active_employees_on_each_company/DESC'),
        /*** url: baseUrl('hris/dashboard/get_active_employees_on_each_company'), ***/
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            resolve(response);
        },
        error: function (response) {
            reject(response);
        }
    });
    // socket.emit('getActiveEmployeesOnEachCompany');
    // try{
    //     socket.on('getActiveEmp', function(d){
    //         resolve(d);
    //     });
    // }catch(e){
    //     reject(e);
    // }
});

const getRetentionRate = new Promise((resolve, reject) => {
    $.ajax({
        url: baseUrl('hris/dashboard/get_retention_rate'),
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            resolve(response);
        },
        error: function (response) {
            reject(response);
        }
    });
    // socket.emit('getRetentionRate');
    // socket.on('retention_rate', function(d){
    //     resolve(d);
    // });
});

const getPersonnelRequestSummary = new Promise((resolve, reject) => {
    $.ajax({
        url: baseUrl('hris/dashboard/get_personnel_request_summary'),
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            resolve(response);
        },
        error: function (response) {
            reject(response);
        }
    });

    // socket.emit('get_personnel_request_summary');
    // socket.on('getPersonnelRequestSummary', function(d){
    //     resolve(d);
    // });
});

// run this event handler once to init table
// override datatable search
$('#table-on-leave-employees')
    .one('preInit.dt', function () {
        // remove the current handler.
        $('#table-on-leave-employees_filter input[type="search"]').off();
        $('#table-on-leave-employees_filter').html(
            '       <div class="form-group m-form__group" style="width: 250px;">' +
            '           <div class="m-input-icon m-input-icon--left position-relative">' +
            '               <input type="search" class="form-control m-input ml-0" placeholder="Search for..." style="box-shadow: none; width: 100%;">' +
            '               <span class="m-input-icon__icon m-input-icon__icon--left">' +
            '                   <span>' +
            '                       <i class="fa fa-search"></i>' +
            '                   </span>' +
            '               </span>' +
            '           </div>' +
            '       </div>');

        // set variable for ease below
        var sbox = $('.table-on-leave-employees_filter input[type="search"]')

        //setup before functions
        var typingTimer;                //timer identifier
        var doneTypingInterval = 1000;  //time in ms, 5 second for example

        //on keyup, start the countdown
        sbox.on('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        });

        //on keydown, clear the countdown
        sbox.on('keydown', function () {
            clearTimeout(typingTimer);
        });

        //user is "finished typing," do something
        function doneTyping() {
            $('#table-on-leave-employees').DataTable().search(sbox.val()).draw();
            $('#table-on-leave-employees').DataTable().ajax.reload();
        }

        $('#table-on-leave-employees_filter').addClass('float-right');
    });


/* START DATA TABLES */
let tblOnLeaveEmployees = $('#table-on-leave-employees')
    .DataTable({
        dom: 'lfrtip',
        serverSide: true,
        processing: true,
        searching: true,
        ordering: true,
        lengthMenu: [[5, 10, 20, 30, 50, 100, -1], [5, 10, 20, 30, 50, 100, 'All']],
        order: [[4, 'asc']],
        ajax: {
            url: baseUrl('hris/dashboard/get_on_leave_employees'),
            type: 'post',
            dataType: 'json',
            data: function (d) {
                d.csrf_token = _csrf_hash;
            }
        },
        columns: [
            {
                data: 'status',
                width: '8%',
                render: function (data) {
                    const badgeColor = data === "Pending" ? "m-badge--warning" : "m-badge--success";
                    return "<span class='m-badge " + badgeColor + " m-badge--wide m--font-boldest'>" + data + "</span>";
                }
            },
            {data: 'employee_name', width: '20%'},
            {data: 'nature', width: '15%'},
            {
                data: 'leave_type',
                width: '10%',
                render: function (data) {
                    let type = null;
                    switch (parseInt(data)) {
                        case 1:
                            type = "Undertime";
                            break;
                        case 2:
                            type = "Half Day";
                            break;
                        case 3:
                            type = "Whole Day";
                            break;
                        default:
                            type = "Others";
                            break;
                    }

                    return type;
                }
            },
            {
                data: 'date_from',
                width: "20%",
                render: function (data, type, row) {
                    let date = null;
                    const leave_type = parseInt(row.leave_type);
                    if (leave_type === 1 || leave_type === 2) {
                        date = moment(data).format("MMM DD, YYYY");
                        date += " " + moment(data).format("hh:mm A") + "-" + moment(row.date_to).format("hh:mm A");
                    } else if (leave_type === 3) {
                        date = moment(data).format("MMM DD, YYYY");
                    } else {
                        date = moment(data).format("MMM DD, YYYY hh:mm A");
                        date += " - " + moment(row.date_to).format("MMM DD, YYYY hh:mm A");
                    }

                    return date;
                }
            },
            {
                data: 'reference_no',
                orderable: false,
                width: '15%'
            }
        ],
        pageLength: 5
    });

function initEvaluationTableSearchBox() {
    $('#table-employee-evaluation').DataTable().destroy();
    $('#table-employee-evaluation')
        .one('preInit.dt', function () {
            // remove the current handler.
            $('#table-employee-evaluation_filter input[type="search"]').off();
            $('#table-employee-evaluation_filter').html(
                '       <div class="form-group m-form__group" style="width: 250px;">' +
                '           <div class="m-input-icon m-input-icon--left position-relative">' +
                '               <input type="search" class="form-control m-input ml-0" placeholder="Search for..." style="box-shadow: none; width: 100%;">' +
                '               <span class="m-input-icon__icon m-input-icon__icon--left">' +
                '                   <span>' +
                '                       <i class="fa fa-search"></i>' +
                '                   </span>' +
                '               </span>' +
                '           </div>' +
                '       </div>');

            // set variable for ease below
            var sbox = $('#table-employee-evaluation_filter input[type="search"]')

            //setup before functions
            var typingTimer;                //timer identifier
            var doneTypingInterval = 1000;  //time in ms, 5 second for example

            //on keyup, start the countdown
            sbox.on('keyup', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(doneTyping, doneTypingInterval);
            });

            //on keydown, clear the countdown
            sbox.on('keydown', function () {
                clearTimeout(typingTimer);
            });

            //user is "finished typing," do something
            function doneTyping() {
                $('#table-employee-evaluation').DataTable().search(sbox.val()).draw();
                $('#table-employee-evaluation').DataTable().ajax.reload();
            }

            $('#table-employee-evaluation_filter').addClass('float-right');
        });
}

$(".employee_evaluation_options").select2();

initEvaluationTableSearchBox();

// =================================================================================

$('#evaluation_tab [data-toggle="tab"]').on('click', function (e) {
    e.preventDefault();

    // Remove active class from all nav links
    $('#evaluation_tab .nav-link, #evaluation_tab .dropdown-item').removeClass('active');

    // Add active class to the clicked link
    $(this).addClass('active');

    // Handle dropdown: mark the dropdown toggle as active if a dropdown-item was clicked
    if ($(this).hasClass('dropdown-item')) {
        $(this).closest('.dropdown').find('.dropdown-toggle').addClass('active');
    }

    // Hide all tab panes
    $('.tab-pane').removeClass('show active');

    // Show the corresponding tab
    const target = $(this).attr('href');
    $(target).addClass('show active');
});

// =================================================================================
// Load Evaluation Table
let loadEvaluationTable_stage;
let search_val_eval_list = "";

const tblEvaluation = $('#table-employee-evaluation').DataTable({
    destroy: true,
    // dom: 'lfrtip',
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    searching: true,
    ordering: true,
    lengthMenu: [[5, 10, 20, 30, 50, 100, -1], [5, 10, 20, 30, 50, 100, 'All']],
    order: [[6, 'desc']],
    ajax: {
        url: baseUrl('hris/dashboard/get_evaluation_list/'),
        type: 'post',
        dataType: 'json',
        global: false,
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val_eval_list;
        }
    },
    columns: [
        {data: 'checkbox'},
        {data: 'idno'},
        {data: 'employee_name'},
        {data: 'company'},
        {data: 'position'},
        {
            data: 'date_start',
            width: "15%",
            render: function (data) {
                return moment(data).format('ll');
            }
        },
        {
            data: 'evaluation_date',
            width: "15%",
            render: function (data) {
                return `<span class="m--font-boldest">${moment(data).format('ll')}</span>`;
            }
        },
    ],
    pageLength: 10,
    select: {
        style:    'multi',
        selector: 'td:first-child'
    },
    columnDefs: [
        {    
            orderable: false,
            className: 'select-checkbox',
            targets: 0,
        },
        {
            targets: "_all",
            className: "v-middle",
        }
    ],
    buttons: [
        { 
            extend: 'csv',
            exportOptions: {
                columns: "thead th:not(.notExport)",
            },
            customize: function(csv) {
                let data = csv.split('\n');

                let targetUppercase = [3]; // Columns to make uppercase
                let removeComma = [5, 6]; // Column to remove commas

                // Loop through each row
                data = data.map((row, rowIndex) => {  
                    // Split row into columns, considering quoted fields
                    let columns = row.match(/(".*?"|[^",\s]+)(?=\s*,|\s*$)/g);

                    columns = columns.map((col, columnIndex) => {
                        col = col.trim(); // Remove extra spaces
                
                        if (rowIndex === 0) { 
                            return col.replace(/\b\w/g, char => char.toUpperCase());
                        }
                
                        // Convert to uppercase for specific columns
                        if (targetUppercase.includes(columnIndex)) {
                            col = col.toUpperCase();
                        }

                        // Remove commas from specific columns
                        if (columnIndex === removeComma) {
                            col = col.replace(/,/g, '');
                        }
                
                        return col;
                    });

                    return columns.join(","); // Join modified columns
                });

                // Add UTF-8 BOM to the beginning of the CSV data for letter "ñ" to appear correctly
                const utf8BOM = '\uFEFF';
                return utf8BOM + data.join("\n"); // Reassemble CSV
            }
        },
        { 
            extend: 'excel',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            },
        },
        { 
            extend: 'pdfHtml5',
            exportOptions: {
                columns: "thead th:not(.notExport)",
            },
            orientation: 'landscape',
            pageSize: 'LEGAL',
            customize: function(doc) {
                // Set dynamic widths for all columns
                let columnWidths = new Array(doc.content[1].table.body[0].length).fill('*');

                // Define custom widths for specific columns (adjust index as needed)
                columnWidths[0] = '8%';
                columnWidths[1] = '18%';
                
                // Set font size for header row
                doc.styles = doc.styles || {};
                doc.styles.tableHeader = doc.styles.tableHeader || {};
                doc.styles.tableHeader.fontSize = 9; 
                doc.styles.tableHeader.fillColor = '#2d4154'; // Set header background color

                // Apply column widths
                doc.content[1].table.widths = columnWidths;

                // Loop through table body and target specific column
                doc.content[1].table.body.forEach(function (row, rowIndex) {

                    // Skip header row from all styles
                    if (rowIndex === 0) { return; }

                    let targetUppercase = [4, 5, 6, 7]; // Columns to make uppercase
                    let targetCenter = [4, 5, 6, 7]; // Columns to center align
                    let targetRight = []; // Column to right align

                    row.forEach((cell, columnIndex) => {
                        // Always set background color for even and odd rows
                        if (rowIndex % 2 === 0) {
                            cell.fillColor = '#f9f9f9'; // Light gray for even rows
                        } else {
                            cell.fillColor = '#ffffff'; // White for odd rows
                        }

                        // Set font size for other rows
                        cell.style = { fontSize: 9 }; 

                        // Vertical center
                        cell.valign = 'middle'; 

                        // Set text to uppercase for specific columns
                        if (cell.text && targetUppercase.includes(columnIndex)) {
                            cell.text = cell.text.toUpperCase();
                        }

                        // Center align specific columns
                        if (targetCenter.includes(columnIndex)) {
                            cell.alignment = 'center';
                        } 
                        
                        // Right align specific columns
                        if (targetRight.includes(columnIndex)) {
                            cell.alignment = 'right';
                        }
                    });
                });
            }
        }
    ],
    drawCallback: function () {
        $('#table-employee-evaluation #cb-select-all').prop('checked', false);  
    }
});

// Load Overdue Evaluation Table
let tblOverdueEvaluation_stage;
let search_val_eval_overdue = "";

const tblOverdueEvaluation = $('#table-employee-evaluation-overdue').DataTable({
    destroy: true,
    // dom: 'lfrtip',
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    searching: true,
    ordering: true,
    lengthMenu: [[5, 10, 20, 30, 50, 100, -1], [5, 10, 20, 30, 50, 100, 'All']],
    ajax: {
        url: baseUrl('hris/dashboard/evaluation_overdue/'),
        type: 'post',
        dataType: 'json',
        global: false,
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val_eval_overdue;
            d.evaluation_stage = tblOverdueEvaluation_stage;
        }
    },
    order: [[5, "desc"]],
    columns: [
        {data: 'checkbox'},
        {data: 'idno'},
        {data: 'employee_name'},
        {data: 'company'},
        {data: 'position'},
        {
            data: 'date_start',
            render: function (data) {
                return moment(data).format('ll');
            } 
        },
        {
            // Evaluation Stage
            data: 'eval_stage_date',
            orderable: false,
            render: function (data, type, row, meta) {
                let stages = [];

                for (let i = 0; i < data.length; i++) {
                    stages += data[i].evaluation_stage + ` <br>`;
                }

                return stages;
            }
        },
        {
            // Evaluation Date
            data: 'eval_stage_date',
            orderable: false,
            render: function (data, type, row, meta) {                
                let date = ``;

                for (let i = 0; i < data.length; i++) {
                    date += `${moment(data[i].evaluation_date).format('ll')} <br>`;
                }

                return date;
            }
        },
        {
            // Overdue Date of evaluation
            data: 'eval_stage_date',
            orderable: false,
            className: 'text-center',
            render: function (data) {                
                let overdue = ``;
                
                for (let i = 0; i < data.length; i++) {
                    let day_or_days = (data[i].overdue_date === 1) ? "Day" : "Days";
                    overdue += `<span class="m--font-boldest m--font-danger">${data[i].overdue_date} ${day_or_days}</span> <br>`;
                }

                return overdue;
            }
        },
    ],
    pageLength: 10,
    select: {
        style:    'multi',
        selector: 'td:first-child'
    },
    columnDefs: [
        {    
            orderable: false,
            className: 'select-checkbox',
            targets: 0,
        },
        {
            targets: "_all",
            className: "v-middle",
        }
    ],
    buttons: [
        { 
            extend: 'csv',
            exportOptions: {
                columns: "thead th:not(.notExport)",
            },
            customize: function(csv) {
                let data = csv.split('\n');

                let targetUppercase = [3]; // Columns to make uppercase
                let removeComma = [5, 7]; // Column to remove commas

                // Loop through each row
                data = data.map((row, rowIndex) => {  
                    // Split row into columns, considering quoted fields
                    let columns = row.match(/(".*?"|[^",\s]+)(?=\s*,|\s*$)/g);

                    columns = columns.map((col, columnIndex) => {
                        col = col.trim(); // Remove extra spaces
                
                        if (rowIndex === 0) { 
                            return col.replace(/\b\w/g, char => char.toUpperCase());
                        }
                
                        // Convert to uppercase for specific columns
                        if (targetUppercase.includes(columnIndex)) {
                            col = col.toUpperCase();
                        }

                        // Remove commas from specific columns
                        if (columnIndex === removeComma) {
                            col = col.replace(/,/g, '');
                        }
                
                        return col;
                    });

                    return columns.join(","); // Join modified columns
                });

                // Add UTF-8 BOM to the beginning of the CSV data for letter "ñ" to appear correctly
                const utf8BOM = '\uFEFF';
                return utf8BOM + data.join("\n"); // Reassemble CSV
            }
        },
        { 
            extend: 'excel',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            },
        },
        { 
            extend: 'pdfHtml5',
            exportOptions: {
                columns: "thead th:not(.notExport)",
            },
            orientation: 'landscape',
            pageSize: 'LEGAL',
            customize: function(doc) {
                // Set dynamic widths for all columns
                let columnWidths = new Array(doc.content[1].table.body[0].length).fill('*');

                // Define custom widths for specific columns (adjust index as needed)
                columnWidths[0] = '8%';
                columnWidths[1] = '18%';
                
                // Set font size for header row
                doc.styles = doc.styles || {};
                doc.styles.tableHeader = doc.styles.tableHeader || {};
                doc.styles.tableHeader.fontSize = 9; 
                doc.styles.tableHeader.fillColor = '#2d4154'; // Set header background color

                // Apply column widths
                doc.content[1].table.widths = columnWidths;

                // Loop through table body and target specific column
                doc.content[1].table.body.forEach(function (row, rowIndex) {

                    // Skip header row from all styles
                    if (rowIndex === 0) { return; }

                    let targetUppercase = [4, 5, 6, 7]; // Columns to make uppercase
                    let targetCenter = [4, 5, 6, 7]; // Columns to center align
                    let targetRight = []; // Column to right align

                    row.forEach((cell, columnIndex) => {
                        // Always set background color for even and odd rows
                        if (rowIndex % 2 === 0) {
                            cell.fillColor = '#f9f9f9'; // Light gray for even rows
                        } else {
                            cell.fillColor = '#ffffff'; // White for odd rows
                        }

                        // Set font size for other rows
                        cell.style = { fontSize: 9 }; 

                        // Vertical center
                        cell.valign = 'middle'; 

                        // Set text to uppercase for specific columns
                        if (cell.text && targetUppercase.includes(columnIndex)) {
                            cell.text = cell.text.toUpperCase();
                        }

                        // Center align specific columns
                        if (targetCenter.includes(columnIndex)) {
                            cell.alignment = 'center';
                        } 
                        
                        // Right align specific columns
                        if (targetRight.includes(columnIndex)) {
                            cell.alignment = 'right';
                        }
                    });
                });
            }
        }
    ],
    drawCallback: function () {
        $('#table-employee-evaluation-overdue #cb-select-all').prop('checked', false); 
    }
});
// =================================================================================

// Load Evaluation Table START
$('#EvalSearch').donetyping(function(callback) {
    search_val_eval_list = $(this).val();
    tblEvaluation.ajax.reload();
});

loadEvaluationTable();

function loadEvaluationTable(evaluation = null) {
    $('.overdue_eval_stage_text').text('Overdue');

    if(evaluation == null){
        evaluation = $(".employee_evaluation_options").val();
    }

    loadEvaluationTable_stage = evaluation;
    const newUrl = baseUrl('hris/dashboard/get_evaluation_list/' + loadEvaluationTable_stage);
    tblEvaluation.ajax.url(newUrl).load();

    const th = $('#table-employee-evaluation').find('th:eq(6)');
    switch (evaluation) {
        case '2nd':
            th.text('4.5TH MONTH');
            break;
        case 'final':
            th.text('5TH MONTH');
            break;
        case 'overdue':
            th.text('Overdue');
            break;
        default:
            th.text('3RD MONTH');
            break;
    }

    tblOverdueEvaluation.rows().deselect();
}

$('#table-employee-evaluation #cb-select-all').on('change', function() {
    if (this.checked) {
        tblEvaluation.rows().select();
    } else {
        tblEvaluation.rows().deselect();
    }
});

tblEvaluation.on('select deselect', function() {
    if (tblEvaluation.rows({ selected: true }).count() !== tblEvaluation.rows().count()) {
        $('#table-employee-evaluation #cb-select-all').prop('checked', false);  
    } else {
        $('#table-employee-evaluation #cb-select-all').prop('checked', true);
    }
});

// Evaluation Overdue datatable export button
$("#eval_ExportExcel").on("click", function() {
    tblEvaluation.button( '.buttons-excel' ).trigger();
    // saveExportLogs('Payments - Export Excel');
});

$("#eval_ExportCSV").on("click", function() {
    tblEvaluation.button( '.buttons-csv' ).trigger();
    // saveExportLogs('Payments - Export CSV');
});

$("#eval_ExportPDF").on("click", function() {
    tblEvaluation.button( '.buttons-pdf' ).trigger();
    // saveExportLogs('Payments - Export PDF');
});

// =================================================================================

// Load Evaluation Table Overdue START
function loadOverdueEvaluationTable(stage) {
    tblOverdueEvaluation_stage = stage;
    tblOverdueEvaluation.ajax.reload();

    // Hide export button if stage == 0, 0 is ALL
    if (stage == 0) {
        $('#exportBtn_eval_overdue').hide();
    } else {
        $('#exportBtn_eval_overdue').show();
    }

    let overdue_eval_stage_text = `Overdue`;

    switch (stage) {
        case 1:
            overdue_eval_stage_text = `3rd Month`;
            break;
        case 2:
            overdue_eval_stage_text = `4.5TH Month`;
            break;
        case 3:
            overdue_eval_stage_text = `5TH MONTH`;
            break;
        default:
            overdue_eval_stage_text = `Overdue`;
    }

    $('.overdue_eval_stage_text').text('Overdue: ' + overdue_eval_stage_text);

    tblEvaluation.rows().deselect();
}

$('#overdueEvalSearch').donetyping(function(callback) {
    search_val_eval_overdue = $(this).val();
    tblOverdueEvaluation.ajax.reload();
});

$('#table-employee-evaluation-overdue #cb-select-all').on('change', function() {
    if (this.checked) {
        tblOverdueEvaluation.rows().select();
    } else {
        tblOverdueEvaluation.rows().deselect();
    }
});

tblOverdueEvaluation.on('select deselect', function() {
    if (tblOverdueEvaluation.rows({ selected: true }).count() !== tblOverdueEvaluation.rows().count()) {
        $('#table-employee-evaluation-overdue #cb-select-all').prop('checked', false);  
    } else {
        $('#table-employee-evaluation-overdue #cb-select-all').prop('checked', true);
    }
});

// Evaluation Overdue datatable export button
$("#eval_due_ExportExcel").on("click", function() {
    tblOverdueEvaluation.button( '.buttons-excel' ).trigger();
    // saveExportLogs('Payments - Export Excel');
});

$("#eval_due_ExportCSV").on("click", function() {
    tblOverdueEvaluation.button( '.buttons-csv' ).trigger();
    // saveExportLogs('Payments - Export CSV');
});

$("#eval_due_ExportPDF").on("click", function() {
    tblOverdueEvaluation.button( '.buttons-pdf' ).trigger();
    // saveExportLogs('Payments - Export PDF');
});
// =================================================================================

function generateChartData(data) {
    var chartData = [];
    for (var i = 0; i < data.length; i++) {
        if (i === selectedPieItem) {
            if (data[i].hasOwnProperty('subs')) {
                for (var x = 0; x < data[i].subs.length; x++) {
                    chartData.push({
                        company: data[i].subs[x].company,
                        cnt: data[i].subs[x].cnt,
                        pulled: true
                    });
                }
            }
        } else {
            chartData.push({
                company: data[i].company,
                cnt: data[i].cnt,
                id: i
            });
        }
    }
    return chartData;
}


$('#column-options').on('click', function (e) {
    e.stopPropagation();
});

/* END DATA TABLES */

const loadTurnoverRate = function (ctr=0){
    $.ajax({
        url: baseUrl('hris/dashboard/get_retention_rate/'+ctr),
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            retentionChart.data = data;
        },
    });
}

const loadTurnoverRateByYear = function (year=null){
    if(year){
        $.ajax({
            url: baseUrl('hris/dashboard/get_retention_rate_by_year/'+year),
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                retentionChart.data = data;
            },
        });
    }else{
        return false;
    }
}