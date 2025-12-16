var tableEmployeeList = $("#table-employee");
var tableEmployeeMobileList = $("#table-mobile-employee");
var tableDependents = $("#tbl-dependents_list");
var tableEducationalBackground = $("#tbl-educational_background_list");
var tableLicensure = $("#tbl-licensure_exams_list");
var tableDriverLicense = $("#tbl-driverlicense");
var tableWorkExperience = $("#tbl-work_experiences_list");
var tableAwards = $("#tbl-awards_list");
var tableOrganization = $("#tbl-organizations_list");
var tableTrainings = $("#tbl-trainings_list");
var tablePersonalReference = $("#tbl-personal_references_list");
var tableMedicalHistory = $("#tbl-medical_records_list");
var tblSkills = $("#tbl-skills-list");
var tblSalaryHistory = $("#tbl-salary-history");
var tblAccountability = $("#tbl-accountability");
var tblPerformanceRating = $("#tbl-performance-rating");
var tblReturnToWork = $("#tbl-return-to-work");

var tableLegalHistory = $("#tbl-legal_history_list");
var tableOffenses = $("#tbl-offenses_list");
var tableCommendation = $("#tbl-commendation_list");
var tableNotices = $("#tbl-notices_list");
var tableOthers = $("#tbl-others_list");
var tableCashAdvance = $("#tbl-cash_advance_list");
var tableDocuments = $("#tbl-documents_list");
var tableBackgroundCheck = $("#tbl-background_check_list");
var tablePerformance = $("#tbl-performance_eval_list");

var currentJobDescription = $("#job_description-content");
var questionsDescription = $("#questions-content");

var modalTempContent = $("#modalTempContent");

var mcompany = $("#m--input-company_id");
var dtEmployee = null;
let dtPerformanceRating = null;
var dtReturnToWork = null;
var companyExceptCurrent, tempData;
var tempDataId = 0;
let questions_list = [];
let clickedView = 'grid';
let vmTabUpdateQuestions = null;
var tableEmployeeGrid = $("#table-employee-grid");

var modalTempContentLg = modalTempContent.clone().prop("id", "modalTempContentLg").appendTo(".m-content");
modalTempContentLg.find(".modal-dialog").addClass("modal-lg");

const changeEmployeeCompanyDialog = $("#change-employee-company-dialog");

const classificationDropdown = $('select[name="employee_status"]');
const status = $('select[name="work_status"]');
let dtWorkExperience = null;
let currentResignDate = "";
let currentClassification = "";

loadEmployees();
let selectedTable="";
let _user = [];
let acctgStatus = 2; // assigned as 2 to not trigger the 0 is_returned status to the first trigger of datatable;
let exported_acctg = null;

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.data !== "undefined" && _tempContentData.data){ _user = _tempContentData.data; }
}


const shouldEnableProbationEndDate = (vmData) => {
    const startDate = vmData.date_start;
    const endDate = vmData.date_end_prob;
    const regularDate = vmData.date_regular;

    if (regularDate && (!endDate || endDate === "0000-00-00")) {
        return true;
    }

    if (startDate && endDate) {
        const startDateTime = new Date(startDate).getTime();
        const endDateTime = new Date(endDate).getTime();
        return startDateTime > endDateTime;
    }

    return false;
};

$("body")
    .tooltip({
        selector: "[data-toggle='m-tooltip']"
    });

$("#emp_status")
    .select2({
        placeholder: "SELECT AN EMPLOYEE STATUS",
        width: "100%",
        templateResult: formatOutput
    })
    .on("select2:select", function (e) {
        loadEmployees();
    });

$("#emp_sex")
    .select2({
        placeholder: "SELECT SEX",
        width: "100%",
        templateResult: formatOutput
    })
    .on("select2:select", function (e) {
        loadEmployees();
    });

function formatOutput(optionElement) {
    if (!optionElement.id) {
        return optionElement.text;
    }
    var $state = $('<span class="m--font-boldest">' + optionElement.text + '</span>');
    return $state;
}

function loadEmployees(employee_status = "All") {
    if(window.innerWidth > 480){
        if (typeof tableEmployeeList !== "undefined") {
            var search_val = "";

            dtEmployee = tableEmployeeList.DataTable({
                dom: '<"toolbar">frtlip',
                serverSide: true,
                processing: true,
                searching: false,
                ordering: false,
                destroy: true,
                ajax: {
                    url: baseUrl("hris/masterfile/get_employee_datatable_request/" + employee_status),
                    type: "post",
                    dataType: "json",
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
                        d.search['value'] = $("#generalSearch").val();
                        d.emp_status = $("#emp_status").val();
                        d.emp_sex = $("#emp_sex").val();
                        let arrPrivileges = [];
                        if(typeof _currentActions !== "undefined" && Object.keys(_currentActions).length > 0 && _currentActions.includes("view_by_dept")){
                            arrPrivileges.push('by_department');
                        }
                        if(typeof _currentActions !== "undefined" && Object.keys(_currentActions).length > 0 && _currentActions.includes("view_by_company")){
                            arrPrivileges.push('by_company');
                        }

                        if(arrPrivileges.length > 0){
                            d.list_view = arrPrivileges;
                        }

                        return d;
                    },
                    global: false,
                },
                columns: [
                    {
                        data: "image",
                        width: "5%",
                        className: "text-center"
                    },
                    { data: "name" },
                    {
                        data: "rating",
                        width: "15%",
                    },
                    {
                        data: "status_201",
                        width: "10%",
                        render: function (data) {
                            const badgeClass = data.toLowerCase() === "incomplete" ? "m-badge--danger" : "m-badge--success";
                            return `<span class="m-badge m-badge--wide m--font-boldest ${badgeClass}">${data}</span>`;
                        }
                    },
                    {
                        data: "work_status",
                        width: "10%",
                        render: function (data) {
                            return `<span class="m--font-boldest">${data}</span>`;
                        }
                    },
                    {
                        data: "date_start",
                        width: "10%",
                        render: function (data) {
                            return `<span class="m--font-boldest">${data}</span>`;
                        }
                    },
                    { data: null, width: "10%", className: "text-center" }
                ],
                columnDefs: [
                    {
                        data: "image",
                        targets: 0,
                        render: function (data, type, row, meta) {
                            var _html = "";

                            const ids = row.id;
                            $("#table-employee.grid tbody td:first-child").addClass('btnViewEmployee201').prop('data-id', ids);
                            
                            _html += '<div id="details" style="padding: 10px">';
                                _html += '<div id="grid">';
                                    _html += '<div class="col1 text-left">';
                                        _html += "<div class='m-card-profile__pic-wrapper mb-2'>"
                                        + "<img class='m--img-rounded m--marginless m--img-centered user__pic' src='" + data + "' />"
                                        + "</div>";
                                        _html += row.name;
                                    _html += '</div>';

                                    _html += '<div class="col2 text-left">';
                                        _html += '<p class="m-0"><small><b>Gender:</b> ' + row.gender + '</small></p>';
                                        _html += '<p class="m-0"><small><b>Address:</b> ' + row.address + '</small></p>';
                                        _html += '<p class="m-0"><small><b>Contact No:</b> ' + row.contact + '</small></p>';
                                        _html += '<p class="m-0 mb-3"><small><b>Email:</b> ' + row.email + '</small></p>';
                                    _html += '</div>';
                                _html += '</div>';

                                _html += '<div id="list">';
                                    _html += "<div class='m-card-profile__pic-wrapper'>"
                                        + "<img class='m--img-rounded m--marginless m--img-centered user__pic' src='" + data + "'/>"
                                        + "</div>";
                                _html += '</div>';
                            _html += '</div>';

                            return _html;
                        },
                        createdCell: function(td, cellData, rowData, row, col){
                            $(td).attr('data-id', rowData.id);
                        }
                    },
                    {
                        data: null,
                        defaultContent: "",
                        targets: -1,
                        orderable: false,
                        render: function (data, type, row, meta) {
                            return employeeDataTableActions(row.id);
                        }
                    },
                    {
                        targets: "_all",
                        defaultContent: ""
                    }
                ],
                drawCallback: function () {
                    const data = dtEmployee.data();
                    setPerformanceRating(data);

                    if(clickedView == 'grid'){
                        $("#table-employee tbody td #details #grid .custom-fullname a").removeAttr('target');
                        $("#table-employee.grid tbody td:first-child").addClass('btnViewEmployee201');
                    }

                    if (data.length == 0) {
                        $("#table-employee.grid .grid").css('justify-content', 'center');
                    } else {
                        $("#table-employee.grid .grid").css('justify-content', 'flex-start');
                    }
                },
                initComplete: function () {
                    $(document)
                        .on("click", ".emp-performance-rating", function () {
                            const empId = $(this).attr('data-emp');
                            $.ajax({
                                url: baseUrl('hris/masterfile/get_employee_rating_remarks/' + empId),
                                type: "GET",
                                dataType: "JSON",
                                global: false,
                                success: function (response) {
                                    if (response) {
                                        const _modal = $("#modal-performance-rating-remarks");
                                        $("#performance-rating-in-remarks", _modal).starRating('setRating', response.rating);
                                        $("#employee", _modal).val(response.fullname);
                                        $("#employee-container").show();
                                        $("#scale-description", _modal).html(response.description);
                                        $("#purpose", _modal).val(response.purpose);
                                        $("#remarks", _modal).val(response.remarks);
                                        _modal.modal("show");
                                    }
                                }
                            });
                        });
                }
            });
        }
    }else{
        if(typeof tableEmployeeMobileList !== 'undefined'){
            var search_val = "";
            dtEmployee = tableEmployeeMobileList.DataTable({
                dom: '<"toolbar">frtlip',
                serverSide: true,
                processing: true,
                searching: false,
                ordering: false,
                destroy: true,
                ajax: {
                    url: baseUrl("hris/masterfile/get_employee_datatable_request/" + employee_status),
                    type: "post",
                    dataType: "json",
                    data: function (d) {
                        d.csrf_token = _csrf_hash;
                        d.search['value'] = $("#generalSearch").val();
                        d.emp_status = $("#emp_status").val();
                        d.emp_sex = $("#emp_sex").val();
                        return d;
                    },
                    global: false,
                },
                columns: [
                    {
                        data: "image",
                        width: "15%",
                        className: "text-center"
                    },
                    { data: "name", width: "*" },
                ],
                columnDefs: [
                    {
                        data: "image",
                        targets: 0,
                        render: function (data, type, row, meta) {
                            var _html =
                                "<div clas='m-card-profile__pic-wrapper'><img class='m--img-rounded m--marginless m--img-centered user__pic' src='" +
                                data +
                                "' /></div>";
                            return _html;
                        }
                    },
                    {
                        data: "name",
                        targets: 1,
                        render: function (data, type, row, meta){
                            var html = "";

                            html += data;
                            html += '<span style="margin: 5px 0 0 0;"></span>';
                            html += row.rating;
                            html += '<small><p style="margin: 0">'+row.work_status+'</p></small>';
                            html += '<small><p style="margin: 0">'+row.date_start+'</p></small>';

                            return html;
                        }
                    }
                ],
                drawCallback: function () {
                    const data = dtEmployee.data();
                    setPerformanceRating(data);
                },
                initComplete: function () {
                    $(document)
                        .on("click", ".emp-performance-rating", function () {
                            const empId = $(this).attr('data-emp');
                            $.ajax({
                                url: baseUrl('hris/masterfile/get_employee_rating_remarks/' + empId),
                                type: "GET",
                                dataType: "JSON",
                                global: false,
                                success: function (response) {
                                    if (response) {
                                        const _modal = $("#modal-performance-rating-remarks");
                                        $("#performance-rating-in-remarks", _modal).starRating('setRating', response.rating);
                                        $("#employee", _modal).val(response.fullname);
                                        $("#employee-container").show();
                                        $("#scale-description", _modal).html(response.description);
                                        $("#purpose", _modal).val(response.purpose);
                                        $("#remarks", _modal).val(response.remarks);
                                        _modal.modal("show");
                                    }else{
                                        
                                    }
                                }
                            });
                        });
                }
            });

            tableEmployeeMobileList.on('click', 'tbody tr', function(){
                var rowId = dtEmployee.row(this).data().id;

                window.open(baseUrl('hris/masterfile/view_employee_masterfile/') + rowId);
            });
        }
    }

    function setPerformanceRating(data) {
        dtEmployee.rows()
            .iterator('row', function (context, index) {
                const node = $(this.row(index).node());
                const ratingEl = node.find("#rating-" + data[index].id);
                const value = ratingEl.attr("data-value");
                $(ratingEl)
                    .starRating({
                        totalStars: 5,
                        starShape: 'rounded',
                        starSize: 20,
                        emptyColor: 'lightgray',
                        hoverColor: 'salmon',
                        activeColor: '#FFAB00',
                        useGradient: false,
                        initialRating: value,
                        readOnly: true
                    });
            });
    }

    function employeeDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            if (jQuery.inArray("edit", _currentActions) !== -1) {
                _actionButton +=
                    " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditEmployee' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='Edit Employee Record' data-id='" + $id + "'><i class='la la-edit'></i></button>";
            }
            if (jQuery.inArray("archive", _currentActions) !== -1) {
                _actionButton +=
                    " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveEmployee btnDelete' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='Archive Employee Record' data-id='" +
                    $id +
                    "'><i class='la la-file-archive-o'></i></button>";
            }
            if (jQuery.inArray("view", _currentActions) !== -1) {
                _actionButton +=
                    " <button type='button' class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnViewEmployee201 btnView' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='View Employee 201 File' data-id='" +
                    $id +
                    "'><i class='la la-file-text'></i></button>";
            }
            _actionButton = (_actionButton) ? _actionButton : "---";
            return _actionButton;
        } else {
            return false;
        }
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtEmployee.ajax.reload();
    });

    $(document).on("click", ".btnNewEmployee", function () {
        window.location.href = baseUrl("hris/masterfile/add_employee_masterfile");
    });

    $(document).on("click", ".btnEditEmployee", function () {
        var dataId = $(this).data("id");
        window.location.href = baseUrl("hris/masterfile/edit_employee_masterfile/" + dataId);
    });

    $(document).on("click", ".btnViewEmployee201", function () {
        var dataId = $(this).data("id");
        window.location.href = baseUrl("hris/masterfile/view_employee_masterfile/" + dataId);
    });

}

if (typeof _tempContentData !== "undefined") {
    tempData = _tempContentData.data;
    questions_list = _tempContentData.questions_list ? _tempContentData.questions_list : [];
    tempDataId = (typeof tempData.id !== "undefined" && tempData.id) ? tempData.id : 0;
    var tempDropdownData = _tempContentData.dropdown_data;

    var displayEmail = tempData.display_email ? tempData.display_email : "";
    var displayName = tempData.display_name
        ? tempData.display_name
        : "No Employee Name";
    var displayAvatar = tempData.pic_filename;

    var leftPanel = new Vue({
        el: "#left_pane-card",
        data: {
            left_pane: {
                display_name: displayName,
                display_email: displayEmail,
                display_avatar: displayAvatar
            }
        }
    });

    var vmTab1 = new Vue({
        el: "#frmEditEmployeeData",
        data: { vm_tab1: tempData }
    });

    var vmTab2 = new Vue({
        el: "#frmEditAdditionalData",
        data: { vm_tab2: tempData },
        mounted: function () {
            var vmData = this.vm_tab2;
            var sssNo = vmData.sss_no;

            var self = $(this.$el);
            var umidSwitch = self.find("#umid_no");
            umidSwitch.prop("checked", false);

            if (typeof sssNo !== "undefined" && sssNo !== null && sssNo !== "") {
                var tempSplitSssNo = sssNo.split("-", 3);
                var firstSplit = tempSplitSssNo[0].length;
                if (firstSplit > 2) {
                    umidSwitch.prop("checked", true);
                }
            }

            var tinNumberMask = self.find("#tin_no");
            var philhealthMask = self.find("#phealth_no");
            var pagibigMask = self.find("#pagibig_no");
            var sssNoMask = self.find("#sss_no");

            tinNumberMask.inputmask("mask", { "mask": "999-999-999" });
            philhealthMask.inputmask("mask", { "mask": "99-999999999-9" });
            pagibigMask.inputmask("mask", { "mask": "9999-9999-9999" });
            sssNoMask.inputmask("mask", { "mask": "99-9999999-9" });

            setTimeout(function () {
                umidSwitch.trigger("change");
            }, 500);

            const _formPersonalInformation = $("form#frmEditEmployeeData");
            const _formAdditionalInformation = $("form#frmEditAdditionalData");
            const radioPtSingle = _formAdditionalInformation.find("#pt_single");
            const radioPtMarried = _formAdditionalInformation.find("#pt_married");
            const radioPtPartner = _formAdditionalInformation.find("#pt_partner");
        
        }, methods: {
            dateFormat(str){
                return (str) ? moment(str).format('LLL') : "No added Date";
            }
        }
            
        
    });
    var vmTab3 = new Vue({
        el: "#frmEditEmploymentData",
        data: { vm_tab3: tempData, multiple_position: [] },
        mounted: function () {
            var vmData = this.vm_tab3;
            currentResignDate = JSON.stringify(vmData.resignation_effective_date);
            currentClassification = vmData.employee_status;
            const employee_status = vmData.employee_status ? vmData.employee_status.toLowerCase() : "";
            const work_status = vmData.work_status ? vmData.work_status.toLowerCase() : "";
            const activateRehireStatuses = ["inactive", "resign",
                "terminated", "awol", "blacklisted",
                "black listed", "end of contract", "retired"];
            if (activateRehireStatuses.includes(employee_status.toLowerCase()) || activateRehireStatuses.includes(work_status.toLowerCase())) {
                // $("#rehire-button-container").removeClass("m--hide");
                $("#rehire-button-container-employment-data").removeClass("m--hide");
            } else {
                // $("#rehire-button-container").addClass("m--hide");
                $("#rehire-button-container-employment-data").addClass("m--hide");
            }

            let _data = this.excludeEmployee(tempDropdownData.dropdown_supervisory, vmData.supervisor); //excluded supervisor in managerial dropdown
            _data = vmData.supervisor != 0 ? _data : tempDropdownData.dropdown_supervisory;
            let _supData = vmData.manager && vmData.manager != 0 ? this.excludeEmployee(tempDropdownData.dropdown_supervisory, vmData.manager) : tempDropdownData.dropdown_supervisory;

            const activeStatusOptions = '' +
                '<option value=""></option>' +
                '<option ' + (vmData.work_status === 'REGULAR' ? 'selected' : '') + ' value="REGULAR">REGULAR</option>' +
                '<option ' + (vmData.work_status === 'PROBATIONARY' ? 'selected' : '') + '  value="PROBATIONARY">PROBATIONARY</option>' +
                '<option ' + (vmData.work_status === 'NO CONTRACT' ? 'selected' : '') + '  value="NO CONTRACT">NO CONTRACT</option>' +
                '<option ' + (vmData.work_status === 'RETIRED' ? 'selected' : '') + '  value="RETIRED">RETIREE</option>' +
                '<option ' + (vmData.work_status === 'CONSULTANT' ? 'selected' : '') + '  value="CONSULTANT">CONSULTANT/RETAINER</option>' +
                '<option ' + (vmData.work_status === 'PART-TIME' ? 'selected' : '') + '  value="PART-TIME">PART-TIME</option>' +
                '<option ' + (vmData.work_status === 'PROJECT BASED' ? 'selected' : '') + '  value="PROJECT BASED">PROJECT BASED</option>';

            const inactiveStatusOptions = '' +
                '<option value=""></option>' +
                '<option ' + (vmData.work_status === 'RESIGNED' ? 'selected' : '') + ' value="RESIGNED">RESIGNED</option>' +
                '<option ' + (vmData.work_status === 'RETIRED' ? 'selected' : '') + '  value="RETIRED">RETIRED</option>' +
                '<option ' + (vmData.work_status === 'TERMINATED' ? 'selected' : '') + ' value="TERMINATED">TERMINATED</option>' +
                '<option ' + (vmData.work_status === 'BLACKLISTED' ? 'selected' : '') + ' value="BLACKLISTED">BLACKLISTED</option>' +
                '<option ' + (vmData.work_status === 'END OF CONTRACT' ? 'selected' : '') + ' value="END OF CONTRACT">END OF CONTRACT</option>' +
                '<option ' + (vmData.work_status === 'INDEFINITE LEAVE' ? 'selected' : '') + ' value="INDEFINITE LEAVE">INDEFINITE LEAVE</option>'+
                '<option ' + (vmData.work_status === 'OTHERS' ? 'selected' : '') + ' value="OTHERS">OTHERS</option>';

            const contractorStatusOption = '' +
                '<option value=""></option>' +
                '<option ' + (vmData.work_status === '"N/A' ? 'selected' : '') + ' value="N/A" selected>N/A</option>';

            const status = $('#status');
            status.find('option').remove();

            // OLD FUNCTION
            // if (vmData.employee_status.toLowerCase() === 'active') {
            //     status.append(activeStatusOptions);
            // } else if (vmData.employee_status.toLowerCase() === 'inactive') {
            //     status.append(inactiveStatusOptions);
            // } else {
            //     status.append(contractorStatusOption);
            //     status.attr('readonly');
            // }

            // NEW FUNCTION
            if(vmData.employee_status) {
                if (vmData.employee_status.toLowerCase() === 'active') {
                    status.append(activeStatusOptions);
                } else if (vmData.employee_status.toLowerCase() === 'inactive') {
                    status.append(inactiveStatusOptions);
                } else {
                    status.append(contractorStatusOption);
                    status.attr('readonly');
                }
            }
            else {
                status.append(contractorStatusOption);
                status.attr('readonly');
            }

            $("#m--input-company_id")
                .select2({
                    data: tempDropdownData.dropdown_company,
                    placeholder: {
                        id: "-1",
                        text: "Select an option"
                    },
                    width: '100%'
                })
                .val(vmData.company_id)
                .trigger("change")
                .on("select2:select", function (e) {
                    const data = e.params.data;
                    vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { company_id: data.id });
                });

            $('#change-company-company_id')
                .select2({
                    data: tempDropdownData.dropdown_company,
                    placeholder: {
                        id: "-1",
                        text: "Select a Company"
                    },
                    width: '100%',
                    dropdownParent: $("#change-employee-company-dialog")
                })
                .val(-1)
                .trigger("change");

            $("#m--input-department_id")
                .select2({
                    data: tempDropdownData.dropdown_department,
                    placeholder: {
                        id: "-1",
                        text: "Select an option"
                    },
                    width: '100%'
                })
                .val(vmData.department_id)
                .trigger("change")
                .on("select2:select", function (e) {
                    const data = e.params.data;
                    vmData = Object.assign({}, vmData, { department_id: data.id });
                });

            $('#change-company-department_id')
                .select2({
                    data: tempDropdownData.dropdown_department,
                    placeholder: {
                        id: "-1",
                        text: "Select a Department"
                    },
                    width: '100%',
                    dropdownParent: $("#change-employee-company-dialog")
                })
                .val(-1)
                .trigger("change");

            this.supervisorySelect2('#m--input-supervisor_id', true, _supData, vmData.supervisor);

            if (vmData.current_tl_supervisory == 1) {
                this.managerialSelect2('#m--input-manager_id', true, _data, vmData.manager);
            }

            if (vmData.is_multiple_position == 1) {
                let isMultiple = $("#is_multiple_position").is(':checked');
                const selectEl = $("#m--input-position_id");
                selectEl.prop("multiple", isMultiple);
                selectEl.attr('name', isMultiple ? 'position[]' : 'position');

                const sortMap = new Map();

                vmData.multiple_position.forEach(p => {
                    sortMap.set(parseInt(p.id), {
                        sort: parseInt(p.sort),
                        primary: parseInt(p.is_primary) // ensure boolean or numeric consistency
                    });
                });

                // intersect 2 array and get the matched data by position id
                
                let intersection = tempDropdownData.dropdown_position
                    .filter(a1 =>
                        vmData.multiple_position.some(a2 => parseInt(a2.id) === parseInt(a1.id))
                    )
                    .map(item => {
                        const data = sortMap.get(parseInt(item.id));
                        return {
                            ...item,
                            primary: data?.primary,
                            sort: data?.sort
                        };
                    })
                    .sort((a, b) => {
                        if (b.primary !== a.primary) {
                            return b.primary - a.primary;
                        }
                        return a.sort - b.sort;
                    });

                this.positionSelect2('#m--input-position_id', true, vmData.position, true, intersection);
                this.multiple_position = [...intersection];
            } else {
                this.positionSelect2('#m--input-position_id', true, vmData.position, false);
                this.multiple_position = [];
            }

            //-------- enable date regularized, separation date----//
            $("#status").change(function(){
                const status = $("#status").val();
                const classification = $("#classification").val();

                if(classification.toLowerCase() == 'active'){
                    if(status == 'REGULAR'){
                        const tempState = shouldEnableProbationEndDate(vmData) === false;
                        $("#m_datepicker-date_regular").attr("disabled", false);
                        $("#m_datepicker-date_end").attr("disabled", true);
                        $("#m_datepicker-date_end_prob").prop('disabled', tempState);

                        const startDateMin = moment(new Date(vmData.date_start), "YYYY-MM-DD").format("YYYY-MM-DD");
                        setTimeout(function () { $("#m_datepicker-date_regular").datepicker("setStartDate", startDateMin); }, 250);
                        if(tempState === false){
                            const endDateMax = moment(new Date(vmData.date_start), "YYYY-MM-DD").add(180, 'days').format("YYYY-MM-DD");
                            setTimeout(function () {
                                $("#m_datepicker-date_end_prob").datepicker("setStartDate", startDateMin);
                                $("#m_datepicker-date_end_prob").datepicker("setEndDate", endDateMax);
                            }, 250);
                        }
                    }else{
                        $("#m_datepicker-date_regular").attr("disabled", true);
                        $("#m_datepicker-date_end").attr("disabled", true);
                        $("#m_datepicker-date_end_prob").prop('disabled', false);
                    }
                }else{
                    $("#m_datepicker-date_end").attr("disabled", false);
                    $("#m_datepicker-date_end_prob").prop('disabled', true);

                    if(vmData.date_end == "0000-00-00" || vmData.date_end == ""){ 
                        const currentDateEnd = moment().format("YYYY-MM-DD");
                        const startDateMin = moment(new Date(vmData.date_start), "YYYY-MM-DD").format("YYYY-MM-DD");
                        setTimeout(function(){ 
                            $("#m_datepicker-date_end").datepicker('setStartDate', startDateMin); 
                            $("#m_datepicker-date_end").datepicker('setDate', currentDateEnd); 
                        }, 250); 
                    }
                }
            });

            $('#change-company-position')
                .select2({
                    data: tempDropdownData.dropdown_position,
                    placeholder: {
                        id: "-1",
                        text: "Select a Position"
                    },
                    width: '100%',
                    dropdownParent: $("#change-employee-company-dialog")
                })
                .val(-1)
                .trigger("change");
            
            $("#m--input-work_mode")
            .select2({
                data: tempDropdownData.dropdown_work_mode,
                placeholder: {
                    id: "-1",
                    text: "Select an option"
                },
                width: '100%'
            })
            .val(vmData.work_mode)
            .trigger("change")
            .on("select2:select", function (e) {
                const data = e.params.data;
                // vmData = Object.assign({}, vmData, { work_mode: data.id });
                vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { work_mode: data.id });
            });

            $("#m--input-payroll_type_id")
                .select2({
                    data: tempDropdownData.dropdown_payroll_type,
                    placeholder: {
                        id: "-1",
                        text: "Select an option"
                    },
                    width: '100%'
                })
                .val(vmData.payroll_type)
                .trigger("change")
                .on("select2:select", function (e) {
                    const data = e.params.data;
                    //vmData = Object.assign({}, vmData, { payroll_type: data.id });

                    vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { payroll_type: data.id });
                });

                //--------for reason for separation input to show---///
                var classification = $("#classification").val();
                if(classification == "Active"){
                    $("#reason_row").attr("hidden", true);
                }else{
                    $("#reason_row").attr("hidden", false);
                }
                //-----//

            $('#classification')
                .select2({
                    placeholder: "Select Option",
                    width: "100%"
                })
                .val(vmData.employee_status)
                .trigger("change")
                .on("select2:select", function (e) {
                    const data = e.params.data;
                    const status = $('#status');
                    status.find('option').remove();

                    if (data.text === 'ACTIVE') {
                        status.append(activeStatusOptions);
                        $("#reason_row").attr("hidden", true);
                        $("#m_datepicker-date_end").prop('disabled', true);
                        $("#m_datepicker-date_end_prob").val('0000-00-00');
                        $("#m_datepicker-date_end").val('0000-00-00');
                        $("#m_datepicker-date_resign").prop("disabled", true);
                    } else if (data.text === 'INACTIVE') {
                        $("#m_datepicker-date_resign").prop("disabled", false);
                        status.append(inactiveStatusOptions);
                        $("#reason_row").attr("hidden", false);

                        if(typeof status.val() != 'undefined' && status.val() != ''){
                            $("#m_datepicker-date_end").attr("disabled", false);
                        }else{
                            $("#m_datepicker-date_end").attr("disabled", true);
                        }
                    } else {
                        status.append(contractorStatusOption);
                        status.attr('readonly');
                    }

                    vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { employee_status: data.id });
                    if(typeof this.vm_tab3 != 'undefined' && Object.keys(this.vm_tab3).length > 0){
                        let { vm_tab3 } = this;
                        vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { employee_status: data.id });
                        // vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { employee_status: data.id });
    
                        if (data.id === 'Contractor') {
                            vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { work_status: 'N/A' });
                            // vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { work_status: 'N/A' });
                        }
                        // vmData = Object.assign({}, vmData);
                    }
                });
                
            if(typeof vmData.location_name != 'undefined' && vmData.location_name.length > 0){
                setTimeout(function(){ $('#station').val(vmData.location_name).trigger('change'); }, 750);
            }

            $('#station')
                .select2({
                    placeholder: "Select Option",
                    width: "100%",
                    data: tempDropdownData.dropdown_station,
                });
                
                $("#station").on("select2:select", function (evt) {
                    var element = evt.params.data.element;
                    var $element = $(element);
                
                    $element.detach();
                    $(this).append($element);
                    $(this).trigger("change");
                });
                
                if(typeof vmData.default_station != 'undefined' && parseInt(vmData.default_station) > 0){
                    setTimeout(function(){ 
                        const dsOption = new Option(vmData.default_station_description, vmData.default_station, true, true);
                        $('#default_station').append(dsOption).trigger('change'); 
                    }, 750);
                }

                $('#default_station')
                .select2({
                    placeholder: "Select An Option",
                    width: "100%",
                    data: tempDropdownData.dropdown_default_station,
                });
                
            $('#status')
                .select2({
                    placeholder: "Select Option",
                    width: "100%"
                })
                .val(vmData.work_status)
                .trigger("change")
                .on("select2:select", function (e) {
                    const data = e.params.data;
                    if(typeof this.vm_tab3 != 'undefined' && Object.keys(this.vm_tab3).length > 0){
                        vmData = Object.assign({}, vmData, { work_status: data.id });
                    }
                    setTimeout(() => { $(e.target).validate(); }, 250);
                });

            $('#level').select2({
                placeholder: "Select Option",
                width: "100%"
            })
            .val(vmData.level)
            .trigger("change")
            .on("select2:select", function (e) {
                const data = e.params.data;
                vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { level: data.id });

                if (data.id === "MANAGERIAL" || data.id === "EXECUTIVE") {
                    $('#is_two_level').trigger('change', function() {
                        $(this).prop('checked', false);
                    });
                    
                    vmTab3.vm_tab3.tl_supervisory = 0;
                    vmTab3.vm_tab3.current_tl_supervisory = 0;
                    vmTab3.vm_tab3.supervisor = 0;
                    vmTab3.vm_tab3.manager = 0;

                    vmTab3.supervisorySelect2('#m--input-supervisor_id', true, tempDropdownData.dropdown_supervisory, 0);
                    vmTab3.managerialSelect2('#m--input-manager_id', true, tempDropdownData.dropdown_supervisory, 0);
                }
            });

            $('#m_datepicker_2').datepicker({
                format: 'M d,yyyy',
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                }
            });

            if(work_status == ''){
                $("#m_datepicker-date_end_prob").prop('disabled', true);
                $("#m_datepicker-date_end").prop('disabled', true);
                $("#performance-rating").attr('onclick', '').unbind('click');
                $("#m_datepicker-date_resign").prop('disabled', true);
            }else{
                const tempState = shouldEnableProbationEndDate(vmData) === false;
                $("#m_datepicker-date_end_prob").prop('disabled', tempState);
                const startDateMin = moment(new Date(vmData.date_start), "YYYY-MM-DD").format("YYYY-MM-DD");
                setTimeout(function () { $("#m_datepicker-date_regular, m_datepicker-date_end").datepicker("setStartDate", startDateMin); }, 250);
                if(tempState === false){
                    const endDateMax = moment(new Date(vmData.date_start), "YYYY-MM-DD").add(180, 'days').format("YYYY-MM-DD");
                    setTimeout(function () {
                        $("#m_datepicker-date_end_prob").datepicker("setStartDate", startDateMin);
                        $("#m_datepicker-date_end_prob").datepicker("setEndDate", endDateMax);
                    }, 250);
                }
            }

            $('#m_datepicker-date_resign').daterangepicker({
                "singleDatePicker": true,
                "showDropdowns": true,
                "autoUpdateInput": false,
                "minDate": moment().format('YYYY-MM-DD'),
                "format": "YYYY-MM-DD",
                "locale": {
                    "format": "YYYY-MM-DD",
                    "cancelLabel": 'Clear'
                },
            }).on('apply.daterangepicker', function(ev, picker){
                let { vm_tab3 } = vmTab3;
                var date = picker.startDate.format('YYYY-MM-DD');

                $(this).val(date);
                vm_tab3.resignation_effective_date = date;

                if($(this).val() != ''){
                    $("#add-performance-rating-modal").modal();
                    $("#reason_row").attr("hidden", false);
                    $("#reason_row #resign_reason").attr('data-validation', 'required');
                }else{
                    $("#reason_row").attr("hidden", true);
                    $("#reason_row #resign_reason").attr('data-validation', false);
                }
            });

            $('#is_two_level').on('change', function(){
                if ($(this).is(':checked')) {
                    let __data = vmTab3.excludeEmployee(tempDropdownData.dropdown_supervisory, vmTab3.vm_tab3.supervisor);
                    vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { current_tl_supervisory: 1 });

                    vmTab3.managerialSelect2('#m--input-manager_id', true, __data, vmData.manager ? vmData.manager : 0);
                } else {
                    vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { current_tl_supervisory: 0, manager: 0 });

                    $("#m--input-manager_id").val('').trigger('change');
                }
            });

            $("#work_schedule").select2({
                placeholder: 'Select an option',
                width: '100%',
            })
            .val(vmData.work_schedule)
            .trigger('change')
            .on('select2:select', function(e) {
                const data = e.params.data;
                vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { work_schedule: data.id });
            });
        },
        methods: {
            supervisorySelect2(target, destroy = false, data = {}, id = 0){
                var currentTarget = $(target);

                if (destroy) {
                    currentTarget.empty();
                    currentTarget.off('select2:select');
                }

                var newObject = { 'id': 0, 'text': 'NONE' };
                let newArr = [newObject].concat(data);
                // added none to array

                if (id == 0) {
                    var option = new Option('NONE', 0, true, true);
                    currentTarget.append(option).trigger('change');
                }

                currentTarget.select2({
                    data: newArr,
                    placeholder: {
                        id: "-1",
                        text: "Select an option"
                    },
                    width: '100%'
                })
                .val(id)
                .trigger("change")
                .on('select2:select', function (e) {
                    var data = e.params.data;
                    vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { supervisor: data.id });

                    if (vmTab3.vm_tab3.current_tl_supervisory == 1) {
                        _data = vmTab3.excludeEmployee(tempDropdownData.dropdown_supervisory, data.id);
    
                        vmTab3.managerialSelect2('#m--input-manager_id', true, _data, vmTab3.vm_tab3.manager ? vmTab3.vm_tab3.manager : 0);
                        if (typeof vmTab3.vm_tab3.manager == 'undefined' || vmTab3.vm_tab3.manager == 0 ) {
                            if ($('#remove-initial-class').hasClass('has-error')) {
                                $('#remove-initial-class').removeClass('has-error');
                                $("#remove-initial-class .help-block.form-error").remove();
                            }
                        }
                    }
                });
            }, managerialSelect2(target, destroy = false, data = {}, id = 0){
                var currentTarget = $(target);

                if (destroy) {
                    currentTarget.empty();
                    currentTarget.off('select2:select');
                }
                currentTarget.select2({
                    data : data,
                    placeholder: "Search",
                    width: '100%'
                }).val(id).trigger("change").on('select2:select', function (e) {
                    var data = e.params.data;
                    vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { manager: data.id });
                    var _tempData = vmTab3.excludeEmployee(tempDropdownData.dropdown_supervisory, data.id);

                    vmTab3.supervisorySelect2('#m--input-supervisor_id', true, _tempData, vmTab3.vm_tab3.supervisor);
                });
            }, excludeEmployee(arr = [], id = 0){
                let _data = [];
                $.each(arr, function (index, value) {
                    if (value.id != id) {
                        _data.push(value);
                    }
                });

                return _data;
            }, positionSelect2 (target, destroy = false, id = 0, isMultiple = false, multiPosition = []) {
                let vmData = this.vm_tab3;
                const currentTarget = $(target);
                let _temp = [];

                if (destroy) {
                    currentTarget.empty();
                    currentTarget.off('select2:select');

                    if (currentTarget.hasClass('select2-hidden-accessible')) {
                        currentTarget.select2('destroy');
                    }
                }

                setTimeout(() => {
                    if (multiPosition.length > 0) {
                        multiPosition.forEach(pos => {
                            var option = new Option(pos.text, pos.id, true, true);
                            currentTarget.append(option).trigger('change');

                            _temp.push(pos.id);
                        });
                    }

                    currentTarget.select2({
                        data: tempDropdownData.dropdown_position,
                        placeholder: {
                            id: "-1",
                            text: "Select an option"
                        },
                        width: '100%',
                        multiple: isMultiple
                    }).on('select2:select', function (e) {
                        const data = e.params.data;

                        if(isMultiple){
                            var element = e.params.data.element;
                            var $element = $(element);
                        
                            $element.detach();
                            $(this).append($element);
                            $(this).trigger("change");

                            let newData = [];
                            const tempData = $(this).select2("data");
                            tempData.forEach((value, index) => { 
                                newData.push({ id: value.id, text: value.text, primary: index === 0 ? 1 : 0, sort: index }); 
                            });

                            vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { multiple_position: newData });

                            multiPosition = newData;
                            vmPrimary.positions = [...newData];
                        } else {
                            vmData.position = data.id;
                        }
                    }).on('select2:unselect', function (e) {
                        if(isMultiple){
                            let newData = [];
                            const tempData = $(this).select2("data");
                            tempData.forEach((value, index) => { 
                                newData.push({ id: value.id, text: value.text, primary: index === 0 ? 1 : 0, sort: index }); 
                            });

                            vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { multiple_position: newData });
                            multiPosition = newData;
                            vmPrimary.positions = [...newData];
                        }
                    });

                    if (!isMultiple) {
                        currentTarget.val(id).trigger("change");
                    } else {
                        if (multiPosition.length == 0) {
                            currentTarget.val(id).trigger("change");
                        } else {
                            currentTarget.val(_temp).trigger("change");
                        }
                    }
                }, 250);

            }, changeTOMultiple(e) {
                const instance = this;
                let vmData = instance.vm_tab3;
                let isMultiple = $(e.target).is(':checked');
                const selectEl = $("#m--input-position_id");
                let intersection = [];

                selectEl.prop("multiple", isMultiple);
                selectEl.attr('name', isMultiple ? 'position[]' : 'position');

                if (instance.vm_tab3.multiple_position.length > 0){
                    const sortMap = new Map();
                    
                    instance.vm_tab3.multiple_position.forEach(p => {
                        sortMap.set(parseInt(p.id), {
                            sort: parseInt(p.sort),
                            primary: parseInt(p.is_primary)
                        });
                    });
                    
                    intersection = tempDropdownData.dropdown_position
                    .filter(a1 =>
                        instance.vm_tab3.multiple_position.some(a2 => parseInt(a2.id) === parseInt(a1.id))
                    )
                    .map(item => {
                        const data = sortMap.get(parseInt(item.id));
                        return {
                            ...item,
                            primary: data?.primary,
                            sort: data?.sort
                        };
                    })
                    .sort((a, b) => {
                        if (b.primary !== a.primary) {
                            return b.primary - a.primary;
                        }
                        return a.sort - b.sort;
                    });
                }

                this.positionSelect2('#m--input-position_id', true, instance.vm_tab3.position, isMultiple, isMultiple ? intersection : []);
                vmPrimary.positions = [...intersection];
                vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { is_multiple_position: isMultiple ? 1 : 0 });

                if (isMultiple) {
                    $("#sort_position").show();
                } else {
                    $("#sort_position").hide();
                }
            }, sortPosition(e) {
                const instance = this;
                let intersection = [];

                vmPrimary.isSortOnly = true;
                if (instance.vm_tab3.multiple_position.length > 0){
                    const sortMap = new Map();
                    
                    instance.vm_tab3.multiple_position.forEach(p => {
                        sortMap.set(parseInt(p.id), {
                            sort: parseInt(p.sort),
                            primary: parseInt(p.is_primary) 
                        });
                    });

                    intersection = tempDropdownData.dropdown_position
                    .filter(a1 =>
                        instance.vm_tab3.multiple_position.some(a2 => parseInt(a2.id) === parseInt(a1.id))
                    )
                    .map(item => {
                        const data = sortMap.get(parseInt(item.id));
                        return {
                            ...item,
                            primary: data?.primary,
                            sort: data?.sort
                        };
                    })
                    .sort((a, b) => {
                        if (b.primary !== a.primary) {
                            return b.primary - a.primary;
                        }
                        return a.sort - b.sort;
                    });
                }


                vmPrimary.positions = [...intersection];
                $("#set_primary_position").modal('show');
                PortletDraggable.init();

            }
        }
    });

    var vmTabQuestions = new Vue({
        el: "#questions-content",
        data: { vm_question: [] },
        mounted(){
            if (tempData.more_questions && tempData.more_questions.length > 0) {
                this.vm_question = [...tempData.more_questions];
                const existingIds = new Set(tempData.more_questions.map(q => q.id));
                questions_list.forEach(q => {
                    if (!existingIds.has(q.id)) {
                        this.vm_question.push({
                            ...q,
                            answer: "N/A" 
                        });
                    }
                });
            }
            else{
                this.vm_question = questions_list;
                this.vm_question.forEach(q => {
                    if( tempData[`ques${q.id}`] == null ||  tempData[`ques${q.id}`] == undefined || tempData[`ques${q.id}`] == ""){
                        q.answer = "N/A"
                    }else{
                        q.answer = tempData[`ques${q.id}`];
                    }
                });
            }
        },
    });

    if (typeof tableDependents !== "undefined") {
        var dtDependents = tableDependents.DataTable({
            dom: '<"toolbar dt-toolbar_dependents">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_dependents"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "dep_name", title: "Name" },
                {
                    data: "dep_age",
                    title: "Age",
                    width: "10%",
                    className: "text-center"
                },
                {
                    data: "dep_relation",
                    title: "Relation",
                    width: "10%",
                    className: "text-center"
                },
                {
                    data: "dep_birthdate",
                    title: "Birth Date",
                    width: "10%",
                    className: "text-center"
                },
                { data: null, title: "Action", width: "8%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return dependentsDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_dependents").append(
                    "<button type='button' class='btn btn-sm btn-success mb-2 btnNew btnAddDependents'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-dependents_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableDependents.dataTable().api();
                            const elem = $("#tbl-dependents_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });

        function dependentsDataTableActions($id) {
            if ($id) {
                var _actionButton = "";

                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditDependents' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                if (jQuery.inArray("archive", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnArchive btnRemoveDependents' data-id='" +
                        $id +
                        "'><i class='la la-file-archive-o'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";
                return _actionButton;
            } else {
                return false;
            }
        }

        $(document).on("click", ".btnAddDependents", function () {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_dependents/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    var modalContent = modalTempContent.find(".modal-content");
                    if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);
                        modalTempContent.modal("show");

                        var dtPickerDepBirthDate = modalContent.find("#dep_birthdate").datepicker({
                            endDate: new Date(), 
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy-mm-dd",
                            autoclose: true,
                        })
                        .on("changeDate", function (e) {
                            var currentDt = moment(e.date).format("YYYY-MM-DD");
                            var self = $(e.target);
                            self.validate();
                        });

                        $.validate({
                            form: "#form-dependents",
                            lang: "en",
                            onSuccess: function (form) {
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();

                                $.ajax({
                                    url: formUrl,
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
                                                "Employee dependent saved.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalTempContent.modal("hide");
                                            dtDependents.ajax.reload();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating employee dependent!",
                                                5000
                                            );
                                        }

                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            });
        });

    }

    if (typeof tableEducationalBackground !== "undefined") {
        var dtEducationalBg = tableEducationalBackground.DataTable({
            dom: '<"toolbar dt-toolbar_education_bg">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_educational_bg"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [{
                data: "educ_level_type", title: "Level"
            }, {
                data: "educ_school",
                title: "School",
                width: "10%",
                className: "text-center"
            }, {
                data: "educ_degree",
                title: "Degree",
                width: "10%",
                className: "text-center"
            }, {
                data: "educ_honors",
                title: "Honors",
                width: "10%",
                className: "text-center"
            }, {
                data: "educ_from",
                title: "From Date",
                width: "10%",
                className: "text-center"
            }, {
                data: "educ_to",
                title: "To Date",
                width: "10%",
                className: "text-center"
            }, {
                data: null, title: "Action", width: "8%", className: "text-center"
            }],
            columnDefs: [{
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return educationalDataTableActions(row.id);
                }
            }, {
                targets: "_all",
                defaultContent: ""
            }],
            initComplete: function () {
                $(".dt-toolbar_education_bg").append(
                    "<button type='button' class='btn btn-sm btn-success mb-2 btnNew btnAddEducation'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-educational_background_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableEducationalBackground.dataTable().api();
                            const elem = $("#tbl-educational_background_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });

        function educationalDataTableActions($id) {
            if ($id) {
                var _actionButton = "";
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditEducational' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                if (jQuery.inArray("archive", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnArchive btnRemoveEducational' data-id='" +
                        $id +
                        "'><i class='la la-file-archive-o'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";
                return _actionButton;
            } else {
                return false;
            }
        }

        $(document).on("click", ".btnAddEducation", function () {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_education/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    var modalContent = modalTempContent.find(".modal-content");
                    if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);
                        modalContent.find("#educ_level_type").select2({
                            width: "100%",
                            placeholder: "Select an option",
                            dropdownParent: modalTempContent
                        });
                        modalTempContent.modal("show");

                        var dtPickerEducationFromDate = modalContent.find("#educ_from").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy",
                            viewMode: "years",
                            minViewMode: "years",
                            autoclose: true
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY");
                                var self = $(e.target);
                                self.validate();
                            });

                        var dtPickerEducationToDate = modalContent.find("#educ_to").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy",
                            viewMode: "years",
                            minViewMode: "years",
                            autoclose: true
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY");
                                var self = $(e.target);
                                self.validate();
                            });

                        $.validate({
                            form: "#form-education",
                            lang: "en",
                            onSuccess: function (form) {
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();

                                $.ajax({
                                    url: formUrl,
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
                                                "Employee educational background has been saved.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalTempContent.modal("hide");
                                            dtEducationalBg.ajax.reload();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating employee educational background!",
                                                5000
                                            );
                                        }

                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            });
        });
    }

    if (typeof tableLicensure !== "undefined") {
        var dtLicensureExam = tableLicensure.DataTable({
            dom: '<"toolbar dt-toolbar_licensure">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_licensure"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "license_type", title: "License/Certificate" },
                { data: "exam_place", title: "Exam Place", className: "text-center" },
                { data: "rating", title: "Rating", className: "text-center" },
                { data: "release_date", title: "Released Date", className: "text-center" },
                { data: "exam_date", title: "Exam Date", className: "text-center" },
                { data: "license_no", title: "License Cert. No.", className: "text-center" },
                { data: "expiration_date", title: "Expiry Date", className: "text-center" },
                { data: "remarks", title: "Remarks", className: "text-center",
                    render: function(data, type, row) {
                        return data && data.trim() !== '' ? data : 'none';
                    }
                },
                {
                    data: "liscert_attachment", 
                    title: "Attachment", 
                    className: "text-center",
                    render: function(data, type, row) {
                        if (!data || data.trim() === '') {
                            return 'none';
                        }
                    
                        const truncated = data.length > 15 ? data.substring(0, 15) + '...' : data;
                        return `<span style="cursor: pointer; color: #007bff; text-decoration: underline;" onclick="openCert('${data}')">${truncated}</span>`;
                    }
                },
                { data: null, title: "Action", width: "8%", className: "text-center" }
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function(data, type, row, meta){
                        var license = "";

                        if(row.type && row.type != null){
                            if(row.type == 'COMPANY SPONSORED - INTERNAL'){
                                license += '<p style="margin: 0; font-size: 9px;" class="badge badge-success">'+row.type+'</p>';
                            }else if(row.type == 'COMPANY SPONSORED - EXTERNAL'){
                                license += '<p style="margin: 0; font-size: 9px;" class="badge badge-danger">'+row.type+'</p>';
                            }
                            else{
                                license += '<p style="margin: 0; font-size: 9px;"  class="badge badge-info">'+row.type+'</p>';
                            }
                            license += '<p style="margin: 0">'+data+'</p>';
                        }else if(data == 'CERTIFICATE'){
                            license += '<p style="margin: 0; font-size: 9px;"  class="badge badge-info">'+"PERSONAL"+'</p>';
                            license += '<p style="margin: 0">'+row.certificate_name+'</p>';
                        }
                        return license;
                    }
                },
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return licensureDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_licensure").append(
                    "<button type='button' class='btn btn-sm btn-success mb-2 btnNew btnAddLicensure'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-licensure_exams_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableLicensure.dataTable().api();
                            const elem = $("#tbl-licensure_exams_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });

        function licensureDataTableActions($id) {
            if ($id) {
                var _actionButton = "";
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditLicensure' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                if (jQuery.inArray("archive", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnArchive btnRemoveLicensure' data-id='" +
                        $id +
                        "'><i class='la la-file-archive-o'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";
                return _actionButton;
            } else {
                return false;
            }
        }

        $(document).on("click", ".btnAddLicensure", function () {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_licensure/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    var modalContent = modalTempContent.find(".modal-content");
                    if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);

                        modalContent.find("#license_type").select2({
                            width: "100%",
                            placeholder: "Select an option",
                            dropdownParent: modalTempContent,
                            ajax:{
                                url: baseUrl('hris/masterfile/get_license_type'),
                                method: "GET",
                                delay: 250,
                            }
                        }).on('select2:select', function (e) {
                            var data = e.params.data;
                            const id = data.id;
                            var cert_name_field = modalContent.find('.cert-name-field');

                            if(id === 'Certificate') {
                                cert_name_field.removeClass('d-none').html(`
                                    <label for="certificate_name" class="form-control-label">Certificate Name *</label>
                                    <input id="certificate_name" name="certificate_name" type="text" maxlength="100" size="100" data-validation="required" autocomplete="off" class="form-control m-input" />
                                `);
                            } else {
                                cert_name_field.addClass('d-none').html('');
                            } 
                        });

                        modalTempContent.modal("show");

                        var dtPickerLicensureReleaseDate = modalContent.find("#release_date").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy-mm-dd",
                            autoclose: true
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY-MM-DD");
                                var self = $(e.target);
                                self.validate();
                            });

                        var dtPickerLicensureExamDate = modalContent.find("#exam_date").daterangepicker({
                            buttonClasses: 'm-btn btn',
                            applyClass: 'btn-primary',
                            cancelClass: 'btn-secondary',
                            showDropdowns: true,
                            autoUpdateInput: false,
                            locale: {
                                cancelLabel: 'Clear'
                            },
                        }).on('apply.daterangepicker', function(ev, picker){
                            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                            var self = $(ev.target);
                            self.validate();
                        });

                        var dtPickerLicensureExpriyDate = modalContent.find("#expiration_date").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy-mm-dd",
                            autoclose: true
                        }).on("changeDate", function (e) {
                            var currentDt = moment(e.date).format("YYYY-MM-DD");
                            var self = $(e.target);
                            self.validate();
                        });

                        modalContent.find("#expiry-switch input").on('click', function(){
                            if(typeof $("#expiry-switch input:checked").val() != 'undefined'){
                                modalContent.find('#with-expiry').removeClass('d-none');
                            }else{
                                modalContent.find('#with-expiry').addClass('d-none');
                            }
                        });
                        let url = baseUrl("hris/masterfile/upload_employee_liscert");
                        $("#fileupload_liscert")
                        .fileupload({
                            url: url,
                            dataType: "json",
                            formData: { csrf_token: _csrf_hash, employee_id: tempDataId },
                            done: function (e, data) {
                                var result = data.result;
                                if (result.response) {
                                    modalContent.find("#liscert_attachment").val(result.filename);
                                    modalContent.find("#temp_fileupload").empty().text(result.filename);
                                    toastr.success(result.toastr_msg, "Upload License and Certificate File", 5000);
                                } else {
                                    toastr.error(result.toastr_msg, "Upload License and Certificate File", 5000);
                                }
                            }
                        })
                        .prop("disabled", !$.support.fileInput)
                        .parent()
                        .addClass($.support.fileInput ? undefined : "disabled");

                        $.validate({
                            form: "#form-licensure",
                            lang: "en",
                            onSuccess: function (form) {
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();

                                $.ajax({
                                    url: formUrl,
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
                                                "Employee licensure exam and certification has been saved.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalTempContent.modal("hide");
                                            dtLicensureExam.ajax.reload();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating employee licensure exam and certification!",
                                                5000
                                            );
                                        }

                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            });
        });
    }

    if (typeof tableDriverLicense !== "undefined") {
        var dtLicensure = tableDriverLicense.DataTable({
            dom: '<"toolbar dt-toolbar_driverlicense">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_driverlicense"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "restriction", title: "Restriction" },
                { data: "license_no", title: "License No." },
                { data: "expiration_date", title: "Exp Date" },
                { data: null, title: "Action", width: "8%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return driverlicenseDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_driverlicense").append(
                    "<button type='button' class='btn btn-sm btn-success btnNew btnAddDriverLicense'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-driverlicense input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableDriverLicense.dataTable().api();
                            const elem = $("#tbl-driverlicense input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });

        function driverlicenseDataTableActions($id) {
            if ($id) {
                var _actionButton = "";
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditDriverLicense' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                if (jQuery.inArray("archive", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnArchive btnRemoveDriverLicense' data-id='" +
                        $id +
                        "'><i class='la la-file-archive-o'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";
                return _actionButton;
            } else {
                return false;
            }
        }

        $(document).on("click", ".btnAddDriverLicense", function () {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_driverlicense/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    var modalContent = modalTempContent.find(".modal-content");
                    if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);
                        modalTempContent.modal("show");

                        var dtPickerLicensureReleaseDate = modalContent.find("#expiration_date").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy-mm-dd",
                            autoclose: true
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY-MM-DD");
                                var self = $(e.target);
                                self.validate();
                            });

                        $.validate({
                            form: "#form-driverlicense",
                            lang: "en",
                            onSuccess: function (form) {
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();

                                $.ajax({
                                    url: formUrl,
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
                                                "Employee Driver's License has been saved.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalTempContent.modal("hide");
                                            dtLicensure.ajax.reload();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating employee driver's license!",
                                                5000
                                            );
                                        }

                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            });
        });
    }

    if (typeof tableAwards !== "undefined") {
        var dtAwards = tableAwards.DataTable({
            dom: '<"toolbar dt-toolbar_awards">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_awards"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "award", title: "Awards/Achievement" },
                { data: "award_institution", title: "Institution" },
                { data: "award_date", title: "Given Date", width: "10%" },
                { data: null, title: "Action", width: "8%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return awardsDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_awards").append(
                    "<button type='button' class='btn btn-sm btn-success mb-2 btnNew btnAddAwards'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-awards_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableAwards.dataTable().api();
                            const elem = $("#tbl-awards_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });

        function awardsDataTableActions($id) {
            if ($id) {
                var _actionButton = "";
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditAwards' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                if (jQuery.inArray("archive", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveAwards' data-id='" +
                        $id +
                        "'><i class='la la-file-archive-o'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";
                return _actionButton;
            } else {
                return false;
            }
        }

        $(document).on("click", ".btnAddAwards", function () {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_awards/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    var modalContent = modalTempContent.find(".modal-content");
                    if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);
                        modalTempContent.modal("show");

                        var dtPickerAwardDate = modalContent.find("#award_date").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy-mm-dd",
                            autoclose: true
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY-MM-DD");
                                var self = $(e.target);
                                self.validate();
                            });

                        $.validate({
                            form: "#form-awards",
                            lang: "en",
                            onSuccess: function (form) {
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();

                                $.ajax({
                                    url: formUrl,
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
                                                "Employee award and achievement has been saved.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalTempContent.modal("hide");
                                            dtAwards.ajax.reload();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating employee award and achievement!",
                                                5000
                                            );
                                        }

                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            });
        });
    }

    if (typeof tableOrganization !== "undefined") {
        var dtOrganization = tableOrganization.DataTable({
            dom: '<"toolbar dt-toolbar_organization">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_organization"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "org_institution", title: "Institution" },
                { data: "org_membership_title", title: "Membership title" },
                { data: "org_from", title: "From" },
                { data: "org_to", title: "To" },
                { data: null, title: "Action", width: "8%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: "org_from",
                    targets: 2,
                    render: function (data, type, row, meta) {
                        return moment(data).format("YYYY");
                    }
                }, {
                    data: "org_to",
                    targets: 3,
                    render: function (data, type, row, meta) {
                        return moment(data).format("YYYY");
                    }
                }, {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return organizationDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_organization").append(
                    "<button type='button' class='btn btn-sm btn-success mb-2 btnNew btnAddOrganization'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-organizations_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableOrganization.dataTable().api();
                            const elem = $("#tbl-organizations_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });

        function organizationDataTableActions($id) {
            if ($id) {
                var _actionButton = "";
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditOrganization' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                if (jQuery.inArray("archive", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveOrganization' data-id='" +
                        $id +
                        "'><i class='la la-file-archive-o'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";
                return _actionButton;
            } else {
                return false;
            }
        }

        $(document).on("click", ".btnAddOrganization", function () {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_organization/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    var modalContent = modalTempContent.find(".modal-content");
                    if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);
                        modalTempContent.modal("show");

                        var dtPickerOrgFromDate = modalContent.find("#org_from").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy",
                            viewMode: "years",
                            minViewMode: "years",
                            autoclose: true
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY");
                                var self = $(e.target);
                                self.validate();
                            });

                        var dtPickerOrgToDate = modalContent.find("#org_to").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy",
                            viewMode: "years",
                            minViewMode: "years",
                            autoclose: true
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY");
                                var self = $(e.target);
                                self.validate();
                            });

                        $.validate({
                            form: "#form-organization",
                            lang: "en",
                            onSuccess: function (form) {
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();

                                $.ajax({
                                    url: formUrl,
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
                                                "Employee organization has been saved.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalTempContent.modal("hide");
                                            dtOrganization.ajax.reload();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating employee organization!",
                                                5000
                                            );
                                        }

                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            });
        });
    }

    if (typeof tableTrainings !== "undefined") {
        var dtTrainings = tableTrainings.DataTable({
            dom: '<"toolbar dt-toolbar_trainings">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_training"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "training", title: "Training" },
                { data: "train_from", title: "From" },
                { data: "train_to", title: "To" },
                { data: "train_institution", title: "Institution" },
                { data: "train_conductor", title: "Conductor" },
                { data: "train_venue", title: "Venue" },
                { data: "attachment", title: "Attachment" },
                { data: null, title: "Action", width: "8%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return trainingsDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_trainings").append(
                    "<button type='button' class='btn btn-sm btn-success mb-2 btnNew btnAddTrainings'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-trainings_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableTrainings.dataTable().api();
                            const elem = $("#tbl-trainings_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });

        function trainingsDataTableActions($id) {
            if ($id) {
                var _actionButton = "";
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditTrainings' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                if (jQuery.inArray("archive", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveTrainings' data-id='" +
                        $id +
                        "'><i class='la la-file-archive-o'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";

                return _actionButton;
            } else {
                return false;
            }
        }

        $(document).on("click", ".btnAddTrainings", function () {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_training/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    var modalContent = modalTempContent.find(".modal-content");
                    if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);
                        modalTempContent.modal("show");

                        var dtPickertrainingFromDate = modalContent.find("#train_from").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy-mm-dd",
                            autoclose: true
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY-MM-DD");
                                var self = $(e.target);
                                self.validate();
                            });

                        var dtPickerTrainingToDate = modalContent.find("#train_to").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy-mm-dd",
                            autoclose: true
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY-MM-DD");
                                var self = $(e.target);
                                self.validate();
                            });


                        var url = baseUrl("hris/masterfile/upload_employee_training");
                        $("#fileupload_training")
                            .fileupload({
                                url: url,
                                dataType: "json",
                                formData: { csrf_token: _csrf_hash, employee_id: tempDataId },
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
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();

                                $.ajax({
                                    url: formUrl,
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
                                            dtTrainings.ajax.reload();
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
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            });
        });
    }

    if (typeof tablePersonalReference !== "undefined") {
        var dtPersonalReference = tablePersonalReference.DataTable({
            dom: '<"toolbar dt-toolbar_personal_reference">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_personal_reference"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "ref_name", title: "Name" },
                { data: "ref_contact_no", title: "Contact No" },
                { data: "ref_address", title: "Address" },
                { data: null, title: "Action", width: "8%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return personalReferenceDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_personal_reference").append(
                    "<button type='button' class='btn btn-sm btn-success mb-2 btnNew btnAddPersonalReference'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-personal_references_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tablePersonalReference.dataTable().api();
                            const elem = $("#tbl-personal_references_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });

        function personalReferenceDataTableActions($id) {
            if ($id) {
                var _actionButton = "";
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditPersonalReference' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                if (jQuery.inArray("archive", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemovePersonalReference' data-id='" +
                        $id +
                        "'><i class='la la-file-archive-o'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";
                return _actionButton;
            } else {
                return false;
            }
        }

        $(document).on("click", ".btnAddPersonalReference", function () {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_references/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    var modalContent = modalTempContent.find(".modal-content");
                    if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);
                        modalTempContent.modal("show");

                        $.validate({
                            form: "#form-references",
                            lang: "en",
                            onSuccess: function (form) {
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();

                                $.ajax({
                                    url: formUrl,
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
                                                "Employee personal reference has been saved.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalTempContent.modal("hide");
                                            dtPersonalReference.ajax.reload();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating employee personal reference!",
                                                5000
                                            );
                                        }

                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            });
        });
    }

    if (typeof tableMedicalHistory !== "undefined") {
        var dtMedicalHistory = tableMedicalHistory.DataTable({
            dom: '<"toolbar dt-toolbar_medical_history">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_medical_history"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "med_details", title: "Details" },
                { data: "med_no", title: "Med. No" },
                { data: "med_date", title: "Date" },
                { data: "med_venue", title: "Venue" },
                { data: "med_physician", title: "Physician" },
                { data: "med_findings", title: "Findings" },
                { data: "remarks", title: "Remarks" },
                { data: null, title: "Action", width: "8%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return medicalHistoryDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_medical_history").append(
                    "<button type='button' class='btn btn-sm btn-success mb-2 btnNew btnAddMedicalHistory'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-medical_records_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableMedicalHistory.dataTable().api();
                            const elem = $("#tbl-medical_records_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });

        function medicalHistoryDataTableActions($id) {
            if ($id) {
                var _actionButton = "";
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditMedicalHistory' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                if (jQuery.inArray("archive", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveMedicalHistory' data-id='" +
                        $id +
                        "'><i class='la la-file-archive-o'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";
                return _actionButton;
            } else {
                return false;
            }
        }

        $(document).on("click", ".btnAddMedicalHistory", function () {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_medical_history/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    var modalContent = modalTempContent.find(".modal-content");
                    if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);
                        modalTempContent.modal("show");

                        var dtPickerMedicalDate = modalContent.find("#med_date").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy-mm-dd",
                            autoclose: true
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY-MM-DD");
                                var self = $(e.target);
                                self.validate();
                            });

                        var url = baseUrl("hris/masterfile/upload_employee_medical_history");
                        $("#fileupload_medical")
                            .fileupload({
                                url: url,
                                dataType: "json",
                                formData: { csrf_token: _csrf_hash, employee_id: tempDataId },
                                done: function (e, data) {
                                    var result = data.result;
                                    if (result.response) {
                                        modalContent.find("#medical_attachment").val(result.filename);
                                        modalContent.find("#temp_fileupload").empty().text(result.filename);
                                        toastr.success(result.toastr_msg, "Upload Medical History/Record File", 5000);
                                    } else {
                                        toastr.error(result.toastr_msg, "Upload Medical History/Record File", 5000);
                                    }
                                }
                            })
                            .prop("disabled", !$.support.fileInput)
                            .parent()
                            .addClass($.support.fileInput ? undefined : "disabled");

                        $.validate({
                            form: "#form-medical_history",
                            lang: "en",
                            onSuccess: function (form) {
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();

                                $.ajax({
                                    url: formUrl,
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
                                                "Employee medical history/record has been saved.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalTempContent.modal("hide");
                                            dtMedicalHistory.ajax.reload();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating employee medical history/record!",
                                                5000
                                            );
                                        }

                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            });
        });
    }

    /****** employment data tab section ********/
    if (typeof tableLegalHistory !== "undefined") {
        var dtLegalHistory = tableLegalHistory.DataTable({
            dom: '<"toolbar dt-toolbar_legal_history">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_legal_history"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "leg_case_no", title: "Case No" },
                { data: "leg_details", title: "Details" },
                { data: "leg_case_date", title: "Date" },
                { data: "leg_court_field", title: "Court Filed" },
                { data: "leg_prosecutor", title: "Prosecutor" },
                { data: "leg_status", title: "Status" },
                { data: null, title: "Action", width: "8%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return legalHistoryDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_legal_history").append(
                    "<button type='button' class='btn btn-sm btn-success mb-2 btnNew btnAddLegalHistory'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-legal_history_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableLegalHistory.dataTable().api();
                            const elem = $("#tbl-legal_history_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });

        function legalHistoryDataTableActions($id) {
            if ($id) {
                var _actionButton = "";
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditLegalHistory' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                if (jQuery.inArray("archive", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveLegalHistory' data-id='" +
                        $id +
                        "'><i class='la la-file-archive-o'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";

                return _actionButton;
            } else {
                return false;
            }
        }

        $(document).on("click", ".btnAddLegalHistory", function () {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_legal_history/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    var modalContent = modalTempContent.find(".modal-content");
                    if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);
                        modalTempContent.modal("show");

                        var dtPickerLegalDate = modalContent.find("#leg_case_date").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy-mm-dd",
                            autoclose: true
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY-MM-DD");
                                var self = $(e.target);
                                self.validate();
                            });

                        $.validate({
                            form: "#form-legal_history",
                            lang: "en",
                            onSuccess: function (form) {
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();

                                $.ajax({
                                    url: formUrl,
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
                                                "Employee legal history/record has been saved.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalTempContent.modal("hide");
                                            dtLegalHistory.ajax.reload();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating legal medical history/record!",
                                                5000
                                            );
                                        }

                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            });
        });
    }

    // Function to open file based on its type
    function openFile(employeeId, name) {
        // Construct the full URL of the file
        var fileUrl = baseUrl("uploads/files/documents/employee_files/empcode_" + employeeId + "/offenses_commendation/" + encodeURIComponent(name));
        // Function to check if file exists and get its MIME type
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
    
        // Check if file exists
        checkFileExists(fileUrl, function(exists, mimeType) {
            if (!exists) {
                // Show error message if file doesn't exist
                $('#pdfViewerModal .modal-body').html('<p class="text-danger">Error: File not found.</p>');
                $('#pdfViewerModal').modal('show');
            } else if (mimeType && mimeType.startsWith('application/pdf')) {
                // Show PDF in modal
                $('#pdfViewerModal .modal-body').html('<iframe id="pdfFrame" style="width: 100%; height: 600px;" frameborder="0"></iframe>');
                $('#pdfViewerModal').modal('show');
                $('#pdfFrame').attr('src', fileUrl);
            } else {
                // Open non-PDF files in new window
                window.open(fileUrl, '_blank');
            }
        });
    }

    if (typeof tableOffenses !== "undefined") {
        var dtOffenses = tableOffenses.DataTable({
            dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 dt-toolbar_offenses'><'col-xl-6 col-lg-6 col-md-6 col-sm-12'f>>" +
                "<'row'<'col-12'rt>>" +
                "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'l><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_offenses/")+'Offenses',
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "offcom_type", width: "*" },
                { 
                    data: "offcom_date", 
                    width: "*",
                    render: function(data, type, row) {
                        // Assuming 'data' is in the format YYYY-MM-DD
                        var date = new Date(data);
                        var formattedDate = date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                        });
                        return formattedDate;
                    },
                },
                { data: "offcom_nature", },
                { data: "offcom_action", },
                { data: "filename",className: "text-center",
                    render:  function(data, type, row, meta){
                        const isDisabled = (row.filename == '---') ? 'disabled' : '';
                        return `
                        <span>
                            <button class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"
                                    onclick="openFile('${tempDataId}', '${data}')" ${isDisabled}>
                                <i class="fa fa-eye"></i>
                            </button>
                        </span>`;
                    }
                },
                {
                    data: "filename",
                    width: "10%",
                    render: function(data, type, row, meta) {
                        const truncatedData = data.length > 20 ? data.substring(0, 20) + '...' : data;
                        return `
                        <span>
                            <span>${truncatedData}</span>
                        </span>`;
                    },
                },
                { data: null, width: "12%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return offensesDataTableActions(row.id,'offenses');
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_offenses").append(
                    "<button id ='btnOffenses' type='button' class='btn btn-sm btn-success mb-2 btnNew btnAddOffenses'  data-select='offenses'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-offenses_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableOffenses.dataTable().api();
                            const elem = $("#tbl-offenses_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });
    }


    if (typeof tableCommendation !== "undefined") {
        var dtCommendation = tableCommendation.DataTable({
            dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 dt-toolbar_commendation'><'col-xl-6 col-lg-6 col-md-6 col-sm-12'f>>" +
                "<'row'<'col-12'rt>>" +
                "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'l><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_offenses/")+'Commendation',
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "offcom_type", width: "*" },
                { 
                    data: "offcom_date", 
                    width: "*",
                    render: function(data, type, row) {
                        // Assuming 'data' is in the format YYYY-MM-DD
                        var date = new Date(data);
                        var formattedDate = date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                        });
                        return formattedDate;
                    },
                },
                { data: "offcom_nature", },
                { data: "offcom_action", },
                { data: "filename",className: "text-center",
                    render:  function(data, type, row, meta){
                        return `
                        <span>
                            <button class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"
                                    onclick="openFile('${tempDataId}', '${data}')">
                                <i class="fa fa-eye"></i>
                            </button>
                        </span>`;
                    }
                },
                {
                    data: "filename",
                    width: "10%",
                    render: function(data, type, row, meta) {
                        const truncatedData = data.length > 20 ? data.substring(0, 20) + '...' : data;
                        return `
                        <span>
                            <span>${truncatedData}</span>
                        </span>`;
                    },
                },
                { data: null, width: "12%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return offensesDataTableActions(row.id, 'commendation');
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_commendation").append(
                    "<button id='btnCommendation' type='button' class='btn btn-sm btn-success mb-2 btnNew btnAddOffenses' data-select='commendation'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-commendation_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableCommendation.dataTable().api();
                            const elem = $("#tbl-commendation_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            },
        });
    }

    if (typeof tableNotices !== "undefined") {
        var dtNotices = tableNotices.DataTable({
            dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 dt-toolbar_notices'><'col-xl-6 col-lg-6 col-md-6 col-sm-12'f>>" +
                "<'row'<'col-12'rt>>" +
                "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'l><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_offenses/")+"Notices",
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "offcom_type", width: "*" },
                { 
                    data: "offcom_date", 
                    width: "*",
                    render: function(data, type, row) {
                        // Assuming 'data' is in the format YYYY-MM-DD
                        var date = new Date(data);
                        var formattedDate = date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                        });
                        return formattedDate;
                    },
                },
                { data: "offcom_nature", },
                { data: "offcom_action", },
                { data: "filename",className: "text-center",
                    render:  function(data, type, row, meta){
                        return `
                        <span>
                            <button class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"
                                    onclick="openFile('${tempDataId}', '${data}')">
                                <i class="fa fa-eye"></i>
                            </button>
                        </span>`;
                    }
                },
                {
                    data: "filename",
                    width: "10%",
                    render: function(data, type, row, meta) {
                        const truncatedData = data.length > 20 ? data.substring(0, 20) + '...' : data;
                        return `
                        <span>
                            <span>${truncatedData}</span>
                        </span>`;
                    },
                },
                { data: null, width: "12%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return offensesDataTableActions(row.id, 'notices');
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_notices").append(
                    "<button id='btnNotices' type='button' class='btn btn-sm btn-success mb-2 btnNew btnAddOffenses' data-select='notices'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-notices_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableNotices.dataTable().api();
                            const elem = $("#tbl-notices_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            },
        });
    }

    if (typeof tableOthers !== "undefined") {
        var dtOthers = tableOthers.DataTable({
            dom: "<'row mb-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12'><'col-xl-6 col-lg-6 col-md-6 col-sm-12'f>>" +
                "<'row'<'col-12'rt>>" +
                "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'l><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_offenses/")+"Others",
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "offcom_type", width: "*" },
                { 
                    data: "offcom_date", 
                    width: "*",
                    render: function(data, type, row) {
                        // Assuming 'data' is in the format YYYY-MM-DD
                        var date = new Date(data);
                        var formattedDate = date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                        });
                        return formattedDate;
                    },
                },
                { data: "offcom_nature", },
                { data: "offcom_action", },
                { data: "filename",className: "text-center",
                    render:  function(data, type, row, meta){
                        return `
                        <span>
                            <button class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"
                                    onclick="openFile('${tempDataId}', '${data}')">
                                <i class="fa fa-eye"></i>
                            </button>
                        </span>`;
                    }
                },
                {
                    data: "filename",
                    width: "10%",
                    render: function(data, type, row, meta) {
                        const truncatedData = data.length > 20 ? data.substring(0, 20) + '...' : data;
                        return `
                        <span>
                            <span>${truncatedData}</span>
                        </span>`;
                    },
                },
                { data: null, width: "12%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return offensesDataTableActions(row.id,'others');
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                let search_thread = null;
                $("#tbl-others_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableOthers.dataTable().api();
                            const elem = $("#tbl-others_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            },

        })  
    }

    function offensesDataTableActions($id,$type) {
        if ($id) {
            var _actionButton = "";
            if (jQuery.inArray("edit", _currentActions) !== -1) {
                _actionButton +=
                    " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditOffenses' data-select='"+$type+"' data-id='" + $id + "'><i class='la la-edit'></i></button>";
            }
            // if (jQuery.inArray("archive", _currentActions) !== -1) {
            //     _actionButton +=
            //         " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveOffenses' data-id='" +
            //         $id +
            //         "'><i class='la la-file-archive-o'></i></button>";
            // UNCOMMENT TO ADD BACK FUNCTIONALITY T_T
            // }
            _actionButton = (_actionButton) ? _actionButton : "---";
            return _actionButton;
        } else {
            return false;
        }
    }

    $(document).on("click", ".btnAddOffenses", function () {

        let selectedData = this.dataset.select;
        let data = [];
        if (selectedData == 'offenses'){
            data =  [
                { id: '1ST OFFENSE', text: '1ST OFFENSE' },
                { id: '2ND OFFENSE', text: '2ND OFFENSE' },
                { id: '3RD OFFENSE', text: '3RD OFFENSE' },
                { id: '4TH OFFENSE', text: '4TH OFFENSE' },
                { id: '5TH OFFENSE', text: '5TH OFFENSE' },
                { id: 'WRITTEN WARNING', text: 'WRITTEN WARNING' },
                { id: '3-DAYS SUSPENSION', text: '3-DAYS SUSPENSION' },
                { id: '6-DAYS SUSPENSION', text: '6-DAYS SUSPENSION' },
                { id: '1-2-DAYS SUSPENSION', text: '1-2 DAYS SUSPENSION' },
                { id: 'DISMISSAL', text: 'DISMISSAL' }
            ]
        }
        else if(selectedData == 'commendation'){
            data =  [
                { id: 'COMMENDATION', text: 'COMMENDATION' },]
        }
        else if(selectedData == 'notices'){
            data =  [
                { id: 'NOTICES', text: 'NOTICE' },]
        }

        $.ajax({
            url: baseUrl("hris/masterfile/get_modal_offenses/" + tempDataId),
            dataType: "json",
            success: function (json) {
                var modalContent = modalTempContent.find(".modal-content");
                if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                    modalContent.empty();
                    modalContent.append(json.html);
                    modalContent.find("#offcom_type").select2({
                        width: "100%",
                        placeholder: "Select an option",
                        dropdownParent: modalTempContent,
                        data: data,
                    });

                    if (data.length === 1) {
                        modalContent.find("#offcom_type")
                            .val(data[0].id)
                            .trigger('change');
                    }

                    modalTempContent.modal("show");

                    var dtPickerLegalDate = modalContent.find("#offcom_date").datepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "yyyy-mm-dd",
                        autoclose: true
                    })
                        .on("changeDate", function (e) {
                            var currentDt = moment(e.date).format("YYYY-MM-DD");
                            var self = $(e.target);
                            self.validate();
                        });

                    var url = baseUrl("hris/masterfile/upload_employee_offenses");
                    $("#fileupload_offenses")
                        .fileupload({
                            url: url,
                            dataType: "json",
                            formData: { csrf_token: _csrf_hash, employee_id: tempDataId },
                            done: function (e, data) {
                                var result = data.result;
                                if (result.response) {
                                    modalContent.find("#offenses_attachment").val(result.filename);
                                    modalContent.find("#temp_fileupload").empty().text(result.filename);
                                    toastr.success(result.toastr_msg, "Upload Offense and Commendation File", 5000);
                                    modalContent.find("#fileupload_offenses").removeAttr('data-validation');
                                } else {
                                    toastr.error(result.toastr_msg, "Upload Offense and Commendation File", 5000);
                                }
                            }
                        })
                        .prop("disabled", !$.support.fileInput)
                        .parent()
                        .addClass($.support.fileInput ? undefined : "disabled");

                    $.validate({
                        form: "#form-offenses",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
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
                                            "Employee offense and commendation has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalTempContent.modal("hide");
                                        if(selectedData == 'offenses'){
                                            dtOffenses.ajax.reload();
                                        }else if(selectedData == 'commendation'){
                                            dtCommendation.ajax.reload();
                                        }else if(selectedData == 'notices'){
                                            dtNotices.ajax.reload();
                                        }
                                        else if(selectedData == 'others'){
                                            dtOthers.ajax.reload();
                                        }
                                        offComTrail.ajax.reload();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating offense and commendation!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            }
        });
    });

    if (typeof tableCashAdvance !== "undefined") {
        var dtCashAdvance = tableCashAdvance.DataTable({
            dom: '<"toolbar dt-toolbar_cash_advance">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_cash_advance"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "reference_no", title: "Reference No" },
                { data: "amt_applied", title: "Amount Applied" },
                { data: "purpose", title: "Purpose" },
                { data: "amt_approved", title: "Amount Approved" },
                { data: "created_dt", title: "Date Applied" },
                { data: "status", title: "Status" },
                { data: null, title: "Action", width: "8%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return cashAdvanceDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                let search_thread = null;
                $("#tbl-cash_advance_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableCashAdvance.dataTable().api();
                            const elem = $("#tbl-cash_advance_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });

        function cashAdvanceDataTableActions($id) {
            if ($id) {
                var _actionButton = "";
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditCashAdvance' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                if (jQuery.inArray("archive", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveCashAdvance' data-id='" +
                        $id +
                        "'><i class='la la-file-archive-o'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";
                return _actionButton;
            } else {
                return false;
            }
        }

    }

    if (typeof tableDocuments !== "undefined") {
        var dtDocuments = tableDocuments.DataTable({
            dom: '<"toolbar dt-toolbar_documents">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_documents"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "doc_type", title: "Type" },
                { data: "doc_filename", title: "Attachment" },
                { data: null, title: "Action", width: "8%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return documentsDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_documents").append(
                    "<button type='button' class='btn btn-sm btn-success mb-2 btnNew btnAddDocuments'><i class='la la-plus mr-1'></i>Add</button>"
                );

                let search_thread = null;
                $("#tbl-documents_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableDocuments.dataTable().api();
                            const elem = $("#tbl-documents_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
                
                getChecklist();
            }
        });

        function documentsDataTableActions($id) {
            if ($id) {
                var _actionButton = "";
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditDocuments' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                if (jQuery.inArray("archive", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveDocuments' data-id='" +
                        $id +
                        "'><i class='la la-file-archive-o'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";
                return _actionButton;
            } else {
                return false;
            }
        }

        $(document).on("click", ".btnAddDocuments", function () {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_documents/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    var modalContent = modalTempContent.find(".modal-content");
                    if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);
                        modalContent.find("#doc_type").select2({
                            width: "100%",
                            placeholder: "Select an option",
                            dropdownParent: modalTempContent
                        });
                        modalTempContent.modal("show");
                        employee_document_upload();
                        var url = baseUrl("hris/masterfile/upload_employee_documents");
                        $("#fileupload_document")
                            .fileupload({
                                url: url,
                                dataType: "json",
                                formData: { csrf_token: _csrf_hash, employee_id: tempDataId },
                                done: function (e, data) {
                                    var result = data.result;
                                    if (result.response) {
                                        modalContent.find("#document_attachment").val(result.filename);
                                        modalContent.find("#temp_fileupload").empty().text(result.filename);
                                        toastr.success(result.toastr_msg, "Upload Document File", 5000);
                                    } else {
                                        toastr.error(result.toastr_msg, "Upload Document File", 5000);
                                    }
                                }
                            })
                            .prop("disabled", !$.support.fileInput)
                            .parent()
                            .addClass($.support.fileInput ? undefined : "disabled");

                        $("#is-checklist").on('click', function(){
                            if($("#is-checklist").is(":checked")){
                                $("#checklist").css('display', 'block');
                                $("#non-checklist").css('display', 'none');
                            }else{
                                $("#checklist").css('display', 'none');
                                $("#non-checklist").css('display', 'block');
                            }
                        });

                        modalContent.find('#checklist_type').select2({
                            width: "100%",
                            placeholder: "Select an Option",
                            dropdownParent: modalTempContent,
                            ajax:{
                                url: baseUrl('hris/settings/get_checklists'),
                                global: false,
                                processResults: function (data) {
                                    return data;
                                },
                                delay: 500
                            }
                        }).on('select2:select', function(e){
                            const data = e.params.data;

                            modalContent.find('#checklistId').val(data.checklistId);
                        });

                        $.validate({
                            form: "#form-documents",
                            lang: "en",
                            onSuccess: function (form) {
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();

                                $.ajax({
                                    url: formUrl,
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
                                                "Employee document has been saved.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalTempContent.modal("hide");
                                            dtDocuments.ajax.reload();

                                            getChecklist();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating document!",
                                                5000
                                            );
                                        }

                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            });
        });

        function getChecklist(){
            $.ajax({
                url: baseUrl('hris/settings/employment_checklist'),
                dataType: "JSON",
                data: {
                    emp_id : tempDataId
                },
                success: function(response){
                    vmChecklist.row = Object.assign({}, response.data);
                    vmChecklist.count = response.data.length;
                }
            });
        }
    }

    if (typeof tableBackgroundCheck !== "undefined") {
        const dtBackgroundCheck = tableBackgroundCheck.DataTable({
            dom: '<"toolbar dt-toolbar_background_check">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_background_check"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "doc_filename", title: "Attachment" },
                { data: null, title: "Action", width: "8%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return backgroundCheckDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_background_check").append(
                    "<button type='button' class='btn btn-sm btn-success mb-2 btnNew btnAddBackgroundCheck'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-background_check_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableBackgroundCheck.dataTable().api();
                            const elem = $("#tbl-background_check_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });

        function backgroundCheckDataTableActions($id) {
            if ($id) {
                var _actionButton = "";
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditBackgroundCheck' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                if (jQuery.inArray("archive", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveBackgroundCheck' data-id='" +
                        $id +
                        "'><i class='la la-file-archive-o'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";
                return _actionButton;
            } else {
                return false;
            }
        }

        $(document).on("click", ".btnAddBackgroundCheck", function () {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_background_check/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    var modalContent = modalTempContent.find(".modal-content");
                    if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);
                        modalTempContent.modal("show");

                        var url = baseUrl("hris/masterfile/upload_employee_background_check");
                        $("#fileupload_background_check")
                            .fileupload({
                                url: url,
                                dataType: "json",
                                formData: { csrf_token: _csrf_hash, emp_id: tempDataId },
                                done: function (e, data) {
                                    var result = data.result;
                                    if (result.response) {
                                        modalContent.find("#background_check_attachment").val(result.filename);
                                        modalContent.find("#temp_fileupload").empty().text(result.filename);
                                        toastr.success(result.toastr_msg, "Upload Background Check File", 5000);
                                    } else {
                                        toastr.error(result.toastr_msg, "Upload Background Check File", 5000);
                                    }
                                }
                            })
                            .prop("disabled", !$.support.fileInput)
                            .parent()
                            .addClass($.support.fileInput ? undefined : "disabled");

                        $.validate({
                            form: "#form-background_check",
                            lang: "en",
                            onSuccess: function (form) {
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();

                                $.ajax({
                                    url: formUrl,
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
                                                "Employee background check has been saved.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalTempContent.modal("hide");
                                            dtBackgroundCheck.ajax.reload();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating background check!",
                                                5000
                                            );
                                        }

                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            });
        });
    }

    getJobDescription();

    function getJobDescription() {
        if (typeof currentJobDescription !== "undefined") {
            $.ajax({
                url: baseUrl("hris/masterfile/get_current_job_description/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    vmJobDesc.row = {};
                    vmJobDesc.is_multiple_position = false;

                    if (json.response) {
                        vmJobDesc.row = json.is_multiple_position == 1 ? {...json.data} : json;
                        vmJobDesc.is_multiple_position = json.is_multiple_position == 1 ? true : false;
                    }
                }
            });
        }
    }

    function getJobDescriptionv1() {
        if (typeof currentJobDescription !== "undefined") {
            var cJobDescription = currentJobDescription.find("#current-job_description");
            if (typeof cJobDescription !== "undefined" && cJobDescription.length == 1) {
                var tempJobDescription = function () {
                    $.ajax({
                        url: baseUrl("hris/masterfile/get_current_job_description/" + tempDataId),
                        dataType: "json",
                        success: function (json) {
                            if (json.response) {
                                var htmlData = "";
                                htmlData += "<h5 id='jobTitle'>";
                                htmlData += "Job Description for ";
                                htmlData += json.position_description;
                                htmlData += "</h5>";
                                htmlData += "<div id='jobDescription'>";
                                htmlData += json.data;
                                htmlData += "</div>";

                                cJobDescription.empty().append(htmlData);
                            } else {
                                cJobDescription.empty().text("No job description available!");
                            }
                        }
                    });
                }

                tempJobDescription();

                /*$(document).on("click", ".btnUpdateJobDescription", function () {
                    $.ajax({
                        url: baseUrl("hris/masterfile/get_modal_job_description/" + tempDataId),
                        dataType: "json",
                        success: function (json) {
                            var modalContent = modalTempContentLg.find(".modal-content");
                            if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                                modalContent.empty();
                                modalContent.append(json.html);
                                modalTempContentLg.modal("show");
 
                                $.validate({
                                    form: "#form-job_description",
                                    lang: "en",
                                    onSuccess: function (form) {
                                        var currentForm = form[0];
                                        var formUrl = currentForm.action;
                                        var formData = $(currentForm).serialize();
 
                                        $.ajax({
                                            url: formUrl,
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
                                                        "Employee job description has been saved.",
                                                        5000
                                                    );
                                                    currentForm.reset();
                                                    modalTempContentLg.modal("hide");
                                                    tempJobDescription();
                                                } else {
                                                    toastr.error(
                                                        json.toastr_msg,
                                                        "Error updating job description!",
                                                        5000
                                                    );
                                                }
 
                                                $(currentForm)
                                                    .find(".btn-submit")
                                                    .removeClass(
                                                        "m-btn--custom m-loader m-loader--light m-loader--right"
                                                    );
                                            }
                                        });
                                        return false;
                                    }
                                });
                            }
                        }
                    });
                });*/
            }
        }
    }

    /*if (typeof questionsDescription !== "undefined") {
        $(document).on("click", ".btnUpdateQuestions", function () {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_question_description/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    var modalContent = modalTempContentLg.find(".modal-content");
                    if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);
                        modalTempContentLg.modal("show");
 
                        $.validate({
                            form: "#form-questions",
                            lang: "en",
                            onSuccess: function (form) {
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();
 
                                $.ajax({
                                    url: formUrl,
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
                                                "Employment question(s) has been saved.",
                                                5000
                                            );
 
                                            var _respData = json.data;
                                            vmTabQuestions.vm_question = Object.assign({}, _respData);
 
                                            currentForm.reset();
                                            modalTempContentLg.modal("hide");
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating employment question(s)!",
                                                5000
                                            );
                                        }
 
                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            });
        });
    }*/

    if (typeof tablePerformance !== "undefined") {
        var dtBackgroundCheck = tablePerformance.DataTable({
            dom: '<"toolbar dt-toolbar_performance">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_performance"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "quarter", title: "Quarter" },
                { data: "range", title: "Range" },
                { data: "year", title: "Year" },
                { data: "filename", title: "Attachment" },
                { data: null, title: "Action", width: "8%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: "quarter",
                    defaultContent: "",
                    targets: 0,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var tempDataQuarter = "---";
                        switch (data) {
                            case "quarter_1":
                                tempDataQuarter = "1st Quarter";
                                break;
                            case "quarter_2":
                                tempDataQuarter = "2nd Quarter";
                                break;
                            case "quarter_3":
                                tempDataQuarter = "3rd Quarter";
                                break;
                            case "quarter_4":
                                tempDataQuarter = "4th Quarter";
                                break;
                            case "month_30":
                                tempDataQuarter = "3rd Month";
                                break;
                            case "month_45":
                                tempDataQuarter = "4.5 Month";
                                break;
                            default:
                                tempDataQuarter = "---";
                                break;
                        }
                        return tempDataQuarter;
                    }
                },
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return performanceDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_performance").append(
                    "<button type='button' class='btn btn-sm btn-success btnNew btnAddPerformance'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-performance_eval_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tablePerformance.dataTable().api();
                            const elem = $("#tbl-performance_eval_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });

        function performanceDataTableActions($id) {
            if ($id) {
                var _actionButton = "";
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditPerformance' data-id='" + $id + "'><i class='la la-edit'></i></button>";
                }
                if (jQuery.inArray("archive", _currentActions) !== -1) {
                    _actionButton +=
                        " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemovePerformance' data-id='" +
                        $id +
                        "'><i class='la la-file-archive-o'></i></button>";
                }
                _actionButton = (_actionButton) ? _actionButton : "---";
                return _actionButton;
            } else {
                return false;
            }
        }

        $(document).on("click", ".btnAddPerformance", function () {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_performance/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    var modalContent = modalTempContent.find(".modal-content");
                    var tempDatax = {};
                    if (typeof json.data !== "undefined" && typeof json.data == "object") {
                        tempDatax = Object.assign({}, json.data);
                    }

                    if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);
                        var select2Quarter = modalContent.find("select#quarter");
                        if (typeof select2Quarter !== "undefined" && select2Quarter.length == 1) {
                            var select2Data = select2Quarter.select2({
                                width: "100%",
                                placeholder: "Select an option",
                                dropdownParent: modalTempContent
                            });
                            select2Data.on("change", function (e) {
                                var self = $(e.target);
                                var currentValue = self.val();
                                var dtRange = tempDatax.date_range[currentValue];
                                $("p#temp_range").text(dtRange);
                                $("input#range").val(dtRange);
                                self.validate();
                            });
                        }

                        modalTempContent.modal("show");

                        var _minDate = "2015-01-01";
                        var cMinDate = moment().format("YYYY");
                        cMinDate = cMinDate - 1;
                        var _xmin = _minDate ? _minDate : moment(cMinDate + "-01-01").format("YYYY");

                        var dtPickerPerformanceDate = modalContent.find("#year").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy",
                            viewMode: "years",
                            minViewMode: "years",
                            autoclose: true,
                            startDate: moment(_xmin).format("YYYY"),
                            endDate: 'y'
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY");
                                var self = $(e.target);
                                self.validate();
                            });

                        var url = baseUrl("hris/masterfile/upload_employee_performance");
                        $("#fileupload_performance")
                            .fileupload({
                                url: url,
                                dataType: "json",
                                formData: { csrf_token: _csrf_hash, employee_id: tempDataId },
                                done: function (e, data) {
                                    var result = data.result;
                                    if (result.response) {
                                        modalContent.find("#performance_attachment").val(result.filename);
                                        modalContent.find("#temp_fileupload").empty().text(result.filename);
                                        toastr.success(result.toastr_msg, "Upload Performance Evaluation File", 5000);
                                    } else {
                                        toastr.error(result.toastr_msg, "Upload Performance Evaluation File", 5000);
                                    }
                                }
                            })
                            .prop("disabled", !$.support.fileInput)
                            .parent()
                            .addClass($.support.fileInput ? undefined : "disabled");

                        $.validate({
                            form: "#form-performance",
                            lang: "en",
                            onSuccess: function (form) {
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();

                                $.ajax({
                                    url: formUrl,
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
                                                "Employee performance evaluation has been saved.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalTempContent.modal("hide");
                                            dtBackgroundCheck.ajax.reload();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating performance evaluation!",
                                                5000
                                            );
                                        }

                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            });
        });
    }
    /****** employment data tab section ********/
}

$(document).ready(function () {
    validatePersonalEmployeeData();
    fileUploadPhoto();
    collapsibleAccordionIconSwitch();

    // commented out -- undefined/0 parameter --
    // displayDriversLicense();
    // getEmployeePerformanceRating();
    // commented out -- undefined/0 parameter --

    setTimeout(function () {
        triggerUpdateSpouse();
        updateUmidTypeSwitch();
    }, 500);

    if(screen.width > 560 && screen.width < 960){
        $("#frmEditEmployeeData label").addClass("text-right");
        $("#frmEditEmploymentData label").addClass("text-right");
        $("div label").addClass("text-right");
    }

    dtWorkExperience = dtTableWorkExperience();
});

const dtTableWorkExperience = function (){
    if (typeof tableWorkExperience !== "undefined") {
        function getWorkExperienceDataTableActions($id) {
            let actionButton = "";
            if (typeof _currentActions !== "undefined") {
                if ($.inArray("edit", _currentActions) !== -1) {
                    actionButton += `<button type="button" class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditWorkExperience" data-id="${$id}"><i class="la la-edit"></i></button>`;
                }
                if ($.inArray("archive", _currentActions) !== -1) {
                    actionButton += `<button type="button" class="btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveWorkExperience" data-id="${$id}"><i class="la la-file-archive-o"></i></button>`;
                }
            }
            return (actionButton) ? actionButton : "---";
        }

        $(document).on("click", ".btnAddWorkExperience", function () {
            $.ajax({
                url: baseUrl("hris/masterfile/get_modal_work_experience/" + tempDataId),
                dataType: "json",
                success: function (json) {
                    var modalContent = modalTempContent.find(".modal-content");
                    if (typeof modalContent !== "undefined" && typeof json.html !== "undefined") {
                        modalContent.empty();
                        modalContent.append(json.html);
                        modalTempContent.modal("show");

                        var dtPickerWorkFromDate = modalContent.find("#work_from").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy",
                            viewMode: "years",
                            minViewMode: "years",
                            autoclose: true
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY");
                                var self = $(e.target);
                                self.validate();
                            });

                        var dtPickerWorkToDate = modalContent.find("#work_to").datepicker({
                            todayHighlight: true,
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy",
                            viewMode: "years",
                            minViewMode: "years",
                            autoclose: true
                        })
                            .on("changeDate", function (e) {
                                var currentDt = moment(e.date).format("YYYY");
                                var self = $(e.target);
                                self.validate();
                            });

                        $.validate({
                            form: "#form-work_experience",
                            lang: "en",
                            onSuccess: function (form) {
                                var currentForm = form[0];
                                var formUrl = currentForm.action;
                                var formData = $(currentForm).serialize();

                                $.ajax({
                                    url: formUrl,
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
                                                "Employee work experience has been saved.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalTempContent.modal("hide");
                                            dtWorkExperience.ajax.reload();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating employee work experience!",
                                                5000
                                            );
                                        }

                                        $(currentForm)
                                            .find(".btn-submit")
                                            .removeClass(
                                                "m-btn--custom m-loader m-loader--light m-loader--right"
                                            );
                                    }
                                });
                                return false;
                            }
                        });
                    }
                }
            });
        });
        return tableWorkExperience.DataTable({
            dom: '<"toolbar dt-toolbar_work_experience">frtlip',
            serverSide: true,
            processing: true,
            ordering: false,
            ajax: {
                url: baseUrl("hris/masterfile/get_employee_work_experience"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, emp_id: tempDataId }
            },
            columns: [
                { data: "work_company", title: "Company" },
                { data: "work_from", title: "From" },
                { data: "work_to", title: "To" },
                { data: "work_position", title: "Position" },
                { data: "old_idno", title: "ID No" },
                { data: "work_status", title: "Status" },
                { data: "work_reason", title: "Reason For Leaving" },
                { data: null, title: "Action", width: "8%", className: "text-center" }
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return getWorkExperienceDataTableActions(row.id);
                    }
                },
                {
                    targets: "_all",
                    defaultContent: ""
                }
            ],
            initComplete: function () {
                $(".dt-toolbar_work_experience").append(
                    "<button type='button' class='btn btn-sm btn-success mb-2 btnNew btnAddWorkExperience'><i class='la la-plus mr-1'></i>New</button>"
                );

                let search_thread = null;
                $("#tbl-work_experiences_list_filter input")
                    .unbind()
                    .bind("input", function (e) {
                        clearTimeout(search_thread);
                        search_thread = setTimeout(function () {
                            const dtTableApi = tableWorkExperience.dataTable().api();
                            const elem = $("#tbl-work_experiences_list_filter input");
                            return dtTableApi.search($(elem).val()).draw();
                        }, 1000);
                    });
            }
        });
    }else{ return false; }
}


$(document).on("change", ".m--partner_switch", function () {
    var _tempSelf = $(this);
    var _currentValue = _tempSelf.val();
    var _identifier = _tempSelf.data("identifier");
    var _tempObjects = $("." + _identifier);

    if (typeof _tempObjects !== "undefined") {
        if (_currentValue == "0") {
            _tempObjects.each(function (i, v) {
                var isTextbox = $(v).is("input[type=text]");
                if (typeof isTextbox !== "undefined" && isTextbox == true) {
                    setTimeout(function () {
                        $(v).val(null);
                    }, 100);
                }
            });
            _tempObjects.prop("disabled", true);
        }
        if (_currentValue !== "0") {
            _tempObjects.each(function (i, v) {
                var cname = $(v).attr("name");
                var _split = cname.split("_");
                if (_currentValue == "2") {
                    _split[0] = "partners";
                } else if (_currentValue == "1") {
                    _split[0] = "spo";
                }
                cname = _split.join("_");
                $(v).prop("name", cname);
            });
            _tempObjects.prop("disabled", false);
        }
    }
});

var validatePersonalEmployeeData = function () {
    $.validate({
        form: "#frmEditEmployeeData",
        lang: "en",
        onSuccess: function (form) {
            var currentForm = form[0];
            var formUrl = currentForm.action;
            var formData = $(currentForm).serialize();

            $.ajax({
                url: formUrl,
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
                            "Employee data has been updated.",
                            5000
                        );
                        var _respData = json.data;
                        var _newData = Object.assign(
                            {},
                            {
                                display_name: _respData.display_name,
                                display_email: _respData.display_email,
                                display_avatar: _respData.pic_filename
                            }
                        );
                        leftPanel.left_pane = _newData;
                        _tempContentData.data = Object.assign({}, json.data);
                        $("#change_personal_info").val(0);
                        $("#personal_information i").remove();
                    } else {
                        toastr.error(
                            json.toastr_msg,
                            "Error updating employee data!",
                            5000
                        );
                    }

                    $(currentForm)
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                }
            });
            return false;
        }
    });

    $.validate({
        form: "#frmEditAdditionalData",
        lang: "en",
        onSuccess: function (form) {
            var currentForm = form[0];
            var formUrl = currentForm.action;
            var formData = $(currentForm).serialize();
        
            $.ajax({
                url: formUrl,
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
                            "Employee data has been updated.",
                            5000
                        );

                        var _respData = json.data;
                        vmTab2.vm_tab2 = Object.assign({}, _respData);
                        $("#change_additional_info").val(0);
                        $("#additional_information i").remove();
                    } else {
                        toastr.error(
                            json.toastr_msg,
                            "Error updating employee data!",
                            5000
                        );
                    }

                    $(currentForm)
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                }
            });
            return false;
        }
    });

    $.validate({
        form: "#frmEditEmploymentData",
        lang: "en",
        onSuccess: function (form) {

            var currentForm = form[0];
            var formUrl = currentForm.action;
            var formData = $(currentForm).serialize();

            const isMultiple = $("#is_multiple_position").is(":checked");
            let formDataObj = {};
            $(currentForm).serializeArray().forEach(function (item) {
                formDataObj[item.name] = item.value;
            });
            let oldDate = (currentResignDate && currentResignDate !== "null") ? currentResignDate : "";
            let oldClassification = (currentClassification && currentClassification !== "null") ? currentClassification : "";

            let newDate = formDataObj.resignation_effective_date ?? ""
            let newClassification = formDataObj.employee_status ?? ""
            
            console.log(oldClassification, newClassification, oldDate, newDate);
            if(oldClassification != newClassification && newClassification.toLowerCase() == 'inactive' || oldDate != newDate){
                $("#m_datepicker-date_resign").attr("readonly", true);
                const table = $("#tbl-loans").DataTable();
                const loans = table.data().toArray()
                .map(row => {
                    const balance = parseFloat(row.amount) - parseFloat(row.total_amount_paid);
                    return { ...row, balance };
                })
                .filter(row => row.active === "1" || row.balance !== 0);

                if (loans.length > 0) {
                    $("#currentLoan").modal("show");
                    $('#current_loan_table').DataTable({
                        data: loans,
                        destroy: true,
                        searching: false,
                        paging: false,
                        ordering: false,
                        info: false,
                        columns: [
                            { data: 'loan_name' },
                            { 
                                data: 'amount',
                                render: (data) => `₱${parseFloat(data).toLocaleString()}`
                            },
                            { 
                                data: 'total_amount_paid',
                                render: (data) => `₱${parseFloat(data).toLocaleString()}`
                            },
                            { 
                                data: 'balance',
                                render: (data) => `₱${parseFloat(data).toLocaleString()}`
                            },
                            { 
                                data: 'remarks',
                                defaultContent: ''
                            }
                        ],
                        columnDefs: [
                            { targets: [1,2,3], className: "text-right" }
                        ]
                    });

                    $('#currentLoan').data('formUrl', formUrl);
                    $('#currentLoan').data('formData', formData);
                    $('#currentLoan').data('formElement', currentForm);
                    $('#currentLoan').data('newClassification', newClassification);
                    $('#currentLoan').data('newDate', newDate);

                    $("#btnConfirmLoan").off("click").on("click", function () {
                        const url = $('#currentLoan').data('formUrl');
                        const data = $('#currentLoan').data('formData');
                        const formEl = $('#currentLoan').data('formElement');
                        $("#currentLoan").modal("hide");
                        saveEmploymentData(url, data, formEl);
                        currentClassification = $('#currentLoan').data('newClassification');
                        currentResignDate = $('#currentLoan').data('newDate');
                        vmPrimary.isSortOnly = false;
                        sendEmail();
                    });
                    return false; 
                }
                
                
            }

            if (isMultiple && vmPrimary.positions.length > 0 && !vmPrimary.isSortOnly) {
                $("#set_primary_position").modal('show');
                PortletDraggable.init();

                $('#set_primary_position').data('formUrl', formUrl);
                $('#set_primary_position').data('formData', formData);
                $('#set_primary_position').data('formElement', currentForm);

                return false;
            } else {
                saveEmploymentData(formUrl, formData, currentForm);
                currentClassification = newClassification;
                currentResignDate = newDate;
                vmPrimary.isSortOnly = false;
                return false;
            }

        }
    });
};

function serializedArrayToObjectKeyPairs(array) {
    return array.reduce((acc, { name, value }) => ({ ...acc, [name]: value }), {});
}

var dtPicker = $(
    "#m_datepicker-birthdate, #m_datepicker-date_end_prob, #m_datepicker-date_regular, #m_datepicker-date_end"
)
    .datepicker({
        todayHighlight: true,
        orientation: "bottom left",
        templates: {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        },
        format: "yyyy-mm-dd",
        autoclose: true
    })
    .on("changeDate", function (e) {
        var currentDt = moment(e.date).format("YYYY-MM-DD");
    });

$("#m_datepicker-date_hired")
    .datepicker({
        todayHighlight: true,
        orientation: "bottom left",
        templates: {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        },
        format: "yyyy-mm-dd",
        autoclose: true
    }).on("changeDate", function (e) {
        const probeeEndDate = moment(e.date).add(180, 'days').format('YYYY-MM-DD');
        $("#m_datepicker-date_end_prob").val(probeeEndDate).datepicker('update');
        $("#m_datepicker-date_hired").val(e.date).datepicker('update');

        vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { date_start: moment(e.date).format('YYYY-MM-DD'), date_end_prob: probeeEndDate });
        setTimeout(() => { $(e.target).validate(); }, 250);
    });

    $("#m_datepicker-salary_effective_date")
    .datepicker({
        todayHighlight: true,
        orientation: "bottom left",
        templates: {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        },
        format: "yyyy-mm-dd",
        autoclose: true
    });    

var fileUploadPhoto = function () {
    var url = baseUrl("hris/masterfile/upload_employee_avatar");
    if (tempData !== undefined) {
        $("#fileupload")
            .fileupload({
                url: url,
                dataType: "json",
                formData: { csrf_token: _csrf_hash, employee_id: tempDataId },
                done: function (e, data) {
                    var result = data.result;
                    if (result.response) {
                        var avatarImage = result.added_image;
                        var renderImage = result.render_image;

                        leftPanel.left_pane = Object.assign({}, leftPanel.left_pane, { display_avatar: avatarImage });

                        toastr.success(result.toastr_msg, "Upload Image", 5000);
                        $("#modalUpdatePhoto").modal("hide");
                    } else {
                        toastr.error(result.toastr_msg, "Upload Image", 10000);
                    }
                },
                progressall: function (e, data) {
                    $("#progress").show();
                    var progress = parseInt((data.loaded / data.total) * 100, 10);
                    var progressTotal = 0;

                    var steps = setInterval(function () {
                        progressTotal += 10;
                        $("#progress .progress-bar").css("width", progressTotal + "%");
                        if (progressTotal == 100) {
                            clearInterval(steps);
                            progressTotal = 0;
                            setTimeout(function () {
                                $("#progress .progress-bar").css("width", progressTotal + "%");
                            }, 1500);
                        }
                    }, 10);

                    if (progress == 100) {
                        setTimeout(function () {
                            $("#progress").hide();
                        }, 1000);
                    }
                }
            })
            .prop("disabled", !$.support.fileInput)
            .parent()
            .addClass($.support.fileInput ? undefined : "disabled");
    }
};

var triggerUpdateSpouse = function () {
    var _spouse = $(".m--partner_switch");

    _spouse.each(function (i, v) {
        var _tempSelf = $(v);
        var _currentValue = _tempSelf.val();
        var _identifier = _tempSelf.data("identifier");
        var _tempObjects = $("." + _identifier);
        var _isChecked = _tempSelf.is(":checked");

        if (typeof _tempObjects !== "undefined") {
            if (_currentValue == "0" && _isChecked == true) {
                _tempObjects.each(function (i, v) {
                    var isTextbox = $(v).is("input[type=text]");
                    if (typeof isTextbox !== "undefined" && isTextbox == true) {
                        $(v).val("");
                    }
                });
                _tempObjects.prop("disabled", true);
            }
            if (_currentValue !== "0" && _isChecked == true) {
                _tempObjects.prop("disabled", false);
            }
        }
    });
};

var updatePartnerDetailSwitch = function () {
    var _formPersonalInformation = $("form#frmEditEmployeeData");
    var _formAdditionalInformation = $("form#frmEditAdditionalData");
    if (typeof _formPersonalInformation !== "undefined" && typeof _formAdditionalInformation !== "undefined") {
        var _partnerSwitch = _formAdditionalInformation.find(".m-input--partners_detail");
        _partnerSwitch.each(function () {
            var partnerThis = $(this);
            partnerThis.prop("checked", false);
        });

        var selectStatus = _formPersonalInformation.find("#civil_stat");
        var radioPtSingle = _formAdditionalInformation.find("#pt_single");
        var radioPtMarried = _formAdditionalInformation.find("#pt_married");
        var radioPtPartner = _formAdditionalInformation.find("#pt_partner");
        var currentStatus = selectStatus.val();

        setTimeout(function () {
            /*if (currentStatus == "Single") {
                radioPtSingle.prop("checked", true);
            }
            if (currentStatus == "Married") {
                if (vmTab2.vm_tab2.partner_type === 1) {
                    radioPtMarried.prop("checked", true);
                    console.log('married');
                } else {
                    radioPtPartner.prop("checked", true);
                    console.log('partner');
                }
            }*/

            if (vmTab2 !== undefined) {
                if (parseInt(vmTab2.vm_tab2.partner_type) === 1) {
                    radioPtMarried.prop("checked", true);
                } else if (parseInt(vmTab2.vm_tab2.partner_type) === 2) {
                    radioPtPartner.prop("checked", true);
                } else {
                    radioPtSingle.prop("checked", true);
                }
            }

            triggerUpdateSpouse();
        }, 200);

        selectStatus.on("change", function () {
            var selectThis = $(this);
            currentValue = selectThis.val();

            _partnerSwitch.each(function () {
                var partnerThis = $(this);
                partnerThis.prop("checked", false);
            });

            setTimeout(function () {
                /*if (currentValue == "Single") {
                    radioPtSingle.prop("checked", true);
                }
                if (currentValue == "Married") {
                    if (vmTab2.vm_tab2.partner_type === 1) {
                        radioPtMarried.prop("checked", true);
                    } else {
                        radioPtPartner.prop("checked", true);
                    }
                }*/

                if (parseInt(vmTab2.vm_tab2.partner_type) === 1) {
                    radioPtMarried.prop("checked", true);
                } else if (parseInt(vmTab2.vm_tab2.partner_type) === 2) {
                    radioPtPartner.prop("checked", true);
                } else {
                    radioPtSingle.prop("checked", true);
                }
                triggerUpdateSpouse();
            }, 200);
        });
    }
}

var updateUmidTypeSwitch = function () {
    var _formAdditionalInformation = $("form#frmEditAdditionalData");
    if (typeof _formAdditionalInformation !== "undefined") {
        var umidNoSwitch = _formAdditionalInformation.find("#umid_no");
        var sssNoMask = _formAdditionalInformation.find("#sss_no");
        umidNoSwitch.on("change", function () {
            var radioThis = $(this);
            var isPropChecked = radioThis.prop("checked");
            if (isPropChecked == true) {
                sssNoMask.inputmask("mask", { "mask": "9999-9999999-9" });
            } else {
                sssNoMask.inputmask("mask", { "mask": "99-9999999-9" });
            }
        });
    }
}

$('#table-employee')
    .on('click', '.btnRemoveEmployee', function () {
        const id = $(this).attr('data-id');

        $('#frm-employee-archive-dialog').attr('action', baseUrl('hris/archive/archive_employee/' + id));

        const dialog = $('#employee-archive-remarks-dialog');
        dialog.modal('show');
    });

function archiveEmployee(el) {
    const form = $(el);
    const url = form.attr('action');
    const formData = new FormData(el);
    formData.append('csrf_token', _csrf_hash);

    const id = url.split('/').pop();
    const tr = $('.btnRemoveEmployee[data-id="' + id + '"]').closest('tr');

    if (form.isValid()) {
        $.ajax({
            url,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message, 'Employee Archived.', 10000);
                    tr.remove();
                } else {
                    toastr.error(response.message, 'Error.', 10000);
                }

                $('#employee-archive-remarks-dialog').modal('hide');
            }
        });
    }
}

function openChangeCompanyDialog() {
    $('input[name="current_company"]').val(vmTab3.vm_tab3.company);
    $('input[name="current_position"]').val(vmTab3.vm_tab3._position);
    $('input[name="current_department"]').val(vmTab3.vm_tab3.department);
    $('input[name="current_status"]').val(vmTab3.vm_tab3._status);
    $('input[name="emp_id"]').val(vmTab3.vm_tab3.id);

    const date_start = new Date(vmTab3.vm_tab3.date_start);

    $('input[name="work_from"]').val(date_start.getFullYear());

    changeEmployeeCompanyDialog.modal("show");
}

function changeEmployeeCompany(_form) {
    const form = $(_form);
    const url = form.attr("action");
    const formData = new FormData(_form);
    formData.append("csrf_token", _csrf_hash);

    if (form.isValid()) {
        $.ajax({
            url,
            dataType: "JSON",
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                if (response.success) {
                    _tempContentData.data = Object.assign({}, response.data);
                    vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, response.data);
                    tableWorkExperience.DataTable().ajax.reload();
                    changeEmployeeCompanyDialog.modal("hide");
                    $("#m--input-position_id").val(vmTab3.vm_tab3.position).trigger('change');
                    $("#m--input-department_id").val(vmTab3.vm_tab3.department_id).trigger('change');

                    $("#change-company-company_id").val('').trigger('change');
                    $("#change-company-department_id").val('').trigger('change');
                    $("#change-company-position").val('').trigger('change');

                    toastr.success(response.message, "Transfer Company", 10000);
                } else {
                    toastr.error(response.message, "Error", 10000);
                }
            }
        });
    }
}

function removeProfilePicture() {
    const id = tempDataId;
    $.ajax({
        url: baseUrl("hris/masterfile/open_confirm_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {
                title: "Confirm Remove Picture",
                message: "Are you sure to remove profile picture?",
                action: "hris/masterfile/remove_profile_picture/" + id + "/remove-profile-picture",
                color: "btn-danger"
            },
            path: "ams/confirmation_dialog",
            function_name: "passDataToDialog"
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}

if (typeof tblSkills !== "undefined") {
    var dtSkills = tblSkills.DataTable({
        dom: '<"toolbar dt-toolbar_skills">frtlip',
        serverSide: true,
        processing: true,
        ordering: false,
        autoWidth: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_employee_skills"),
            type: "post",
            dataType: "json",
            data: { csrf_token: _csrf_hash, emp_id: tempDataId }
        },
        columns: [
            { data: "skills", title: "Skills" },
            { data: null, title: "Action", width: "15%", className: "text-center" }
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return skillTableAction(row.id);
                }
            },
            {
                targets: "_all",
                defaultContent: ""
            }
        ],
        initComplete: function () {
            $(".dt-toolbar_skills").append(
                "<button type='button' " +
                "        class='btn btn-sm btn-success mb-2 btnNew btnAddSkills'>" +
                "           <i class='la la-plus mr-1'></i>New" +
                "</button>"
            );

            let search_thread = null;
            $("#tbl-skills-list_filter input")
                .unbind()
                .bind("input", function (e) {
                    clearTimeout(search_thread);
                    search_thread = setTimeout(function () {
                        const dtTableApi = tblSkills.dataTable().api();
                        const elem = $("#tbl-skills-list_filter input");
                        return dtTableApi.search($(elem).val()).draw();
                    }, 1000);
                });
        },
    });

    function skillTableAction($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' " +
                "         class='btn btn-default m-btn m-btn--hover-accent m-btn--icon " +
                "                m-btn--icon-only m-btn--pill btnEditSkill' " +
                "         data-id='" + $id + "'>" +
                "           <i class='la la-edit'></i>" +
                " </button>";
            _actionButton +=
                " <button type='button' " +
                "         class='btn btn-default m-btn m-btn--hover-warning " +
                "                m-btn--icon m-btn--icon-only m-btn--pill btnRemoveSkill' " +
                "         data-id='" + $id + "'>" +
                "           <i class='la la-file-archive-o'></i>" +
                " </button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $(document)
        .on("click", ".btnAddSkills", function () {
            $("#add-skill-modal").modal("show");
        });

    $("#tbl-skills-list")
        .on("click", ".btnEditSkill", function () {
            const id = $(this).attr('data-id');
            const row = $(this).closest('tr');
            const skills = row.find("td:eq(0)").text();

            $("#frm-edit-skill").find("[name='id']").val(id);
            $("#frm-edit-skill").find("[name='skills']").val(skills);
            $("#edit-skill-modal").modal("show");
        });
}

function addSkill(form) {
    const url = baseUrl('hris/masterfile/add_skill');
    const _form = $(form);
    const formData = new FormData(form);
    formData.append("emp_id", tempDataId);

    if (_form.isValid()) {
        $.ajax({
            url,
            type: "POST",
            dataType: "JSON",
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                if (response !== undefined) {
                    if (response.success) {
                        toastr.success(response.message, response.title, 10000);
                    } else {
                        toastr.error(response.message, response.title, 10000);
                    }
                }

                _form.resetForm();
                $("#add-skill-modal").modal("hide");
                dtSkills.ajax.reload();
            }
        });
    }
}

function editSkill(form) {
    const url = baseUrl('hris/masterfile/edit_skill');
    const _form = $(form);
    const formData = new FormData(form);

    if (_form.isValid()) {
        $.ajax({
            url,
            type: "POST",
            dataType: "JSON",
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                if (response !== undefined) {
                    if (response.success) {
                        toastr.success(response.message, response.title, 10000);
                    } else {
                        toastr.error(response.message, response.title, 10000);
                    }
                }

                _form.resetForm();
                $("#edit-skill-modal").modal("hide");
                dtSkills.ajax.reload();
            }
        });
    }
}

/* --START-- SALARY HISTORY */
if (typeof tblSalaryHistory !== "undefined") {
    var dtSalaryHistory = tblSalaryHistory.DataTable({
        dom: '<"toolbar dt-toolbar-salary-history">frtlip',
        serverSide: true,
        processing: true,
        ordering: false,
        autoWidth: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_employee_salary_history"),
            type: "post",
            dataType: "json",
            data: { csrf_token: _csrf_hash, emp_id: tempDataId }
        },
        columns: [
            {
                data: "sal_date",
                title: "Date",
                width: "10%"
            },
            {
                data: "sal_rate",
                title: "Rate",
                width: "10%"
            },
            {
                data: "position",
                title: "Position"
            },
            {
                data: "sal_remarks",
                title: "Remarks"
            },
            {
                data: null,
                title: "Action",
                width: "15%",
                className: "text-center"
            }
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return historySalaryAction(row.id, row.position_id);
                }
            },
            {
                targets: "_all",
                defaultContent: ""
            }
        ],
        initComplete: function () {
            $(".dt-toolbar-salary-history")
                .append(
                    "<button type='button' " +
                    "        class='btn btn-sm btn-success btnNew btnAddSalaryHistory'>" +
                    "           <i class='la la-plus mr-1'></i>" +
                    "           New" +
                    "</button>"
                );

            let search_thread = null;
            $("#tbl-salary-history_filter input")
                .unbind()
                .bind("input", function (e) {
                    clearTimeout(search_thread);
                    search_thread = setTimeout(function () {
                        const dtTableApi = tblSalaryHistory.dataTable().api();
                        const elem = $("#tbl-salary-history_filter input");
                        return dtTableApi.search($(elem).val()).draw();
                    }, 1000);
                });
        },
    });

    function historySalaryAction($id, position_id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' " +
                "         class='btn btn-default m-btn m-btn--hover-accent m-btn--icon " +
                "                m-btn--icon-only m-btn--pill btnEditSalaryHistory btnEdit' " +
                "         data-id='" + $id + "' data-position-id='" + position_id + "'>" +
                "           <i class='la la-edit'></i>" +
                " </button>";
            _actionButton +=
                " <button type='button' " +
                "         class='btn btn-default m-btn m-btn--hover-warning " +
                "                m-btn--icon m-btn--icon-only m-btn--pill btnRemoveSalaryHistory btnArchive' " +
                "         data-id='" + $id + "'>" +
                "           <i class='la la-file-archive-o'></i>" +
                " </button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $(document)
        .on("click", ".btnAddSalaryHistory", function () {
            $("#add-salary-emp-id").val(tempDataId);
            $("#add-salary-history-modal").modal("show");
        });

    if (typeof $(".money") !== "undefined" && typeof $(".money").maskMoney == "function") {
        $(".money").maskMoney({ thousands: ',', decimal: '.', allowZero: false });
    }

    $(".sal_date_container")
        .datepicker({
            todayHighlight: true,
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            },
            autoclose: true
            // format: "mm/dd/yyyy",
            // todayHighlight: true,
            // orientation: "bottom left",
            // templates: {
            //     leftArrow: '<i class="la la-angle-left"></i>',
            //     rightArrow: '<i class="la la-angle-right"></i>'
            // },
            // autoclose: true
        })
        .on('changeDate', function (e) {
            $(".sal_date_container").datepicker("update", moment(e.date).format("MM/DD/YYYY"));
        });


    $("#frm-add-salary-history [name='sal_position']")
        .select2({
            placeholder: "Select Position",
            width: "100%",
            ajax: {
                url: baseUrl("hris/masterfile/get_position_select2_data"),
                dataType: "JSON",
                delay: 500
            },
            dropdownParent: $("#add-salary-history-modal")
        });

    $("#frm-edit-salary-history [name='sal_position']")
        .select2({
            placeholder: "Select Position",
            width: "100%",
            ajax: {
                url: baseUrl("hris/masterfile/get_position_select2_data"),
                dataType: "JSON",
                delay: 500
            },
            dropdownParent: $("#edit-salary-history-modal")
        });

    tblSalaryHistory
        .on("click", ".btnEditSalaryHistory", function () {
            const id = $(this).attr('data-id');
            const position_id = $(this).attr("data-position-id");
            const row = $(this).closest('tr');
            $("#frm-edit-salary-history [name='sal_position']").val("").trigger("change");

            let sal_date = row.find("td:eq(0)").text();
            sal_date = sal_date ? moment(sal_date).format("MM/DD/YYYY") : "";

            const position = row.find("td:eq(2)").text();

            const form = $("#frm-edit-salary-history");

            form.find("[name='id']").val(id);
            form.find("[name='sal_date']").val(sal_date);
            form.find(".sal_date_container").datepicker("update", sal_date);

            let rate = row.find("td:eq(1)").text();
            rate = rate ? parseFloat(rate).toLocaleString(undefined, { minimumFractionDigits: 2 }) : "";
            form.find("[name='sal_rate']").val(rate);
            form.find("[name='sal_remarks']").val(row.find("td:eq(3)").text());

            if (!isNaN(position_id)) {
                const option = new Option(position, position_id, false, true);
                $("#frm-edit-salary-history [name='sal_position']").append(option).trigger("change");
            }

            $("#edit-salary-history-modal").modal("show");
        });

    $.validate({
        form: "#frm-add-salary-history",
        lang: "en",
        scrollToTopOnError: false,
        onSuccess: function (form) {
            const currentForm = $(form);
            var formData = currentForm.serialize();

            $.ajax({
                url: baseUrl("hris/masterfile/add_salary_history"),
                type: "post",
                dataType: "json",
                data: formData,
                beforeSend: function () {
                    currentForm
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (json) {
                    if (json) {
                        toastr.success("Salary saved.", "Salary successfully saved.", 10000);
                        currentForm.resetForm();
                        $("[name='sal_position']").val('').trigger('change');
                        $("#add-salary-history-modal").modal("hide");
                        dtSalaryHistory.ajax.reload();
                    } else {
                        toastr.error("Error", "Error saving new salary!", 10000);
                    }

                    currentForm
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                }
            });
            return false;
        }
    });

    $.validate({
        form: "#frm-edit-salary-history",
        lang: "en",
        scrollToTopOnError: false,
        onSuccess: function (form) {
            const currentForm = $(form);
            var formData = currentForm.serialize();

            $.ajax({
                url: baseUrl("hris/masterfile/edit_salary_history"),
                type: "post",
                dataType: "json",
                data: formData,
                beforeSend: function () {
                    currentForm
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (json) {
                    if (json) {
                        toastr.success("Salary Updated.", "Salary successfully updated.", 10000);
                        currentForm.resetForm();
                        currentForm.find("[name='sal_position']").val('').trigger('change');
                        $("#edit-salary-history-modal").modal("hide");
                        dtSalaryHistory.ajax.reload();
                    } else {
                        toastr.error("Error", "Error updating salary!", 10000);
                    }

                    currentForm
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                }
            });
            return false;
        }
    });
}
/* --END-- SALARY HISTORY */

/* --START-- ACCOUNTABILITY */
if (typeof tblAccountability !== "undefined") {
    var dtAccountability = tblAccountability.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        ordering: false,
        autoWidth: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_employee_accountability"),
            type: "post",
            dataType: "json",
            data: function(d){
                d.csrf_token = _csrf_hash;
                d.emp_id = tempDataId;
                d.status = acctgStatus;
                return d;
            }
            // data: { csrf_token: _csrf_hash, emp_id: tempDataId, status: acctgStatus },
        },
        columns: [
            {
                data: "reference_no",
                title: "Reference #",
                width: "10%"
            },
            {
                data: "asset_code",
                title: "Asset Code",
                width: "10%"
            },
            {
                data: "aname",
                title: "Asset Name",
            },
            {
                data: "amount",
                title: "Amount",
                width: "10%",
                render: function (data) {
                    return parseFloat(data).toLocaleString(undefined, { minimumFractionDigits: 2 });
                },
            },
            {
                data: "is_returned",
                title: "Returned",
                width: "10%",
                render: function (data) {
                    const badgeClass = parseInt(data) === 0 ? "m-badge--warning" : "m-badge--success";
                    const text = parseInt(data) === 0 ? "No" : "Yes";
                    return "<span class='m-badge " + badgeClass + " m--font-boldest px-2'>" + text + "</span>";
                },
            },
            {
                data: "date_returned",
                title: "Date",
                width: "15%",
                render: function (data) {
                    if (data === "0000-00-00") {
                        return "--";
                    } else {
                        return moment(data).format("MMM. DD, YYYY");
                    }
                }
            },
        ],
        buttons: [
            'copy',
            {
                extend: 'pdf',
                filename: function(){
                    var _name = _user.firstname + ' ' + _user.lastname;
                    return _name.toUpperCase() + ' - ' + exported_acctg + ' ACCOUNTABILITY';
                },
                title: function(){
                    return exported_acctg + ' ACCOUNTABILITY REPORT';
                },
                messageTop: function(){
                    var html = '';

                    var _name = _user.firstname + ' ' + _user.lastname;

                    html += 'ISSUED TO: ' + _name.toUpperCase() + '\n';
                    html += 'POSITION: ' + _user.pos_description + ' \t \t';
                    html += 'DEPARTMENT: ' + _user.department + ' \t \t';
                    html += 'COMPANY: ' + _user.company;

                    return html;
                }
            },
            {
                extend: 'excelHtml5',
                filename: function(){
                    var _name = _user.firstname + ' ' + _user.lastname;
                    return _name.toUpperCase() + ' - ' + exported_acctg + ' ACCOUNTABILITY';
                },
                title: function(){
                    return '';
                },
                customize: function(xlsx){
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var downrows = 5;
                    var clRow = $('row', sheet);
                    
                    var _name = _user.firstname + ' ' + _user.lastname;

                    clRow.each(function () {
                        var attr = $(this).attr('r');
                        var ind = parseInt(attr);
                        ind = ind + downrows;
                        $(this).attr("r",ind);
                    });

                    $('row c ', sheet).each(function () {
                        var attr = $(this).attr('r');
                        var pre = attr.substring(0, 1);
                        var ind = parseInt(attr.substring(1, attr.length));
                        ind = ind + downrows;
                        $(this).attr("r", pre + ind);
                    });
             
                    function Addrow(index,data) {
                        msg ='<row r="'+index+'">'
                        for(i=0; i < data.length;i++){
                            var key=data[i].k;
                            var value=data[i].v;
                            msg += '<c t="inlineStr" r="' + key + index + '" s="51">';
                            msg += '<is>';
                            msg +=  '<t>'+value+'</t>';
                            msg+=  '</is>';
                            msg+='</c>';
                        }
                        msg += '</row>';

                        return msg;
                    }
             
                    var company = "";
                    if(_user.company == 'GC&C'){
                        company = "GCC";
                    }else{
                        company = _user.company;
                    }

                    let mergeCells = $('mergeCells', sheet);
                    mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                        attr: {
                            ref: 'A1' + ':' + 'F1',
                        },
                    }));
                    mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                        attr: {
                            ref: 'B2' + ':' + 'C2',
                        },
                    }));
                    mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                        attr: {
                            ref: 'B3' + ':' + 'C3',
                        },
                    }));
                    mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                        attr: {
                            ref: 'B4' + ':' + 'C4',
                        },
                    }));
                    mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                        attr: {
                            ref: 'B5' + ':' + 'C5',
                        },
                    }));

                    //insert
                    var r1 = Addrow(1, [{ k: 'A', v: exported_acctg + ' ACCOUNTABILITY REPORT' }, { k: 'B', v: '' }, { k: 'C', v: '' }, { k: 'D', v: '' }, { k: 'E', v: '' }, { k: 'F', v: '' }]);
                    var r2 = Addrow(2, [{ k: 'A', v: 'ISSUED TO: ' }, { k: 'B', v:  _name.toUpperCase() }, { k: 'C', v: '' }, { k: 'D', v: '' }, { k: 'E', v: '' }, { k: 'F', v: '' }]);
                    var r3 = Addrow(3, [{ k: 'A', v: 'POSITION: ' }, { k: 'B', v: _user.pos_description }, { k: 'C', v: '' }, { k: 'D', v: '' }, { k: 'E', v: '' }, { k: 'F', v: '' }]);
                    var r4 = Addrow(4, [{ k: 'A', v: 'DEPARTMENT: ' }, { k: 'B', v: _user.department }, { k: 'C', v: '' }, { k: 'D', v: '' }, { k: 'E', v: '' }, { k: 'F', v: '' }]);
                    var r5 = Addrow(5, [{ k: 'A', v: 'COMPANY: ' }, { k: 'B', v: company }, { k: 'C', v: '' }, { k: 'D', v: '' }, { k: 'E', v: '' }, { k: 'F', v: '' }]);

                    sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + r5 + sheet.childNodes[0].childNodes[1].innerHTML;

                    function _createNode(doc, nodeName, opts) {
                        var tempNode = doc.createElement(nodeName);
                        if (opts) {
                            if (opts.attr) { $(tempNode).attr(opts.attr); }
                            if (opts.children) {
                                $.each(opts.children, function (key, value) {
                                    tempNode.appendChild(value);
                                });
                            }
                            if (opts.text !== null && opts.text !== undefined) { tempNode.appendChild(doc.createTextNode(opts.text)); }
                        }
                        return tempNode;
                    }

                }
            },
            {
                extend: 'print',
                filename: function(){
                    var _name = _user.firstname + ' ' + _user.lastname;
                    return _name.toUpperCase() + ' - ' + exported_acctg + ' ACCOUNTABILITY';
                },
                title: "",
                messageTop: function(){

                    var html = '';

                    var _name = _user.firstname + ' ' + _user.lastname;

                    html += '<center> <h3>' + exported_acctg + ' ACCOUNTABILITY REPORT</h3></center>';

                    html += '<p style="color: #000; font-weight: 500;"><strong>ISSUED TO:</strong> ' + _name.toUpperCase() + '</p>';
                    html += '<p style="color: #000; font-weight: 500;"><strong>POSITION:</strong> ' + _user.pos_description + '&emsp; &emsp;';
                    html += '<strong>DEPARTMENT:</strong> ' + _user.department + '&emsp; &emsp;';
                    html += '<strong>COMPANY:</strong> ' + _user.company + '</p>';

                    return html;
                },
            }
        ],
        initComplete: function () {
            let search_thread = null;
            $("#tbl-accountability_filter input")
                .unbind()
                .bind("input", function (e) {
                    clearTimeout(search_thread);
                    search_thread = setTimeout(function () {
                        const dtTableApi = tblAccountability.dataTable().api();
                        const elem = $("#tbl-accountability_filter input");
                        return dtTableApi.search($(elem).val()).draw();
                    }, 1000);
                });
        }
    });
}

if (typeof tblPerformanceRating !== "undefined") {
    dtPerformanceRating = tblPerformanceRating.DataTable({
        dom: '<"toolbar dt-toolbar-performance-rating">frtlp',
        serverSide: true,
        processing: true,
        ordering: false,
        autoWidth: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_employee_performance_rating"),
            type: "post",
            dataType: "json",
            data: { csrf_token: _csrf_hash, emp_id: tempDataId },
            global: false,
        },
        columns: [
            {
                data: "rating",
                title: "RATING",
                width: "10%",
            },
            {
                data: "current",
                title: "CURRENT",
                width: "5%",
                render: function (data) {
                    const badgeColor = parseInt(data) === 1 ? "m-badge--success" : "";
                    return `<span class="m-badge m-badge--wide m--font-boldest ${badgeColor}">${parseInt(data) === 1 ? "YES" : "NO"}</span>`;
                }
            },
            {
                data: "remarks",
                title: "REMARKS",
                width: "25%",
                render: function (data) {
                    return data ? data : `<span class="text-muted m--regular-font-size-sm1">No remarks added.</span>`;
                }
            },
            {
                data: null,
                title: "ACTIONS",
                width: "5%",
                className: "text-center",
                render: function (data, type, row) {
                    let buttons = ``;
                    if (_actions.includes("btnEdit") && _actions.includes("btnUpdate_performance_rating")) {
                        buttons += ` <button class="btn m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-accent"
                                             onclick="openUpdatePerformanceRatingModal(${row.id}, ${row.current})">
                                        <i class="la la-edit"></i>
                                    </button>`;
                    }

                    if (_actions.includes("btnDelete")) {
                        buttons += ` <button class="btn m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-warning btnRemovePerformanceRating"
                                             data-id="${row.id}">
                                        <i class="la la-file-archive-o"></i>
                                    </button>`;
                    }

                    return buttons ? buttons : "---";
                }
            }
        ],
        initComplete: function () {
            let search_thread = null;

            $(".dt-toolbar-performance-rating")
                .append(
                    "<button type='button' " +
                    "        class='btn btn-sm btn-success btnAdd_performance_rating btnAddPerformanceRating' data-toggle='modal'" +
                    "        data-target='#add-performance-rating-modal'>" +
                    "           <i class='la la-plus mr-1'></i>" +
                    "           New" +
                    "</button>"
                );

            $("#tbl-performance-rating_filter input")
                .unbind()
                .bind("input", function (e) {
                    clearTimeout(search_thread);
                    search_thread = setTimeout(function () {
                        const elem = $("#tbl-performance-rating_filter input");
                        return dtPerformanceRating.search($(elem).val()).draw();
                    }, 1000);
                });
        },
        drawCallback: function () {
            const data = dtPerformanceRating.data();

            // show only the latest performance rating
            if(typeof data[0] != 'undefined'){
                $("#resign_remarks").text(data[0].remarks);
            }

            dtPerformanceRating
                .rows()
                .iterator('row', function (context, index) {
                    const node = $(this.row(index).node());
                    const ratingEl = node.find("#row-rating-" + data[index].id);
                    const value = ratingEl.attr("data-value");
                    $(ratingEl)
                        .starRating({
                            totalStars: 5,
                            starShape: 'rounded',
                            starSize: 20,
                            emptyColor: 'lightgray',
                            hoverColor: 'salmon',
                            activeColor: '#FFAB00',
                            useGradient: false,
                            initialRating: value,
                            readOnly: true
                        });
                });
        }
    });
}

/* --END-- ACCOUNTABILITY */

/* --Start Return to Work */
if (typeof tblReturnToWork !== "undefined") {
    dtReturnToWork = tblReturnToWork.DataTable({
        dom: '<"toolbar dt-toolbar-return-to-work">frtlp',
        serverSide: true,
        processing: true,
        ordering: false,
        autoWidth: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_employee_return_to_work"),
            type: "post",
            dataType: "json",
            data: { csrf_token: _csrf_hash, emp_id: tempDataId },
            global: false,
        },
        columns: [
            {
                data: "reference_no",
                title: "REFERENCE NO.",
            },
            {
                data: "from_date",
                title: "EFFECTIVE DATE",
            },
            {
                data: "return_type",
                title: "TYPE",
            },
            {
                data: "reason",
                title: "PURPOSE",
                width: "25%",
            },
            {
                data: "approved_by",
                title: "APPROVED BY",
                width: "25%",
            }
        ],
        initComplete: function () {
            let search_thread = null;

            // $(".dt-toolbar-performance-rating")
            //     .append(
            //         "<button type='button' " +
            //         "        class='btn btn-sm btn-success btnAdd_performance_rating btnAddPerformanceRating' data-toggle='modal'" +
            //         "        data-target='#add-performance-rating-modal'>" +
            //         "           <i class='fa fa-plus'></i>" +
            //         "           <span>New</span>" +
            //         "</button>"
            //     );

            $("#tbl-return-to-work_filter input")
                .unbind()
                .bind("input", function (e) {
                    clearTimeout(search_thread);
                    search_thread = setTimeout(function () {
                        const elem = $("#tbl-return-to-work_filter input");
                        return dtReturnToWork.search($(elem).val()).draw();
                    }, 1000);
                });
        },
        drawCallback: function () {
            const data = dtReturnToWork.data();

            dtReturnToWork
                .rows()
                .iterator('row', function (context, index) {
                    const node = $(this.row(index).node());
                    const ratingEl = node.find("#row-rating-" + data[index].id);
                    const value = ratingEl.attr("data-value");
                    $(ratingEl)
                        .starRating({
                            totalStars: 5,
                            starShape: 'rounded',
                            starSize: 20,
                            emptyColor: 'lightgray',
                            hoverColor: 'salmon',
                            activeColor: '#FFAB00',
                            useGradient: false,
                            initialRating: value,
                            readOnly: true
                        });
                });
        }
    });
}
/* --END-- Return to Work */

function filterEmployees(el) {
    const employee_status = $(el).val();
    loadEmployees(employee_status);
}

$("#rehire-application")
    .select2({
        width: "100%",
        placeholder: "Select...",
        ajax: {
            url: baseUrl("hris/transaction/get_hiring_positions_for_select"),
            type: "GET",
            dataType: "JSON",
            delay: 500
        },
        dropdownParent: $("#modal-rehire"),
        escapeMarkup: function (markup) {
            return markup;
        },
    })
    .on("select2:select", function (e) {
        const data = e.params.data;
        $("#frm-rehire input[name='company_id']").val(data.company_id);
        $("#frm-rehire input[name='department_id']").val(data.department_id);
        $("#frm-rehire input[name='position']").val(data.position_id);
        $("#frm-rehire input[name='needed']").val(data.needed);
    });

$("#date-rehired")
    .datepicker({
        todayHighlight: true,
        orientation: "bottom left",
        templates: {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        },
        format: {
            toDisplay: function (date, format, language) {
                const d = new Date(date);
                return moment(d).format("MMMM DD, YYYY");
            },
            toValue: function (date, format, language) {
                const d = new Date(date);
                return moment(d).format("YYYY-MM-DD");
            }
        },
        autoclose: true,
        immediateUp: true
    });

const validateRehireForm = $.validate({
    form: "#frm-rehire",
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (formEl) {
        const form = $(formEl);

        $.ajax({
            url: baseUrl("hris/transaction/rehire/" + tempDataId),
            type: "post",
            dataType: "json",
            data: form.serialize(),
            beforeSend: function () {
                form
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (response) {
                if (response.success === true) {
                    const activeStatusOptions = '' +
                        '<option value=""></option>' +
                        '<option value="REGULAR">REGULAR</option>' +
                        '<option value="PROBATIONARY" selected>PROBATIONARY</option>' +
                        '<option value="NO CONTRACT">NO CONTRACT</option>' +
                        '<option value="RETIRED">RETIREE</option>' +
                        '<option value="CONSULTANT">CONSULTANT/RETAINER</option>' +
                        '<option value="PROJECT BASED">PROJECT BASED</option>';

                    const status = $('#status');
                    status.find('option').remove();
                    status.append(activeStatusOptions);

                    $('#classification').val("Active").trigger("change");
                    vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, {
                        employee_status: "Active",
                        work_status: "PROBATIONARY",
                        _status: "PROBATIONARY",
                        company: response.company,
                        company_id: response.company_id,
                        current_company_id: response.company_id,
                        current_department_id: response.department_id,
                        current_position_id: response.position_id,
                        department: response.department,
                        department_id: response.department_id,
                        _position: response.position,
                        position: response.position_id,
                    });

                    $("#m--input-department_id").val(response.department_id).trigger("change");
                    $("#m--input-position_id").val(response.position_id).trigger("change");

                    dtWorkExperience.ajax.reload();
                    $("#rehire-button-container").addClass("m--hide");
                    toastr.success("Rehire information was successfully saved.", "Employee Rehired.", { timeOut: 10000 });
                } else {
                    toastr.error("An error occurred while updating.", "Rehiring Error", { timeOut: 10000 });
                }

                $("#modal-rehire").modal("hide");
                $(".modal-backdrop").remove();
                location.reload();
                form
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );

                        
            }
        });
        return false;
    }
});

const performanceRatingEl = $("#performance-rating");
const performanceRatingDescriptionEl = $("#performance-rating-description");
const performanceRatingRehireEl = $("#performance-rating-rehire");
const performanceRatingRemarksEl = $("#performance-rating-remarks");


if (typeof $("#add-performance-rating") !== "undefined" && typeof $("#add-performance-rating").starRating == "function") {
    $("#add-performance-rating")
        .starRating({
            totalStars: 5,
            starShape: 'rounded',
            starSize: 40,
            emptyColor: 'lightgray',
            hoverColor: '#FFC400',
            activeColor: '#FFAB00',
            ratedColor: '#FFAB00',
            useGradient: false,
            initialRating: 0,
            disableAfterRate: false,
            useFullStars: true,
            callback: function (currentRating, $el) {
                $("#rating-value").val(currentRating);
                const scale = ratingScale.find((_scale) => {
                    return parseInt(_scale.value) === parseInt(currentRating);
                });

                $("#scale-description").html(scale.description);
            }
        });

    $("#add-performance-rating-modal")
        .on("hidden.bs.modal", function () {
            $("#scale-description").html("");
            $("#add-performance-rating").starRating('setRating', 0);
            $("form", this).resetForm();
        });
}


$.validate({
    form: $("#frm-add-performance-rating"),
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        let data = $(form).serializeArray();
        data.push({ name: "emp_id", value: tempDataId });
        data.push({ name: "csrf_token", value: _csrf_hash });

        const submitButton = form.find("[type='submit']");

        $.ajax({
            url: baseUrl('hris/masterfile/add_employee_performance_rating'),
            type: "POST",
            dataType: "JSON",
            data,
            beforeSend: function () {
                submitButton.addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (response) {
                const toast = response.success ? "success" : "error";
                toastr[toast](response.message, response.title, { timeOut: 10000 });
                submitButton.removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                $("#add-performance-rating-modal").modal("hide");

                if($("#m_datepicker-date_resign").val() && $("#m_datepicker-date_resign").val() !== ''){
                    $("#resignation_remarks").css('display', 'block');
                }else{
                    $("#resignation_remarks").css('display', 'none');
                }

                dtPerformanceRating.ajax.reload();
                getEmployeePerformanceRating();
            }
        });
        return false;
    }
});

$.validate({
    form: $("#frm-update-performance-rating"),
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        let data = $(form).serializeArray();
        data.push({ name: "emp_id", value: tempDataId });
        data.push({ name: "csrf_token", value: _csrf_hash });

        const submitButton = form.find("[type='submit']");

        $.ajax({
            url: baseUrl('hris/masterfile/update_employee_performance_rating'),
            type: "POST",
            dataType: "JSON",
            data,
            beforeSend: function () {
                submitButton.addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (response) {
                const toast = response.success ? "success" : "error";
                toastr[toast](response.message, response.title, { timeOut: 10000 });
                submitButton.removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                $("#update-performance-rating-modal").modal("hide");
                dtPerformanceRating.ajax.reload();

                getEmployeePerformanceRating();
            }
        });
        return false;
    }
});

if (typeof performanceRatingEl.starRating == "function") {
    performanceRatingEl
        .starRating({
            totalStars: 5,
            starShape: 'rounded',
            starSize: 25,
            emptyColor: 'lightgray',
            activeColor: '#FFAB00',
            ratedColor: '#FFAB00',
            useGradient: false,
            initialRating: 0,
            readOnly: true
        });
}

function getEmployeePerformanceRating() {
    var status = $("#status").val();
    $.ajax({
        url: baseUrl('hris/masterfile/get_employee_rating/' + tempDataId),
        type: "GET",
        dataType: "JSON",
        beforeSend: function () {
        },
        success: function (response) {
            if (response) {
                performanceRatingEl.starRating('setRating', response.rating);
                performanceRatingDescriptionEl.html(response.description);
                if(status == 'RESIGNED'){
                    if(response.for_rehire == 1){
                        var rehire = "<span class='m-badge m-badge--danger m-badge--wide'>Not for Rehire</span>";
                        performanceRatingRemarksEl.html(response.remarks);
                        $("#rehire-button-container").addClass("m--hide");
                    }else{
                        var rehire = "<span class='m-badge m-badge--success m-badge--wide'>For Rehire</span>";
                    }
                    performanceRatingRehireEl.html(rehire);
                }

                if($("#m_datepicker-date_resign").val() && $("#m_datepicker-date_resign").val() !== ''){
                    $("#resign_remarks").text(response.remarks);
                }
            }
        }
    });
}

function openPerformanceRatingRemarksModal() {
    $.ajax({
        url: baseUrl('hris/masterfile/get_employee_rating_remarks/' + tempDataId),
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            if (response) {
                const _modal = $("#modal-performance-rating-remarks");
                $("#performance-rating-in-remarks", _modal).starRating('setRating', response.rating);
                $("#employee-container").hide();
                $("#scale-description", _modal).html(response.description);
                $("#purpose", _modal).val(response.purpose);
                $("#remarks", _modal).val(response.remarks);
                _modal.modal("show");
            }else{
                toastr.info("No current Performance Rating.", { timeOut : 3000});
            }
        }
    });
}

if (typeof $("#update-performance-rating").starRating == "function") {
    $("#update-performance-rating")
        .starRating({
            totalStars: 5,
            starShape: 'rounded',
            starSize: 40,
            emptyColor: 'lightgray',
            hoverColor: '#FFC400',
            activeColor: '#FFAB00',
            ratedColor: '#FFAB00',
            useGradient: false,
            initialRating: 0,
            disableAfterRate: false,
            useFullStars: true,
            callback: function (currentRating, $el) {
                $("#rating-value", "#update-performance-rating-modal").val(currentRating);
                const scale = ratingScale.find((_scale) => {
                    return parseInt(_scale.value) === parseInt(currentRating);
                });

                $("#scale-description", "#update-performance-rating-modal").html(scale.description);
            }
        });

    $("#update-performance-rating-modal")
        .on("show.bs.modal", function () {
            const modal = $(this);
            const id = $("#id", modal).val();
            $.ajax({
                url: baseUrl('hris/masterfile/get_employee_rating_for_update/' + id),
                type: "GET",
                dataType: "JSON",
                beforeSend: function () {
                },
                success: function (response) {
                    if (response) {
                        $("#update-performance-rating").starRating('setRating', response.rating);
                        $("#scale-description", modal).html(response.description);
                        $("#rating-value", modal).val(response.rating);
                        $("#remarks", modal).val(response.remarks);
                        $("#purpose", modal).val(response.purpose);
                    }
                }
            });
        });
}


function openUpdatePerformanceRatingModal(id, current) {
    $("#id", "#update-performance-rating-modal").val(id);
    $("#current", "#update-performance-rating-modal").val(current);
    $("#update-performance-rating-modal").modal("show");
}


$.validate({
    form: "#updateSalaryModalForm",
    lang: "en",
    onSuccess: function () {
        var formData = $("#updateSalaryModalForm").serialize();
        $.ajax({
            url: baseUrl('hris/masterfile/update_salary_rating'),
            type: "POST",
            data: formData,
            dataType: "JSON",
            beforeSend: function () {
            },
            success: function (response) {
                if(response){
                $("#update_salary_history").modal('hide');
                    toastr.success("Success", { timeOut : 3000});
                }else{
                    toastr.error("Error adding salary rate.", { timeOut : 3000});
                }
            }
        });
        return false;
    }
});


function getLatestSalaryRate(){
    var data = $("#updateSalaryModalForm").serialize();
    $.ajax({
        url: baseUrl('hris/masterfile/get_latestSalaryRate'),
        type: "POST",
        data: data,
        dataType: "JSON",
        success: function (response){
            $("#salary_rate").val(response.sal_rate);
            $("#salary_remarks").val(response.sal_remarks);
        }
    });
    
}

var employee_document_upload = function(){
    var url = baseUrl("hris/masterfile/employee_document_upload");
    var emp_id = $("#emp_id").val();
    var val = [];
    var files = [];
    $("#documentupload")
    .fileupload({
        url: url,
        dataType: "json",
        formData: { csrf_token: _csrf_hash, emp_id : emp_id },
        done: function(e, data) {
            var result = data.result;
            if (result.response) {
                var filePath = result.added_file;
                var renderFile = result.render_file;
                $("#file_append").text(renderFile);
                $("#path").val(filePath);
                $("#filename").val(renderFile);
                if(result.extension=="jpg" || result.extension=="png" || result.extension=="JPG" || result.extension=="PNG" || result.extension=="jpeg"){
                    
                }else{
                    $("#picture").attr("src", "");
                }
                val.push(renderFile);
                $("#document_names").val(val);
                $("#picture").html($("#document_names").val());
            } else {
                toastr.error(result.toastr_msg, "File error", 5000);
            }
        }
        
    });
}


function displayDriversLicense(){
    var emp_id = $("#accordion_emp_id").val();
    var csrf_token = $("#csrf_token").val();
    var url = baseUrl("hris/masterfile/get_employee_position/"+emp_id);
    $.ajax({
        url: url,
        data : {
            emp_id : emp_id, 
            csrf_token : csrf_token
        },
        success: function(data){
            if(data == 0){
                $("#DrLicenseCard").attr("hidden", true);
            }else{
                $("#DrLicenseCard").attr("hidden", false);
            }
        }
    });
}

$("#status").change(function(){
    var status = $("#status").val();
    var date_end = $("#m_datepicker-date_end").val();
    var classification = $("#classification").val();
    if(classification == 'Inactive' && (date_end == "0000-00-00" || date_end == "")){
        $("#for_rehire_display").attr("hidden", false);
        $("#add-performance-rating-modal").modal();
        $("#performance_purpose").val("1");
        $("#m_datepicker-date_end").attr("data-validation","required");
    }else{
        $("#performance_purpose").val("0");
        $("#for_rehire_display").attr("hidden", true);
    }
});

var vmPayInfo = new Vue({
        el: "#frmEditPayrollData-container",
        data: { 
            vmpayinfo: tempData, 
            edited_content: {},
            psInfoCtr: 0, 
            forApprovalCtr: 0,
        }, methods: {
            scrollToBottom(){ $('html, body').animate({ scrollTop: $('#payroll_info-history').offset().top}, 1000); },
            forApprovalModal(){
                if(typeof modalForApproval !== "undefined" && modalForApproval.length == 1){
                    modalForApproval.modal("show");
                }
            }
        }, mounted: function () {
            $("#payroll_type")
                .select2({
                    width: "100%",
                    placeholder: "SELECT..."
                })
                .on("select2:select", function (e) {
                    const data = e.params.data;
                    vmPayInfo.vmpayinfo = Object.assign({}, vmPayInfo.vmpayinfo, {payroll_type: data.id});
                    vmPayInfo.edited_content = Object.assign({}, vmPayInfo.edited_content, {payroll_type: data.id});
                });

            $("#payout_sched")
                .select2({
                    width: "100%",
                    placeholder: "SELECT..."
                })
                .on("select2:select", function (e) {
                    const data = e.params.data;
                    vmPayInfo.vmpayinfo = Object.assign({}, vmPayInfo.vmpayinfo, {payout_sched: data.id});
                    vmPayInfo.edited_content = Object.assign({}, vmPayInfo.edited_content, {payout_sched: data.text});
                });
        }
    });

var vmChecklist = new Vue({
    el: "#documentsChecklist",
    data: { row: {}, count: 0 },
    methods: {
        colmn(num){
            return parseInt(num)+1;
        }
    }
});

function exportAccountability(file, status, type){
    acctgStatus = status;
    exported_acctg = type.toUpperCase();
    if(file == 'excel'){
        dtAccountability.ajax.reload( function(){
            dtAccountability.button('.buttons-excel').trigger();
        });
    }else if(file == 'pdf'){
        dtAccountability.ajax.reload( function(){
            dtAccountability.button('.buttons-pdf').trigger();
        });
    }else{
        dtAccountability.ajax.reload( function(){
            dtAccountability.button('.buttons-print').trigger();
        });
    }

    setTimeout( function(){
        acctgStatus = 2;
        dtAccountability.ajax.reload();
    }, 1000);
}

$("#view-btn button").on('click', function(){
    let btn = ['list', 'grid'];

    var clicked = $(this).attr('id');
    if(!$(this).hasClass('active')){
        clickedView = clicked;
        
        if(jQuery.inArray(clicked, btn) == 0){
            $('#list').addClass('active').removeClass('btn-default').addClass('btn-accent');
            $('#grid').removeClass('active');

            $("#table-employee").removeClass('grid').addClass('list');
            $("#table-employee tbody").addClass('list').removeClass('grid');
            $("#table-employee tbody td #details #grid").css('display', 'none');
            $("#table-employee tbody td #details #list").css('display', 'block');
            $("#table-employee tbody td #details #grid .custom-fullname a").attr('target', '_blank');

            $("#table-employee tbody td:first-child").removeClass('btnViewEmployee201');
        }else{
            $('#list').removeClass('active').removeClass('btn-accent').addClass('btn-default');
            $('#grid').addClass('active');

            $("#table-employee").addClass('grid').removeClass('list');
            $("#table-employee tbody").addClass('grid').removeClass('list');
            $("#table-employee tbody td #details #grid").css('display', 'flex');
            $("#table-employee tbody td #details #list").css('display', 'none');
            $("#table-employee tbody td #details #grid .custom-fullname a").removeAttr('target');

            $("#table-employee tbody td:first-child").addClass('btnViewEmployee201');
        }
    }
});

var offComTrail = $("#offensesCommendationTrail").DataTable({
    serverSide: true,
    processing: true,
    ordering: false, 
    searching: false,
    autoWidth: false,
    ajax: {
        url: baseUrl("hris/masterfile/get_offenses_commendation_trail"),
        type: "POST",
        dataType: "json",
        data: function(d) {
            d.id = tempDataId; 
            d.csrf_token = _csrf_hash;
            return d;
        }
    },
    columns: [
        { data: 'id' , visible :false },
        { data: 'logs', width:"70%" },
        { data: 'action',width:"10%" },
        {data : null, width:"20%", class: 'text-left',
            render: function(data, type, row) {
                const createdAt = new Date(row.created_at);
                const optionsDate = { year: 'numeric', month: 'long', day: '2-digit' };
                const formattedDate = createdAt.toLocaleDateString('en-US', optionsDate);
                const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
                const formattedTime = createdAt.toLocaleTimeString('en-US', optionsTime);
                const formattedDateTime = `${formattedDate} ${formattedTime}`;
                return `<td><p class="mb-0">${row.user}</p><small><span class="m--font-boldest">${formattedDateTime}</span></small></td>`;
        }
        }
    ]
});

var vmBankInfo = new Vue({
    el: "#frmEditBankData-container",
    data: { row: tempData }
});
$('#offense-tabs .nav-link').on('click', function(e) {
    e.preventDefault();
    $('#offense-tabs .nav-link').removeClass('active');
    $('#offense-content .tab-pane').removeClass('active show');
    $(this).addClass('active');
    var targetId = $(this).attr('href');
    $(targetId).addClass('active show');
});

$('#collapseOffenses').on('shown.bs.collapse', function() {
    $('#offense-tabs .nav-link').removeClass('active');
    $('#offense-content .tab-pane').removeClass('active show');
    $('#collapseOffenses .nav-tabs .nav-link:first').tab('show');
});

$("#mobile_no").inputmask({
	mask: "(0\\9) 9999-99999",
	alias: 'phonenumber'
});

$("#company_no").inputmask({
	mask: "(0\\9) 9999-99999",
	alias: 'phonenumber'
});

function openCert(name) {
    // Construct the full URL of the file
    var fileUrl = baseUrl("uploads/files/documents/employee_files/empcode_" + tempDataId + "/licenses_certificates/" + encodeURIComponent(name));
    // Function to check if file exists and get its MIME type
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

    // Check if file exists
    checkFileExists(fileUrl, function(exists, mimeType) {
        if (!exists) {
            // Show error message if file doesn't exist
            $('#pdfViewerModal .modal-body').html('<p class="text-danger">Error: File not found.</p>');
            $('#pdfViewerModal').modal('show');
        } else if (mimeType && mimeType.startsWith('application/pdf')) {
            // Show PDF in modal
            $('#pdfViewerModal .modal-body').html('<iframe id="pdfFrame" style="width: 100%; height: 600px;" frameborder="0"></iframe>');
            $('#pdfViewerModal').modal('show');
            $('#pdfFrame').attr('src', fileUrl);
        } else {
            // Open non-PDF files in new window
            window.open(fileUrl, '_blank');
        }
    });
}

const vmPrimary = new Vue({
    el: "#set_primary_position",
    data: { positions: {}, isSortOnly: false },
    methods: {
        savePrimaryPosition(){
            const formUrl = $('#set_primary_position').data('formUrl');
            const formData = $('#frmEditEmploymentData').serialize(); // retrieve the latest changes in multiple position
            const currentForm = $('#set_primary_position').data('formElement');

            if (!this.isSortOnly && this.positions.length > 0) {
                if ( typeof vmTab3.vm_tab3.multiple_position !== 'undefined' && vmTab3.vm_tab3.multiple_position.length > 0 && this.positions.length == 0) {
                    saveEmploymentData(formUrl, formData, currentForm);
                } else {
                    Swal.fire({
                        title: "Save Changes?",
                        text: "Are you sure you want to save changes?",
                        icon: "question",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        showCancelButton: false,
                        confirmButtonText: "Submit"
                    }).then(response => {
                        if (response.isConfirmed) {
                            $("#set_primary_position").modal('hide');
    
                            setTimeout( function () {
                                saveEmploymentData(formUrl, formData, currentForm);
                            }, 750)
                        }
                    });
                }
            } else {
                Swal.fire({
                    title: "Save Changes?",
                    text: "Are you sure you want to save changes?",
                    icon: "question",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    showCancelButton: false,
                    confirmButtonText: "Submit"
                }).then(response => {
                    if (response.isConfirmed) {
                        $("#set_primary_position").modal('hide');
                    }
                });
            }

        },
        isEmpty(arr) {
            return $.isEmptyObject(arr)
        }
    },
});

var PortletDraggable = function () {
    return {
        init: function () {
            $("#m_sortable_portlets").sortable({
                connectWith: ".m-portlet__head",
                items: ".m-portlet",
                opacity: 0.8,
                handle: '.m-portlet__head',
                coneHelperSize: true,
                placeholder: 'm-portlet--sortable-placeholder',
                forcePlaceholderSize: true,
                tolerance: "pointer",
                helper: "clone",
                tolerance: "pointer",
                forcePlaceholderSize: !0,
                helper: "clone",
                cancel: ".m-portlet--sortable-empty",
                revert: 250,
                start: function (event, ui) {
                    originalIndex = ui.item.index();
                },
                update: function (b, c) {
                    const newData = [];
                    const _temp = [];
                    $('#m_sortable_portlets .m-portlet').each(function (index) {
                        const id = $(this).data('id');
                        const text = $(this).find('.position-text').text().trim();

                        newData.push({
                            id: id,
                            text: text,
                            primary: index === 0 ? 1 : 0,
                            sort: index
                        });
                    });

                    vmPrimary.positions = [...newData];
                    vmTab3.multiple_position = [...newData];
                    vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, { multiple_position: newData });
                    vmTab3.positionSelect2('#m--input-position_id', true, vmTab3.vm_tab3.position, true, newData);
                }
            });
        }
    };
}();

function saveEmploymentData(formUrl, formData, currentForm) {
    $.ajax({
        url: formUrl,
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
                    "Employee data has been updated.",
                    5000
                );
                var _respData = json.data;
                var _newData = Object.assign(
                    {},
                    {
                        display_name: _respData.display_name,
                        display_email: _respData.display_email,
                        display_avatar: _respData.pic_filename
                    }
                );
                leftPanel.left_pane = _newData;
                _tempContentData.data = Object.assign({}, json.data);

                if(typeof vmTab3.vm_tab3 != 'undefined' && Object.keys(vmTab3.vm_tab3).length > 0){
                    let { vm_tab3 } = vmTab3;
                    // vm_tab3 = Object.assign({}, vm_tab3, json.data); -> commented as it doesnt overwrite the old the after updating the record
                    vmTab3.vm_tab3 = Object.assign({}, vm_tab3, json.data);

                    if (json.data.is_multiple_position == 1) {
                        let intersection = [];
                        const sortMap = new Map();

                        vmTab3.vm_tab3.multiple_position.forEach(p => {
                            sortMap.set(parseInt(p.id), {
                                sort: parseInt(p.sort),
                                primary: parseInt(p.is_primary) // ensure boolean or numeric consistency
                            });
                        });
                        
                        intersection = tempDropdownData.dropdown_position
                        .filter(a1 =>
                            vmTab3.vm_tab3.multiple_position.some(a2 => parseInt(a2.id) === parseInt(a1.id))
                        )
                        .map(item => {
                            const data = sortMap.get(parseInt(item.id));
                            return {
                                ...item,
                                primary: data?.primary,
                                sort: data?.sort
                            };
                        })
                        .sort((a, b) => {
                            if (b.primary !== a.primary) {
                                return b.primary - a.primary;
                            }
                            return a.sort - b.sort;
                        });

                        $("#m--input-position_id").prop("multiple", true);
                        $("#m--input-position_id").attr('name', 'position[]');
                        vmTab3.multiple_position = [...intersection];
                        vmTab3.positionSelect2('#m--input-position_id', true, vmTab3.vm_tab3.position, true, intersection);
                    } else {
                        vmTab3.multiple_position = [];
                    }
                }
                // vmTab3.vm_tab3 = Object.assign({}, vmTab3.vm_tab3, json.data);
                getJobDescription();
                getEmployeePerformanceRating();
                $("#change_employment_info").val(0);
                $("#employment_information i").remove();
            } else {
                toastr.error(
                    json.toastr_msg,
                    "Error updating employee data!",
                    5000
                );
            }

            $(currentForm)
                .find(".btn-submit")
                .removeClass(
                    "m-btn--custom m-loader m-loader--light m-loader--right"
                );

            if (vmPrimary.positions.length > 0) {
                vmPrimary.positions = [];
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error:", textStatus, errorThrown);
            toastr.error(errorThrown);
        }
    });
}

const vmJobDesc = new Vue({
    el: "#job_description-content",
    data: { row: {}, is_multiple_position: false },
    methods: {
        isEmpty(arr) {
            return $.isEmptyObject(arr)
        }
    }
});

function sendEmail(){
    $.ajax({
        type: 'POST',
        global: true,
        // url: '<?= base_url('login/authenticate')?>',
        data: formData,
        dataType: 'json',
        beforeSend: function() {}
    });
}