let eventsDetails = null;
let participants = null;
let employees = null;
let empId = null;
let attachments = null;
let schedule = null;
let selectedSchedule = null;
let modalTraining = null;
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
const attachmentTypes = [
    { id: 'training_document', text: 'Training Document' },
    { id: 'training_evaluation', text: 'Training Evaluation' },
    { id: 'resource_evaluation', text: 'Resource Evaluation' },
];
let selectedData = {
    company: '',
    department: '',
    email: '',
    firstname: '',
    lastname: '',
    middlename: '',
    mobile_no: '',
    position: '',
};
let selectedNonEmployee = {
    company: '',
    department: '',
    email: '',
    firstname: '',
    lastname: '',
    middlename: '',
    mobile_no: '',
    position: '',
}
if (_tempContentData !== undefined && _tempContentData !== null && _tempContentData !== '') {
    eventsDetails = {..._tempContentData.event_details};
    participants = {..._tempContentData.participants};
    employees =_tempContentData.employees;
    attachments = _tempContentData.attachments;
    schedule = _tempContentData.schedule;
}


let eventVue = new Vue({
    el: "#events-content",
    data: {
        eventsData:{},
        participants:participants,
        participantData:{
            company: '',
            department: '',
            email: '',
            firstname: '',
            lastname: '',
            middlename: '',
            mobile_no: '',
            position: '',
            suffix: '',
        },
        participantDataSelected:{},
        uploadedFiles:[],
        attachments:attachments,
        schedule:schedule,
        participantSched:[],
        loadingAssign: {},
        loadingUnassign: {},
        editSched:{
            id:'',
            title: '',
            location: '',
            description: '',
            event_date: '',
            start: '',
            end: '',
        },
        attendance:{},
        employee_attendance:{},
        emp_attendance_selected:{},
    },
    mounted: function () {
        this.eventsData = JSON.parse(JSON.stringify(eventsDetails));
        $('#employee-select').prop('disabled', false);
        $('#new_event_form input[type="text"], #new_event_form input[type="email"]')
            .not('#employee-select') 
            .prop('disabled', true);
        $('#edit_schedule_date').datepicker({ 
            autoclose: true,
            pickerPosition: 'bottom left',
            format: 'MM dd, yyyy',
            startDate: new Date(eventsDetails.event_from),
            endDate: new Date(eventsDetails.event_to),
        });
    },
    computed: {
        eventStatus() {
            return this.eventsStatus(this.eventsData.event_from, this.eventsData.event_to);
        },
        eventAlreadyHappened() {
            if (!this.eventsData?.event_to) return false; 
            const now = new Date();
            const eventEnd = new Date(this.eventsData.event_to);
            return eventEnd < now; 
          },
          participantsCount() {
            let list = Object.values(this.participants);
            let invited = list.length;
            let confirmed = list.filter(p => p.status === "confirmed").length;
            let declined = list.filter(p => p.status === "declined").length;
            let pending = list.filter(p => p.status === "pending").length;
            return { invited, confirmed, declined, pending };
        }
    },
    methods:{
        eventsStatus(date_from, date_to) {
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const start = new Date(date_from);
            const end = new Date(date_to);
            const startDate = new Date(start.getFullYear(), start.getMonth(), start.getDate());
            const endDate = new Date(end.getFullYear(), end.getMonth(), end.getDate());
            if (today < startDate) {
                return { label: "Upcoming Event", class: "bg-info text-dark" };
            }
            if (today >= startDate && today <= endDate) {
                return { label: "Ongoing Event", class: "bg-warning text-dark" };
            }
            return { label: "Event Done", class: "bg-success" };
        },
        formatDate(date_from, date_to) {
            const start = new Date(date_from), end = new Date(date_to);
            const fmt = (d, opts) => d.toLocaleDateString("en-US", opts);
            const optMD = { month: "short", day: "numeric" }, optY = { year: "numeric" };
            if (start.toDateString() === end.toDateString()) {
                return fmt(start, { ...optMD, ...optY });
            }
            if (start.getFullYear() === end.getFullYear()) {
                if (start.getMonth() === end.getMonth()) {
                    return `${fmt(start, optMD)} - ${end.getDate()}, ${start.getFullYear()}`;
                }
                return `${fmt(start, optMD)} - ${fmt(end, optMD)}, ${start.getFullYear()}`;
            }
            return `${fmt(start, { ...optMD, ...optY })} - ${fmt(end, { ...optMD, ...optY })}`;
        },
        toggleEmployeeFields() {
            if ($('#nonEmployeeToggle').is(':checked')) {
                $('#employee-select').prop('disabled', false);
                $('#new_event_form input[type="text"], #new_event_form input[type="email"]')
                    .not('#employee-select')
                    .prop('disabled', true);
                    selectedNonEmployee = JSON.parse(JSON.stringify(this.participantData)); 
                    this.participantData =  JSON.parse(JSON.stringify(selectedEmployee));
                    let toggle = $('#nonEmployeeToggle').prop('checked');
                    $('#new_event_form')[0].reset(); 
                    $("#employee-select").val(empId).trigger('change');
                    $('#nonEmployeeToggle').prop('checked', toggle);
            } else {
                $('#employee-select').prop('disabled', true);
                $('#new_event_form input[type="text"], #new_event_form input[type="email"]').prop('disabled', false);
                selectedEmployee = JSON.parse(JSON.stringify(this.participantData));
                this.participantData = JSON.parse(JSON.stringify(selectedNonEmployee));
                let toggle = $('#nonEmployeeToggle').prop('checked'); 
                $('#new_event_form')[0].reset(); 
                $("#employee-select").val(empId).trigger('change');
                $('#nonEmployeeToggle').prop('checked', toggle);
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
            return baseUrl(`assets/images/file_icons/${fileName}`);
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
        getAttachmentExtension: function(filename) {
            if (!filename) return '';
            const parts = filename.split('.');
            return parts.length > 1 ? parts.pop().toLowerCase() : '';
        },
        fileDelete: function(id){
            this.uploadedFiles.pop(id);
            this.count = this.uploadedFiles.length;
        },
        openFile(name,type) {
            let fileUrl = baseUrl("uploads/files/documents/event_" + eventsDetails.id + "/"+type+"/" + encodeURIComponent(name));
            checkFileExists(fileUrl, function(exists, mimeType) {
                if (!exists) {
                    $('#pdfViewerModal .modal-body').html('<p class="text-danger">Error: File not found.</p>');
                    $('#pdfViewerModal').modal('show');
                } else if (mimeType && mimeType.startsWith('application/pdf')) {
                    $('#pdfViewerModal .modal-body').html('<iframe id="pdfFrame" style="width: 100%; height: 600px;" frameborder="0"></iframe>');
                    $('#pdfViewerModal').modal('show');
                    $('#pdfFrame').attr('src', fileUrl);
                } else {
                    window.open(fileUrl, '_blank');
                }
            });
        },
        removeAttachment: function(id, type, filename) {
            const self = this;
            const fileUrl = baseUrl("uploads/files/documents/event_" + eventsDetails.id + "/" + type + "/" + encodeURIComponent(filename));
        
            Swal.fire({
                title: "Are you sure?",
                text: "This file will be permanently deleted.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: baseUrl("events/remove_file"),
                        type: "POST",
                        dataType: "json",
                        data: {
                            csrf_token : _csrf_hash,
                            file_path: fileUrl,
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message || "File deleted successfully.");
                                if (self.attachments[type]) {
                                    self.attachments[type] = self.attachments[type].filter(item => item.id !== id);
                                }
                                if (self.attachments[type] && self.attachments[type].length === 0) {
                                    delete self.attachments[type];
                                }
                            } else {
                                toastr.error(response.message || "Failed to remove attachment.");
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error("Error removing attachment: " + error);
                        }
                    });
                }
            });
        },
        formatTypeLabel(type) {
            if (!type) return '';
            let formatted = type.replace(/_/g, ' ');
            formatted = formatted.replace(/\b\w/g, c => c.toUpperCase());
            return formatted;
        },
        formatTime(start_time, end_time) {
            const format = (time) => {
                return moment(time, 'HH:mm').format('hh:mm A');
            };
            return `${format(start_time)} - ${format(end_time)}`;
        },
        editSchedule(event) {
            selectedSchedule = JSON.parse(JSON.stringify(event));
            this.editSched = event;
            $('#edit_schedule_date').datepicker('setDate',moment(event.event_date, 'YYYY-MM-DD').toDate());
            $('#edit_schedule_start').timepicker('setTime', moment(event.start, 'HH:mm:ss').format('hh:mm A'));
            $('#edit_schedule_end').timepicker('setTime', moment(event.end, 'HH:mm:ss').format('hh:mm A'));
            $('#edit_schedule').modal('show');
        },
        deleteSchedule(id){
            $.ajax({
                url: baseUrl("events/delete_schedule"),
                type: "POST",
                global: false,
                data: {
                    csrf_token:_csrf_hash,
                    id:id,
                    event_id: eventsDetails.id
                },
                dataType: "JSON",
                success: function(res) {
                    if (res.success) {
                        eventVue.schedule = res.schedule;
                        toastr.success(res.toastr_msg, "Success", 5000);
                    } else {
                        toastr.error(res.toastr_msg, "Error", 5000);
                    }
                }
            });
        },
        assignParticipant(schedule_id, participant_id) {
            self = this;
            Swal.fire({
                title: "Assign this participant?",
                text: "This will assign the participant to the schedule.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, assign",
                cancelButtonText: "Cancel"
            }).then(result => {
                if (result.isConfirmed) {
                    self.$set(self.loadingAssign, schedule_id, true);
                    $.ajax({
                        url: baseUrl("events/assign_participant"),
                        type: "POST",
                        global: false,
                        data: {
                            csrf_token: _csrf_hash,
                            schedule_id: schedule_id,
                            participant_id: participant_id,
                            event_id: eventsDetails.id
                        },
                        dataType: "JSON",
                        success: function(res) {
                            if (res.success) {
                                const sched = self.participantSched.find(s => s.schedule_id == schedule_id);
                                if (sched) sched.is_assigned = 1;
                                toastr.success(res.toastr_msg, "Success", 5000);
                            } else {
                                toastr.error(res.toastr_msg, "Error", 5000);
                            }
                        },
                        error: function() {
                            toastr.error("Request failed. Please try again.", "Error", 5000);
                        },
                        complete: function() {
                            self.$set(self.loadingAssign, schedule_id, false);
                        }
                    });
                }
            });
        },
        unassignParticipant(schedule_id, participant_id) {
            self = this;
            Swal.fire({
                title: "Unassign this participant?",
                text: "This will remove the participant from the schedule.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, unassign",
                cancelButtonText: "Cancel"
            }).then(result => {
                if (result.isConfirmed) {
                    self.$set(self.loadingUnassign, schedule_id, true);
                    $.ajax({
                        url: baseUrl("events/unassign_participant"),
                        type: "POST",
                        global: false,
                        data: {
                            csrf_token: _csrf_hash,
                            schedule_id: schedule_id,
                            participant_id: participant_id,
                            event_id: eventsDetails.id
                        },
                        dataType: "JSON",
                        success: function(res) {
                            if (res.success) {
                                const sched = self.participantSched.find(s => s.schedule_id == schedule_id);
                                if (sched) sched.is_assigned = 0;
                                toastr.success(res.toastr_msg, "Success", 5000);
                            } else {
                                toastr.error(res.toastr_msg, "Error", 5000);
                            }
                        },
                        error: function() {
                            toastr.error("Request failed. Please try again.", "Error", 5000);
                        },
                        complete: function() {
                            self.$set(self.loadingUnassign, schedule_id, false);
                        }
                    });
                }
            });
        },
        formatDateLocale(date) {
            const d = new Date(date);
            return d.toLocaleDateString('en-US', { 
                weekday: 'short', 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        },
        formatDateLocale2(date) {
            const d = new Date(date);
            return d.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        },
        takeAttendance(sched) {
            const btn = $(event.currentTarget);
            btn.prop("disabled", true);
            $.ajax({
                url: baseUrl("events/take_attendance"),
                type: "POST",
                global: false,
                data: {
                    csrf_token: _csrf_hash,
                    sched_id: sched.id,
                },
                dataType: "JSON",
                success: function(res) {
                    eventVue.attendance = res;
                    eventVue.editSched = sched;
                    $('#generate_attendance').modal('show');
                },
                error: function() {
                    toastr.error("Failed to fetch attendance.", "Error");
                },
                complete: function() {
                    btn.prop("disabled", false).html('<i class="la la-calendar"></i>');
                }
            });
        },        
        togglePresence(attendance_id,value){
            $.ajax({
                url: baseUrl("events/update_attendance"),
                type: "POST",
                global: false,
                data: {
                    csrf_token: _csrf_hash,
                    id: attendance_id,
                    is_present: value,
                },
                success: function(res) {
                    if(res.success){
                        toastr.success(res.toastr_msg, 'Success', 5000);
                    }else{
                        toastr.error(res.toastr_msg, 'Error', 5000);
                    }
                }
            });
        },
        exportAttendance(item){
            const headers = [
                "FIRST NAME", "MIDDLE NAME", "LAST NAME", "EMAIL", "MOBILE NO", "POSITION",
                "COMPANY", "DEPARTMENT", "ATTENDANCE"
            ];
            const workbook = XLSX.utils.book_new();
            const worksheet = XLSX.utils.aoa_to_sheet([headers]);
            const dataRows = item.map(participant => [
                (participant.firstname || '').toString().toUpperCase(),
                (participant.middlename || '').toString().toUpperCase(),
                (participant.lastname || '').toString().toUpperCase(),
                (participant.email || '').toString().toUpperCase(),
                (participant.mobile_no || '').toString().toUpperCase(),
                (participant.position || '').toString().toUpperCase(),
                (participant.company || '').toString().toUpperCase(),
                (participant.department || '').toString().toUpperCase(),
                ('').toString().toUpperCase(),
            ]);
            XLSX.utils.sheet_add_aoa(worksheet, dataRows, { origin: 'A2' });
            XLSX.utils.book_append_sheet(workbook, worksheet, "Attendance");
            const wbout = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
            const blob = new Blob([wbout], { type: 'application/octet-stream' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `ATTENDANCE_${new Date().toISOString().split('T')[0]}.xlsx`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        },
        uploadCertificate(rowId) {
            const rowData = participantsTable.row(`#${rowId}`).data();
            $('#attendanceCheck').modal('hide');
            let modalTempContent = $("#modalTempContent"); // grab the whole modal
            let modalContent = modalTempContent.find("#modal-content");
            if (typeof modalContent !== "undefined" && typeof modalTraining !== "undefined") {
                modalContent.empty();
                modalContent.append(modalTraining);
                modalTempContent.modal('show');

                if ($('#train_from').data('daterangepicker')) {
                    $('#train_from').data('daterangepicker').remove();
                }
                if ($('#train_to').data('daterangepicker')) {
                    $('#train_to').data('daterangepicker').remove();
                }
                
                $('#train_from').daterangepicker({
                    showDropdowns: true,
                    autoUpdateInput: false,
                    singleDatePicker: true,
                    startDate: eventsDetails.event_from ? moment(eventsDetails.event_from) : moment(),
                    locale: {
                        format: 'YYYY-MM-DD',
                        cancelLabel: 'Clear'
                    }
                });
                
                $('#train_from').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD'));
                });
                
                $('#train_from').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                });
                
                $('#train_to').daterangepicker({
                    showDropdowns: true,
                    autoUpdateInput: false,
                    singleDatePicker: true,
                    startDate: eventsDetails.event_to ? moment(eventsDetails.event_to) : moment(),
                    locale: {
                        format: 'YYYY-MM-DD',
                        cancelLabel: 'Clear'
                    }
                });
                
                $('#train_to').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD'));
                });
                
                $('#train_to').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                });
                $("#train_from").val(moment(eventsDetails.event_from).format("YYYY-MM-DD"));
                $("#train_to").val(moment(eventsDetails.event_to).format("YYYY-MM-DD"));

                $("#train_from").attr("data-original", eventsDetails.event_from); 
                $("#train_from").attr("value", moment(eventsDetails.event_from).format("MMM DD, YYYY"));

                $("#train_to").attr("data-original", eventsDetails.event_to);
                $("#train_to").attr("value", moment(eventsDetails.event_to).format("MMM DD, YYYY"));

                $("#training").val(eventsDetails.event_title);
                $("#train_institution").val(eventsDetails.events_by);

                if (Array.isArray(eventsDetails.speakers)) {
                    const speakers = eventsDetails.speakers
                        .map(s => s.speaker_name)
                        .join(", ");
                    $("#train_conductor").val(speakers);
                }
                $("#train_venue").val(eventsDetails.event_venue);

                let url = baseUrl("events/upload_employee_training");
                $("#fileupload_training")
                    .fileupload({
                        url: url,
                        dataType: "json",
                        formData: { csrf_token: _csrf_hash, employee_id: rowData.emp_id, is_employee:rowData.is_employee, applicant_id:rowData.id },
                        done: function (e, data) {
                            var result = data.result;
                            if (result.response) {
                                modalContent.find("#training_attachment").val(result.filename);
                                modalContent.find("#temp_fileupload").empty().text(result.filename);
                                toastr.success(result.toastr_msg, "Upload Training and Seminar File", 5000);
                            } else {
                                toastr.error(result.toastr_msg, "Upload Training and Seminar File", 5000);
                            }
                        }
                    })
                    .prop("disabled", !$.support.fileInput)
                    .parent()
                    .addClass($.support.fileInput ? undefined : "disabled");

                $.validate({
                    form: "#form-trainings",
                    lang: "en",
                    onSuccess: function (form) {
                        let attachment = $("#training_attachment").val().trim();
                        if (!attachment) {
                            toastr.warning("Please upload a training attachment before submitting.", "Missing File");
                            return false; 
                        }
                        let currentForm = form[0];
                        let url = baseUrl("events/set_modal_trainings");
                        let formData = $(currentForm).serialize();
                        formData += "&event_id=" + encodeURIComponent(eventsDetails.id);
                        formData += "&is_employee=" + encodeURIComponent(rowData.is_employee);
                        formData += "&applicant_id=" + encodeURIComponent(rowData.id);
                        formData += "&emp_id=" + encodeURIComponent(rowData.emp_id);
                        $.ajax({
                            url: url,
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
                                        "Employee training and seminar has been saved.",
                                        5000
                                    );
                                    currentForm.reset();
                                    modalTempContent.modal("hide");
                                    setParticipantsData(json.participants,true);
                                } else {
                                    toastr.error(
                                        json.toastr_msg,
                                        "Error updating employee training and seminar!",
                                        5000
                                    );
                                }

                                $(currentForm)
                                    .find(".btn-submit")
                                    .removeClass(
                                        "m-btn--custom m-loader m-loader--light m-loader--right"
                                    );
                            },

                        });
                        return false;
                    }
                });
            }

        },
        removeCertificate(rowId) {
            const rowData = participantsTable.row(`#${rowId}`).data();
            if (rowData.is_employee == 1) {
                fileUrl = baseUrl(`/uploads/files/documents/employee_files/empcode_${rowData.emp_id}/trainings/${rowData.cert_attachment}`);
            } else {
                fileUrl = baseUrl(`/uploads/files/documents/applicant_files/appcode_${rowData.id}/trainings/${rowData.cert_attachment}`);
            }
            Swal.fire({
                title: "Are you sure?",
                text: "This will remove the certificate record for this participant.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, remove",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: baseUrl("events/remove_certificate"),
                        type: "POST",
                        data: {
                            csrf_token: _csrf_hash,
                            event_id: eventsDetails.id,
                            participant_id: rowData.id,
                            cert_id : rowData.cert_awarded,
                            file_path: fileUrl,
                        },
                        dataType: "JSON",
                        success: function(res) {
                            if(res.success){
                                toastr.success(res.message,"Certificate Removed", 5000);
                                setParticipantsData(res.participants,true);
                            }else{
                                toastr.error(res.message,"Failed", 5000);
                            }
                            $("#pdfViewerModal").modal("hide");
                        },
                        error: function() {
                            Swal.fire({
                                icon: "error",
                                title: "Failed",
                                text: "An error occurred while removing the certificate.",
                            });
                        },
                    });
                }
            });
        },
    },
});

let participantsArray = Object.values(participants);
const participantsTable = $('#participantsTable').DataTable({
    dom: 'frtlip',
    data: participantsArray,
    // scrollX: true,
    responsive: true,
    autoWidth: false,
    searching: true,
    rowId: 'id',
    order: [[1, 'asc']],
    columns: [
        { data: 'id', visible: false, defaultContent: '' },
        { data: 'fullname', title:'Name', visible: false, defaultContent: '' },
        { data: 'position', title:'Position', visible: false, defaultContent: '' },
        { data: 'department_head_fullname', title:'Department Head', visible: false, defaultContent: '' },
        { data: 'department', title:'Department', visible: false, defaultContent: '' },
        { data: 'lastname', title: 'Participant', defaultContent: '',
            render: function (data, type, row, meta) {
                return `
                    <div class="font-weight-bold text-uppercase">${row.fullname}</div>
                    <div class="text-muted small">${row.position ?? ''}</div>
                `;
            }
        },
        { data: "is_employee", title: 'Company', className: "text-left", defaultContent: '',
            render: function (data, type, row, meta) {
                return `
                    <div>${row.company ?? ''}</div>
                    <div class="text-muted small">${row.department ?? ''}</div>
                `;
            }
        },
        { data: 'email', title: 'Contact', className: "text-left", defaultContent: '',
            render: (data, type, row) => `
                ${row.email ? `<div>${row.email}</div>` : ''}
                ${row.mobile_no ? `<div> ${row.mobile_no}</div>` : ''}
            `
        },
        { 
            data: 'status', 
            title: 'Status', 
            className: "text-left", 
            defaultContent: '',
            render: function (data, type, row, meta) {
                const statusMap = {
                    pending: 'badge-warning',
                    invited: 'badge-info',
                    confirmed: 'badge-success',
                    declined: 'badge-danger'
                };
        
                let toDate   = moment(eventsDetails.event_to);
                let today    = moment();
                let isDone   = today.isAfter(toDate, 'day');
        
                if (isDone) {
                    if (data === 'confirmed') {
                        if (row.cert_awarded && row.cert_awarded != 0) {
                            return `<span class="badge bg-success" >Certificate given</span>`;
                        } else {
                            return `<span class="badge badge-secondary">Certificate not given</span>`;
                        }
                    } 
                    if (data === 'pending' || data === 'invited' || data === 'declined') {
                        return `<span class="badge badge-dark">Did not attend</span>`;
                    }
                }
        
                return `<span class="badge ${statusMap[data] || 'badge-secondary'}">${data}</span>`;
            }
        },        
        { data: null, title: 'Actions', className: "text-left", orderable: false, defaultContent: '',
            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id, row.status, row.cert_awarded);
            }
        }
    ],
    // initComplete: function () {
    //     var btns = $('.dt-buttons').detach();
    //     $('#participantsTable_filter').append(btns);
    //     $('#participantsTable_filter .dt-button').removeClass('dt-button');

    //     $(".btnAdvanceSearch").addClass("btn m-btn--square btn-warning text-white mb-2 mt-2");
    //     $(".btnPdfAction").addClass("btn m-btn--square btn-warning text-white ml-2");
    //     $(".btnExcelAction").addClass("btn m-btn--square btn-info text-white ml-2");
    // },
});

function itemDatatableActions(id, status, awarded) {
    let _actionButton = "";
    let fromDate = moment(eventsDetails.event_from);
    let toDate   = moment(eventsDetails.event_to);
    let today    = moment();
    let isUpcoming = today.isBefore(fromDate, 'day');
    let isDone     = today.isAfter(toDate, 'day');
    console.log(status);
    if (!isDone) {

        if(status != 'confirmed' && status != 'declined'){
            _actionButton += `
            <a href="javascript:void(0)" 
                class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnSave" 
                onclick="confirmParticipant(${id})" 
                title="Confirm Attendance">
                <i class="la la-check-circle text-success"></i>
            </a>
            <a href="javascript:void(0)" 
                class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnSave" 
                onclick="declineParticipant(${id})" 
                title="Decline Attendance">
                <i class="la la-times-circle text-danger"></i>
            </a>`;
        }

        _actionButton += `
            <a href="javascript:void(0)" 
                class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnEdit" 
                onclick="onEditEvent(${id})" 
                title="Edit Participant">
                <i class="la la-eye"></i>
            </a>`;

        _actionButton += `
            <a href="javascript:void(0)" 
                class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnArchive" 
                onclick="archiveParticipant(${id})" 
                title="Archive Participant">
                <i class="la la-file-archive-o"></i>
            </a>`;
        
        if(status == 'confirmed'){
            _actionButton += `
            <a href="javascript:void(0)" 
                class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnEdit" 
                onclick="assignSchedule(${id})" 
                title="Manage Schedule">
                <i class="la 	la-calendar-plus-o"></i>
            </a>`;
        }
    }

    if (isDone) {
        if (status === 'confirmed') {
            if (!awarded || awarded == 0) {
                _actionButton += `
                    <a href="javascript:void(0)" 
                        class="btn btn-primary btn-sm m-btn m-btn--pill btnSave" 
                        onclick="awardCertificate(${id})" 
                        title="Check Attendance">
                        <i class="la la-clipboard"></i> Check Attendance
                    </a>`;
            } else {
                _actionButton += `
                    <button class="btn btn-secondary btn-sm m-btn m-btn--pill text-dark" onclick="openCertificate(${id})">
                        <i class="la la-certificate"></i> View Certificate
                    </button>`;
            }
        } else {
            _actionButton += `
                <button class="btn btn-secondary btn-sm m-btn m-btn--pill text-dark" disabled>
                    <i class="la la-certificate"></i> Not Eligible
                </button>`;
        }
    }

    return _actionButton;
}

function awardCertificate(rowId) {
    const btn = $(`.btnSave[onclick="awardCertificate(${rowId})"]`);
    btn.prop("disabled", true).html('<i class="la la-spinner la-spin"></i> Checking...');
    $.ajax({
        url: baseUrl("events/check_attendance"),
        type: "post",
        global: false,
        data:{
            csrf_token : _csrf_hash,
            events_id: eventsDetails.id,
            participant_id: rowId
        },
        dataType: "json",
        cache: false,
        success: function (res) {
            const rowData = participantsTable.row(`#${rowId}`).data();
            eventVue.employee_attendance = res.attendance;
            modalTraining = res.modal.html;
            eventVue.emp_attendance_selected = rowData;
            $("#attendanceCheck").modal("show");
        },
        error: function () {
            toastr.error("An error occurred while checking attendance.", "Error");
        },
        complete: function () {
            btn.prop("disabled", false).html('<i class="la la-clipboard"></i> Check Attendance');
        }
    });
}


function openCertificate(id) {
    const rowData = participantsTable.row(`#${id}`).data();
    eventVue.emp_attendance_selected = rowData;
    let fileUrl = "";
    if (rowData.is_employee == 1) {
        fileUrl = baseUrl(`/uploads/files/documents/employee_files/empcode_${rowData.emp_id}/trainings/${rowData.cert_attachment}`);
    } else {
        fileUrl = baseUrl(`/uploads/files/documents/applicant_files/appcode_${rowData.id}/trainings/${rowData.cert_attachment}`);
    }
    checkFileExists(fileUrl, function (exists, mimeType) {
        const $modalBody = $('#pdfViewerModal .modal-body');
        if (!exists) {
            $modalBody.html('<p class="text-danger">Error: File not found.</p>');
            $('#pdfViewerModal').modal('show');
            return;
        }
        if (mimeType && mimeType.startsWith('application/pdf')) {
            $modalBody.html('<iframe id="pdfFrame" style="width:100%;height:600px;" frameborder="0"></iframe>');
            $('#pdfViewerModal').modal('show');
            $('#pdfFrame').attr('src', fileUrl);
        } else {
            window.open(fileUrl, '_blank');
        }
    });
}

$("#employee-select").select2({
    dropdownParent: $('#addNewParticipant'),
    data: employees,
    allowClear: true,
    placeholder: "Select an option",
    width: '100%'
})
.on('select2:select', function (e) {
    empId = $(this).val();
    $('#new_event_form')[0].reset();
    $(this).val(empId);

    $.ajax({
        url: baseUrl('events/get_employee_information'),
        type: "POST",
        dataType: "json",
        data: {
            emp_id: empId,
            csrf_token: $("#csrf_token").val()
        },
        success: function (response) {
            selectedEmployee = response;
            eventVue.participantData = JSON.parse(JSON.stringify(response));
        }
    });
})
.on('change', function () {
    if (!$(this).val()) {
        selectedEmployee = {
            company: '',
            department: '',
            email: '',
            firstname: '',
            lastname: '',
            middlename: '',
            mobile_no: '',
            position: '',
        };
        eventVue.participantData = {
            company: '',
            department: '',
            email: '',
            firstname: '',
            lastname: '',
            middlename: '',
            mobile_no: '',
            position: '',
            suffix: '',
        };
    }
});

let newValidation = $.validate({
    form : '#new_event_form',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess : function(form) {
        let formData = eventVue.participantData;
        formData.csrf_token = $("#csrf_token").val();
        formData.is_employee = $("#nonEmployeeToggle").prop("checked") ? 1 : 0;
        if(formData.is_employee == 1){
            formData.emp_id = empId; 
        }
        formData.event_id = eventsDetails.id;
        $.ajax({
            url: baseUrl('events/save_participant'),
            type: "POST",
            dataType: "json",
            data: formData,
            // beforeSend: function() {
            //     $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            // },
            success: function(res) {
                if(res.success){
                    eventVue.participantData ={
                        company: '',
                        department: '',
                        email: '',
                        firstname: '',
                        lastname: '',
                        middlename: '',
                        mobile_no: '',
                        position: '',
                        suffix: '',
                    },
                    $("#addNewParticipant").modal('hide');
                    toastr.success(res.message, 'Success', 5000);
                    $("#employee-select option[value='" + empId + "']").remove();
                    $("#employee-select").trigger('change.select2');
                    setParticipantsData(res.participants);
                }else{
                    toastr.error(res.message, 'Error', 5000);
                }
            }
        });
        return false;
    }
});

function setParticipantsData(newData, redraw = false) { 
    let currentPage = 0;
    if (redraw) {
        currentPage = participantsTable.page(); 
    }
    participantsTable.clear();             
    participantsTable.rows.add(newData);   
    if (redraw) {
        participantsTable.draw(false); 
        participantsTable.page(currentPage).draw(false);
    } else {
        participantsTable.draw(); 
    }
    eventVue.participants = JSON.parse(JSON.stringify(newData));
}

function onEditEvent(id) {
    let rowData = participantsTable.row('#'+id).data();
    selectedData = rowData; 
    eventVue.participantDataSelected = JSON.parse(JSON.stringify(rowData));
    $("#editParticipant").modal("show");
}

function archiveParticipant(id) {
    let rowData = participantsTable.row('#'+id).data();
    let fullname = rowData.firstname + ' ' + rowData.middlename + ' ' + rowData.lastname;
    Swal.fire({
        title: 'Are you sure?',
        text: "This participant will be archived.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, archive it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl('events/archive_participant'),
                type: 'POST',
                data: {
                    csrf_token: $("#csrf_token").val(),
                    id: id,
                    event_id: eventsDetails.id,
                    is_employee: rowData.is_employee,
                    emp_id: rowData.emp_id,
                    emp_name: fullname,
                    event_title: eventsDetails.event_title
                },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message, 'Success', { timeOut: 5000 });
                        setParticipantsData(res.participants);
                        if (rowData.is_employee == 1 && res.employee) {
                            let newOption = new Option(res.employee.text, res.employee.id, false, false);
                            $('#employee-select').append(newOption).trigger('change');
                        }
                    } else {
                        toastr.error(res.message || 'Failed to archive participant.', 'Error', { timeOut: 5000 });
                    }
                    eventVue.participantData = {
                        company: '',
                        department: '',
                        email: '',
                        firstname: '',
                        lastname: '',
                        middlename: '',
                        mobile_no: '',
                        position: '',
                        suffix: '',
                    };
                },
                error: function(xhr, status, error) {
                    toastr.error('Something went wrong. Please try again.', 'Error', { timeOut: 5000 });
                }
            });
        }
    });
}


$.validate({
    form : '#edit_participant_form',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess : function(form) {
        let formData = JSON.parse(JSON.stringify(eventVue.participantDataSelected));
        if(!checkChanges(formData, selectedData)){
            toastr.error("NO CHANGES DETECTED", 'Error', 5000);
            return false;
        }
        formData.csrf_token = $("#csrf_token").val();
        formData.event_title = eventsDetails.event_title;
        $.ajax({
            url: baseUrl('events/update_participant'),
            type: "POST",
            dataType: "json",
            data: formData,
            success: function(res) {
                if(res.success){
                    $("#editParticipant").modal('hide');
                    toastr.success(res.message, 'Success', 5000);
                    setParticipantsData(res.participants);
                }else{
                    toastr.error(res.message, 'Error', 5000);
                }
            }
        });
    }
});

function checkChanges(newData, oldData,){
    if(JSON.stringify(oldData) !== JSON.stringify(newData)){
        return true;
    }
}

function confirmParticipant(id) {
    let rowData = participantsTable.row('#'+id).data();
    let fullname = rowData.firstname + ' ' + rowData.middlename + ' ' + rowData.lastname;
    Swal.fire({
        title: 'Confirm Attendance?',
        text: "Do you want to mark this participant as attending?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Confirm',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl('events/confirm_participant'),
                type: "POST",
                global: false,
                data: { 
                    csrf_token: $("#csrf_token").val(),
                    event_id: eventsDetails.id,
                    fullname: fullname,
                    event_title: eventsDetails.event_title,
                    id: id,
                    schedule : schedule,
                },
                dataType: "json",
                success: function(res) {
                    if(res.success){
                        toastr.success(res.message, 'Success', 5000);
                        setParticipantsData(res.participants,true);
                        if(res.schedule){
                            eventVue.schedule = res.schedule;
                        }
                    }else{
                        toastr.error(res.message, 'Error', 5000);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Something went wrong while updating participant.', 'Error');
                }
            });
        }
    });
}


function declineParticipant(id) {
    let rowData = participantsTable.row('#'+id).data();
    let fullname = rowData.firstname + ' ' + rowData.middlename + ' ' + rowData.lastname;
    Swal.fire({
        title: 'Decline Attendance?',
        text: "Do you want to mark this participant as not attending?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Decline',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl('events/decline_participant'),
                type: "POST",
                global: false,
                data: { 
                    csrf_token: $("#csrf_token").val(),
                    event_id: eventsDetails.id,
                    fullname: fullname,
                    event_title: eventsDetails.event_title,
                    id: id },
                dataType: "json",
                success: function(res) {
                    if(res.success){
                        toastr.success(res.message, 'Success', 5000);
                        setParticipantsData(res.participants,true);
                    }else{
                        toastr.error(res.message, 'Error', 5000);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Something went wrong while declining participant.', 'Error');
                }
            });
        }
    });
}

const expandedButtons = $('button[aria-expanded="true"]');
expandedButtons.each(function (i, el) {
    $(el).css('transform', 'rotate(90deg)');
})

$('#toggle_schedule').on('click', function() {
    $('#toggle_schedule_icon').toggleClass('la-plus la-minus');
    $('#m_portlet_schedule').toggleClass('m-portlet--collapsed');
});

$('.accordion').on('hide.bs.collapse', function (e) {
    const el = $(e.target)
        .prev('.accordion-header')
        .find(".btn");
    el.css('transform', 'rotate(0deg)');
});
$('.accordion').on('show.bs.collapse', function (e) {
    const el = $(e.target)
        .prev('.accordion-header')
        .find(".btn");
    el.css('transform', 'rotate(90deg)');
});


$('#fileupload').on('change', function(e) {
    handleFiles(e.target.files);
});

function handleFiles(fileList) {
    eventVue.uploadedFiles = [];
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
    
    const exists = eventVue.uploadedFiles.some(f => f.name === file.name);
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
    eventVue.uploadedFiles.push(fileObj);
}

$('#attachment_type').select2({
    placeholder: "Select Attachment Type",
    width: '100%',
    data: attachmentTypes,
});

$('#New_Add_File').on('submit', function(e) {
    e.preventDefault();
    const form = $('#New_Add_File');
    const formData = new FormData(form[0]);
    formData.append('event_id', eventsDetails.id);
    if (form.isValid()) {
        $.ajax({
            url: baseUrl('events/upload_documents'),
            dataType: "JSON",
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.toastr_msg, 'Success', 5000);
                    eventVue.uploadedFiles = [];
                    $('#fileupload').val(null);
                    eventVue.attachments = response.attachments;
                }else{
                    toastr.error(response.toastr_msg, 'Error', 5000);
                }
                $('#New_Add_File')[0].reset();
                $('#attachment_type').val(null).trigger('change');
                $('#newAttachment').modal('hide');
            }
        });
    }
});

$('#schedule_date').datepicker({ 
    autoclose: true,
    pickerPosition: 'bottom left',
    format: 'MM dd, yyyy',
    startDate: new Date(eventsDetails.event_from),
    endDate: new Date(eventsDetails.event_to)
}).datepicker('setDate', new Date(eventsDetails.event_from));

$('#schedule_start').timepicker({
    defaultTime: '08:00 AM',
    minuteStep: 10,
});

$('#schedule_end').timepicker({
    defaultTime: '05:00 PM',
    minuteStep: 10,
});

function assignSchedule(participant){
    $.ajax({
        url: baseUrl("events/assign_schedule"),
        type: "POST",
        global: false,
        data: {
            csrf_token : _csrf_hash,
            events_participants_id: participant,
            events_id: eventsDetails.id,
        },
        dataType: "JSON",
        success: function(res) {
            if (!Array.isArray(res) || res.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No schedule created for this event yet.',
                    timer: 5000,
                    timerProgressBar: true,
                    confirmButtonColor: '#d33'
                });
                return;
            }else{
                eventVue.participantSched = res;
                $('#attendanceSheet').modal('show');
            }
        }   
    });
}

$.validate({
    form: "#new_event_sched",
    lang: "en",
    onSuccess: function (form) {
        let currentForm = form[0];
        let url = baseUrl("events/new_event_sched");
        let formData = $(currentForm).serialize();
        formData += "&event_id=" + encodeURIComponent(eventsDetails.id);
        $.ajax({
            url: url,
            type: "POST",
            dataType: "JSON",
            data: formData,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.toastr_msg, 'Success', 5000);
                    eventVue.schedule = response.schedule;
                } else {
                    toastr.error(response.toastr_msg, 'Error', 5000);
                }
            }
        });
    }
});

$.validate({
    form: "#edit_event_sched",
    lang: "en",
    onSuccess: function (form) {
        let currentForm = form[0];
        let edited = JSON.parse(JSON.stringify(eventVue.editSched));
        if(!checkChanges(selectedSchedule, edited)){
            toastr.error("NO CHANGES DETECTED", 'Error', 5000);
            return false;
        }
        let url = baseUrl("events/update_schedule");
        let formData = $(currentForm).serialize();
        formData += "&event_id=" + encodeURIComponent(eventsDetails.id);
        $.ajax({
            url: url,
            type: "POST",
            dataType: "JSON",
            data: formData,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.toastr_msg, 'Success', 5000);
                    eventVue.schedule = response.schedule;
                    $(currentForm)[0].reset();
                    $('#edit_schedule').modal('hide');
                } else {
                    toastr.error(response.toastr_msg, 'Error', 5000);
                }
            }
        });
        return false;
    }

});

$('#edit_schedule_start').on('changeTime.timepicker', function(e) {
    const time24 = moment(e.time.value, ["h:mm A"]).format("HH:mm:ss");
    eventVue.editSched.start = time24;
});

$('#edit_schedule_end').on('changeTime.timepicker', function(e) {
    const time24 = moment(e.time.value, ["h:mm A"]).format("HH:mm:ss");
    eventVue.editSched.end = time24;
});

$('#edit_schedule_date').on('changeDate', function (e) {
    eventVue.editSched.event_date = moment(e.date).format('YYYY-MM-DD');
});



function checkFileExists(url, callback) {
    $.ajax({
        url: url,
        type: 'HEAD',
        success: function(response, status, xhr) {
            var mimeType = xhr.getResponseHeader("Content-Type");
            callback(true, mimeType);
        },
        error: function(xhr, status, error) {
            callback(false, null);
        }
    });
}

$("#employee-select").select2({
    dropdownParent: $('#addNewParticipant'),
    data: employees,
    allowClear: true,
    placeholder: "Select an option",
    width: '100%'
})

function syncScheduleTime(changed) {
    const startVal = $('#schedule_start').val();
    const endVal = $('#schedule_end').val();
    if (!startVal || !endVal) return;
    const start = moment(startVal, 'h:mm A');
    const end = moment(endVal, 'h:mm A');
    if (!start.isValid() || !end.isValid()) return;
    if (end.isBefore(start)) {
        if (changed === 'start') {
            $('#schedule_end').val(start.format('h:mm A')).trigger('change');
        } else {
            $('#schedule_start').val(end.format('h:mm A')).trigger('change');
        }
    }
}

$('#schedule_start').on('change', function () {
    syncScheduleTime('start');
});

$('#schedule_end').on('change', function () {
    syncScheduleTime('end');
});

function syncScheduleTimeEdit(changed) {
    const startVal = $('#edit_schedule_start').val();
    const endVal = $('#edit_schedule_end').val();
    if (!startVal || !endVal) return;
    const start = moment(startVal, 'h:mm A');
    const end = moment(endVal, 'h:mm A');
    if (!start.isValid() || !end.isValid()) return;
    if (end.isBefore(start)) {
        if (changed === 'start') {
            $('#edit_schedule_end').val(start.format('h:mm A')).trigger('change');
        } else {
            $('#edit_schedule_start').val(end.format('h:mm A')).trigger('change');
        }
    }
}

$('#edit_schedule_start').on('change', function () {
    syncScheduleTimeEdit('start');
});

$('#edit_schedule_end').on('change', function () {
    syncScheduleTimeEdit('end');
});