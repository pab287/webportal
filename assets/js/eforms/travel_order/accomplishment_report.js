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

getReport();

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
    // getReport();
});

$("#search-accomplishments").donetyping( function(){
    getReport();

});

$("#clear-options").on("click",function(){
    $('#departmentSelect').val(null).trigger('change');
    $('#companySelect').val(null).trigger('change');
    $(".selected-filter", $('#accomplishment-report-date-range-picker')).html('Today');

    $('#accomplishment-report-date-range-picker').data('daterangepicker').setStartDate(initAccomplishmentReportStartDate);
    $('#accomplishment-report-date-range-picker').data('daterangepicker').setEndDate(initAccomplishmentReportStartDate);
});

$("#search-report").on('click', function(){
    getReport();
});

function getReport(){
    dtTable = tblReport.DataTable({
        dom: 'rtlpi',
        serverSide: true,
        destroy: true,
        ajax: {
            url: baseUrl(`eforms/travel_order/get_accomplishment_report`),
            type: "POST",
            dataType: "JSON",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.startDate = moment(selectedAccomplishmentReportStartDate).format("YYYY-MM-DD");
                d.endDate = moment(selectedAccomplishmentReportEndDate).format("YYYY-MM-DD");
                d.search['value'] = $("#search-accomplishments").val();
                d.department = $("#departmentSelect").val();
                d.company = $("#companySelect").val();
                
            },error: function(xhr ,error, code){
                if(error == "parsererror"){
                    dtTable.ajax.reload();
                }
            },
        },
        columns: [
            { data: "reference_no", width: '13%', 
                render: function(data, type, row, meta){
                    var html = '';

                    html += '<div style="line-height: 1.1">';
                    html += '<p class="mt-2 mb-0 m-font-3"><small><b>Reference No: '+ data +'</b></small></p>';
                    html += '<p class="mb-0 m-font-3"><small><b>File Under:</b> '+ row.company +'</small></p>';
                    html += '<p class="mb-0 m-font-3"><small><b>Department:</b> '+ row.department +'</small></p>';
                    html += '</div>';

                    return html;
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
        ]
    });
}