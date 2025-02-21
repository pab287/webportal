$('table')
    .on('click', '.btnEditDocuments',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_document",
                    function_name: "getDocumentInfo",
                    model: "Employee_model",
                    formData: {id},
                    init_modal_data_function: 'getDocumentInfo'
                },
                success: function (response) {
                    initEditDocumentDialog(response);

                    $("#is-checklist").on('click', function(){
                        if($("#is-checklist").is(":checked")){
                            $("#checklist").css('display', 'block');
                            $("#non-checklist").css('display', 'none');
                        }else{
                            $("#checklist").css('display', 'none');
                            $("#non-checklist").css('display', 'block');
                        }
                    });
                }
            });
        })
    .on('click', '.btnRemoveDocuments',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_document/" + id + "/btnRemoveDocuments",
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
        })
    .on('click', '.btnEditLegalHistory',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_legal_records",
                    function_name: "getLegalRecords",
                    model: "Employee_model",
                    formData: {id}
                },
                success: function (response) {
                    initRegularEditDialog(response);
                }
            });
        })
    .on('click', '.btnRemoveLegalHistory',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_legal_record/" + id + "/btnRemoveLegalHistory",
                        color: "btn-danger",
                        table: "tbl-legal_history_list",
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
        })
    .on('click', '.btnEditOffenses',
        function () {
            let selectedData = $(this).attr('data-select');
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_offenses_and_commendations",
                    function_name: "getOffensesAndCommendations",
                    model: "Employee_model",
                    formData: {id},
                    init_modal_data_function: 'getOffensesAndCommendations'
                },
                success: function (response) {
                    initEditOffensesAndCommendationsDialog(response,selectedData);
                }
            });
        })
    .on('click', '.btnRemoveOffenses',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_offenses_and_commendations/" + id + "/btnRemoveOffenses",
                        color: "btn-danger",
                        table: "tbl-offenses_list"
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
        })
    .on('click', '.btnEditBackgroundCheck',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_background_check",
                    function_name: "getBackgroundCheck",
                    model: "Employee_model",
                    formData: {id}
                },
                success: function (response) {
                    initRegularEditDialog(response);
                }
            });
        })
    .on('click', '.btnRemoveBackgroundCheck',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_bg_check/" + id + "/btnRemoveBackgroundCheck",
                        color: "btn-danger",
                        table: "tbl-background_check_list",
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
        })
    .on('click', '.btnEditPerformance',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_performance_evaluation",
                    function_name: "getPerformanceEvaluation",
                    model: "Employee_model",
                    formData: {id},
                    init_modal_data_function: 'getPerformanceEvaluation'
                },
                success: function (response) {
                    initEditPerformanceEvaluationDialog(response);
                }
            });
        })
    .on('click', '.btnRemovePerformance',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_performance_evaluation/" + id + "/btnRemovePerformance",
                        color: "btn-danger",
                        table: "tbl-performance_eval_list"
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
        })
    .on('click', '.btnEditCashAdvance',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_cash_advance",
                    function_name: "getCashAdvances",
                    model: "Employee_model",
                    formData: {id}
                },
                success: function (response) {
                    initRegularEditDialog(response);
                }
            });
        })
    .on('click', '.btnRemoveCashAdvance',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_employee_cash_advance/" + id + "/btnRemoveCashAdvance",
                        color: "btn-danger",
                        table: "tbl-cash_advance_list"
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
        })
    .on('click', '.btnEditDependents',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_dependent",
                    function_name: "getDependentInfo",
                    model: "Employee_model",
                    formData: {id}
                },
                success: function (response) {
                    initRegularEditDialog(response);
                }
            });
        })
    .on('click', '.btnRemoveDependents',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_dependent/" + id + "/btnRemoveDependents",
                        color: "btn-danger",
                        table: 'tbl-dependents_list',
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
        })
    .on('click', '.btnEditEducational',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_educational_background",
                    function_name: 'getEducationalInfo',
                    model: 'Employee_model',
                    formData: {id},
                    init_modal_data_function: 'getEducationalInfo'
                },
                success: function (response) {
                    initEditEducationalBackground(response);
                }
            });
        })
    .on('click', '.btnRemoveEducational',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_educational_background/" + id + "/btnRemoveEducational",
                        color: "btn-danger",
                        table: 'tbl-educational_background_list',
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
        })
    .on('click', '.btnEditLicensure',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_licensure",
                    function_name: 'getLicensure',
                    model: 'Employee_model',
                    formData: {id},
                    init_modal_data_function: 'getLicenseInfo'
                },
                success: function (response) {
                    initEditLicenses(response);
                }
            });
        })  
    .on('click', '.btnRemoveLicensure',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_licensure/" + id + "/btnRemoveLicensure",
                        color: "btn-danger",
                        table: "tbl-licensure_exams_list",
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
        })
    .on('click', '.btnEditDriverLicense',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_driverlicense",
                    function_name: 'getDriverLicense',
                    model: 'Employee_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);
                }
            });
        })
    .on('click', '.btnRemoveDriverLicense',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_driverlicense/" + id + "/btnRemoveDriverLicense",
                        color: "btn-danger",
                        table: "tbl-driverlicense",
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
        })          
    .on('click', '.btnEditWorkExperience',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_work_experience",
                    function_name: 'getWorkExperience',
                    model: 'Employee_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);
                }
            });
        })
    .on('click', '.btnRemoveWorkExperience',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_worK_experience/" + id + "/btnRemoveWorkExperience",
                        color: "btn-danger",
                        table: "tbl-work_experiences_list"
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
        })
    .on('click', '.btnEditAwards',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_award",
                    function_name: 'getAward',
                    model: 'Employee_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);
                }
            });
        })
    .on('click', '.btnRemoveAwards',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_award/" + id + "/btnRemoveAwards",
                        color: "btn-danger",
                        table:"tbl-awards_list",
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
        })
    .on('click', '.btnEditOrganization',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_organization",
                    function_name: 'getOrganization',
                    model: 'Employee_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);
                }
            });
        })
    .on('click', '.btnRemoveOrganization',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_organization/" + id + "/btnRemoveOrganization",
                        color: "btn-danger",
                        table: "tbl-organizations_list",
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
        })
    .on('click', '.btnEditTrainings',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_training",
                    function_name: 'getTraningsAndSeminars',
                    model: 'Employee_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);
                }
            });
        })
    .on('click', '.btnRemoveTrainings',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_training/" + id + "/btnRemoveTrainings",
                        color: "btn-danger",
                        table: "tbl-trainings_list",
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
        })
    .on('click', '.btnEditPersonalReference',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_personal_references",
                    function_name: 'getPersonalReference',
                    model: 'Employee_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);
                }
            });
        })
    .on('click', '.btnRemovePersonalReference',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_personal_reference/" + id + "/btnRemovePersonalReference",
                        color: "btn-danger",
                        table: "tbl-personal_references_list",
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
        })
    .on('click', '.btnEditMedicalHistory',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "hris/masterfile/employee/edit_modals/edit_medical_history",
                    function_name: 'getMedicalHistory',
                    model: 'Employee_model',
                    formData: {id},
                },
                success: function (response) {
                    initRegularEditDialog(response);
                }
            });
        })
    .on('click', '.btnRemoveMedicalHistory',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_medical_record/" + id + "/btnRemoveMedicalHistory",
                        color: "btn-danger",
                        table: "tbl-medical_records_list",
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
        })
    .on('click', '.btnRemoveSkill',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_skill/" + id + "/btnRemoveSkill",
                        color: "btn-danger",
                        table: "tbl-skills-list",
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
        })
    .on('click', '.btnRemoveSalaryHistory',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "hris/masterfile/archive_salary_history/" + id + "/btnRemoveSalaryHistory",
                        color: "btn-danger",
                        table: "tbl-salary-history"
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
        })
    .on('click', '.btnRemovePerformanceRating',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("hris/masterfile/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "<i class='la la-archive mr-2'></i>Confirm Archive",
                        message: "Are you sure to delete this performance rating?",
                        action: "hris/masterfile/delete_performance_rating/" + id + "/btnRemovePerformanceRating",
                        color: "btn-danger",
                        table: "tbl-performance-rating"
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
        });


$(".m-content")
    .on("submit", "#employee-data-update-document",
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);

            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditDocuments[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            const data = response.data;
                            tr.find('td:eq(0)').html(data.doc_type);
                            data.doc_filename && tr.find('td:eq(1)').html(data.doc_filename);
                        }

                        _toaster(response, 'Document Updated.', 10000);
                        closeDialog();
                    }
                });
            }
        })
    .on("submit", "#confirmation-dialog",
        function (e) {
            e.preventDefault();
            const url = $(this).attr('action');
            const btn = url.split('/').pop();
            const id = url.split('/').slice(-2)[0];
            const tr = (btn === 'remove-profile-picture') ? '' : $('.' + btn + '[data-id="' + id + '"]').closest("tr");
            const dataTableValue = $('#confirmation-dialog').data('table');
            $.ajax({
                url: baseUrl(url),
                type: "GET",
                dataType: "JSON",
                success: function (response) {
                    if (!(btn === 'remove-profile-picture')) {
                        if(dataTableValue != ''){
                            $('#'+dataTableValue).DataTable().ajax.reload();
                        }
                        else{
                            response.success && tr.remove();
                        }
                    } else {
                        const data = response.data;
                        leftPanel.left_pane = Object.assign({}, leftPanel.left_pane, {display_avatar: data.image});
                    }

                    if (response) {
                        _toaster(response, response.title, 10000);
                    }
                    closeDialog();
                }
            });
        })
    .on("submit", "#employee-data-update-legal-records",
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);

            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditLegalHistory[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            const data = response.data;
                            if (data) {
                                Object.keys(data).forEach((item, i) => {
                                    tr.find('td:eq(' + i + ')').html(data[item]);
                                });
                            }
                        }

                        _toaster(response, 'Legal Record Updated.', 10000);
                        closeDialog();
                    }
                });
            }
        })
    .on("submit", "#employee-data-update-offenses-and-commendations",
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);
            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditOffenses[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            if(selectedTable == 'offenses'){
                                dtOffenses.ajax.reload();
                            }else if(selectedTable == 'commendation'){
                                dtCommendation.ajax.reload();
                            }else if(selectedTable == 'notices'){
                                dtNotices.ajax.reload();
                            }
                            else if(selectedTable == 'others'){
                                dtOthers.ajax.reload();
                            }
                        }

                        _toaster(response, 'Offenses & Commendations Updated.', 10000);
                        closeDialog();
                        offComTrail.ajax.reload();
                    }
                });
            }
        })
    .on("submit", "#employee-data-update-bg-check",
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);

            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditBackgroundCheck[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            const data = response.data;
                            if (data) {
                                Object.keys(data).forEach((item, i) => {
                                    tr.find('td:eq(' + i + ')').html(data[item]);
                                });
                            }
                        }

                        _toaster(response, 'Background Check Updated.', 10000);
                        closeDialog();
                    }
                });
            }
        })
    .on('click', '.btnUpdateQuestions',
        function () {
            const id = _tempContentData.data.id || null;
            if (id) {
                $.ajax({
                    url: baseUrl("hris/masterfile/open_edit_modal"),
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        csrf_token: _csrf_hash,
                        path: "hris/masterfile/employee/modals/questions",
                        function_name: "getQuestionAnswers",
                        model: "Employee_model",
                        formData: {id}
                    },
                    success: function (response) {
                        initRegularEditDialog(response);
                    }
                });
            }
        })
    .on('click', '.btnUpdateJobDescription',
        function () {
            const id = _tempContentData.data.id || null;
            if (id) {
                $.ajax({
                    url: baseUrl("hris/masterfile/open_edit_modal"),
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        csrf_token: _csrf_hash,
                        path: "hris/masterfile/employee/modals/job_description",
                        function_name: "getJobDescription",
                        model: "Employee_model",
                        formData: {id}
                    },
                    success: function (response) {
                        initRegularEditDialog(response);
                    }
                });
            }
        })
    .on('submit', '#employee-data-update-question-answers',
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);
            const collapseQuestions = $("#collapseQuestions");
            const answers = collapseQuestions.find(".m-form-row__paragraph");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            const data = response.data;
                            if (data) {
                                data.forEach((answer, i) => {
                                    const item = $(answers)[i];
                                    $(item).html(answer);
                                });
                            }
                        }

                        _toaster(response, 'Answers Updated.', 10000);
                        closeDialog();
                    }
                });
            }
        })
    .on('submit', '#employee-update-job-description',
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);
            formData.append('emp_id', tempDataId);
            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            const data = response.data;
                            if (data) {
                                $("#jobDescription").html(data.job_desc);
                            }
                        }

                        _toaster(response, 'Job Description Updated.', 10000);
                        closeDialog();
                    }
                });
            }
        })
    .on('submit', '#employee-data-update-performance-evaluation',
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);

            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditPerformance[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            const data = response.data;
                            if (data) {
                                Object.keys(data).forEach((item, i) => {
                                    tr.find('td:eq(' + i + ')').html(data[item]);
                                });
                            }
                        }

                        _toaster(response, 'Performance Evaluation Updated.', 10000);
                        closeDialog();
                    }
                });
            }
        })
    .on('submit', '#employee-data-update-cash-advance',
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);

            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditCashAdvance[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            const data = response.data;
                            if (data) {
                                tr.find('td:eq(1)').html(data.amt_applied);
                                tr.find('td:eq(2)').html(data.purpose);
                                tr.find('td:eq(3)').html(data.amt_approved);
                            }
                        }

                        _toaster(response, 'Cash Advance Updated.', 10000);
                        closeDialog();
                    }
                });
            }
        })
    .on('submit', '#employee-data-update-dependent',
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);
            formData.append('emp_id', tempDataId);
            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditDependents[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            dtDependents.ajax.reload();
                        }

                        _toaster(response, 'Dependent Updated.', 10000);
                        closeDialog();
                    }
                });
            }
        })
    .on('submit', '#employee-data-update-educational-background',
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);

            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditEducational[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            dtEducationalBg.ajax.reload();
                        }

                        _toaster(response, 'Educational Background Updated.', 10000);
                        closeDialog();
                    }
                });
            }
        })
    .on('submit', '#employee-data-update-licensure',
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);

            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditLicensure[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            dtLicensureExam.ajax.reload();
                        }
                        _toaster(response, 'Licensure Exams. & Cerfications Updated..', 10000);
                        closeDialog();
                    }
                });
            }
        })
        .on('submit', '#employee-data-update-driverlicense',
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);

            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditDriverLicense[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            dtLicensure.ajax.reload();
                        }

                        _toaster(response, 'Driver Licenses Updated..', 10000);
                        closeDialog();
                    }
                });
            }
        })
    .on('submit', '#employee-date-update-work-experience',
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);

            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditWorkExperience[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            dtWorkExperience.ajax.reload();
                        }

                        _toaster(response, 'Work Experience updated.', 10000);
                        closeDialog();
                    }
                });
            }
        })
    .on('submit', '#employee-data-update-award',
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);

            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditAwards[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            dtAwards.ajax.reload();
                        }

                        _toaster(response, 'Award & Achievements updated.', 10000);
                        closeDialog();
                    }
                });
            }
        })
    .on('submit', '#employee-data-update-organization',
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);

            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditOrganization[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            dtOrganization.ajax.reload();
                        }

                        _toaster(response, 'Organization updated.', 10000);
                        closeDialog();
                    }
                });
            }
        })
    .on('submit', '#employee-data-update-training',
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);

            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditTrainings[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            dtTrainings.ajax.reload();
                        }

                        _toaster(response, 'Training and Seminars updated.', 10000);
                        closeDialog();
                    }
                });
            }
        })
    .on('submit', '#employee-data-update-personal-references',
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);

            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditPersonalReference[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            dtPersonalReference.ajax.reload();
                        }

                        _toaster(response, 'Personal Reference updated.', 10000);
                        closeDialog();
                    }
                });
            }
        })
    .on('submit', '#employee-update-medical-record',
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);

            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditMedicalHistory[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            dtMedicalHistory.ajax.reload();
                        }

                        _toaster(response, 'Medical Record updated.', 10000);
                        closeDialog();
                    }
                });
            }
        });

function initEditDocumentDialog(response) {
    const html = response.html;
    const data = response.info.data;
    const _modal = $('.document-modal-container');
    _modal.empty();
    _modal.append(html);

    const select2_doc_type = _modal.find("#doc_type");
    const _docType = data.doc_type;
    select2_doc_type.val(_docType).trigger('change');
    select2_doc_type
        .select2({
            placeholder: "Select Type",
            width: "100%",
            dropdownParent: $(".document-modal-container")
        });
    _modal.modal('show');

    /***
     * checklistedId -> tblchecklist_documents id;
     * checklist_id -> tblchecklist id
     */
    
    const _checkType = data.checklistedId;
    const select2_checklist_type = _modal.find("#checklist_type");

    if(_checkType != '' && _checkType){
        var option = new Option(data.doc_type, data.checklist_id, true, true);
        select2_checklist_type.html(option).trigger('change');

        _modal.find("#is-checklist").prop('checked', true);

        _modal.find("#checklist").css('display', 'block');
        _modal.find("#non-checklist").css('display', 'none');


        _modal.find('#checklistId').val(data.checklist_id);
        _modal.find('#checklistedId').val(_checkType);
        
    }

    select2_checklist_type
        .select2({
            placeholder: "Select Type",
            width: "100%",
            dropdownParent: $(".document-modal-container"),
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

            _modal.find('#checklistId').val(data.checklistId);
        });

}

function initRegularEditDialog(response) {
    const html = response.html;
    const _modal = $('.document-modal-container');
    _modal.empty();
    _modal.append(html);

    _modal.find("input.date")
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

    _modal.find(".date-range")
        .daterangepicker({
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            showDropdowns: true
        }, function (start, end, label) {
            $('.date-range .form-control')
                .val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
        });

    _modal.find(".date-year")
        .datepicker({
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
        });
    _modal.modal('show');
}

function initEditOffensesAndCommendationsDialog(response,selectedData) {
        selectedTable = selectedData;
        let selectData = [];
        if (selectedData == 'offenses'){
            selectData =  [
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
            selectData =  [
                { id: 'COMMENDATION', text: 'COMMENDATION' },]
        }
        else if(selectedData == 'notices'){
            selectData =  [
                { id: 'NOTICES', text: 'NOTICE' },]
        }
        else if(selectedData == 'others'){
            selectData =  [
                { id: 'OTHERS', text: 'OTHERS' },]
        }

    const html = response.html;
    const data = response.info.data;
    const _modal = $('.document-modal-container');
    _modal.empty();
    _modal.append(html);

    const _offcom_type = data.offcom_type;
    const select2Element = _modal.find("#offcom_type_edit");
    select2Element.select2({
        width: "100%",
        placeholder: "Select an option",
        // dropdownParent: modalTempContent,
        data: selectData,
    });

    if (selectedData == 'offenses') {
        select2Element
            .val(_offcom_type)
            .trigger('change');
    }

    if (selectData.length === 1) {
        select2Element.val(selectData[0].id).trigger('change');
    }

    _modal.find("input.date")
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
        const tempFileUpload = _modal.find("#temp_fileupload");
        const fileupload_offenses = _modal.find("#fileupload_offenses");
        if ($.trim(tempFileUpload.text()) === '') {
            fileupload_offenses.attr('data-validation', 'required');
        }
    _modal.modal('show');
}


function initEditPerformanceEvaluationDialog(response) {
    if (!response) {
        return;
    }

    const html = response.html;
    const data = response.info.data;
    const date_range = response.info.other.date_range;
    const _modal = $('.document-modal-container');
    _modal.empty();
    _modal.append(html);

    const select = _modal.find('select');
    const quarter = data.quarter;
    select.val(quarter).trigger('change');

    select
        .select2({
            placeholder: "Select Quarter",
            width: "100%",
            dropdownParent: $(".document-modal-container")
        })
        .on('select2:select', function (e) {
            const _data = e.params.data;
            $("#range").val(date_range[_data.id]);
        });

    const _minDate = "2015-01-01";
    let cMinDate = moment().format("YYYY");
    cMinDate = cMinDate - 1;
    const _xmin = _minDate ? _minDate : moment(cMinDate + "-01-01").format("YYYY");
    _modal.find('input.date')
        .datepicker({
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
    _modal.modal("show");
}

function initEditEducationalBackground(response) {
    const html = response.html;
    const data = response.info.data;
    const _modal = $('.document-modal-container');
    _modal.empty();
    _modal.append(html);

    const educ_level_type = _modal.find("#educ_level_type");
    const _educ_level_type = data.educ_level_type;
    educ_level_type.val(_educ_level_type).trigger('change');
    educ_level_type
        .select2({
            placeholder: "Select Education Type",
            width: "100%",
            dropdownParent: $(".document-modal-container")
        });
    _modal.find(".date")
        .datepicker({
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
        });
    _modal.modal('show');
}

function initEditLicenses(response) {
    const html = response.html;
    const _modal = $('.document-modal-container');
    const data = response.info.data;
    _modal.empty();
    _modal.append(html);

    const license_type = _modal.find("#license_type");
    if (response.info.data.text == "0-CERTIFICATE" ){
        response.info.data.text="CERTIFICATE";
    }
    const select2Option = new Option(response.info.data.text, response.info.data.id, false, true);
    license_type.html(select2Option);
    if (response.info.data.text != 'CERTIFICATE'){
        $('#cert_name').hide();
    }
    license_type.select2({
        width: "100%",
        placeholder: {id: "-1", text: "Select an option"},
        dropdownParent: $(".document-modal-container"),
        ajax:{
            url: baseUrl('hris/masterfile/get_license_type'),
            method: "GET",
            delay: 250,
        }
    }).on('select2:select', function (e) {
        var data = e.params.data;
        const id = data.id;
        if(id == "Certificate"){
            $('#cert_name').show();
        } else {
            $('#cert_name').hide();
        }
    });

    _modal.find("input.date")
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

    _modal.find(".date-range")
        .daterangepicker({
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            showDropdowns: true
        }, function (start, end, label) {
            $('.date-range .form-control')
                .val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
        });

    _modal.find(".date-year")
        .datepicker({
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
        });

    _modal.find("#expiry-switch input").on('click', function(){
        if(typeof $("#expiry-switch input:checked").val() != 'undefined'){
            _modal.find('#with-expiry').removeClass('d-none');
        }else{
            _modal.find('#with-expiry').addClass('d-none');
        }
    });

    _modal.modal('show');
}

function setFilename(el, elContainer) {
    const files = $(el)[0].files;
    if (files && files.length > 0) {
        const file = files[0];
        $('.modal-content ' + elContainer).html(file.name);
    } else {
        $('.modal-content ' + elContainer).html($("input[name='current_filename']").val());
    }
}

function closeDialog() {
    $('.document-modal-container').modal('hide');
}

function _toaster(response, title, duration) {
    const type = response.success ? 'success' : 'error';
    const _title = response.success ? title : 'Error';
    toastr[type](response.message, _title, duration);
}