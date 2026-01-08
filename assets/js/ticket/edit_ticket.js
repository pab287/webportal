let performed_by = [];
let getUrlParameter = function getUrlParameter(sParam) {
    let sPageURL = decodeURIComponent(window.location.search.substring(1)),
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
let requested_id = 0;
let filenames ="";
let element = "";
jQuery(document).ready(function () {
    fileUploadPhoto();
    $("#progress").hide();
    $("#webportal").hide();
    $("#dept-res").hide();
});

$('#date_required_group').datepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    format: 'yyyy-mm-dd hh:mm',
});

let arrImg = [];
let images = [];
$.ajax({
    url: baseUrl("ticket/ticket/ticket_details/") + param_id,
    type: "GET",
    dataType: "JSON",
    success: function (data) {
        getComments();
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
        
        $("#ticket_id").val(param_id);

        $("#issue").val(vmData.message);
        $("#date_required").val(moment(vmData.requested_date).format("MMMM D, YYYY hh:mm A"));
        $("#requested_by").val(vmData.requested_by);

        const picUrl = vmData.attachment ? baseUrl("uploads/files/images/employee_files/" + vmData.attachment) : baseUrl('assets/images/ams/images/no_image.jpg');
        const requestor = vmData.requestor;
        requested_id = requestor;
        arrImg = vmData.attachment.split(',');
        arrImg.forEach(function(file){
            const fileArr = file.split("/");
            const filename = fileArr[fileArr.length - 1];
            const ext = filename.split(".");

            let avatarImage = baseUrl("uploads/files/images/employee_files/empcode_"+ requestor +"/ticketing/" + filename);
            let renderImage = vmData.attachment;
            images.push(filename);
            const filename1 = filename;
            const shortenedName = filename1.length <= 20 ? filename1 : `${filename1.slice(0, 20)}...`;
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
            var viewButton = '';
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
                        shortenedName +
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
       
        
        vmTab1.vm_tab1 = Object.assign({}, data);

        const categories = (vmTab1.vm_tab1.category || '').toLowerCase();
        console.log(categories);
        if (categories === "payroll") {
            performed_by = _tempContentData.performed_by_payroll;
        }
        else if (categories === "qms") {
            performed_by = _tempContentData.performed_by_qms;
        }
        else {
            performed_by = _tempContentData.performed_by;
        }
        console.log(performed_by);
        $("#performed_by").select2({
            width: "100%",
            placeholder: "Select an option",
            data: performed_by,
            allowClear: true,
        });
        
        $("#department").select2({
            width: "100%",
            placeholder: "Select an option",
            data: _tempContentData.department,
            allowClear: true,
        });
        
        $("#category").select2({
            width: "100%",
            placeholder: "Select an option",
            data: _tempContentData.category,
            allowClear: true,
        });
        let reqq;
        $("#sub_category").select2({
            width: "100%",
            placeholder: "Select an option",
            data: _tempContentData.subcategory,
            allowClear: true,
        });
        if(data.performed_by_id != 0){
             reqq = data.performed_by_id
        }else{
             reqq = null
        }
        $("#category").on("change", function (e) {
            let type = ($("#category option:selected").text() || "").toLowerCase();
            if(type == 'webportal'){
                $("#webportal").show();
            }else{
                $("#webportal").hide();
            }
            if(type == 'software'){
                $("#dept-res").show();
            }else{
                $("#dept-res").hide();
            }
            $("#performed_by").select2("destroy");
            $("#performed_by").empty();
            if(type == 'payroll'){
                $("#performed_by").select2({
                    width: "100%",
                    placeholder: "Select an option",
                    data:  _tempContentData.performed_by_payroll,
                    allowClear: true,
                }).val(reqq).trigger('change');
            }
            else if(type == 'qms'){
                $("#performed_by").select2({
                    width: "100%",
                    placeholder: "Select an option",
                    data:  _tempContentData.performed_by_qms,
                    allowClear: true,
                }).val(reqq).trigger('change');
            }
            else{
                $("#performed_by").select2({
                    width: "100%",
                    placeholder: "Select an option",
                    data:  _tempContentData.performed_by,
                    allowClear: true,
                }).val(reqq).trigger('change');
            }
        });

        if(!_currentActions.includes('can_perform_ticket')){
            $("#performed_by").select2("destroy");
            $("#performed_by").empty();
            $("#performed_by_block").remove();
        }
        
        if(data.status == 'open'){
            _tempContentData.status = [{'text': 'open', 'id': 'open'},{'text': 'In Progress', 'id': 'in progress'}];
        }

        console.log(data);

        $("#status").select2({
            width: "100%",
            placeholder: "Select an option",
            data: _tempContentData.status,
            allowClear: true,
        });
        
        $("#severity").select2({
            width: "100%",
            placeholder: "Select an option",
            data: _tempContentData.severity,
            allowClear: true,
        });

        $("#responsibility").select2({
            width: "100%",
            placeholder: "Select an option",
            data: _tempContentData.responsibility,
            allowClear: true,
        });

        let category = new Option(vmData.category, vmData.category, true, true);

        
        if(vmData.category == 'webportal'){
            $("#webportal").show();
            $('#sub_category').val(data.sub_category).trigger('change');
        }
        if(vmData.category == 'software'){
            $("#dept-res").show();
            $('#responsibility').val(data.responsibility).trigger('change');
        }
        $('#status').val(data.status).trigger('change');
        $('#category').val(data.category).trigger('change');
        $('#severity').val(data.severity_id).trigger('change');
        $('#department').val(data.department_id).trigger('change');
    }
});

var vmTab1 = new Vue({
    el: "#frm_status_new",
    data: {vm_tab1: {}},
    mounted: function () {
        setTimeout(function (data) {
            var vmData = this.vmTab1.vm_tab1;
        }, 1000);
    }
});

var vmTabComments = new Vue({
    el: "#frm_comments",
    data: {vm_tab2: {}},
    mounted: function () {
        setTimeout(function (data) {
            var vmData = this.vmTabComments.vm_tab2;
        }, 400);
    }
});

function getComments(){
    $.ajax({
        url: baseUrl("ticket/ticket/get_comments/") + param_id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            const vmData = data;
            if(data){
                vmTabComments.vm_tab2 = Object.assign({}, data);
            }else{
                vmTabComments.vm_tab2 = null;
            }
        }
    });
}

$.validate({
    form: '#frm-add-comment',
    lang: 'en',
    onSuccess: function (form) {
        var comment = $('#comment').val().trim();
        if (!comment) {
            toastr.error("Comment cannot be empty or contain only spaces.", "Validation Error!", 5000);
            return false; 
        }
        $.ajax({
            url: baseUrl("ticket/ticket/add_comment"),
            type: "POST",
            dataType: "json",
            data: $("#frm-add-comment").find("input,textarea").serialize(),
            success: function (data) {
                if (data) {
                    $('#comment').val('');
                    toastr.success("Comment successfully saved.", "Saved.", 5000);
                    getComments();
                } else {
                    toastr.error(data.toastr_msg, "Notice: Error!", 5000);
                }
            }
        });
        return false;
    },
});

// $('#need_dt_group').datetimepicker({
//     todayHighlight: true,
//     autoclose: true,
//     pickerPosition: 'bottom-left',
//     todayBtn: true,
//     format: 'yyyy-mm-dd hh:mm tt',
// }).on("changeDate", function (e) {
//     var currentDt = moment(e.date).format("yyyy-mm-dd hh:mm");
//     var self = $(e.target);
//     self.validate();
// });

$('#date_required').datetimepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    format: 'MM dd, yyyy HH:ii P',
    showMeridian: true,
}).on("changeDate", function (e) {
    var formattedDate = moment(e.date).format('YYYY-MM-DD HH:mm');
    $(this).val(formattedDate);
});

let fileUploadPhoto = function () {
    var url = baseUrl("ticket/ticket/upload_ticket_photo");

    const allowedFileTypes = [
        {
            _type: ["jpg", "jpeg", "png", "PNG", "JPEG", "JPG"],
            icon: "jpg.svg",
            color: "success"
        },
        // {
        //     _type: ["docx", "DOCX"],
        //     icon: "doc.svg",
        //     color: "info"
        // },
        {
            _type: ["pdf", "PDF"],
            icon: "pdf.svg",
            color: "danger"
        },
    ];

    $('#fileupload').on('change', function(e) {
        let valid = true;
        let errorMessage = '';
        
        $.each(e.target.files, function(index, file) {
            // Get filename without extension
            const fileName = file.name.substring(0, file.name.lastIndexOf('.'));
            
            // Check for special characters
            if (!/^[a-zA-Z0-9\s._-]+$/g.test(fileName)) {
                valid = false;
                errorMessage = 'File "' + file.name + '" contains special characters. Please rename the file without special characters.';
                return false; // Break the loop
            }
        });

        if (!valid) {
            toastr.error(errorMessage, "Upload Image", 5000);
            $(this).val(''); // Clear input
            return false;
        }
    });

    $("#fileupload")
        .fileupload({
            url: url,
            dataType: "json",
            formData: {csrf_token: _csrf_hash, ticket_id: param_id},
            done: function (e, data) {
                var result = data.result;
                if (result.response) {
                    var avatarImage = result.added_image;
                    var renderImage = result.render_image;
                    images.push(result.display_filename);
                    const filename = result.display_filename;
                    const shortenedName = filename.length <= 20 ? filename : `${filename.slice(0, 20)}...`;
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
                                    ' class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnRemove" type="button" id="previewFile"> '+
                                    ' <i class="la la-eye"></i>' +
                                ' </button>';
                        }
                        const uploadlist = `
                            <div class="m-widget2">
                                <div class="m-widget2__item m-widget2__item--${color}">
                                    <div class="m-widget2__checkbox">
                                        <div class="m-widget2__img m-widget2__img--icon">
                                            <img src="${icon_path}" width="45" alt>
                                        </div>
                                    </div>
                                    <div class="m-widget2__desc">
                                        <span class="m-widget2__user-text"></span><br>
                                        <span class="m-widget2__user-name">${shortenedName}</span>
                                        <span class="m-widget2__user-name"></span><br><br>
                                    </div>
                                    <div class="m-widget2__actions">
                                        ${previewButton}
                                        <button type="button"
                                                onclick="removeDocument(this, '${result.display_filename}')"
                                                class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView"
                                                data-name="${result.display_filename}">
                                            <i class="la la-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>`;
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
            url: baseUrl("ticket/ticket/update_ticket/") + param_id,
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
                        window.location.assign(baseUrl("ticket/tickets"));
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
    alert('asdas');

    
}

function removeDocument(el, filename) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl("ticket/ticket/remove_file"),
                type: 'POST',
                dataType: "json",
                data: {
                    csrf_token : _csrf_hash,
                    filename : filename,
                    ticket_id : param_id,
                    requested_id : requested_id,
                },
                success: function (response) {
                    if (response.result) {
                        const _name = $(el).attr("data-name");
                        const parent = $(el).closest('.m-widget2__item');
                        images = images.filter((n) => {return n != _name});
                        $(parent).remove();
                        $("#pic").val(images);
                        console.log(images)
                        $("#remove-file-confirmation-modal").modal("hide");
                        toastr.success(_name,"Removed File", 5000);
                    } else {
                        toastr.error("File not found","Error", 5000);
                    }
                }
            });
        }
    });
}

function delete_comment(id){
    Swal.fire({
        title: "Comments",
        text:'Are you sure you want to remove this comment?!',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Remove it!'
      }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl("ticket/ticket/remove_actionstkn"),
                type: 'POST',
                dataType: "json",
                data: {
                    csrf_token : _csrf_hash,
                    id : id
                },
                success: function (response) {
                    getComments();
                    toastr.success("Success", "Deleted.", 5000);
                }
            })
        }
      });
}

let statuslog = new Vue({
    el: "#status-log",
    data: {trail: null},
    mounted: function () {
        $.ajax({
            url: baseUrl("ticket/ticket/get_trail_log/") + param_id,
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                if (response) {
                    statuslog.trail =  response.data;
                } else {
                    statuslog.trail = null;
                }
            }
        })
    }
});
