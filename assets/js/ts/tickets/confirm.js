var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

param_id = getUrlParameter('id');

jQuery(document).ready(function () {
    $("#progress").hide();
    fileUploadPhoto();
    $("#webportal").hide();
});
let images = [];
$.ajax({
    url: baseUrl("ts/ticketing/confirm_details/") + param_id,
    type: "GET",
    dataType: "JSON",
    success: function (data) {
        if (data.status !== 'Closed') {
            $(".fileinput-button").css("display", "inline-block");
        }
        
        const vmData = data;

        const allowedFileTypes = [
            {
                _type: ["jpg", "jpeg", "png", "PNG", "JPEG", "JPG"],
                icon: "jpg.svg",
                color: "success"
            },
            {
                _type: ["docx", "DOCX"],
                icon: "doc.svg",
                color: "info"
            },
            {
                _type: ["pdf", "PDF"],
                icon: "pdf.svg",
                color: "danger"
            },
        ];
        
        const created_by = vmData.created_by;
        const arrImg = vmData.picture.split(',');
        arrImg.forEach(function(file){
            const fileArr = file.split("/");
            const filename = fileArr[fileArr.length - 1];
            const ext = filename.split(".");

            var avatarImage = baseUrl("uploads/files/images/employee_files/empcode_"+ created_by +"/ticketing/" + filename);
            var renderImage = vmData.picture;
            images.push(filename);
            $("#picture").attr("src", renderImage);
            $("#pic").val(images);
            let icon = '';
            let color = '';
            test = ext[ext.length - 1];
            allowedFileTypes.forEach((item, i) => {
                if (item._type.includes(test)) {
                    icon = item.icon;
                    color = item.color;
                }
                
            });
            const icon_path = baseUrl('assets/images/file_icons/' + icon);
            let viewButton = '';
                if (ext == 'docx') {
                    viewButton = '';
                } else{
                    viewButton = '' +
                        ' <button '+
                            ' title="Preview" ' +
                            ' onclick="previewDocument(\'' + avatarImage + '\', \'' + icon + '\', \'' + filename + '\')"' +
                            ' class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnRemove" type="button" id="previewFile"> '+
                            ' <i class="la la-eye"></i>' +
                        ' </button>';
                }
            var fileList = '<div class="m-widget2">'+
                '<div class="m-widget2__item m-widget2__item--'+color+'">' +
                    '<div class="m-widget2__checkbox">'+
                        '<div class="m-widget2__img m-widget2__img--icon">'+
                        '<img src="'+icon_path+'" width="45" alt>' +
                        '</div>'+
                    '</div>'+
                    '<div class="m-widget2__desc">'+
                        '<span class="m-widget2__user-text">'+
                        ''+
                        '</span><br>'+
                        '<span class="m-widget2__user-name">'+
                        filename +
                        '</span>'+
                        '<span class="m-widget2__user-name">'+
                        '</span><br><br>'+
                    '</div>' +
                    '<div class="m-widget2__actions">' +
                    '' + viewButton +
                    '</div>'+
                '</div>'+
                '</div>';
                $("#uploaded_files").append(fileList);
        });

        var type = new Option(vmData.type, vmData.type, true, true);
        $('#type').append(type).trigger('change');

        var department = new Option(vmData.name, vmData.department_id, true, true);
        $('#department').append(department).trigger('change');

        vmTab1.vm_tab1 = Object.assign({}, data);
        
    }
});

var vmTab1 = new Vue({
    el: "#frm_status_confirm_ticket",
    data: {vm_tab1: {}},
    mounted: function () {
        setTimeout(function () {
            var vmData = this.vmTab1.vm_tab1;
            if (vmData.type == "WEBPORTAL") {
                $("#webportal").show();
            }


        }, 400);
    }
});

$("#department").select2({
    placeholder: 'Select. .',
    width: '100%',
    ajax: {
        url: baseUrl("ts/ticketing/department"),
        processResults: function (data) {
            return data;
        }
    }
});

$('#need_dt').datetimepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    format: 'yyyy/mm/dd hh:ii:ss',
});

var data = [
    {
        id: "HARDWARE",
        text: "HARDWARE"
    },
    {
        id: "SOFTWARE",
        text: "SOFTWARE"
    },
    {
        id: "OUTLOOK",
        text: "OUTLOOK"
    },
    {
        id: "WEBPORTAL",
        text: "WEBPORTAL"
    },
    {
        id: "WEBSITE",
        text: "WEBSITE"
    }
];

$("#type").select2({
    placeholder: 'Select. .',
    width: '100%',
    data: data
});

var module = [
    {
        id: "HUMAN RESOURCE INFORMATION SYSTEM (HRIS)",
        text: "HUMAN RESOURCE INFORMATION SYSTEM (HRIS)"
    },
    {
        id: "MATERIAL REQUISITION SYSTEM (MRS)",
        text: "MATERIAL REQUISITION SYSTEM (MRS)"
    },
    {
        id: "COMPANY RECRUITMENT SYSTEM (CRS)",
        text: "COMPANY RECRUITMENT SYSTEM (CRS)"
    },
    {
        id: "TRAINING AND ORIENTATION SYSTEM (TOS)",
        text: "TRAINING AND ORIENTATION SYSTEM (TOS)"
    },
    {
        id: "CORPORATE SMS GATEWAY SERVICE (SMS)",
        text: "CORPORATE SMS GATEWAY SERVICE (SMS)"
    },
    {
        id: "MATERIAL MANAGEMENT SYSTEM (MMS)",
        text: "MATERIAL MANAGEMENT SYSTEM (MMS)"
    },
    {
        id: "ASSET MANAGEMENT SYSTEM (AMS)",
        text: "ASSET MANAGEMENT SYSTEM (AMS)"
    },
    {
        id: "DATA ARCHIVING SYSTEM (DAS)",
        text: "DATA ARCHIVING SYSTEM (DAS)"
    },
    {
        id: "TICKETING SYSTEM (TS)",
        text: "TICKETING SYSTEM (TS)"
    },
    {
        id: "USER ACCOUNTS MANAGEMENT (UAM)",
        text: "USER ACCOUNTS MANAGEMENT (UAM)"
    },
    {
        id: "SHIPPING ADVICE (SA)",
        text: "SHIPPING ADVICE (SA)"
    },
    {
        id: "TRANSMITTAL REPORT (TR)",
        text: "TRANSMITTAL REPORT (TR)"
    },
    {
        id: "ACCOUNTABILITY FORM (AF)",
        text: "ACCOUNTABILITY FORM (AF)"
    },
    {
        id: "BORROWING FORM (BF)",
        text: "BORROWING FORM (BF)"
    },
    {
        id: "CASH ADVANCE (CA)",
        text: "CASH ADVANCE (CA)"
    },
    {
        id: "LEAVE OF ABSENCE (LOA)",
        text: "LEAVE OF ABSENCE (LOA)"
    },
    {
        id: "TRAVEL ORDER (TO)",
        text: "TRAVEL ORDER (TO)"
    }
];

$("#module").select2({
    placeholder: 'Select. .',
    width: '100%',
    data: module
});

$("#type").on("select2:select", function () {
    if ($("#type option:selected").attr("value") == "WEBPORTAL") {
        $("#webportal").show();
    } else {
        $("#webportal").hide();
    }

});

$.validate({
    form: '#frm_status_new',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("ts/ticketing/save_ticket"),
            type: "POST",
            dataType: "json",
            data: $("#frm_status_new").find("input,select,textarea,img").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    toastr.success(data.toastr_msg, "Notification: Successfully saved", 5000);
                } else {
                    toastr.error(data.toastr_msg, "Notification: Error", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

var fileUploadPhoto = function () {
    var url = baseUrl("ts/ticketing/upload_ticket_photo");
    $("#fileupload")
        .fileupload({
            url: url,
            dataType: "json",
            formData: {csrf_token: _csrf_hash},
            done: function (e, data) {
                var result = data.result;
                if (result.response) {
                    var avatarImage = result.added_image;
                    var renderImage = result.render_image;
                    $("#picture").attr("src", avatarImage);
                    $("#pic").val(renderImage);
                    toastr.success(result.toastr_msg, "Upload Image", 5000);
                } else {
                    toastr.error(result.toastr_msg, "Upload Image", 5000);
                }
            },
            progressall: function (e, data) {
                $("#progress").show();
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
                        $("#progress").hide();
                    }, 1000);
                }
            }
        })
        .prop("disabled", !$.support.fileInput)
        .parent()
        .addClass($.support.fileInput ? undefined : "disabled");
};

function confirmService(element) {
    $('#reason').attr('data-validation', '');
    const form = $(element);
    var disabled = form.find('input:disabled').removeAttr('disabled');

    $.ajax({
        url: baseUrl("ts/ticketing/update_confirm/") + param_id,
        type: "POST",
        dataType: "json",
        data: form.serialize(),
        beforeSend: function () {
            $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function (data) {
            if (data) {
                toastr.success("Service was successfully confirmed.", "Confirm service ticket.", 5000);
                disabled.attr('disabled', 'disabled');

            } else {
                toastr.error(data.toastr_msg, "Notification: Error", 5000);
                disabled.attr('disabled', 'disabled');
            }
            $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }
    });
}

$("#reopen").on("click", function () {
    $('#reason').attr('data-validation', 'required');
    const form = $('#frm_status_confirm_ticket');

    if (form.isValid()) {
        $.ajax({
            url: baseUrl("ts/ticketing/reopen_ticket/") + param_id,
            type: "POST",
            dataType: "json",
            data: form.serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    toastr.success("Ticket was successfully re-opnened.", "Ticket re-opened.", 5000)
                    setTimeout(() => {
                        window.location.assign(baseUrl("ts/ticketing/masterfile"));
                    }, 700);
                } else {
                    toastr.error(data.toastr_msg, "Notice: Error!", 5000);

                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
    }
});
function previewDocument(url, icon, filename) {
    let src = url;
    if (icon === "doc.svg") {
        src = "https://docs.google.com/gview?url=" + url + "&embeded=true";
    }

    const modal = $("#preview-document-dialog");
    const modal_title = modal.find(".modal-title");
    const modal_body = modal.find(".modal-body");
    const preview = "<embed src='" + src + "' />";
    modal_title.html(filename);
    modal_body.html(preview);
    modal.modal("show");
}
  