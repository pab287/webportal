let _companies = [], _station = [];
const cbSelectAll = $("#cb-select-all");
const _dtTableNoStation = $("#table-employee-no_station");
let globalStationText = null;
let globalCompanyId = 0;

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){ _companies = _tempContentData.company; }
    if(typeof _tempContentData.station !== "undefined" && _tempContentData.station.length > 0){ _station = _tempContentData.station; }
}

const companySelect2 = $("#company").select2({
    width: '100%',
    data: _companies,
    placeholder: 'Select an option',
    allowClear: true,
});

$("select#station").select2({
    width: "100%",
    placeholder: "Select an option",
    data: _station,
    allowClear: true,
}).on("select2:select", function(e){
    const { text } = e.params.data;
    globalStationText = text;
});

const dtTable = _dtTableNoStation.DataTable({
    dom: 'frtlip',
    searching: true,
    ordering: false,
    columns: [ 
        {
            data: "id",
                width: "3%",
                orderable: false,
                className: "text-center",
                render: function (data) {
                    return `<label class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                        <input type="checkbox" name="employee_id[]" value="${data}" /><span></span>
                    </label>`;
                }
        },
        { data: "employee_name", width: "*" }, 
        { data: "position", width: "35%" },
        { data: "department", width: "25%" }, 
    ],
    drawCallback: function(){
        $(".dataTables_filter").find("input[type=search]").removeClass("form-control-sm");
    }
});

function checkCbSelectAll() {
    const cbCount = $('tbody input[type=\'checkbox\']', _dtTableNoStation).length;
    const checkedCbCount = $('tbody input[type=\'checkbox\']:checked', _dtTableNoStation).length;
    cbSelectAll.prop('checked', (parseInt(cbCount) === parseInt(checkedCbCount) && parseInt(checkedCbCount) >= 1));
}

cbSelectAll.on('change', function (e) {
    const checkedValue = e.target.checked;
    $('tbody input[type=\'checkbox\']', _dtTableNoStation).prop('checked', checkedValue);
    dtTable.draw();
});

_dtTableNoStation.on('change', 'tbody input[type=\'checkbox\']', function () { checkCbSelectAll(); });

$.validate({
    form: '#frm-filter-hris-station',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        var currentForm = form[0];
        var formMethod = currentForm.method;
        var formData = $(currentForm).serialize();

        $.ajax({
            url: baseUrl("hris/reports/get_employee_no_stations"),
            type: formMethod,
            dataType: "json",
            data: formData,
            beforeSend: function () {
                $(currentForm)
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                globalCompanyId = 0;

                if(json.response){
                    globalCompanyId = json.id;

                    dtTable.clear();
                    dtTable.rows.add(json.rows);
                    dtTable.draw(false);
                }
                $(currentForm)
                    .find(".btn-submit")
                    .removeClass("m-btn--custom m-loader m-loader--light m-loader--right")
                    .prop("disabled", false);
            }
        });
        
        return false;
    }
});

$.validate({
    form: '#frm-assign-station',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        var currentForm = form[0];
        var formMethod = currentForm.method;
        var formData = $(currentForm).serialize();

        const checkedCbCount = $('tbody input[type=\'checkbox\']:checked', _dtTableNoStation).length;
        if(checkedCbCount > 0){
            const checkedInput = $('tbody input[type=\'checkbox\']:checked', _dtTableNoStation);
            var checkedData = $(checkedInput).serialize();            
            formData += `&${checkedData}&company=${globalCompanyId}`;

            Swal.fire({
                title: 'Station Assignment?',
                html: "Are you sure you want to assign this employee/s to this station <strong class='m--font-warning'>`"+globalStationText.toUpperCase()+"`</strong>?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
              }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: baseUrl("hris/reports/set_employees_without_stations"),
                        type: formMethod,
                        dataType: "json",
                        data: formData,
                        beforeSend: function () {
                            $(currentForm)
                                .find(".btn-submit")
                                .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        },
                        success: function (json) {
                            if(json.response){
                                dtTable.clear();
                                dtTable.rows.add(json.rows);
                                dtTable.draw(false);

                                toastr.success('Employee/s assigned to `'+globalStationText.toUpperCase()+'` has been added successfully.', 'Station Assignment');
                            }else{
                                toastr.error('Failed to assign employee/s in `'+globalStationText.toUpperCase()+'` station!', 'Station Assignment');
                            }

                            $(currentForm)
                                .find(".btn-submit")
                                .removeClass("m-btn--custom m-loader m-loader--light m-loader--right")
                                .prop("disabled", false);
                        }
                    });
                }
              });

        }

        return false;
    }
});