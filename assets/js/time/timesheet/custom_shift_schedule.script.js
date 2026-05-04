const customShiftModal = $("#custom-shift-schedule-modal");
const editCustomShiftModal = $("#edit-custom-shift-schedule-modal");
const deleteCustomShiftModal = $("#delete-custom-shift-schedule-modal");
const customShiftMoreDetailsModal = $("#more_details_custom-shift-schedule-modal");
const customShiftTable = $("#tbl-time-custom_shift_schedule");
let dtCustomShiftSchedule;

$.formUtils.addValidator({
    name: 'atleast_one',
    validatorFunction: function (value, $el, config, language, $form) {
        return parseFloat(value) > 0;
    },
    errorMessage: 'You have to fill up atleast 1 shift schedule time IN or OUT!',
    errorMessageKey: 'badTimeCount'
});

$.validate({
    form: "#frmAddCustomShift",
    lang: "en",
    scrollToTopOnError: false,
    validateHiddenInputs: true,
    onSuccess: function (form) {
        const currentForm = form[0];
        const formUrl = currentForm.action;
        const formData = $(currentForm).serialize();
        const btnSubmit = $("button[type='submit']", form);

        $.ajax({
            url: formUrl,
            data: formData,
            type: "post",
            dataType: "json",
            global: false,
            beforeSend: function () {
                btnSubmit.addClass('m-btn--custom m-loader m-loader--light m-loader--left');
                btnSubmit.html("Please wait...");
                $(':input', form).prop('disabled', true);
            },
            success: function (json) {
                if (json.response) {
                    toastr.success(json.toastr_msg, "New Custom Shift Schedule");
                    currentForm.reset();
                    $(currentForm).find("#shift_id").val("").trigger("change");
                    dtCustomShiftSchedule.ajax.reload(null, false);
                    customShiftModal.modal("hide");
                } else {
                    toastr.error(json.toastr_msg, "New Custom Shift Schedule");
                }
                setTimeout(function () {
                    btnSubmit.removeClass('m-btn--custom m-loader m-loader--light m-loader--left');
                    btnSubmit.html("Save");
                    $(':input', form).prop('disabled', false);
                }, 1500);
            }
        });

        return false;
    }
});

var validateEditedSchedule = function () {
    $.validate({
        form: "#frmEditCustomShift",
        lang: "en",
        scrollToTopOnError: false,
        validateHiddenInputs: true,
        onSuccess: function (form) {
            const currentForm = form[0];
            const formUrl = currentForm.action;
            const formData = $(currentForm).serialize();
            const btnSubmit = $("button[type='submit']", form);

            $.ajax({
                url: formUrl,
                data: formData,
                type: "post",
                dataType: "json",
                global: false,
                beforeSend: function () {
                    btnSubmit.addClass('m-btn--custom m-loader m-loader--light m-loader--left');
                    btnSubmit.html("Please wait...");
                    $(':input', form).prop('disabled', true);
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(json.toastr_msg, "Update Custom Shift Schedule");
                        currentForm.reset();
                        $(currentForm).find("#shift_id").val("").trigger("change");
                        dtCustomShiftSchedule.ajax.reload(null, false);
                        editCustomShiftModal.modal("hide");
                    } else {
                        toastr.error(json.toastr_msg, "Update Custom Shift Schedule");
                    }
                    setTimeout(function () {
                        btnSubmit.removeClass('m-btn--custom m-loader m-loader--light m-loader--left');
                        btnSubmit.html("Save");
                        $(':input', form).prop('disabled', false);
                    }, 1500);
                }
            });

            return false;
        }
    });
}
validateEditedSchedule();

if (typeof customShiftModal !== "undefined" && customShiftModal.length == 1) {
    customShiftModal.on('show.bs.modal', function (e) {
        var currentTarget = $(e.target);
        var currentForm = currentTarget.find("form");
        if (typeof currentForm !== "undefined" && currentForm.length == 1) {
            currentForm[0].reset();
            vmNewCustomShift.setModalRenderer();
        }
    });
}

if (typeof customShiftTable !== "undefined" && customShiftTable.length == 1) {
    dtCustomShiftSchedule = customShiftTable.DataTable({
        dom: '<"row"<"col-12" rt>><"row"<"col-6" l><"col-6" p>>',
        serverSide: true,
        destroy: true,
        ajax: {
            url: baseUrl('gcctime/timesheet/get_custom_shift_schedule_request'),
            type: 'POST',
            dataType: 'JSON',
            data: function (_data) {
                let statuses = [];
                _data.csrf_token = _csrf_hash;
                _data.status = statuses;
            },
            global: false
        },
        order: [[0, 'desc']],
        columns: [
            { data: 'scheduled_date', width: "6%" },
            {
                data: 'scheduled_date', width: "8%", render: function (data) {
                    return moment(data).format('dddd');
                }
            },
            {
                data: 'shift_am_start', width: "6%", orderable: false, render: function (data) {
                    var tempHtml = "--:--";
                    tempHtml = (data && data !== "--:--") ? moment(data, "HH:mm:ss").format("LT") : data;
                    return tempHtml;
                }
            },
            {
                data: 'shift_am_end', width: "6%", orderable: false, render: function (data) {
                    var tempHtml = "--:--";
                    tempHtml = (data && data !== "--:--") ? moment(data, "HH:mm:ss").format("LT") : data;
                    return tempHtml;
                }
            },
            {
                data: 'shift_pm_start', width: "6%", orderable: false, render: function (data) {
                    var tempHtml = "--:--";
                    tempHtml = (data && data !== "--:--") ? moment(data, "HH:mm:ss").format("LT") : data;
                    return tempHtml;
                }
            },
            {
                data: 'shift_pm_end', width: "6%", orderable: false, render: function (data) {
                    var tempHtml = "--:--";
                    tempHtml = (data && data !== "--:--") ? moment(data, "HH:mm:ss").format("LT") : data;
                    return tempHtml;
                }
            },
            {
                data: 'shift_resource',
                width: "*",
                orderable: false,
                render: function (data) {
                    let tempResource = ``;
                    if (data.length > 0) {
                        $.each(data, function (index, value) {
                            tempResource += `<span class="m-badge m-badge--metal m-badge--wide m-badge--rounded m--font-boldest mb-2">${value}</span> `;
                        });
                    } else {
                        tempResource += `<span class="m-badge m-badge--warning m-badge--wide m-badge--rounded m--font-boldest">No Assigned Shift</span> `;
                    }
                    return tempResource;
                }
            },
            {
                data: "employee_count",
                width: "12%",
                orderable: false,
                className: "text-center",
                render: function (data) {
                    let tempResource = `<span class="m-badge m-badge--brand">${data}</span>`;
                    return tempResource;
                }
            },
            {
                data: "employees",
                width: "14%",
                orderable: false,
                className: "text-center",
                render: function (data) {
                    let html = '';

                    if (data.length > 0) {
                        $.each(data, function(index, value){
                            html += `<span class="m-badge m-badge--metal m-badge--wide m-badge--rounded m--font-boldest mb-2" style="font-size: 10px">${value}</span> `;
                        });
                    } else {
                        html += '---';
                    }

                    return html;
                }
            },
            {
                data: "has_shift",
                width: "5%",
                orderable: false,
                className: "text-center",
                render: function (data) {
                    let _hasShift = parseInt(data) == 1 ? true : false;
                    let tempClass = _hasShift ? "fa-check-circle-o m--font-success" : "fa-times-circle-o m--font-danger";
                    let tempHtml = `<i class="fa ${tempClass}" style="font-size: 22px;"></i>`;
                    return tempHtml;
                }
            },
            {
                data: null,
                width: "4%",
                orderable: false,
                className: "text-center",
                render: function (data, type, row, meta) {
                    const template = `
                        <div class="m-dropdown m-dropdown--inline m-dropdown--align-right m-dropdown--large" 
                                data-dropdown-toggle="click" aria-expanded="true">
                            <a href="#" class="m-dropdown__toggle btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill"
                                id="ellipses-menu-${meta.row}" data-toggle="m-tooltip" data-original-title="More Options" data-skin="dark"
                                data-delay='{"show": 500}'>
                                <i class="fa fa-ellipsis-v"></i>
                            </a>
                            <div class="m-dropdown__wrapper">
                                <span class="m-dropdown__arrow m-dropdown__arrow--right"></span>
                                <div class="m-dropdown__inner">
                                    <div class="m-dropdown__body">
                                        <div class="m-dropdown__content">
                                            <ul class="m-nav">
                                                <li class="m-nav__section m-nav__section--first">
                                                    <span class="m-nav__section-text">OPTIONS</span>
                                                </li>
                                                <li class="m-nav__item more-details-link">
                                                    <a href="javascript:void(0)" class="m-nav__link" 
                                                        onclick="moreDetailsCustomShiftSchedule(${row.id})">
                                                        <i class="m-nav__link-icon fa fa-info-circle"></i>
                                                        <span class="m-nav__link-text">MORE DETAILS</span>
                                                    </a>
                                                </li>
                                                <li class="m-nav__item more-details-link">
                                                    <a href="javascript:void(0)" class="m-nav__link" 
                                                        onclick="editCustomShiftSchedule(${row.id})">
                                                        <i class="m-nav__link-icon fa fa-pencil"></i>
                                                        <span class="m-nav__link-text">Edit Custom Shift Schedule</span>
                                                    </a>
                                                </li>
                                                <li class="m-nav__item more-details-link">
                                                    <a href="javascript:void(0)" class="m-nav__link" 
                                                        onclick="deleteCustomShiftSchedule(${row.id})">
                                                        <i class="m-nav__link-icon fa fa-trash"></i>
                                                        <span class="m-nav__link-text">Remove Custom Shift Schedule</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    return template;
                }
            }
        ]
    });
}

var vmEditCustomSchedule = new Vue({
    el: "#modal-edit_custom_content",
    data: { row: {}, prop_shift: false },
    methods: {
        prop_shift_schedule: function ($el) {
            let _this = this;
            const currentElement = _this.$el;
            const currentValue = $el.target.value;
            _this.prop_shift = parseInt(currentValue) == 1 ? true : false;
            if (currentValue == "0") {
                $(currentElement)
                    .find(".check-atleast_one")
                    .val("")
                    .prop("disabled", true);
            }
        },
        prop_row_shift_schedule() {
            var _this = this;
            var currentElement = _this.$el;
            var currentRow = _this.row;
            currentRow.prop_shift = false;
            if (typeof currentRow.has_shift !== "undefined" && parseInt(currentRow.has_shift) == 1) {
                currentRow.prop_shift = true;
                $(currentElement)
                    .find(".check-atleast_one")
                    .prop("disabled", false)
            } else {
                $(currentElement)
                    .find(".check-atleast_one")
                    .val("")
                    .prop("disabled", true);
            }
            return _this;
        },
        getShiftScheduleSelect2: function () {
            var _this = this;
            var currentElement = _this.$el;
            var currentRow = _this.row;
            var select2Shift = $(currentElement).find("#shift_id");
            var select2Employee = $(currentElement).find("#employee_id");
            if (typeof select2Shift !== "undefined" && select2Shift.length == 1) {
                select2Shift.select2({
                    width: '100%',
                    ajax: {
                        delay: 150,
                        global: false,
                        url: baseUrl('gcctime/timesheet/get_shift_schedule_for_filter/' + currentRow.id),
                        dataType: 'JSON',
                        type: 'GET',
                        dropdownParent: editCustomShiftModal,
                        data: function (params) {
                            var tempDate = $(currentElement).find("#scheduled_date").val();
                            if (typeof tempDate !== "undefined" && tempDate) {
                                params.scheduled_date = tempDate;
                            }
                            return params;
                        },
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    },
                    templateResult: function (data) {
                        return data.html;
                    },
                    templateSelection: function (data) {
                        return data.text;
                    }
                });

                if (typeof currentRow.shift_id == "object" && currentRow.shift_id.length > 0) {
                    select2Shift.empty();
                    $.each(currentRow.shift_id, function (ii, vv) {
                        var tempId = vv;
                        var tempValue = currentRow.shift_resource[ii];
                        var tempOption = new Option(tempValue, tempId, true, true);
                        select2Shift.append(tempOption);
                    });
                }
            }
            if (typeof select2Employee !== "undefined" && select2Employee.length == 1) {
                select2Employee.select2({
                    width: '100%',
                    ajax: {
                        delay: 150,
                        global: false,
                        url: baseUrl('gcctime/timesheet/get_shift_schedule_for_employee_filter/' + currentRow.id),
                        dataType: 'JSON',
                        type: 'GET',
                        dropdownParent: editCustomShiftModal,
                        data: function (params) {
                            var tempDate = $(currentElement).find("#scheduled_date").val();
                            if (typeof tempDate !== "undefined" && tempDate) {
                                params.scheduled_date = tempDate;
                            }
                            return params;
                        },
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    },
                    templateResult: function (data) {
                        return data.html;
                    },
                    templateSelection: function (data) {
                        return data.text;
                    }
                });

                if (typeof currentRow.employee_id == "object" && currentRow.employee_id.length > 0) {
                    select2Employee.empty();
                    $.each(currentRow.employee_id, function (ii, vv) {
                        var tempId = vv;
                        var tempValue = currentRow.employee_resource[ii];
                        var tempOption = new Option(tempValue, tempId, true, true);
                        select2Employee.append(tempOption);
                    });
                }
            }
        }, setModalRenderer: function () {
            var _this = this;
            var currentElement = _this.$el;
            var currentRow = _this.row;

            var arrDates = ["shift_am_start", "shift_am_end", "shift_pm_start", "shift_pm_end"];
            var ctr = 0;
            $.each(arrDates, function (iix, vvx) {
                if (typeof currentRow[vvx] !== "undefined" && currentRow[vvx]) {
                    ctr++;
                }
            });

            $(currentElement).find("#atleast_one").val(ctr);
            var checkAtleast = $(currentElement).find(".check-atleast_one");
            if (typeof checkAtleast !== "undefined" && checkAtleast.length > 0) {
                checkAtleast.donetyping(function () {
                    var tempCtr = 0;
                    var _this = this;
                    $.each(_this, function (index, element) {
                        hasValue = $(element).val();
                        tempCtr += (hasValue) ? 1 : 0;
                    });
                    $(currentElement).find("#atleast_one").val(tempCtr).validate();
                }, 500);
                checkAtleast.on("change", function (e) {
                    var tempCtr = 0;
                    $.each(checkAtleast, function (index, element) {
                        hasValue = $(element).val();
                        tempCtr += (hasValue) ? 1 : 0;
                    });
                    $(currentElement).find("#atleast_one").val(tempCtr).validate();
                });
            }

            $(currentElement).find("#date-schedule").datepicker({
                defaultDate: '',
                todayHighlight: true,
                orientation: 'bottom left',
                autoclose: true,
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                },
                format: 'yyyy-mm-dd',
                container: editCustomShiftModal
            }).on("changeDate", function (e) {
                var scheduleDate = $(e.target).find("input#scheduled_date");
                if (typeof scheduleDate !== "undefined" && scheduleDate.length == 1) {
                    scheduleDate.validate();
                }
            });

            //$(currentElement).find('#shift_id').select2('destroy');

            $(currentElement).find("#am-start, #am-end, #pm-start, #pm-end").timepicker({
                defaultTime: null,
                minuteStep: 1,
                showSeconds: false,
                showMeridian: true,
                snapToStep: true,
                change: function (time) {
                    var element = $(this), text;
                    // get access to this Timepicker instance
                    var timepicker = element.timepicker();
                    text = 'Selected time is: ' + timepicker.format(time);
                    element.siblings('span.help-line').text(text);
                }
            });
            $(currentElement).find("#am-start").timepicker('setTime', currentRow.shift_am_start);
            $(currentElement).find("#am-end").timepicker('setTime', currentRow.shift_am_end);
            $(currentElement).find("#pm-start").timepicker('setTime', currentRow.shift_pm_start);
            $(currentElement).find("#pm-end").timepicker('setTime', currentRow.shift_pm_end);
        }
    }, mounted: function () {
        this.setModalRenderer();
        this.getShiftScheduleSelect2();
        this.prop_row_shift_schedule();
        validateEditedSchedule();
    }
});

var vmNewCustomShift = new Vue({
    el: "#tempCustomShiftContent",
    data: { prop_shift: true },
    methods: {
        prop_shift_schedule: function ($el) {
            let _this = this;
            const currentElement = _this.$el;
            const currentValue = $el.target.value;
            if (currentValue == "0") {
                $(currentElement).find(".check-atleast_one").val("");
            }
            _this.prop_shift = parseInt(currentValue) == 1 ? true : false;
        },
        setModalRenderer: function () {
            var _this = this;
            var currentElement = _this.$el;
            var currentRow = _this.row;

            var currentModal = $(currentElement).closest(".modal");

            var checkAtleast = $(currentElement).find(".check-atleast_one");
            if (typeof checkAtleast !== "undefined" && checkAtleast.length > 0) {
                checkAtleast.donetyping(function () {
                    var tempCtr = 0;
                    var _this = this;
                    $.each(_this, function (index, element) {
                        hasValue = $(element).val();
                        tempCtr += (hasValue) ? 1 : 0;
                    });
                    $(currentElement).find("#atleast_one").val(tempCtr).validate();
                }, 500);
                checkAtleast.on("change", function (e) {
                    var tempCtr = 0;
                    $.each(checkAtleast, function (index, element) {
                        hasValue = $(element).val();
                        tempCtr += (hasValue) ? 1 : 0;
                    });
                    $(currentElement).find("#atleast_one").val(tempCtr).validate();
                });
            }

            $(currentElement).find("#date-schedule").datepicker({
                defaultDate: '',
                todayHighlight: true,
                orientation: 'bottom left',
                autoclose: true,
                todayBtn: 'linked',
                clearBtn: true,
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                },
                format: 'yyyy-mm-dd',
                container: currentModal
            }).on("changeDate", function (e) {
                $(e.target).find("input#scheduled_date").validate();
            });

            $(currentElement).find('#shift_id').select2({
                width: '100%',
                ajax: {
                    delay: 1000,
                    global: false,
                    url: baseUrl('gcctime/timesheet/get_shift_schedule_for_filter'),
                    dataType: 'JSON',
                    type: 'GET',
                    dropdownParent: currentModal,
                    data: function (params) {
                        var tempDate = $(currentElement).find("#scheduled_date").val();
                        if (typeof tempDate !== "undefined" && tempDate) {
                            params.scheduled_date = tempDate;
                        }
                        return params;
                    },
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                templateResult: function (data) {
                    return data.html;
                },
                templateSelection: function (data) {
                    return data.text;
                }
            });

            $(currentElement).find('#employee_id').select2({
                width: '100%',
                ajax: {
                    delay: 1000,
                    global: false,
                    url: baseUrl('gcctime/timesheet/get_shift_schedule_for_employee_filter'),
                    dataType: 'JSON',
                    type: 'GET',
                    dropdownParent: currentModal,
                    data: function (params) {
                        var tempDate = $(currentElement).find("#scheduled_date").val();
                        if (typeof tempDate !== "undefined" && tempDate) {
                            params.scheduled_date = tempDate;
                        }
                        return params;
                    },
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                templateResult: function (data) {
                    return data.html;
                },
                templateSelection: function (data) {
                    return data.text;
                }
            });

            $(currentElement).find("#am-start, #am-end, #pm-start, #pm-end").timepicker({
                defaultTime: null,
                minuteStep: 1,
                showSeconds: false,
                showMeridian: true,
                snapToStep: true,
                change: function (time) {
                    var element = $(this), text;
                    // get access to this Timepicker instance
                    var timepicker = element.timepicker();
                    text = 'Selected time is: ' + timepicker.format(time);
                    element.siblings('span.help-line').text(text);
                }
            });
        }
    }
});

var vmDeleteCustomShift = new Vue({
    el: "#delete_custom-shift_content",
    data: { row: {} },
    methods: {
        updateTimeFormat: function (time) {
            return moment(time, "HH:mm:ss").format("LT");
        },
        getWeekdayFormat: function (date) {
            return moment(date).format('dddd');
        },
        removeCurrentShiftSchedule: function () {
            var _this = this;
            var currentRow = _this.row;
            if (typeof currentRow.id !== "undefined" && parseInt(currentRow.id) > 0) {
                $.ajax({
                    url: siteUrl("gcctime/timesheet/remove_custom_shift_schedule"),
                    dataType: "json",
                    type: "post",
                    data: { [_csrf_token]: _csrf_hash, id: currentRow.id },
                    success: function (json) {
                        if (json.response) {
                            dtCustomShiftSchedule.ajax.reload(null, false);
                            deleteCustomShiftModal.modal("hide");
                        }
                        toastr[json.state](json.toastr_msg, "Remove Custom Shift Schedule");
                    }
                });
            }
            console.log(this);
        }
    }
});

var vmMoreDetails = new Vue({
    el: "#custom-shift_content",
    data: { row: {} },
    methods: {
        updateTimeFormat: function (time) {
            return moment(time, "HH:mm:ss").format("LT");
        },
        getWeekdayFormat: function (date) {
            return moment(date).format('dddd');
        },
        getResourceCount($resource = null) {
            var self = this;
            var currentRow = self.row;
            var count = 0;
            if ($resource) {
                var tempKey = $resource + "_id";
                if (typeof currentRow[tempKey] !== "undefined") {
                    count = currentRow[tempKey].length;
                }
            }
            return count;
        }
    }
});

var moreDetailsCustomShiftSchedule = function (id) {
    if (id) {
        $.ajax({
            url: siteUrl("gcctime/timesheet/get_custom_shift_schedule_data/" + id),
            dataType: "json",
            success: function (json) {
                var tempData = Object.assign({});
                if (json.response) { tempData = Object.assign({}, json.data); }
                vmMoreDetails.row = tempData;
                customShiftMoreDetailsModal.modal("show");
            }

        });
    }
}

var editCustomShiftSchedule = function (id) {
    if (id) {
        $.ajax({
            url: siteUrl("gcctime/timesheet/get_custom_shift_schedule_data/" + id),
            dataType: "json",
            success: function (json) {
                var tempData = Object.assign({});
                if (json.response) { tempData = Object.assign({}, json.data); }
                vmEditCustomSchedule.row = tempData;
                vmEditCustomSchedule.$mount();
                editCustomShiftModal.modal("show");
            }
        });
    }
}

var deleteCustomShiftSchedule = function (id) {
    if (id) {
        $.ajax({
            url: siteUrl("gcctime/timesheet/get_custom_shift_schedule_data/" + id),
            dataType: "json",
            success: function (json) {
                var tempData = Object.assign({});
                if (json.response) { tempData = Object.assign({}, json.data); }
                vmDeleteCustomShift.row = tempData;
                deleteCustomShiftModal.modal("show");
            }
        });
    }
}

$(document).ready(function () { });