var dtImports = $('#table-import').DataTable({
    paging: false
});

$("#projectSelect").select2({
    placeholder: 'SELECT Project',
    width: '100%',
    ajax: {
      url: baseUrl("eforms/tripping/get_projects_report"),
      global: false,
      processResults: function (data) {
        return data;
      }
  
    }
});

$("#driverSelect").select2({
    placeholder: 'SELECT Driver',
    width: '100%',
    ajax: {
      url: baseUrl("eforms/tripping/get_driver_select2"),
      global: false,
      processResults: function (data) {
        return data;
      }
  
    }
});

$("#originSelect").select2({
    placeholder: 'SELECT Origin',
    width: '100%',
    ajax: {
      url: baseUrl("eforms/tripping/get_routes_Code"),
      global: false,
      processResults: function (data) {
        return data;
      }
  
    }
});

$("#destinationSelect").select2({
    placeholder: 'SELECT Destination',
    width: '100%',
    ajax: {
      url: baseUrl("eforms/tripping/get_routes_Code"),
      global: false,
      processResults: function (data) {
        return data;
      }
  
    }
});



var FileUpload = function () {
    var url = baseUrl("eforms/tripping/temp_upload_file");
    $("#temp_fileupload")
        .fileupload({
            url: url,
            dataType: "json",
            formData: { csrf_token: _csrf_hash },
            success: function (data) {
                if (data.response) {
                    toastr.success(data.toastr_msg, "Upload File", 5000);

                    dtImports.rows.add(data.csv).draw();
                } else {
                    toastr.error(data.toastr_msg, "Upload File", 5000);
                }
            },
            progressall: function (e, data) {
                $("#progress_approve").show();
                var progress = parseInt((data.loaded / data.total) * 100, 10);
                var progressTotal = 0;

                var steps = setInterval(function () {
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
                    }, 1000);
                }
            }
        })
        .prop("disabled", !$.support.fileInput)
        .parent()
        .addClass($.support.fileInput ? undefined : "disabled");
};


$.validate({
    form: '#frmDownloadTemplate',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
          url: $("#frmDownloadTemplate").attr('action'),
          type: "POST",
          data: $('#frmDownloadTemplate').serialize(),
          dataType: "JSON",
          success: function (data) {
                $("#m_modal_downloadtemplateLink").modal("show");
              $(".link").attr("href",baseUrl("uploads/files/trippings/template/template_for_import_trppings.csv"));
              
          }
        });
        return false;
    },
  });


$(document).ready(function(){
    FileUpload();
});