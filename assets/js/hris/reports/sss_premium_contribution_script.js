const filterHired = $("#tempFilter");
let company = [];
let employee = [];
if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){ company = _tempContentData.company; }
}
if(typeof filterHired !== "undefined" && filterHired.length == 1){
    $("#company").select2({
        placeholder: 'Select an option',
        width: '100%',
        data: company,
        allowClear: true
    });
    $("#employee").select2({
        placeholder: 'Select an option',
        width: '100%',
        data: employee,
        allowClear: true
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
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            locale: { format: 'MM/DD/YYYY' }
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

const resetFilter = function (event) {
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        form.find("input#hired_employees")[0].click();
        form.find("input#hired_sort")[0].click();
        form.find("input#ascending_sort")[0].click();
        initDateRangePicker(true);
        temporaryFilterByHired.all_filter = "all";
        const select2 = form.find("#company, #department, #position");
        if (typeof select2 !== "undefined" && select2.length > 0) {
            $.each(select2, function (_i, v) {
                const multi = $(v)[0].multiple;
                if (multi) {
                    $(v).val([])
                        .trigger("change")
                        .prop("disabled", false);
                } else {
                    $(v).val("")
                        .trigger("change");
                }
            });
        }
    }
}