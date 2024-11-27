const tblReport = $("#table-salary_payinfo-report");
let dtTableReport;

if(typeof tblReport !== "undefined" && tblReport.length == 1){
    dtTableReport = tblReport.DataTable({
        dom: 'rtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("hris/reports/get_salary_payinfo_datatable_request"),
        },
        columns: [
            { data: 'lastname', width: "12%" },
            { data: 'firstname', width: "12%" },
            { data: 'company_description', width: "12%" },
            { data: 'dept_code', width: "12%" },
            { data: 'dept_description', width: "12%" },
            { data: 'position', width: "12%" },
            { data: 'station', width: "*" },
            { data: 'salary_rate', className: "text-right", width: "10%", render: function(data){
                return numberFormat(data);
            } },
        ], buttons: [
            { extend: 'print', text: 'PRINT', 
                title: function () {
                    return `<div class="text-center m--regular-font-size-lg1">SALARY PAY INFORMATION REPORT</div>`;
                }, customize: function (win) {
                    var css = `@page { size: auto; margin: 0.5cm; }  
                    .dt-print-view table { font-size: 12px; }`,
                    head = win.document.head || win.document.getElementsByTagName('head')[0],
                    style = win.document.createElement('style');

                    style.type = 'text/css';
                    style.media = 'print';

                    if (style.styleSheet) {
                        style.styleSheet.cssText = css;
                    } else {
                        style.appendChild(win.document.createTextNode(css));
                    }

                    head.appendChild(style);
                    win.document.title = "Printable Page";

                    var tempTable = win.document.getElementsByClassName('dataTable')[0];
                    $(tempTable).removeClass("table-bordered");
                }
            },
            {extend: 'excel' }
        ]
    });
}

function printDtTable(el) {
    $("i", el).removeClass();
    $("i", el).addClass("fa fa-spinner fa-spin");
    $("i", el).css({ right: 0, left: 0 });

    setTimeout(() => {
        dtTableReport.button(".buttons-print").trigger();
        $("i", el).removeClass("fa fa-spinner fa-spin").addClass("fa fa-print");
        $("i", el).css({ top: "50%", left: "50%" });
    }, 150);
}

function exportExcelDtTable(el) {
    $("i", el).removeClass();
    $("i", el).addClass("fa fa-spinner fa-spin");
    $("i", el).css({ right: 0, left: 0 });

    setTimeout(() => {
        dtTableReport.button(".buttons-excel").trigger();
        $("i", el).removeClass("fa fa-spinner fa-spin").addClass("fa fa-print");
        $("i", el).css({ top: "50%", left: "50%" });
    }, 150);
}