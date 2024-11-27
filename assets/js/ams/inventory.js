let searchVal = "";
loadGraph([]);

let tblInventoryVerifiedList = $("#tbl-inventory-verified-list")
    .DataTable({
        dom: 'Bfrtlip',
        serverSide: true,
        processing: true,
        autoWidth: false,
        lengthMenu: [[30, 50, 100, 150, 200, -1], [30, 50, 100, 150, 200, "all"]],
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'GCC ASSET MANAGEMENT INVENTORY REPORT'
            },
            {
                extend: 'pdfHtml5',
                title: function(){
                    var type = $("select[name='type'] option:selected").text().toUpperCase();
                    var printTitle = type + " INVENTORY REPORT";
                    return printTitle;
                },
                messageTop: function(){
                    var type = $("select[name='type'] option:selected").text().toUpperCase();
                    var company = $("select[name='company'] option:selected").text().toUpperCase();
                    var date = $("input[name='date']").val();
                    return "DATE: "+date+"\nCOMPANY: "+company+"\nTYPE: "+type;
                },
                orientation: 'landscape'
            },
            {
                extend: 'print',
                title: function(){
                    var type = $("select[name='type'] option:selected").text().toUpperCase();
                    var printTitle = type + " INVENTORY REPORT";
                    return printTitle;
                },
                messageTop: function(){
                    var type = $("select[name='type'] option:selected").text().toUpperCase();
                    var company = $("select[name='company'] option:selected").text().toUpperCase();
                    var date = $("input[name='date']").val();
                    return "DATE: "+date+"<br>COMPANY: "+company+"<br>TYPE: "+type;
                }
            }
        ],
        ajax: {
            url: baseUrl("ams/inventory/get_verified_inventory_list"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = searchVal;
                d.date = $("input[name='date']").val();
                d.type = $("select[name='type']").val();
                d.company = $("select[name='company']").val();
                d.is_archived = $("input[name='is_archived']:checked").val();
            }
        },
        searching: false,
        columns: [
            {
                data: "company_code",
                width: "13%"
            },
            { 
                data: 'asset_code',
                width: "11%",
            },
            {
                data: 'asset_name',
                width: '15%',
            },
            /** original source code for asset code and name */
            // {
            //     data: "asset_name",
            //     width: "22%",
            //     render: function (data, type, row) {
            //         return "<p class='mb-0 m--regular-font-size-sm1 font-weight-bold text-muted'>" + row.asset_code + "</p>" +
            //             "<p class='mb-0 font-weight-bold'>" + (row.asset_name ? row.asset_name : "--") + "</p>" +
            //             "<p class='mb-0 text-muted m--regular-font-size-sm1'>" + row.category + "</p>";
            //     }
            // },
            /** original source code for asset code and name */
            {
                data: "status",
                width: "10%",
                render: function (data, type, row) {
                    let temp = data? true: false;
                    let tempdata = "--";
                    tempdata = (temp == false && row.temp_status)? row.temp_status: data? data: tempdata;
                    //return data ? data : "--";
                    return tempdata;
                }
            },
            { 
                data: 'created_at',
                width: '12%',
                render: function(data){
                    return moment(data).format('YYYY-MM-DD');
                }
            },
            /** commented out to be replace as create date */
            // {
            //     data: "inventory_status",
            //     width: "15%",
            // },
            /** commented out to be replace as create date */
            {
                data: "inventory_date",
                width: "13%"
            },
            {
                data: "location",
                width: "13%"
            },
            {
                data: "accounted_to",
                width: "13%",
                render: function (data) {
                    return data ? data : "--";
                }
            },
        ]
    });

$('#date-range-picker')
    .daterangepicker({
        buttonClasses: 'm-btn btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary'
    }, function (start, end, label) {
        $('#date-range-picker .form-control')
            .val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
    });

$("select[name='type']")
    .select2({
        placeholder: "Select Type"
    });

$("select[name='company']")
    .select2({
        placeholder: "Select Company",
        ajax: {
            url: baseUrl("ams/vehicles/get_company_collection"),
            dataType: "JSON",
            delay: 500,
            processResults: function (data) {
                const results = [{id: "all", text: "All"}];

                data.results.map((item) => {
                    results.push({
                        id: item.code,
                        text: item.description
                    });
                });

                return {results};
            }
        }
    });

function filterReport(el) {
    const form = $(el);
    const formData = new FormData(el);

    if (form.isValid()) {
        $.ajax({
            url: baseUrl("ams/inventory/get_inventory_report_data"),
            dataType: "JSON",
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                loadGraph(response.summary);
            }
        });
        
        tblInventoryVerifiedList.ajax.reload();
    }
}

function loadGraph(data) {
    am4core.ready(function () {
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        let chart = am4core.create("chartdiv", am4charts.PieChart);

        // Add and configure Series
        let pieSeries = chart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "value";
        pieSeries.dataFields.category = "cluster";

        // Let's cut a hole in our Pie chart the size of 30% the radius
        chart.innerRadius = am4core.percent(30);

        // Put a thick white border around each Slice
        pieSeries.slices.template.stroke = am4core.color("#fff");
        pieSeries.slices.template.strokeWidth = 1;
        pieSeries.slices.template.strokeOpacity = 1;
        pieSeries.slices.template
            // change the cursor on hover to make it apparent the object can be interacted with
            .cursorOverStyle = [
            {
                "property": "cursor",
                "value": "pointer"
            }
        ];

        pieSeries.alignLabels = true;
        pieSeries.labels.template.bent = true;
        pieSeries.labels.template.radius = 3;
        pieSeries.labels.template.padding(0, 0, 0, 0);

        pieSeries.ticks.template.disabled = false;

        // Create a base filter effect (as if it's not there) for the hover to return to
        let shadow = pieSeries.slices.template.filters.push(new am4core.DropShadowFilter);
        shadow.opacity = 0;

        // Create hover state
        let hoverState = pieSeries.slices.template.states.getKey("hover"); // normally we have to create the hover state, in this case it already exists

        // Slightly shift the shadow and make it more prominent on hover
        let hoverShadow = hoverState.filters.push(new am4core.DropShadowFilter);
        hoverShadow.opacity = 0.7;
        hoverShadow.blur = 5;
        var type = $("select[name='type'] option:selected").text().toUpperCase();
        let title = chart.titles.create();
        if(type == "ALL"){
            type = "ALL ASSET TOTAL PERCENTAGE";
        }else{
            type = type + " TOTAL PERCENTAGE";
        }
        title.text = type;
        title.fontSize = 18;
        title.fontWeight = 500;

        //Add chart export
        chart.exporting.menu = new am4core.ExportMenu();

        // Add a legend
        chart.legend = new am4charts.Legend();
        chart.data = data;
    });
}