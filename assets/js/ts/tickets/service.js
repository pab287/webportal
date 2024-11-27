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

    $("#reopen_field").hide();
    $("#onhold_field").hide();
    $("#webportal").hide();
});
let images = [];
$.ajax({
    url: baseUrl("ts/ticketing/service_details/") + param_id,
    type: "GET",
    dataType: "JSON",
    success: function (data) {
        if (data.status !== 'Closed') {
            $(".fileinput-button").css("display", "inline-block");
        }

        const vmData = data;

        if (vmData.type == "WEBPORTAL") {
            $("#webportal").show();
        }

        /*$("#issue").val(vmData.issue);
        $("#requested_by").val(vmData['requested_by']);
        $("#need_dt").val(moment(vmData.need_dt).format("YYYY/MM/DD"));
        $("#requested_dt").val(moment(vmData.requested_dt).format("YYYY/MM/DD"));
        $("#remarks").val(vmData.remark);
        $("#reopen").val(vmData.open);
        $("#onhold").val(vmData.onhold);*/

        if (vmData.onhold && vmData.status === 'Onhold') {
            $("#onhold_field").show();
        } else {
            $("#onhold_field").hide();
        }
        if (vmData.open) {
            $("#reopen_field").show();
        } else {
            $("#reopen_field").hide();
        }

        var modules = new Option(vmData.module, vmData.module, true, true);
        $('#module').append(modules).trigger('change');

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
        const picUrl = vmData.picture ? baseUrl("uploads/files/images/employee_files/" + vmData.picture) : baseUrl('assets/images/ams/images/no_image.jpg');
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
                    if(filename){
                    viewButton = '' +
                        ' <button '+
                            ' title="Preview" ' +
                            ' onclick="previewDocument(\'' + avatarImage + '\', \'' + icon + '\', \'' + filename + '\')"' +
                            ' class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnRemove" type="button" id="previewFile"> '+
                            ' <i class="la la-eye"></i>' +
                        ' </button>';
                    }else{
                        viewButton = '';
                    }
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

        if (vmData.emp_id) {
            var performed_by = new Option(vmData.performed_by_det, vmData.performed_by, true, true);
            $('#performed_by').append(performed_by).trigger('change');
        }

        let _status = null;
        switch (vmData.status) {
            case 'Closed':
                _status = 'COMPLETED';
                break;
            case 'Confirmed':
                _status = 'RESOLVED'
                break;
            default:
                _status = vmData.status;
        }

        var status = new Option(_status, vmData.status, true, true);
        $('#status').append(status).trigger('change');

        var type = new Option(vmData.type, vmData.type, true, true);
        $('#type').append(type).trigger('change');

        var department = new Option(vmData.description, vmData.department_id, true, true);
        $('#department').append(department).trigger('change');
        
        
        vmTab1.vm_tab1 = Object.assign({}, data);
    }
});

var vmTab1 = new Vue({
    el: "#frm_status_new",
    data: {vm_tab1: {}},
    mounted: function () {
        setTimeout(function (data) {
            var vmData = this.vmTab1.vm_tab1;
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

$("#performed_by").select2({
    placeholder: 'Select',
    width: '100%',
    ajax: {
        url: baseUrl("ts/ticketing/performed"),
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});

var statuses = [
    {
        id: "Open",
        text: "OPEN"
    },
    {
        id: "Inprogress",
        text: "INPROGRESS"
    },
    {
        id: "Onhold",
        text: "ONHOLD"
    },
    {
        id: "Cancelled",
        text: "CANCELLED"
    },
    {
        id: "Closed",
        text: "COMPLETED"
    }, {
        id: "Confirmed",
        text: "RESOLVED"
    }
];

$("#status").select2({
    placeholder: 'Select. .',
    width: '100%',
    data: statuses
});

var type = [
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
    data: type
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

$("#status").on("select2:select", function () {
    if ($("#status option:selected").attr("value") == "Onhold") {
        $("#onhold_field").show();
    } else {
        $("#onhold_field").hide();
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
                    toastr.success("Service ticket was successfully updated.", "Service Ticket Updated.", 5000);
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

$.validate({
    form: '#frm_status_new',
    lang: 'en',
    onSuccess: function (form) {
        var disabled = $('#frm_status_new').find('input:disabled').removeAttr('disabled');
        $.ajax({
            url: baseUrl("ts/ticketing/update_service/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#frm_status_new").find("input,select,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    toastr.success("Service ticket was successfully saved.", "Service Ticket Saved.", 5000)
                    setTimeout(() => {
                        window.location.assign(baseUrl("ts/ticketing/masterfile"));
                    }, 700);
                    disabled.attr('disabled', 'disabled');

                } else {
                    toastr.error(data.toastr_msg, "Notice: Error!", 5000);
                    disabled.attr('disabled', 'disabled');
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
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

function removeDocument(el){
    var _name = $(el).attr("data-name");
    const parent = $(el).closest('.m-widget2__item');
    images = images.filter((n) => {return n != _name});
    $(parent).remove();
    $("#pic").val(images);
    toastr.success(_name,"Removed File", 5000);
}
  