const filterHired = $("#tempFilter");
let company = [];
let employee = [];
let entryDate = null;
if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){ company = _tempContentData.company; }
    if(typeof _tempContentData.entry_date !== "undefined" && _tempContentData.entry_date){ entryDate = _tempContentData.entry_date; }
}
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

const initDateRangePicker = function(destroy=false){
    if(typeof filterHired !== "undefined" && filterHired.length == 1){
        if(destroy){ 
            $("#date-picker", filterHired).daterangepicker("destroy"); 
            $("#date-range", filterHired).val("");
        }
        $("#date-picker", filterHired)
        .daterangepicker({
            maxDate: moment().format("MM/DD/YYYY"),
            minDate: moment(new Date(entryDate), "YYYY-MM-DD").format("MM/DD/YYYY"),
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            locale: { format: 'MM/DD/YYYY' },
        }).on('apply.daterangepicker', function (ev, picker) {
            $("#date-range", filterHired)
            .val(picker.startDate.format('MMM. DD, YYYY') + ' - ' + picker.endDate.format('MMM. DD, YYYY'))
            .validate();
            filters.date_range = $("#date-range", filterHired).val();
        }).on('cancel.daterangepicker', function (ev, picker) {
            filters.date_range = "";
        });
    }
}

initDateRangePicker();

const vmTempFilterBy = new Vue({
    el: "#tempFilterBy",
    data: { all_filter: "all" },
    watch: { 
        all_filter(nValue){
            if(nValue == "date_range"){ setTimeout(function(){ 
                initDateRangePicker(true); 
                toastr.info("Rendering Date Range Picker", "Filter By - Date Range");
            }, 500); }
        }
    }
});

const responseContent = new Vue({
    el: "#filteredContent",
    data: { rows: [], count: 0 }
});

const resetFilter = function (event) {
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        initDateRangePicker(true);
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
                    console.log(json);
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