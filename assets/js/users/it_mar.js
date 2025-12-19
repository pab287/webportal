console.log(_tempContentData);
let _employee = [];
let ITMar = null;
let selectedEmpId = null;
if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.employee !== "undefined" && _tempContentData.employee.length > 0){
        _employee = _tempContentData.employee;
    }
}

$(document).ready(function () {
    ITMar = $('#itmarTable').DataTable({
        serverSide: true,
        processing: true,
        searching: false,
        ajax: {
            url: baseUrl("users/get_itmar_list"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
            },
        },
        columns: [
            { data: "id", visible: false, searchable: false },
            { data: "emp_id" },
            { data: "purpose" },
            { data: "app_name" },
            { data: "created_at" },
            { data: "updated_at" },
            { data: null },
        ],
    });
});

$("#employee").select2({
    width: "100%",
    dropdownParent: $("#newITMARModal"),
    placeholder: "Select an option",
    data: _employee,
});

$("#employee").on("select2:select", function (e) {
    const data = e.params.data;
    selectedEmpId = data.id;
    $("#position").val(data.position || "");
    $("#department").val(data.department || "");
    const locations = data.site_locations ? data.site_locations.split("|").join(", ") : "";
    $("#ass_loc").val(locations);
});

$.validate({
    form : '#new_itmar',
    lang: 'en',
    onSuccess : function(form) {
        $.ajax({
            url: siteUrl("users/save_itmar"),
            type: "POST",
            dataType: "json",
            data: {
                csrf_token: _csrf_hash, 
                emp_id: selectedEmpId,
                purpose: $('#purpose').val(),
                app_type: $('input[name="app_type"]:checked').val(),
            },
            success: function (response) {
                if(response.success){
                    $('#newITMARModal').modal('hide');
                    ITMar.ajax.reload();
                }else{
                    alert(response.message);
                }
            }
        });
    }
});