var modalWindow = $("#modalTempContent");
var tablePersonnelRequestList = $("#table-personnel_request");
var tablePersonnelRequestList_completed = $("#table-personnel_request_completed");
var search_val = "";
var editPersonnelRequestPage = $(".hris.edit-personnel_request");
var approvalPersonnelRequestPage = $(".hris.approval-personnel_request");
const modalArchivePersonnelRequestDialog = $('#modal-archive-personnel-request-dialog');
let jobClassicEditor = [];

if (typeof tablePersonnelRequestList !== "undefined") {
    var search_val = "";
    var dtPersonnel = tablePersonnelRequestList.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        buttons: [
            { 
                extend: 'copyHtml5',
                title: 'HRIS - Personnel Request Masterfile',
                exportOptions: {
                    columns: "thead th:not(.notExport)",
                    format: {
                        header: function ( text, index, node ) {
                            return text === "Action" ? "" : text;
                        }
                    }
                }
            },
            { 
                extend: 'excelHtml5',
                title: 'HRIS - Personnel Request Masterfile',
                exportOptions: {
                    columns: "thead th:not(.notExport)",
                    format: {
                        header: function ( text, index, node ) {
                            return text === "Action" ? "" : text;
                        }
                    }
                }
            },
            { 
                extend: 'csvHtml5',
                title: 'HRIS - Personnel Request Masterfile',
                exportOptions: {
                    columns: "thead th:not(.notExport)",
                    format: {
                        header: function ( text, index, node ) {
                            return text === "Action" ? "" : text;
                        }
                    }
                }
            },
            { 
                extend: 'pdfHtml5',
                title: 'HRIS - Personnel Request Masterfile',
                exportOptions: {
                    columns: "thead th:not(.notExport)"
                }
            }
        ],
        ajax: {
            url: baseUrl("hris/masterfile/get_personnel_request_datatable_request/" + $('#filter').val()),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        columns: [
            { data: "position", width: "32%" },
            { data: "type", width: "10%", className: "text-center" },
            { data: "needed", width: "8%", className: "text-center" },
            { data: "overdue", width: "10%", className: "text-center" },
            { data: "requested_date", width: "10%", className: "text-center" },
            { data: "needed_date", width: "10%", className: "text-center" },
            { data: "status", width: "10%", className: "text-center" },
            { data: null, width: "10%", className: "text-center" }
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

    $("#copy").on("click", function(e) {
        e.preventDefault();
        dtPersonnel.button('.buttons-copy').trigger();
    });

    $("#ExportExcel").on("click", function(e) {
        e.preventDefault();
        dtPersonnel.button('.buttons-excel').trigger();
    });

    $("#ExportCSV").on("click", function(e) {
        e.preventDefault();
        dtPersonnel.button('.buttons-csv').trigger();
    });

    $("#ExportPDF").on("click", function(e) {
        e.preventDefault();
        dtPersonnel.button('.buttons-pdf').trigger();
    });

    $("#ExportExcel_comp").on("click", function(e) {
        e.preventDefault();
        dtPersonnel_completed.button('.buttons-excel').trigger();
    });

    $("#ExportCSV_comp").on("click", function(e) {
        e.preventDefault();
        dtPersonnel_completed.button('.buttons-csv').trigger();
    });

    $("#ExportPDF_comp").on("click", function(e) {
        e.preventDefault();
        dtPersonnel_completed.button('.buttons-pdf').trigger();
    });

    var dtPersonnel_completed = tablePersonnelRequestList_completed.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_personnel_request_datatable_request_completed/" + $('#filter').val()),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        buttons: [
            { 
                extend: 'excelHtml5',
                title: 'HRIS - Personnel Request Masterfile - Completed',
                exportOptions: {
                    columns: "thead th:not(.notExport)",
                    format: {
                        header: function ( text, index, node ) {
                            return text === "Action" ? "" : text;
                        }
                    }
                }
            },
            { 
                extend: 'csvHtml5',
                title: 'HRIS - Personnel Request Masterfile - Completed',
                exportOptions: {
                    columns: "thead th:not(.notExport)",
                    format: {
                        header: function ( text, index, node ) {
                            return text === "Action" ? "" : text;
                        }
                    }
                }
            },
            { 
                extend: 'pdfHtml5',
                title: 'HRIS - Personnel Request Masterfile - Completed',
                exportOptions: {
                    columns: "thead th:not(.notExport)",
                    
                }
            }
        ],
        columns: [
            { data: "position", width: "32%" },
            { data: "type", width: "10%", className: "text-center" },
            { data: "needed", width: "8%", className: "text-center" },
            { data: "overdue", width: "10%", className: "text-center" },
            { data: "requested_date", width: "10%", className: "text-center" },
            { data: "needed_date", width: "10%", className: "text-center" },
            { data: "status", width: "10%", className: "text-center" },
            { data: null, width: "10%", className: "text-center" }
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
            if ($status == "For Approval") {
                _actionButton +=
                    " <button type='button' " +
                    "   class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnForApprovalPersonnelRequest' " +
                    "   data-id='" + $id + "'" +
                    "   data-toggle='m-tooltip' data-placement='bottom'" +
                    "   data-original-title='Approve Request' data-skine='dark'" +
                    "   data-delay='{\"show\": 300}'><i class='la la-thumbs-up'></i></button>";
            }
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditPersonnelRequest' " +
                "   data-id='" + $id + "'" +
                "   data-toggle='m-tooltip' data-placement='bottom'" +
                "   data-original-title='Edit Request' data-skine='dark'" +
                "   data-delay='{\"show\": 300}'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemovePersonnelRequest' " +
                "   data-id='" + $id + "'" +
                "   data-toggle='m-tooltip' data-placement='bottom'" +
                "   data-original-title='Archive Request' data-skine='dark'" +
                "   data-delay='{\"show\": 300}'><i class='la la-file-archive-o'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtPersonnel.ajax.reload();
        dtPersonnel_completed.ajax.reload();
    });

    $(document).on("click", ".btnForApprovalPersonnelRequest", function () {
        var dataId = $(this).data("id");
        location.href = baseUrl("hris/masterfile/approval_personnel_request/" + dataId);
    });

    $(document).on("click", ".btnEditPersonnelRequest", function () {
        var dataId = $(this).data("id");
        location.href = baseUrl("hris/masterfile/edit_personnel_request/" + dataId);
    });

    $(document).on("click", ".btnNewPersonnelRequest", function () {
        $.ajax({
            url: baseUrl("hris/masterfile/get_personnel_request_modal_content"),
            dataType: "json",
            success: function (json) {
                if (json.response) {
                    if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                        var tempModalContent = modalWindow.find("#modalTempContainer");
                        tempModalContent.empty().html(json.html);
                        modalWindow.modal("show");

                        var select2Type = modalWindow.find("select#type").select2({
                            width: "100%",
                            placeholder: "Select an option",
                            dropdownParent: modalWindow
                        });

                        var select2Company = modalWindow.find("select#company_id").select2({
                            width: "100%",
                            placeholder: "Select an option",
                            dropdownParent: modalWindow,
                            ajax: {
                                url: baseUrl("hris/masterfile/get_company_select2_data"),
                                dataType: "json",
                                delay: 250,
                                processResults: function (data) {
                                    return data;
                                }
                            }
                        });

                        var select2Department = modalWindow.find("select#department_id").select2({
                            width: "100%",
                            placeholder: "Select an option",
                            dropdownParent: modalWindow,
                            ajax: {
                                url: baseUrl("hris/masterfile/get_department_select2_data"),
                                dataType: "json",
                                delay: 250,
                                processResults: function (data) {
                                    return data;
                                }
                            }
                        });

                        var select2Position = modalWindow.find("select#position_id").select2({
                            width: "100%",
                            placeholder: "Select an option",
                            dropdownParent: modalWindow,
                            ajax: {
                                url: baseUrl("hris/masterfile/get_position_select2_data"),
                                dataType: "json",
                                delay: 250,
                                processResults: function (data) {
                                    return data;
                                }
                            }
                        });

                        var select2Salary = modalWindow.find("select#salary_id").select2({
                            width: "100%",
                            placeholder: "Select an option",
                            dropdownParent: modalWindow,
                            ajax: {
                                url: baseUrl("hris/masterfile/get_salary_select2_data"),
                                dataType: "json",
                                delay: 250,
                                processResults: function (data) {
                                    return data;
                                }
                            }
                        });

                        var select2RequestedBy = modalWindow.find("select#requested_by").select2({
                            width: "100%",
                            placeholder: "Select an option",
                            dropdownParent: modalWindow,
                            ajax: {
                                url: baseUrl("hris/masterfile/get_personnel_select2_data"),
                                dataType: "json",
                                delay: 250,
                                processResults: function (data) {
                                    return data;
                                }
                            }
                        });

                        select2Type.on("change", function (e) {
                            var self = $(e.target);
                            self.validate();
                        });
                        select2Company.on("change", function (e) {
                            var self = $(e.target);
                            self.validate();
                        });
                        select2Department.on("change", function (e) {
                            var self = $(e.target);
                            self.validate();
                        });
                        select2Position.on("change", function (e) {
                            var self = $(e.target);
                            self.validate();
                        });
                        select2Salary.on("change", function (e) {
                            var self = $(e.target);
                            self.validate();
                        });
                        select2RequestedBy.on("change", function (e) {
                            var self = $(e.target);
                            self.validate();
                        });

                        var dtPickerNeededDate = modalWindow.find("#need_dt").datepicker({
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

                        $.validate({
                            form: "#form-add_personnel_request",
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
                                            .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                    },
                                    success: function (json) {
                                        if (json.response) {
                                            toastr.success(
                                                json.toastr_msg,
                                                "Personnel request details has been saved.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalWindow.modal("hide");
                                            dtPersonnel.ajax.reload();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error adding personnel request details!",
                                                5000
                                            );
                                        }

                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            }
        });
    });
}

if (typeof editPersonnelRequestPage !== "undefined" && editPersonnelRequestPage.length == 1) {
    var tempLeftPane = {};
    tempLeftPane = _tempContentData.left_pane;

    var tempRightPane = {};
    tempRightPane = _tempContentData.right_pane;

    var vmLeftPane = new Vue({
        el: "#form-update_personnel_request",
        data: { left_pane: tempLeftPane },
        mounted: function () {
            var tempData = this._data.left_pane;
            var companyOption = new Option(tempData.company, tempData.company_id, true, true);
            var departmentOption = new Option(tempData.department, tempData.department_id, true, true);
            var positionOption = new Option(tempData.position, tempData.position_id, true, true);
            var salaryOption = new Option(tempData.salary, tempData.salary_id, true, true);
            var requestedOption = new Option(tempData.requested_name, tempData.requested_by, true, true);

            editPersonnelRequestPage.find("select#company_id").empty().html(companyOption).trigger("change");
            editPersonnelRequestPage.find("select#department_id").empty().html(departmentOption).trigger("change");
            editPersonnelRequestPage.find("select#position_id").empty().html(positionOption).trigger("change");
            editPersonnelRequestPage.find("select#salary_id").empty().html(salaryOption).trigger("change");
            editPersonnelRequestPage.find("select#requested_by").empty().html(requestedOption).trigger("change");

            var dtPickerNeededDate = editPersonnelRequestPage.find("#need_dt").datepicker({
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

            updatePersonnelRequest();
        }
    });

    var vmRightPane = new Vue({
        el: "#right_pane-content",
        data: { right_pane: tempRightPane }
    });

    $(document).on("click", ".btnUpdatePersonnelRequest", function () {
        $("#form-update_personnel_request").submit();
    });

    $(document).on("click", ".cancelPersonnelRequestAction", function () {
        if (typeof tempLeftPane.id !== "undefined") {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_cancel_action/cancel/" + tempLeftPane.id),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        console.log(json);
                        var modalContent = modalWindow.find(".modal-content");
                        if (typeof modalContent !== "undefined") {
                            modalContent.empty().html(json.html);
                            modalWindow.modal("show");
                            setStatusPersonnelRequest();
                        }
                    }
                }
            });
        }
    });

    $(document).on("click", ".holdPersonnelRequestAction", function () {
        if (typeof tempLeftPane.id !== "undefined") {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_cancel_action/hold/" + tempLeftPane.id),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        var modalContent = modalWindow.find(".modal-content");
                        if (typeof modalContent !== "undefined") {
                            modalContent.empty().html(json.html);
                            modalWindow.modal("show");
                            setStatusPersonnelRequest();
                        }
                    }
                }
            });
        }
    });

    $(document).on("click", ".activatePersonnelRequestAction", function () {
        if (typeof tempLeftPane.id !== "undefined") {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_cancel_action/activate/" + tempLeftPane.id),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        var modalContent = modalWindow.find(".modal-content");
                        if (typeof modalContent !== "undefined") {
                            modalContent.empty().html(json.html);
                            modalWindow.modal("show");
                            setStatusPersonnelRequest();
                        }
                    }
                }
            });
        }
    });

    $(document).on("click", ".completePersonnelRequestAction", function () {
        if (typeof tempLeftPane.id !== "undefined") {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_approval_action/complete/" + tempLeftPane.id),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        var modalContent = modalWindow.find(".modal-content");
                        if (typeof modalContent !== "undefined") {
                            modalContent.empty().html(json.html);
                            modalWindow.modal("show");
                            setCompletePersonnelRequest();
                        }
                    }
                }
            });
        }
    });

    $(document).on("click", ".viewCompletedRemarks", function () {
        var metaId = $(this).data("meta");
        if (typeof metaId !== "undefined" && (metaId !== null || metaId !== 0)) {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_completed_remarks/" + metaId),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        var modalContent = modalWindow.find(".modal-content");
                        if (typeof modalContent !== "undefined") {
                            modalContent.empty().html(json.html);
                            modalWindow.modal("show");
                        }
                    }
                }
            });
        }
    });

    jQuery(document).ready(function () {

        var status = $("#pr_status").val();
        if(status == "Completed"){
            var a = $("#appli_position").text();
        }else{
            var a = $("#position_id").text();
        }
        
        var csrf_token = $("#csrf_token").val();
        $.ajax({
            url: baseUrl("hris/masterfile/get_position_details_pr"),
            type: 'POST',
            dataType: 'JSON',
            data: {a , csrf_token},
            success: function (response) {
                $("#job_description").html(response.job_description);
                $("#qualification").html(response.qualification);
                if(typeof ClassicEditor !== "undefined") {
                    console.log("Initializing CKEditor instances...");
                    const editorIds = ['#job_description', '#qualification', '#remark', '#request_remark'];
                    editorIds.forEach(id => {
                        ClassicEditor.create(document.querySelector(id))
                            .then(editor => {
                                jobClassicEditor[id] = editor;
                                console.log(`Editor for ${id} was initialized successfully.`);
                            })
                            .catch(error => {
                                console.error(`There was a problem initializing the editor for ${id}:`, error);
                            });
                    });
                }
            }
        });
        // $.post(), {
        //     a : a,
        //     csrf_token : csrf_token
        // }, function(data){
        //     $("#job_description").html(data);
        // });

        var select2Type = editPersonnelRequestPage.find("select#type").select2({
            width: "100%",
            placeholder: "Select an option",
        });

        var select2Company = editPersonnelRequestPage.find("select#company_id").select2({
            width: "100%",
            placeholder: "Select an option",
            ajax: {
                url: baseUrl("hris/masterfile/get_company_select2_data"),
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return data;
                }
            }
        });

        var select2Department = editPersonnelRequestPage.find("select#department_id").select2({
            width: "100%",
            placeholder: "Select an option",
            ajax: {
                url: baseUrl("hris/masterfile/get_department_select2_data"),
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return data;
                }
            }
        });

        var select2Position = editPersonnelRequestPage.find("select#position_id").select2({
            width: "100%",
            placeholder: "Select an option",
            ajax: {
                url: baseUrl("hris/masterfile/get_position_select2_data"),
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return data;
                }
            }
        });

        var select2Salary = editPersonnelRequestPage.find("select#salary_id").select2({
            width: "100%",
            placeholder: "Select an option",
            ajax: {
                url: baseUrl("hris/masterfile/get_salary_select2_data"),
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return data;
                }
            }
        });

        var select2RequestedBy = editPersonnelRequestPage.find("select#requested_by").select2({
            width: "100%",
            placeholder: "Select an option",
            ajax: {
                url: baseUrl("hris/masterfile/get_personnel_select2_data"),
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return data;
                }
            }
        });

        select2Type.on("change", function (e) {
            var self = $(e.target);
            self.validate();
        });
        select2Company.on("change", function (e) {
            var self = $(e.target);
            self.validate();
        });
        select2Department.on("change", function (e) {
            var self = $(e.target);
            self.validate();
        });
        select2Position.on("change", function (e) {
            var self = $(e.target);

            $.ajax({
                url: baseUrl("hris/masterfile/get_position_jdq/" + self.val()),
                success: function (json) {
                    if (json.response) {
                        vmLeftPane.left_pane = Object.assign({}, tempLeftPane, json.data);
                    }
                }
            });

            self.validate();
        });
        select2Salary.on("change", function (e) {
            var self = $(e.target);
            self.validate();
        });
        select2RequestedBy.on("change", function (e) {
            var self = $(e.target);
            self.validate();
        });
    });

    function setStatusPersonnelRequest() {
        $.validate({
            form: "#form-set_status_personnel_request",
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
                            .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function (json) {
                        if (json.response) {
                            if (typeof json.data !== "undefined") {
                                var tempData = json.data;
                                if (typeof tempData.right_pane !== "undefined") {
                                    var rightPaneData = tempData.right_pane;
                                    var leftPaneData = tempData.left_pane;
                                    vmRightPane.right_pane = Object.assign({}, rightPaneData);
                                    vmLeftPane.left_pane = Object.assign({}, leftPaneData);
                                }
                            }
                            modalWindow.modal("hide");
                            toastr.success(
                                json.toastr_msg,
                                "Personnel request status has been updated.",
                                5000
                            );
                        } else {
                            toastr.error(
                                json.toastr_msg,
                                "Error updating personnel request status!",
                                5000
                            );
                        }

                        $(currentForm)
                            .find(".btn-submit")
                            .removeClass(
                                "m-btn--custom m-loader m-loader--light m-loader--right"
                            );
                    }
                });
                return false;
            }
        });
    }

    function setCompletePersonnelRequest() {
        $.validate({
            el: "#form-approval_personnel_request",
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
                            .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function (json) {
                        if (json.response) {
                            toastr.success(
                                json.toastr_msg,
                                "Personnel request complete",
                                5000
                            );
                            if (typeof json.data !== "undefined") {
                                var tempData = json.data;
                                if (typeof tempData.right_pane !== "undefined") {
                                    var rightPaneData = tempData.right_pane;
                                    vmRightPane.right_pane = Object.assign({}, rightPaneData);
                                }
                            }
                            modalWindow.modal("hide");
                        } else {
                            toastr.error(
                                json.toastr_msg,
                                "Error on personnel request completion!",
                                5000
                            );
                        }

                        $(currentForm)
                            .find(".btn-submit")
                            .removeClass(
                                "m-btn--custom m-loader m-loader--light m-loader--right"
                            );
                    }
                });
                return false;
            }
        });
    }

    function updatePersonnelRequest() {
        $.validate({
            form: "#form-update_personnel_request",
            lang: "en",
            onSuccess: function (form) {
                const jd = typeof jobClassicEditor["#job_description"] != "undefined" ? jobClassicEditor["#job_description"].getData() : null;
                const quali = typeof jobClassicEditor["#qualification"] != "undefined" ? jobClassicEditor["#qualification"].getData() : null;
                const reqrem = typeof jobClassicEditor["#request_remark"] != "undefined" ? jobClassicEditor["#request_remark"].getData() : null;
                const rem = typeof jobClassicEditor["#remark"] != "undefined" ? jobClassicEditor["#remark"].getData() : null;
                var jobDescriptionContent = jd;
                var qualification =quali;
                var request_remark = reqrem;
                var remark = rem;
                var currentForm = form[0];
                var formUrl = currentForm.action;
                var formData = $(currentForm).serialize();
                formData += "&job_description=" + encodeURIComponent(jobDescriptionContent);
                formData += "&qualification=" + encodeURIComponent(qualification);
                formData += "&request_remark=" + encodeURIComponent(request_remark);
                formData += "&remark=" + encodeURIComponent(remark);
                $.ajax({
                    url: formUrl,
                    type: "post",
                    dataType: "json",
                    data: formData,
                    beforeSend: function () {
                        $(currentForm)
                            .find(".btn-submit")
                            .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function (json) {
                        if (json.response) {
                            if (typeof json.data !== "undefined") {
                                var tempData = json.data;
                                if (typeof tempData.right_pane !== "undefined") {
                                    var rightPaneData = tempData.right_pane;
                                    vmRightPane.right_pane = Object.assign({}, rightPaneData);
                                }
                            }

                            toastr.success(json.toastr_msg, "Personnel request details has been saved.", 5000);
                            setTimeout(() => {
                                window.location.assign(baseUrl("hris/masterfile/personnel_request"));
                            }, 500);
                        } else {
                            toastr.error(
                                json.toastr_msg,
                                "Error adding personnel request details!",
                                5000
                            );
                        }

                        $(currentForm)
                            .find(".btn-submit")
                            .removeClass(
                                "m-btn--custom m-loader m-loader--light m-loader--right"
                            );
                    }
                });
                return false;
            }
        });
    }
}

if (typeof approvalPersonnelRequestPage !== "undefined" && approvalPersonnelRequestPage.length == 1) {
    
    var tempLeftPane = {};
    tempLeftPane = _tempContentData.left_pane;

    var tempRightPane = {};
    tempRightPane = _tempContentData.right_pane;

    var vmLeftPane = new Vue({
        el: "#left_pane-content",
        data: { left_pane: tempLeftPane }
    });

    var vmRightPane = new Vue({
        el: "#right_pane-content",
        data: { right_pane: tempRightPane }
    });

    $(document).on("click", ".editPersonnelRequestAction", function () {
        if (typeof tempLeftPane.id !== "undefined") {
            location.href = baseUrl("hris/masterfile/edit_personnel_request/" + tempLeftPane.id);
        }
    });

    $(document).on("click", ".approvalRequestAction", function () {
        if (typeof tempLeftPane.id !== "undefined") {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_approval_action/approval/" + tempLeftPane.id),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        var modalContent = modalWindow.find(".modal-content");
                        if (typeof modalContent !== "undefined") {
                            modalContent.empty().html(json.html);
                            modalWindow.modal("show");
                            approvalValidation();
                        }
                    }
                }
            });
        }
    });

    $(document).on("click", ".denyRequestAction", function () {
        if (typeof tempLeftPane.id !== "undefined") {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_approval_action/deny/" + tempLeftPane.id),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        var modalContent = modalWindow.find(".modal-content");
                        if (typeof modalContent !== "undefined") {
                            modalContent.empty().html(json.html);
                            modalWindow.modal("show");
                            approvalValidation();
                        }
                    }
                }
            });
        }
    });

    function approvalValidation() {
        $.validate({
            el: "#form-approval_personnel_request",
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
                            .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function (json) {
                        if (json.response) {
                            toastr.success(
                                json.toastr_msg,
                                "Personnel request approval",
                                5000
                            );
                            if (typeof json.data !== "undefined") {
                                var tempData = json.data;
                                if (typeof tempData.right_pane !== "undefined") {
                                    var rightPaneData = tempData.right_pane;
                                    vmRightPane.right_pane = Object.assign({}, rightPaneData);
                                }
                            }
                            modalWindow.modal("hide");
                        } else {
                            toastr.error(
                                json.toastr_msg,
                                "Error on personnel request approval!",
                                5000
                            );
                        }

                        $(currentForm)
                            .find(".btn-submit")
                            .removeClass(
                                "m-btn--custom m-loader m-loader--light m-loader--right"
                            );
                    }
                });
                return false;
            }
        });
    }
    
}
    var status = $("#pr_status").val();
    var csrf_token = $("#csrf_token").val();
    var a = $("#position_id_forApproval").text();
    // $.ajax({
    //     url: baseUrl("hris/masterfile/get_position_details_pr"),
    //     type: 'POST',
    //     dataType: 'JSON',
    //     data: {a , csrf_token},
    //     success: function (response) {
    //         $("#job_description_forApproval").html(response.job_description);
    //         $("#qualification_forApproval").html(response.qualification);
    //     }
    // });
$(document).on("click", ".btnBackToList", function () {
    window.location.href = baseUrl("hris/masterfile/personnel_request");
});

tablePersonnelRequestList
    .on('click', '.btnRemovePersonnelRequest', function () {
        const id = $(this).attr('data-id');
        modalArchivePersonnelRequestDialog.find('input[name="id"]').val(id);
        modalArchivePersonnelRequestDialog.modal('show');
    });

tablePersonnelRequestList_completed    
    .on('click', '.btnRemovePersonnelRequest', function () {
        const id = $(this).attr('data-id');
        modalArchivePersonnelRequestDialog.find('input[name="id"]').val(id);
        modalArchivePersonnelRequestDialog.modal('show');
    });

function archive_personnel_request(el) {
    const form = $(el);
    const url = form.attr('action');
    const formData = new FormData(el);
    const id = form.find('input[name="id"]').val();
    const tr = $('.btnRemovePersonnelRequest[data-id=' + id + ']').closest('tr');

    if (form.isValid()) {
        $.ajax({
            url,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                if (response.success) {
                    form.resetForm();
                    toastr.success(response.message, 'Archive Personnel Request.', 10000);
                    tr.remove();
                } else {
                    toastr.error(response.message, 'Error.', 10000);
                }

                modalArchivePersonnelRequestDialog.modal('hide');
            }
        });
    }
}

$("input[name='people_no']")
    .on('keydown', function (e) {
        if (e.shiftKey == true) {
            e.preventDefault();
        }

        if ((e.keyCode >= 48 && e.keyCode <= 57) ||
            (e.keyCode >= 96 && e.keyCode <= 105) ||
            e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 37 ||
            e.keyCode == 39 || e.keyCode == 46 || e.keyCode == 190) {

        } else {
            e.preventDefault();
        }

        if ($(this).val().indexOf('.') !== -1 && e.keyCode == 190)
            e.preventDefault();
        //if a decimal has been added, disable the "."-button
    });