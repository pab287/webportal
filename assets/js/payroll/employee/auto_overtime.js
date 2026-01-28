let searchRequest = '';
let _companies = [];
let globalRequest = {};
const dropdownToggle = $(".m-dropdown__toggle.export-as");

let _statusOptions =[
    { id: ' ', text: 'All' },
    { id: '1', text: 'Active' },
    { id: '0', text: 'Inactive' },
]

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){
        _companies = _tempContentData.company;
    }
}

$("#status").select2({
    width: '100%',
    data: _statusOptions,
    placeholder: 'Select an option',
    allowClear: false,
});

$("#company").select2({
    width: '100%',
    data: _companies,
    placeholder: 'Select an option',
    allowClear: true,
}).on('select2:select', function(e){
    $("#payroll_group").empty();
    $("#employees").empty().attr('disabled', false);
}).on('select2:unselect', function(){
    $("#payroll_group").empty();
    $("#employees").empty().attr('disabled', false);
});

$("#employees").select2({
    placeholder: 'Select',
    width: '100%',
    ajax: {
        url: baseUrl("payroll/reports/select_employee"),
        dataType: "json",
        delay: 250,
        global: false,
        processResults: function (data) {
            return data;
        }
    }
});

$("#payroll_group").select2({
    placeholder: 'Select',
    width: '100%',
    ajax: {
        url: baseUrl("payroll/select_payroll_group"),
        dataType: "json",
        type: 'get',
        delay: 250,
        global: false,
        data: function (params) {
            params.company_id = $("#company").val();
            return params;
        },
        processResults: function (data) {
            return data;
        }
    }
}).on("select2:select", function (e) {
    const _this = this;
    const tempVal = $(_this).val();
    const data = e.params.data;
    let employees = [];
    if (typeof data.employees == "object" && typeof data.employees !== "undefined") { employees = data.employees; }
    if (tempVal.length > 1) {
        $.ajax({
            url: baseUrl("payroll/get_payroll_group_multiple"),
            type: "post",
            dataType: "json",
            data: { group_id: tempVal, [_csrf_token]: _csrf_hash },
            success: function (json) {
                if (json.response) {
                    console.log(json);
                    const tempData = json.data;
                    if (typeof tempData == "object" && typeof tempData !== "undefined") {
                        const tempEmployeeSelector = $("#employees");
                        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                            tempEmployeeSelector.empty();
                            $.each(tempData, function (_ii, vv) {
                                const tempOption = new Option(vv.text, vv.id, true, true);
                                tempEmployeeSelector.append(tempOption);
                            });
                            tempEmployeeSelector.prop("disabled", true);
                        }
                    }
                }
            }
        });
    } else {
        if (typeof employees == "object" && typeof employees !== "undefined") {
            const tempEmployeeSelector = $("#employees");
            if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                tempEmployeeSelector.empty();
                $.each(employees, function (_ii, vv) {
                    const tempOption = new Option(vv.text, vv.id, true, true);
                    tempEmployeeSelector.append(tempOption);
                });
                tempEmployeeSelector.prop("disabled", true);
            }
        }
    }
}).on("select2:unselect", function (e) {
    const _this = this;
    const tempValUnselected = $(_this).val();
    if (tempValUnselected.length == 0) {
        const tempEmployeeSelector = $("#employees");
        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
            tempEmployeeSelector.prop("disabled", false);
        }
    } else {
        $.ajax({
            url: baseUrl("payroll/get_payroll_group_multiple"),
            type: "post",
            dataType: "json",
            data: { group_id: tempValUnselected, [_csrf_token]: _csrf_hash },
            success: function (json) {
                if (json.response) {
                    const tempData = json.data;
                    if (typeof tempData == "object" && typeof tempData !== "undefined") {
                        const tempEmployeeSelector = $("#employees");
                        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                            tempEmployeeSelector.empty();
                            $.each(tempData, function (_ii, vv) {
                                const tempOption = new Option(vv.text, vv.id, true, true);
                                tempEmployeeSelector.append(tempOption);
                            });
                            tempEmployeeSelector.prop("disabled", true);
                        }
                    }
                }
            }
        });

    }

});

const dtTable = $('#tbl-employee-auto-overtime').DataTable({
    dom: "<'row justify-content-between mb-3'<'col-xl-4 col-lg-4 col-md-4 col-sm-12 exportDropdown'><'col-xl-4 col-lg-4 col-md-4 col-sm-12 exportSearch'f>>" +
    "<'row'<'col-12'liprt>>" +
    "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'><'col-xl-6 col-lg-6 col-md-6 col-sm-12'>>",
    processing: true,
    searching: false,
    serverSide: true,
    ordering: false,
    rowId: 'employee_id',
    buttons: [
        {
            extend: 'excelHtml5',
            title: 'CRS - APPLICANT REPORT',
            action: function (e, dt, button, config) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
            }
        },
        {
            extend: 'pdfHtml5',
            title: 'CRS - APPLICANT REPORT',
            action: function (e, dt, node, config) {
            }
        },
    ],
    ajax: {
        url: baseUrl('payroll/employee/get_employee_auto_overtime_list'),
        type: 'POST',
        dataType: 'JSON',
        global: false,
        data: function (d) { 
            d.csrf_token = _csrf_hash;
            d.search['value'] = searchRequest;
            d.params = globalRequest;
            return d;
        }
    },
    columns: [
        { data: 'idno', title: "ID No" },
        { data: 'employee_name', title: "Employee Name",
            render: function (data, _type, row) { 
                return `<p class="mb-0">${data}</p>
                <p class="mb-0"><small class="m--font-bolder">${row.position}</small></p>`; 
            }
        },
        { data: 'company_code', title: "Company", width: '15%' },
        { data: 'last_updated_at', title: "Last Updated By", 
            render: function (data, _type, row) {
                const recordDate = data ? moment(data).format("LLL") : "";
                const _html = recordDate ? `<p class="m--font-bolder mb-0">${row.last_updated_by}</p><p class=" mb-0"><small>${recordDate}</small></p>` : `---`;
                return _html;
        }}, { data: 'allow_auto_overtime', title: "Auto Overtime", className: 'text-center', width: '10%',
            render: function (data) { return parseInt(data) === 1 ? "<i class='fa fa-check-circle text-success m--icon-font-size-lg3'></i>" : "<i class='fa fa-times-circle text-danger m--icon-font-size-lg3'></i>" } 
        }, { data: null,    
            title: `
            Action 
                <button type='button' 
                    id='btnMassToggle' 
                    class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnEdit'
                    data-placement='bottom' data-toggle='m-tooltip' title='' 
                    data-original-title='Mass Toggle Auto Overtime'>
                    <i class='fa fa-toggle-off'></i>
                </button>
            `, 
            className: 'text-center', width: '7%',
            render: function (_data, _type, row) {
                let actionCtr = 0;
                let _actionButton = "";
                const tempIcon = parseInt(row.allow_auto_overtime) === 1 ? "fa-toggle-on" : "fa-toggle-off";
                const tempTooltip = parseInt(row.allow_auto_overtime) === 1 ? "Deactivate  Auto Overtime" : "Activate Auto Overtime";
                const tempState = parseInt(row.allow_auto_overtime) === 1 ? "danger" : "success";

                const rawData = JSON.stringify(row);
                if (typeof _currentActions != "undefined" && _currentActions.length > 0 && jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton = `<button type='button' 
                    class='btn btn-default m-btn m-btn--hover-${tempState} m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnAutoOvertime' 
                    data-placement='bottom' data-toggle='m-tooltip' title='' 
                    data-original-title='${tempTooltip}' data-row='${rawData}'>
                        <i class='fa ${tempIcon}'></i>
                    </button>`;
                    actionCtr++;
                }
                if(actionCtr == 0) { _actionButton = '---'; }
                return _actionButton;
        }}
    ],
    drawCallback: function () {

        const table = $('#tbl-employee-auto-overtime').DataTable();
        const data = table.rows({ search: 'applied' }).data().toArray();
    
        const $btn = $('#btnMassToggle');
        const $icon = $btn.find('i');
    
        const enabledCount = data.filter(row => parseInt(row.allow_auto_overtime) === 1).length;
        const disabledCount = data.filter(row => parseInt(row.allow_auto_overtime) === 0).length;
    
        if (enabledCount === data.length) {
            $icon.removeClass('fa-toggle-off').addClass('fa-toggle-on');
            $btn.removeClass('m-btn--hover-success')
                .addClass('m-btn--hover-danger')
                .attr('data-original-title', 'Deactivate Auto Overtime');
        } 
        else if (disabledCount === data.length) {
            $icon.removeClass('fa-toggle-on').addClass('fa-toggle-off');
            $btn.removeClass('m-btn--hover-danger')
                .addClass('m-btn--hover-success')
                .attr('data-original-title', 'Activate Auto Overtime');
        } 
    },
    initComplete: function () {
        const dropdown = '' +
        '       <div class="m-dropdown m-dropdown--inline m-dropdown--align-left" ' +
        '             data-dropdown-toggle="hover" aria-expanded="true">' +
        '            <button class="m-dropdown__toggle btn btn-success dropdown-toggle export-as">' +
        '                EXPORT AS' +
        '            </button>' +
        '            <div class="m-dropdown__wrapper">' +
        '                <div class="m-dropdown__inner">' +
        '                    <div class="m-dropdown__body">' +
        '                        <div class="m-dropdown__content">' +
        '                            <ul class="m-nav">' +
        '                                <li class="m-nav__item">' +
        '                                    <a id="export-as-excel" style="cursor:pointer;" ' +
        '                                       onclick="exportAs(\'excel\'); return false;" class="m-nav__link">' +
        '                                        <i class="m-nav__link-icon fa fa-file-excel-o m--font-success"></i>' +
        '                                        <span class="m-nav__link-text" style="text-transform: none;">' +
        '                                           Excel File' +
        '                                        </span>' +
        '                                    </a>' +
        '                                </li>' +
        '                                <li class="m-nav__item">' +
        '                                    <a id="export-as-pdf" style="cursor:pointer;" ' +
        '                                       onclick="exportAs(\'pdf\'); return false;" class="m-nav__link">' +
        '                                        <i class="m-nav__link-icon fa fa-file-pdf-o m--font-danger"></i>' +
        '                                        <span class="m-nav__link-text" style="text-transform: none;">' +
        '                                          PDF File' +
        '                                        </span>' +
        '                                    </a>' +
        '                                </li>' +
        '                            </ul>' +
        '                        </div>' +
        '                    </div>' +
        '                </div>' +
        '            </div>' +
        '        </div>';

        $(dropdown).appendTo("#tbl-employee-auto-overtime_wrapper .exportDropdown");
        dropdownEl = $(".m-dropdown__toggle.export-as");
        const filterDiv = $('<div>').addClass('dataTables_filter');
        const searchInput = $('<input>')
            .attr('type', 'text')
            .addClass('form-control')
            .attr('placeholder', 'Search...')
            .attr('id', 'generalSearch');
        filterDiv.append(searchInput);
        $(filterDiv).appendTo("#tbl-employee-auto-overtime_wrapper .exportSearch");
        $('#generalSearch').donetyping(function (_callback) {
            searchRequest = $(this).val();
            dtTable.ajax.reload();
        });
        $('#tbl-employee-auto-overtime_paginate').css('padding-top', '0');
    },

});

$(document).on("click", ".btnAutoOvertime", function () {
    const rowData = $(this).data("row");
    const { id, allow_auto_overtime, employee_name, employee_id } = rowData;
    const state = parseInt(allow_auto_overtime) === 1 ? 'Deactivate' : 'Activate';
    const tempState = parseInt(allow_auto_overtime) === 1 ? 'danger' : 'success';
    Swal.fire({
        title: state + ' Auto Overtime?',
        html: "Are you sure you want to <strong class='text-"+ tempState +"'>`"+ state.toUpperCase() +"`</strong> Auto Overtime for <strong class='text-primary'>`"+ employee_name.toUpperCase() +"`</strong>?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes '+ state + ' it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: siteUrl("payroll/employee/update_auto_overtime_status"),
                type: "post",
                dataType: "json",
                data: {
                    csrf_token: _csrf_hash,
                    id: id,
                    allow_auto_overtime: allow_auto_overtime,
                    employee_id: employee_id
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(json.toastr_msg, "Employee Auto Overtime", 5000);
                        const scrollPos = $(window).scrollTop();
                        dtTable.ajax.reload(() => {
                            $(window).scrollTop(scrollPos);
                        }, false);
                    }
                }
            });
        }
    });
});

const resetFilter = function (event) {
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        const select2 = form.find("#employees, #payroll_group, #company, #status");
        if (typeof select2 !== "undefined" && select2.length > 0) {
            $.each(select2, function (i, v) {
                const multi = $(v)[0].multiple;
                if (multi) {
                    $(v).val([]).trigger("change").prop("disabled", false);
                } else {
                    $(v).val("").trigger("change");
                }
            });
        }
    }
    globalRequest = {};
    dtTable.ajax.reload();
}

$.validate({
    form: "#frm-filter-payroll-auto_overtime",
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const currentForm = form[0];
        let formData = $(currentForm).serialize();
        const empSerialized = $(currentForm).find("#employees").serialize();
        
        const emptyEmployeeList = empSerialized != "";
        if (emptyEmployeeList === false && $(currentForm).find("#employees").val().length > 0) {
            formData += '&serialized_employees=' + $(currentForm).find("#employees").val().toString();
        }
        let nData = {};
        formData.split('&').forEach(function(item) {
            const part = item.split('=');
            const key = decodeURIComponent(part[0]);
            const value = decodeURIComponent(part[1] || '');

            if(key.endsWith("[]")) {
                const cleanKey = key.slice(0, -2);
                if(typeof nData[cleanKey] !== "undefined") { nData[cleanKey].push(value); } 
                else { nData[cleanKey] = [value]; }
            }else{ nData[key] = value; }
        });

        globalRequest = { ...nData };
        const scrollPos = $(window).scrollTop();
        dtTable.ajax.reload(() => {
            $(window).scrollTop(scrollPos);
        }, false);
        return false;
    }
});

$('#btnMassToggle').on('click', function () {
    const $btn = $(this);
    const $icon = $btn.find('i');
    const isActive = $icon.hasClass('fa-toggle-on');
    const actionText = isActive ? 'Deactivate' : 'Activate';
    const confirmColor = isActive ? '#d33' : '#28a745';

    $btn.tooltip('hide');
    $btn.blur();

    Swal.fire({
        title: `${actionText} Auto Overtime`,
        text: `This will ${actionText.toLowerCase()} auto overtime for listed employee.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Yes, ${actionText}`,
        cancelButtonText: 'Cancel',
        confirmButtonColor: confirmColor,
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            if (isActive) {
                $icon.removeClass('fa-toggle-on').addClass('fa-toggle-off');
                $btn
                    .removeClass('m-btn--hover-danger')
                    .addClass('m-btn--hover-success')
                    .attr('data-original-title', 'Activate Auto Overtime');
            } else {
                $icon.removeClass('fa-toggle-off').addClass('fa-toggle-on');
                $btn
                    .removeClass('m-btn--hover-success')
                    .addClass('m-btn--hover-danger')
                    .attr('data-original-title', 'Deactivate Auto Overtime');
            }
            $btn.tooltip('hide');
            const table = $('#tbl-employee-auto-overtime').DataTable();
            const targetState = isActive ? 0 : 1;
            const ids = [];
            table.rows().every(function () {
                const rowData = this.data();
                if (parseInt(rowData.allow_auto_overtime) !== targetState) {
                    ids.push(rowData.employee_id);
                }
            });
            if (ids.length > 0) {
                $.ajax({
                    url: siteUrl("payroll/employee/mass_update_auto_overtime_status"),
                    type: "post",
                    dataType: "json",
                    global: false,
                    data: { employee_ids: ids, status: targetState, csrf_token: _csrf_hash },
                    success: function (res) {
                        const scrollPos = $(window).scrollTop();
                        dtTable.ajax.reload(() => {
                            $(window).scrollTop(scrollPos);
                        }, false);
                    }
                });
            }
            else{
                toastr.error("No employees to update.");
            }
        }
    });
});

function exportAs(type) {
    dropdownToggle.addClass("m-btn--custom m-loader m-loader--light m-loader--left");
    let clearHere = false;

    setTimeout(() => {
        switch (type) {
            case "excel":
                dtTable.button(".buttons-excel").trigger();
                break;
            case "pdf":
                dtTable.button(".buttons-pdf").trigger();
                break;
            case "print":
                dtTable.button(".buttons-print").trigger();
                break;
        }

        if (clearHere) {
            dropdownToggle.removeClass("m-btn--custom m-loader m-loader--light m-loader--left");
        }
    }, 150);
}