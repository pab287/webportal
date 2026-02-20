let _employee = null;
let _projects = null;
let _req_types = null;
let rfiTable = null;
let rfaTable = null;
let is_archive = 0;
let informationEditor;

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

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.employee !== "undefined" && _tempContentData.employee.length > 0){
        _employee = _tempContentData.employee;
    }
    if(typeof _tempContentData.projects !== "undefined" && _tempContentData.projects.length > 0){
        _projects = _tempContentData.projects;
    }
    if(typeof _tempContentData.req_types !== "undefined" && _tempContentData.req_types.length > 0){
        _req_types = _tempContentData.req_types;
    }
}

let rfi_vue = new Vue({
    el: "#request-content",
    data: {
        req_types: _req_types,
        selectedType: null,
        otherText: '',
        attachments:{
            className: "",
            count: 0,
            uploadedFiles: [],
        }
    },

    computed: {
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
        isOthersSelected: function () {
            const others = this.req_types.find(t =>
                t.type_name.toLowerCase() === 'others'
            );
            return others && this.selectedType == others.type_name;
        }
    },

    methods: {
        getExtension: function(type) {
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
        getClass: function(type) {        
            let extension = mimeMap[type] || (type.includes('/') ? type.split('/').pop() : type);
            extension = extension.toLowerCase();
            const classMap = {
                "doc": "m-widget4 m-widget2__item m-widget2__item--primary col-lg-4 col-md-12 col-sm-12",
                "docx": "m-widget4 m-widget2__item m-widget2__item--primary col-lg-4 col-md-12 col-sm-12",
                "pdf": "m-widget4 m-widget2__item m-widget2__item--danger col-lg-4 col-md-12 col-sm-12",
                "jpg": "m-widget4 m-widget2__item m-widget2__item--success col-lg-4 col-md-12 col-sm-12",
                "jpeg": "m-widget4 m-widget2__item m-widget2__item--success col-lg-4 col-md-12 col-sm-12"
            };
        
            return classMap[extension] || "m-widget4 m-widget2__item m-widget2__item--default col-lg-4 col-md-12 col-sm-12";
        },
        fileDelete: function(id){
            console.log(id);
            this.attachments.uploadedFiles.pop(id);
            this.attachments.count = this.attachments.uploadedFiles.length;
        },
        handleTypeSelect(type) {
            const employee = _employee.find(emp => 
                emp.id == type.person_in_charge
            );
            if (employee) {
                $('#consultant').val(employee.text);
            } else {
                $('#consultant').val('');
            }
        },
        formatLabel: function (value) {
            return value.replace(/\b\w/g, function (l) {
                return l.toUpperCase();
            });
        },
        clearForm: function () {
            console.log("CLEAR");
        }
    }
});



$('#requested_by').select2({
    width: '100%',
    placeholder: 'Select requestor',
    allowClear: true,
    data: _employee
});

// $('#consultant').select2({
//     width: '100%',
//     placeholder: 'Select an option',
//     allowClear: false,
//     data: _employee
// }).prop('disabled', true);


$('#project_name').select2({
    width: '100%',
    placeholder: 'Select project',
    allowClear: true,
    data: _projects
});

$("#project_name").on("select2:select", function (e) {
    const data = e.params.data;
    $('#project_location').val(data.project_location);
}).on("select2:unselect", function () {
    $('#project_location').val('');
});

$('#prepared_dt').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    autoUpdateInput: false,
    minDate: moment('2023-01-01'),
    maxDate: moment().add(365, 'days'),
    locale: {
        format: 'MMM DD, YYYY',
        cancelLabel: 'Clear'
    }
});

$('#prepared_dt').on('apply.daterangepicker', function (ev, picker) {
    $(this).val(picker.startDate.format('MMM DD, YYYY'));
});

$('#prepared_dt').on('cancel.daterangepicker', function () {
    $(this).val('');
});

$('#reply_needed').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    autoUpdateInput: false,
    minDate: moment('2023-01-01'),
    maxDate: moment().add(365, 'days'),
    locale: {
        format: 'MMM DD, YYYY',
        cancelLabel: 'Clear'
    }
});

$('#reply_needed').on('apply.daterangepicker', function (ev, picker) {
    $(this).val(picker.startDate.format('MMM DD, YYYY'));
});

$('#reply_needed').on('cancel.daterangepicker', function () {
    $(this).val('');
});

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

$.validate({
    form: "#rfi-form",
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
    },
    onSuccess: function (form) {
        let formData = $(form).serializeArray();
        formData.push({ name: 'csrf_token', value: _csrf_hash });
        $.ajax({
            url: siteUrl("eforms/engineering_request_forms/save_rfi"),
            type: "POST",
            dataType: "json",
            data: formData,
            success: function (response) {
                
            }
        });
        return false;
    }
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
    console.log("FILE",file);
    if (file.size > maxFileSize) {
        toastr.error(`File "${file.name}" is too large. Maximum size is 10MB.`);
        return false;
    }
    
    if (!allowedTypes.includes(file.type)) {
        toastr.error(`File "${file.name}" has an unsupported format. Only PDF and DOCX files are allowed.`, 'danger');
        return false;
    }
    
    const exists = rfi_vue.attachments.uploadedFiles.some(f => f.name === file.name);
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
    rfi_vue.attachments.uploadedFiles.push(fileObj);
}