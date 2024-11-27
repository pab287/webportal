var url = baseUrl("hris/masterfile/import_csv");
var my_columns = [];
$("#fileupload")
    .fileupload({
        url: url,
        dataType: "json",
        formData: {csrf_token: _csrf_hash,},
        success: function (data) {
            
            if (data.response) {
                toastr.success(data.toastr_msg, "Import Employee Data", 5000);

                $.each( data.data[0], function( key, value ) {
                    var my_item = {};
                    my_item.data = key;
                    my_item.title = key;
                    my_columns.push(my_item);
                });
            
                $('#table-employee_import').DataTable({
                    data: data.data,
                    "columns": my_columns
                });

            } else {
                toastr.error(data.toastr_msg, "Import Employee Data", 5000);
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


