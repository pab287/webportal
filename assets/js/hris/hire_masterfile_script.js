var modalWindow = $("#modalTempContent");
var tableHireList = $("#table-hire");
var newHireEmployeePage = $(".hris.transaction-hire_new_employee");
var search_val = "";
if(screen.width > 560 && screen.width < 960){
    $("#frmAddEmployeeData label").addClass("text-right");
}
if (typeof tableHireList !== "undefined") {
    var search_val = "";
    var dtHire = tableHireList.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("hris/transaction/get_hire_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        columns: [
            {data: "position", width: "40%"},
            {data: "requested_by", width: "22%"},
            {data: "needed", width: "10%", className: "text-center"},
            {data: "needed_date", width: "10%", className: "text-center"},
            {data: "overdue", width: "10%", className: "text-center"},
            {data: null, width: "8%", className: "text-center"}
        ],
        columnDefs: [{
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return tempDataTableActions(row.id, row.status);
            }
        }, {
            targets: "_all",
            defaultContent: ""
        }]
    });

    function tempDataTableActions($id, $status) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                "<button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnNew btnAddNewHire' " +
                "   data-id='" + $id + "' " +
                "   data-toggle='m-tooltip'" +
                "   data-original-title='Add Employee'" +
                "   data-skin='dark'" +
                "   data-placement='bottom'" +
                "   data-delay='{\"show\": 300}'>" +
                "   <i class='la la-user-plus'></i>" +
                "</button>";

            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnNew btnExternalLink' " +
                "   data-id='" + $id + "' " +
                "   data-toggle='m-tooltip'" +
                "   data-original-title='Internal Link'" +
                "   data-skin='dark'" +
                "   data-placement='bottom'" +
                "   data-delay='{\"show\": 300}'>" +
                "   <i class='fa fa-external-link'></i>" +
                "</button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtHire.ajax.reload();
    });

    $(document).on("click", ".btnAddNewHire", function () {
        var dataId = $(this).data("id");
        window.location.href = baseUrl("hris/transaction/hire_employee/" + dataId);
    });

    $(document).on("click", ".btnExternalLink", function () {
        var dataId = $(this).data("id");
        window.open(baseUrl("hris/employee_fillout/index?id=")+dataId);
    });
}
if (typeof newHireEmployeePage !== "undefined") {
    jQuery(document).ready(function () {
        var dtPickerBirthDate = $("#frmAddEmployeeData").find("#bday").datepicker({
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            },
            format: "yyyy-mm-dd",
            autoclose: true
        })
            .on("changeDate", function (e) {
                var currentDt = moment(e.date).format("YYYY-MM-DD");
                var self = $(e.target);
                self.validate();
            });
        fileUploadPhoto();
        validatePersonalEmployeeData();
        employee_document_upload();
        $('#firstname').donetyping(function () {
            const firstname = $(this).val();
            const middlename = $("#middlename").val();
            const lastname = $("#lastname").val();
            if (lastname && middlename) {
                searchEmployeeHire(firstname, middlename, lastname);
            } else {
                $('.search-with-dropdown-suggestion-list').addClass('invisible');
            }
        });   

        $('#middlename').donetyping(function () {
            const firstname = $("#firstname").val();
            const middlename = $(this).val();
            const lastname = $("#lastname").val();
            if (firstname && lastname) {
                searchEmployeeHire(firstname, middlename, lastname);
            } else {
                $('.search-with-dropdown-suggestion-list').addClass('invisible');
            }
        });   

        $('#lastname')
            .donetyping(function () {
                const firstname = $("#firstname").val();
                const middlename = $("#middlename").val();
                const lastname = $(this).val();
                if (lastname) {
                    searchEmployeeHire(firstname, middlename, lastname);
                } else {
                    $('.search-with-dropdown-suggestion-list').addClass('invisible');
                }
            });   

        $('#lastname')
            .on('focus', function () {
                const firstname = $("#firstname").val();
                const middlename = $("#firstname").val();
                const lastname = $(this).val();
                setTimeout(() => {
                    if (lastname) {
                        $('.search-with-dropdown-suggestion-list').removeClass('invisible');
                    }
                }, 300);
            });

    });

    function searchEmployeeHire(firstname, middlename, lastname) {
        $.ajax({
            url: baseUrl('hris/transaction/search_employee'),
            type: 'POST',
            dataType: 'JSON',
            data: {
                csrf_token: _csrf_hash,
                firstname : firstname,
                middlename : middlename,
                lastname: lastname
            },
            success: function (response) {
                const container = $('ul.employee-suggestion');
                container.html('');
                if (response.length <= 0) {
                    const li = '' +
                        '<li style="min-height: 50px;">' +
                        '    <div class="d-flex flex-column">' +
                        '        <p style="font-weight: normal; text-transform: none;" ' +
                        '           class="m-0 title">No matching record found.</p>' +
                        '    </div>' +
                        '</li>';
                    container.append(li);
                    $(".btn-submit").prop("disabled", false);
                    
                    $("#emp_exist_notif").addClass('m--hide');
                } else {
                    response.forEach((employee) => {
                        // const imageUrl = baseUrl('uploads/files/images/employee_files/empcode_' + employee.id + '/' + employee.pic_filename);
                        const imageUrl = employee.image;
                        const li = '' +
                            '<li onclick="openEmployeeDataSheet(' + employee.id + ')">' +
                            '   <div class="avatar" style="background-image: url(\'' + imageUrl + '\')"></div>' +
                            '   <div class="d-flex align-items-start flex-column pl-4" style="flex: 1;">' +
                            '       <p class="m-0 title">' + employee.employee_name + '</p>' +
                            '       <p class="m-0 text-muted">' + employee.company + '</p>' +
                            '       <p class="m-0 text-muted">' + employee.position + '</p>' +
                            '   </div>' +
                            '</li>';
                        container.append(li);
                    });
                    $(".btn-submit").prop("disabled", true);
                    
                    $("#emp_exist_notif").removeClass('m--hide');
                }
                $('.search-with-dropdown-suggestion-list').removeClass('invisible');
            }
        });
    }

    var validatePersonalEmployeeData = function () {
        $.validate({
            form: "#frmAddEmployeeData",
            lang: "en",
            onSuccess: function (form) {
                var currentForm = form[0];
                var formUrl = currentForm.action;
                var formData = $(currentForm).serialize();

                $.ajax({
                    url: formUrl,
                    type: "post",
                    dataType: "json",
                    data: formData,
                    beforeSend: function () {
                        $(currentForm)
                            .find(".btn-submit")
                            .addClass("m-btn--custom m-loader m-loader--light m-loader--right")
                            .prop("disabled", true);
                    },
                    success: function (json) {
                        if (json.response) {
                            toastr.success(
                                json.toastr_msg,
                                "Employee data has been added.",
                                5000
                            );
                            window.location.href = baseUrl("hris/masterfile/edit_employee_masterfile/" + json.id);
                        } else {
                            toastr.error(
                                json.toastr_msg,
                                "Error adding employee data!",
                                5000
                            );
                        }

                        $(currentForm)
                            .find(".btn-submit")
                            .removeClass(
                                "m-btn--custom m-loader m-loader--light m-loader--right"
                            ).prop("disabled", false);
                    }
                });
                return false;
            }
        });
    }

    var fileUploadPhoto = function () {
        var url = baseUrl("hris/masterfile/temp_upload_employee_avatar");
        $("#fileupload")
            .fileupload({
                url: url,
                dataType: "json",
                formData: {csrf_token: _csrf_hash},
                done: function (e, data) {
                    var result = data.result;
                    if (result.response) {
                        var avatarImage = result.added_image;
                        var tempImage = result.temp_image;

                        uploadVM.left_pane = Object.assign({}, {display_avatar: avatarImage});
                        vmTab1.vm_tab1 = Object.assign({}, {pic_filename: tempImage});

                        toastr.success(result.toastr_msg, "Upload Image", 5000);
                        $("#modalUpdatePhoto").modal("hide");
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

    var uploadVM = new Vue({
        el: "#left_pane-card",
        data: {left_pane: {display_avatar: baseUrl("assets/images/profile/no_image.jpg")}}
    });

    var tempData = _tempContentData;
    var vmTab1 = new Vue({
        el: "#frmAddEmployeeData",
        data: {
            vm_tab1: {
                pic_filename: "",
                is_hiring: true,
            },
            vm_hire: tempData,
            vm_crs: {}
        },
        methods: {
        },
    });
}

function openEmployeeDataSheet(emp_id) {
    const url = baseUrl('hris/masterfile/view_employee_masterfile/' + emp_id);
    window.location.assign(url);
}
let val = [];
var employee_document_upload = function(){
    var url = baseUrl("hris/masterfile/temp_employee_document_upload");
    // var emp_id = $("#emp_id").val();
    var doc_type = $("#clearance_type").val();
    
    var files = [];
    $("#documentupload")
      .fileupload({
        url: url,
        dataType: "json",
        formData: { csrf_token: _csrf_hash, doc_type : doc_type},
        done: function(e, data) {
            var result = data.result;
            if (result.response) {
                
                if(result.extension !== "pdf"){
                    toastr.error(result.toastr_msg, "File format not allowed.", 1000);
                    return false;
                }else{
                    var filePath = result.added_file;
                    var renderFile = result.render_file;
                    $("#file_append").text(renderFile);
                    $("#path").val(filePath);
                    $("#filename").val(renderFile);
                    $("#picture").attr("src", "");
                    
                    val.push(renderFile);
                }
                var doc_name = "";
                $.each(val, function(i, value){
                    doc_name +="<div class='alert alert-success alert-small alert-dismissible fade show m-alert m-alert--square m-alert--air doc_badge' role='alert' style='text-overflow: ellipsis; white-space: nowrap; overflow: hidden; height: 30px; padding-top: 5px; border-radius: 5px;'><button type='button' class='close' data-dismiss='alert' onclick='removeDocument("+i+")' aria-label='Close'></button>"+value+"</div>";
                });
                if(val.length > 0){
                    $("#document_names").val(val);
                }else{
                    $("#document_names").val();
                }
                $("#picture").html(doc_name);
            } else {
              toastr.error(result.toastr_msg, "File error", 5000);
            }
        }
        
    });
  }

  function removeDocument(key){
    $("#document_names").val("");
    var removeItem = key
    val.splice($.inArray(removeItem, val), 1);
    
    if(val.length > 0){
        $("#filename").val();
    }
  }

//   hire from crs
function crsModal(){
    $("#crs-modal").modal();
    $("#crs-applicants").val("").trigger('change');

    $("#crs-applicants").select2({
        destroy: true,
        width: '100%',
        placeholder: 'Select an Option',
        dropdownParent: $('#crs-modal'),
        ajax:{
            url: baseUrl('hris/transaction/get_applicants'),
            dataType: 'JSON',
            type: 'GET',
            global: false,
            delay: 250,
            processResults: function (data) {
                return { results: data };
            }
        }
    }).on('select2:select', function(e){
        const data = e.params.data;
        $("#submit-selected").attr('onclick', 'getApplicant('+data.id+')');
    });
}

function getApplicant(id){
    $.ajax({
        url: baseUrl('hris/transaction/get_selected_applicant'),
        type: "GET",
        dataType: "JSON",
        data: {
            id: id
        },
        beforeSend: function(){
            $(this).addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function(data){
            if(data){
                var json = data.data;
                vmTab1.vm_crs = Object.assign({}, json);

                if(json.civil_stat){
                    var selected_civil = new Option(json.civil_stat, json.civil_stat, true, true);
                    $("#civil_stat").append(selected_civil).trigger('change');
                }

                if(json.gender){
                    $("#gender[value=" + json.gender + "]"). prop('checked', true);
                }

                $("#crs-modal").modal('hide');
            }else{
                toastr.error('No data found.', 'HRIS Hire');
            }
        }
    });

}
//   hire from crs

