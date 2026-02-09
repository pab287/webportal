$(document).ready(function() {
    fileUploadPhoto();
    fileUploadPhoto_();
    $("form").attr('autocomplete', 'off');
});

const initReadingStartDate = moment();
const initReadingEndDate = moment();
let selectedReadingStartDate = null;
let selectedReadingEndDate = null;

var search_val = "";
var query_builder = "";
var tblReadings = $("#table-readings").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/billing/get_reading_collection/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function(d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
            d.query_builder = query_builder;

            if (selectedReadingStartDate) {
                d.startDate = moment(selectedReadingStartDate).format("YYYY-MM-DD");
            }

            if (selectedReadingEndDate) {
                d.endDate = moment(selectedReadingEndDate).format("YYYY-MM-DD");
            }
        }
    },
    searching: true,
    drawCallback: function(setting) {
        var api = this.api();
        let getJson = api.ajax.json();
        let check_if_billed = getJson.to_billed;
        
        if(check_if_billed == true) {
            $("#table-readings thead tr th:first-child").removeClass('m--hide');
            $("#table-readings tbody tr td:first-child").removeClass('m--hide');
        } else {
            $("#table-readings thead tr th:first-child").addClass('m--hide');
            $("#table-readings tbody tr td:first-child").addClass('m--hide');
        }
    },
    columns: [
        {
            width: '2%',
            orderable: false,
            data: null,
            className: 'text-center',
            render: function (data, type, row) {
                var temp = "";
                if(row.status == 0) {
                    temp = `<label class="m-checkbox m-checkbox--air m-checkbox--state-primary" title='Check to Print'> <input id="selectedReading" type="checkbox" class="text-gray chckBox" value="`+row.id+`" name="selected"><span></span></label>`;
                }
                return temp;
            }
        },
        { 
            data: "ref_no", render: function (data) {
                return "<strong style='color: #525252;'>"+data+"</strong>";
            }
        },
        { data: "accountno" },
        { data: "name" },
        { data: "meterno" },
        { data: "reading_date" },
        { data: "model" },
        { data: "block" },
        { data: "lot" },
        { 
            data: "reading", className: "text-right", render: function (data) {
                return "<strong style='color: #525252;'>" + numberWithCommas(data) + "</strong>";
            }
        },
        { 
            data: "status", className: "text-center", render: function (data) {
                return renderStatus(data);
            }
        },
        { data: null, width: "5%", className: "text-center" },
    ],
    order: [[1, 'desc']],
    columnDefs: [
        { targets: [0]},
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function ( data, type, row, meta ) { return itemDatatableActions(row); },
        }
    ],
    buttons: [
        { 
            extend: 'csv',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, 
        { 
            extend: 'excel',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, 
        { 
            extend: 'pdf',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }
    ]
});

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function renderStatus(data) {
    switch (data) {
        case "1":
            return '<div class="m-badge text-white m-badge--accent m-badge--wide" role="alert"><strong>Billed</strong></div>';
            break;
        default:
            return '<div class="m-badge text-white m-badge--warning m-badge--wide" role="alert"><strong>Unbilled</strong></div>';
            break;
    }
}

$('#readings-date-picker').daterangepicker({
    buttonClasses: 'm-btn btn',
    applyClass: 'btn-primary',
    cancelClass: 'btn-secondary',
    startDate: initReadingStartDate,
    endDate: initReadingEndDate,
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    format: "MMM. DD, YYYY"
}, 
function (start, end, label) {
    selectedReadingStartDate = start;
    selectedReadingEndDate = end;
    let _label = label;
    filterLateReport = label;

    if(label === "Custom Range") {
        _label += ": <strong>" + start.format("MMM. DD, YYYY") + "</strong> to <strong>" + end.format("MMM. DD, YYYY") + "</strong>";
    }

    $(".selected-filter", $('#readings-date-picker')).html(_label);
    tblReadings.ajax.reload();
});

function itemDatatableActions(row) {
    if(row) {
        var tempHtml = "---";
        var tempActions = [];
        var currentActions = ["edit", "delete"];
        $.each(currentActions, function(index, value) {
            tempActions.push(value);
        });

        tempHtml = `<div class="dropdown">
                        <a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown"> 
                            <i class="la la-ellipsis-h"></i>
                        </a>
                        
                        <div class="dropdown-menu dropdown-menu-right">`;
                            $.each(tempActions, function(ii, vv){
                                switch(vv){
                                    case "edit":
                                        var action_name = "", icon_name = "";

                                        if(row.status == 1) {
                                            action_name = "View";
                                            icon_name = "la	la-eye";
                                        } else {
                                            action_name = "Edit";
                                            icon_name = "la la-edit";
                                        }

                                        tempHtml += `<a class="dropdown-item " data-toggle='modal' data-target='#m_editReading' href="javascript:void(0);" id='editReading' data-id='`+row.id+`'><i class="`+icon_name+`"></i> `+action_name+`</a>`;
                                        break;

                                    case "delete":
                                        if(row.status != 1) {
                                            tempHtml += `<a class="dropdown-item " style="color: #FF8383;" href="javascript:void(0);" onclick='modalArchive(`+ row.id +`,`+`\"` + row.ref_no + `\")'><i class="la la-trash" style="color: #FF8383;"></i> Archive</a>`;
                                        }
                                        break;
                                }
                            });
          tempHtml += ` </div>
                    </div>`;
        return tempHtml;
    } else { 
      return false; 
    }
}

function modalArchive(id, ref_no){
    const temp = `<p>Are you sure you wan't to archive <strong class='m--font-boldest'>${ref_no}</strong>?</p>`;
    $('#m_archived').modal('show');
    $('#archive_text').empty().html(temp);
    $("#m_archived input[name=id]").val(id);
    $("#m_archived input[name=archive_ref_no]").val(ref_no);
}

function archiveReading(){
    var reading_id = document.getElementById('archive_id').value;
    var ref_no = document.getElementById('archive_ref_no').value;

    $.ajax({
        url: baseUrl("eforms/billing/archive_reading"),
        type: 'post',
        data: { 
            csrf_token: _csrf_hash, 
            id: reading_id, 
            ref_no:ref_no 
        },
        success: function (data) {
            if(data.status){
                $('#m_archived').modal('hide');
                tblReadings.ajax.reload();
            }
        },
        error: function (request, status, error) {
            toastr.error("Please check your internet connection.", "Connection error");
        }
    });
}

$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    tblReadings.ajax.reload();
});

$("#ExportExcel").on("click", function(e) {
    e.preventDefault();
    tblReadings.button( '.buttons-excel' ).trigger();
    saveExportLogs('Readings - Export Excel');
});

$("#ExportCSV").on("click", function(e) {
    e.preventDefault();
    tblReadings.button( '.buttons-csv' ).trigger();
    saveExportLogs('Readings - Export CSV');
});

$("#ExportPDF").on("click", function(e) {
    e.preventDefault();
    tblReadings.button( '.buttons-pdf' ).trigger();
    saveExportLogs('Readings - Export PDF');
});

function saveExportLogs(export_) {
    $.ajax({
        url: baseUrl("eforms/billing/save_export_logs"),
        type: 'post',
        data: { 
          csrf_token: _csrf_hash, 
          export_: export_ 
        },
        success: function (data) {}
    });
}

Inputmask.extendAliases({
    deci: {
        prefix: "",
        groupSeparator: ".",
        alias: "numeric",
        placeholder: "0",
        autoGroup: !0,
        digits: 2,
        digitsOptional: !1,
        clearMaskOnLostFocus: !1
    }
});

$("#reading").inputmask({ alias : "deci", removeMaskOnSubmit: true });
$("#edit_reading").inputmask({ alias : "deci", removeMaskOnSubmit: true });

$("#select2_account").select2({
    placeholder: 'SELECT AN OPTION',
    dropdownParent: $("#m_newReading"),
    width: '100%',
    minimumInputLength: 3,
    ajax: {
        url: baseUrl("eforms/billing/get_account_select_reading"),
        global: false,
        processResults: function (data) {
            return data;
        }
    }
});

$("#select2_account_edit").select2({
    placeholder: 'SELECT AN OPTION',
    dropdownParent: $("#m_editReading"),
    width: '100%',
    ajax: {
        url: baseUrl("eforms/billing/get_account_select_reading"),
        global: false,
        processResults: function (data) {
            return data;
        }
    }
});

$('.newReadingBtn').click(function(e) {
    $('#fromCreateReading').trigger("reset");
    $(".picInput").remove();
    $(".dip_img").remove();
    $(".picture_null").show();
    $('#m_newReading #select2_account').val('').trigger("change");
    $('.initial-reading').html('');
});

function account_details(){
    var account_id = $('[name="account_id"]').val();

    if(typeof account_id != "undefined" && account_id && account_id != 'null') {
        $.ajax({
            url: baseUrl('eforms/billing/get_account_details/') + account_id,
            dataType: "JSON",
            success: function(data) {
                $(".meterno").val(data.data.meterno);
                $("#meterno_raw").val(data.data.meterno_raw);
                $(".block").val(data.data.block);
                $(".lot").val(data.data.lot);
                $(".previous_reading").val(data.previous_reading);
                $(".account_name").val(data.data.firstname + " " + data.data.lastname);
                $("#reading").val("");

                // Get year and month from previous reading date
                const yearMonth = moment(data.previous_reading_date).format("YYYY-MM");

                // Set the start date for the reading date picker to the first day of the month of the previous reading date
                let previous_reading_date_startDate = moment(yearMonth + "-01").format('YYYY/MM/DD');
                
                // Set the reading date picker to the previous reading date plus one month to avoid selecting the same month
                const futureDateMonth = moment(previous_reading_date_startDate).add(1, 'month').format('YYYY/MM/DD');

                // Set the reading date picker to the previous reading date
                $('#readingdate').datetimepicker("setStartDate", futureDateMonth);

                if (data.initial_reading === "0") {
                    $('.initial-reading').html(`<small style="color: #ff0000;">This meter was replaced.</small>`);
                } else {
                    $('.initial-reading').html('');
                }
            },
            error: function (request, status, error) {
                toastr.error("Please check your internet connection.", "Connection error");
                $("#m_newReading").modal("hide");
            }
        });
    }
}

$('#readingdate').datetimepicker({
    minView:'month',
    format: 'yyyy/mm/dd',
    todayHighlight: true,
    autoclose: true,
    orientation: "bottom left",
    templates: {
        leftArrow: '<i class="la la-angle-left"></i>',
        rightArrow: '<i class="la la-angle-right"></i>'
    },
    endDate: getCurrentDate(),
});

$.validate({
    form : '#fromCreateReading',
    lang: 'en',
    onSuccess : function(form) {
        $.ajax({
            url : $(form).attr("action"),
            type: "POST",
            data: $('#fromCreateReading').serialize(),
            dataType: "JSON",
            success: function(data) {
                if(data.status == true) {
                  toastr.success(data.msg, "Notification");
                  tblReadings.ajax.reload();
                  $('#fromCreateReading').trigger("reset");
                  $(".picInput").remove();
                  $(".dip_img").remove();
                  $('#m_newReading #select2_account').val('').trigger("change");
                  $('.initial-reading').html('');
                } else {
                  toastr.warning(data.msg, "Notification");
                }
            },
            error: function (request, status, error) {
                toastr.error("Please check your internet connection.", "Connection error");
            }
        });

        return false;
    },
});

//fileupload
$("#m_newReading #progress").hide();
var fileUploadPhoto = function () {
    var url = baseUrl("eforms/billing/upload_reading_photo");

    $("#fileupload").fileupload({
        url: url,
        dataType: "json",
        formData: {csrf_token: _csrf_hash},
        done: function (e, data) {
            var result = data.result;
            if (result.response) {
                var avatarImage = result.added_image;
                var renderImage = result.render_image;
                $("#m_newReading .picture_null").hide();
                $("#img_primary").prepend('<div class="col-md-6 dip_img text-center"><img name="picture" style="max-width: 250px; margin: 0 auto;" src="'+avatarImage+'"></div>');
                $(".attachValueContaniner").append('<input class="picInput" type="hidden" value="'+renderImage+'" name="pic[]">');
                toastr.success(result.toastr_msg, "Upload Image", 5000);
            } else {
                toastr.error(result.toastr_msg, "Upload Image", 5000);
            }
        },
        progressall: function (e, data) {
            $("#m_newReading #progress").show();
            var progress = parseInt((data.loaded / data.total) * 100, 10);
            var progressTotal = 0;

            var steps = setInterval(function () {
                progressTotal += 10;
                $("#progress .progress-bar").css("width", progressTotal + "%");
                if (progressTotal == 100) {
                    clearInterval(steps);
                    progressTotal = 0;
                    setTimeout(function () {
                        $("#progress .progress-bar").css("width", progressTotal + "%");
                    }, 1500);
                }
            }, 10);

            if (progress == 100) {
                setTimeout(function () {
                    $("#m_newReading #progress").hide();
                }, 1000);
            }
        }
    }).prop("disabled", !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : "disabled");
};

$("#m_editReading #progress").hide();
var fileUploadPhoto_ = function () {
    var url = baseUrl("eforms/billing/upload_reading_photo");
    $("#fileupload_").fileupload({
        url: url,
        dataType: "json",
        formData: { csrf_token: _csrf_hash },
        done: function (e, data) {
            var result = data.result;
            if (result.response) {
                var avatarImage = result.added_image;
                var renderImage = result.render_image;
                $("#m_editReading #picture").hide();
                $("#m_editReading #img_primary").prepend('<div class="col-md-6 dip_img text-center"><img name="picture" style="max-width: 250px; margin: 0 auto;" src="'+avatarImage+'"></div>');
                $("#m_editReading .attachValueContaniner").prepend('<input class="picInput" type="hidden" value="'+renderImage+'" name="pic[]">');
                toastr.success(result.toastr_msg, "Upload Image", 5000);
            } else {
                toastr.error(result.toastr_msg, "Upload Image", 5000);
            }
        },
        progressall: function (e, data) {
            $("#m_editReading #progress").show();
            var progress = parseInt((data.loaded / data.total) * 100, 10);
            var progressTotal = 0;

            var steps = setInterval(function () {
                progressTotal += 10;
                $("#progress .progress-bar").css("width", progressTotal + "%");
                if (progressTotal == 100) {
                    clearInterval(steps);
                    progressTotal = 0;
                    $("#m_editReading .picture_null").hide();
                    setTimeout(function () {
                        $("#progress .progress-bar").css("width", progressTotal + "%");
                    }, 1500);
                }
            }, 10);

            if (progress == 100) {
                setTimeout(function () {
                    $("#m_editReading #progress").hide();
                }, 1000);
            }
        }
    }).prop("disabled", !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : "disabled");
};

$("#table-readings").on("click","#editReading",function(e){
    var id = $(this).attr("data-id");
    $(".dip_img").each(function(){
        $(this).remove();
    });

    $(".picInput").each(function(){
        $(this).remove();
    });

    $.ajax({
        url: baseUrl('eforms/billing/get_reading_details/') + id,
        dataType: "JSON",
        success: function(data) {
            if(data.is_billed == 1) {
                $("#m_editReading .btnUpdate").hide();
                $("#m_editReading .fileinput-button").hide();
                $('#m_editReading #main_body').css('pointer-events', 'none');
            } else {
                $("#m_editReading .btnUpdate").show();
                $("#m_editReading .fileinput-button").show();
                $('#m_editReading #main_body').css('pointer-events', '');
            }

            $("#ref_no_").val(data.ref_no);
            $("#m_editReading input[name=id]").val(data.id);
            $("#m_editReading .ref_no").val(data.ref_no);
            $("#m_editReading .meterno").val(data.meterno);
            $("#m_editReading #account_id").val(data.account_id);
            $("#m_editReading .block").val(data.block);
            $("#m_editReading .lot").val(data.lot);
            $("#m_editReading .account_name").val(data.name);
            $("#m_editReading .reading").val(data.reading);
            $("#m_editReading #old_reading").val(data.reading);
            $("#m_editReading .account_no").val(data.accountno);
            $("#m_editReading #old_reading_date").val(data.reading_date);
            // $('#m_editReading .readingdate').datepicker("setDate", data.reading_date);
            $('#m_editReading .readingdate_readonly').html(data.reading_date);

            if(data.pic.length > 1) {
                $("#m_editReading .picture_null").hide();

                $.each(data.pic, function(index, val) {
                    if(val != "") {
                        if(data.is_billed == 1) {
                            $("#m_editReading #img_primary").prepend('<div data-cont-id="'+index+'" class="col-md-6 dip_img text-center"><img name="picture" style="max-width: 250px; margin: 0 auto;" src="'+baseUrl(val)+'"><br/></div>');
                            $("#m_editReading .attachValueContaniner").append('<input class="picInput" data-cont-id="'+index+'" type="hidden" value="'+val+'" name="pic[]">');
                        } else {
                            $("#m_editReading #img_primary").prepend('<div data-cont-id="'+index+'" class="col-md-6 dip_img text-center"><img name="picture" style="max-width: 250px; margin: 0 auto;" src="'+baseUrl(val)+'"><br/><button type="button" class="btn btn-danger removeAttached" data-src="'+val+'" data-id="'+index+'">Remove</button></div>');
                            $("#m_editReading .attachValueContaniner").append('<input class="picInput" data-cont-id="'+index+'" type="hidden" value="'+val+'" name="pic[]">');
                        }
                    }
                });
            } else {
                $("#m_editReading .picture_null").show();
            }
        },
        error: function (request, status, error) {
            toastr.error("Please check your internet connection.", "Connection error");
        }
    });
});

$('.readingdate').datepicker({
    format: 'yyyy/mm/dd',
    todayHighlight: true,
    autoclose: true,
    orientation: "bottom left",
    templates: {
        leftArrow: '<i class="la la-angle-left"></i>',
        rightArrow: '<i class="la la-angle-right"></i>'
    },
    endDate: getCurrentDate()
});

$("#fromUpdateReading #img_primary").on("click",".removeAttached",function() {
    var id = $(this).attr("data-id");
    var src = $(this).attr("data-src");
    $("div[data-cont-id="+id+"]").remove();
    $("input[data-cont-id="+id+"]").remove();
});

$.validate({
    form : '#fromUpdateReading',
    lang: 'en',
    onSuccess : function(form) {
        // Get the value from the p tag
        var reading_date = $('.readingdate_readonly').text().trim();

        // Serialize the form data
        var formData = $('#fromUpdateReading').serialize();

        // Append the reading_date to the payload
        formData += '&reading_date=' + encodeURIComponent(reading_date);

        $.ajax({
            url : $(form).attr("action"),
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function(data) {
                if(data.status == true) {
                    toastr.success(data.msg, "Notification");
                    tblReadings.ajax.reload();
                    $('#fromUpdateReading').trigger("reset");
                    $(".picInput").remove();
                    $(".dip_img").remove();
                    $("#m_editReading").modal("hide");
                } else {
                    toastr.warning(data.msg, "Notification");
                }
            },
            error: function (request, status, error) {
                toastr.error("Please check your internet connection.", "Connection error");
            }
        });
        
        return false;
    },
});  

$(".btnApprove_action").on("click",function(){
    var id = $("#fromUpdateReading input[name=id]").val();
    $.ajax({
        url: baseUrl("eforms/billing/approve_reading"),
        type: "POST",
        data: { id: id, csrf_token: _csrf_hash },
        success: function(data){
          
        },
        error: function (request, status, error) {
            toastr.error("Please check your internet connection.", "Connection error");
        }
    });
});

$("#cb-select-all").click(function () {
    $('#table-readings tbody input[type="checkbox"]').prop('checked', this.checked);
});

$("#table-readings").on("click", "tbody input[type='checkbox']", function () {
    const allCheckboxes = $("#table-readings tbody input[type='checkbox']").length;
    const checkedCheckboxes = $("#table-readings tbody input[type='checkbox']:checked").length;
    const checked = allCheckboxes <= checkedCheckboxes;
    $('#cb-select-all').prop('checked', checked);
});

$("#generate_bill").click(function (){
    var selectedReading = [];
    $(".chckBox").each(function() {
        var trig = $(this).is(":checked");

        if(trig) {
            selectedReading.push($(this).attr("value"));
        }
    });

    if(selectedReading.length > 0) {
        $('#m_generate').modal('show');
    } else {
        toastr.warning("No selected reading.", "Notification");
    }
});

function generateBill(){
    var selectedReading = [];
    $(".chckBox").each(function(){
        var trig = $(this).is(":checked");

        if(trig) {
            selectedReading.push($(this).attr("value"));
        }
    });

    if(selectedReading.length > 0) {
        $.ajax({
            url: baseUrl("eforms/billing/generate_bill"),
            type: "POST",
            data: { csrf_token: _csrf_hash, selectedReading: selectedReading },
            success: function(data) {
                data.forEach((e) => {
                    if(e.status) {
                        toastr.success(e.msg, e.ref_no);
                    } else {
                        toastr.error(e.msg, e.ref_no);
                    }
                });

                tblReadings.ajax.reload();
                $('#m_generate').modal('hide');
                $('#cb-select-all').prop('checked', false);
            },
            error: function (request, status, error) {
                tblReadings.ajax.reload();
                $('#m_generate').modal('hide');
                toastr.warning("Please check your internet connection.", "Connection error");
            }
        });
    } else {
        toastr.warning("No selected reading.", "Notification");
    }
}

function getCurrentDate() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();
    return yyyy+'-'+mm+'-'+dd;
}
