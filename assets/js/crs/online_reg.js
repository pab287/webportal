let position,referral, schools, courses;

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.position != "undefined" && _tempContentData.position.length > 0){ position = _tempContentData.position; }
    if(typeof _tempContentData.referral != "undefined" && _tempContentData.referral.length > 0){ referral = _tempContentData.referral; }
    if(typeof _tempContentData.schools != "undefined" && _tempContentData.schools.length > 0){ schools = _tempContentData.schools; }
    if(typeof _tempContentData.courses != "undefined" && _tempContentData.courses.length > 0){ courses = _tempContentData.courses; }
}

const forms = [
    "#personal_information_form",
    "#additional_information_form",
    "#contact_information_form",
    "#work_experience_form",
    "#application_information_form",
    "#resume_upload_form"
];

const recruitmentSources = [
    { id: 'mynimo', text: 'MYNIMO' },
    { id: 'jobstreet', text: 'JOBSTREET' },
    { id: 'facebook', text: 'FACEBOOK' },
    { id: 'linkedin', text: 'LINKEDIN' },
    { id: 'walkin', text: 'WALK IN' },
    { id: 'referral', text: 'REFERRAL' },
    { id: 'jobfair', text: 'JOB FAIR' },
    { id: 'indeed', text: 'INDEED' }
];

const civilStatusOptions = [    
    { id: 'single', text: 'SINGLE' },
    { id: 'married', text: 'MARRIED' },
    { id: 'separated', text: 'SEPARATED' },
    { id: 'divorced', text: 'DIVORCED' },
    { id: 'widowed', text: 'WIDOWED' },
    { id: 'annulled', text: 'ANNULLED' },
    { id: 'other', text: 'OTHER' }
];

const genderOptions = [    
    { id: 'male', text: 'MALE' },
    { id: 'female', text: 'FEMALE' },
];

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


$(document).ready(function () {

    
    $('#applied_dt').datepicker({
        endDate: new Date(),
        todayHighlight: true,
        autoclose: true,
        todayBtn: 'linked',
        format: 'mm/dd/yyyy',
        forceParse: false
    });

    let today = new Date();
    let month = ('0' + (today.getMonth() + 1)).slice(-2);
    let day = ('0' + today.getDate()).slice(-2);
    let year = today.getFullYear();
    let formatted = month + '/' + day + '/' + year;
    $('#applied_dt').val(formatted);

    $("#referral").select2({
        placeholder: 'SELECT AN OPTION',
        width: '100%',
        data: referral,
        allowClear: true,
    });

    $("#school_id").select2({
        placeholder: 'SELECT AN OPTION',
        width: '100%',
        data: schools,
        multiple: true,
    });

    $("#course_id").select2({
        placeholder: 'SELECT AN OPTION',
        width: '100%',
        data: courses,
        multiple: true,
    });

    $("#gender").select2({
        width: '100%',
        placeholder: 'SELECT GENDER',
        data: genderOptions,
    });

    $('#birthdate').datepicker({
        endDate: new Date(),
        todayHighlight: true,
        autoclose: true,
        pickerPosition: 'bottom left',
        // todayBtn: 'linked',
        format: 'mm/dd/yyyy',
        forceParse: false
    });

    $("#position_id").select2({
        placeholder: 'SELECT AN OPTION',
        width: '100%',
        data: position,    
    });

    $("#recruitment").select2({
        width: '100%',
        placeholder: 'SELECT SOURCE',
        data: recruitmentSources,
    }).on('select2:select', function (e) {
        const selectedValue = e.params.data.id;
    
        if (selectedValue == 'referral') {
            $('.referral').removeClass('d-none');
        } else {
            $('.referral').addClass('d-none');
            $('#referral').val(null).trigger('change'); // Optional: clear selection
        }
    });

});

function formatDate(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length > 2 && value.length <= 4) {
        value = value.slice(0,2) + '/' + value.slice(2);
    } else if (value.length > 4 && value.length <= 8) {
        value = value.slice(0,2) + '/' + value.slice(2,4) + '/' + value.slice(4);
    } else if (value.length > 8) {
        value = value.slice(0,8); 
        value = value.slice(0,2) + '/' + value.slice(2,4) + '/' + value.slice(4);
    }
    input.value = value;
}

let application_vue = new Vue({
    el: "#m_content",
    data: { 
        className: "",
        count: 0,
        uploadedFiles: [],
        currentStep:"#personal_information",
        tabs: {
            "#personal_information": false,
            "#additional_information": false,
            "#contact_information": false,
            "#work_experience": false,
            "#application_information": false,
            "#resume_upload": false
        }
    },
    computed: {
        // isLastStep() {
        //     return this.currentStep === this.steps.length - 1;
        // }
    },
    methods: {
        submitApplication() {
            this.submitBtn = true;
            console.log("Submitting full application");
        },
        // goToStep(index) {
        //     if (this.canAccessStep(index)) {
        //         this.currentStep = index;

        //         $('.nav-tabs a[href="' + this.steps[index] + '"]').tab('show');
        //     }
        // },
        canAccessStep(index) {
            if (index <= this.currentStep) return true;
            return this.completedSteps.includes(index - 1);
        },
        markStepComplete(index) {
            if (!this.completedSteps.includes(index)) {
                this.completedSteps.push(index);
            }
        },
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
            return baseUrl + `assets/images/file_icons/${fileName}`;
        },
        getClass: function(type) {        
            let extension = mimeMap[type] || (type.includes('/') ? type.split('/').pop() : type);
            extension = extension.toLowerCase();
            const classMap = {
                "doc": "m-widget4 m-widget2__item m-widget2__item--primary col-lg-4 col-md-4 col-sm-12",
                "docx": "m-widget4 m-widget2__item m-widget2__item--primary col-lg-4 col-md-4 col-sm-12",
                "pdf": "m-widget4 m-widget2__item m-widget2__item--danger col-lg-4 col-md-4 col-sm-12",
                "jpg": "m-widget4 m-widget2__item m-widget2__item--success col-lg-4 col-md-4 col-sm-12",
                "jpeg": "m-widget4 m-widget2__item m-widget2__item--success col-lg-4 col-md-4 col-sm-12"
            };
        
            return classMap[extension] || "m-widget4 m-widget2__item m-widget2__item--default col-lg-4 col-md-4 col-sm-12";
        },
        fileDelete: function(id){
            this.uploadedFiles.pop(id);
            this.count = this.uploadedFiles.length;
        },
    },
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
    
    const exists = application_vue.uploadedFiles.some(f => f.name === file.name);
    if (exists) {
        toastr.error(`File "${file.name}" is already selected.`);
        return false;
    }
    
    return true;
}

function addFile(file) {
    const fileObj = {
        id: 'f' + Math.floor(1000 + Math.random() * 9000),
        name: file.name,
        type: file.type,
        size: file.size,
    };
    application_vue.uploadedFiles.push(fileObj);
}

forms.forEach(function (form) {
    $.validate({
        form: form,
        onSuccess: function () {
            console.log("success");
            return false;
        },
        onError: function () {
            console.log("error");
            return false;
        }
    });
});


$('#next').on('click', function () {
    let currentForm = application_vue.currentStep + "_form";
    $(currentForm).submit();
});

$('#back').on('click', function () {

});


$('.nav-tabs a').on('shown.bs.tab', function (e) {
    let tabId = $(this).attr('href');
    application_vue.currentStep = tabId;
    console.log(application_vue.currentStep);
});

