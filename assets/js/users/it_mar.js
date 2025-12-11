console.log(_tempContentData);
let _employee = [];

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.employee !== "undefined" && _tempContentData.employee.length > 0){
        _employee = _tempContentData.employee;
    }
}

$("#employee").select2({
    width: "100%",
    dropdownParent: $("#newITMARModal"),
    placeholder: "Select an option",
    data: _employee,
});

$.validate({
    form : '#new_itmar',
    lang: 'en',
    onSuccess : function(form) {
        console.log('The form is valid!');
    }
});