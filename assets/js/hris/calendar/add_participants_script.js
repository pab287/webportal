let eventsDetails = null;
let participants = null;
let employees = null;
let empId = null;
let selectedData = null;
let selectedEmployee = {
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
}


let eventVue = new Vue({
    el: "#events-content",
    data: {
        eventsData:{

        },
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
          }
    },
    methods:{
        eventsStatus(date_from, date_to) {
            const now = new Date();
            const start = new Date(date_from);
            const end = new Date(date_to);
            if (now < start) {
                return { label: "Upcoming Event", class: "bg-warning text-dark" };
            }
            if (now >= start && now <= end) {
                return { label: "Ongoing Event", class: "bg-success" };
            }
            return { label: "Event Done", class: "bg-secondary" };
        },
        formatDate(date_from, date_to) {
            const start = new Date(date_from), end = new Date(date_to)
            const fmt = (d, opts) => d.toLocaleDateString("en-US", opts)
            const optMD = { month: "short", day: "numeric" }, optY = { year: "numeric" }
    
            if (start.getFullYear() === end.getFullYear()) {
                if (start.getMonth() === end.getMonth())
                    return `${fmt(start, optMD)} - ${end.getDate()}, ${start.getFullYear()}`
                return `${fmt(start, optMD)} - ${fmt(end, optMD)}, ${start.getFullYear()}`
            }
            return `${fmt(start, { ...optMD, ...optY })} - ${fmt(end, { ...optMD, ...optY })}`
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
    dom: 'frtlip',
    data: participantsArray,
    // scrollX: true,
    responsive: true,
    autoWidth: false,
    searching: true,
    rowId: 'id',
    order: [[1, 'asc']],
    columns: [
        { data: 'id', visible: false },
        { data: 'lastname', title: 'Participant',
            render: function (data, type, row, meta) {
                return `
                    <div class="font-weight-bold text-uppercase">${row.fullname}</div>
                    <div class="text-muted small">${row.position ?? ''}</div>
                `;
            }
        },
        { data: "is_employee", title: 'Company', className: "text-center",
            render: function (data, type, row, meta) {
                return `
                    <div>${row.company ?? ''}</div>
                    <div class="text-muted small">${row.department ?? ''}</div>
                `;
            }
        },
        { data: null, title: 'Contact', className: "text-center",
            render: function (data, type, row, meta) {
                return `
                    <div><i class="la la-envelope"></i> ${row.email ?? ''}</div>
                    <div><i class="la la-phone"></i> ${row.mobile_no ?? ''}</div>
                `;
            }
        },
        { data: 'status', title: 'Status', className: "text-center",
            render: function (data, type, row, meta) {
                const statusMap = {
                    pending: 'badge-warning',
                    invited: 'badge-info',
                    confirmed: 'badge-success',
                    declined: 'badge-danger'
                };
                return `<span class="badge ${statusMap[data] || 'badge-secondary'}">${data}</span>`;
            }
        },
        { data: null, title: 'Actions', className: "text-left", orderable: false,
            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id, row.status);
            }
        }
    ],
    buttons: [
        { 
            extend: 'csv',
            exportOptions: {
                // columns: "thead th:not(.notExport)"
            },
            // fieldBoundary: '',
            customize: function (csv) {
                let data = csv.split("\n"); // Split CSV into rows
                
                let targetUppercase = [1, 6]; // Columns to make uppercase
                let targetTotalCharges = 5;
                // Loop through each row
                data = data.map((row, rowIndex) => {
                    // Split row into columns, considering quoted fields
                    let columns = row.match(/(".*?"|[^",\s]+)(?=\s*,|\s*$)/g);
                
                    columns = columns.map((col, columnIndex) => {
                        col = col.trim(); // Remove extra spaces
                
                        if (rowIndex === 0) { 
                            return col.replace(/\b\w/g, char => char.toUpperCase());
                        }
                
                        if (targetUppercase.includes(columnIndex)) {
                            col = col.toUpperCase(); // Convert to uppercase
                        }
                
                        if (columnIndex === targetTotalCharges) {
                            col = col.replace(/,/g, ''); // Remove commas
                        }
                
                        return col;
                    });
                
                    return columns.join(","); // Join modified columns
                });
  
                return data.join("\n"); // Reassemble CSV
            }
        }, 
        { 
            extend: 'excel',
            exportOptions: {
                // columns: "thead th:not(.notExport)"
            },
            customize: function (xlsx) {
                let sheet = xlsx.xl.worksheets['sheet1.xml'];
  
                // Convert Column B to Uppercase
                $('row:not(:nth-child(2)) c[r^="B"]', sheet).each(function () {
                    let cell = $(this).find('is t, v'); // Find the text inside
                    let text = cell.text().trim(); // Get the existing text
  
                    if (text) {
                        cell.text(text.toUpperCase()); // Convert to uppercase
                    }
                });
            }
        }, 
        // {
        //     extend: 'pdf',
        //     exportOptions: {
        //         columns: "thead th:not(.notExport)"
        //     },
        //     orientation: 'landscape',
        //     pageSize: 'LEGAL',
        //     customize: function (doc) {
        //         // Set dynamic widths for all columns
        //         let columnWidths = new Array(doc.content[1].table.body[0].length).fill('*');
  
        //         // Define custom widths for specific columns (adjust index as needed)
        //         columnWidths[1] = '20%';
  
        //         // Apply column widths
        //         doc.content[1].table.widths = columnWidths;
                
        //         // Loop through table body and target specific column
        //         doc.content[1].table.body.forEach(function (row, rowIndex) {
        //             if (rowIndex === 0) { return; } // Skip the header row
  
        //             let targetUppercase = [1, 6]; // Columns to make uppercase
        //             let targetCenter = [0, 2, 3, 4, 6]; // Columns to center align
        //             let targetRight = 5; // Column to right align
  
        //             row.forEach((cell, columnIndex) => {
        //               if (!cell.text) { return; }
  
        //               if (targetUppercase.includes(columnIndex)) {
        //                   cell.text = cell.text.toUpperCase();
        //               }
  
        //               if (targetCenter.includes(columnIndex)) {
        //                   cell.alignment = 'center';
        //               }
  
        //               if (columnIndex === targetRight) {
        //                   cell.alignment = 'right';
        //               }
        //             });
        //         });
        //     }
        // },
    ],
});

function itemDatatableActions(id, status) {
    let _actionButton = "";

    if (status === 'pending') {
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

    return _actionButton;
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
        url: baseUrl('hris/calendar/get_employee_information'),
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

$.validate({
    form : '#new_event_form',
    lang: 'en',
    onSuccess : function(form) {
        let formData = eventVue.participantData;
        formData.csrf_token = $("#csrf_token").val();
        formData.is_employee = $("#nonEmployeeToggle").prop("checked") ? 1 : 0;
        if(formData.is_employee == 1){
            formData.emp_id = empId; 
        }
        formData.event_id = eventsDetails.id;
        $.ajax({
            url: baseUrl('hris/calendar/save_participant'),
            type: "POST",
            dataType: "json",
            data: formData,
            // beforeSend: function() {
            //     $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            // },
            success: function(res) {
                $('#new_event_form')[0].reset();
                if(res.success){
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

$('#addNewParticipant').on('hidden.bs.modal', function () {
    $('#new_event_form')[0].reset();
});

function setParticipantsData(newData) {
    participantsTable.clear();             
    participantsTable.rows.add(newData);   
    participantsTable.draw();            
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
                url: baseUrl('hris/calendar/archive_participant'),
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
            url: baseUrl('hris/calendar/update_participant'),
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
                url: baseUrl('hris/calendar/confirm_participant'),
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
                url: baseUrl('hris/calendar/decline_participant'),
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

