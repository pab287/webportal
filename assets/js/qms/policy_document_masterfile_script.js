var globalIncidentDescription;
var modalWindow = $("#modalTempContent");
var tblPolicy = $("#table-policy");
var tblArchivedPolicy = $("#table-archived-policy");
var search_val = '';
var query_builder = '';
var modalAdvance = $("#modalAdvanceSearch");
var advance_search = '';

let _category = [];
let _emp = [];
let _comp = [];
let file = [];
let editFile = [];
let _dept = [];

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.category !== "undefined" && _tempContentData.category){ _category = _tempContentData.category; }
    if(typeof _tempContentData.employees !== "undefined" && _tempContentData.employees){ _emp = _tempContentData.employees; }
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company){ _comp = _tempContentData.company; }
    if(typeof _tempContentData.department !== "undefined" && _tempContentData.department){ _dept = _tempContentData.department; }
}

if(typeof tblPolicy != 'undefined'){
    var table = tblPolicy.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("qms/masterfile/get_policy_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        columns: [
            { data: "added_dt" },
            { data: "title", width: "20%" },
            { data: "document_no", width: "15%" },
            { data: "author", width: "20%" },
            { data: "category", },
            { data: "objective", },
            { data: "companies" },
            // { data: "filename", width: "5%", className: "text-center" },
            { data: null, width: "10%", className: "text-center" }
        ],
        columnDefs: [
            {
                targets: 0,
                visible: false,
                searchable: false
            },
            {
                targets: 2,
                render: function(data, type, row, meta){
                    var html = "";

                    html += '<div style="line-height: 1.1">';
                    html += '<p class="mb-0 m-font-3"><b>' + data + ' </b></p>';
                    html += '<p class="mt-2 mb-0 m-font-3"><small><b>Revision No:</b> ' + row.revision_no + ' </small></p>';
                    html += '</div>';

                    return html;
                }
            },
            {
                targets: 3,
                render: function(data, type, row, meta){
                    if(data){
                        return data;
                    }else{
                        return '---';
                    }
                }
            },
            // {
            //     targets: 5,
            //     defaultContent: "",
            //     render: function(data, type, row, meta){
            //         var html = "";

            //         if(data){
                       
            //         }else{
            //             html += ' --- ';
            //         }

            //         return html;
            //     }
            // },
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return policyDataTableActions(row.filename, row.id, row.title);
                }
            }
        ]
    });

    // function policyDataTableActions(file, id, title){
    //     if (id) {
    //         var _actionButton = "";
    //         _actionButton +=
    //             " <button type='button' " +
    //             "   class='btn btn-default m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill btnViewPolicy' " +
    //             "   data-toggle='modal' data-target='#edit_modal' " +
    //             "   data-placement='bottom'" +
    //             "   data-skin='dark'" +
    //             "   title='View Document'" +
    //             "   data-delay='{\"show\": 300}' onclick='viewPDF(`" + file + "`, " + id + ", `" + title + "`)" +
    //             "'><i class='la la-file-pdf-o'></i></button>";

    //         _actionButton +=
    //             " <button type='button' " +
    //             "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnEditPolicy' " +
    //             "   data-toggle='modal' data-target='#edit_modal' " +
    //             "   data-placement='bottom'" +
    //             "   data-skin='dark'" +
    //             "   title='Edit Document'" +
    //             "   data-delay='{\"show\": 300}' data-id='" +
    //             id +
    //             "'><i class='la la-edit'></i></button>";
    //         _actionButton +=
    //             " <button type='button' " +
    //             "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnRemovePolicy' " +
    //             "   data-toggle='m-tooltip' " +
    //             "   data-placement='bottom'" +
    //             "   data-skin='dark'" +
    //             "   title='Archive'" +
    //             "   data-delay='{\"show\": 300}' data-id='" +
    //             id +
    //             "'><i class='la la-file-archive-o'></i></button>";

    //         return _actionButton;
    //     } else {
    //         return false;
    //     }
    // }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        table.ajax.reload();
    });
}

$(document).on("click", "#btnNewPolicy", function () {
    $.ajax({
        url: baseUrl("qms/masterfile/get_policy_modal_content/add"), 
        dataType: "json",
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    modalWindow.modal("show");

                    $("#m-dropzone-one").dropzone({
                        url : "#",
                        maxFiles: 1,
                        acceptedFiles: "application/pdf",
                        autoProcessQueue: false,
                        addRemoveLinks: true,
                        accept: function(file, done){
                            done();
                            file.previewElement.classList.add("dz-success");
                            $('.dz-preview').addClass('dz-complete');
                        },
                        addedfiles: function(files){
                            // const tempFiles = this.files[0];
                            const tempFiles = files[0];
                            const char = /[`!@#$%^&*()+\=\[\]{};':"\\|<>\/?~]/;

                            if(tempFiles.type !== 'application/pdf'){
                                toastr.error("Unsupported file, PDF format only.", "Attachment", 5000);
                                this.removeFile(tempFiles);
                            }else{
                                if(containsSpecialChars(tempFiles.name)) {
                                    Swal.fire({
                                        title: 'Special Character Found.',
                                        html: '<p>Please do not use <b>special characters</b>. <br><br> <b style="color: #b72e2e">( ' + char + ' )</b> <br>Are not allowed.</p>',
                                        icon: 'warning'
                                    });

                                    this.removeFile(tempFiles);
                                }else{
                                    if(tempFiles.name.length > 100){
                                        Swal.fire({
                                            title: "Filename's character count has exceeded",
                                            text: 'Please rename filename not exceeding 100 characters.',
                                            icon: 'warning'
                                        });
                                        this.removeFile(tempFiles);
                                    }else{
                                        file = tempFiles;
                                    }
                                }
                            }
                        },
                        init: function(){
                            this.on("removedfile", function (file) {
                                file = [];
                            });
                        }
                    });

                    tempModalContent.find('#policy-author').select2({
                        width: '100%',
                        placeholder:  { id: "-1", text: "Select an Option" },
                        dropdownParent: tempModalContent,
                        data: _emp,
                    });

                    tempModalContent.find('#policy-reviewed').select2({
                        width: '100%',
                        placeholder:  { id: "-1", text: "Select an Option" },
                        dropdownParent: tempModalContent,
                        data: _emp,
                    });
                    
                    tempModalContent.find('#policy-approved').select2({
                        width: '100%',
                        placeholder:  { id: "-1", text: "Select an Option" },
                        dropdownParent: tempModalContent,
                        data: _emp,
                    });

                    tempModalContent.find('#policy-category').select2({
                        width: '100%',
                        placeholder:  { id: "-1", text: "Select an Option" },
                        data: _category
                    }).on("select2:select", function (e) {
                        var data = e.params.data;
                        
                        if(data.text.toLowerCase() !== 'form' && data.text.toLowerCase() !== 'forms'){
                            $("#to-remove").css('display', 'flex');

                            if(data.text.toLowerCase() == 'memorandum'){
                                $("#for-memorandum").removeClass('d-none');
                                $("#others-category").addClass('d-none');
                                $("#for-kra").addClass('d-none');

                                $("#memo-ref_code").val('Memo No.');
                            }else if(data.text.toLowerCase() == 'kra/kpi'){
                                $("#for-memorandum").addClass('d-none');
                                $("#others-category").addClass('d-none');
                                $("#for-kra").removeClass('d-none');

                                $("#kra-ref_code").val(data.code);
                            }else{
                                $("#for-memorandum").addClass('d-none');
                                $("#others-category").removeClass('d-none');
                                $("#for-kra").addClass('d-none');
                            }
                        }else{
                            $("#to-remove").css('display', 'flex');
                        }
                    });

                    tempModalContent.find('#policy-company').select2({
                        width: '100%',
                        placeholder:  { id: "-1", text: "Select an Option" },
                        data: _comp,
                    });
                    
                    tempModalContent.find('#policy-department').select2({
                        width: '100%',
                        placeholder:  { id: "-1", text: "Select an Option" },
                        data: _dept,
                    });

                    tempModalContent.find('#policy-effective').datepicker({
                        format: "yyyy-mm-dd",
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        autoclose: true
                    }).on("changeDate", function (e) {
                        var currentDt = moment(e.date).format("YYYY-MM-DD");
                        var self = $(e.target);
                        self.validate();
                    });

                    tempModalContent.find('#policy-prepared_date').datepicker({
                        format: "yyyy-mm-dd",
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        autoclose: true,
                        endDate: moment().format('YYYY-MM-DD')
                    }).on("changeDate", function (e) {
                        var currentDt = moment(e.date).format("YYYY-MM-DD");
                        var self = $(e.target);
                        self.validate();
                    });

                    tempModalContent.find('#policy-reviewed_date').datepicker({
                        format: "yyyy-mm-dd",
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        autoclose: true,
                        endDate: moment().format('YYYY-MM-DD')
                    }).on("changeDate", function (e) {
                        var currentDt = moment(e.date).format("YYYY-MM-DD");
                        var self = $(e.target);
                        self.validate();
                    });

                    tempModalContent.find('#policy-approved_date').datepicker({
                        format: "yyyy-mm-dd",
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        autoclose: true,
                        endDate: moment().format('YYYY-MM-DD')
                    }).on("changeDate", function (e) {
                        var currentDt = moment(e.date).format("YYYY-MM-DD");
                        var self = $(e.target);
                        self.validate();
                    });

                    if(typeof ClassicEditor !== "undefined"){
                        ClassicEditor
                        .create( document.querySelector( '#policy-desc' ) )
                        .then( editor => {
                            editor.ui.view.editable.element.style.height = '115px';
                            // editor.data.set("nothing follows");
                            globalIncidentDescription = editor;
                        } )
                        .catch( error => {} );
                    }

                    tempModalContent.find("#policy-name").on('keypress', function(event){
                        // var regex = new RegExp(/[^\w]|_/g);
                        // var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                        // if (!regex.test(key)) {
                        //     event.preventDefault();
                        //     return false;
                        // }
                        var txt = String.fromCharCode(event.which);
                        if(!txt.match(/[a-zA-Z0-9-.&(), :';]/)){
                            return false;
                        }
                    });

                    tempModalContent.find("#policy-scope").on('keypress', function(event){
                        // var regex = new RegExp("^[a-zA-Z0-9]+$");
                        // var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                        // if (!regex.test(key)) {
                        //     event.preventDefault();
                        //     return false;
                        // }
                        var txt = String.fromCharCode(event.which);
                        if(!txt.match(/[a-zA-Z0-9-.&(), :';]/)){
                            return false;
                        }
                    });

                    tempModalContent.find("#policy-obj").on('keypress', function(event){
                        // var regex = new RegExp("^[a-zA-Z0-9]+$");
                        // var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                        // if (!regex.test(key)) {
                        //     event.preventDefault();
                        //     return false;
                        // }
                        var txt = String.fromCharCode(event.which);
                        if(!txt.match(/[a-zA-Z0-9-.&(), :';]/)){
                            return false;
                        }
                    });

                    $.validate({
                        form: "#form-add_policy",
                        lang: "en",
                        onSuccess: function(form){
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            var formdata = new FormData(currentForm);

                            if(!jQuery.isEmptyObject(file)){
                                formdata.append('file', file);
    
                                $.ajax({
                                    url: formUrl,
                                    type: "post",
                                    dataType: "json",
                                    data: formdata,
                                    processData: false,
                                    contentType: false,
                                    beforeSend: function(){
                                        $(currentForm).find('.btn-submit').addClass('"m-btn--custom m-loader m-loader--light m-loader--right');
                                    },
                                    success: function(response){
                                        if(response.response){
                                            toastr.info(response.toastr_msg, "Document", 5000);
                                            // table.ajax.reload();
                                            currentForm.reset();
                                            modalWindow.modal("hide");

                                            if(response.code == 'Memo No.'){
                                                vmTab1.getTimeline(response.category_id);
                                            }else if(response.code == 'KRA/KPI'){
                                                vmTab1.getTimeline(response.category_id);
                                            }else{
                                                if(!jQuery.isEmptyObject(vmTab1.table)){
                                                    vmTab1.table.ajax.reload();
                                                }
                                            }
                                        }else{
                                            toastr.error(response.toastr_msg, "Document", 5000);
                                        }

                                        $(currentForm).find(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                    }
                                })
                            }else{
                                toastr.error("Please add attachment.", "Attachment", 5000);
                            }

                            return false;
                        }
                    });
                }
            }
        }
    });
});

$(document).on('click', '.btnEditPolicy', function(){
    var _self = $(this);
    var dataId = _self.data("id");

    $.ajax({
        url: baseUrl("qms/masterfile/get_policy_modal_content/edit"), 
        dataType: "json",
        type: "POST",
        data: {csrf_token: _csrf_hash, id: dataId},
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    modalWindow.modal("show");
                    var data = json.data;

                    // if(data.category.toLowerCase() == 'form' || data.category.toLowerCase() == 'forms'){
                    //     $("#form-edit_policy #to-remove").css('display', 'none');
                    // }else{
                    //     $("#form-edit_policy #to-remove").css('display', 'flex');
                    // }
                    $("#form-edit_policy #to-remove").css('display', 'flex');

                    if(typeof data.category_id !== 'undefined' && data.category_id){
                        var catOption = new Option(data.category, data.category_id, true, true);
                        tempModalContent.find('#policy-category').append(catOption).trigger('change');
                    }

                    if(typeof data.author !== 'undefined' && data.author){
                        $.each(data.author, function(index, item){
                            var authOption = new Option(item.name, item.id, true, true);
                            tempModalContent.find('#policy-author').append(authOption).trigger('change');
                        });
                    }

                    if(typeof data.companies !== 'undefined' && data.companies){
                        $.each(data.companies, function(index, item){
                            var compOption = new Option(item.code, item.id, true, true);
                            tempModalContent.find('#policy-company').append(compOption).trigger('change');
                        });
                    }

                    if(typeof data.reviewed_by !== 'undefined' && data.reviewed_by){
                        $.each(data.reviewed_by, function(index, item){
                            var reviewOption = new Option(item.name, item.id, true, true);
                            tempModalContent.find('#policy-reviewed').append(reviewOption).trigger('change');
                        });
                    }

                    if(typeof data.approved_by !== 'undefined' && data.approved_by){
                        $.each(data.approved_by, function(index, item){
                            var approveOption = new Option(item.name, item.id, true, true);
                            tempModalContent.find('#policy-approved').append(approveOption).trigger('change');
                        });
                    }

                    if(typeof data.departments !== 'undefined' && data.departments){
                        $.each(data.departments, function(index, item){
                            var deptOption = new Option(item.description, item.id, true, true);
                            tempModalContent.find('#policy-department').append(deptOption).trigger('change');
                        });
                    }

                    $("#m-dropzone-one").dropzone({
                        url : "#",
                        maxFiles: 1,
                        acceptedFiles: "application/pdf",
                        autoProcessQueue: false,
                        addRemoveLinks: true,
                        accept: function(file, done){
                            done();
                            file.previewElement.classList.add("dz-success");
                            $('.dz-preview').addClass('dz-complete');
                        },
                        addedfiles: function(files){
                            // const tempFiles = this.files[0];
                            // editFile = tempFiles;

                            const tempFiles = files[0];
                            const char = /[`!@#$%^&*()+\=\[\]{};':"\\|<>\/?~]/;

                            if(tempFiles.type !== 'application/pdf'){
                                toastr.error("Unsupported file, PDF format only.", "Attachment", 5000);
                                this.removeFile(tempFiles);
                            }else{

                                if(containsSpecialChars(tempFiles.name)) {
                                    Swal.fire({
                                        title: 'Special Character Found.',
                                        html: '<p>Please do not use <b>special characters</b>. <br><br> <b style="color: #b72e2e">( ' + char + ' )</b> <br>Are not allowed.</p>',
                                        icon: 'warning'
                                    });

                                    this.removeFile(tempFiles);
                                }else{
                                    if(tempFiles.name.length > 100){
                                        Swal.fire({
                                            title: "Filename's character count has exceeded",
                                            text: 'Please rename filename not exceeding 100 characters.',
                                            icon: 'warning'
                                        });
                                        this.removeFile(tempFiles);
                                    }else{
                                        editFile = tempFiles;
                                    }
                                }
                            }
                        },
                        init: function(){
                            this.on("removedfile", function (file) {
                                editFile = [];
                            });
                        }
                    });

                    tempModalContent.find('#policy-author').select2({
                        width: '100%',
                        placeholder:  { id: "-1", text: "Select an Option" },
                        dropdownParent: tempModalContent,
                        data: _emp,
                    });

                    tempModalContent.find('#policy-reviewed').select2({
                        width: '100%',
                        placeholder:  { id: "-1", text: "Select an Option" },
                        dropdownParent: tempModalContent,
                        data: _emp,
                    });
                    
                    tempModalContent.find('#policy-approved').select2({
                        width: '100%',
                        placeholder:  { id: "-1", text: "Select an Option" },
                        dropdownParent: tempModalContent,
                        data: _emp,
                    });

                    tempModalContent.find('#policy-category').select2({
                        width: '100%',
                        placeholder:  { id: "-1", text: "Select an Option" },
                        data: _category
                    }).on('select2:select', function(e){
                        var data = e.params.data;

                        if(data.text.toLowerCase() !== 'form' && data.text.toLowerCase() !== 'forms'){
                            $("#to-remove").css('display', 'flex');

                            if(data.text.toLowerCase() == 'memorandum'){
                                $("#for-memorandum").removeClass('d-none');
                                $("#others-category").addClass('d-none');
                                $("#for-kra").addClass('d-none');

                                $("#memo-ref_code").val('Memo No.');
                            }else if(data.text.toLowerCase() == 'kra/kpi'){
                                $("#for-memorandum").addClass('d-none');
                                $("#others-category").addClass('d-none');
                                $("#for-kra").removeClass('d-none');

                                $("#kra-ref_code").val(data.code);
                            }else{
                                $("#for-memorandum").addClass('d-none');
                                $("#others-category").removeClass('d-none');
                                $("#for-kra").addClass('d-none');
                            }
                        }else{
                            $("#to-remove").css('display', 'flex');
                        }
                    });

                    tempModalContent.find('#policy-company').select2({
                        width: '100%',
                        placeholder:  { id: "-1", text: "Select an Option" },
                        data: _comp,
                    });

                    tempModalContent.find('#policy-department').select2({
                        width: '100%',
                        placeholder:  { id: "-1", text: "Select an Option" },
                        data: _dept,
                    });

                    tempModalContent.find('#policy-effective').datepicker({
                        format: "yyyy-mm-dd",
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        autoclose: true
                    }).on("changeDate", function (e) {
                        var currentDt = moment(e.date).format("YYYY-MM-DD");
                        var self = $(e.target);
                        self.validate();
                    });

                    tempModalContent.find('#policy-prepared_date').datepicker({
                        format: "yyyy-mm-dd",
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        autoclose: true,
                        endDate: moment().format('YYYY-MM-DD')
                    }).on("changeDate", function (e) {
                        var currentDt = moment(e.date).format("YYYY-MM-DD");
                        var self = $(e.target);
                        self.validate();
                    });

                    tempModalContent.find('#policy-reviewed_date').datepicker({
                        format: "yyyy-mm-dd",
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        autoclose: true,
                        endDate: moment().format('YYYY-MM-DD')
                    }).on("changeDate", function (e) {
                        var currentDt = moment(e.date).format("YYYY-MM-DD");
                        var self = $(e.target);
                        self.validate();
                    });

                    tempModalContent.find('#policy-approved_date').datepicker({
                        format: "yyyy-mm-dd",
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        autoclose: true,
                        endDate: moment().format('YYYY-MM-DD')
                    }).on("changeDate", function (e) {
                        var currentDt = moment(e.date).format("YYYY-MM-DD");
                        var self = $(e.target);
                        self.validate();
                    });

                    tempModalContent.find("#policy-name").on('keypress', function(event){
                        // var regex = new RegExp("^[a-zA-Z0-9]+$");
                        // var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                        // if (!regex.test(key)) {
                        //     event.preventDefault();
                        //     return false;
                        // }
                        var txt = String.fromCharCode(event.which);
                        if(!txt.match(/[a-zA-Z0-9-.&(), :';]/)){
                            return false;
                        }
                    });
                    
                    tempModalContent.find("#policy-scope").on('keypress', function(event){
                        // var regex = new RegExp("^[a-zA-Z0-9]+$");
                        // var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                        // if (!regex.test(key)) {
                        //     event.preventDefault();
                        //     return false;
                        // }
                        var txt = String.fromCharCode(event.which);
                        if(!txt.match(/[a-zA-Z0-9-.&(), :';]/)){
                            return false;
                        }
                    });

                    tempModalContent.find("#policy-obj").on('keypress', function(event){
                        // var regex = new RegExp("^[a-zA-Z0-9]+$");
                        // var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                        // if (!regex.test(key)) {
                        //     event.preventDefault();
                        //     return false;
                        // }
                        var txt = String.fromCharCode(event.which);
                        if(!txt.match(/[a-zA-Z0-9-.&(), :';]/)){
                            return false;
                        }
                    });

                    $.validate({
                        form: "#form-edit_policy",
                        lang: "en",
                        onSuccess: function(form){
                            var currentForm = form[0];
                            var formUrl = currentForm.action;

                            var formdata = new FormData(currentForm);

                            if(!jQuery.isEmptyObject(editFile)){
                                formdata.append('file', editFile);
                            }

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formdata,
                                processData: false,
                                contentType: false,
                                beforeSend: function(){
                                    $(currentForm).find('.btn-submit').addClass('"m-btn--custom m-loader m-loader--light m-loader--right');
                                },
                                success: function(response){
                                    if(response.response){
                                        toastr.info(response.toastr_msg, "Document", 5000);
                                        // table.ajax.reload();
                                        currentForm.reset();
                                        modalWindow.modal("hide");

                                        if(response.code == 'Memo No.'){
                                            vmTab1.getTimeline(response.category_id);
                                        }else if(response.code == 'KRA/KPI'){
                                            vmTab1.getTimeline(response.category_id);
                                        }else{
                                            if(!jQuery.isEmptyObject(vmTab1.table)){
                                                vmTab1.table.ajax.reload();
                                            }
                                        }
                                    }else{
                                        toastr.error(response.toastr_msg, "Document", 5000);
                                    }

                                    $(currentForm).find(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
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

$(document).on("click", ".btnRemovePolicy", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    var modalWindowRemove = $("#modalRemovePolicy");
    modalWindowRemove.find("input#policyId").val(dataId);
    modalWindowRemove.modal("show");
});

$(document).on("click", ".btnRemoveCurrentPolicy", function () {
    var dataId = $("#modalRemovePolicy").find("input#policyId").val();
    if (typeof dataId !== "undefined" && dataId !== 0) {
        $.ajax({
            url: baseUrl("qms/masterfile/remove_current_policy"),
            type: "post",
            dataType: "json",
            data: {csrf_token: _csrf_hash, id: dataId},
            beforeSend: function () {
                $("#modalRemovePolicy")
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }, success: function (json) {
                if (json.response) {
                    toastr.info(json.toastr_msg, "Archive Document", 5000);
                    vmTab1.table.ajax.reload();
                    $("#modalRemovePolicy").modal("hide");
                } else {
                    toastr.error(json.toastr_msg, "Archive Document", 5000);
                }

                $("#modalRemovePolicy")
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            }
        })
    }
});

function viewPDF(file, id, title, exist = true){
    if(exist){
        $("#modalTempView .modal-body").html();

        var url = baseUrl('uploads/files/qms/document/' + id + '/' + file) + "?#toolbar=0";
    
        $("#view-pdf").attr('src', url);
        $("#view-title").text(title + ' File');
    
        $("#modalTempView").modal('show');
        mapBlockUI();

        setTimeout( function(){
            mapUnblockUI();
        }, 1000);
    }else{
        toastr.error("File not found. Contact QMS department for assistance.", "Attachment", 5000);
    }
}

if(typeof tblArchivedPolicy != 'undefined'){
    var archiveTable = tblArchivedPolicy.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("qms/masterfile/get_archived_policy_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        columns: [
            { data: "added_dt" },
            { data: "title", width: "20%" },
            { data: "document_no", width: "15%" },
            { data: "author", width: "20%" },
            { data: "category", },
            { data: "objective", },
            { data: "companies" },
            { data: null, width: "10%", className: "text-center" }
        ],
        columnDefs: [
            {
                targets: 0,
                visible: false,
                searchable: false
            },
            {
                targets: 2,
                render: function(data, type, row, meta){
                    var html = "";

                    html += '<div style="line-height: 1.1">';
                    html += '<p class="mb-0 m-font-3"><b>' + data + ' </b></p>';
                    html += '<p class="mt-2 mb-0 m-font-3"><small><b>Revision No:</b> ' + row.revision_no + ' </small></p>';
                    html += '</div>';

                    return html;
                }
            },
            {
                targets: 3,
                render: function(data, type, row, meta){
                    if(data){
                        return data;
                    }else{
                        return '---';
                    }
                }
            },
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return policyDataTableActions(row.filename, row.id);
                }
            }
        ]
    });

    function policyDataTableActions(file, id){
        if (id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill btnEditPolicy' " +
                "   data-toggle='modal' data-target='#edit_modal' " +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   title='View Document'" +
                "   data-delay='{\"show\": 300}' onclick='viewPDF(`" + file + "`, " + id + ")" +
                "'><i class='la la-file-pdf-o'></i></button>";

            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnRestorePolicy' " +
                "   data-toggle='m-tooltip' " +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   title='Restore'" +
                "   data-delay='{\"show\": 300}' data-id='" +
                id +
                "'><i class='la la-reply'></i></button>";

            return _actionButton;
        } else {
            return false;
        }
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        archiveTable.ajax.reload();
    });
}

$(document).on("click", ".btnRestorePolicy", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    var modalWindowRemove = $("#modalRestorePolicy");
    modalWindowRemove.find("input#policyId").val(dataId);
    modalWindowRemove.modal("show");
});

$(document).on("click", ".btnRestoreCurrentPolicy", function () {
    var dataId = $("#modalRestorePolicy").find("input#policyId").val();
    if (typeof dataId !== "undefined" && dataId !== 0) {
        $.ajax({
            url: baseUrl("qms/masterfile/restore_current_policy"),
            type: "post",
            dataType: "json",
            data: {csrf_token: _csrf_hash, id: dataId},
            beforeSend: function () {
                $("#modalRestorePolicy")
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }, success: function (json) {
                if (json.response) {
                    toastr.info(json.toastr_msg, "Restore Document", 5000);
                    archiveTable.ajax.reload();
                    $("#modalRestorePolicy").modal("hide");
                } else {
                    toastr.error(json.toastr_msg, "Restore Document", 5000);
                }

                $("#modalRestorePolicy")
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            }
        })
    }
});

function containsSpecialChars(str) {
    const specialChars = /[`!@#$%^&*()+\=\[\]{};':"\\|<>\/?~]/;
    return specialChars.test(str);
}

$(document).ready( function(){
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': { delay: 100 },
        filters: [
            { id: 'a.id', label: 'ID #', type: 'integer' },
            { id: 'a.title', label: 'Title', type: 'string' },
            { id: 'a.objective', label: 'Objective', type: 'string' },
            { 
                id: 'a.author', 
                label: 'Author', 
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select an option',
                    data: _emp,
                    width: "120%",
                    dropdownParent: $("#modal-query-builder"),
                },
                operators: ['equal', 'not_equal']
            },
            { 
                id: 'a.approved_by', 
                label: 'Approved By', 
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select an option',
                    data: _emp,
                    width: "120%",
                    dropdownParent: $("#modal-query-builder"),
                },
                operators: ['equal', 'not_equal']
            },
            {
                id: 'e.company_id', 
                label: 'Company', 
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select an option',
                    data: _comp,
                    width: "275px",
                    dropdownParent: $("#modal-query-builder"),
                },
                operators: ['equal', 'not_equal']
            },
            {
                id: 'f.department_id', 
                label: 'Department', 
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select an option',
                    data: _dept,
                    width: "275px",
                    dropdownParent: $("#modal-query-builder"),
                },
                operators: ['equal', 'not_equal']
            },
            {
                id: 'effective_date',
                label: 'Effectivity Date',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: { format: 'yyyy-mm-dd' },
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
            }
        ]
    });

    $("#query-builder_group_0").addClass("col-12");
    getCategory();
});

$('#query-builder-btn').on('click', function () {
    var result = $('#query-builder').queryBuilder('getSQL');

    if (!$.isEmptyObject(result)) {
        query_builder = result;
        vmTab1.table.ajax.reload();
        $("#modal-query-builder").modal("hide");
    }
});

function clear_query_builder() {
    $('#query-builder').queryBuilder('reset');
    query_builder = null;
    vmTab1.table.ajax.reload();
}


function getCategory(){
    $.ajax({
        url: baseUrl('qms/masterfile/get_department'),
        dataType: "json",
        success: function(json){
            vmTab1.row = Object.assign({}, json.data);
            vmTab1.content = Object.assign({}, json.data);
            vmTab1.count = json.data.length;

            setTimeout( function(){
                vmTab1.getContent(json.data[0].department, json.data[0].department_id);
            }, 500);
        }
    })
}

var vmTab1 = new Vue({
    el: "#tabbedCategories",
    data: { 
        row: {}, 
        count: 0, 
        content: {}, 
        timeline: {}, 
        timelineCount: 0,
        table: {},
        anchorTimeline: {},
        timer: null
    },
    mounted(){
    },
    methods: {
        getContent: function(tableId, id){
            const instance = vmTab1;
            $(".tab-pane").removeClass('show active');
            $('.tab-pane#v-pills-'+tableId).addClass('show active');

            var tabId = $('#table-'+tableId);
            instance.table = tabId.DataTable({
                dom: '<"toolbar">frtlip',
                serverSide: true,
                processing: true,
                searching: false,
                ordering: false,
                destroy: true,
                retrieve: true,
                ajax: {
                    url: baseUrl("qms/masterfile/get_department_policy_datatable_request"),
                    type: "post",
                    dataType: "json",
                    data: function (d) {
                        d.csrf_token = _csrf_hash,
                        d.search['value'] = search_val,
                        d.department = id,
                        d.query_builder = query_builder;
                        d.advance_search = advance_search;
                    }
                },
                columns: [
                    { data: "added_dt" },
                    { title: 'Title', data: "title", width: "20%" },
                    { title: 'Document No.', data: "document_no", width: "15%" },
                    { title: 'Author', data: "author", width: "15%" },
                    { title: 'Objectives', data: "objective", width: '15%' },
                    { title: 'Companies under this document', data: "companies", width: '10%', 
                        render: function(data){
                            var html = '';
                            html += '<ul class="p-3">';
                            $.each(data, function(index, item){
                                html += `<li>${item}</li>`;
                            });
                            html += '</ul>';

                            return html;
                        }
                    },
                    { title: 'Action', data: null, width: "10%", className: "text-center" }
                ],
                columnDefs: [
                    {
                        targets: 0,
                        visible: false,
                        searchable: false
                    },{
                        targets: 2,
                        render: function(data, type, row, meta){
                            var html = "";
        
                            html += '<div style="line-height: 1.1">';
                            html += '<a href="javascript:void(0)" style="text-decoration: none" class="mb-0 m-font-3" onclick="viewPDF(`' + row.filename + '`, ' + row.id + ', `' + row.title + '`, ' + row.file_exist + ')"><b>' + data + ' </b></a>';
                            html += '<p class="mt-2 mb-0 m-font-3"><small><b>Revision No:</b> ' + row.revision_no + ' </small></p>';
                            html += '</div>';
        
                            return html;
                        }
                    },{
                        targets: 3,
                        render: function(data, type, row, meta){
                            if(data){
                                return data;
                            }else{
                                return '---';
                            }
                        }
                    },{
                        data: null,
                        defaultContent: "",
                        targets: -1,
                        orderable: false,
                        render: function (data, type, row, meta) {
                            var _actionButton = "";
                            _actionButton +=
                                " <button type='button' " +
                                "   class='btn btn-default m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill btnViewPolicy' " +
                                "   data-toggle='modal' data-target='#edit_modal' " +
                                "   data-placement='bottom'" +
                                "   data-skin='dark'" +
                                "   title='View Document'" +
                                "   data-delay='{\"show\": 300}' onclick='viewPDF(`" + row.filename + "`, " + row.id + ", `" + row.title + "`, " + row.file_exist + ")" +
                                "'><i class='la la-file-pdf-o'></i></button>";
                
                            _actionButton +=
                                " <button type='button' " +
                                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnEditPolicy' " +
                                "   data-toggle='modal' data-target='#edit_modal' " +
                                "   data-placement='bottom'" +
                                "   data-skin='dark'" +
                                "   title='Edit Document'" +
                                "   data-delay='{\"show\": 300}' data-id='" +
                                row.id +
                                "'><i class='la la-edit'></i></button>";
                            _actionButton +=
                                " <button type='button' " +
                                "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnRemovePolicy' " +
                                "   data-toggle='m-tooltip' " +
                                "   data-placement='bottom'" +
                                "   data-skin='dark'" +
                                "   title='Archive'" +
                                "   data-delay='{\"show\": 300}' data-id='" +
                                row.id +
                                "' + data-row='" + JSON.stringify(row) +
                                "'><i class='la la-file-archive-o'></i></button>";
                
                            return _actionButton;
                        }
                    }
                ]
            });

            $('#query-builder').queryBuilder('reset');
            query_builder = null;
            advance_search = null;
        }, getTimeline (id = null, tab){
            $.ajax({
                url: baseUrl("qms/masterfile/get_timeline_list"),
                type: "post",
                data: {
                    id: id ? id : 1,
                    csrf_token: _csrf_hash,
                    search: search_val,
                    query_builder: query_builder,
                    advance_search: advance_search
                },
                dataType: "json",
                success: function(json){
                    vmTab1.timeline = Object.assign({}, json.data);

                    let tempData = [];
                    $.each(json.data, function(index, item){
                        tempData.push(Object.assign({}, { id: item.ref_year, text: item.ref_year }));
                    });

                    vmTab1.anchorTimeline = Object.assign({}, tempData);
                    vmTab1.timelineCount = tempData.length;

                    if(tempData.length == 0){
                        tempData = Object.assign({}, { id: '-1', text: 'No Data Found' });
                    }

                    var anchorId = tab ? `#${tab}-anchorToYear` : '#memorandum-anchorToYear';
                    setTimeout( function(){
                        vmTab1.setSelect2Year(anchorId, true, tempData);
                    }, 1000);

                }
            })
        }, getList(arr){
            return arr.join(', ');
        }, viewPDF(file, id, title, exist = false){
            
            if(exist){
                $("#modalTempView .modal-body").html();

                var url = baseUrl('uploads/files/qms/document/' + id + '/' + file) + "?#toolbar=0";
            
                $("#view-pdf").attr('src', url);
                $("#view-title").text(title + ' File');
            
                $("#modalTempView").modal('show');
                mapBlockUI();

                setTimeout( function(){
                    mapUnblockUI();
                }, 1000);
            }else{
                toastr.error("File not found. Contact QMS department for assistance.", "Attachment", 5000);
            }
        }, getSearchEvent(e){
            const instance = vmTab1;

            clearTimeout(this.timer);

            vmTab1.timer = setTimeout(() => {
                const searchValue = e.target.value;
                search_val = searchValue;

                instance.table.ajax.reload();
            }, 1000);
        }, getSearchMemo(e){
            const instance = vmTab1;

            clearTimeout(this.timer);

            vmTab1.timer = setTimeout(() => {
                const searchValue = e.target.value;
                search_val = searchValue;

                this.getTimeline();
            }, 1000);
        }, setSelect2Year(targetElement, isDestroy = false, data = {}){
            const currentTarget = $(targetElement);
            const select2Init = currentTarget.data('select2');

            if(typeof currentTarget !== "undefined" && currentTarget.length == 1){
                if(typeof select2Init !== "undefined"){ currentTarget.select2('destroy'); }
                if(isDestroy){ currentTarget.empty(); }
    
                currentTarget.select2({
                    width: '100%',
                    placeholder: { id: '-1', text: 'Select an Option' },
                    data: data
                }).on("select2:select", function (e) {
                    const data = e.params.data;
    
                    $(".custom-scrollbar").animate({
                        scrollTop: $('#'+data.id).offset().top
                    }, 'slow');
                });

                if(isDestroy == false){ currentTarget.val("").trigger("change"); }
            }

        }, removeCurrentPolicy(arr){
            console.log(arr);
            var modalWindowRemove = $("#modalRemovePolicy");
            modalWindowRemove.find("input#policyId").val(arr.id);
            modalWindowRemove.modal("show");

            $(document).on("click", ".btnRemoveCurrentPolicy", function () {
                var dataId = $("#modalRemovePolicy").find("input#policyId").val();
                if (typeof dataId !== "undefined" && dataId !== 0) {
                    $.ajax({
                        url: baseUrl("qms/masterfile/remove_current_policy"),
                        type: "post",
                        dataType: "json",
                        data: {csrf_token: _csrf_hash, id: dataId},
                        beforeSend: function () {
                            $("#modalRemovePolicy")
                                .find(".btn-submit")
                                .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        }, success: function (json) {
                            if (json.response) {
                                toastr.info(json.toastr_msg, "Archive Document", 5000);
                                
                                if(arr.ref_code == 'Memo No.'){
                                    vmTab1.getTimeline(arr.category_id);
                                }else if(arr.ref_code == 'KRA/KPI'){
                                    vmTab1.getTimeline(arr.category_id);
                                }else{
                                    instance.table.ajax.reload();
                                }
                                $("#modalRemovePolicy").modal("hide");
                            } else {
                                toastr.error(json.toastr_msg, "Archive Document", 5000);
                            }
            
                            $("#modalRemovePolicy")
                                .find(".btn-submit")
                                .removeClass(
                                    "m-btn--custom m-loader m-loader--light m-loader--right"
                                );
                        }
                    })
                }
            });
        }, setAuthorSelect2(targetElement, modalContent = null, select2Destroy = false){
            const currentTarget = $(targetElement);
            if(typeof modalContent != 'undefined' && modalContent){
                if(select2Destroy){ currentTarget.empty(); }

                const select2Option = new Option('Select an Option', -1, false, true);
                currentTarget.html(select2Option);

                modalContent.find(targetElement).select2({
                    width: '100%',
                    placeholder:  { id: "-1", text: "Select an Option" },
                    dropdownParent: modalAdvance,
                    data: _emp,
                }).on('select2:select', function(e){
                    let { search } = vmTab2;
                    const data = e.params.data;
                    vmTab2.search.author = data.id;
                });;
            }
        }, setCompanySelect2(targetElement, modalContent = null, select2Destroy = false){
            const currentTarget = $(targetElement);
            if(typeof modalContent != 'undefined' && modalContent){
                if(select2Destroy){ currentTarget.empty(); }

                modalContent.find(targetElement).select2({
                    width: '100%',
                    placeholder:  { id: "-1", text: "Select an Option" },
                    data: _comp,
                    dropdownParent: modalAdvance,
                }).on("select2:select", function(e){
                    let { search } = vmTab2;
                    const data = e.params.data;
                    
                    let newData = [];
                    const tempData = $(this).select2("data");
                    tempData.forEach((value) => { newData.push(value.id); });
        
                    vmTab2.search.companies = newData;
                }).on("select2:unselect", function(e){
                    let { search } = vmTab2;
                    const data = e.params.data;
                    
                    let newData = [];
                    const tempData = $(this).select2("data");
                    tempData.forEach((value) => { newData.push(value.id); });
        
                    vmTab2.search.companies = newData;
                });
            }
        }, setCategorySelect2(targetElement, modalContent = null, select2Destroy = false){
            const currentTarget = $(targetElement);
            if(typeof modalContent != 'undefined' && modalContent){
                if(select2Destroy){ currentTarget.empty(); }

                modalContent.find(targetElement).select2({
                    width: '100%',
                    placeholder:  { id: "-1", text: "Select an Option" },
                    data: _category,
                    dropdownParent: modalAdvance,
                }).on("select2:select", function(e){
                    let { search } = vmTab2;
                    const data = e.params.data;
                    
                    let newData = [];
                    const tempData = $(this).select2("data");
                    tempData.forEach((value) => { newData.push(value.id); });
        
                    search.category = newData;
                }).on("select2:unselect", function(e){
                    let { search } = vmTab2;
                    const data = e.params.data;
                    
                    let newData = [];
                    const tempData = $(this).select2("data");
                    tempData.forEach((value) => { newData.push(value.id); });
        
                    search.category = newData;
                });
            }
        }
    }
});

var vmTab2 = new Vue({
    el: "#modalSearchForm",
    data: { search: {} },
});

$(document).on('click', '#advance-search', function(){
    if (typeof modalAdvance !== "undefined" && modalAdvance.length == 1) {
        var tempModalContent = modalAdvance.find("#modalTempContainer");
        modalAdvance.modal("show");

        vmTab1.setAuthorSelect2('#author', tempModalContent, true);
        vmTab1.setCompanySelect2('#companies', tempModalContent, true);
        vmTab1.setCategorySelect2('#category', tempModalContent, true);

        tempModalContent.find('#modal-advance-search-submit').on('click', function(){
            advance_search = vmTab2.search;
            modalAdvance.modal("hide");
            vmTab1.table.ajax.reload();
        });
    }
});

$(document).on('click', '#timeline-advance-search', function(){
    if (typeof modalAdvance !== "undefined" && modalAdvance.length == 1) {
        var tempModalContent = modalAdvance.find("#modalTempContainer");
        modalAdvance.modal("show");

        vmTab1.setAuthorSelect2('#author', tempModalContent, true);
        vmTab1.setCompanySelect2('#companies', tempModalContent, true);
        vmTab1.setCategorySelect2('#category', tempModalContent, true);

        tempModalContent.find('#modal-advance-search-submit').on('click', function(){
            advance_search = vmTab2.search;
            modalAdvance.modal("hide");
            vmTab1.getTimeline();
        });
    }
});

function clear_advance_search(){
    $("#advance-search-form").trigger('reset');
    var tempModalContent = modalAdvance.find("#modalTempContainer");
    vmTab1.setAuthorSelect2('#author', tempModalContent, true);
    vmTab1.setCompanySelect2('#companies', tempModalContent, true);
    vmTab1.setCategorySelect2('#category', tempModalContent, true);
    vmTab2.search = Object.assign({});

    vmTab1.table.ajax.reload();
}
