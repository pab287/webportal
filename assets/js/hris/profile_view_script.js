let actions = _currentActions;
let session_id = $("#user_id").val();
let employeeData = _tempContentData.data.main;
let user_name = _tempContentData.data.user.display_name;
let id = employeeData.id;
let today = _tempContentData.data.timestamp;
const OFFENSE_TYPES = [
    'OFFENSE', '1ST OFFENSE', '2ND OFFENSE', '3RD OFFENSE', '4TH OFFENSE',
    '5TH OFFENSE', '6TH OFFENSE', '7TH OFFENSE', 'DISMISSAL',
    'WRITTEN WARNING', '3-DAYS SUSPENSION', '6-DAYS SUSPENSION', '1-2-DAYS SUSPENSION'
  ];
  
  const COMMENDATION_TYPES = ['COMMENDATION'];
  const NOTICE_TYPES = [
    'LAST WARNING', 'FINAL WRITTEN WARNING', 'VERBAL WARNING', 
    'RETURN TO WORK NOTICE', 'NTE', 'REMINDER NOTICE', 'NOD', 
    'NOTICE OF ADMINISTRATIVE', 'NOTICES', 'SUSPENSION'
  ];
  
  const OTHER_TYPES = [
    'OFFENSE', '1ST OFFENSE', '2ND OFFENSE', '3RD OFFENSE', '4TH OFFENSE',
    '5TH OFFENSE', '6TH OFFENSE', '7TH OFFENSE', 'COMMENDATION', 
    'LAST WARNING', 'FINAL WRITTEN WARNING', 'VERBAL WARNING', 
    'WRITTEN WARNING', 'RETURN TO WORK NOTICE', 'NTE', 'REMINDER NOTICE', 
    'NOD', 'DISMISSAL', 'NOTICE OF ADMINISTRATIVE HEARING', 'NOTICES',
    '3-DAYS SUSPENSION', '6-DAYS SUSPENSION', '1-2-DAYS SUSPENSION'
  ];
  
$(document).ready(function(){
    $('#column-options').on('click', function (e) {
        e.stopPropagation();
    });
});

let employeeDataSheet = new Vue({
    el:"#m-content",
    data:{ 
            activeTab: 'offenses',
            filteredOffenses: false,
            activeSection:"",
            main:[],
            supervisor:"",
            manager: "",
            path:"",
            dependents:[],
            job_desc:"",
            questions:[],
            educations:"",
            licensesAndCerts:{
                licenses:"",
                driverlicenses:"",
                if_driver:"",
            },
            works:[],
            awards:[],
            skillset:[],
            organizations: [],
            trainings:[],
            references:[],
            medicals:[],
            legals:[],
            accountability:[],
            offenses:[],
            salaries:[],
            stations:[],
            default_station:[],
            printData:{
                main : {},
                dependents:{},
                licensesAndCerts:{
                    licenses:"",
                    driverlicenses:"",
                    if_driver:"",
                },
                experiences:[],
                awards:[],
                skillset:[],
                organizations: [],
                trainings:[],
                references:[],
                medicals:[],
                legals:[],
                accountability:[],
                offenses:[],
                salaries:[],
                stations:[],
                default_station:[],
                return_to_work:[],
            }
    },
    created() {

    },
    mounted(){
        this.getSidebarData();
        getPerformanceRating(id);
        if (_tempContentData.tab == null){
            this.$data.activeSection = "personalInfo"
        }else{
            this.$data.activeSection =  _tempContentData.tab
        }
        switch (this.$data.activeSection) {
            case "personalInfo":
                this.getPersonalInformation();
                break;
            case "additionalInfo":
                this.getPersonalInformation();
                getAdditionalInformation();
                break;
            case "employmentQuestion":
                this.getPersonalInformation();
                break;
            case "educBackground":
                getEducationBackground();
                break;
            case "licenseAndCert":
                getLicenseAndCert();
                break;
            case "workExperience":
                getWorkExperience();
                break;
            case "employeeAwards":
                getAwardsAndAchievements();
                break;
            case "empSkills":
                getEmpSkills();
                break;
            case "empOrg":
                getEmpOrgs();
                break;
            case "empTrainings":
                getTrainingsAndSeminars();
                break;
            case "empPersonalReferences":
                getPersonalReferences();
                break;
            case "empMedicalHistory":
                getMedicalHistory();
                break;
            case "empLegalHistory":
                getLegalHistory();
                break;
            case "empAccountability":
                getAccountability();
                break;
            case "empEmploymentInfo":
                this.getPersonalInformation();
                getEmploymentInformation();
                break;
            case "jobDescription":
                getJobDescription();
                break;
            default:
             {
                this.$data.activeSection = "personalInfo"
                this.getPersonalInformation();
             }
        }
        if (this.main.more_questions && this.main.more_questions.length > 0) {
            this.questions = this.main.more_questions;
        }
        else{
            this.questions = _tempContentData.questions_list;
            this.questions.forEach(q => {
                if( this.main[`ques${q.id}`] == null ||  this.main[`ques${q.id}`] == undefined || this.main[`ques${q.id}`] == ""){
                    q.answer = "N/A"
                }else{
                    q.answer = this.main[`ques${q.id}`];
                }
            });
        }
    },

    methods:{
        filterOffenses(type) {
            this.activeTab = type;
            const offensesArray = Object.values(this.offenses);

            if (type === 'offenses') {
                this.filteredOffenses = offensesArray.filter(offense =>
                    OFFENSE_TYPES.includes(offense.offcom_type.toUpperCase())
                );
            } else if (type === 'commendations') {
                this.filteredOffenses = offensesArray.filter(offense =>
                    COMMENDATION_TYPES.includes(offense.offcom_type.toUpperCase())
                );
            } else if (type === 'notices') {
                this.filteredOffenses = offensesArray.filter(offense =>
                    NOTICE_TYPES.includes(offense.offcom_type.toUpperCase())
                );
            } else if (type === 'others') {
                this.filteredOffenses = offensesArray.filter(offense =>
                    !OTHER_TYPES.includes(offense.offcom_type.toUpperCase())
                );
            }
            if (this.filteredOffenses.length === 0) {
                this.filteredOffenses = false;
            }
        },
        getSidebarData(){
            this.main = { ...this.$data.main, ..._tempContentData.data.main };
            this.path = _tempContentData.data.path;
            this.supervisor = _tempContentData.data.supervisor;
            this.manager = _tempContentData.data.manager;
        },
        getDisplayName() {
            const { firstname, middlename, lastname, suffix } = this.main;
      
            let displayName1 = '';
            let displayName2 = '';
      
            const middleInitial = middlename && !['', 'N/A', 'NONE'].includes(middlename.toUpperCase()) 
              ? middlename.trim()[0].toUpperCase() + '.'
              : '';
      
            const formattedSuffix = suffix && !['', 'N/A', 'NONE'].includes(suffix.toUpperCase()) 
              ? suffix.toUpperCase()
              : '';
      
            if (middleInitial && formattedSuffix) {
              displayName1 = `${lastname}, ${firstname} ${middleInitial} ${formattedSuffix}`;
              displayName2 = `${firstname} ${middleInitial} ${lastname} ${formattedSuffix}`;
            } else if (formattedSuffix) {
              displayName1 = `${lastname}, ${firstname} ${formattedSuffix}`;
              displayName2 = `${firstname} ${lastname} ${formattedSuffix}`;
            } else if (middleInitial) {
              displayName1 = `${lastname}, ${firstname} ${middleInitial}`;
              displayName2 = `${firstname} ${middleInitial} ${lastname}`;
            } else {
              displayName1 = `${lastname}, ${firstname}`;
              displayName2 = `${firstname} ${lastname}`;
            }
      
            displayName1 = displayName1.toUpperCase();
            displayName2 = displayName2.toUpperCase();
      
            return {
              display_name_0: displayName1,
              display_name_1: displayName2
            };
        },
        getPersonalInformation(){
            if (!hasValue(this.main)) {
                this.main = { ...this.$data.main, ..._tempContentData.data.main };
            }
        },
        calculateAge(birthdate) {
            if (!birthdate || birthdate == '0000-00-00') return '---';
            const today = new Date();
            const birth = new Date(birthdate);
            let years = today.getFullYear() - birth.getFullYear();
            let months = today.getMonth() - birth.getMonth();
            let days = today.getDate() - birth.getDate();
            if (days < 0) {
                months--;
                const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                days += prevMonth.getDate();
            }
            if (months < 0) {
                years--;
                months += 12;
            }
            const parts = [];
    
            if (years > 0) {
                parts.push(`${years} ${years === 1 ? 'Year' : 'Years'}`);
            }
            
            if (months > 0) {
                parts.push(`${months} ${months === 1 ? 'Month' : 'Months'}`);
            }
            
            if (days > 0) {
                parts.push(`${days} ${days === 1 ? 'Day' : 'Days'}`);
            }
            
            if (parts.length === 0) return 'Less than a day old';
            return `${parts.join(' ')} old`;
        },
        displayName(lastname, firstname, middlename, suffix) {
            const middleInitial = middlename?.trim()?.[0]?.toUpperCase() || '';
            const formattedSuffix = suffix?.toLowerCase() === suffix?.toUpperCase() ? '' : suffix.toUpperCase();
          
            return [
              `${lastname.toUpperCase()}, ${firstname.toUpperCase()}${middleInitial}${formattedSuffix}`,
              `${firstname.toUpperCase()}${middleInitial} ${lastname.toUpperCase()}${formattedSuffix}`
            ];
        },
        formatDate(empdate) {
            if (!empdate || empdate == '0000-00-00') {
                return '---';
              }
            const date = new Date(empdate);
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: '2-digit', 
                year: 'numeric' 
              });
        },
        formatStartDate(empdate) {
            if (!empdate || empdate == '0000-00-00') {
                return 'N/A';
              }
            const date = new Date(empdate);
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: '2-digit', 
                year: 'numeric' 
              });
        },
        getExpirationClass(expirationDate) {
            const today = new Date();
            const expirationDateObj = new Date(expirationDate);
            return expirationDateObj > today ? 'm-badge--success' : 'm-badge--danger';
        },
        formatAmount(amount) {
            return new Intl.NumberFormat('en-PH', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            }).format(amount);
        },
        isReturned(acct) {
            return parseInt(acct.is_returned) == 1;
        },
        hasRemarks(acct) {
            return !!acct.remarks_returned
        },
        formatSalaryRate(rate) {
            if (!rate || rate == '') return 'NONE';

            const formattedRate = new Intl.NumberFormat('en-PH', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            }).format(parseFloat(rate.replace(',', '')));
            
            return formattedRate;
        },
        isCurrentSalary(salary, index) {
            const grandTotal = parseFloat(this.data.grandTotal);
            return salary.sal_rate == grandTotal && index == 0;
        },
        formattedJobDesc() {
            if (!this.job_desc) return '';
            
            // Create a temporary div to parse HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = this.job_desc;
            
            // Check if there are any li elements
            const hasListItems = tempDiv.getElementsByTagName('li').length > 0;
            
            // If it has list items, return the HTML as is
            // If not, convert newlines to <br>
            return hasListItems 
              ? this.job_desc 
              : this.job_desc.replace(/\n/g, '<br>');
        },
        formattedJobDescPrint(data) {
            if (!data) return '';
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data;
            const hasListItems = tempDiv.getElementsByTagName('li').length > 0;

            return hasListItems 
              ? data 
              : data.replace(/\n/g, '<br>');
        },
        changeSMS(e) {
            var val = $(e.target).is(':checked') ? 1 : 0;
            
            $.ajax({
                url: baseUrl('core/profile/allow_sms/') + id,
                data: { 
                    csrf_token: _csrf_hash,
                    allow: val
                },
                dataType: 'JSON',
                type: 'POST',
                success: function (response) {
                    if (response.state){
                        toastr.success(response.msg, 'SMS Notification', 5000);
                    } else {
                        toastr.error('Failed to Enable/Disable the SMS notification', 'SMS Notification', 5000);
                    }
                }
            });
        }
    }
})

$('#personalInfo-body, #collapsePersonal').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.main)) {
        employeeDataSheet.getPersonalInformation();
    }
})

$('#additionalInfo-body, #collapseAdditional').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.main) || !hasValue(employeeDataSheet.dependents)) {
        employeeDataSheet.getPersonalInformation();
        getAdditionalInformation();
    } 
});

$('#employmentQuestion-body, #collapseQuestion').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.main)) {
        employeeDataSheet.getPersonalInformation();
    }
});

$('#educBackground-body, #collapseEducation').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.educations)) {
        getEducationBackground();
    }
});

$('#licenseAndCert-body, #collapseLicense').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.licensesAndCerts)) {
        getLicenseAndCert();
    }
});

$('#workExperience-body, #collapseWork').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.works)) {
        getWorkExperience();
    }
});

$('#employeeAwards-body, #collapseAwards').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.awards)) {
        getAwardsAndAchievements();
    }
});

$('#empSkills-body, #collapseSkill').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.skillset)) {
        getEmpSkills();
    }
});

$('#empOrg-body, #collapseOrg').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.organizations)) {
        getEmpOrgs();
    }
});

$('#empTrainings-body, #collapseTrain').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.trainings)) {
        getTrainingsAndSeminars();
    }
});

$('#empPersonalReferences-body, #collapseRef').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.references)) {
        getPersonalReferences();
    }
});

$('#empMedicalHistory-body, #collapseMed').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.medicals)) {
        getMedicalHistory();
    }
});

$('#empLegalHistory-body, #collapseLegal').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.legals)) {
        getLegalHistory();
    }
});

$('#empAccountability-body, #collapseAccountability').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.accountability)) {
        getAccountability();
    }
});

$('#empEmploymentInfo-body, #collapseEmployment').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.main) || !hasValue(employeeDataSheet.offenses)) {
        employeeDataSheet.getPersonalInformation();
        getEmploymentInformation();
    } 
});

$('#jobDescription-body, #collapseEmployment').on('show.bs.collapse', function () {
    if (!hasValue(employeeDataSheet.job_desc)) {
        getJobDescription();
    }
})

function hasValue(obj) {
    return Object.values(obj).some(value => {
        if (value !== null && value !== undefined) {
            return Boolean(value);
        }
        return false;
    });
}

function getPerformanceRating(id){
    $("#performance-rating").starRating({
        readOnly: true, 
        totalStars: 5,
        starShape: 'rounded',
        starSize: 25,
        emptyColor: 'lightgray',
        hoverColor: '#FFC400',
        activeColor: '#FFAB00',
        ratedColor: '#FFAB00',
        useGradient: false,
        useFullStars: true,
      });

    $.ajax({
        url: baseUrl("core/profile/get_employee_rating_remarks/" + id),
        type: "post",
        dataType: "json",
        global: false,
        data: { csrf_token: _csrf_hash},
        success: function (response) {
            if(response){
                $("#performance-rating").starRating('setRating', response.rating);
                $("#performance-rating-description").html(response.description);
            }else{
                $("#performance-rating").starRating('setRating', 0);
                $("#performance-rating-description").html("");
            }
        }
    });
    
}

function showRemarks(remarks) {
    $('#remarksText').text(remarks);
    $('#remarksModal').modal('show');
}

function getAdditionalInformation(){
    $.ajax({
        url: baseUrl("core/profile/get_additional_info/")+id,
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function(response) {
            if (!response.dependents || Object.keys(response.dependents).length == 0) {
                employeeDataSheet.$data.dependents = false;
            } else {
                employeeDataSheet.$data.dependents = { ...employeeDataSheet.$data.dependents, ...response.dependents};
            }
        }
    });
}

function getEducationBackground(){
    $.ajax({
        url: baseUrl("core/profile/get_education_background/")+id,
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function(response) {
            if (!response.educations || Object.keys(response.educations).length == 0) {
                employeeDataSheet.$data.educations = false;
            } else {
                employeeDataSheet.$data.educations = { ...employeeDataSheet.$data.educations, ...response.educations 
                };
            }
        }
    });
}

function getLicenseAndCert(){
    $.ajax({
        url: baseUrl("core/profile/get_license_and_cert/")+id,
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function(response) {
            employeeDataSheet.$data.licensesAndCerts = { ...employeeDataSheet.$data.licensesAndCerts, ...response };
        }
    });
}

function getWorkExperience(){
    $.ajax({
        url: baseUrl("core/profile/get_work_experience/")+id,
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function(response) {
            if (!response.works || Object.keys(response.works).length == 0) {
                employeeDataSheet.$data.works = false;
            } else {
                employeeDataSheet.$data.works = { ...employeeDataSheet.$data.works, ...response.works };
            }
        }
    });
}


function getAwardsAndAchievements(){
    $.ajax({
        url: baseUrl("core/profile/get_awards_and_achievements/")+id,
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function(response) {
            if (!response || Object.keys(response.awards).length == 0) {
                employeeDataSheet.$data.awards = false;
            } else {
                employeeDataSheet.$data.awards = { ...employeeDataSheet.$data.awards, ...response.awards };
            }
        }
    });
}

function getEmpSkills(){
    $.ajax({
        url: baseUrl("core/profile/get_emp_skills/")+id,
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function(response) {
            if (!response || Object.keys(response.skillset).length == 0) {
                employeeDataSheet.$data.skillset = false;
            } else {
                employeeDataSheet.$data.skillset = { ...employeeDataSheet.$data.skillset, ...response.skillset };
            }
        }
    });
}

function getEmpOrgs(){
    $.ajax({
        url: baseUrl("core/profile/get_orgs/")+id,
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function(response) {
            if (!response || Object.keys(response.organizations).length == 0) {
                employeeDataSheet.$data.organizations = false;
            } else {
                employeeDataSheet.$data.organizations = { ...employeeDataSheet.$data.organizations, ...response.organizations };
            }
        }
    });
}

function getTrainingsAndSeminars(){
    $.ajax({
        url: baseUrl("core/profile/get_trainings_and_seminars/")+id,
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function(response) {
            if (!response || Object.keys(response.trainings).length == 0) {
                employeeDataSheet.$data.trainings = false;
            } else {
                employeeDataSheet.$data.trainings = { ...employeeDataSheet.$data.trainings, ...response.trainings };
            }
        }
    });
}


function getPersonalReferences(){
    $.ajax({
        url: baseUrl("core/profile/get_personal_references/")+id,
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function(response) {
            if (!response || Object.keys(response.references).length == 0) {
                employeeDataSheet.$data.references = false;
            } else {
                employeeDataSheet.$data.references = { ...employeeDataSheet.$data.references, ...response.references };
            }
        }
    });
}

function getMedicalHistory(){
    $.ajax({
        url: baseUrl("core/profile/get_medical_history/")+id,
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function(response) {
            if (!response || Object.keys(response.medicals).length == 0) {
                employeeDataSheet.$data.medicals = false;
            } else {
                employeeDataSheet.$data.medicals = { ...employeeDataSheet.$data.medicals, ...response.medicals };
            }
        }
    });
}

function getLegalHistory(){
    $.ajax({
        url: baseUrl("core/profile/get_legal_history/")+id,
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function(response) {
            if (!response || Object.keys(response.legals).length == 0) {
                employeeDataSheet.$data.legals = false;
            } else {
                employeeDataSheet.$data.legals = { ...employeeDataSheet.$data.legals, ...response.legals };
            }   
        }
    });
}

function getAccountability(){
    $.ajax({
        url: baseUrl("core/profile/get_accountability/")+id,
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function(response) {
            if (!response || Object.keys(response.accountability).length == 0) {
                employeeDataSheet.$data.accountability = false;
            } else {
                employeeDataSheet.$data.accountability = { ...employeeDataSheet.$data.accountability, ...response.accountability };
                $("#accountability_table_mobile").dataTable({
                    pageLength : 5,
                    bLengthChange : false,
                    data: Object.values(employeeDataSheet.$data.accountability),
                    columns:[
                        { data: 'status' },
                        { data: null, 
                            render: function(data, type, row) {

                                const formattedAmount = parseFloat(row.amount).toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                

                                const returnStatus = parseInt(row.is_returned) === 1 
                                    ? '<span class="m-badge m-badge--success px-2 m--font-bolder">Yes</span>'
                                    : '<span class="m-badge m-badge--danger px-2 m--font-bolder">No</span>';
                
     
                                const formattedDate = row.date_returned && row.date_returned !== "0000-00-00" 
                                    ? new Date(row.date_returned).toLocaleDateString('en-US', {
                                        month: 'short',
                                        day: 'numeric',
                                        year: 'numeric'
                                    })
                                    : 'N/A';
                
                                return `
                                    <div style="font-size: 0.8em">
                                        <p style="margin-bottom: 0.3rem"><b>Ref. No:</b> <span>${row.reference_no}</span></p>
                                        <p style="margin-bottom: 0.3rem"><b>Asset Code:</b> <span>${row.asset_code}</span></p>
                                        ${row.status.toLowerCase() == 'released' ? `
                                            <p style="margin-bottom: 0.3rem"><b>Date Released:</b> <span>${new Date(row.date_returned).toLocaleDateString('en-US', {
                                                month: 'short',
                                                day: 'numeric',
                                                year: 'numeric'
                                            })}</span></p>` : ''}
                                        <p style="margin-bottom: 0.3rem"><b>Asset Name:</b> <span>${row.aname}</span></p>
                                        <p style="margin-bottom: 0.3rem"><b>Amount:</b> <span>${formattedAmount}</span></p>
                                        <p style="margin-bottom: 0.3rem">
                                            <b>Returned:</b> ${returnStatus}
                                        </p>
                                        <p style="margin-bottom: 0.3rem"><b>Date:</b> <span>${formattedDate}</span></p>
                                    </div>
                                `;
                            }
                        }
                    ]
                });

            }
        }
    });
}

function getEmploymentInformation(){
    $.ajax({
        url: baseUrl("core/profile/get_employment_information/")+id,
        type: "post",
        data:{csrf_token: _csrf_hash,biono : employeeData.biometricno},
        dataType: "JSON",
        global: false,
        success: function(response) {
            if (actions.includes("view_own_request") && employeeDataSheet.$data.main.id !== session_id) {
                employeeDataSheet.$data.salaries = "not_allowed";
            }else{
                if (!response || Object.keys(response.salaries).length == 0) {
                    employeeDataSheet.$data.salaries = false;
                } else {
                    employeeDataSheet.$data.salaries = { ...employeeDataSheet.$data.salaries, ...response.salaries };
                }
            }
            if (!response || Object.keys(response.offenses).length == 0) {
                employeeDataSheet.$data.offenses = false;
            } else {
                employeeDataSheet.$data.offenses = { ...employeeDataSheet.$data.offenses, ...response.offenses };
                employeeDataSheet.$data.filteredOffenses = employeeDataSheet.$data.offenses;
                employeeDataSheet.filterOffenses('offenses');
            }

            if (!response || Object.keys(response.stations).length == 0) {
                employeeDataSheet.$data.stations = false;
            } else {
                employeeDataSheet.$data.stations = { ...employeeDataSheet.$data.stations, ...response.stations };
            }

            if (!response || response.default_station == null) {
                employeeDataSheet.$data.default_station = false;
            } else {
                employeeDataSheet.$data.default_station = { ...employeeDataSheet.$data.default_station, ...response.default_station };
            }

        }
    });
}

function getJobDescription(){
    $.ajax({
        url: baseUrl("core/profile/get_job_description/")+employeeDataSheet.$data.main.position_id,
        type: "post",
        data:{csrf_token: _csrf_hash},
        dataType: "JSON",
        global: false,
        success: function(response) {            
            if (!response || response.job_desc == null) {
                employeeDataSheet.$data.job_desc = false;
            } else {
                employeeDataSheet.$data.job_desc =  response.job_desc;
            }
        }
    });
}

function printFetch(){
    $.ajax({
        url: baseUrl("core/profile/get_print_data/")+id,
        type: "post",
        data:{csrf_token: _csrf_hash},
        dataType: "JSON",
        global: false,
        success: function(response) {
            if(response){
                employeeDataSheet.$data.printData = { ...employeeDataSheet.$data.printData, ...response.data };
                if (actions.includes("view_own_request") && employeeDataSheet.$data.printData.main.id !== session_id) {
                    employeeDataSheet.$data.printData.salaries = "not_allowed";
                }
                Vue.nextTick(() => {
                    printEmployeeDataSheet(employeeDataSheet.path, employeeDataSheet.$data.printData, user_name ,today)
                });
            }
        }
    })
}

function printEmployeeDataSheet(avatar, info, user, timestamp) {
    const divToPrint = $(".data-sheet").html();
    const newWin = window.open('', 'Print-Employee Data Sheet');
    const style1 = baseUrl("assets/css/responsiveTable.css");

    const bootstrapRowCol = `.row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px; }
        .col-1, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-10, .col-11, .col-12, .col,
        .col-auto, .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm,
        .col-sm-auto, .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12, .col-md,
        .col-md-auto, .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg,
        .col-lg-auto, .col-xl-1, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl,
        .col-xl-auto {
        position: relative;
        width: 100%;
        min-height: 1px; }

        .col {
        flex-basis: 0;
        flex-grow: 1;
        max-width: 100%; }

        .col-auto {
        flex: 0 0 auto;
        width: auto;
        max-width: none; }

        .col-1 {
        flex: 0 0 8.33333%;
        max-width: 8.33333%; }

        .col-2 {
        flex: 0 0 16.66667%;
        max-width: 16.66667%; }

        .col-3 {
        flex: 0 0 25%;
        max-width: 25%; }

        .col-4 {
        flex: 0 0 33.33333%;
        max-width: 33.33333%; }

        .col-5 {
        flex: 0 0 41.66667%;
        max-width: 41.66667%; }

        .col-6 {
        flex: 0 0 50%;
        max-width: 50%; }

        .col-7 {
        flex: 0 0 58.33333%;
        max-width: 58.33333%; }

        .col-8 {
        flex: 0 0 66.66667%;
        max-width: 66.66667%; }

        .col-9 {
        flex: 0 0 75%;
        max-width: 75%; }

        .col-10 {
        flex: 0 0 83.33333%;
        max-width: 83.33333%; }

        .col-11 {
        flex: 0 0 91.66667%;
        max-width: 91.66667%; }

        .col-12 {
        flex: 0 0 100%;
        max-width: 100%; }
    `;
    const _document = '' +
        '<html>' +
        '   <head>' +
        '       <link rel="stylesheet" href="' + style1 + '" />' +
        '       <style>' +
        '           @media print {' +
        '               td {' +
        '                   text-transform: uppercase;' +
        '               }' +
        '               th {' +
        '                   text-align: left;' +
        '               }' +
        '               td[scope], th[scope] {' +
        '                   background-color: #f8f8f8;' +
        '                   -webkit-print-color-adjust: exact;' +
        '               }' +
        '               .avatar {' +
        '                   position: relative;' +
        '                   background-image: url("' + avatar + '");' +
        '                   width: 192px;' +
        '                   height: 192px;' +
        '                   background-size: cover;' +
        '                   background-position: center;' +
        '                   background-repeat: no-repeat;' +
        '                   overflow: hidden;' +
        '                   border-radius: 6px;' +
        '                   -webkit-print-color-adjust: exact;' +
        '               }' +
        '               table { page-break-inside: auto; }' +
        '               tr, td, th, { page-break-inside: avoid; page-break-after: auto; }' +
        '           }' +
        '' +
        '           td {' +
        '               text-transform: uppercase;' +
        '           }' +
        '           th {' +
        '               text-align: left;' +
        '           }' +
        '           body * {' +
        '               font-family: Arial, Helvetica, sans-serif;' +
        '               font-size: 12px;' +
        '           }' +
        '           img {' +
        '               width: 192px;' +
        '               border: 1px solid #dedede;' +
        '               border-radius: 6px;' +
        '           }' +
        '           .title {' +
        '               font-size: 18px;' +
        '               margin: 0;' +
        '           }' +
        '           th[scope], td[scope] {' +
        '               background: #f8f8f8;' +
        '           }' +
        '           .avatar {' +
        '               position: relative;' +
        '               background-image: url(' + avatar + ');' +
        '               width: 192px;' +
        '               height: 192px;' +
        '               background-size: cover;' +
        '               background-position: center;' +
        '               background-repeat: no-repeat;' +
        '               overflow: hidden;' +
        '               border-radius: 6px;' +
        '           }' +
        '           .m--hidden-desktop {' +
        '               display: none;' +
        '           }' +
        '           #hide-in-print {' +
        '               display: none;' +
        '           }' + bootstrapRowCol +
        '       </style>' +
        '   </head>' +
        '   <body onload="window.print()">' +
        '       <table border="0" collspan="0" cellpadding="0" cellspacing="0" width="100%"' +
        '              style="margin-bottom: 24px;">' +
        '           <tr valign="bottom">' +
        '               <td width="90%">' +
        '                   <h1 class="title">EMPLOYEE DATA SHEET</h1>' +
        '               </td>' +
        '               <td>' +
        '                   <div style="display: block;">' +
        '                       <div class="avatar"></div>' +
        '                       <p style="margin: 8px 0 0; font-size: 14px;">ID No.: ' + info.main.idno + '</p>' +
        '                       <p style="margin: 2px 0 0; font-size: 14px;">Biometric No.: ' + info.main.biometricno + '</p>' +
        '                   </div>' +
        '               </td>' +
        '           </tr>' +
        '       </table>' +
        '       ' + divToPrint +
        '       <table border="0" collspan="0" cellpadding="0" cellspacing="0" width="100%">' +
        '           <tr valign="bottom">' +
        '               <td style="line-height: 20px; text-align: justify;" colspan="2">' +
        '                   I AUTHORIZE THE COMPANY TO INVESTIGATE ALL INFORMATION CONTAINED IN THIS APPLICATION. I UNDERSTAND AND THAT ANY' +
        '                   MISREPRESENTATION OR OMISSION OF FACTS CALLED FOR IN THIS APPLICATION SHALL BE CONSIDERED CAUSE FOR ANY DISMISSAL.' +
        '               </td>' +
        '           </tr>' +
        '           <tr>' +
        '               <td width="50%" align="center">' +
        '                   <div style="width: 40%; border-top: 1px solid grey; margin-top: 48px;">DATE</div>' +
        '               </td>' +
        '               <td width="50%" align="center">' +
        '                   <div style="width: 70%; border-top: 1px solid grey; margin-top: 48px;">SIGNATURE OVER PRINTED NAME</div>' +
        '               </td>' +
        '           </tr>' +
        '           <tr>' +
        '               <td colspan="2">' +
        '                   <div style="margin-top: 48px;">' +
        '                       <i><b>Printed by: </b><span style="text-transform: capitalize;">' + user.toLowerCase() + ';</span> <b>Date & Time:</b> ' + timestamp + '</i>' +
        '                   </div>' +
        '               </td>' +
        '           </tr>' +
        '       </table>' +
        '   </body' +
        '</html>';
    newWin.document.open();
    newWin.document.write(_document);
    newWin.document.close();
    setTimeout(function () {
        newWin.close();
    }, 1500);
}

$('#offense-tabs .nav-link').on('click', function(e) {
    e.preventDefault();
    $('#offense-tabs .nav-link').removeClass('active');
    $('#offense-content .tab-pane').removeClass('active show');
    $(this).addClass('active');
    var targetId = $(this).attr('href');
    $(targetId).addClass('active show');
 });