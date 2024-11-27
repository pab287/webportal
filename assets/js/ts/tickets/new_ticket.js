jQuery(document).ready(function () {
    $("#progress").hide();
    fileUploadPhoto();
    $("#webportal").hide();
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
	if (json.response) {
		setTimeout(function () {
			var option = new Option(json.text, json.id, true, true);
			sel.append(option).trigger('change');
		}, 150);
	}
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

$('#need_dt').datetimepicker({
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
        id: "",
        text: ""
    },
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
    },
    {
        id: "PAYROLL SYSTEM (PS)",
        text: "PAYROLL SYSTEM (PS)"
    },
    {
        id: "TIME MANAGEMENT (TIME)",
        text: "TIME MANAGEMENT (TIME)"
    }
];

$("#module").select2({
    placeholder: "Please select and option",
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
                    toastr.success("Ticket was successfully saved.", "New Ticket Saved.", 5000)
                    setTimeout(() => {
                        window.location.assign(baseUrl("ts/ticketing/masterfile"));
                    }, 700);
                } else {
                    toastr.error(data.toastr_msg, "Notice: Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});
let images = [];
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
                                    ' onclick="removeDocument(this)"'+
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

$('body, .modal-body')
    .tooltip({
        selector: '[title]',
        skin: "dark",
        delay: {
            show: 500
        },
        placement: "top"
    });
  
