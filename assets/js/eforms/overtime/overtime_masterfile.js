let search_val = "";
let query_builder = "";
let filteredIds = [];
let enabledFilter = false;
let param_status = "";
let isMass = false;

const getUrlParameter = function getUrlParameter(sParam) {
    const sPageURL = decodeURIComponent(window.location.search.substring(1));
    const sURLVariables = sPageURL.split('&');
    let sParameterName, i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

if(typeof getUrlParameter('status') !== 'undefined'){
    param_status = getUrlParameter('status');
}

const hasPrev = (jQuery.inArray("mass_update", _currentActions) !== -1 || jQuery.inArray("mass_approve", _currentActions) !== -1);

const tblOvertime = $("#table-overtime").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/overtime/overtime_masterfile"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
            d.query_builder = query_builder;
            d.ids = filteredIds;
            d.filtered = enabledFilter;
            d.status = param_status;
        }
    },
    searching: false,
    columns: [
        { data: "id", width: "3%", orderable: false, visible: hasPrev,
            render: function(data, type, row, meta){
                let html = "";
                if(row.status == 'Pending'){
                    html =  `<label class="m-checkbox m-checkbox--air m-checkbox--state-success"> ` +
                                `<input type="checkbox" class="selectedOvertime" id="selectedOvertime" value="${data}" name="selectedItem[]" reference_no="${row.reference_no}"><span></span>` + 
                            `</label>`;
                }else if(row.status == 'Disapproved'){
                    html =  `<label class="m-checkbox m-checkbox--air m-checkbox--state-danger"> ` +
                                `<input type="checkbox" disabled readonly><span></span>` + 
                            `</label>`;
                }else{
                    html =  `<label class="m-checkbox m-checkbox--air m-checkbox--state-success"> ` +
                                `<input type="checkbox" checked disabled readonly><span></span>` + 
                            `</label>`;
                }
                return html;
            }
        },
        { data: "status", width: "10%", render: function (data) { return statusBg(data) } },
        { data: "reference_no", width: "10%" },
        {
            data: "firstname", width: "20%", render: function (data, type, row, meta) {
                let tempHtml = "<p class='m--marginless'>" + row.display_employee + "</p>";
                tempHtml += row.display_details;
                return tempHtml;
            }
        },
        { data: "purpose" },
        { data: "date_from", width: "10%", className: "text-justify", render: function (data) { return formatTime(data) } },
        { data: "date_to", width: "10%", className: "text-justify", render: function (data) { return formatTime(data) } },
        { data: "duration", width: "8%", className: "text-center", orderable: false },
        { data: null, width: "8%", className: "text-center" },
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) { return itemDatatableActions(row.id); },
        }
    ], buttons: [
        {
            extend: 'csv',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, {
            extend: 'excel',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, {
            extend: 'pdf',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }
    ],
});

$("#ExportExcel").on("click", function (e) {
    e.preventDefault();
    tblOvertime.button('.buttons-excel').trigger();
    $.ajax({
        url: baseUrl("eforms/overtime/export_event_log/1"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val,
            query_builder : query_builder
        }
    });
});

$("#ExportCSV").on("click", function (e) {
    e.preventDefault();
    tblOvertime.button('.buttons-csv').trigger();
    $.ajax({
        url: baseUrl("eforms/overtime/export_event_log/2"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val,
            query_builder : query_builder
        }
    });
});

$("#ExportPDF").on("click", function (e) {
    e.preventDefault();
    tblOvertime.button('.buttons-pdf').trigger();
    $.ajax({
        url: baseUrl("eforms/overtime/export_event_log/3"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val,
            query_builder : query_builder
        }
    });
});

function statusBg(status) {
    let tempState = "";
    switch (status) {
        case "Pending":
            tempState = '<div class="m-badge m-badge--warning text-white m-badge--wide" role="alert"><strong>Pending</strong></div>';
            break;
        case "Approved":
            tempState = '<div class="m-badge m-badge--success text-white m-badge--wide" role="alert"><strong>Approved</strong></div>';
            break;
        case "Disapproved":
            tempState = '<div class="m-badge m-badge--danger text-white m-badge--wide" role="alert"><strong>Disapproved</strong></div>';
            break;
        default:
            tempState = '<div class="m-badge m-badge--metal text-white m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
            break;
    }
    return tempState;
}

function formatTime(time) {
    return (time == "0000-00-00 00:00:00") ? "" : moment(time).format("LLL");
}

function itemDatatableActions($id) {
    if ($id) {
        let _actionButton = "";
        _actionButton += "<a href='view_overtime?id=" + $id + "' target='__blank'><button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit' data-toggle='m-tooltip' data-original-title='View Details' data-placement='bottom' data-delay='{\"show\": 300}'><i class='la la-pencil-square'></i></button>";
        return _actionButton;
    } else { return false; }
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblOvertime.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblOvertime.ajax.reload();
});

$(document).ready(function () {
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': { delay: 100 },
        filters: [
            // { id: 'a.id', label: 'ID #', type: 'integer' },
            { id: 'b.firstname', label: 'Firstname', type: 'string' },
            { id: 'b.middlename', label: 'Middlename', type: 'string' },
            { id: 'b.lastname', label: 'Lastname', type: 'string' },
            { id: 'b.suffix', label: 'Suffix', type: 'string' },
            {
                id: 'a.status',
                label: 'Status',
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select an option',
                    data: [
                        {
                            id: "Pending",
                            text: "Pending"
                        }, {
                            id: "Approved",
                            text: "Approved"
                        }, {
                            id: "Disapproved",
                            text: "Disapproved"
                        }
                    ],
                    width: "120%",
                    dropdownParent: $("#modal-query-builder"),
                },
                operators: ['equal', 'not_equal']
            },
            { id: 'a.purpose', label: 'Purpose', type: 'string' },
            {
                id: 'a.created_at',
                label: 'Date Created',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: { format: 'yyyy-mm-dd' },
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
            }, {
                id: 'a.approved_at',
                label: 'Date Approved',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: { format: 'yyyy-mm-dd' },
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
            },
        ],
    });

    $("#query-builder_group_0").addClass("col-12");
    uploadOvertimeCsvFile();

    $(document).on("click","#cb-select-all",function(){
        var isChecked = $(this).is(':checked');
        $(".selectedOvertime").each(function(){
            $(this).prop('checked', isChecked);
        })
        isMass = $('.selectedOvertime:checked').length > 0;
    })
    $(document).on("click",".selectedOvertime",function(){
        if ( $('.selectedOvertime:checked').length !== $('.selectedOvertime').length) {
            var isChecked = $("#cb-select-all").is(':checked');
            $("#cb-select-all").prop('checked', false);
        }else{
            $("#cb-select-all").prop('checked', true);
        }
        isMass = $('.selectedOvertime:checked').length > 0;
    })
});

$('#query-builder-btn').on('click', function () {
    var result = $('#query-builder').queryBuilder('getSQL');

    if (!$.isEmptyObject(result)) {
        enabledFilter = true;
        query_builder = result;
        tblOvertime.ajax.reload(function () {
            enabledFilter = false;
            query_builder = null;
        }, false);
        $("#modal-query-builder").modal("hide");
    }
});

function clear_query_builder() {
    $('#query-builder').queryBuilder('reset');
    query_builder = null;
    tblOvertime.ajax.reload(function () {
        enabledFilter = false;
    }, false);
}

$('#refresh').click(function() {
  enabledFilter = false;
  filteredIds = [];
  tblOvertime.ajax.reload();
});

// $("#frm-advance-search").on("submit", function (e) {
//     e.preventDefault();
//     var tempUrl = e.target.action;
//     var tempType = e.target.method;

//     $.ajax({
//         url: tempUrl,
//         type: tempType,
//         dataType: "json",
//         data: $(e.target).serialize(),
//         success: function (json) {
//             if (json.response) {
//                 filteredIds = json.ids;
//                 enabledFilter = true;
//                 $("#modal-advance-search").modal('hide');
//                 tblOvertime.ajax.reload();
//             }
//             if (json.has_error) { 
//               console.log('Error');
//               toastr.error(json.toastr_msg, "Filtered Search", { timeOut: 5000 }); }
//         }
//     });
// });

$.validate({
    form: "#frm-advance-search",
    lang: "en",
    onSuccess: function (form) {
        var currentForm = form[0];
        var formUrl = currentForm.action;
        var formData = $(currentForm).serialize();
        var currentModal = $(currentForm).closest(".modal");

        $.ajax({
            url: formUrl,
            type: 'post',
            dataType: "json",
            data: formData,
            success: function (json) {
                if (json.response) {
                    filteredIds = json.ids;
                    enabledFilter = true;
                    if (typeof currentModal !== "undefined") { currentModal.modal("hide"); }
                    tblOvertime.ajax.reload();

                    $(currentForm).trigger('reset');
                }
                if (json.has_error) { 
                    console.log('Error');
                    toastr.error(json.toastr_msg, "Filtered Search", { timeOut: 5000 }); }
                }
        });

        return false;
    }
})

$("#employee").select2({
  placeholder: 'Select an option',
  width: '100%',
  dropdownParent: $("#modal-advance-search"),
  minimumInputLength: 3,
    ajax: {
      url: baseUrl("eforms/overtime/get_employee"),
      dataType: "json",
      delay: 250,
      global: false,
      data: function (params) {
        return {
          company: $("#company").val(),
          q: params.term
        };
      },
      processResults: function (data) {
          return data;
      }
}
});

$("#company").select2({
  placeholder: 'Select an option',
  width: '100%',
  dropdownParent: $("#modal-advance-search"),
  minimumInputLength: 3,
  ajax: {
      url: baseUrl("eforms/overtime/get_company"),
      dataType: "json",
      delay: 250,
      global: false,
      processResults: function (data) {
          return data;
      }
  }
});



$("#status").select2({
    placeholder: 'Select an option',
    width: '100%',
    dropdownParent: $("#modal-advance-search"),
});

var startDate = moment().startOf('week');
var endDate = moment().endOf('week');

$("#date_time").daterangepicker({
  startDate: startDate,
  endDate: endDate,
  locale: {
    format: 'MM/DD/YYYY',
    cancelLabel: 'Clear'
  },
  autoUpdateInput: false
});

$("#date_time").on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));

    var self = $(ev.target);
    self.validate();
});

$("#date_time").on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('').trigger('change');
});

$("#modal-advance-search").on("hidden.bs.modal", function () {
  $('#status').val(null).trigger('change');
  $('#company').val(null).trigger('change');
  $('#employee').val(null).trigger('change');

  $("#frm-advance-search").trigger('reset');
})

$(document).on('shown.bs.modal', '#modal-import-overtime', function (e) {
    var tempTable = vmTempUploadedContent.current_table;
    if (typeof tempTable !== "undefined" && typeof tempTable == "object" && tempTable !== null) {
        tempTable.clear().destroy();
    }
    vmTempUploadedContent.rows = {};
    vmTempUploadedContent.count = 0;
    vmTempUploadedContent.json_file = null;
    vmTempUploadedContent.current_table = null;
    vmTempUploadedContent.has_uploaded_file = false;
    vmTempUploadedContent.employee_records = [];

});

$(document).on('hidden.bs.modal', '#modal-import-overtime', function (e) {
    var tempTable = vmTempUploadedContent.current_table;
    if (typeof tempTable !== "undefined" && typeof tempTable == "object" && tempTable !== null) {
        tempTable.clear().destroy();
    }
    vmTempUploadedContent.rows = {};
    vmTempUploadedContent.count = 0;
    vmTempUploadedContent.json_file = null;
    vmTempUploadedContent.current_table = null;
    vmTempUploadedContent.has_uploaded_file = false;
    vmTempUploadedContent.employee_records = [];

});

const uploadOvertimeCsvFile = function () {
    const url = baseUrl("eforms/overtime/temp_upload_csv_file");
    $("#import_csv")
        .fileupload({
            url: url,
            dataType: "json",
            formData: { csrf_token: _csrf_hash },
            done: function (e, data) {
                const result = data.result;
                vmTempUploadedContent.employee_records = [];
                if (result.response) {
                    vmTempUploadedContent.has_uploaded_file = true;
                    vmTempUploadedContent.json = result.added_json_file;
                    vmTempUploadedContent.json_file = result.json_file;
                    vmTempUploadedContent.invalid_ctr = result.invalid_ctr;
                    vmTempUploadedContent.valid_ctr = result.valid_ctr;
                    
                    toastr.success(result.toastr_msg, "Upload File", 5000);
                    setTimeout(function () {
                        vmTempUploadedContent.generateDataTable();
                    }, 500);

                    if(result.biometric_not_found){
                        setTimeout(function(){
                            Swal.fire({
                                title: 'Biometric Number!',
                                html: "<strong class='m--font-danger'>Biometric # Not Found!</strong> "+ result.biometric_not_found,
                                icon: 'warning',
                            });
                        }, 1000);
                    }
                } else {
                    if (typeof result.employee_record !== "undefined" && typeof result.employee_record == "object" && result.employee_record.length > 0) {
                        vmTempUploadedContent.employee_records = result.employee_record;
                    }
                    vmTempUploadedContent.has_uploaded_file = false;
                    vmTempUploadedContent.json = null;
                    toastr[result.toastr_state](result.toastr_msg, "Upload File", 5000);
                }
            },
            progressall: function (e, data) {
                $("#progress_uploaded_csv")
                    .addClass("m--margin-top-10")
                    .show();
                let progress = parseInt((data.loaded / data.total) * 100, 10);
                let progressTotal = 0;

                let steps = setInterval(function () {
                    progressTotal += 10;
                    $("#progress_uploaded_csv .progress-bar").css("width", progressTotal + "%");
                    if (progressTotal == 100) {
                        clearInterval(steps);
                        progressTotal = 0;
                        setTimeout(function () {
                            $("#progress_uploaded_csv .progress-bar").css("width", progressTotal + "%");
                        }, 1500);
                    }
                }, 10);

                if (progress == 100) {
                    setTimeout(function () {
                        $("#progress_uploaded_csv")
                            .removeClass("m--margin-top-10")
                            .hide();
                    }, 1000);
                }
            }
        })
        .prop("disabled", !$.support.fileInput)
        .parent()
        .addClass($.support.fileInput ? undefined : "disabled");
};

const approveModalFileUpload = function () {
    const url = baseUrl("eforms/overtime/temp_upload_file");
    $("#temp_fileupload")
        .fileupload({
            url: url,
            dataType: "json",
            formData: { csrf_token: _csrf_hash },
            done: function (e, data) {
                const result = data.result;
                if (result.response) {
                    toastr.success(result.toastr_msg, "Upload File", 5000);
                } else {
                    toastr.error(result.toastr_msg, "Upload File", 5000);
                }
            },
            progressall: function (e, data) {
                $("#progress_approve").show();
                const progress = parseInt((data.loaded / data.total) * 100, 10);
                let progressTotal = 0;
                let steps = setInterval(function () {
                    progressTotal += 10;
                    $("#progress_approve .progress-bar").css("width", progressTotal + "%");
                    if (progressTotal == 100) {
                        clearInterval(steps);
                        progressTotal = 0;
                        setTimeout(function () {
                            $("#progress_approve .progress-bar").css("width", progressTotal + "%");
                        }, 1500);
                    }
                }, 10);

                if (progress == 100) {
                    setTimeout(function () {
                        $("#progress_approve").hide();
                        getCurrentUploadFiles();
                    }, 1000);
                }
            }
        })
        .prop("disabled", !$.support.fileInput)
        .parent()
        .addClass($.support.fileInput ? undefined : "disabled");
};

$.formUtils.addValidator({
    name: 'checkbox_group_min1',
    validatorFunction: function (value, $el, config, language, $form) {
        return parseInt(value) > 0;
    },
    errorMessage: 'Select at least 1 image option!',
    errorMessageKey: 'checkboxMinimumOne'
});

$.validate({
    form: "#frm-import_overtime",
    lang: "en",
    validateHiddenInputs: true,
    onSuccess: function (form) {
        const currentForm = form[0];
        const formUrl = currentForm.action;
        const formData = $(currentForm).serialize();
        const currentModal = $(currentForm).closest(".modal");

        Swal.fire({
            icon: 'question',
            title: 'Overtime Summary',
            text: 'Would you like to print the overtime summary?',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                
            } else {
            }
            if (typeof currentModal !== "undefined") { currentModal.modal("hide"); }
        });
        // if(vmTempUploadedContent.count > 0){
        //     $.ajax({
        //         url: formUrl,
        //         type: "post",
        //         data: formData,
        //         dataType: "json",
        //         beforeSend: function () {
        //             $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        //         },
        //         success: function (json) {
        //             if (json.response) {
        //                 toastr.success(json.toastr_msg, "Import Overtime");
        //                 tblOvertime.ajax.reload();

        //                 Swal.fire({
        //                     icon: 'question',
        //                     title: 'Overtime Summary',
        //                     text: 'Would you like to print the overtime summary?',
        //                     showCancelButton: true,
        //                     confirmButtonColor: '#3085d6',
        //                     cancelButtonColor: '#d33',
        //                     confirmButtonText: 'Yes'
        //                 }).then((result) => {
        //                     if (result.isConfirmed) {

        //                     } else {
        //                     }
        //                     if (typeof currentModal !== "undefined") { currentModal.modal("hide"); }
        //                 });
        //             } else {
        //                 toastr.error(json.toastr_msg, "Import Overtime");
        //             }
        //         }
        //     });
        // }else{
        //     toastr.error('Please Upload Attachment Image First.', "Import Overtime");
        // }

        return false;
    }
});

const vmTempUploadedContent = new Vue({
    el: "#temp-uploaded_content",
    data: {
        has_uploaded_file: false,
        json_file: null,
        json: null,
        current_table: null,
        rows: {},
        count: 0,
        employee_records: [],
        invalid_ctr: 0,
        valid_ctr: 0,
    },
    methods: {
        generateDataTable: function () {
            const _this = this;
            const currentElement = _this.$el;
            const currentModal = $(currentElement).closest(".modal");
            const currentTable = $(currentElement).find("#uploaded_csv_table");
            const currentSelect2 = $(currentElement).find("#approved_by");

            if (typeof currentTable !== "undefined") {

                if (typeof _this.current_table !== 'undefined' && _this.current_table) {
                    currentTable.DataTable().clear().destroy();
                }

                _this.current_table = currentTable.DataTable({
                    destroy: true,
                    dom: "lftp",
                    serverSide: false,
                    processing: false,
                    ajax: _this.json,
                    scrollY: 450,
                    scrollCollapse: true,
                    paging: false,
                    ordering: false,
                    retrieve: true,
                    columns: [
                        { data: "is_valid", title: "", width: "3%", render: function (data) {
                            return data === true ? `<i class="fa fa-check text-success"></i>` : `<i class="fa fa-times text-danger"></i>`;
                        }},
                        { data: "display_name", title: "Employee Name", width: "25%", render: function (data, _type, row, _meta) {
                            const labelClass = row.is_valid === true ? "text-success" : "text-danger";
                            const tempState = row.is_valid === true ? "" : `<span class="ml-3 m-badge m-badge--warning m-badge--wide">Invalid</span>`;
                            const tempHtml = `<div class='${labelClass} m--font-boldest'><p class='mb-0 m--font-bolder'>${data}</p><p>${row.biometricno}${tempState}</p></div>`;
                            return tempHtml;
                        }},
                        {
                            data: "date_from", title: "Date From", width: "12%", render: function (data, type, row) {
                                const isInvalid = row.is_valid === true ? '' : 'font-weight: 600';
                                return `<span style="${isInvalid}">` + moment(data).format("YYYY-MM-DD HH:mm") + `</span>`;
                            }
                        },
                        {
                            data: "date_to", title: "Date To", width: "12%", render: function (data, type, row) {
                                const isInvalid = row.is_valid === true ? '' : 'font-weight: 600';
                                return  `<span style="${isInvalid}">` + moment(data).format("YYYY-MM-DD HH:mm") + `</span>`;
                            }
                        },
                        {
                            data: "approved_date", title: "Approved Date", className: "text-center", width: "14%", render: function (data, type, row) {
                                const isInvalid = row.is_valid === true ? '' : 'font-weight: 600';
                                return `<span style="${isInvalid}">` + moment(data).format("YYYY-MM-DD") + `</span>`;
                            }
                        },
                        { data: "purpose", title: "Purpose", width: "*",
                            render: function(data, type, row) {
                                const isInvalid = row.is_valid === true ? '' : 'font-weight: 600';
                                return `<span style="${isInvalid}">` + data + `</span>`;
                            }
                        },
                        {
                            data: null, title: 'Remarks', width: '20%',
                            render: function(data, type, row) {
                                let html = ``;

                                if (row.is_valid) {
                                    return ' --- ';
                                } else {
                                    html += `<div style="font-size: 13px !important">`;
                                        html += `<p class="m-0 text-danger m--font-boldest">${row.is_valid_message}</p>`;
                                        if (row.is_duplicate) {
                                            if (typeof row.duplicate_entry !== null) {
                                                html += `<ul style="padding-left: 20px !important">`;
                                                    html += `<li><strong>Reference No: </strong> ${row.duplicate_entry.reference_no}</li>`;
                                                    html += `<li><strong>Datetime:</strong> ${moment(row.duplicate_entry.date_from).format('YYYY-MM-DD hh:mm A')} - ${moment(row.duplicate_entry.date_to).format('YYYY-MM-DD hh:mm A')}</li>`;
                                                html += `</ul>`;
                                            }
                                        }
                                    html += `</div>`;
                                }

                                return html;
                            }
                        }
                    ], drawCallback: function (settings) {
                        const tableWrapper = $(settings.nTableWrapper);
                        tableWrapper.find("#uploaded_csv_table_filter input").removeClass("form-control-sm");
                    }
                });
            }
            if (typeof currentSelect2 !== "undefined") {
                currentSelect2.select2({
                    width: "100%",
                    placeholder: "Select an option",
                    dropdownParent: currentModal,
                    ajax: {
                        url: siteUrl("eforms/overtime/get_employee_department_head"),
                        dataType: "json",
                        delay: 250,
                        global: false,
                        processResults: function (data) {
                            return data;
                        }
                    }
                }).on('select2:select', function(e) {
                    $(e.target).validate();
                });
            }
            getCurrentUploadFiles();
            approveModalFileUpload();
        },
        renderImageLabel: function (index) {
            const tempIndex = parseInt(index) + 1;
            return "Image " + tempIndex;
        },
        getCheckedCount: function () {
            const currentElement = this.$el;
            const checked = $(currentElement).find(".temp-attachment_image:checked");
            const checkedCounter = $(currentElement).find("#checked_count");
            checkedCounter.val(checked.length).validate();
        }
    },
    mounted: function () { }
});

const getCurrentUploadFiles = function () {
    $.ajax({
        url: baseUrl("eforms/overtime/get_current_uploaded_file"),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                const tempRows = Object.assign({}, json.rows);
                const tempCount = json.count;

                vmTempUploadedContent.rows = tempRows;
                vmTempUploadedContent.count = tempCount;
            }
        }
    });
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true
    });
}

const generateReference = function () {
    $.ajax({
        url: baseUrl("eforms/overtime/generateReferenceNo"),
        dataType: "json",
        success: function (response) {
            if (response) {
                toastr.success("Reference No. generated for old entries.", "Success", 5000);
                tblOvertime.ajax.reload();
            }
        }
    });
}

const generateReferenceDetails = function () {
    $.ajax({
        url: baseUrl("eforms/overtime/generateReferenceDetails"),
        dataType: "json",
        success: function (response) {
            if (response) {
                toastr.success("Reference details generated for old entries.", "Success", 5000);
                tblOvertime.ajax.reload();

            }
        }
    })
}

$("#mass_date_time").daterangepicker({
    timePicker: true,
    minDate: moment().subtract(2, 'years'),
    startDate: moment().startOf('hour'),
    endDate: moment().startOf('hour').add(32, 'hour'),
    locale: {
        format: 'M/DD hh:mm A'
    }
}).on('apply.daterangepicker', function (ev, picker) {
    $("#date_from").val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
    $("#date_to").val(picker.endDate.format('YYYY-MM-DD HH:mm:ss'));
    $("#date").val(picker.startDate.format('MM/DD/YYYY hh:mm a') + ' - ' + picker.endDate.format('MM/DD/YYYY hh:mm a'));
});

$.validate({
    form: "#frm-mass-update",
    lang: "en",
    validateHiddenInputs: true,
    onSuccess: function (form) {
        const currentForm = form[0];
        const formUrl = currentForm.action;
        const formData = $(currentForm).serializeArray();
        let arr = [];

        $(".selectedOvertime:checked").each( function(){
            const value = $(this).val();
            arr.push(value);
        });

        formData.push({
            name: 'selected', value: arr
        });

        if(arr.length > 0){
            $.ajax({
                url: formUrl,
                type: "post",
                data: formData,
                dataType: "json",
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }, 
                success: function(response) {
                    if(response.state){
                        toastr.success(response.msg, "Mass Update Overtime", 5000);
                        tblOvertime.ajax.reload();
    
                        $("#modal-mass-update").modal('hide');
                    }else{
                        toastr.error(response.msg, "Mass Update Overtime", 5000);
                    }
    
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            })
        }else{
            toastr.error('Please select atleast 1 overtime to be updated.', "Mass Update Overtime", 5000);
        }

        
        
        return false;
    }
});

$("#mass_approved_by").select2({
    width: "100%",
    placeholder: "Select an option",
    dropdownParent: $("#modal-mass-approve"),
    ajax: {
        url: siteUrl("eforms/overtime/get_employee_department_head"),
        dataType: "json",
        delay: 250,
        global: false,
        processResults: function (data) {
            return data;
        }
    }
});

$.validate({
    form: "#frm-mass-approve",
    lang: "en",
    validateHiddenInputs: true,
    onSuccess: function (form) {
        const currentForm = form[0];
        const formUrl = currentForm.action;
        const formData = $(currentForm).serializeArray();
        let arr = [];

        $(".selectedOvertime:checked").each( function(){
            const value = $(this).val();
            arr.push(value);
        });

        formData.push({
            name: 'selected', value: arr
        });

        if(arr.length > 0){
            $.ajax({
                url: formUrl,
                type: "post",
                data: formData,
                dataType: "json",
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }, 
                success: function(response) {
                    if(response.state){
                        toastr.success(response.msg, "Mass Approve Overtime", 5000);
                        tblOvertime.ajax.reload();
    
                        $("#modal-mass-approve").modal('hide');
                    }else{
                        toastr.error(response.msg, "Mass Approve Overtime", 5000);
                    }
    
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            })
        }else{
            toastr.error('Please select atleast 1 overtime to approve.', "Mass Approve Overtime", 5000);
        }

        
        return false;
    }
});

$("#mass_disapproved_by").select2({
    width: "100%",
    placeholder: "Select an option",
    dropdownParent: $("#modal-mass-disapprove"),
    ajax: {
        url: siteUrl("eforms/overtime/get_employee_department_head"),
        dataType: "json",
        delay: 250,
        global: false,
        processResults: function (data) {
            return data;
        }
    }
});

$.validate({
    form: "#frm-mass-disapprove",
    lang: "en",
    validateHiddenInputs: true,
    onSuccess: function (form) {
        const currentForm = form[0];
        const formUrl = currentForm.action;
        const formData = $(currentForm).serializeArray();
        let arr = [];

        $(".selectedOvertime:checked").each( function(){
            const value = $(this).val();
            arr.push(value);
        });

        formData.push({
            name: 'selected', value: arr
        });

        if(arr.length > 0){
            $.ajax({
                url: formUrl,
                type: "post",
                data: formData,
                dataType: "json",
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }, 
                success: function(response) {
                    if(response.state){
                        toastr.success(response.msg, "Mass Disapprove Overtime", 5000);
                        tblOvertime.ajax.reload();
    
                        $("#modal-mass-disapprove").modal('hide');
                    }else{
                        toastr.error(response.msg, "Mass Disapprove Overtime", 5000);
                    }
    
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            })
        }else{
            toastr.error('Please select atleast 1 overtime to disapprove.', "Mass Disapprove Overtime", 5000);
        }

        return false;
    }
});

function formatToBullets(text) {
    return text
        .split(/\r?\n|,/)
        .map(item => item.trim())
        .filter(item => item.length)
        .map(item => `- ${item}`)
        .join('<br>');
}