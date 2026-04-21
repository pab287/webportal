let position, schools, courses, candidate_information, employee;
let manpower_request = null;
let mrf_table = null;
let archive = 0;
let oldAssessment = null;
let currentData = null;

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.position !== "undefined" && _tempContentData.position.length > 0){ 
        position = _tempContentData.position;
    }
    if(typeof _tempContentData.candidate_information !== "undefined" && _tempContentData.candidate_information !== null && Object.keys(_tempContentData.candidate_information).length > 0){ 
        candidate_information = _tempContentData.candidate_information;
    }
    if(typeof _tempContentData.employee !== "undefined" && _tempContentData.employee.length > 0){ 
        employee = _tempContentData.employee;
    }
}

let positions = candidate_information.candidate.positions;
let candidate_id = candidate_information.candidate.id;

const mimeMap = {
    "application/pdf": "pdf",
    // "application/msword": "doc",
    // "application/vnd.openxmlformats-officedocument.wordprocessingml.document": "docx",
    // "image/jpeg": "jpg"
};

const interviewLocationOptions = [
    { id: 'office', text: 'Office Interview' },
    { id: 'zoom', text: 'Zoom Meeting' },
    { id: 'teams', text: 'Microsoft Teams' },
    { id: 'phone', text: 'Phone Interview' },
    { id: 'other', text: 'Other' }
];

const interviewTypeOptions = [
    {id: 'initial', text: 'Initial Interview'},
    {id: 'skill_test', text: 'Skill Test'},
    {id: 'final', text: 'Final Interview'}
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

const levelOptions = [
    { id: "Primary", text: "Primary" },
    { id: "Secondary", text: "Secondary" },
    { id: "Senior_High_School", text: "Senior High School" },
    { id: "Vocational", text: "Vocational / Technical" },
    { id: "College", text: "College" },
    { id: "Post_Graduate", text: "Post Graduate" }
];

const genderOptions = [    
    { id: 'male', text: 'MALE' },
    { id: 'female', text: 'FEMALE' },
];

const assesmentData = [
    { id: 'pass', text: 'Pass' },
    { id: 'fail', text: 'Fail' }
];

const maxFileSize = 50 * 1024 * 1024; // 50MB
const allowedTypes = [
    'application/pdf',
    'image/jpeg',
    'image/png'
];


let application_vue = new Vue({
    el: "#m_content",
    data: {
        attachUpdate: false,
        to_remove_work_exp: [],
        to_remove_educ_info: [],
        to_remove_assessment_attachments: [],
        originalApplication: null,
        selected_form: '#personal_information_form',
        interviews: {},
        isEditable:false,
        can_edit_candidate: false,
        selectedApplication: candidate_information.candidate,
        isFreshGraduate: false,
        references: candidate_information.references,
        isSubmitting: false, 
        className: "",
        count: 0,
        uploadedFile: candidate_information.candidate.resume,
        workExperiences: candidate_information && candidate_information.work_exp && candidate_information.work_exp.length > 0 ? candidate_information.work_exp : [{ work_company: '', work_position: '', work_from: '', work_to: '', work_status: '', work_reason: '' }],
        educInfo: candidate_information.education,
        manpower_request: candidate_information.manpower_request,
        assigned_manpower_request: candidate_information.assigned_manpower_req,
        selectedInterview: null,
        assessment: {
            interview_id: '',
            status: '',
            remarks: '',
            attachments:[],
        },
        edit_attachments: [],
    },
    mounted: function () {
        let vm = this;

        $('#applied_dt').datepicker({
            endDate: new Date(),
            todayHighlight: true,
            autoclose: true,
            todayBtn: 'linked',
            format: 'mm/dd/yyyy',
            forceParse: false
        });
        $('#applied_dt').datepicker('setDate',moment(vm.selectedApplication.applied_dt).format('MM/DD/YYYY'));
    
        $("#employee").select2({
            placeholder: 'SELECT AN OPTION',
            width: '100%',
            data: employee,
            allowClear: true,
        }).val(null).trigger('change');
    
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
        }).val(vm.selectedApplication.civil_status).trigger('change');
    
    
        $("#gender").select2({
            width: '100%',
            placeholder: 'SELECT GENDER',
            data: genderOptions,
        }).val(vm.selectedApplication.gender).trigger('change');
    
        $('#birthdate').datepicker({
            endDate: new Date(),
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'bottom left',
            format: 'mm/dd/yyyy',
            forceParse: false
        }).on('changeDate', function (e) {
        });

        $('#birthdate').datepicker('clearDates');
        $("#position_id").select2({
            placeholder: 'SELECT AN OPTION',
            width: '100%',
            data: position,    
        }).val(vm.selectedApplication.positions).trigger('change');
    
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
        }).val(vm.selectedApplication.recruitment).trigger('change');

        $('#referral').val(vm.selectedApplication.referral);
        $('#referral-relationship').val(vm.selectedApplication.referral_relationship);

        this.$nextTick(() => {
            this.loadEducSelect2();
        });
        $('#birthdate').datepicker('setDate', vm.selectedApplication.birthdate);

        if(candidate_information.work_exp <= 0){
            vm.isFreshGraduate = true;
        }else{
            vm.isFreshGraduate = false;
        }

        this.getInterviews();
        this.initValidations();
        $('#assessInterviewModal').on('shown.bs.modal', function () {
            if (!$('#assessmentResult').hasClass('select2-hidden-accessible')) {
                $('#assessmentResult').select2({
                    width: '100%',
                    placeholder: 'Select Result',
                    data: assesmentData,
                    dropdownParent: $('#assessInterviewModal')
                }).on('change', function () {
                    vm.assessment.status = $(this).val();
                });
            }
        
            $('#assessmentResult').val(vm.assessment.status).trigger('change').prop('disabled', true);
            $('#assessmentResult').trigger('change.select2');
        });

        $('#assessInterviewModal').on('hidden.bs.modal', function () {
            $('#assessmentResult').prop('disabled', false).trigger('change.select2');
        });

        $('#viewInterviewModal').on('shown.bs.modal', function () {
            if (!$('#edit_assessmentResult').hasClass('select2-hidden-accessible')) {
                $('#edit_assessmentResult').select2({
                    width: '100%',
                    placeholder: 'Select Result',
                    data: assesmentData,
                    dropdownParent: $('#viewInterviewModal')
                }).on('change', function () {
                    vm.assessment.status = $(this).val();
                });
            }
        
            $('#edit_assessmentResult').val(vm.selectedInterview.status).trigger('change').prop('disabled', true);
            $('#edit_assessmentResult').trigger('change.select2');
        });

        $('#viewInterviewModal').on('hidden.bs.modal', function () {
            vm.isEditable = false;
        });
    },
    computed: {
        fullName() {
            const firstName = this.selectedApplication?.firstname || '';
            const middleName = this.selectedApplication?.middlename || '';
            const lastName = this.selectedApplication?.lastname || '';
            return `${firstName} ${middleName ? middleName + ' ' : ''}${lastName}`.trim();
        },
    },
    methods: {
        initValidations: function(){
            vm = this;
            $.validate({
                form: '#personal_information_form, #contact_information_form, #work_experience_form, #educational_information_form, #application_information_form',
                scrollToTopOnError: false,
                onSuccess: function (form) {
                    const formData = $(form).serializeArray();
                    const normalizedFormData = normalizeFormData(formData);
                    const update = getAllUpdates(normalizedFormData, currentData);
                    const to_remove_work_exp = vm.to_remove_work_exp || [];
                    const to_remove_educ_info = vm.to_remove_educ_info || [];
                    const payload = {
                        csrf_token: _csrf_hash,
                        id: candidate_id,
                        update: update
                    };
                    if (to_remove_work_exp.length > 0) {
                        payload.to_remove_work_exp = to_remove_work_exp;
                    }
                    if (to_remove_educ_info.length >0 ){
                        payload.to_remove_educ_info = to_remove_educ_info;
                    }
                    $.ajax({
                        url: baseUrl("crs/update_candidate_information"),
                        type: 'POST',
                        global: false,
                        data: payload,
                        success: function (response) {
                            if (response.success) {
                                toastr.success(response.toastr_msg, 'Success', 3000);
                                if (vm.selected_form === '#personal_information_form' || vm.selected_form === '#contact_information_form') {
                                    currentData = applyObjectUpdates(currentData, update);
                                }
                                else if(vm.selected_form === '#work_experience_form'){
                                    if (update['is_fresh_graduate'] == 'on') {
                                        currentData.is_fresh_graduate = true;
                                        currentData.work_experiences = [{
                                                work_company: '',
                                                work_position: '',
                                                work_from: '',
                                                work_to: '',
                                                work_status: '',
                                                work_reason: ''
                                            }
                                        ];
                                    } else {
                                        currentData.is_fresh_graduate = false;
                                        const current_work_exp = currentData.work_experiences || [];
                                        const update_work_exp = update['work_experiences'] || [];
                                        currentData.work_experiences = applyWorkExperienceUpdates(current_work_exp, update_work_exp);
                                        if (to_remove_work_exp.length > 0) {
                                            currentData.work_experiences = (currentData.work_experiences || []).filter(function (item) {
                                                return !to_remove_work_exp.includes(item.id);
                                            });
                                        }
                                    }
                                }
                                else if(vm.selected_form === '#educational_information_form'){
                                    const current_schools = currentData.schools || [];
                                    const update_schools = update['schools'] || [];
                                    currentData.schools = applyArrayUpdates(current_schools, update_schools);
                                    if (to_remove_educ_info.length > 0) {
                                        currentData.schools = (currentData.schools || []).filter(function (item) {
                                            return !to_remove_educ_info.includes(item.id);
                                        });
                                    }
                                }
                                else if(vm.selected_form === '#application_information_form'){
                                    Object.keys(update).forEach(function (key) {
                                        if (key === 'positions' && Array.isArray(update[key])) {
                                            currentData[key] = [...update[key]];
                                        } else {
                                            currentData[key] = update[key];
                                        }
                                    });
                                }
                            } else {
                                toastr.error(response.toastr_msg, 'Error', 3000);
                            }
                            vm.cancelEdit();
                        },
                        error: function (xhr, status, error) {

                        }
                    });
                    return false;
                }
            });

            $.validate({
                form: '#resume_upload_form',
                scrollToTopOnError: false,
                onSuccess: function () {
                    const formData = new FormData(document.getElementById('resume_upload_form'));
                    if(!application_vue.attachUpdate){
                        toastr.error('No changes detected', 'Error', 3000);
                        return false;
                    }
                    formData.append('csrf_token', _csrf_hash);
                    formData.append('id', candidate_id);
                    $.ajax({
                        url: baseUrl("crs/update_candidate_attachment"),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.success) {
                                toastr.success(response.toastr_msg, 'Success', 3000);
                            }else{
                                toastr.error(response.toastr_msg, 'Error', 3000);
                            }
                        }
                    });
                    return false;
                }
            });
        },
        resumefileDelete: function() {
            this.uploadedFile = '';
            $('#resume-fileupload').val('');
        },
        setSelectedForm: function(formId){
            this.selected_form = formId;
        },
        startEdit: function () {
            vm = this;
            if (vm.selected_form === '#personal_information_form') {
                currentData = JSON.parse(JSON.stringify(vm.selectedApplication));
            } else if (vm.selected_form === '#contact_information_form') {
                currentData = JSON.parse(JSON.stringify({...vm.selectedApplication,references: vm.references}));
            } else if (vm.selected_form === '#work_experience_form') {
                currentData = {
                    work_experiences: vm.isFreshGraduate ? [{
                            work_company: '',
                            work_position: '',
                            work_from: '',
                            work_to: '',
                            work_status: '',
                            work_reason: ''
                        }] : JSON.parse(JSON.stringify(vm.workExperiences)),
                    is_fresh_graduate: JSON.parse(JSON.stringify(vm.isFreshGraduate)) ? 'on' : ''
                };
            }
            else if (vm.selected_form === '#educational_information_form') {
                currentData = {
                    schools: JSON.parse(JSON.stringify(vm.educInfo))
                };
            }
            else if(vm.selected_form === '#application_information_form'){
                const selected_data = JSON.parse(JSON.stringify(vm.selectedApplication));
                currentData = {
                    positions: selected_data.positions ? selected_data.positions : [],
                    recruitment: selected_data.recruitment,
                    referral: selected_data.referral,
                    referral_relationship: selected_data.referral_relationship,
                    applied_dt: selected_data.applied_dt
                };
            }
            else if (vm.selected_form === "#resume_upload_form"){
                currentData = JSON.parse(JSON.stringify(vm.uploadedFile));
            }
            this.can_edit_candidate = true;
        },
        cancelEdit: function() {
            this.can_edit_candidate = false
            if (this.selected_form === '#personal_information_form') {
                this.selectedApplication = JSON.parse(JSON.stringify(currentData));
                this.$nextTick(function () {
                    $('#personal_information_form').find('select.select2, select[data-control="select2"], select')
                        .each(function () {
                            const name = $(this).attr('name');
                            const value = currentData[name] ?? '';
                            $(this).val(value).trigger('change.select2');
                        });
                });
                $('#birthdate').datepicker('setDate', this.selectedApplication.birthdate);
            }
            else if(this.selected_form === '#contact_information_form'){
                this.selectedApplication = JSON.parse(JSON.stringify(currentData));
                this.references = JSON.parse(JSON.stringify(currentData.references));
            }
            else if(this.selected_form === '#work_experience_form'){
                this.workExperiences =  JSON.parse(JSON.stringify(currentData.work_experiences));
                this.isFreshGraduate = JSON.parse(JSON.stringify(currentData.is_fresh_graduate));
                this.to_remove_work_exp = [];
            }else if (this.selected_form === '#educational_information_form') {
                this.educInfo = JSON.parse(JSON.stringify(currentData.schools));
                this.$nextTick(function () {
                    $('#educational_information_form')
                        .find('.educ-level-select')
                        .each(function () {
                            const index = $(this).data('index');
                            const value = currentData.schools?.[index]?.educ_level_type ?? '';
                            $(this).val(value).trigger('change.select2');
                        });
                });
                this.to_remove_educ_info = [];
            }else if (this.selected_form === '#application_information_form'){
                this.selectedApplication = JSON.parse(JSON.stringify(currentData));
                $('#position_id').val(this.selectedApplication.positions).trigger('change');
                $('#recruitment').val(this.selectedApplication.recruitment).trigger('change');
                $('#referral').val(this.selectedApplication.referral);
                $('#referral-relationship').val(vm.selectedApplication.referral_relationship);
                $('#applied_dt').datepicker('setDate', moment(this.selectedApplication.applied_dt).format('MM/DD/YYYY'));
            }else if (this.selected_form === "#resume_upload_form"){
                this.uploadedFile = JSON.parse(JSON.stringify(currentData));
            }
        },
        updateApplication: function() {
            const vm = this;
            $(vm.selected_form).submit();
        },
        hasAssessmentChanges: function(){
            const current = {
                status: this.assessment.status,
                assessment_remarks: this.selectedInterview.assessment.assessment_remarks,
                attachments: this.selectedInterview.assessment.assessment_attachments
            }
        },
        deleteInterview: function(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: baseUrl("crs/delete_candidate_interview"),
                        type: 'POST',
                        data: { id: id, csrf_token: _csrf_hash },
                        dataType: 'json',
                        success: (res) => {

                            if (res.success) {
                                this.getInterviews();
                                toastr.success(res.toastr_msg, 'Success', 3000);
                            }
                        },

                    });
                }
            });
        },
        passInterview: function(item){
            this.selectedInterview = item;
            this.assessment.interview_id = item.id;
            this.assessment.status = 'pass';
            this.assessment.remarks = '';
            $('#assessInterviewModal').modal('show');

        },
        failInterview: function(item){
            this.selectedInterview = item;
            this.assessment.interview_id = item.id;
            this.assessment.status = 'fail';
            this.assessment.remarks = '';
            $('#assessInterviewModal').modal('show');
        },
        viewInterview: function(item){
            this.selectedInterview = JSON.parse(JSON.stringify(item));
            oldAssessment = JSON.parse(JSON.stringify(item.assessment));
        
            oldAssessment.attachments = item.assessment.assessment_attachments
                ? item.assessment.assessment_attachments.split(',').map(file => {
                    const trimmedFile = file.trim();
                    const parts = trimmedFile.split('.');
                    const ext = parts.length > 1 ? parts.pop().toLowerCase() : '';
        
                    return {
                        uploaded: true,
                        name: trimmedFile,
                        type: ext
                    };
                })
                : [];
        
            delete oldAssessment.assessment_attachments;
        
            this.edit_attachments = JSON.parse(JSON.stringify(oldAssessment.attachments));
            $('#viewInterviewModal').modal('show');
        },
        editInterview: function(){
            this.isEditable = true;
        },
        cancelEditInterview: function() {
            this.isEditable = false;
            $('#edit_assessmentResult').val(oldAssessment.status).trigger('change');
            this.selectedInterview.assessment.assessment_remarks = JSON.parse(JSON.stringify(oldAssessment.assessment_remarks));
            this.edit_attachments = JSON.parse(JSON.stringify(oldAssessment.attachments));
            this.to_remove_assessment_attachments = [];
        },
        formatDateTime(datetime) {
            if (!datetime || datetime === "0000-00-00 00:00:00") return "-";
            return moment(datetime).format("MMM DD, YYYY hh:mm A");
        },
        formatPlatform(platformId) {
            const platform = interviewLocationOptions.find(item => item.id === platformId);
            return platform ? platform.text : "-";
        },
        getInterviews: function() {
            let vm = this;
            $.ajax({
                global: false,
                url: baseUrl("crs/get_candidate_interview"),
                type: "POST",
                dataType: "json",
                data: {
                    csrf_token: _csrf_hash,
                    candidate_id: candidate_id
                },
                success: function (res) {
                    if(res){
                        vm.interviews = res;
                    }
                },
                error: function (xhr) {
                }
            })
        },
        getClass: function(filename) {
            if (!filename) {
                return "m-widget4 m-widget2__item m-widget2__item--default col-12";
            }
            let extension = '';
            if (filename.includes('.')) {
                extension = filename.split('.').pop().toLowerCase();
            }
            const classMap = {
                doc: "m-widget4 m-widget2__item m-widget2__item--primary col-12",
                docx: "m-widget4 m-widget2__item m-widget2__item--primary col-12",
                pdf: "m-widget4 m-widget2__item m-widget2__item--danger col-12",
                jpg: "m-widget4 m-widget2__item m-widget2__item--success col-12",
                jpeg: "m-widget4 m-widget2__item m-widget2__item--success col-12",
                png: "m-widget4 m-widget2__item m-widget2__item--success col-12"
            };
        
            return classMap[extension] || "m-widget4 m-widget2__item m-widget2__item--default col-12";
        },
        getExtension: function(filename) {
            if (!filename) {
                return baseUrl("assets/images/file_icons/default.svg");
            }
        
            let extension = '';
        
            if (filename.includes('.')) {
                extension = filename.split('.').pop().toLowerCase();
            }
        
            const iconMap = {
                doc: "doc.svg",
                docx: "doc.svg",
                pdf: "pdf.svg",
                jpg: "jpg.svg",
                jpeg: "jpg.svg",
                png: "jpg.svg"
            };
        
            const fileName = iconMap[extension] || "default.svg";
            return baseUrl(`assets/images/file_icons/${fileName}`);
        },
        formatDate(date) {
            return moment(date).format('MMMM D, YYYY');
        },
        onFreshGraduateChange() {
            this.freshGraduate = !this.freshGraduate;
        },
        addWork: function() {
            this.workExperiences.push(
                { work_company: '', work_position: '', work_from: '', work_to: '', work_status: '', work_reason: '' }
            );
        },
        removeWork: function(index) {
            const removedWork = this.workExperiences[index];
            if (removedWork && removedWork.id) {
                if (!this.to_remove_work_exp) {
                    this.to_remove_work_exp = [];
                }
                if (!this.to_remove_work_exp.includes(removedWork.id)) {
                    this.to_remove_work_exp.push(removedWork.id);
                }
            }
            this.workExperiences.splice(index, 1);
        },
        removeEducInfo: function(index) {
            const removedEduc = this.educInfo[index];
            if (removedEduc && removedEduc.id) {
                if (!this.to_remove_educ_info) {
                    this.to_remove_educ_info = [];
                }
                if (!this.to_remove_educ_info.includes(removedEduc.id)) {
                    this.to_remove_educ_info.push(removedEduc.id);
                }
            }
            this.educInfo.splice(index, 1);
            this.$nextTick(() => {
                this.loadEducSelect2();
            });
        },
        addEducInfo: function() {
            this.educInfo.push(
                {educ_level_type: '', educ_school: '', educ_degree:'', educ_honors: '', educ_from: '', educ_to: ''}
            );
            this.$nextTick(() => {
                this.loadEducSelect2();
            });
        },
        loadEducSelect2() {
            const vm = this;
            this.$nextTick(() => {
                $('.educ-level-select').each(function () {
                    const $select = $(this);
                    const index = Number($select.data('index'));
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }
                    $select.select2({
                        width: '100%',
                        placeholder: 'SELECT LEVEL',
                        allowClear: true,
                        data: levelOptions
                    });
                    const value = vm.educInfo[index]?.educ_level_type || '';
                    $select.val(value).trigger('change.select2');
                    $select.off('change.educ').on('change.educ', function () {
                        vm.educInfo[index].educ_level_type = $(this).val();
                    });
                });
            });
        },
        assessmentFileDelete: function(index) {
            this.assessment.attachments.splice(index, 1);
        },
        interviewfileDelete: function(index) {
            const file = this.edit_attachments[index];

            if (file && file.uploaded === true) {
                this.to_remove_assessment_attachments.push(file.name);
            }
        
            this.edit_attachments.splice(index, 1);
            // Swal.fire({
            //     title: 'Are you sure?',
            //     text: 'This action cannot be undone.',
            //     icon: 'warning',
            //     showCancelButton: true,
            //     confirmButtonText: 'Yes, remove it',
            //     cancelButtonText: 'Cancel',
            //     confirmButtonColor: '#d33',
            //     cancelButtonColor: '#6c757d'
            // }).then((result) => {
            //     if (result.isConfirmed) {
                
            //         let edit_attachments = this.edit_attachments.map(file => file.name).join(',');
            //         $.ajax({
            //             url: baseUrl("crs/update_interview_attachments"),
            //             type: 'POST',
            //             data: { id: this.selectedInterview.id, to_remove: edit_attachments, csrf_token: _csrf_hash },
            //             dataType: 'json',
            //             success: (res) => {
            //                 if (res.success) {
            //                     toastr.success(res.toastr_msg, 'Success', 3000);
            //                     this.getInterviews();
            //                 }
            //             },
            //         })
            //     }
            // });
        },
    }
});

// $('a[href="#main_candidate_information"]').on('shown.bs.tab', function () {
//     $('a[href="#candidate_personal_information"]').tab('show');
// });

$('#manpowerRequestModal').on('shown.bs.modal', function () {
    if ($.fn.DataTable.isDataTable('#table-manpower_request')) {
        mrf_table.ajax.reload(null, false);
    } else {
        mrf_table = $('#table-manpower_request').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    global: false,
                    url: baseUrl("crs/get_available_manpower_request"),
                    type: "POST",
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
                        d.positions = positions;
                        d.archive = archive;
                    }
                },
                columns: [
                    { data: "id", visible: false },
                    { data: "mrf_reference_no", orderable: false },
                    { data: null, orderable: false,
                        render : function ( data, type, row ) {
                            return `${row.position} <br> ${row.type}`;
                        }
                    },
                    { data: null, orderable: false,
                        render : function ( data, type, row ) {
                            return `<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#manpowerRequestModal" onclick="assignManpowerRequest(${row.id})">Assign</button>`;
                        }
                    }
                ],
            }
        );
    }
});

function assignManpowerRequest(id){
    $.ajax({
        url: baseUrl("crs/assign_manpower_request"),
        type: "POST",
        data: {id: id, csrf_token: _csrf_hash},
        success: function (data) {
            // $('#manpowerRequestModal').modal('hide');
        }
    });
}

$('#interviewModal').on('shown.bs.modal', function () {
    $('#interviewDate').daterangepicker({
        showDropdowns: true,
        drops: 'auto',
        opens: 'right',
        singleDatePicker: true,
        timePicker: true,
        timePicker24Hour: true,
        timePickerIncrement: 15,
        autoUpdateInput: false, 
        minDate: moment(),
        parentEl: "#interviewModal .modal-content",
        locale: {
            format: 'MM/DD/YYYY HH:mm'
        }
    }).on('apply.daterangepicker', function(ev, picker) {
        const formatted = picker.startDate.format('MM/DD/YYYY HH:mm');
        $(this).val(formatted);
    }).on('cancel.daterangepicker', function(ev, picker) {
        $(this).val(''); // Clear the input if user cancels
    });

    $('#interviewLocation').select2({
        width: '100%',
        dropdownParent: $('#interviewModal'),
        placeholder: 'Select an option',
        data:interviewLocationOptions,
    });

    $('#interviewType').select2({
        width: '100%',
        dropdownParent: $('#interviewModal'),
        placeholder: 'Select an option',
        data:interviewTypeOptions,
    });

});

$(document).ready(function () {
    $('#mainTabNav').on('click', '.main-tab-link', function (e) {
        e.preventDefault();
        e.stopPropagation();
        let target = $(this).attr('href');
        $('#mainTabNav .main-tab-link').removeClass('active');
        $(this).addClass('active');
        $('#content_candidate_info > .tab-pane').removeClass('active show').hide();
        $(target).addClass('active show').show();
    });
});


$.validate({
    form: "#interviewForm",
    scrollToTopOnError: false,
    onSuccess: function () {
        let form = $("#interviewForm")[0];
        let formData = new FormData(form);
        formData.append("csrf_token", _csrf_hash);
        formData.append("candidate_id", candidate_id);
        $.ajax({
            url: baseUrl("crs/submit_interview_schedule"),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if(res.success){
                    toastr.success(res.toastr_msg, 'Success', 3000);
                    application_vue.getInterviews();
                    $('#interviewModal').modal('hide');
                    $('#interviewForm')[0].reset();
                }
                else{
                    toastr.error(res.toastr_msg, 'Error', 3000);
                }
            },
            error: function (xhr) {
            }
        });

        return false;
    }
});



$(document).ready(function () {

    $(document).on('change', '#assessment_fileupload', function (e) {
        $('#assessAttachment').find('span.help-block.form-error').remove();
        handleFiles(this.files);
        this.value = '';
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
            toastr.error(`FILE "${file.name}" IS TOO LARGE. MAXIMUM SIZE IS 50MB.`);
            return false;
        }
        
        if (!allowedTypes.includes(file.type)) {
            toastr.error(`FILE "${file.name}" HAS AN UNSUPPORTED FORMAT. ONLY PDF FILES ARE ALLOWED.`, 'DANGER');
            return false;
        }
        
        const exists = application_vue.assessment.attachments.some(f => f.name === file.name);
        if (exists) {
            toastr.error(`FILE "${file.name}" IS ALREADY SELECTED.`);
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
        application_vue.assessment.attachments.push(fileObj);
    }

    $.validate({
        form: "#interview_assessment",
        scrollToTopOnError: false,
        onSuccess: function () {
            let formData = new FormData($('#interview_assessment')[0]);
            let assessment_id = application_vue.assessment.interview_id;
            formData.append('csrf_token', _csrf_hash);
            formData.append('assessment_id', assessment_id);
            formData.append('candidate_id', candidate_id);
            formData.append('status', application_vue.assessment.status);
    
            (application_vue.assessment.attachments || []).forEach(file => {
                if (file.raw) {
                    formData.append('files[]', file.raw);
                }
            });
    
            $.ajax({
                global: false,
                url: baseUrl("crs/assess_candidate_interview"),
                type: "POST",
                dataType: "json",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.toastr_msg, 'Success', 3000);
                        application_vue.getInterviews();
                        $('#assessInterviewModal').modal('hide');
                        $('#interview_assessment')[0].reset();
                    } else {
                        toastr.error(res.toastr_msg, 'Error', 3000);
                    }
                }
            });
    
            return false;
        }
    });

    $.validate({
        form: "#view_interview_assessment",
        scrollToTopOnError: false,
        onSuccess: function () {
            let assessment_id = application_vue.selectedInterview.id;
            const formData = {
                assessment_remarks: $('#edit_assessmentRemarks').val(),
                result: $('#edit_assessmentResult').val(),
            };
            const updates = getAssessmentUpdates(oldAssessment, formData);
            const newAttachments = application_vue.edit_attachments.filter(
                item => item.uploaded !== undefined && item.uploaded === false
            );
            console.log(newAttachments);
            const payload = new FormData();
            payload.append('csrf_token', _csrf_hash);
            payload.append('assessment_id', assessment_id);
            payload.append('candidate_id', candidate_id);
    
            Object.keys(updates).forEach(key => {
                payload.append(key, updates[key]);
            });
    
            newAttachments.forEach((item, index) => {
                if (item.raw) {
                    payload.append('files[]', item.raw);
                }
            });

            if (application_vue.to_remove_assessment_attachments && application_vue.to_remove_assessment_attachments.length > 0) {
                payload.append('to_remove', JSON.stringify(application_vue.to_remove_assessment_attachments));
            }

            $.ajax({
                global: false,
                url: baseUrl("crs/update_interview_assessment"),
                type: "POST",
                dataType: "json",
                data: payload,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.toastr_msg, 'Success', 3000);
                        application_vue.getInterviews();
                    } else {
                        toastr.error(res.toastr_msg, 'Error', 3000);
                    }
                    $('#viewInterviewModal').modal('hide');
                }
            });
    
            return false;
        }
    });

    $(document).on('change', '#resume-fileupload', function () {
        application_vue.uploadedFile = this.files[0].name;
        application_vue.attachUpdate = true;
    });

    $(document).on('change', '#edit_assessment_fileupload', function () {
        if (!this.files || !this.files.length) return;
        const maxSize = 50 * 1024 * 1024; // 50 MB
        application_vue.edit_attachments = application_vue.edit_attachments || [];
        Array.from(this.files).forEach(file => {
            const fileName = file.name.toLowerCase();
            const isPdf = file.type === 'application/pdf' || fileName.endsWith('.pdf');
            const isImage = file.type.startsWith('image/') ||
                fileName.endsWith('.jpg') ||
                fileName.endsWith('.jpeg') ||
                fileName.endsWith('.png') ||
                fileName.endsWith('.bmp') ||
                fileName.endsWith('.webp');
    
            if (!isPdf && !isImage) {
                toastr.error(`FILE "${file.name}" MUST BE A PDF OR IMAGE FILE.`);
                return;
            }
    
            if (file.size > maxSize) {
                toastr.error(`FILE "${file.name}" EXCEEDS THE 50 MB MAXIMUM SIZE.`);
                return;
            }
    
            const alreadyExists = application_vue.edit_attachments.some(item =>
                item.name === file.name &&
                item.type === file.type &&
                (
                    (item.raw && item.raw.size === file.size && item.raw.lastModified === file.lastModified) ||
                    (!item.raw)
                )
            );
    
            if (!alreadyExists) {
                application_vue.edit_attachments.push({
                    uploaded: false,
                    name: file.name,
                    type: file.type,
                    raw: file,
                });
            } else {
                toastr.error(`File "${file.name}" IS ALREADY SELECTED.`);
            }
        });
    
        $(this).val('');
    });

});

function getAssessmentUpdates(currentAssessment, updatedAssessment) {
    const updates = {};

    Object.keys(updatedAssessment).forEach((key) => {
        const currentKey = key === 'result' ? 'status' : key;
        if (currentAssessment[currentKey] !== updatedAssessment[key]) {
            updates[key] = updatedAssessment[key];
        }
    });
    return updates;
}

function getAllUpdates(normalizedFormData, currentData) {
    const updates = {};

    Object.keys(normalizedFormData).forEach(function (key) {
        if (key === 'references' || key === 'work_experiences' || key === 'schools') {
            return;
        }

        const oldValue = currentData[key] ?? '';
        const newValue = normalizedFormData[key] ?? '';

        if (String(oldValue) !== String(newValue)) {
            updates[key] = newValue;
        }
    });

    const newReferences = normalizedFormData.references || [];
    const oldReferences = currentData.references || [];
    const changedReferences = [];

    newReferences.forEach(function (reference, index) {
        const referenceUpdates = {};

        Object.keys(reference).forEach(function (field) {
            const oldValue = oldReferences[index]?.[field] ?? '';
            const newValue = reference[field] ?? '';

            if (String(oldValue) !== String(newValue)) {
                referenceUpdates[field] = newValue;
            }
        });

        if (Object.keys(referenceUpdates).length > 0) {
            referenceUpdates.id = reference.id ?? '';
            changedReferences.push(referenceUpdates);
        }
    });

    if (changedReferences.length > 0) {
        updates.references = changedReferences;
    }

    const newWorkExperiences = normalizedFormData.work_experiences || [];
    const oldWorkExperiences = currentData.work_experiences || [];
    const changedWorkExperiences = [];

    newWorkExperiences.forEach(function (work, index) {
        const workUpdates = {};

        Object.keys(work).forEach(function (field) {
            const oldValue = oldWorkExperiences[index]?.[field] ?? '';
            const newValue = work[field] ?? '';

            if (String(oldValue) !== String(newValue)) {
                workUpdates[field] = newValue;
            }
        });

        if (Object.keys(workUpdates).length > 0) {
            workUpdates.id = work.id ?? '';
            changedWorkExperiences.push(workUpdates);
        }
    });

    if (changedWorkExperiences.length > 0) {
        updates.work_experiences = changedWorkExperiences;
    }

    const newSchools = normalizedFormData.schools || [];
    const oldSchools = currentData.schools || [];
    const changedSchools = [];

    newSchools.forEach(function (school, index) {
        const schoolUpdates = {};

        Object.keys(school).forEach(function (field) {
            const oldValue = oldSchools[index]?.[field] ?? '';
            const newValue = school[field] ?? '';

            if (String(oldValue) !== String(newValue)) {
                schoolUpdates[field] = newValue;
            }
        });

        if (Object.keys(schoolUpdates).length > 0) {
            schoolUpdates.id = school.id ?? '';
            changedSchools.push(schoolUpdates);
        }
    });

    if (changedSchools.length > 0) {
        updates.schools = changedSchools;
    }

    return updates;
}

function normalizeFormData(formData) {
    const result = {
        positions: []
    };

    formData.forEach(function (item) {
        const name = item.name;
        const value = item.value ?? '';

        const matches = name.match(/^([a-zA-Z0-9_]+)\[(\d+)\]\[(.+)\]$/);

        if (matches) {
            const group = matches[1];
            const index = parseInt(matches[2], 10);
            const field = matches[3];

            if (!result[group]) {
                result[group] = [];
            }

            if (!result[group][index]) {
                result[group][index] = {};
            }

            result[group][index][field] = value;
        } else if (name === 'positions') {
            result.positions.push(value);
        } else if (name === 'referral-relationship') {
            result.referral_relationship = value;
        } else if (name === 'applied_dt') {
            result.applied_dt = value
                ? moment(value, 'MM/DD/YYYY').format('YYYY-MM-DD')
                : '';
        } else {
            result[name] = value;
        }
    });

    if (result.positions.length > 0) {
        result.positions.sort(function (a, b) {
            return Number(a) - Number(b);
        });
    }

    return result;
}

function applyWorkExperienceUpdates(currentWorkExp, updatedWorkExp) {
    updatedWorkExp.forEach(function (updatedItem) {
        if (updatedItem.id) {
            const index = currentWorkExp.findIndex(function (item) {
                return String(item.id) === String(updatedItem.id);
            });

            if (index !== -1) {
                Object.keys(updatedItem).forEach(function (key) {
                    currentWorkExp[index][key] = updatedItem[key];
                });
            } else {
                currentWorkExp.push(updatedItem);
            }
        } else {
            const blankIndex = currentWorkExp.findIndex(function (item) {
                return !item.id &&
                    !item.work_company &&
                    !item.work_position &&
                    !item.work_from &&
                    !item.work_to &&
                    !item.work_status &&
                    !item.work_reason;
            });

            if (blankIndex !== -1) {
                currentWorkExp[blankIndex] = {
                    ...currentWorkExp[blankIndex],
                    ...updatedItem
                };
            } else {
                currentWorkExp.push(updatedItem);
            }
        }
    });

    return currentWorkExp;
}

function applyArrayUpdates(currentArray, updatedArray) {
    if (!Array.isArray(currentArray)) {
        currentArray = [];
    }

    updatedArray.forEach(function (updatedItem) {
        const index = currentArray.findIndex(function (item) {
            return String(item.id) === String(updatedItem.id);
        });

        if (index !== -1) {
            Object.keys(updatedItem).forEach(function (field) {
                currentArray[index][field] = updatedItem[field];
            });
        } else {
            currentArray.push(updatedItem);
        }
    });

    return currentArray;
}

function applyObjectUpdates(currentData, update) {
    Object.keys(update).forEach(function (key) {
        if (Array.isArray(update[key])) {
            currentData[key] = applyArrayUpdates(currentData[key], update[key]);
        } else {
            currentData[key] = update[key];
        }
    });

    return currentData;
}