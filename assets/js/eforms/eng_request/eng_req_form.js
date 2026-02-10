let _employee = null;
let _projects = null;
let rfiTable = null;
let rfaTable = null;
let is_archive = 0;

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.employee !== "undefined" && _tempContentData.employee.length > 0){
        _employee = _tempContentData.employee;
    }
    if(typeof _tempContentData.projects !== "undefined" && _tempContentData.projects.length > 0){
        _projects = _tempContentData.projects;
    }
}


$(document).ready(function () {

    rfiTable = $('#rfi_table').DataTable({
        serverSide: true,
        processing: true,
        searching: false,
        rowId: 'id',
        ajax: {
            url: baseUrl("eforms/engineering_request_forms/get_rfis"),
            type: "POST",
            dataType: "json",
            global: false,
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.is_archive = is_archive;
            }
        }
    }); 

});

$('#project_name').select2({
    dropdownParent: $('#newRFIModal'),
    width: '100%',
    placeholder: 'Select project',
    allowClear: true,
    data: _projects
});

$("#project_name").on("select2:select", function (e) {
    const data = e.params.data;
    $('#project_location').val(data.project_location);
}).on("select2:unselect", function () {
    $('#project_location').val('');
});

// $('#cc_to').select2({
//     placeholder: 'Select. .',
//     dropdownParent: $('#newRFIModal'),
//     tags: true,
//     multiple: true,
//     allowClear: false,
//     tokenSeparators: [',', ' '],
//     width: '100%',
//     createTag: function (params) {
//         const term = $.trim(params.term);
//         const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

//         if (term === '' || !emailRegex.test(term)) {
//             return null;
//         }

//         return {
//             id: term,
//             text: term,
//             newTag: true
//         };
//     }
// });

$.validate({
    form: "#project_form",
    lang: "en",
    onSuccess: function (form) {
        let formData = $(form).serializeArray();
        let ccValue = $('#cc_to').val();
        formData = formData.filter(item => item.name !== 'cc_to');
        if (ccValue) {
            ccValue.split(',').map(e => e.trim()).filter(e => e.length)
                .forEach(email => {
                    formData.push({ name: 'cc_to[]', value: email });
                });
        }

        formData.push({ name: 'csrf_token', value: _csrf_hash });
        $.ajax({
            url: siteUrl("eforms/engineering_request_forms/create_rfi"),
            type: "POST",
            dataType: "json",
            data: formData,
            success: function (response) {
                
            }
        });
        return false;
    }
});

$('#prepared_dt').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    autoUpdateInput: false,
    minDate: moment('2023-01-01'),
    maxDate: moment().add(365, 'days'),
    locale: {
        format: 'MMM DD, YYYY',
        cancelLabel: 'Clear'
    }
});

$('#prepared_dt').on('apply.daterangepicker', function (ev, picker) {
    $(this).val(picker.startDate.format('MMM DD, YYYY'));
});

$('#prepared_dt').on('cancel.daterangepicker', function () {
    $(this).val('');
});

$('#reply_needed').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    autoUpdateInput: false,
    minDate: moment('2023-01-01'),
    maxDate: moment().add(365, 'days'),
    locale: {
        format: 'MMM DD, YYYY',
        cancelLabel: 'Clear'
    }
});

$('#reply_needed').on('apply.daterangepicker', function (ev, picker) {
    $(this).val(picker.startDate.format('MMM DD, YYYY'));
});

$('#reply_needed').on('cancel.daterangepicker', function () {
    $(this).val('');
});