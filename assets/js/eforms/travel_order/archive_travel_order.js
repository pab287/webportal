var search_val = "";
var start_date = "";
var end_date = "";
var tblTravelOrder = $("#table-travel_order").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/travel_order/get_travel_order_archive_list"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.search['value'] = search_val,
                d.start_date =start_date,
                d.end_date =end_date
        }
    },
    searching: true,
    columns: [
        { data: "status", render: function (data, type, row, meta) { return renderStatusHtml(data,row) } },
        { data: "reference_no" },
        { data: "company" },
        { data: null },
        { data: null, width: "15%", },
        { data: null, width: "25%", },
        { data: "created_dt", width: "10%", orderable: true, render: function (data, type, row, meta) { return dateDisplay(row.from_to)}},
        { data: null, width: "8%", className: "text-center" },
    ],
    columnDefs: [{
        data: null,
        defaultContent: "",
        targets: -1,
        orderable: false,
        render: function (data, type, row, meta) { return itemDatatableActions(row.id); },
    }, {
        data: null,
        defaultContent: "",
        targets: 3,
        orderable: false,
        render: function (data, type, row, meta) {
            var tempHtml = "<div class='custom-details driver--details'><p>No Assigned Driver</p></div>";
            if (typeof row.is_service !== "undefined" && row.is_service == "1" && row.driver !== "") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>" + row.driver + "</p>";
                if (typeof row.vehicle_plate !== "undefined" && row.vehicle_description !== "undefined") {
                    tempHtml += "<p>" + row.vehicle_plate + "</p>";
                    tempHtml += "<p>" + row.vehicle_description + "</p>";
                }
                tempHtml += "</div>";
            }
            if (typeof row.is_commute !== "undefined" && row.is_commute == "1") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>Commute</p>";
                tempHtml += "</div>";
            }
            if (typeof row.is_personal !== "undefined" && row.is_personal == "1") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>Personal</p>";
                tempHtml += "<p>Vehicle</p>";
                tempHtml += "</div>";
            }
            if (typeof row.is_others !== "undefined" && row.is_others == "1" && $.trim(row.others_remarks) !== "") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>" + row.others_remarks + "</p>";
                tempHtml += "</div>";
            }

            return tempHtml;
        },
    }, {
        data: null,
        defaultContent: "",
        targets: 4,
        orderable: false,
        render: function (data, type, row, meta) {
            var tempHtml = "<div class='custom-details driver--details'><ul class='custom_list--dot'><li>No Assigned Personnel</li></ul></div>";
            if (typeof row.personnels !== "undefined" && row.personnels.length > 0) {
                tempHtml = "<div class='custom-details'>";
                tempHtml += "<ul class='custom_list--dot'>";
                jQuery.each(row.personnels, function (i, v) {
                    tempHtml += "<li>" + v + "</li>";
                });
                tempHtml += "</ul>";
                tempHtml += "</div>";
            }

            return tempHtml;
        },
    }, {
        data: null,
        defaultContent: "",
        targets: 5,
        orderable: false,
        render: function (data, type, row, meta) {
            var tempHtml = "<div class='custom-details driver--details'><p>No Assigned Destination</p></div>";
            if (typeof row.destination !== "undefined" && row.destination.length > 0) {
                tempHtml = "<div class='custom-details'>";
                tempHtml += "<ul class='custom_list--dot'>";
                jQuery.each(row.destination, function (i, v) {
                    tempHtml += "<li>" + v + "</li>";
                });
                tempHtml += "</ul>";
                tempHtml += "</div>";
            }

            return tempHtml;
        },
    }],buttons: [
        { 
            extend: 'csv',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, { 
            extend: 'excel',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, { 
            extend: 'pdf',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }
    ]
});


$("#ExportExcel").on("click", function(e) {
    e.preventDefault();
    tblTravelOrder.button('.buttons-excel').trigger();
    $.ajax({
        url: baseUrl("eforms/travel_order/export_event_log_archive/1"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val
        }
    });
});

$("#ExportCSV").on("click", function(e) {
    e.preventDefault();
    tblTravelOrder.button('.buttons-csv').trigger();
    $.ajax({
        url: baseUrl("eforms/travel_order/export_event_log_archive/2"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val
        }
    });
});

$("#ExportPDF").on("click", function(e) {
    e.preventDefault();
    tblTravelOrder.button('.buttons-pdf').trigger();
    $.ajax({
        url: baseUrl("eforms/travel_order/export_event_log_archive/3"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val
        }
    });
});

function dateDisplay($row){
    return $row;
}

function renderStatusHtml(data,row) {
    switch (data) {
        case "Pending":
            return '<div class="m-badge m-badge--warning m-badge--wide" role="alert"><strong>Pending</strong></div>';
            break;
        case "Approved":
            if(row.accomplishment_dt=="0000-00-00 00:00:00"){
                return '<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Approved</strong></div>'; 
            }else{
                return '<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Accomplished</strong></div>'; 
            }
            
            break;
        case "Disapproved":
            return '<div class="m-badge m-badge--danger m-badge--wide" role="alert"><strong>Disapproved</strong></div>';
            break;
        case "HR Noted":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>HR Noted</strong></div>';
            break;
        case "Received":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>Received</strong></div>';
            break;
        default:
            return '<div class="m-badge m-badge--metal text-white m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
            break;
    }
}

function formatCalendarDate(data) {
    if (data == "0000-00-00 00:00:00") {
        return "";
    }
    else {
        return moment(data).format("MM/DD/YYYY");
    }

}

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += " <a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' href='" + baseUrl('eforms/travel_order/view_travel_order?id=') + $id + "' target='__blank'><i class='la la-pencil-square'></i></a>";
        return _actionButton;
    } else { return false; }
}
//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblTravelOrder.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblTravelOrder.ajax.reload();
});

$("#m_daterangepicker_2").daterangepicker({
    minDate: moment().subtract(2, 'years'),
}, function (start, end, label) {
    $("#end_date").val(end.format('YYYY-MM-DD HH:mm:ss'));
    $("#start_date").val(start.format('YYYY-MM-DD HH:mm:ss'));
    $("#range_date").val(start.format('L') + ' – ' + end.format('L'));
});

$('#m_daterangepicker_2').on('apply.daterangepicker', function (start, end) {
    //do something, like clearing an input
    start_date = $("#start_date").val();
    end_date = $("#end_date").val();
    //search_val= $("#range_date").val();
    tblTravelOrder.ajax.reload();
});