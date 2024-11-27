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
    url: baseUrl("ts/ticketing/edit_ticket_details/") + param_id,
    type: "GET",
    dataType: "JSON",
    success: function (data) {
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
        const vmData = data;
        if (vmData['result'].type == "WEBPORTAL") {
            $("#webportal").show();
        }
        $("#issue").val(vmData['result'].issue);
        $("#need_dt").val(moment(vmData['result'].need_dt).format("YYYY/MM/DD"));
        $("#requested_dt").val(moment(vmData['result'].requested_dt).format("YYYY/MM/DD"));
        $("#requested_by").val(vmData['requested_by']);

        var modules = new Option(vmData['result'].module, vmData['result'].module, true, true);
        $('#module').append(modules).trigger('change');

        const picUrl = vmData['result'].picture ? baseUrl("uploads/files/images/employee_files/" + vmData['result'].picture) : baseUrl('assets/images/ams/images/no_image.jpg');
        const created_by = vmData['result'].created_by;
        
        const arrImg = vmData['result'].picture.split(',');
        arrImg.forEach(function(file){
            const fileArr = file.split("/");
            const filename = fileArr[fileArr.length - 1];
            const ext = filename.split(".");

            var avatarImage = baseUrl("uploads/files/images/employee_files/empcode_"+ created_by +"/ticketing/" + filename);
            var renderImage = vmData['result'].picture;
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
                        ' </button>'+
                        ' <button ' +
                        ' type="button" ' +
                        ' onclick="removeDocument(this,\''+ filename +'\')"'+
                        ' class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView" data-name="'+filename+'">'+
                        ' <i class="la la-times"></i>'+
                        ' </button>'
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
       
        
        

        var type = new Option(vmData['result'].type, vmData['result'].type, true, true);
        $('#type').append(type).trigger('change');

        var department = new Option(vmData['result'].description, vmData['result'].department_id, true, true);
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
});

var sel = $("#department");
$.ajax({
	type: 'GET',
	url: baseUrl("ts/ticketing/get_user_emp_data"),
	dataType: 'JSON'
}).then(function (json) {
	if (typeof json.allow_search_employee !== "undefined" && json.allow_search_employee == true) {
		sel.select2({
			placeholder: 'SELECT DEPARTMENT',
			width: '100%',
			ajax: {
				url: baseUrl("ts/ticketing/get_department_collection"),
				dataType: "json",
				delay: 250,
				global: false,
				processResults: function (data) {
					return data;
				}
			}
		});
	}
});

sel.on("change", function (e) {
	var currentObject = $(e.target);
	currentObject.validate();
});

$('#need_dt_group').datetimepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    format: 'yyyy/mm/dd',
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

var fileUploadPhoto = function () {
    var url = baseUrl("ts/ticketing/upload_ticket_photo");

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
                    images.push(result.display_filename);
                    $("#picture").attr("src", renderImage);
                    $("#pic").val(images);
                    let ext = renderImage ? renderImage.split(".") : "";
                    let icon = '';
                    let color = '';
                    ext = ext[ext.length - 1];
                    allowedFileTypes.forEach((item, i) => {
                        if (item._type.includes(ext)) {
                            icon = item.icon;
                            color = item.color;
                        }
                    });
                    const icon_path = baseUrl('assets/images/file_icons/' + icon);
                    let previewButton = '';
                        if (result.file_extension == 'docx') {
                            previewButton = '';
                        } else{
                            previewButton = '' +
                                ' <button '+
                                    ' title="Preview" ' +
                                    ' type="button"' +
                                    ' onclick="previewDocument(\'' + avatarImage + '\', \'' + icon + '\', \'' + result.display_filename + '\')"' +
                                    ' class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnRemove" id="previewFile"> '+
                                    ' <i class="la la-eye"></i>' +
                                ' </button>';
                        }
                    const uploadlist = ' ' + 
                            '<div class="m-widget2">'+
                            '<div class="m-widget2__item m-widget2__item--'+ color +'">' +
                                '<div class="m-widget2__checkbox">'+
                                    '<div class="m-widget2__img m-widget2__img--icon">'+
                                    '<img src='+ icon_path +' width="45" alt>' +
                                    '</div>'+
                                '</div>'+
                                '<div class="m-widget2__desc">'+
                                    '<span class="m-widget2__user-text">'+
                                    ''+
                                    '</span><br>'+
                                    '<span class="m-widget2__user-name">'+
                                    result.display_filename +
                                    '</span>'+
                                    '<span class="m-widget2__user-name">'+
                                    '</span><br><br>'+
                                '</div>' +
                                '<div class="m-widget2__actions">' +
                                   ''  + previewButton +
                                    ' <button ' +
                                    ' type="button" ' +
                                    ' onclick="removeFile('+param_id+')"'+
                                    ' class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView" data-name="'+result.display_filename+'">'+
                                    ' <i class="la la-times"></i>'+
                                    ' </button>'+
                                '</div>'+
                            '</div>'+
                            '</div>';
                    $("#uploaded_files").append(uploadlist);
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
            url: baseUrl("ts/ticketing/update_ticket/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#frm_status_new").find("input,select,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    toastr.success("Ticket was successfully updated.", "Ticket Updated.", 5000)
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

function removeFile(el){
    

    
}

function removeDocument(el,filename){
    $("#remove-file-confirmation-modal").modal("show");

    $("#frm-remove-file").submit(function(e){
        e.preventDefault();
        $.ajax({
            url: baseUrl("ts/ticketing/remove_file"),
            type: 'POST',
            dataType: "json",
            data: {
                csrf_token : _csrf_hash,
                filename : filename
            },
            success: function (response) {
                if (response.result) {
                    const _name = $(el).attr("data-name");
                    const parent = $(el).closest('.m-widget2__item');
                    images = images.filter((n) => {return n != _name});
                    $(parent).remove();
                    $("#pic").val(images);

                    $("#remove-file-confirmation-modal").modal("hide");
                    toastr.success(_name,"Removed File", 5000);
                }
            }
        });
    });
    
}
