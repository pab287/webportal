const filterHired = $("#tempFilter");
let company = [];
let employee = [];
let _years = [];
let filters = {};
if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){ company = _tempContentData.company; }
    if(typeof _tempContentData.years !== "undefined" && _tempContentData.years.length > 0){ _years = _tempContentData.years; }
}
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

if(typeof filterHired !== "undefined" && filterHired.length == 1){
    $("#company").select2({
        placeholder: 'Select an option',
        width: '100%',
        data: company,
        allowClear: true
    }).on("select2:select", function (e) {
        $("#employee").val("").trigger("change");
    });

    $("#employee").select2({
        placeholder: 'Select an option',
        width: '100%',
        allowClear: true,
        ajax: {
            url: baseUrl('hris/reports/get_reports_select2_employee_data'),
            dataType: 'json',
            delay: 250,
            global: false,
            data: function (params) {
                return {
                    company_id: $("#company").val(),
                    q: params.term
                };
            },
            processResults: function (data) {
                return data;
            }
        }, minimumInputLength: 3,
        escapeMarkup: function (markup) {
            return markup;
        }
    });    
}

const initMonthSelect2 = function(){
    if(typeof filterHired !== "undefined" && filterHired.length == 1){
        $("#filter_month", filterHired).select2({
            placeholder: 'MONTH',
            width: '100%',
            data: months,
        }).on("select2:select, change", function (e) {
            $(e.target).validate();
        });
    }
}

const initYearSelect2 = function(){
    if(typeof filterHired !== "undefined" && filterHired.length == 1){
        if($("#filter_year", filterHired).hasClass("select2-hidden-accessible")){
            $("#filter_year", filterHired).select2("destroy");
        }
        $("#filter_year", filterHired).select2({
            placeholder: 'YEAR',
            width: '100%',
            data: _years,
        }).on("select2:select, change", function (e) {
            $(e.target).validate();
        });
    }
}

const vmTempFilterBy = new Vue({
    el: "#tempFilterBy",
    data: { all_filter: "all" },
    watch: { 
        all_filter(nValue){
            if(nValue == "month"){ 
                setTimeout(function(){
                    initMonthSelect2();
                    initYearSelect2();
                }, 250);
            }else if(nValue == "year"){
                setTimeout(function(){ initYearSelect2(); }, 250);
            }
        }
    }
});

const responseContent = new Vue({
    el: "#filteredContent",
    data: { rows: [], count: 0 }
});

const dtTable = $("#table-sss_premium_contribution").DataTable({
    dom: "<'row'<'col-md-8 dtDetails'><'col-md-4 dtActions m--hide'B>>rtlp",
    serverSide: false,
    processing: false,
    destroy: true,
    autoWidth: false,
    ordering: false,
    pageLength: 10,
    searching: false,
    columns: [
        { data: "year", className: "text-center" },
        { data: "month_name" },
        { data: "sss_total", className: "text-center", render: function (data) {
            return numberFormat(data);
        }},
        { data: "sbr_no", className: "text-center" },
        { data: "payment_date", className: "text-center", render: function (data) {
            return moment(data).format("MM/DD/YYYY");
        }}
    ],
    buttons: [
        {
            extend: 'print',
            autoPrint: false,
            text: '<i class="fa fa-print"></i><span class="m--font-boldest">PRINT</span>',
            className: "pull-right printAction btnPrint",
            title: function () {
                const { employee, company } = filters;
                const employeeName = typeof employee.employee_name !== "undefined" ? employee.employee_name : '';
                const sssNo = typeof employee.sss_no !== "undefined" ? employee.sss_no : '';
                const companyName = typeof company.company_name !== "undefined" ? company.company_name : 'GC&C, INC.';
                const companyAddress = typeof company.company_address !== "undefined" ? company.company_address : 'Carlos Hilado Ave., Circumferential Road, Brgy. Bata, Bacolod City, Negros Occidental';
                return `<div class="text-center m--regular-font-size-lg2" style="text-transform: uppercase;">${companyName.toUpperCase()}</div>
                <div class="text-center m--regular-font-size-sm1 text-muted" style="text-transform: uppercase;">${companyAddress.toUpperCase()}</div>
                <div class="text-center m--regular-font-size-lg2 mt-5">CERTIFICATE OF SSS PREMIUM CONTRIBUTION</div>
                <div class="m--regular-font-size-lg1 mt-5">TO  WHOM IT MAY CONCERN:</div>
                <div class="m--regular-font-size-lg1 mt-3 mb-3">
                    <p class="text-justify"><span class="ml-5"></span>THIS IS TO CERTIFY THAT WE HAVE REMITTED FOR MS./MR. <span class="m--font-boldest custom-underlined">${employeeName}</span>
                    WITH SSS NUMBER <span class="m--font-boldest custom-underlined">${sssNo}</span> THE FOLLOWING SSS PREMIUM CONTRIBUTIONS.
                    </p>
                </div>`;
            }, customize: function (win) {
                const { employee } = filters;
                const employeeName = typeof employee.employee_name !== "undefined" ? employee.employee_name : '';
                const css = `@page { size: auto portrait; margin: 1.27cm 2.54cm 0.5cm 2.54cm; }
                    table { font-size: 14px; }
                    table.dataTable { margin: 0 auto; }
                    table.dataTable tfoot tr:first-child th{ border-top: 1px solid #000000; }
                    table.dataTable tfoot tr:first-child th{ border-bottom: 4px double #000000; }
                    .print-size-auto{ width: auto; }
                    .print-size-5{ width: 5%; }
                    .print-size-8{ width: 8%; }
                    .print-size-10{ width: 10%; }
                    .print-size-25{ width: 25%; }
                    .printable-row_content{ page-break-inside: avoid; }
                    span.custom-underlined{ border-bottom: 0.5px solid black; }`,
                    head = win.document.head || win.document.getElementsByTagName('head')[0],
                    body = win.document.body || win.document.getElementsByTagName('body')[0],
                    style = win.document.createElement('style'),
                    footerDiv = win.document.createElement('div');

                style.type = 'text/css';
                style.media = 'print';

                if (style.styleSheet) { style.styleSheet.cssText = css; } 
                else { style.appendChild(win.document.createTextNode(css)); }

                head.appendChild(style);
                win.document.title = "SSS Premium Contibution Printable Page";
                const tempTable = win.document.getElementsByClassName('dataTable')[0];
                $(tempTable).removeClass("table-bordered");

                const todayMY = moment().format("MMMM YYYY");
                const dayToday = moment().format("Do");
                footerDiv.innerHTML = `<div class="printable-row_content">
                    <div class="m--regular-font-size-lg1 m--font-bolder mt-5"><p class="text-justify"><span class="ml-5"></span>THIS CERTIFICATION IS ISSUED THIS 
                        <span class="m--font-boldest custom-underlined">${dayToday.toUpperCase()}</span> DAY OF <span class="m--font-boldest custom-underlined">${todayMY.toUpperCase()}</span> 
                        UPON THE REQUEST OF MS./MR. <span class="m--font-boldest custom-underlined">${employeeName}</span> FOR WHATEVER LEGAL PURPOSE IT MAY SERVE.</p>
                    </div>
                <div>`;
                body.appendChild(footerDiv);
            }
        }
    ], drawCallback: function (settings) {
        const api = this.api();
        const btnPrint = $(settings.nTableWrapper).find(".printAction");
        const dtActions = $(settings.nTableWrapper).find(".dtActions");
        if (typeof btnPrint !== "undefined" && typeof dtActions !== "undefined") {
            btnPrint.addClass("btn m-btn btn-brand m-btn--icon m--hide animated fadeIn ml-1");
            const tempData = api.data();
            if (tempData.length > 0) {
                if (btnPrint.hasClass("m--hide") === true) { btnPrint.removeClass("m--hide"); }
                if (dtActions.hasClass("m--hide") === true) { dtActions.removeClass("m--hide"); }
            } else {
                if (dtActions.hasClass("m--hide") === false) { dtActions.addClass("m--hide"); }
                if (btnPrint.hasClass("m--hide") === false) { btnPrint.addClass("m--hide"); }
            }
        }
    }
});

const resetFilter = function (event) {
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        vmTempFilterBy.all_filter = "all";
        const select2 = form.find("#company, #employee");
        if (typeof select2 !== "undefined" && select2.length > 0) {
            $.each(select2, function (_i, v) {
                const multi = $(v)[0].multiple;
                if (multi) {
                    $(v).val([]).trigger("change").prop("disabled", false);
                } else {
                    $(v).val("").trigger("change");
                }
            });
        }
    }
}

$.validate({
    form: "#formFilter",
    lang: "en",
    onSuccess: function (form) {
        const currentForm = $(form);
        $.ajax({
            url: siteUrl("hris/reports/sss_premium_contribution_report_data"),
            type: "POST",
            dataType: "JSON",
            data: currentForm.serialize(),
            beforeSend: function () {
                currentForm
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                if (json.response) {
                    filters = { ...json.filter };
                    dtTable.clear().rows.add(json.data).draw(false);
                    responseContent.rows = json.data;
                    responseContent.count = json.count;

                    toastr.success(json.toastr_msg, "Filtered Options");
                } else {
                    toastr.error(json.toastr_msg, "Filtered Options");
                }
                currentForm.find(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });

        return false;
    }
});