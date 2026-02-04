$(document).ready(function () {
});

let _employee = null;
let _projects = null;
if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.employee !== "undefined" && _tempContentData.employee.length > 0){
        _employee = _tempContentData.employee;
    }
    if(typeof _tempContentData.projects !== "undefined" && _tempContentData.projects.length > 0){
        _projects = _tempContentData.projects;
    }
}

let rfiTable = null;
let rfaTable = null;
let is_archive = 0;
let selectedId = null;

$('#project_name').select2({
    dropdownParent: $('#newRFIModal'),
    width: '100%',
    placeholder: 'Select project',
    allowClear: true,
    data: _projects
});

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
            d.is_archive = is_archive;
        }
    }
}); 

$("#project_name").on("select2:select", function (e) {
    const data = e.params.data;
    selectedEmpId = data.id || null;
    console.log("SELECTED EMP ID", selectedEmpId);
}).on("change", function () {

});
