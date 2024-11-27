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
    $("#reopen_field").hide();
    $("#webportal").hide();
});
let images = [];
$.ajax({
    url: baseUrl("ts/ticketing/service_details/") + param_id,
    type: "GET",
    dataType: "JSON",
    success: function (data) {
        const vmData = data;
        if (data.type == "WEBPORTAL") {
            $("#webportal").show();
        }

        if (data.open) {
            $("#reopen_field").show();
        } else {
            $("#reopen_field").hide();
        }

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
