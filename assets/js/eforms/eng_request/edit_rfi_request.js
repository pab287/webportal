let _request = null;
let _req_types = null;
let _attachments = null;
let _replies = null;
let _reply_attachments = null;
let informationEditor;

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){

    if(typeof _tempContentData.request !== "undefined" && Object.keys(_tempContentData.request).length > 0){
        _request = _tempContentData.request;
    }

    if(typeof _tempContentData.req_types !== "undefined" && Object.keys(_tempContentData.req_types).length > 0){
        _req_types = _tempContentData.req_types;
    }

    if(typeof _tempContentData.attachments !== "undefined" && Object.keys(_tempContentData.attachments).length > 0){
        _attachments = _tempContentData.attachments;
    }

    if(typeof _tempContentData.reply !== "undefined" && Object.keys(_tempContentData.reply).length > 0){
        _replies = _tempContentData.reply;
    }

    if(typeof _tempContentData.reply_attachments !== "undefined" && Object.keys(_tempContentData.reply_attachments).length > 0){
        _reply_attachments = _tempContentData.reply_attachments;
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

let edit_rfi = new Vue ({
    el: '#edit_rfi_content',
    data: {
        activity_logs: {},
        content: _request,
        req_types: _req_types,
        selectedType: null,
        attachments: _attachments,
        replyAttachmentsUpload: {
            className: "",
            count: 0,
            uploadedFiles: [],
        },
        filePath: null,
        reply : _replies,
        replyAttachments: _reply_attachments
    },
    mounted: function () {
        // console.log(this.attachments);
    },
    computed: {
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
        // formatReply(replies){
        //     if (!replies) return '';
        //     const list = Array.isArray(replies) ? replies : Object.values(replies);
        //     return list.map(r => `
        //         <div class="mb-2">
        //             ${r.reply}
        //             <div>
        //                 <small class="text-muted">${r.created_at}</small>
        //                 <small class="ml-2">${r.created_by_name || ''}</small>
        //             </div>
        //         </div>
        //     `).join('<hr>');
        // },
        formatDate(date) {
            return moment(date).format('MMMM D, YYYY');
        },
        formatLabel: function (value) {
            return value.replace(/\b\w/g, function (l) {
                return l.toUpperCase();
            });
        },
        getExtension: function (name) {
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
        getClass: function (name) {

            if (!name || typeof name !== "string") {
                return "m-widget4 m-widget2__item m-widget2__item--default col-lg-4 col-md-12 col-sm-12";
            }
        
            // get extension safely
            const extension = name
                .substring(name.lastIndexOf('.') + 1)
                .toLowerCase();
        
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
        openFile(filename) {
            const extension = filename.split('.').pop().toLowerCase();
            const wordExtensions = ['doc', 'docx'];
        
            if (wordExtensions.includes(extension)) {
                const encodedFilename = encodeURIComponent(filename);
                const downloadPath = baseUrl() + `uploads/files/engineering_request/rfi_${this.content.id}/${encodedFilename}`;
                window.open(downloadPath, '_blank');
                toastr.info('The document has been downloaded.');
                return;
            }
        
            const encodedFilename = encodeURIComponent(filename);
            this.filePath = baseUrl() + `uploads/files/engineering_request/rfi_${this.content.id}/${encodedFilename}`;
            $("#fileViewModal").modal("show");
        },

        getAttachExtension: function(type) {
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
        getAttachClass: function(type) {        
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
        fileDelete: function(index){
            this.replyAttachments.uploadedFiles.splice(index,1);
            this.replyAttachments.count = this.replyAttachments.uploadedFiles.length;
        },

    }
})

ClassicEditor.create( document.querySelector('#information_needed' ),{
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
    informationEditor.model.document.on('change:data', () => {
        const data = informationEditor.getData();
        const plainText = data.replace(/<[^>]*>/g, '').trim();
        if (plainText) {
            $('#information_needed-error').hide();
        }else{
            $('#information_needed-error').show();
        }
    });
    editor.editing.view.change(writer => {
        writer.setStyle(
            'min-height',
            '100px',
            editor.editing.view.document.getRoot()
        );
    });
})
.catch( error => {
});


$('#fileupload').on('change', function(e) {
    handleFiles(e.target.files);
});

function handleFiles(fileList) {
    $.each(fileList, function(index, file) {
        if (validateFile(file)) {
            addFile(file);
        }
    });
}

function validateFile(file) {
    if (file.size > maxFileSize) {
        toastr.error(`File "${file.name}" is too large. Maximum size is 10MB.`);
        return false;
    }
    
    if (!allowedTypes.includes(file.type)) {
        toastr.error(`File "${file.name}" has an unsupported format. Only PDF and DOCX files are allowed.`, 'danger');
        return false;
    }
    
    const exists = edit_rfi.replyAttachments.uploadedFiles.some(f => f.name === file.name);
    if (exists) {
        toastr.error(`File "${file.name}" is already selected.`);
        return false;
    }
    return true;
}

function addFile(file) {
    let fileObj = {
        id: 'f' + Math.floor(1000 + Math.random() * 9000),
        name: file.name,
        type: file.type,
        size: file.size,
    };
    edit_rfi.replyAttachments.uploadedFiles.push(fileObj);
}

$('#replyModal').on('shown.bs.modal', function () {
    let id = edit_rfi.content.id;
    $.validate({
        form: "#reply_form",
        lang: "en",
        scrollToTopOnError: false,
        onValidate: function () {
            if (informationEditor) {
                const data = informationEditor.getData();
                const plainText = data.replace(/<[^>]*>/g, '').trim();
                $('#information_needed').val(data);
                if (!plainText) {
                    $('#information_needed-error').show();
                    $('.ck-editor__editable')
                        .addClass('is-invalid');
                    return false;
                } else {
                    $('#information_needed-error').hide();
                    $('.ck-editor__editable').removeClass('is-invalid');
                }
            }
            return true;
        },
        onSuccess: function () {
            const form = $('#reply_form');
            const formData = new FormData(form[0]);
            formData.append('csrf_token', _csrf_hash);
            formData.append('rfi_id', id);
            $.ajax({
                url: siteUrl("eforms/engineering_request_forms/save_reply"),
                type: "POST",
                dataType: "JSON",
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    if(res.success){
                        $('#replyModal').modal('hide');
                    }
                }
            });
            return false; 
        }
    });

});
