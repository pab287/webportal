const colors = [
    '#f44336',
    '#e91e63',
    '#9c27b0',
    '#673ab7',
    '#3f51b5',
    '#2196f3',
    '#03a9f4',
    '#009688',
    '#ff5722',
    '#795548',
    '#607d8b',
    '#A36060',
    '#C68351',
    '#F28F45',
    '#9D8A0E',
    '#64A438',
    '#418313',
    '#1BAC75',
    '#0F8C5C',
    '#4081A4',
    '#8598C2',
    '#56459B',
    '#3F26A1',
    '#290AA5',
    '#23165A',
    '#39165A',
    '#6323A0'
];

var tblDocument = $("#table-document");

function getColor(txt) {
    if ((txt === null || txt === '')) {
        return '#ffffff';
    }
    return colors[(txt.charCodeAt(0)) % colors.length];
}

am4core.ready(function () {
    get_category_analytics();
    get_company_analytics();
});

function get_category_analytics(){
    am4core.useTheme(am4themes_animated);

    $.ajax({
        type: "GET",
        url: baseUrl('qms/qms/get_category_analytics'),
        dataType: "JSON",
        success: function (response) {
            var result = response.data;

            if(result){
                result.map((item) => {
                    item.color = getColor(item.category);
                });
    
                am4core.useTheme(am4themes_animated);
    
                var chart = am4core.create("categoryChart", am4charts.XYChart3D);
    
                // Create axes
                var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
                categoryAxis.dataFields.category = "category";
                categoryAxis.renderer.inversed = true;
    
                var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
    
                // Create series
                var series = chart.series.push(new am4charts.ColumnSeries3D());
                series.dataFields.valueX = "count";
                series.dataFields.categoryY = "category";
                series.name = "Count";
                series.columns.template.propertyFields.fill = "color";
                series.columns.template.tooltipText = "{valueX}";
                series.columns.template.column3D.stroke = am4core.color("#fff");
                series.columns.template.column3D.strokeOpacity = 0.2;
                chart.data = result;
            }else{
                var html = "";

                html += '<div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: center; height: 100%">';
                    html += '<h2 class="m-0">No Data Found.</h2>';
                html += '</div>';

                $("#categoryChart").html(html);
            }
        }
    });
}

function get_company_analytics(){
    am4core.useTheme(am4themes_animated);

    $.ajax({
        type: "GET",
        url: baseUrl('qms/qms/get_company_analytics'),
        dataType: "JSON",
        success: function (response) {
            var result = response.data;

            if(result){
                result.map((item) => {
                    item.color = getColor(item.company);
                });
    
                am4core.useTheme(am4themes_animated);

                var chart = am4core.create("companyChart", am4charts.XYChart3D);

                var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                categoryAxis.dataFields.category = "company";

                var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                valueAxis.dataFields.category = "count";
    
                var series = chart.series.push(new am4charts.ColumnSeries3D());
                series.dataFields.categoryX = "company";
                series.dataFields.valueY = "count";
                series.name = "Company";
                series.columns.template.propertyFields.fill = "color";
                series.columns.template.tooltipText = "{count}";
                series.columns.template.column.stroke = am4core.color("#fff");
                series.columns.template.column.strokeOpacity = 0.2;
                
                chart.data = result;
            }else{
                var html = "";

                html += '<div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: center; height: 100%">';
                    html += '<h2 class="m-0">No Data Found.</h2>';
                html += '</div>';

                $("#companyChart").html(html);
            }
        }
    });

}

var table = tblDocument.DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    searching: false,
    ordering: false,
    ajax: {
        url: baseUrl("qms/qms/get_recently_document_datatable"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash
        }
    },
    columns: [
        { data: "added_dt", visible: false },
        { data: 'title', width: '50%' },
        { data: "document_no", width: '40%' },
        { data: null }
    ],
    columnDefs: [
        {
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return policyDataTableActions(row.filename, row.id, row.title);
            }
        }
    ]
});

function policyDataTableActions(file, id, title){
    if (id) {
        var _actionButton = "";
        _actionButton +=
            " <button type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill btnViewPolicy' " +
            "   data-toggle='modal' data-target='#edit_modal' " +
            "   data-placement='bottom'" +
            "   data-skin='dark'" +
            "   title='View Document'" +
            "   data-delay='{\"show\": 300}' onclick='viewPDF(`" + file + "`, " + id + ", `" + title + "`)" +
            "'><i class='la la-file-pdf-o'></i></button>";

        return _actionButton;
    } else {
        return false;
    }
}

function viewPDF(file, id, title){
    var url = baseUrl('uploads/files/qms/document/' + id + '/' + file) + "?#toolbar=0";

    $("#view-pdf").attr('src', url);
    $("#view-title").text(title + ' File');

    $("#modalTempView").modal('show');
}