let position, schools, courses;

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.position != "undefined" && _tempContentData.position.length > 0){ position = _tempContentData.position; }
    if(typeof _tempContentData.schools != "undefined" && _tempContentData.schools.length > 0){ schools = _tempContentData.schools; }
    if(typeof _tempContentData.courses != "undefined" && _tempContentData.courses.length > 0){ courses = _tempContentData.courses; }
}

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

const levelOptions = [
    { id: "Primary", text: "Primary" },
    { id: "Secondary", text: "Secondary" },
    { id: "Senior High School", text: "Senior High School" },
    { id: "Vocational", text: "Vocational / Technical" },
    { id: "College", text: "College" },
    { id: "Post Graduate", text: "Post Graduate" }
];

const genderOptions = [    
    { id: 'male', text: 'MALE' },
    { id: 'female', text: 'FEMALE' },
];

const maxFileSize = 50 * 1024 * 1024; // 50MB
const allowedTypes = [
    'application/pdf',
    // 'application/msword',
    // 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    // 'image/jpeg'
  ];

const mimeMap = {
    "application/pdf": "pdf",
    // "application/msword": "doc",
    // "application/vnd.openxmlformats-officedocument.wordprocessingml.document": "docx",
    // "image/jpeg": "jpg"
};

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
        isFreshGraduate: false,
        contactFormData: {
            contact_no: "", email: "", tel_no: "", address: "", permanent_address: "",
        },
        references: [
            { ref_name: "", ref_contact_no: "", ref_address: "", ref_company: "", ref_position: "", ref_relationship: "" },
            { ref_name: "", ref_contact_no: "", ref_address: "", ref_company: "", ref_position: "", ref_relationship: "" },
            { ref_name: "", ref_contact_no: "", ref_address: "", ref_company: "", ref_position: "", ref_relationship: "" },
        ],
        isSubmitting: false, 
        className: "",
        count: 0,
        uploadedFiles: [],
        validate:{
            firstname: "",
            middlename: "",
            lastname: "",
            suffix: "",
            birthdate: "",
        },
        workExperiences: [
            { company: '', position: '', from: '', to: '', status: '', reason: '' }
        ],
        educInfo:[
            { level: '', school: '', degree:'', honor: '', from: '', to: ''}
        ],
        currentStep:"#personal_information",
        steps: [
            {
                tab: "#personal_information",
                form: "personal_information_form",
                valid: false,
                data: {}
            },
            {
                tab: "#contact_information",
                form: "contact_information_form",
                valid: false,
                data: {
                    contactFormData: this.contactFormData,
                    references: this.references,
                }
            },
            {
                tab: "#work_experience",
                form: "work_experience_form",
                valid: false,
                data: { experiences: [this.workExperiences] , is_fresh_graduate: this.isFreshGraduate}
            },
            {
                tab: "#educational_information",
                form: "educational_information_form",
                valid: false,
                data: this.educInfo
            },
            {
                tab: "#application_information",
                form: "application_information_form",
                valid: false,
                data: {}
            },
            {
                tab: "#resume_upload",
                form: "resume_upload_form",
                valid: false,
                data: this.uploadedFiles
            }
        ]
    },
    mounted: function () {
        let vm = this;
        vm.checksOnloads();
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
    
        // $("#referral").select2({
        //     placeholder: 'SELECT AN OPTION',
        //     width: '100%',
        //     data: referral,
        //     allowClear: true,
        // }).val(null).trigger('change');
    
        // $("#school_id").select2({
        //     placeholder: 'SELECT AN OPTION',
        //     width: '100%',
        //     data: schools,
        //     multiple: true,
        // });
    
        // $("#course_id").select2({
        //     placeholder: 'SELECT AN OPTION',
        //     width: '100%',
        //     data: courses,
        //     multiple: true,
        // });
    
    
        $("#civil_status").select2({
            placeholder: 'SELECT AN OPTION',
            width: '100%',
            data: civilStatusOptions,
        }).val(null).trigger('change');
    
    
        $("#gender").select2({
            width: '100%',
            placeholder: 'SELECT GENDER',
            data: genderOptions,
        }).val(null).trigger('change');
    
        $('#birthdate').datepicker({
            endDate: new Date(),
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'bottom left',
            format: 'mm/dd/yyyy',
            forceParse: false
        })
        .on('changeDate', function (e) {
            vm.validate.birthdate = $(this).val();
        });

        $('#birthdate').datepicker('clearDates');
    
        $("#position_id").select2({
            placeholder: 'SELECT AN OPTION',
            width: '100%',
            data: position,    
        }).val(null).trigger('change');
    
        $("#recruitment").select2({
            width: '100%',
            placeholder: 'SELECT SOURCE',
            data: recruitmentSources,
        }).on('change', function (e) {
            const selectedValue = $(this).val();
            if (selectedValue == 'referral') {
                $('.referral').removeClass('d-none');
            } else {
                $('.referral').addClass('d-none');
            }
        }).on('select2:select', function (e) {
            $('#referral').val("");
            $('#referral-relationship').val("");
        });

        restoreApplicationData(this);
        this.$nextTick(() => {
            this.initEducSelect2();
        })
    },
    computed: {
        canGoBack() {
            return this.steps.findIndex(
                step => step.tab === this.currentStep
            ) > 0;
        },
        canGoNext() {
            return this.steps.findIndex(
                step => step.tab === this.currentStep
            ) < this.steps.length - 1;
        },
        canSubmit() {
            return this.currentStep == "#resume_upload" && this.uploadedFiles.length > 0;
        }
    },
    methods: {
        onFreshGraduateChange() {
            this.freshGraduate = !this.freshGraduate;
        },
        checksOnloads() {
            const consent = localStorage.getItem('gcc_data_consent');
            if (!consent) {
                $('#modalConsent').modal('show');
            }
        },
        acceptConsent() {
            localStorage.clear();
            localStorage.setItem('gcc_data_consent', '1');
            document.cookie ="gcc_data_consent=1;path=/;max-age=" + (60 * 60 * 24 * 30);
            $('#modalConsent').modal('hide');
        },
        declineConsent() {
            localStorage.clear();
            document.cookie.split(";").forEach(function(c) {
                document.cookie =
                    c.replace(/^ +/, "")
                     .replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
            });
            window.location.href = "https://www.facebook.com/gcandcgroup";
        },
        getCookie(name) {
            const cookies = document.cookie.split('; ');
            for (let cookie of cookies) {
                const [key, value] = cookie.split('=');
                if (key === name) return value;
            }
            return null;
        },
        alreadyRegistered() {
            const _this = this;
            return $.ajax({
                url: baseUrl + "crs/online_registration/validate_application",
                type: "POST",
                global: false,
                dataType: "json",
                data: {
                    ..._this.validate,
                    csrf_token: _csrf_hash
                }
            });
        },
        removeEducInfo: function(index) {
            $('.educ-level-select').each(function () {
                if ($(this).hasClass("select2-hidden-accessible")) {
                    $(this).off('change').select2('destroy');
                }
            });
            this.$nextTick(() => {
                this.educInfo.splice(index, 1);
                this.initEducSelect2();
                $('#educational_information_form').get(0).reset();
            });
        },
        addEducInfo: function() {
            this.educInfo.push(
                { level: '', school: '', degree:'', honor: '', from: '', to: ''}
            );
            this.initEducSelect2();
        },
        initEducSelect2() {
            const vm = this;
            this.$nextTick(() => {
                $('.educ-level-select').each(function () {
                    if ($(this).hasClass("select2-hidden-accessible")) {
                        return;
                    }
                    const i = $(this).data('index');
                    $(this).select2({
                        width: '100%',
                        placeholder: 'SELECT LEVEL',
                        allowClear: true,
                        data: levelOptions
                    }).on('change', function () {
                        const index = $(this).data('index');
                        vm.educInfo[index].level = $(this).val();
                    });
                    const savedValue = vm.educInfo[i]?.level;
                    if (savedValue) {
                        $(this).val(savedValue).trigger('change');
                    }
                });
            });
        },
        addWork: function() {
            this.workExperiences.push(
                { company: '', position: '', from: '', to: '', status: '', reason: '' }
            );
        },
        removeWork: function(index) {
            this.workExperiences.splice(index, 1);
            $('#work_experience_form').get(0).reset();
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
                "doc": "m-widget4 m-widget2__item m-widget2__item--primary col-12",
                "docx": "m-widget4 m-widget2__item m-widget2__item--primary col-12",
                "pdf": "m-widget4 m-widget2__item m-widget2__item--danger col-12",
                "jpg": "m-widget4 m-widget2__item m-widget2__item--success col-12",
                "jpeg": "m-widget4 m-widget2__item m-widget2__item--success col-12"
            };
        
            return classMap[extension] || "m-widget4 m-widget2__item m-widget2__item--default col-12";
        },
        fileDelete: function(index) {
            this.uploadedFiles.splice(index, 1);
            const fileInput = document.getElementById('fileupload');
            if (fileInput) {
                fileInput.value = '';
            }
            this.count = this.uploadedFiles.length;
            if(this.count == 0){
                this.steps.find(s => s.tab === "#resume_upload").valid = false;
            }
            this.steps.find(s => s.tab === "#resume_upload").data = this.uploadedFiles;
        },
        goBack() {
            let currentIndex = this.steps.findIndex(
                step => step.tab === this.currentStep
            );
    
            if (currentIndex > 0) {
                let prevStep = this.steps[currentIndex - 1];
                this.currentStep = prevStep.tab;
                $('a[href="' + prevStep.tab + '"]').tab('show');
            }
        },
        async goNext() {
            let currentIndex = this.steps.findIndex(
                step => step.tab === this.currentStep
            );

            if (currentIndex === 0) {
                try {
                    const validation = await this.alreadyRegistered();
                    if (validation) {
                        $("#modalExisting").modal('show');
                        return;
                    }
                } catch (error) {
                    return;
                }
            }
    
            let nextStep = this.steps[currentIndex + 1];
            if (nextStep && this.steps[currentIndex].valid) {
                this.currentStep = nextStep.tab;
                $('a[href="' + nextStep.tab + '"]').tab('show');
            } else if (!this.steps[currentIndex].valid) {
                $("#" + this.steps[currentIndex].form).submit();
            }
        },
        submitAll() {
            _this = this;
            let payload = {};
            this.steps.forEach(step => {
                if (!step.valid) return;
                if (Array.isArray(step.data)) {
                    payload[step.form] = [...step.data];
                }
                else {
                    Object.assign(payload, step.data);
                }
            });
            const formData = new FormData();
            formData.append("payload", JSON.stringify(payload));
            formData.append("csrf_token", _csrf_hash);
            const file = $("#fileupload")[0].files[0];
            formData.append("files", file);
            this.isSubmitting = true;
            $.ajax({
                url: baseUrl + "crs/online_registration/submit_application",
                type: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                data: formData,
                success: function (res) {
                    if(res.success){
                        toastr.success(res.message, "Success", 10000);
                        localStorage.clear();
                        document.cookie = "gcc_already_submitted=true;path=/;max-age=259200";
                        window.location.href = baseUrl + "crs/online_registration/thank_you";
                    }
                },
                error: function (err) {
                    toastr.error("Something went wrong!", "Error", 10000);
                }
            });
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
        raw: file
    };
    application_vue.uploadedFiles = [fileObj];
    application_vue.steps.find(s => s.tab === "#resume_upload").data = application_vue.uploadedFiles;
    application_vue.steps.find(s => s.tab === "#resume_upload").valid = true;
}

application_vue.steps.forEach(function(step, index) {
    $.validate({
        form: "#" + step.form,
        scrollToTopOnError: false,
        onSuccess: function () {
            application_vue.steps[index].valid = true;
            if (step.tab === "#work_experience") {
                application_vue.steps[index].data.experiences = application_vue.workExperiences;
                application_vue.steps[index].data.is_fresh_graduate = application_vue.isFreshGraduate;
            }
            else if (step.tab === "#educational_information") {
                application_vue.steps[index].data = application_vue.educInfo;
            }
            else if(step.tab === "#contact_information") {
                application_vue.steps[index].data.contactFormData = application_vue.contactFormData;
                application_vue.steps[index].data.references = application_vue.references;
            } 
            else {
                const formData = {};
                $("#" + step.form).serializeArray().forEach(function(field) {
                    if (field.name === "schools" || field.name === "courses" || field.name === "positions") {
                        if (!formData[field.name]) {
                            formData[field.name] = [];
                        }
                        formData[field.name].push(field.value);
                        return;
                    }
                    formData[field.name] = field.value;
                });
                application_vue.steps[index].data = formData;
            }
            localStorage.setItem("gcc_job_application",JSON.stringify(application_vue.steps));
            application_vue.$nextTick(function () {
                let nextStep = application_vue.steps[index + 1];
                if (nextStep) {
                    $('a[href="' + nextStep.tab + '"]').tab('show');
                }
            });
            return false;
        },
        onError: function () {
            application_vue.steps[index].valid = false;
            return false;
        }
    });
});

$('.nav-tabs a').on('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    return false;
});

$('.nav-tabs a').on('shown.bs.tab', function (e) {
    let tabId = $(this).attr('href');
    application_vue.currentStep = tabId;
    let step = application_vue.steps.find(s => s.tab === tabId);
    if (step) {
        step.valid = false;
    }
});

function restoreApplicationData(vue_app) {
    let savedSteps = localStorage.getItem("gcc_job_application");
    if (!savedSteps) return;
    savedSteps = JSON.parse(savedSteps);
    vue_app.steps = savedSteps;
    vue_app.steps.forEach(function(step){
        if (step.valid === true) {
            if(step.tab === "#personal_information") {
                vue_app.validate = step.data
            }
            if (step.tab === "#work_experience") {
                vue_app.isFreshGraduate = step.data.is_fresh_graduate;
                vue_app.workExperiences = step.data.experiences;
            }
            if (step.tab === "#educational_information") {
                vue_app.educInfo = step.data;
            }
            if (step.tab === "#contact_information") {
                vue_app.contactFormData = step.data.contactFormData;
                vue_app.references = step.data.references;
            }
            Object.keys(step.data).forEach(function(name) {
                if(name === "schools" || name === "courses" || name === "positions" || name === "gender" || name === "civil_status" || name === "recruitment") {
                    $('[name="' + name + '"]').val(step.data[name]).trigger("change");
                }
                else {
                    $('[name="' + name + '"]').val(step.data[name]);
                }
            });
        }
    });
}

