let _request = false;
let _req_types = false;
let _attachments = false;
let _replies = false;
let _reply_attachments = [];
let informationEditor;
let id = null;
let reply_id = null;
let fileStore = {};
let _assignatory = null;

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(_tempContentData.request && Object.keys(_tempContentData.request).length > 0){
        _request = _tempContentData.request;
    }
    if(_tempContentData.req_types && Object.keys(_tempContentData.req_types).length > 0){
        _req_types = _tempContentData.req_types;
    }
    if(_tempContentData.attachments && Object.keys(_tempContentData.attachments).length > 0){
        _attachments = _tempContentData.attachments;
    }
    if (_tempContentData.reply && Object.keys(_tempContentData.reply).length > 0) {
        _replies = _tempContentData.reply;
    }
    if(_tempContentData.reply_attachments && Object.keys(_tempContentData.reply_attachments).length > 0){
        _reply_attachments = _tempContentData.reply_attachments;
    }
    if(_tempContentData.assignatory && Object.keys(_tempContentData.assignatory).length > 0){
        _assignatory = _tempContentData.assignatory;
    }
}

const maxFileSize = 50 * 1024 * 1024; // 50MB

const allowedTypes = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg'
];

const mimeMap = {
    "application/pdf": "pdf",
    "application/msword": "doc",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document": "docx",
    "image/jpeg": "jpg"
};

const remarksMap = {
    approved: 'approve_remarks',
    pending: 'disapprove_remarks',
    noted: 'note_remarks',
};

const messages = {
    approved: "APPROVE THIS REPLY?",
    disapproved: "DISAPPROVE THIS REPLY?",
    noted: "MARK THIS REPLY AS NOTED?",
};

let edit_rfi = new Vue ({
    el: '#edit_rfi_content',
    data: {
        isSubmitting: false,
        isProcessingReply: false,
        activity_logs: {},
        isImage: false,
        attachmentsToUpload: [],
        content: _request,
        req_types: _req_types,
        selectedType: null,
        attachments: _attachments,
        filePath: null,
        reply : _replies,
        assignatory : _assignatory,
        replyAttachments: _reply_attachments,
        currentReply: "",
        currentAttachment: null,
        changes:{
            reply: false,
            attachment: false
        },
        attachmentsToAdd: [],
        attachmentsToRemove: [],
        showUpdate: false,
        
    },
    mounted: function () {
        loadEditor(this.reply.reply);
        $('#fileupload-error').hide();
        id = this.content.id;
        reply_id = this.reply.id;
        if (this.reply && this.reply.reply !== undefined) {
            this.currentReply = JSON.parse(JSON.stringify(this.reply.reply));
        }
        
        if (this.replyAttachments !== undefined) {
            this.currentAttachment = JSON.parse(JSON.stringify(this.replyAttachments));
        }
    },
    computed: {
        hasFile() {
            if(this.replyAttachments.length > 0 && this.replyAttachments !== undefined){
                return true;
            }else{
                return false;
            }
        },
        reqNoted(){
            if(this.reply.status == "noted"){
                return true;
            }else{
                return false;
            }
        },
        canEdit(){
            if(_actions.includes('btnCanreply')){
                return true;
            }else{
                return false;
            }
        },
        isOthersSelected: function () {
            const others = this.req_types.find(t =>
                t.type_name.toLowerCase() === 'others'
            );
            return others && this.selectedType == others.type_name;
        },
        sortedTypes: function () {
            let types = [...this.req_types];
            types.sort(function (a, b) {
                if (a.type_name.toLowerCase() === 'others') return 1;
                if (b.type_name.toLowerCase() === 'others') return -1;

                return a.type_name.localeCompare(b.type_name);
            });
            return types;
        },

        chunkedTypes: function () {
            const columns = 3;
            const sorted = this.sortedTypes;
            const rows = Math.ceil(sorted.length / columns);
            let result = [];
            for (let r = 0; r < rows; r++) {
                let row = [];
                for (let c = 0; c < columns; c++) {
                    const index = r + rows * c;
                    if (sorted[index]) {
                        row.push(sorted[index]);
                    }
                }
                result.push(row);
            }
            return result;
        },
    },
    methods: {
        onFileChange(event) {
            const files = event.target.files;
            handleFiles(files);     
            this.checkChanges();
        },
        fileDelete(index){
            const file = this.replyAttachments[index];
            delete fileStore[file.id];
            this.replyAttachments.splice(index,1);
            this.replyAttachments.count = this.replyAttachments.length;
            this.checkChanges();
        },
        checkChanges() {
            const cleanName = (name) =>
                name.trim()
                    .replace(/[()]/g, '')
                    .replace(/\s+/g, '')
                    .toLowerCase();
        
            const replyFiles = this.replyAttachments.map(f => ({
                original: f,
                name: cleanName(f.filename)
            }));
        
            const currentFiles = this.currentAttachment.map(f => ({
                original: f,
                name: cleanName(f.filename)
            }));
        
            const replyNames = replyFiles.map(f => f.name);
            const currentNames = currentFiles.map(f => f.name);
            this.attachmentsToAdd = replyFiles.filter(f => !currentNames.includes(f.name)).map(f => f.original.filename || f.original.name);
            this.attachmentsToUpload = replyFiles.filter(f => !currentNames.includes(f.name)).map(f => f.original.id || f.original.id);
            this.attachmentsToRemove = currentFiles.filter(f => !replyNames.includes(f.name)).map(f => f.original.filename || f.original.name);
            this.changes.attachment = this.attachmentsToAdd.length > 0 || this.attachmentsToRemove.length > 0;
        },
        editReply() {
            this.showUpdate = !this.showUpdate;
            if (this.showUpdate) {
                informationEditor.disableReadOnlyMode('reply-lock');

            } else {
                informationEditor.enableReadOnlyMode('reply-lock');
            }
        },
        processReply(status) {
            const _this = this;
            _this.isProcessingReply = true;
            Swal.fire({
                title: messages[status] || "UPDATE REPLY STATUS?",
                icon: "question",
                input: "textarea",
                inputLabel: "REMARKS",
                showCancelButton: true,
                confirmButtonText: "OK",
                cancelButtonText: "CANCEL",
                customClass: {
                    input: "form-control",
                },
                inputValidator: (value) => {
                    if (!value) return "THIS IS A REQUIRED FIELD";
                  }
            }).then((result) => {
                if (!result.isConfirmed){
                    _this.isProcessingReply = false;
                    return;
                } 
                $.ajax({
                    url: baseUrl('eforms/engineering_request_forms/process_reply'),
                    method: "POST",
                    data: {
                        csrf_token: _csrf_hash,
                        set_status: status,
                        remarks: result.value,
                        consultant: _this.content.consultant,
                        requestor: _this.content.requested_by,
                        creator: _this.content.created_by,
                        ..._this.reply
                    },
                    success: function (res) {
                        if (res.success) {
                            toastr.success(res.message, "Success", { timeOut: 5000 });
                            setTimeout(function () {
                                window.location.reload();
                            }, 1500);
                        } else {
                            toastr.error(res.message);
                            _this.isProcessingReply = false;
                        }
                    },
                    error: function () {
                        toastr.error("Request failed.");
                        _this.isProcessingReply = false;
                    }
                });
            });
        },
        formatDate(date) {
            return moment(date).format('MMMM D, YYYY');
        },
        formatLabel(value) {
            return value.replace(/\b\w/g, function (l) {
                return l.toUpperCase();
            });
        },
        getExtension(name) {
            if (!name || typeof name !== "string") {
                return baseUrl("assets/images/file_icons/default.svg");
            }
            const extension = name.substring(name.lastIndexOf('.') + 1).toLowerCase();
            const iconMap = {
                doc: "doc.svg",
                docx: "doc.svg",
                pdf: "pdf.svg",
                jpg: "jpg.svg",
                jpeg: "jpg.svg"
            };
            const fileName = iconMap[extension] || "default.svg";
            return baseUrl(`assets/images/file_icons/${fileName}`);
        },
        getClass(name) {
            if (!name || typeof name !== "string") {
                return "m-widget4 m-widget2__item m-widget2__item--default col-lg-4 col-md-12 col-sm-12";
            }
            const extension = name.substring(name.lastIndexOf('.') + 1).toLowerCase();
        
            const classMap = {
                doc: "m-widget4 m-widget2__item m-widget2__item--primary col-lg-4 col-md-12 col-sm-12",
                docx: "m-widget4 m-widget2__item m-widget2__item--primary col-lg-4 col-md-12 col-sm-12",
                pdf: "m-widget4 m-widget2__item m-widget2__item--danger col-lg-4 col-md-12 col-sm-12",
                jpg: "m-widget4 m-widget2__item m-widget2__item--success col-lg-4 col-md-12 col-sm-12",
                jpeg:"m-widget4 m-widget2__item m-widget2__item--success col-lg-4 col-md-12 col-sm-12"
            };
        
            return classMap[extension] ||
                "m-widget4 m-widget2__item m-widget2__item--default col-lg-4 col-md-12 col-sm-12";
        },
        openFile(file, reply = false) {
            const filename = file.filename;
            const extension = filename.split('.').pop().toLowerCase();
            const rep = reply ? "reply" : "request";
            const wordExtensions = ['doc', 'docx'];
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        
            const isWord = wordExtensions.includes(extension);
            this.isImage = imageExtensions.includes(extension);
        
            if (file.url?.startsWith('blob:')) {
                if (isWord) {
                    window.open(file.url, '_blank');
                } else {
                    this.filePath = file.url;
                    $("#fileViewModal").modal("show");
                }
                return;
            }
        
            const encodedFilename = encodeURIComponent(filename);
            const fileUrl = baseUrl(`uploads/files/engineering_request/rfi_${id}/${rep}/${encodedFilename}`);
        
            $.ajax({
                url: fileUrl,
                type: 'HEAD',
                global: false,
                success: () => {
                    if (isWord) {
                        window.open(fileUrl, '_blank');
                        toastr.info('The document has been downloaded.');
                    } else {
                        this.filePath = fileUrl;
                        $("#fileViewModal").modal("show");
                    }
                },
                error: () => {
                    toastr.error('File not found or already removed.');
                }
            });
        },
        getAttachExtension(type) {
            let extension = mimeMap[type] || (type.includes('/') ? type.split('/').pop() : type);
            extension = extension.toLowerCase();
            const iconMap = {
                "doc": "doc.svg",
                "docx": "doc.svg",
                "pdf": "pdf.svg",
                "jpg": "jpg.svg",
                "jpeg": "jpg.svg"
            };
        
            const fileName = iconMap[extension] || "default.svg";
            return baseUrl(`assets/images/file_icons/${fileName}`);
        },
        getAttachClass(type) {        
            let extension = mimeMap[type] || (type.includes('/') ? type.split('/').pop() : type);
            extension = extension.toLowerCase();
            const classMap = {
                "doc": "m-widget4 m-widget2__item m-widget2__item--primary col-lg-6 col-md-12 col-sm-12",
                "docx": "m-widget4 m-widget2__item m-widget2__item--primary col-lg-6 col-md-12 col-sm-12",
                "pdf": "m-widget4 m-widget2__item m-widget2__item--danger col-lg-6 col-md-12 col-sm-12",
                "jpg": "m-widget4 m-widget2__item m-widget2__item--success col-lg-6 col-md-12 col-sm-12",
                "jpeg": "m-widget4 m-widget2__item m-widget2__item--success col-lg-6 col-md-12 col-sm-12"
            };
        
            return classMap[extension] || "m-widget4 m-widget2__item m-widget2__item--default col-lg-4 col-md-12 col-sm-12";
        },
    }
})


function handleFiles(fileList) {
    $.each(fileList, function(index, file) {
        if (validateFile(file)) {
            addFile(file);
        }
    });
}

function cleanName(name){
    return name.trim().replace(/[()]/g, '').replace(/\s+/g, '') .toLowerCase();
};

function validateFile(file) {
    if (file.size > maxFileSize) {
        toastr.error(`File "${file.name}" is too large. Maximum size is 10MB.`);
        return false;
    }
    
    if (!allowedTypes.includes(file.type)) {
        toastr.error(`File "${file.name}" has an unsupported format. Only PDF and DOCX files are allowed.`, 'danger');
        return false;
    }
    
    const exists = edit_rfi.replyAttachments.some(f =>
        cleanName(f.filename) === cleanName(file.name)
    );

    if (exists) {
        toastr.error(`File "${file.name}" is already selected.`);
        return false;
    }
    return true;
}

function addFile(file) {
    const id = 'f' + Math.floor(1000 + Math.random() * 9000);
    let fileObj = {
        id: id,
        filename: cleanName(file.name),
        type: file.type,
        size: file.size,
        url: URL.createObjectURL(file),
    };
    fileStore[id] = file;
    edit_rfi.replyAttachments.push(fileObj);
}





function loadEditor(reply){
    reply = reply ?? "";
    let isReady = false;
    ClassicEditor.create(document.querySelector('#reply_needed'), {
        toolbar: [
            'bold',
            'italic',
            'bulletedList',
            'numberedList',
            'blockQuote',
            'undo',
            'redo'
        ],
    })
    .then(editor => {
        informationEditor = editor;
        editor.editing.view.change(writer => {
            writer.setStyle(
                'min-height',
                '100px',
                editor.editing.view.document.getRoot()
            );
        });

        editor.setData(reply);
        isReady = true;

        editor.model.document.on('change:data', () => {
            if (!isReady) return;
            const data = editor.getData();
            const plainText = $('<div>').html(data).text().trim();
            $('#reply_needed-error').toggle(!plainText);
            edit_rfi.changes.reply = (data !== edit_rfi.currentReply);
        });

        editor.enableReadOnlyMode('reply-lock');
    })
    .catch(error => {
        // console.error(error);
    });
}

$.validate({
    form: "#edit_reply_form",
    lang: "en",
    scrollToTopOnError: false,
    onValidate: function (form) {

        if (informationEditor) {
            const data = informationEditor.getData();
            const plainText = data.replace(/<[^>]*>/g, '').trim();
            $('#reply_needed').val(data);
            if (!plainText) {
                $('#reply_needed-error').show();
                $('.ck-editor__editable').addClass('is-invalid');
                return false;
            } else {
                $('#reply_needed-error').hide();
                $('.ck-editor__editable').removeClass('is-invalid');
            }
        }
        return true;
    },
    onSuccess: function () {
        if (informationEditor) {
            const data = informationEditor.getData();
            const plainText = data.replace(/<[^>]*>/g, '').trim();
            $('#reply_needed').val(data);
            if (!plainText) {
                $('#reply_needed-error').show();
                $('.ck-editor__editable').addClass('is-invalid');
                return false;
            } else {
                $('#reply_needed-error').hide();
                $('.ck-editor__editable').removeClass('is-invalid');
            }
        }
        if (edit_rfi.replyAttachments.length <= 0) {
            $('#fileupload-error').show();
            return false;
        }
        edit_rfi.isSubmitting = true;
        const form = $('#edit_reply_form');
        const formData = new FormData(form[0]);
        const toUpload = edit_rfi.attachmentsToUpload;
        formData.append('attachmentsToAdd',JSON.stringify(edit_rfi.attachmentsToAdd));
        formData.append('attachmentsToRemove',JSON.stringify(edit_rfi.attachmentsToRemove));
        formData.append('csrf_token', _csrf_hash);
        formData.append('rfi_id', id);
        formData.append('reply_id', reply_id);
        toUpload.forEach(fileObj => {
            const rawFile = fileStore[fileObj];
            if (rawFile) {
                formData.append('files[]', rawFile, fileObj.name);
            }
        });

        $.ajax({
            url: siteUrl("eforms/engineering_request_forms/update_reply"),
            type: "POST",
            dataType: "JSON",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    toastr.success(res.message, "Success", {
                        timeOut: 3000,         
                        closeButton: true,
                        progressBar: true
                    });
            
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                }
            }
        });
        return false; 
    }
});