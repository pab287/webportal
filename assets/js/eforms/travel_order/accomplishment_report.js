const initAccomplishmentReportStartDate = moment();
const initAccomplishmentReportEndDate = moment();
// let selectedAccomplishmentReportStartDate = '02/05/2024';
// let selectedAccomplishmentReportEndDate = '02/05/2024';
let selectedAccomplishmentReportStartDate = moment();
let selectedAccomplishmentReportEndDate = moment();
let filterReport = "Today";

const tblReport = $("#report-table");
let dtTable;
var searchVal = '';

$("#departmentSelect").select2({
    placeholder: 'Select Department',
    width: '100%',
    allowClear: true,
    ajax: {
        url: baseUrl("eforms/travel_order/get_departments"),
        dataType: "JSON",
        delay: 250,
        processResults: function (data) {
            return data;
        }
    }
});

$("#companySelect").select2({
    placeholder: 'Select Company',
    width: '100%',
    allowClear: true,
    ajax: {
        url: baseUrl("eforms/travel_order/get_company_collection"),
        dataType: "json",
        delay: 250,
        global: false,
        processResults: function (data) {
            return data;
        }
    }
});

$("#statusSelect").select2({
    placeholder: 'Select Status',
    width: '100%',
    allowClear: true,
});

$('#accomplishment-report-date-range-picker').daterangepicker({
    buttonClasses: 'm-btn btn',
    applyClass: 'btn-primary',
    cancelClass: 'btn-secondary',
    startDate: initAccomplishmentReportStartDate,
    endDate: initAccomplishmentReportEndDate,
    showDropdowns: true,
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    format: "MMM. DD, YYYY"
}, function (start, end, label) {
    selectedAccomplishmentReportStartDate = start;
    selectedAccomplishmentReportEndDate = end;

    let _label = label;
    filterReport = label;
    if (label === "Custom Range") {
        _label += ": <strong>" + start.format("MMM. DD, YYYY") + "</strong> to <strong>" + end.format("MMM. DD, YYYY") + "</strong>";
    }

    $(".selected-filter", $('#accomplishment-report-date-range-picker')).html(_label);
});

$("#search-accomplishments").donetyping( function(){
    searchVal = $(this).val();
    dtTable.ajax.reload(null, false);
});

$("#clear-options").on("click",function(){
    $('#departmentSelect').val(null).trigger('change');
    $('#companySelect').val(null).trigger('change');
    $("#statusSelect").val(null).trigger('change');
    $(".selected-filter", $('#accomplishment-report-date-range-picker')).html('Today');

    $('#accomplishment-report-date-range-picker').data('daterangepicker').setStartDate(initAccomplishmentReportStartDate);
    $('#accomplishment-report-date-range-picker').data('daterangepicker').setEndDate(initAccomplishmentReportStartDate);

    selectedAccomplishmentReportStartDate = moment();
    selectedAccomplishmentReportEndDate = moment();
    dtTable.ajax.reload(null, false);
});

$("#search-report").on('click', function(){
    dtTable.ajax.reload(null, false);
});

dtTable = tblReport.DataTable({
    dom: 'rtlpi',
    serverSide: true,
    destroy: true,
    buttons: [
        {
            extend: 'excel',
            exportOptions: {
                columns: [0,1,2,3,4,5,6,7,12,13,14],
            }
        }
    ],
    ajax: {
        url: baseUrl(`eforms/travel_order/get_accomplishment_report`),
        type: "POST",
        dataType: "JSON",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.startDate = moment(selectedAccomplishmentReportStartDate).format("YYYY-MM-DD");
            d.endDate = moment(selectedAccomplishmentReportEndDate).format("YYYY-MM-DD");
            d.search['value'] = searchVal;
            d.department = $("#departmentSelect").val();
            d.company = $("#companySelect").val();
            d.status = $("#statusSelect").val();
            
        },error: function(xhr ,error, code){
            if(error == "parsererror"){
                dtTable.ajax.reload();
            }
        },
    },
    columns: [
        { data: 'status', visible: false,
            render: function (data, type, row, meta) {
                var html = ``;

                html += data.toUpperCase();
                html += (row.is_emergency == 1) ? ' [EMERGENCY]' : '';

                return html;
            }
        },
        { data: 'reference_no', visible: false,
            render: function (data, type, row, meta) {
                return data.toUpperCase();
            }
        },
        { data: 'company', visible: false,
            render: function (data, type, row, meta) {
                return data.toUpperCase();
            }
        },
        { data: 'department', visible: false,
            render: function (data, type, row, meta) {
                return data.toUpperCase();
            }
        },
        { data: null, visible: false, 
            render: function (data, type, row, meta){
                var tempHtml = 'No Assigned Driver';

                if (typeof row.is_service !== "undefined" && row.is_service == "1" && row.driver !== "" || typeof row.is_hitch !== "undefined" && row.is_hitch == "1" && row.driver !== "") {
                    tempHtml = row.driver.toUpperCase();
                }

                if (typeof row.is_commute !== "undefined" && row.is_commute == "1") { tempHtml = 'COMMUTE'; }
                if (typeof row.is_personal !== "undefined" && row.is_personal == "1") { tempHtml = 'PERSONAL VEHICLE';}
                if (typeof row.is_others !== "undefined" && row.is_others == "1" && $.trim(row.others_remarks) !== "") { tempHtml = row.others_remarks.toUpperCase(); }

                return tempHtml.toUpperCase();
            }
        },
        { data: null, visible: false,
            render: function(data, type, row, meta) {
                var tempHtml = ' --- ';

                if (typeof row.is_service !== "undefined" && row.is_service == "1" && row.driver !== "" || typeof row.is_hitch !== "undefined" && row.is_hitch == "1" && row.driver !== "") {
                    if (typeof row.vehicle_plate !== "undefined" && row.vehicle_description !== "undefined") {
                        tempHtml = 'Plate: ' + row.vehicle_plate + '<br>\n';
                        tempHtml = 'Description: ' + row.vehicle_description + '<br>\n';
                    }
                }

                return tempHtml.toUpperCase();
            }
        },
        { data: null, visible: false,
            render: function (data, type, row, meta) {
                return typeof row.personnel !== "undefined" && row.personnel.length > 0 ? row.personnel.join(', ').toUpperCase() : 'No Assigned Personnel';
            }
        },
        { data: null, visible: false,
            render: function (data, type, row, meta) {
                return typeof row.destination !== "undefined" && row.destination.length > 0 ? row.destination.join(', ').toUpperCase() : 'No Assigned Destination';
            }
        },
        { data: "reference_no", width: '13%', 
            render: function(data, type, row, meta){
                return renderStatusHtml(row.status, row);
            }
        },
        { data: null, width: "15%", orderable: false, 
            render: function(data, type, row, meta){
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

                if (typeof row.is_hitch !== "undefined" && row.is_hitch == "1" && row.driver !== "") {
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
            }
        },
        { data: null, width: "15%", orderable: false,
            render: function(data, type, row, meta){
                var tempHtml = "<div class='custom-details driver--details'><ul class='custom_list--dot'><li>No Assigned Personnel</li></ul></div>";
                if (typeof row.personnel !== "undefined" && row.personnel.length > 0) {
                    tempHtml = "<div class='custom-details'>";
                    tempHtml += "<ul class='custom_list--dot'>";
                    jQuery.each(row.personnel, function (i, v) {
                        tempHtml += "<li>" + v + "</li>";
                    });
                    tempHtml += "</ul>";
                    tempHtml += "</div>";
                }

                return tempHtml;
            }
        },
        { data: null, width: "20%", orderable: false,
            render: function(data, type, row, meta){
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
            }
        },
        { data: 'created_dt', width: "12%" },
        { data: "created_dt", width: "15%", orderable: true,
            render: function (data, type, row, meta) {
                if (row.from_to.length > 0) {
                    let template = "" +
                        "<div class='custom-details'>" +
                        "   <ul class='custom_list--dot' id='from_to'>";
                    row.from_to.forEach((d) => {
                        template += "<li style='font-size: 12px;'>" + d + "</li>";
                    });
                    template += "</ul>" +
                        "</div>";
                    return template;
                }
                return "";
            }
        },
        { data: 'accomplished_name', visible: false,
            render: function (data, type, row, meta) {
                var html = `---`;

                if(typeof data != 'undefined' && data){
                    html += 'Name: ' + data.toUpperCase();
                    html += 'Date: ' + row.accomplishment_dt.toUpperCase();
                }

                return html.toUpperCase();
            }
        },
        { data: 'accomplished_name', 
            render: function(data, type, row, meta){
                var html = "";

                if(typeof data != 'undefined' && data){
                    html += '<div style="line-height: 1.1">';
                    html += '<p class="mt-2 mb-0 m-font-3"><small><b>'+ data +'</b></small></p>';
                    html += '<p class="mb-0 m-font-3"><small>'+ row.accomplishment_dt +'</small></p>';
                    html += '</div>';
                }else{
                    html = ' --- ';
                }

                return html;
            }
        }
    ], 
    drawCallback: function(settings) {
        if(typeof settings.json != "undefined"){
            const { recordsTotal } = settings.json;

            if (recordsTotal > 0) {
                $("#export-excel").prop('disabled', false);
            } else {
                $("#export-excel").prop('disabled', true);
            }
        }
    }
});

$('#export-excel').on('click', function() {
    dtTable.button(".buttons-excel").trigger();
});

function renderStatusHtml(data, row) {
    var action= '';
    switch (data) {
        case "Pending":
            action += '<div class="m-badge m-badge--warning text-white m-badge--wide " role="alert"><small><strong>For Recommendation</strong></small></div>\n';
            break;
        case "Recommend_Approved":
            action += '<div class="m-badge m-badge--info text-white m-badge--wide " role="alert"><small><strong>Pending Approval</strong></small></div>\n';
            break;
        case "Approved":
            if (row.accomplishment_dt == "0000-00-00 00:00:00" || (row.accomplished == 0 && row.accomplished)) {
                action += '<div class="m-badge m-badge--accent m-badge--wide accomplishment_'+row.id+'" role="alert"><small><strong>Approved</strong></small></div>\n';
            } else {
                action += '<div class="m-badge m-badge--success m-badge--wide" role="alert"><small><strong>Accomplished</strong></small></div>\n';
            }
            break;
        case "Disapproved":
            action += '<div class="m-badge m-badge--danger m-badge--wide" role="alert"><small><strong>Disapproved</strong></small></div>\n';
            break;
        case "HR Noted":
            action += '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><small><strong>HR Noted</strong></small></div>\n';
            break;
        case "Received":
            action += '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><small><strong>Received</strong></small></div>\n';
            break;
        default:
            action += '<div class="m-badge m-badge--metal text-white m-badge--wide" role="alert"><small><strong>Cancelled</strong></small></div>\n';
            break;
    }

    if (row.is_emergency == 1) {
        action += '<div><span class="m-badge m-badge--info text-white m-badge--wide mt-1"><small><strong>Emergency</strong></small></span></div>';
    }

    action += '<div style="line-height: 1.1"><p class="mt-2 mb-0 m-font-3"><small><b>Reference no: '+row.reference_no+'</b></small></p>\n';
    action += '<p class="mb-0 m-font-3"><small><b>File Under:</b> '+row.company+'</small></p>';
    action += '<p class="mb-0 m-font-3"><small><b>Department:</b> '+ row.department +'</small></p>';

    return action;
}