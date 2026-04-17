let _signatory = [];
let selectedEmployee = [];

const getUrlParameter = function getUrlParameter(sParam) {
    const sPageURL = decodeURIComponent(window.location.search.substring(1));
    const sURLVariables = sPageURL.split('&');
    let sParameterName;
    let i;
    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.signatory !== "undefined" && _tempContentData.signatory.length > 0){
        _signatory = _tempContentData.signatory;
    }
}

const param_id = getUrlParameter('id');
const vmTab1 = new Vue({
    el: "#form_overtime",
    data: { vm_tab1: {}, loading_content: true },
    methods: {
        removeActionDuration(startDate) {
            if (startDate) {
                const currentDate = moment();
                const startTime = moment(startDate);
                const timeDifference = Math.abs(currentDate - startTime);
                const daysDifference = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));
                /*** hotfix for undo approvals ***/
                if(startTime >= currentDate){ return true; }
                /*** hotfix for undo approvals ***/
                return daysDifference <= 15;
            }
            return false;
        }
    }
});

$(".btnPending").hide();
$(".btnApproved").hide();
$(".btnDisapproved").hide();

$.ajax({
    url: baseUrl("eforms/overtime/get_overtime_request_details/") + param_id,
    type: "GET",
    dataType: "JSON",
    global: false,
    success: function (data) {
        const { valid_ot_dates, status } = data;
        vmTab1.vm_tab1 = { ...data };
        vmTab1.loading_content = false;

        (data.requested_remarks == "") ? $("#requested_remarks").hide() : $("#requested_remarks").show();
        (data.cancelled_remarks == "") ? $("#cancelled_remarks").hide() : $("#cancelled_remarks").show();
        (data.status != "Approved") ? $("#approved_by").hide() : $("#approved_by").show();
        (data.status != "Disapproved") ? $("#disapproved_by").hide() : $("#disapproved_by").show();
        (data.status != "Cancelled") ? $("#cancelled_by").hide() : $("#cancelled_by").show();
        (data.actual_time_start == "0000-00-00 00:00:00") ? $("#actual_time").hide() : $("#actual_time").show();

        switch (data.status) {
            case "Pending":
                $("#status_state").addClass("alert alert-warning text-white");
                $(".btnPending").show();
                break;
            case "Approved":
                $("#status_state").addClass("alert alert-success");
                $(".btnApproved").show();
                break;
            case "Disapproved":
                $("#status_state").addClass("alert alert-danger");
                $(".btnDisapproved").show();
                break;
            default:
                $("#status_state").addClass("alert alert-metal text-white");
                break;
        }
        
        if (valid_ot_dates === false && status === "Pending") {
            setTimeout(() => {
                Swal.fire({
                    title: 'Invalid Overtime Request!',
                    text: 'The overtime request is invalid. Please check the dates and times.',
                    icon: 'warning',
                });
            }, 750);
        }

        /*** Swal.fire({
            title: 'Invalid Overtime Request?',
            html: "Testing",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, '+tempTitle+' it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: siteUrl("hris/masterfile/approval_updated_payroll_data/"+nType),
                    type: "post",
                    data: { csrf_token: _csrf_hash, id: id },
                    dataType: "json",
                    success: function(json){
                        if(json.response){ 
                            toastr.success(json.toastr_msg, "For Approval");
                            dtForApproval.clear().rows.add(json.data).draw();
                        }else{ toastr.error(json.toastr_msg, "For Approval"); }
                    }
                });
            }
        }); ***/
    }
});

function edit() {
    location.href = 'edit_overtime?id=' + param_id;
}

$.formUtils.addValidator({
    name: 'checkbox_group_min1',
    validatorFunction: function (value, $el, config, language, $form) {
        return parseInt(value) > 0;
    },
    errorMessage: 'Select at least 1 image option!',
    errorMessageKey: 'checkboxMinimumOne'
});

$.validate({
    form: '#approve_form',
    lang: 'en',
    validateHiddenInputs: true,
    onSuccess: function (form) {

        if (vmTempImages.count > 0) {
            $.ajax({
                url: baseUrl("eforms/overtime/approve_overtime/") + param_id,
                type: "POST",
                dataType: "json",
                data: $("#approve_form").find("input").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data.state) {
                        $('#approve_modal').modal('hide');
                        toastr.success(data.message, "Updated successfully!", 5000);
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    } else {
                        toastr.error(data.message, "Error!", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        } else {
            toastr.error('Please upload atleast 1 attachment.', 'Approve Overtime');
        }

        return false;
    },
});

$.validate({
    form: '#undo_approval_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/overtime/undo_approve_overtime/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#undo_approval_form").find("input").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.state) {
                    $('#undo_approval_modal').modal('hide');
                    toastr.success(data.message, "Updated successfully!", 5000);
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(data.message, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.validate({
    form: '#disapprove_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/overtime/disapprove_overtime/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#disapprove_form").find("input").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.state) {
                    $('#disapprove_modal').modal('hide');
                    toastr.success(data.message, "Updated successfully!", 5000);
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(data.message, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.validate({
    form: '#undo_disapproval_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/overtime/undo_disapprove_overtime/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#undo_disapproval_form").find("input").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.state) {
                    $('#undo_disapproval_modal').modal('hide');
                    toastr.success(data.message, "Updated successfully!", 5000);
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(data.message, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.validate({
    form: '#cancel_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/overtime/cancel_overtime/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#cancel_form").find("input, textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.state) {
                    $('#cancel_modal').modal('hide');
                    toastr.success(data.message, "Updated successfully!", 5000);
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(data.message, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

function prints() {
    setTimeout(function(){
        window.open(baseUrl("eforms/overtime/print_overtime/") + param_id);
    }, 1500);
    
}

const approveModalFileUpload = function () {
    const url = baseUrl("eforms/overtime/temp_upload_file");
    $("#temp_fileupload")
        .fileupload({
            url: url,
            dataType: "json",
            formData: { csrf_token: _csrf_hash },
            done: function (e, data) {
                const result = data.result;
                if (result.response) {
                    toastr.success(result.toastr_msg, "Upload File", 5000);
                } else {
                    toastr.error(result.toastr_msg, "Upload File", 5000);
                }
            },
            progressall: function (e, data) {
                $("#progress_approve").show();
                let progress = parseInt((data.loaded / data.total) * 100, 10);
                let progressTotal = 0;

                const steps = setInterval(function () {
                    progressTotal += 10;
                    $("#progress_approve .progress-bar").css("width", progressTotal + "%");
                    if (progressTotal == 100) {
                        clearInterval(steps);
                        progressTotal = 0;
                        setTimeout(function () {
                            $("#progress_approve .progress-bar").css("width", progressTotal + "%");
                        }, 1500);
                    }
                }, 10);

                if (progress == 100) {
                    setTimeout(function () {
                        $("#progress_approve").hide();
                        getCurrentUploadFiles();
                    }, 1000);
                }
            }
        })
        .prop("disabled", !$.support.fileInput)
        .parent()
        .addClass($.support.fileInput ? undefined : "disabled");
};

const getCurrentUploadFiles = function () {
    $.ajax({
        url: baseUrl("eforms/overtime/get_current_uploaded_file"),
        dataType: "json",
        global: false,
        success: function (json) {
            if (json.response) {
                const tempRows = json.rows.sort(SortByDate);
                vmTempImages.rows = { ...tempRows };
                vmTempImages.count = json.count;
            }
        }
    });
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true
    });
}

var vmTempImages = new Vue({
    el: "#tempModalApproveImages",
    data: { count: 0, rows: {} },
    methods: {
        renderImageLabel: function (index) {
            var tempIndex = parseInt(index) + 1;
            return "Image " + tempIndex;
        },
        getCheckedCount: function () {
            var currentElement = this.$el;
            var checked = $(currentElement).find(".temp-attachment_image:checked");
            var checkedCounter = $(currentElement).find("#checked_count");
            checkedCounter.val(checked.length).validate();
        }
    }
});

jQuery(document).ready(function () {
    approveModalFileUpload();
    getCurrentUploadFiles();
});

function SortByDate(a, b){
    return new Date(b.created_date) - new Date(a.created_date);
}

function printSignatory() {
    $("#modal-import-overtime").modal();

    signatorySelect2('#signatory', true, _signatory);
}

const vmPrint = new Vue({
    el: '#signatory-content',
    data: {
        isPrint: false,
        signatory: [],
        approvedIds: param_id
    },
    methods: {
        editSignatory () {
            const instance = this;

            vmEditSignatory.row = instance.signatory;
            vmEditSignatory.count = instance.signatory.count;

            setTimeout(function () {
                vmEditSignatory.setModalSelect2();
            }, 500);
            $("#modal-ot--signatory").modal();
        },
        resetModalSignatory () {
            const instance = this;

            vmEditSignatory.row = instance.signatory;
            vmEditSignatory.count = instance.signatory.count;
            $("#modal-ot--reset-signatory").modal();
        }
    }
})

const vmEditSignatory = new Vue({
    el: "#updatePrintableSignatories",
    data: {
        row: [],
        count: 0
    },
    mounted: function () {
        const instance = this;
    },
    methods: {
        activeSignatory: function (e) {
            const currentTarget = e.target;
            const formGroup = $(currentTarget).closest(".form-group.m-form__group.row");
            if (typeof formGroup !== "undefined" && formGroup.length == 1) {
                let isChecked = $(currentTarget).is(":checked");
                const select2Container = formGroup.find(".select2--value");
                if (typeof select2Container !== "undefined" && select2Container.length == 1) {
                    if (isChecked) {
                        if (select2Container.is(":disabled") == true) {
                            select2Container.prop("disabled", false);
                        }
                    } else {
                        if (select2Container.is(":disabled") == false) {
                            select2Container.prop("disabled", true);
                        }
                    }
                }
            }
        },
        setModalSelect2: function () {
            const _this = this;
            const _currentElement = _this.$el;
            const meta = _this.row.meta;
            const psModalSignatory = $(_currentElement)
                .closest("#modal-ot--signatory");
            if (typeof psModalSignatory !== "undefined" && psModalSignatory.length == 1) {
                initSelect2Employee(psModalSignatory, meta);
            }
        }
    }
});

function signatorySelect2(targetElement, destroy = false, data = [], id = 0) {
    var currentElement = $(targetElement);
    var tempModal = $(currentElement).closest(".modal");    

    if (destroy) {
        if (currentElement.data("select2")) {
            currentElement.select2("destroy");
        }
        
        currentElement.off('select2:select');
        currentElement.empty();
    }

    const selected = id == 0 ? data.find(item => parseInt(item.company_id) == company_id) : data.find(item => parseInt(item.signatory_id) == id);

    if (selected) {
        var option = new Option(selected.text, selected.id, true, true);
        $('#signatory').append(option).trigger('change');

        vmPrint.signatory = Object.assign({}, {
            company_id: selected.company_id, 
            tempId: selected.tempId,
            signatory_id: selected.signatory_id, 
            text: selected.text, 
            meta : selected.meta,
            count: countTheObjects(selected.meta)
        });

        $("#editSignatory").removeClass('d-none');

        if (selected.allow_reset === true) {
            $("#resetSignatory").removeClass('d-none');

            vmResetSignatories.row = Object.assign({}, {
                company_id: selected.company_id, 
                tempId: selected.tempId,
                signatory_id: selected.signatory_id, 
                text: selected.text, 
                meta : selected.meta,
                count: countTheObjects(selected.meta)
            });
            vmResetSignatories.count = countTheObjects(selected.meta);
        }
    }

    $("#signatory").select2({
        placeholder: { id: -1, text: 'Select an Option'},
        data: data,
        width: '100%',
        dropdownParent: $("#signatory-content"),
        allowClear: true
    }).on('select2:select', function(e) {
        const _data = e.params.data;

        vmPrint.signatory = Object.assign({}, {
            company_id: _data.company_id, 
            tempId: _data.tempId,
            signatory_id: _data.signatory_id, 
            text: _data.text, 
            meta : _data.meta,
            count: countTheObjects(_data.meta)
        });

        $("#editSignatory").removeClass('d-none');

        if (_data.allow_reset === true) {
            $("#resetSignatory").removeClass('d-none');

            vmResetSignatories.row = Object.assign({}, {
                company_id: _data.company_id, 
                tempId: _data.tempId,
                signatory_id: _data.signatory_id, 
                text: _data.text, 
                meta : _data.meta,
                count: countTheObjects(_data.meta)
            });
            vmResetSignatories.count = countTheObjects(_data.meta);
        }

        const self = $(this);
        self.validate();

    }).on('select2:unselect', function () {
        // vmTempUploadedContent.signatory = [];
        $("#editSignatory").addClass('d-none');
        $("#resetSignatory").addClass('d-none');
    });

    if (!selected) {
        $("#signatory").val(null).trigger("change");
    }

    $("#modal-ot--signatory").on('shown.bs.modal', function(){
        $("#modal-import-overtime .modal-content").addClass('dimmed');
    });

    $("#modal-ot--signatory").on('hidden.bs.modal', function(){
        $("#modal-import-overtime .modal-content").removeClass('dimmed');
        selectedEmployee = [];
        if ($('.modal.show').length) {
            $('body').addClass('modal-open');
            var $lastModal = $('.modal.show').last();
            $lastModal.focus();
        }
    });
    
    $("#modal-ot--reset-signatory").on('shown.bs.modal', function(){
        $("#modal-import-overtime .modal-content").addClass('dimmed');
    });

    $("#modal-ot--reset-signatory").on('hidden.bs.modal', function(){
        $("#modal-import-overtime .modal-content").removeClass('dimmed');

        if ($('.modal.show').length) {
            $('body').addClass('modal-open');
            var $lastModal = $('.modal.show').last();
            $lastModal.focus();
        }
    });
}

function countTheObjects(arr) {
    return arr.filter((item) => item && typeof item === "object" && !Array.isArray(item)).length;
}

var initSelect2Employee = function (tempModal, ids) {
    if (typeof tempModal !== "undefined" && tempModal.length == 1) {
        let tempSelector = tempModal.find("select.select2--value");
        if (typeof portlet !== "undefined") { tempSelector = portlet.find("select.select2--value"); }

        if (typeof tempSelector !== "undefined") {
            if (typeof ids != 'undefined') {    
                $.each(ids, function(index, item) {
                    selectedEmployee.push(item.value);
                });
            }

            tempSelector.each((_i, select2) => {
                const $select = $(select2);
                if ($select.data("select2-initialized")) return;
                
                $select.next('.select2-container').remove();
                $select.val('');
                
                $select.off(".select2Events");
                
                if ($select.hasClass("select2-hidden-accessible")) {
                    $select.select2("destroy");
                }

                const value = ids?.[_i]?.value;
                if (value !== undefined && value !== null) {
                    let tempOption = new Option(value, value, true, true);
                    $select.append(tempOption).trigger('change');
                }

                let prevValue = null;

                $select.select2({
                    tags: true,
                    allowClear: true,
                    placeholder: 'Select an option',
                    width: '100%',
                    dropdownParent: $("#parent"),
                    ajax: {
                        url: baseUrl("eforms/overtime/select_signatory_employee"),
                        type: 'post',
                        dataType: "json",
                        delay: 250,
                        global: false,
                        data: function ({ term }) {
                            return {
                                csrf_token: _csrf_hash,
                                q: term,
                            }
                        },
                        processResults: function (data) {
                            let tempData = [];
                            $.each(data.results, function (i, v) {
                                const dd = { id: v.text, text: v.text, empId: v.id };
                                tempData.push(dd);
                            });
                            return { results: tempData };
                        }
                    }
                }).on('select2:opening.select2Events', function (e) {
                    prevValue = $(this).val();
                }).on('select2:select.select2Events', function(e) {
                    const self = $(e.target);
                    self.validate();
                    const data = e.params.data;
                });

                $select.data("select2-initialized", true);
            });
        }
    }
}

var vmResetSignatories = new Vue({
    el: "#reset-signatory--content",
    data: { row: {}, count: 0 },
});

$.validate({
    form: '#resetPrintableSignatories',
    lang: 'en',
    onSuccess: function (form) {
        const tempUrl = form[0].action;
        const tempType = form[0].method;
        const formData = $(form[0]).serialize();

        $.ajax({
            url: tempUrl,
            type: tempType,
            dataType: "json",
            data: formData,
            beforeSend: function () {
                $("#resetPrintableSignatories .btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                let tempRow = Object.assign({});
                let ctr = 0;

                if (json.response) {
                    tempRow = Object.assign({}, json.data);
                    ctr = json.count;

                    var index = _signatory.findIndex(function(item){ return parseInt(item.signatory_id) == tempRow.signatory_id});
                    _signatory[index] = tempRow;
                    signatorySelect2('#signatory', true, _signatory, tempRow.signatory_id);
                }

                vmEditSignatory.row = Object.assign({}, tempRow);
                vmEditSignatory.count = ctr;

                vmPrint.signatory = Object.assign({}, {
                    company_id: tempRow.company_id, 
                    tempId: tempRow.tempId,
                    signatory_id: tempRow.signatory_id, 
                    text: tempRow.text, 
                    meta : tempRow.meta,
                    count: countTheObjects(tempRow.meta)
                });

                if (tempRow.allow_reset === true) {
                    vmResetSignatories.row = Object.assign({}, tempRow);
                    vmResetSignatories.count = ctr;

                    $("#resetSignatory").removeClass('d-none');
                } else {
                    $("#resetSignatory").addClass('d-none');
                }

                const currentModal = $('#modal-ot--reset-signatory').closest(".modal");
                currentModal.modal("hide");
                $("#resetPrintableSignatories .btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },

});

$.validate({
    form: '#updatePrintableSignatories',
    lang: 'en',
    onSuccess: function (form) {
        const tempUrl = form[0].action;
        const tempType = form[0].method;
        const formData = $(form[0]).serialize();

        $.ajax({
            url: tempUrl,
            type: tempType,
            dataType: "json",
            data: formData,
            beforeSend: function () {
                $("#updatePrintableSignatories .btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                const currentModal = $('#modal-ot--signatory');
                if (json.response) {
                    const currentData = json.data;
                    if (Object.keys(currentData).length > 0) {
                        const metaFields = currentData.meta;
                        const ctr = metaFields.length;

                        var index = _signatory.findIndex(function(item){ return parseInt(item.signatory_id) == currentData.signatory_id});
                        _signatory[index] = currentData;
                        signatorySelect2('#signatory', true, _signatory, currentData.signatory_id);

                        vmEditSignatory.row = Object.assign({}, currentData);
                        vmEditSignatory.count = ctr;

                        vmPrint.signatory = Object.assign({}, {
                            company_id: currentData.company_id, 
                            tempId: currentData.tempId,
                            signatory_id: currentData.signatory_id, 
                            text: currentData.text, 
                            meta : currentData.meta,
                            count: countTheObjects(currentData.meta)
                        });

                        if (currentData.allow_reset === true) {
                            vmResetSignatories.row = Object.assign({}, currentData);
                            vmResetSignatories.count = ctr;

                            $("#resetSignatory").removeClass('d-none');
                        } else {
                            $("#resetSignatory").addClass('d-none');
                        }

                        if (typeof currentModal !== "undefined" && currentModal.length == 1) {
                            currentModal.modal("hide");
                        }

                        selectedEmployee = [];
                    }
                } else {
                    toastr.error("Overtime Signatory", json.toastr_msg);
                }
                $("#updatePrintableSignatories .btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },

});

$.validate({
    form: '#print-import_overtime',
    lang: 'en',
    onSuccess: function (form) {
        const currentForm = form[0];
        const formData = $(currentForm).serialize();
        const currentModal = $(currentForm).closest(".modal");

        $.ajax({
            url: baseUrl('eforms/overtime/print_summary'),
            type: 'post',
            data: formData,
            dataType: 'json',
            beforeSend: function () {
                $("#print-import_overtime .btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (response) {
                var printWindow = window.open('', '_blank');
                printWindow.document.open();
                printWindow.document.write(response.html);
                printWindow.document.close();

                printWindow.addEventListener('load', function() {
                    setTimeout(function() {
                        printWindow.focus();
                        printWindow.print();
                        
                        printWindow.addEventListener('afterprint', function() {
                            printWindow.close();
                        });
                        
                        setTimeout(function() {
                            if (!printWindow.closed) {
                                printWindow.close();
                            }
                        }, 500);
                    }, 100);
                });

                $("#print-import_overtime .btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });

        return false;
    }
})