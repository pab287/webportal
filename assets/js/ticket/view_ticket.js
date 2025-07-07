
getUrlParameter = function getUrlParameter(sParam) {
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

jQuery(document).ready(function () {
    if (typeof _tempContentData !== 'undefined' && _tempContentData && _tempContentData.status) {
        Swal.fire({
            title: "Ticket Status",
            text: _tempContentData.message,
            icon: 'success',
            confirmButtonText: 'Continue',
            allowOutsideClick: false,
            timer: 3000,
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        }).then((result) => {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.delete('serve');
            window.location.replace(currentUrl.toString());
        });
    }
    else if(typeof _tempContentData !== 'undefined' && _tempContentData && _tempContentData.rate && _tempContentData.rate == 1) {
        Swal.fire({
            title: 'HOW WOULD YOU RATE OUR SERVICE?',
            html: `<div style="margin: 20px 0; text-align: center;">
                        <div id="stars" style="font-size: 30px; margin-bottom: 20px;">
                            <span class="star" data-rating="1" title="Terrible">☆</span>
                            <span class="star" data-rating="2" title="Poor">☆</span>
                            <span class="star" data-rating="3" title="Average">☆</span>
                            <span class="star" data-rating="4" title="Good">☆</span>
                            <span class="star" data-rating="5" title="Excellent">☆</span>
                        </div>
                        <textarea id="feedback" placeholder="FEEDBACK..." style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solidrgb(0, 0, 0);"></textarea>
                    </div>`,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showCloseButton: false,
            confirmButtonText: 'Submit Rating',
            preConfirm: () => {
                const rating = document.querySelector('.star.active') ? 
                document.querySelector('.star.active').getAttribute('data-rating') : null;
                if (!rating) {
                    Swal.showValidationMessage('Please select a rating');
                    return false;
                }
                return {rating: rating};
            },
        }).then((result) => {
            const ratingData = result.value;
            console.log('Rating submitted:', ratingData);
        });

        const stars = document.querySelectorAll('.star');
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                highlightStars(rating);
            });
        });

    }

    function highlightStars(rating) {
        const stars = document.querySelectorAll('.star');
        stars.forEach(star => {
            star.style.color = '#FFC000';
            star.textContent = '☆';
            star.classList.remove('active');
            if (parseInt(star.getAttribute('data-rating')) <= rating) {
                star.textContent = '★';
                if (parseInt(star.getAttribute('data-rating')) === rating) {
                    star.classList.add('active');
                }
            }
        });
    }

    $("#progress").hide();
    $("#reopen_field").hide();
    $("#onhold_field").hide();
    $("#webportal").hide();
});

let images = [];
function getTicketDetails(){
    $.ajax({
        url: baseUrl("ticket/ticket/ticket_details/") + param_id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            if (data.status !== 'Closed') {
                $(".fileinput-button").css("display", "inline-block");
            }
    
            const vmData = data;
            
            $("#ticket_id").val(param_id);
    
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
            const picUrl = vmData.attachment ? baseUrl("uploads/files/images/employee_files/" + vmData.attachment) : baseUrl('assets/images/ams/images/no_image.jpg');
            const created_by = vmData.requestor;
            const arrImg = vmData.attachment.split(',');
            arrImg.forEach(function(file){
                const fileArr = file.split("/");
                const filename = fileArr[fileArr.length - 1];
                const ext = filename.split(".");
    
                var avatarImage = baseUrl("uploads/files/images/employee_files/empcode_"+ created_by +"/ticketing/" + filename);
                var renderImage = vmData.picture;
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
                            '<img src="'+icon_path+'" width="35" alt>' +
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
    
            if (vmData.emp_id) {
                var performed_by = new Option(vmData.performed_by_det, vmData.performed_by, true, true);
                $('#performed_by').append(performed_by).trigger('change');
            }
    
            // let _status = null;
            // switch (vmData.status) {
            //     case 'Closed':
            //         _status = 'COMPLETED';
            //         break;
            //     case 'Confirmed':
            //         _status = 'RESOLVED'
            //         break;
            //     default:
            //         _status = vmData.status;
            // }
    
            var status = new Option(vmData.status, vmData.status, true, true);
            $('#status').append(status).trigger('change');
    
            var type = new Option(vmData.type, vmData.type, true, true);
            $('#type').append(type).trigger('change');
    
            var department = new Option(vmData.description, vmData.department_id, true, true);
            $('#department').append(department).trigger('change');
    
            // if($rs->priority == 'low'){
            //     $priority = "<span class='m-badge m-badge--info m-badge--wide text-white'><strong>".$rs->priority."</strong></span>";
            // }elseif($rs->priority == 'medium'){
            //     $priority = "<span class='m-badge m-badge--warning m-badge--wide text-white'><strong>".$rs->priority."</strong></span>";
            // }else{
            //     $priority = "<span class='m-badge m-badge--danger m-badge--wide text-white'><strong>".$rs->priority."</strong></span>";
            // }
    
            if(vmData.priority == 'low'){
                vmData.priority = "<span class='m-badge m-badge--info m-badge--wide text-white'>"+vmData.priority+"</span>";
            }else if(vmData.priority == 'medium'){
                vmData.priority = "<span class='m-badge m-badge--warning m-badge--wide text-white'>"+vmData.priority+"</span>";
            }else{
                vmData.priority = "<span class='m-badge m-badge--danger m-badge--wide text-white'>"+vmData.priority+"</span>";
            }
    
            if(vmData.status == 'completed'){
                vmData.status = "<span class='m-badge m-badge--success m-badge--wide text-white'>"+vmData.status+"</span>";
            }else if(vmData.status == 'open'){
                vmData.status = "<span class='m-badge m-badge--brand m-badge--wide text-white'>"+vmData.status+"</span>";
            }else{
                vmData.status = "<span class='m-badge m-badge--metal m-badge--wide text-white'>"+vmData.status+"</span>";
            }
            vmTab1.vm_tab1 = Object.assign({}, data);
            getComments();
            getStatusLogs();
            getRating();
        },
    });

}


$('#view-ticket-modal').on('show.bs.modal', function (e) {
    var ticketId = $(e.relatedTarget).data('ticket-id');
    param_id = ticketId;
    getTicketDetails();
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


var vmTab1 = new Vue({
    el: "#frm_status_new",
    data: {vm_tab1: {}},
    mounted: function () {
        setTimeout(function (data) {
            var vmData = this.vmTab1.vm_tab1;
        }, 400);
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

function getStatusLogs(){
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
}
