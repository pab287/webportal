let eventsDetails = null;
let participants = null;
let employees = null;
let empId = null;
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
    console.log(participants);
}


let eventVue = new Vue({
    el: "#events-content",
    data: {
        eventsData:{

        },
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
        },
        participantDataSelected:{},

    },
    mounted: function () {
        this.eventsData = JSON.parse(JSON.stringify(eventsDetails));
        $('#employee-select').prop('disabled', false);
        $('#new_event_form input[type="text"], #new_event_form input[type="email"]')
            .not('#employee-select') 
            .prop('disabled', true);
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
            const start = new Date(date_from);
            const end = new Date(date_to);
            if (now < start) {
                return { label: "Upcoming Event", class: "bg-info text-dark" };
            }
            if (now >= start && now <= end) {
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
    },
});

let participantsArray = Object.values(participants);
const participantsTable = $('#participantsTable').DataTable({
    dom: 'Bfrtlip',
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
                return itemDatatableActions(row.id, row.status, row.emp_id, row.cert_awarded);
            }
        }
    ],
    initComplete: function () {
        var btns = $('.dt-buttons').detach();
        $('#participantsTable_filter').append(btns);
        $('#participantsTable_filter .dt-button').removeClass('dt-button');

        // Apply Metronic styles
        $(".btnAdvanceSearch").addClass("btn m-btn--square btn-warning text-white mb-2 mt-2");
        $(".btnPdfAction").addClass("btn m-btn--square btn-warning text-white ml-2");
        $(".btnExcelAction").addClass("btn m-btn--square btn-info text-white ml-2");
    },
    buttons: [
        // {
        //     text: 'GENERATE ATTENDANCE SHEET',
        //     title: 'CRS REPORTS',
        //     className: 'btnAdvanceSearch btnSave',
        //     action: function ( e, dt, node, config ){
        //         console.log(eventVue.eventsData);
        //         if ($('#attendanceDate').data('daterangepicker')) {
        //             $('#attendanceDate').data('daterangepicker').remove();
        //         }
        //         $('#attendanceDate').daterangepicker({
        //             showDropdowns: true,
        //             autoUpdateInput: false,
        //             singleDatePicker: true,
        //             startDate: moment(),
        //             parentElement: $('#attendanceSheet .modal-body'),
        //             locale: {
        //                 format: 'MMM DD, YYYY',
        //                 cancelLabel: 'Clear'
        //             }
        //         });
        //         $('#attendanceDate').on('apply.daterangepicker', function(ev, picker) {
        //             $(this).val(picker.startDate.format('MMM DD, YYYY'));
        //         });
            
        //         $('#attendanceDate').on('cancel.daterangepicker', function(ev, picker) {
        //             $(this).val('');
        //         });

        //         $('#startTime').timepicker({
        //             timeFormat: 'h:mm p',
        //             interval: 30,
        //             minTime: '8',
        //             defaultTime: '8',
        //             startTime: '8:00',
        //         });

        //         $('#endTime').timepicker({
        //             timeFormat: 'h:mm p',
        //             interval: 30,
        //             minTime: '8',
        //             defaultTime: '12p',
        //             startTime: '8:00',
        //         });
        //         $("#attendanceSheet").modal('show');
        //     }
        // },
        // {
        //     extend: 'excelHtml5',
        //     title: 'Attendance Sheet',
        //     className: 'd-none btnSave buttons-excel',
        //     filename: function() {
        //         return 'attendance_sheet_' + moment().format('YYYY-MM-DD');
        //     },
        //     messageTop: function() {
        //         var attendanceDate = $('#attendanceDate').val() || moment().format('MMM DD, YYYY');
        //         var startTime = $('#startTime').val() || '9:00 AM';
        //         var endTime = $('#endTime').val() || '12:00 PM';
        //         var eventTitle = (eventVue && eventVue.eventsData && eventVue.eventsData.title) ? 
        //             eventVue.eventsData.title.toUpperCase() : 'TRAINING EVENT';
                
        //         return eventTitle + '\n' + 
        //                'Date: ' + attendanceDate + '\n' + 
        //                'Time: ' + startTime + ' - ' + endTime + '\n\n';
        //     },
        //     exportOptions: {
        //         columns: [1,2,3,4] ,
        //       },
        //     action: function ( e, dt, node, config ){
        //         $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, node, config);
        //     },
        //     customize: function(xlsx) {
        //         var sheet = xlsx.xl.worksheets['sheet1.xml'];
        //         $('row', sheet).each(function(index) {
        //             var rowNum = index + 1;
        //             if (index === 0) {
        //                 $(this).append('<c r="E1" t="inlineStr" s="2"><is><t>Signature</t></is></c>');
        //             } else {
        //                 $(this).append('<c r="E' + rowNum + '" t="inlineStr"><is><t></t></is></c>');
        //             }
        //         });
        //     }
        // }
    ],
});

function itemDatatableActions(id, status, emp_id = null, awarded) {
    let _actionButton = "";
    let fromDate = moment(eventsDetails.event_from);
    let toDate   = moment(eventsDetails.event_to);
    let today    = moment();
    let isUpcoming = today.isBefore(fromDate, 'day');
    let isDone     = today.isAfter(toDate, 'day');

    if (isUpcoming && status === 'pending') {
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

    if (!isDone) {
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
    }

    if (isDone) {
        if (status === 'confirmed') {
            if (!awarded || awarded == 0) {
                _actionButton += `
                    <a href="javascript:void(0)" 
                        class="btn btn-primary btn-sm m-btn m-btn--pill btnSave" 
                        onclick="awardCertificate(${emp_id})" 
                        title="Award Certificate">
                        <i class="la la-certificate"></i> Award Certificate
                    </a>`;
            } else {
                _actionButton += `
                    <button class="btn btn-secondary btn-sm m-btn m-btn--pill text-dark" disabled>
                        <i class="la la-certificate"></i> Certificate Awarded
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




function awardCertificate(id,rowId) {
        console.log(eventsDetails);
    $.ajax({
        url: baseUrl("events/get_modal_training/" + id),
        type: "post",
        data:{
            csrf_token : _csrf_hash
        },
        dataType: "json",
        
        cache: false,
        success: function (json) {
            console.log(json);
            let modalTempContent = $("#modalTempContent"); // grab the whole modal
            let modalContent = modalTempContent.find("#modal-content");
            if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                modalContent.empty();
                modalContent.append(json.html);
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
                        formData: { csrf_token: _csrf_hash, employee_id: id },
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
                        let currentForm = form[0];
                        let url = baseUrl("events/set_modal_trainings");
                        let formData = $(currentForm).serialize();
                        formData += "&event_id=" + encodeURIComponent(eventsDetails.id);
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
                                    setParticipantsData(json.participants);
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
        error: function (xhr) {
            toastr.error(
                "Something went wrong. Please try again.",
                "Error updating employee training and seminar!",
                5000
            );
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
    // Fires on any change, including val(null).trigger('change')
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

function setParticipantsData(newData) {
    participantsTable.clear();             
    participantsTable.rows.add(newData);   
    participantsTable.draw();  
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
                        setParticipantsData(res.participants);
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
                        setParticipantsData(res.participants);
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

$.validate({
    form : '#attendance_sheet_form',
    lang: 'en',
    onSuccess : function(form) {
        console.log(participantsTable.buttons().count()); 
        participantsTable.button(1).trigger();
        return false; 
    }
});