let search_val = "";
let archive = 0;
let selectedYear = $('#year').val();
let position,referral, schools, courses;

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.position != "undefined" && _tempContentData.position.length > 0){ position = _tempContentData.position; }
    if(typeof _tempContentData.referral != "undefined" && _tempContentData.referral.length > 0){ referral = _tempContentData.referral; }
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

// const civilStatusOptions = [    
//     { id: 'single', text: 'SINGLE' },
//     { id: 'married', text: 'MARRIED' },
//     { id: 'separated', text: 'SEPARATED' },
//     { id: 'divorced', text: 'DIVORCED' },
//     { id: 'widowed', text: 'WIDOWED' },
//     { id: 'annulled', text: 'ANNULLED' },
//     { id: 'other', text: 'OTHER' }
// ];

// const genderOptions = [    
//     { id: 'male', text: 'MALE' },
//     { id: 'female', text: 'FEMALE' },
// ];

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
let yearthis = today.getFullYear();

let formatted = month + '/' + day + '/' + yearthis;
$('#applied_dt').val(formatted);

$("#referral").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    data: referral,
    allowClear: true,
});

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


// $("#civil_status").select2({
//     placeholder: 'SELECT AN OPTION',
//     width: '100%',
//     data: civilStatusOptions,
// });


// $("#gender").select2({
//     width: '100%',
//     placeholder: 'SELECT GENDER',
//     data: genderOptions,
// });

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
        $('#referral').val(null).trigger('change');
    }
});

let application_vue = new Vue({
    el: "#m_content",
    data: { 
        selectedApplication: {},
        educInfo : [],
        uploadedFiles : [],
    },
    methods: {
        formatDate(date) {
            return moment(date).format('MMMM D, YYYY');
        },
        renderRecruitment(recruitment) {
            const source = recruitmentSources.find(
                item => item.id == recruitment
            );
            return source ? source.text : '---';
        }
    }
});


let tblCandidates = $("#candidates_table").DataTable({
    dom: 'rtlip',
    serverSide: true,
    processing: true,
    searching: true,
    order: [[ 0, "desc" ]],
    rowId: "id",
    ajax: {
        url: baseUrl("crs/get_candidates/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
            d.year = selectedYear;
            d.archive = archive;
        }
    },
    columns: [
        {
            data: "id",
            visible: false,
        },
        {
            data: "applied_dt",
            render: function (data, type, row) {
                let content = `
                    Applied on: ${moment(data).format('MMM D, YYYY')}<br>
                    Recruitment: ${application_vue.renderRecruitment(row.recruitment)}`;
        
                if (row.recruitment === 'REFERRAL' && row.referral_name) {
                    content += `<br>Referral by: ${row.referral_name}`;
                }
        
                content += `<br>
                    <span class="badge badge-warning text-uppercase">${row.status || 'N/A'}</span>`;
        
                if (row.is_online == 1) {
                    content += ` <span class="badge badge-info text-uppercase">Applied Online</span>`;
                }
        
                return content;
            }
        },
        {
            data: "name", 
            render: function (data, type, row) {
                return `
                    <div>
                        <strong>${data}</strong><br>
                        ${row.contact_no || '-'}<br>
                       <div class="small">${row.email || '-'}</div>
                    </div>
                `;
            }
        },
        // {
        //     data: "contact_no",
        // },
        // {
        //     data: "schools", orderable: false,
        //     render: function (data) {
        //         return formatTag(data);
        //     }
        // },
        // {
        //     data: "courses", orderable: false,
        //     render: function (data) {
        //         return formatTag(data);
        //     }
        // },
        {
            data: "positions",
            orderable: false,
            render: function (data) {
                if (!data || data.length === 0) return "";
        
                let html = "<ul class='mb-0 ps-3'>";
                data.forEach(function(pos){
                    html += `<li>${pos}</li>`;
                });
                html += "</ul>";
        
                return html;
            }
        },
        // {
        //     data: "remarks", orderable: false,
        //     // render: function (data) {
        //     //     return "<div class='small'>" + data + "</div>"
        //     // }
        // },
        // {
        //     data: "recruitment", orderable: false,
        //     render: function (data, type, row) {
        //         if (data === "REFERRAL") {
        //             return `Referral by <br>${row.referral_name}`;
        //         }
        //         return data; // fallback if not "REFERRAL"
        //     }
        // },
        {
            data: "attachment",
            orderable: false,
            render: function (data, type, row) {
                if (!data) return "N/A";
        
                const filename = data;
                const displayName = filename;
                const resumeFilePath = `uploads/files/hrd/new_resume_${row.id}/${filename}`;
        
                return `
                    <div class="small">
                        <a href="../${resumeFilePath}" target="_blank" title="${displayName}">
                            ${displayName}
                        </a>
                    </div>
                `;
            }
        },
        {
            data: null,
            width: "10%",
            className: "text-center",
            orderable: false,
            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id, row.status);
            },
        },
    ]

});

function itemDatatableActions(id, status) {
    let _actionButton = "<span class='action-buttons'>";
    if (!archive) {
        _actionButton += `
                   <a style="text-decoration: none;" 
                        class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnView" 
                        onclick="onViewApplication(${id})" 
                        data-toggle="m-tooltip" data-placement="bottom" 
                        data-skin="dark" 
                        title="View Application">
                        <i class="la la-eye"></i>
                    </a>

                    <button 
                        type="button" 
                        class="btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnHire" 
                        onclick="hireApplication(${id})" 
                        data-toggle="m-tooltip" 
                        data-placement="bottom" 
                        title="Hire Applicant" 
                        data-skin="dark">
                        <i class="la la-check"></i>
                    </button>

                    <button 
                        type="button" 
                        class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnArchive" 
                        onclick="deleteApplication(${id})" 
                        data-toggle="m-tooltip" data-placement="bottom" title="Archive Application" 
                        data-skin="dark">
                        <i class="la la-file-archive-o"></i>
                    </button>
                    `;
    } else {
         _actionButton += `                  
                    <button 
                        type="button" 
                        class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnRestore" 
                        onclick="restoreApplication(${id})" 
                        data-toggle="m-tooltip" data-placement="bottom" title="Restore Application" 
                        data-skin="dark">
                        <i class="la la-undo"></i>
                    </button>`;
    }
    return _actionButton;
}


var currentYear = new Date().getFullYear();
for (var year = 2015; year <= currentYear; year++) {
    $('#year').append(new Option(year, year));
}
$('#year').val(currentYear).trigger('change');
$('#year').select2({
    placeholder: 'Year',
    width: '100%',
    allowClear: true,
}).on("select2:select", function (e) {
    selectedYear = $(this).val();
    tblCandidates.ajax.reload();
}).on("select2:unselecting", function (e) {
    selectedYear = "All";
    tblCandidates.ajax.reload();
});

$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblCandidates.ajax.reload();
});

function onViewApplication(id){
    let rowData = tblCandidates.row('#'+id).data();
    application_vue.selectedApplication = JSON.parse(JSON.stringify(rowData));
    $("#view-application-modal").modal("show");
}

function deleteApplication(id) {
    Swal.fire({
        title: 'Archive Application?',
        text: "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, archive it'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl("crs/delete_application"),
                type: "POST",
                global: false,
                dataType: "json",
                data: {
                    csrf_token: _csrf_hash,
                    id: id
                },

                beforeSend: function () {
                    Swal.showLoading();
                },
                success: function (res) {
                    Swal.close();
                    if (res.success) {
                        toastr.success(res.msg, 'Success', 3000);
                        tblCandidates.ajax.reload();
                    } else {
                        toastr.error(res.msg, 'Error', 3000);
                    }
                },
                error: function () {
                    toastr.error("Something went wrong!", 'Error', 3000);
                }
            });
        }
    });
}

function restoreApplication(id){

    Swal.fire({
        title: 'Restore Application?',
        text: "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, restore it'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl("crs/restore_application"),
                type: "POST",
                global: false,
                dataType: "json",
                data: {
                    csrf_token: _csrf_hash,
                    id: id
                },

                beforeSend: function () {
                    Swal.showLoading();
                },
                success: function (res) {
                    Swal.close();
                    if (res.success) {
                        toastr.success(res.msg, 'Success', 3000);
                        tblCandidates.ajax.reload();
                    } else {
                        toastr.error(res.msg, 'Error', 3000);
                    }
                },
                error: function () {
                    toastr.error("Something went wrong!", 'Error', 3000);
                }
            });
        }
    });

}

function openArchive(){
    archive = archive === 0 ? 1 : 0;
    if (archive === 1) {
        $('#page_title').text('ONLINE APPLICATION ARCHIVE');
        $('#archive_text').text('Back to Active');
        $('#newOption').hide();
    } else {
        $('#page_title').text('ONLINE APPLICATION MASTERFILE');
        $('#archive_text').text('Archive');
        $('#newOption').show();
    }
    tblCandidates.ajax.reload();
}

function hireApplication(id) {
    Swal.fire({
        title: 'Hire Applicant?',
        text: 'This will change the status of the application to HIRED.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, hire applicant'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = baseUrl("crs/online_application/hire/" + id);
        }
    });
}