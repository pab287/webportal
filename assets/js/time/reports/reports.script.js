const initLateReportStartDate = moment();
const initLateReportEndDate = moment();
let selectedLateReportStartDate = moment();
let selectedLateReportEndDate = moment();

const initAbsenteeReportStartDate = moment();
const initAbsenteeReportEndDate = moment();
let selectedAbsenteeReportStartDate = moment();
let selectedAbsenteeReportEndDate = moment();

let filterLateReport = "Today";
const tblLateReport = $("#tbl-late-report");
const tblAbsenteeReport = $("#tbl-absentee-report");
let dtLateReport;
let dtAbsenteeReport;

const dropdown = $("#tbl-absentee-report > i");

getAbsenteeReportList();
//init company collection

$(".m-tabs__link")
    .on('click', function () {
        const url = $(this).attr('href');
        if (url === "#late_report_tab") {
            $("#absentee-report-options").addClass("m--hide");
            getLateReportList();
        } else {
            $("#absentee-report-options").removeClass("m--hide");
            getAbsenteeReportList();
        }
    });

$('#late-report-date-range-picker')
    .daterangepicker({
        buttonClasses: 'm-btn btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',

        startDate: initLateReportStartDate,
        endDate: initLateReportEndDate,
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
        selectedLateReportStartDate = start;
        selectedLateReportEndDate = end;

        let _label = label;
        filterLateReport = label;
        if (label === "Custom Range") {
            _label += ": <strong>" + start.format("MMM. DD, YYYY") + "</strong> to <strong>" + end.format("MMM. DD, YYYY") + "</strong>";
        }

        $(".selected-filter", $('#late-report-date-range-picker')).html(_label);
        getLateReportList();
    });

$('#absentee-report-date-range-picker')
    .daterangepicker({
        buttonClasses: 'm-btn btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',

        startDate: initAbsenteeReportStartDate,
        endDate: initAbsenteeReportEndDate,
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
        selectedAbsenteeReportStartDate = start;
        selectedAbsenteeReportEndDate = end;

        let _label = label;
        filterLateReport = label;
        if (label === "Custom Range") {
            _label += ": <strong>" + start.format("MMM. DD, YYYY") + "</strong> to <strong>" + end.format("MMM. DD, YYYY") + "</strong>";
        }

        $(".selected-filter", $('#absentee-report-date-range-picker')).html(_label);
        getAbsenteeReportList();
    });
    function getAbsenteeReportList() {
        dtAbsenteeReport = tblAbsenteeReport
            .DataTable({
                dom: 'rtlp',
                serverSide: true,
                destroy: true,
                ajax: {
                    url: baseUrl(`gcctime/reports/get_absentee_report`),
                    type: "POST",
                    dataType: "JSON",
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
                        d.startDate = moment(selectedAbsenteeReportStartDate).format("YYYY-MM-DD");
                        d.endDate = moment(selectedAbsenteeReportEndDate).format("YYYY-MM-DD");
                        d.search.value = $("#search-absentee-report").val();
                        d.department = $("#departmentSelect option:selected").val();
                        d.company = $("#companySelect option:selected").val();
                        
                    },error: function(xhr ,error, code){
                        if(error == "parsererror"){
                            dtAbsenteeReport.ajax.reload();
                        }
                    },
                },
                columns: [
                    {
                        data: "lastname", visible: false
                    },
                    {
                        width: "15%",
                        orderable: false,
                        data: null,
                        render: function () {
                            return moment(selectedAbsenteeReportStartDate).format("MMM. DD, YYYY") + " - " + moment(selectedAbsenteeReportEndDate).format("MMM. DD, YYYY");
                        }
                    },
                    {
                        width: "10%",
                        data: "biometric_id"
                    },
                    {
                        width: "20%",
                        data: "lastname",
                        render: function (data, type, row) {
                            return row.employee_name;
                        }
                    },
                    {
                        orderable: false,
                        data: "absentee_dates",
                        render: function (data) {
                            const late_dates = data.split(",");
                            let strDates = ``;
                            late_dates.forEach((date) => {
                                strDates += date;
                            });
    
                            return strDates;
                        }
                    },
                    {
                        data: "with_loa",
                        width: "8%",
                        className: "text-center",
                        orderable: false,
                        render: function (data) {
                            return `<span class="m--font-boldest">${data}</span>`;
                        }
                    },
                    {
                        data: "whole_day",
                        width: "8%",
                        className: "text-center",
                        orderable: false,
                        render: function (data) {
                            return `<span class="m--font-boldest">${data}</span>`;
                        }
                    },
                    {
                        data: "half_day_count",
                        width: "8%",
                        className: "text-center",
                        orderable: false,
                        render: function (data) {
                            return `<span class="m--font-boldest">${data}</span>`;
                        }
                    },
                    {
                        data: "total_absent",
                        width: "8%",
                        className: "text-center",
                        render: function (data) {
                            return `<span class="m--font-boldest">${data}</span>`;
                        }
                    }
                ],
                buttons: [
                    {
                        extend: 'excel',
                        text: 'EXCEL',
                        title: "Absentee Report - " + moment().format('ll'),
                        exportOptions: {
                            columns: ':visible:not(:eq(0)):not(.actions)'
                        },
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        title: "Absentee Report - " + moment().format('ll'),
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        customize: function (doc) {
                            doc.styles.tableHeader.alignment = 'left';
                        },
                        exportOptions: {
                            columns: ':visible:not(:eq(0)):not(.actions)'
                        }
                    }
                ],
            });
    }

function getLateReportList() {
    dtLateReport = tblLateReport
        .DataTable({
            dom: 'rtlp',
            serverSide: true,
            destroy: true,
            ajax: {
                url: baseUrl(`gcctime/reports/get_late_report`),
                type: "POST",
                dataType: "JSON",
                data: function (d) {
                    d.csrf_token = _csrf_hash;
                    d.startDate = moment(selectedLateReportStartDate).format("YYYY-MM-DD");
                    d.endDate = moment(selectedLateReportEndDate).format("YYYY-MM-DD");
                    d.search.value = $("#search-late-report").val();
                    d.department = $("#departmentSelectLate option:selected").val();
                    d.company = $("#companySelectLate option:selected").val();
                },error: function(xhr ,error, code){
                    if(error == "parsererror"){
                        dtLateReport.ajax.reload();
                    }
                },
            },
            columns: [
                {
                    data: "lastname", visible: false
                },
                {
                    width: "15%",
                    orderable: false,
                    data: null,
                    render: function () {
                        return moment(selectedLateReportStartDate).format("MMM. DD, YYYY") + " - " + moment(selectedLateReportEndDate).format("MMM. DD, YYYY");
                    }
                },
                {
                    width: "10%",
                    data: "biometric_id"
                },
                {
                    width: "20%",
                    data: "lastname",
                    render: function (data, type, row) {
                        return row.employee_name;
                    }
                },
                {
                    orderable: false,
                    data: "late_dates",
                    render: function (data) {
                        const late_dates = data.split(",");
                        let strDates = ``;
                        late_dates.forEach((ldate) => {
                            const ldate_formatted = moment(ldate).format("MMM.DD,YYYY hh:mm A");
                            strDates += `<span style="border-radius: 3em;" 
                                               class="m-badge m-badge--outline m-badge--wide pt-1 pb-1
                                                      m--font-boldest2 m--margin-right-5
                                                      m-badge--danger">${ldate_formatted}</span>`;
                        });

                        return strDates;
                    }
                },
                {
                    data: "late_count",
                    className: "text-center",
                    render: function (data) {
                        return `<span class="m--font-boldest2 m--font-danger">${data}</span>`;
                    }
                }
            ],
            buttons: [
                {
                    extend: 'excel',
                    text: 'EXCEL',
                    title: "Late Report - " + moment().format('ll'),
                    exportOptions: {
                        columns: ':visible:not(:eq(0)):not(.actions)'
                    },
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    title: "Late Report - " + moment().format('ll'),
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    customize: function (doc) {
                        doc.styles.tableHeader.alignment = 'left';
                    },
                    exportOptions: {
                        columns: ':visible:not(:eq(0)):not(.actions)'
                    }
                }
            ],
        });
}



function exportAs(type) {
    dropdown.removeClass();
    dropdown.addClass("fa fa-spinner fa-spin");

    setTimeout(() => {
        switch (type) {
            case "excel":
                dtAbsenteeReport.button(".buttons-excel").trigger();
                break;
            case "pdf":
                dtAbsenteeReport.button(".buttons-pdf").trigger();
                break;
        }
    }, 150);
}

function exportAsLate(type) {
    dropdown.removeClass();
    dropdown.addClass("fa fa-spinner fa-spin");

    setTimeout(() => {
        switch (type) {
            case "excel":
                dtLateReport.button(".buttons-excel").trigger();
                break;
            case "pdf":
                dtLateReport.button(".buttons-pdf").trigger();
                break;
        }
    }, 150);
}

$("#clear-options").on("click",function(){
    $('#departmentSelect').val(null).trigger('change');
    $('#companySelect').val(null).trigger('change');
});

$("#clear-options-late").on("click",function(){
    $('#departmentSelectLate').val(null).trigger('change');
    $('#companySelectLate').val(null).trigger('change');
});

$("#search-late-report")
    .donetyping(function () {
        getLateReportList();
    });

    $("#companySelect")
    .select2({
        placeholder: 'Select',
        width: '100%',
        allowClear: true,
        ajax: {
            url: baseUrl("gcctime/reports/get_company_collection"),
            dataType: "json",
            async: true,
            contentType:"application/json; charset=utf-8",
            global: false,
            data: {csrf_token : _csrf_hash},
            processResults: function (data) {
                return data;
            }
        }
    });


    //init dept collection
$("#departmentSelect")
    .select2({
        placeholder: 'Select Department',
        width: '100%',
        ajax: {
            url: baseUrl("gcctime/reports/get_dept_collection"),
            dataType: "JSON",
            delay: 250,
            processResults: function (data) {
                const results = $.map(data.results, function (item) {
                    return {
                        text: item.description,
                        id: item.id
                    }
                });
    
                return {
                    results
                }
            }
        }
    });

    //init company collection
$("#companySelectLate")
.select2({
    placeholder: 'Select',
    width: '100%',
    allowClear: true,
    ajax: {
        url: baseUrl("gcctime/reports/get_company_collection"),
        dataType: "json",
        delay: 250,
        global: false,
        processResults: function (data) {
            return data;
        }
    }
});


//init dept collection
$("#departmentSelectLate")
.select2({
    placeholder: 'Select Department',
    width: '100%',
    ajax: {
        url: baseUrl("gcctime/reports/get_dept_collection"),
        dataType: "JSON",
        delay: 250,
        processResults: function (data) {
            const results = $.map(data.results, function (item) {
                return {
                    text: item.description,
                    id: item.id
                }
            });

            return {
                results
            }
        }
    }
});

function getAbsents(){
    if($("#companySelect").val() != '' && $("#departmentSelect").val() != ''){
        getAbsenteeReportList();
    }
}

function getlate(){
    if($("#companySelectLate").val() != '' && $("#departmentSelectLate").val() != ''){
        getLateReportList();
    }
}

// copy LOA to clipboard
// clipboard.min.js
function copyToClipboard(text){
    navigator.clipboard.writeText(text)
    .then(() => {
        alert('LOA copied to clipboard!');
    })
    .catch(err => {
        console.error('Error in copying text: ', err);
    });
}

// $(document).on("keyup", "#search-absentee-report", function () {
//     setTimeout(dtAbsenteeReport.ajax.reload(), 10000);
// });