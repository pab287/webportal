const datePicker = $('#date-picker');
const createTimeAdjustmentModal = $('#create-time-adjustment-modal');
const timeManualEntryModal = $('#time-manual-entry-modal');
const timeManualOvertimeEntryModal = $('#time-manual-overtime-entry-modal');
const overtimeManualEntryModal = $('#overtime-manual-entry-modal');
const alertModal = $('#alert-modal');
const cbSelectAll = $('#cb-select-all');
const tblTimesheet = $('#tbl-timesheet');
const confirmationModal = $('#timesheet-confirmation-modal');
const modalContainer = $('#container-modal');
const timeAdjustmentDetailsContainerModal = $('#time-adjustment-details-container-modal');
const importModal = $('#timesheet-import-modal');
const noEmployeeBiometricModal = $('#no-employee-biometric-modal');
const newEmployeeModal = $('#new-employee-modal');
const importHistoryModal = $('#import-history-modal');
const addExcludedEmployeeModal = $("#add-excluded-employee-from-timesheet-modal");
const editExcludedEmployeeModal = $("#edit-excluded-employee-from-timesheet-modal");
const generateManuallyModal = $("#generate-manually-modal");
const tsPossibleDuplicatesModal = $("#import-timesheet-possible-duplicates-modal");
const dropdown = $("#btn-export-timesheet > i");
const tblExcludedEmployees = $("#tbl-excluded-employees");
const possibleMatchesModal = $("#possible-matches-modal");
const lookUpAndUpdateModal = $("#look-up-and-update-modal");
const addShiftModal = $("#add-shift-modal");
const customShiftModal = $("#modal-custom-shift-schedule");

const monthlyEmployeeModal = $("#monthly-employees-list-modal");
const importInvalidModal = $("#timesheet-import-invalid-modal");

let dtTimesheet;
let employeeImage = null;
let dtImportHistory;
let dtExcludedEmployees;

const activeStatusOptions = '' +
    '<option value=""></option>' +
    '<option value="REGULAR">REGULAR</option>' +
    '<option value="PROBATIONARY">PROBATIONARY</option>' +
    '<option value="NO CONTRACT">NO CONTRACT</option>' +
    '<option value="RETIRED">RETIREE</option>' +
    '<option value="CONSULTANT">CONSULTANT/RETAINER</option>' +
    '<option value="PROJECT BASED">PROJECT BASED</option>';

const inactiveStatusOptions = '' +
    '<option value=""></option>' +
    '<option value="RESIGN">RESIGNED</option>' +
    '<option value="TERMINATED">TERMINATED</option>' +
    '<option value="BLACKLISTED">BLACKLISTED</option>' +
    '<option value="END OF CONTRACT">END OF CONTRACT</option>' +
    '<option value="INDEFINITE LEAVE">INDEFINITE LEAVE</option>';

toastr.options = {
    timeOut: 10000,
    newestOnTop: true,
    positionClass: 'toast-bottom-right toast-opacity-1',
    closeButton: true
};

function urltoFile(url, filename, mimeType) {
    mimeType = mimeType || (url.match(/^data:([^;]+);/) || '')[1];
    return (fetch(url)
        .then(function (res) {
            return res.arrayBuffer();
        })
        .then(function (buf) {
            return new File([buf], filename, { type: mimeType });
        })
    );
}

const dataURItoBlob = (dataURI) => {
    const bytes = dataURI.split(',')[0].indexOf('base64') >= 0
        ? atob(dataURI.split(',')[1])
        : unescape(dataURI.split(',')[1]);
    const mime = dataURI.split(',')[0].split(':')[1].split(';')[0];
    const max = bytes.length;
    const ia = new Uint8Array(max);
    for (let i = 0; i < max; i += 1) ia[i] = bytes.charCodeAt(i);
    return new Blob([ia], { type: mime });
};

const resizeImage = ({ file, maxSize }) => {
    const reader = new FileReader();
    const image = new Image();
    const canvas = document.createElement('canvas');

    const resize = () => {
        let { width, height } = image;

        if (width > height) {
            if (width > maxSize) {
                height *= maxSize / width;
                width = maxSize;
            }
        } else if (height > maxSize) {
            width *= maxSize / height;
            height = maxSize;
        }

        canvas.width = width;
        canvas.height = height;
        canvas.getContext('2d').drawImage(image, 0, 0, width, height);

        const dataUrl = canvas.toDataURL('image/jpeg');

        return dataURItoBlob(dataUrl);
    };

    return new Promise((ok, no) => {
        if (!file.type.match(/image.*/)) {
            no(new Error('Not an image'));
            return;
        }

        reader.onload = (readerEvent) => {
            image.onload = () => ok(resize());
            image.src = readerEvent.target.result;
        };

        reader.readAsDataURL(file);
    });
};

$('#file-image', newEmployeeModal)
    .on('change', function () {
        const file = this.files[0];
        const image_holder = $('#image--holder');

        if (file && file !== undefined) {
            const name = file.name;

            resizeImage({ file, maxSize: 250 })
                .then((resizedImage) => {
                    const reader = new FileReader();
                    reader.readAsDataURL(resizedImage);
                    reader.onloadend = function () {
                        const base64data = reader.result;
                        image_holder.attr('src', base64data);
                        employeeImage = { base64data, name };
                    }
                })
                .catch((err) => {
                });
        } else {
            image_holder.attr('src', baseUrl(`assets/images/profile/no_image.jpg`));
        }
    });

cbSelectAll.on('change', function (e) {
    const checkedValue = e.target.checked;
    $('tbody input[type=\'checkbox\']', tblTimesheet).prop('checked', checkedValue);

    if (checkedValue) {
        $('#btn-verify').removeAttr('disabled');
    } else {
        $('#btn-verify').attr('disabled', 'true');
    }
});

$(document).tooltip({
    selector: '[data-toggle="m-tooltip"]',
    container: 'body'
});

const vmTimsheetActions = new Vue({
    el: "#overtime_modal_action",
    data: { has_overtime_request: false },
    methods: {
        renderOvertimeModal: function(){
            $("#overtime-nobreak-modal").modal();
        }
    }
});

const dtOvertimeRecords = $("#tbl-overtime--nobreak").DataTable({
    ordering: false,
    columns: [
        { data: "tsID", className: "text-center", render: function(data){
            return `<input type="checkbox" name="selected_ot[]" value="${data}" />`;
        } },
        { data: "date", className: "text-center" },
        { data: "employee_name" },
        { data: "overtime_in", className: "text-center" },
        { data: "overtime_out", className: "text-center" },
        { data: "accredited_ot_hrs", className: "text-right" },
        { data: "accredited_ndiff_ot_hrs", className: "text-right" },
    ],
});

$(".btnUpdateTsNoBreak").on("click", function(){
    const chkboxes = $("#tbl-overtime--nobreak").find("input[type=checkbox]");
    if(typeof chkboxes != "undefined" && chkboxes.length > 0){
        let checkedCtr = 0;
        let tsIds = [];
        $.each(chkboxes, function(k, v){
            const isChecked = $(v).is(":checked");
            if(isChecked){
                tsIds.push($(v).val()); 
                checkedCtr++;
            }
        });

        if(checkedCtr > 0){
            $.ajax({
                url: siteUrl("gcctime/timesheet/timesheet_no_overtime_break"),
                type: "post",
                data: { [_csrf_token]: _csrf_hash, ts_id: tsIds },
                dataType: "json",
                success: function(json){
                    if(json.response){
                        dtTimesheet.ajax.reload(null, false);
                        $("#overtime-nobreak-modal").modal("hide");
                        toastr.success(json.message, "Timesheet Overtime");
                    }else{
                        Swal.fire({
                            title: 'Timesheet Overtime',
                            text: json.message,
                            icon: 'error',
                        });
                    }
                }
            });
        }else{
            Swal.fire({
                title: 'Update!',
                text: "Nothing to update!",
                icon: 'error',
            });
        }
    }
});

$(document)
    .ready(function () {
        const defaultDate = moment().subtract('1', 'days');
        $('#cut-offs')
            .select2({
                width: '100%',
                placeholder: 'SELECT CUT OFF',
                allowClear: true,
            })
            .on('change', function (e) {
                const data = e.target.value;
                $('.form-control', datePicker).val(data ? null : (defaultDate.format('MMM DD, YYYY') + ' / ' + defaultDate.format('MMM DD, YYYY')));
                $('.form-control', datePicker).prop('disabled', !!data);
            });

        $('#employees').select2({
            width: '100%',
            ajax: {
                delay: 1000,
                global: false,
                url: baseUrl('gcctime/timesheet/get_employees_for_filter'),
                dataType: 'JSON',
                type: 'GET'
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

        $('#shift_id').select2({
            width: '100%',
            ajax: {
                delay: 1000,
                global: false,
                url: baseUrl('gcctime/timesheet/get_shift_schedule_for_filter'),
                dataType: 'JSON',
                type: 'GET',
                dropdownParent: customShiftModal,
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

        $("#am-start, #am-end, #pm-start, #pm-end").timepicker({
            defaultTime: null,
            minuteStep: 1,
            showSeconds: false,
            showMeridian: true,
            snapToStep: true,
        });

        $('#timesheet-status-filter')
            .select2({
                allowClear: true,
                placeholder: 'FILTER BY STATUS',
                width: '100%',
            })
            .on('change', function (e) {
                dtTimesheet.ajax.reload();
            });

        $('#company').select2({
            allowClear: true,
            placeholder: 'SELECT COMPANY',
            width: '100%'
        });

        $("#payroll_group").select2({
            placeholder: 'Select',
            width: '100%',
            allowClear: true,
            // minimumInputLength: 1,
            ajax: {
                url: siteUrl("gcctime/timesheet/select_payroll_group"),
                dataType: "json",
                type: 'get',
                delay: 250,
                global: false,
                data: function (params) {
                    params.company_id = $("form#frm-filter select#company").val();
                    return params;
                }, error: function (xhr, error, code) {
                    if (error == "parseerror") { }
                },
                processResults: function (data) {
                    return data;
                }
            }
        }).on("select2:select", function (e) {
            const data = e.params.data;
            if (typeof data.employees == "object" && typeof data.employees !== "undefined") {
                const tempEmployeeSelector = $("form#frm-filter select#employees");
                if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                    // tempEmployeeSelector.empty();
                    $.each(data.employees, function (ii, vv) {
                        var tempOption = new Option(vv.text, vv.id, true, true);
                        tempEmployeeSelector.append(tempOption);
                    });
                    tempEmployeeSelector.prop("disabled", true);
                }
            }
        }).on("select2:unselect", function (e) {
            const tempData = $(this).select2("data");

            const tempEmployeeSelector = $("form#frm-filter select#employees");
            if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {

                // removed employees by its payroll group unselected
                if(!jQuery.isEmptyObject(tempData)){
                    tempEmployeeSelector.empty();
                    $.each(tempData[0].employees, function(ii, vv){
                        const tempOption = new Option(vv.text, vv.id, true, true);
                        tempEmployeeSelector.append(tempOption);
                    });

                    tempEmployeeSelector.prop("disabled", true);
                }else{
                    tempEmployeeSelector.empty();
                    tempEmployeeSelector.prop("disabled", false);
                }
                
            }
        });

        $("form#frm-generate-manually select#company").select2({
            placeholder: 'Select an option',
            width: '100%',
            allowClear: true,
            dropdownParent: generateManuallyModal,
        });

        $("form#frm-generate-manually #payroll_group").select2({
            placeholder: 'Select an option',
            width: '100%',
            allowClear: true,
            // minimumInputLength: 1,
            dropdownParent: generateManuallyModal,
            ajax: {
                url: siteUrl("gcctime/timesheet/select_payroll_group"),
                dataType: "json",
                type: 'get',
                delay: 250,
                global: false,
                data: function (params) {
                    params.company_id = $("form#frm-generate-manually select#company").val();
                    return params;
                }, error: function (xhr, error, code) {
                    if (error == "parseerror") { }
                },
                processResults: function (data) {
                    return data;
                }
            }
        }).on("select2:select", function (e) {
            const data = e.params.data;
            if (typeof data.employees == "object" && typeof data.employees !== "undefined") {
                const tempEmployeeSelector = $("form#frm-generate-manually select#employees");
                if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                    // tempEmployeeSelector.empty();
                    $.each(data.employees, function (ii, vv) {
                        var tempOption = new Option(vv.text, vv.id, true, true);
                        tempEmployeeSelector.append(tempOption);
                    });
                    tempEmployeeSelector.prop("disabled", true);
                }
            }
        }).on("select2:unselect", function (e) {
            const tempData = $(this).select2("data");
            
            const tempEmployeeSelector = $("form#frm-generate-manually select#employees");
            if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {

                if(!jQuery.isEmptyObject(tempData)){
                    tempEmployeeSelector.empty();
                    $.each(tempData[0].employees, function(ii, vv){
                        const tempOption = new Option(vv.text, vv.id, true, true);
                        tempEmployeeSelector.append(tempOption);
                    });

                    tempEmployeeSelector.prop("disabled", true);
                }else{
                    tempEmployeeSelector.empty();
                    tempEmployeeSelector.prop("disabled", false);
                }
            }
        });

        datePicker
            .daterangepicker({
                buttonClasses: 'm-btn btn',
                applyClass: 'btn-primary',
                cancelClass: 'btn-secondary',
                /*startDate: defaultDate,
                endDate: defaultDate*/
            }, function (start, end, label) {
                $('#cut-offs').val(null).trigger('change');
                $('.form-control', datePicker).val(start.format('MMM DD, YYYY') + ' / ' + end.format('MMM DD, YYYY'));
            });

        $('.form-control', datePicker).val(defaultDate.format('MMM DD, YYYY') + ' / ' + defaultDate.format('MMM DD, YYYY'));
        // START TEST CODE FILTERS
        // $('.form-control', datePicker).val(`${moment('2020-07-21').format('MMM DD, YYYY')} / ${moment('2020-08-05').format('MMM DD, YYYY')}`);
        // const option = new Option("ACERO, ARIEL S.", 209, true, true);
        // $('#employees').append(option).trigger("change");
        // END TEST CODE FILTERS

        const exportOptions = {
            columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
            format: {
                body: function (data, row, column, node) {
                    const rowData = dtTimesheet.rows(row).data()[0];

                    const col = parseInt(column);
                    if (col === 1) {
                        return moment(rowData._date).format("MM/DD/YYYY");
                    } else if (col === 2) {
                        return rowData._weekday;
                    } else if (col === 7) {
                        return rowData.total_late ? rowData.total_late : ``;
                    } else if (col === 8) {
                        return rowData.total_ut ? rowData.total_ut : ``;
                    } else if (col === 9) {
                        if (rowData.total_time_rendered) {
                            const hrs = (parseFloat(rowData.total_time_rendered) / 60);
                            const diff = Math.ceil(hrs) - Math.floor(hrs);
                            return parseFloat(diff) > 0 ? hrs.toFixed(2) : hrs;
                        } else {
                            return ``;
                        }
                    } else if (col === 10) {
                        return rowData.total_accredited_ot_hrs ? rowData.total_accredited_ot_hrs : ``;
                    } else {
                        return data;
                    }
                }
            }
        };

        dtTimesheet = tblTimesheet
            .DataTable({
                dom: '<"row"<"col-12" rt>><"row"<"col-6" l><"col-6" p>>',
                serverSide: false,
                destroy: true,
                ajax: {
                    url: baseUrl('gcctime/timesheet/get_timesheet'),
                    type: 'POST',
                    dataType: 'JSON',
                    data: function (_data) {
                        _data.csrf_token = _csrf_hash;
                        _data.filter = {};
                        _data.filter.cut_off = $('#cut-offs').val();
                        _data.filter.dates = $('#date-range').val();
                        _data.filter.employees = $('#employees').val();
                        _data.filter.company = $('#company').val();
                        _data.filter.status = $('#timesheet-status-filter').val();
                        _data.inclusive_filter = $('input[name="inclusive_filter"]:checked').val() || 3;
                    }, error: function (_xhr, error, _code) {
                        if (error == "parsererror") { dtTimesheet.ajax.reload(null, false); }
                    },
                    /*** global: false, ***/
                },
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'EXCEL',
                        title: function () {
                            const datesStr = $("#date-picker > input").val();
                            const dates = datesStr.split("/");
                            const dateStart = moment(dates[0]);
                            const dateEnd = moment(dates[1]);

                            const company = $("#company").val();
                            const company_name = $("#company option:selected").text();

                            let strTitle = "";
                            if (company) {
                                strTitle = company_name + " ";
                            }

                            strTitle += "TIMESHEET FOR " + dateStart.format("MM/DD/YYYY") + " TO " + dateEnd.format("MM/DD/YYYY");
                            return strTitle;
                        },
                        filename: function () {
                            const datesStr = $("#date-picker > input").val();
                            const dates = datesStr.split("/");
                            const dateStart = moment(dates[0]);
                            const dateEnd = moment(dates[1]);

                            const company = $("#company").val();
                            const company_name = $("#company option:selected").text();

                            let strTitle = "";
                            if (company) {
                                strTitle = company_name + "-";
                            }

                            strTitle += "TIMESHEET-FOR-" + dateStart.format("MM/DD/YYYY") + "-TO-" + dateEnd.format("MM/DD/YYYY");
                            return strTitle;
                        },
                        exportOptions,
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        title: function () {
                            const datesStr = $("#date-picker > input").val();
                            const dates = datesStr.split("/");
                            const dateStart = moment(dates[0]);
                            const dateEnd = moment(dates[1]);

                            const company = $("#company").val();
                            const company_name = $("#company option:selected").text();

                            let strTitle = "";
                            if (company) {
                                strTitle = company_name + " ";
                            }

                            strTitle += "TIMESHEET FOR " + dateStart.format("MM/DD/YYYY") + " TO " + dateEnd.format("MM/DD/YYYY");
                            return strTitle;
                        },
                        filename: function () {
                            const datesStr = $("#date-picker > input").val();
                            const dates = datesStr.split("/");
                            const dateStart = moment(dates[0]);
                            const dateEnd = moment(dates[1]);

                            const company = $("#company").val();
                            const company_name = $("#company option:selected").text();

                            let strTitle = "";
                            if (company) {
                                strTitle = company_name + "-";
                            }

                            strTitle += "TIMESHEET-FOR-" + dateStart.format("MM/DD/YYYY") + "-TO-" + dateEnd.format("MM/DD/YYYY");
                            return strTitle;
                        },
                        orientation: 'landscape',
                        customize: function (doc) {
                            doc.content[1].table.widths = ['20%', '10%', '6%', '7%', '7%', '7%', '7%', '6%%', '6%', '10%', '6%', '8%'];
                            const rowCount = doc.content[1].table.body.length;
                            for (i = 1; i < rowCount; i++) {
                                doc.content[1].table.body[i][0].alignment = 'left';
                                doc.content[1].table.body[i][1].alignment = 'center';
                                doc.content[1].table.body[i][2].alignment = 'center';
                                doc.content[1].table.body[i][3].alignment = 'center';
                                doc.content[1].table.body[i][4].alignment = 'center';
                                doc.content[1].table.body[i][5].alignment = 'center';
                                doc.content[1].table.body[i][6].alignment = 'center';
                                doc.content[1].table.body[i][7].alignment = 'center';
                                doc.content[1].table.body[i][8].alignment = 'center';
                                doc.content[1].table.body[i][9].alignment = 'center';
                                doc.content[1].table.body[i][10].alignment = 'center';
                                doc.content[1].table.body[i][11].alignment = 'center';
                            }
                        },
                        exportOptions,
                    },
                    {
                        extend: 'print',
                        text: 'PRINT',
                        title: function () {
                            const datesStr = $("#date-picker > input").val();
                            const dates = datesStr.split("/");
                            const dateStart = moment(dates[0]);
                            const dateEnd = moment(dates[1]);

                            const company = $("#company").val();
                            const company_name = $("#company option:selected").text();

                            let strTitle = "";
                            if (company) {
                                strTitle = company_name + " ";
                            }

                            strTitle += "TIMESHEET FOR " + dateStart.format("MM/DD/YYYY") + " TO " + dateEnd.format("MM/DD/YYYY");
                            return strTitle;
                        },
                        exportOptions,
                    }

                ],
                columns: [
                    {
                        data: 'employee_name',
                        visible: false
                    },
                    {
                        width: '4%',
                        data: null,
                        className: 'text-center',
                        render: function (data, type, row) {
                            const widthAdjustment = parseInt(row.with_adjustment);
                            const pendingAdjustment = row.has_pending_adjustment;
                            const verified = parseInt(row.verified);
                            const id = row.id;
                            const hasShift = parseInt(row.has_shift) == 1;
                            const hasOvertimeRequest = parseInt(row.has_overtime) == 1;
                            const isPosted = row.is_posted;

                            const isMonthlyPaid = typeof row.is_monthly_paid !== "undefined" && row.is_monthly_paid ? row.is_monthly_paid: false;

                            let hasPunches = false;
                            let regenerateRow = false;
                            let nullData = false;
                            let ctrNullData = 0;
                            const props = ["am_in", "am_out", "pm_in", "pm_out"];
                            const propChecker = ["total_time_rendered", "total_late", "total_ut", "total_accredited_ndiff_ot_hrs", "total_accredited_ot_hrs"];
                            let ctrAttendanceEntries = 0;
                            $.each(props, function (i, v) { if (row[v] !== null) { hasPunches = true; ctrAttendanceEntries++; } });
                            $.each(propChecker, function (i, v) {
                                if (row[v] == null || parseFloat(row[v]) == 0) {
                                    nullData = true;
                                    ctrNullData++;
                                }
                            });
                            if (hasPunches && nullData && propChecker.length == ctrNullData && pendingAdjustment === false) { regenerateRow = true; }

                            if (verified === 1 || isMonthlyPaid) {
                                return `<i class="fa fa-check m--font-success m--regular-font-size-lg3"
                                           data-toggle="m-tooltip" data-original-title="Verified"
                                           data-skin="dark"
                                           data-placement="left" style="cursor: pointer;"></i>`;
                            }

                            if (widthAdjustment === 1 && pendingAdjustment === true && verified === 0 && isPosted === false) {
                                return `<i class="fa fa-exclamation-circle"
                                           data-toggle="m-tooltip"
                                           data-original-title="Has Time Adjustments."
                                           data-skin="dark"
                                           data-placement="left"
                                           data-id="${row.id}"
                                           style="font-size: 20px; cursor: pointer"
                                           onclick="openTimeAdjustmentListModal(this, ${row._emp_id})"></i>`;
                            } else if (id && (hasShift || hasOvertimeRequest) && isPosted === false ) {
                                let tempHtml = `<label class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                                    <input type="checkbox" name="selected[]" value="${row.id}"
                                        id="cb${row.id}" class="cb-emp-${row._emp_id}"><span></span>
                                </label>`;

                                if (regenerateRow === true || (pendingAdjustment === false && ctrAttendanceEntries == 1)) {
                                    tempHtml = `<i class="fa fa-refresh m--font-warning m--regular-font-size-lg3"
                                        data-toggle="m-tooltip" data-original-title="Re-generate Row"
                                        data-skin="dark"
                                        data-placement="left" style="cursor: pointer;"></i>`;
                                }
                                return tempHtml;
                            } else {
                                return null;
                            }
                        }
                    },
                    {
                        width: '14%',
                        data: '_date',
                        className: 'text-center',
                        render: function (data, _type, row) {
                            const date = moment(data);
                            const dayOfWeek = date.day();

                            const isMonthlyPaid = typeof row.is_monthly_paid !== "undefined" && row.is_monthly_paid ? row.is_monthly_paid : false;

                            let OTicon = '';
                            if (parseInt(row.has_overtime) === 1) {
                                OTicon = `<i style="cursor: pointer;"
                                class="fa fa-clock-o ml-2" data-toggle="m-tooltip" data-skin="dark"
                                data-original-title="with OT" data-delay='{"show": 500}'
                                onclick="openMoreDetailsModal(${row.id}, ${row._emp_id}, '${row._date}')"></i>`;
                            }

                            let Fisrticon = ``;
                            let Secondicon = ``;
                            let Thirdicon = '';
                            let travelOrderIcon = ``;

                            const icon_list = ["fa-car", "fa-calendar", "fa-clock-o"];

                            if (typeof row.datelist !== "undefined") {
                                const i1 = row.datelist[0] + '', i2 = row.datelist[1] + '', i3 = row.datelist[2] + '';
                                let f1 = "", f2 = "", f3 = "";
                                let t1 = "", t2 = "", t3 = "";
                                if (i1.split(",")[1] != "0") {
                                    if (i1.split(",")[0] == "TO") { f1 = icon_list[0]; t1 = "TO"; }
                                    else if (i1.split(",")[0] == "LOA") { f1 = icon_list[1]; t1 = "LOA"; }
                                    else if (i1.split(",")[0] == "OT") { f1 = icon_list[2]; t1 = "OT"; }
                                    if (f1 && t1) {
                                        Fisrticon = `<i style="cursor: pointer;" class="fa ${f1} ml-2"
                                            data-toggle="m-tooltip" data-skin="dark"
                                            data-original-title="with ${t1}" data-delay='{"show": 500}'
                                            onclick="openMoreDetailsModal(${row.id}, ${row._emp_id}, '${row._date}')"></i>`;
                                        if(t1 == 'TO'){ travelOrderIcon = Fisrticon; }
                                    }
                                }

                                if (i2.split(",")[1] != "0") {
                                    if (i2.split(",")[0] == "TO") { f2 = icon_list[0]; t2 = "TO"; }
                                    else if (i2.split(",")[0] == "LOA") { f2 = icon_list[1]; t2 = "LOA"; }
                                    else if (i2.split(",")[0] == "OT") { f2 = icon_list[2]; t2 = "OT"; }
                                    if (f2 && t2) {
                                        Secondicon = `<i style="cursor: pointer;" class="fa ${f2} ml-2"
                                            data-toggle="m-tooltip" data-skin="dark"
                                            data-original-title="with ${t2}" data-delay='{"show": 500}'
                                            onclick="openMoreDetailsModal(${row.id}, ${row._emp_id}, '${row._date}')"></i>`;
                                        if(t2 == 'TO'){ travelOrderIcon = Secondicon; }
                                    }
                                }

                                if (i3.split(",")[1] != "0") {
                                    if (i3.split(",")[0] == "TO") { f3 = icon_list[0]; t3 = "TO"; }
                                    else if (i3.split(",")[0] == "LOA") { f3 = icon_list[1]; t3 = "LOA"; }
                                    else if (i3.split(",")[0] == "OT") { f3 = icon_list[2]; t3 = "OT"; }
                                    if (f3 && t3) {
                                        Thirdicon = `<i style="cursor: pointer;" class="fa ${f3} ml-2"
                                            data-toggle="m-tooltip" data-skin="dark"
                                            data-original-title="with ${t3}" data-delay='{"show": 500}'
                                            onclick="openMoreDetailsModal(${row.id}, ${row._emp_id}, '${row._date}')"></i>`;
                                        if(t3 == 'TO'){ travelOrderIcon = Thirdicon; }
                                    }
                                }
                            }

                            const paidHolidayState = parseInt(row.paid_holiday) === 1 ? 'm--font-success' : '';
                            const paidHolidayText = parseInt(row.paid_holiday) === 1 ? 'Paid Holiday' : 'Holiday';
                            let HolidayIcon = ``;
                            if (parseInt(row.is_holiday) == 1) {
                                const isPaidHoliday = parseInt(row.paid_holiday) == 1;
                                const iconClass = isPaidHoliday ? "m--regular-font-size-lg2 m--font-success" : "";
                                const label = isPaidHoliday ? "Paid Holiday" : "Holiday";

                                HolidayIcon = `<i style="cursor: pointer;" class="fa fa-flag ml-2 ${iconClass}"
                                    data-toggle="m-tooltip" data-skin="dark"
                                    data-original-title="${label}" data-delay='{"show": 500}'></i>`;
                            }

                            if (dayOfWeek === 0) {
                                return `<span class="m--sunday-font">${date.format('MM/DD/YYYY')}</span>${travelOrderIcon}${OTicon}`;
                            }
                            const tempIcons = `${Fisrticon}${Secondicon}${Thirdicon}${HolidayIcon}`;
                            const defaultRender = `<span class="clickable-date" onclick="openMoreDetailsModal(${row.id}, ${row._emp_id}, '${row._date}')">${date.format('MM/DD/YYYY')}</span>${tempIcons}`;
                            const monthlyPaidRenderer = `<span class="m--sunday-font">${date.format('MM/DD/YYYY')}</span>${OTicon}${HolidayIcon}`;

                            return isMonthlyPaid ? monthlyPaidRenderer: defaultRender;
                        }
                    },
                    {
                        width: '6%',
                        data: '_weekday',
                        className: 'text-center',
                        render: function (data, type, row) {
                            const date = moment(row._date);
                            const dayOfWeek = date.day();

                            if (dayOfWeek === 0) {
                                return `<span class="m--sunday-font">${data}</span>`;
                            }

                            return data;
                        }
                    },
                    {
                        width: '9%',
                        data: 'am_in',
                        className: 'text-center',
                        render: function (data, _type, row, _meta) {
                            let tempHtml = ``;
                            tempHtml = (data && data !== null) ? moment(data, 'HH:mm:ss').format('hh:mm A') : ``;
                            if (parseInt(row.verified) == 1 && tempHtml == '') { tempHtml = '--:--'; }
                            return tempHtml;
                        }
                    },
                    {
                        width: '9%',
                        data: 'am_out',
                        className: 'text-center',
                        render: function (data, type, row, meta) {
                            let tempHtml = ``;
                            tempHtml = (data && data !== null) ? moment(data, 'HH:mm:ss').format('hh:mm A') : ``;
                            if (parseInt(row.verified) == 1 && tempHtml == '') { tempHtml = '--:--'; }
                            return tempHtml;
                        }
                    },
                    {
                        width: '9%',
                        data: 'pm_in',
                        className: 'text-center',
                        render: function (data, type, row, meta) {
                            let tempHtml = ``;
                            tempHtml = (data && data !== null) ? moment(data, 'HH:mm:ss').format('hh:mm A') : ``;
                            if (parseInt(row.verified) == 1 && tempHtml == '') { tempHtml = '--:--'; }
                            return tempHtml;
                        }
                    },
                    {
                        width: '9%',
                        data: 'pm_out',
                        className: 'text-center',
                        render: function (data, type, row, meta) {
                            let tempHtml = ``;
                            tempHtml = (data && data !== null) ? moment(data, 'HH:mm:ss').format('hh:mm A') : ``;
                            if (parseInt(row.verified) == 1 && tempHtml == '') { tempHtml = '--:--'; }
                            return tempHtml;
                        }
                    },
                    {
                        width: '9%',
                        data: 'total_late',
                        className: 'text-center',
                        render: function (data, type, row, meta) {
                            let tempHtml = ``;
                            let attCount = 0;
                            const arrInOut = ['am_in', 'am_out', 'pm_in', 'pm_out', 'total_accredited_ot_hrs'];
                            $.each(arrInOut, function (ii, vv) {
                                if (typeof row[vv] !== "undefined" && row[vv] !== null) {
                                    attCount++;
                                }
                            });
                            const isMonthlyPaid = typeof row.is_monthly_paid !== "undefined" && row.is_monthly_paid ? row.is_monthly_paid : false;
                            const focusClass = (typeof data !== "undefined" && data !== null && parseFloat(data) > 0) ? 'm--font-boldest2 m--font-danger' : 'm--font-bolder';
                            tempHtml = data ? `<span class="${focusClass}" style="cursor: pointer;" data-toggle="m-tooltip" data-html="true"
                                                 data-original-title="<strong>${data}</strong> minutes" data-delay='{"show": 150}'>${data}</span>` : ``;
                            if (attCount > 0 && parseInt(row.verified) == 0 && tempHtml == '') { tempHtml = 0; }
                            if (parseInt(row.verified) == 1 && tempHtml == '' || isMonthlyPaid) { tempHtml = 0; }
                            return tempHtml;
                        }
                    },
                    {
                        width: '9%',
                        data: 'total_ut',
                        className: 'text-center',
                        render: function (data, type, row, meta) {
                            let tempHtml = ``;
                            let attCount = 0;
                            const arrInOut = ['am_in', 'am_out', 'pm_in', 'pm_out', 'total_accredited_ot_hrs'];
                            $.each(arrInOut, function (ii, vv) {
                                if (typeof row[vv] !== "undefined" && row[vv] !== null) {
                                    attCount++;
                                }
                            });
                            const isMonthlyPaid = typeof row.is_monthly_paid !== "undefined" && row.is_monthly_paid ? row.is_monthly_paid : false;
                            const focusClass = (typeof data !== "undefined" && data !== null && parseFloat(data) > 0) ? 'm--font-boldest2 m--font-danger' : 'm--font-bolder';
                            tempHtml = data ? `<span class="${focusClass}" style="cursor: pointer;" data-toggle="m-tooltip" data-html="true"
                                                 data-original-title="<strong>${data}</strong> minutes" data-delay='{"show": 150}'>${data}</span>` : ``;
                            if (attCount > 0 && parseInt(row.verified) == 0 && tempHtml == '') { tempHtml = 0; }
                            if (parseInt(row.verified) == 1 && tempHtml == '' || isMonthlyPaid) { tempHtml = 0; }
                            return tempHtml;
                        }
                    },
                    {
                        width: '9%',
                        data: 'total_time_rendered',
                        className: 'text-center',
                        render: function (data, type, row, meta) {
                            let attCount = 0;
                            const arrInOut = ['am_in', 'am_out', 'pm_in', 'pm_out', 'total_accredited_ot_hrs'];
                            $.each(arrInOut, function (ii, vv) {
                                if (typeof row[vv] !== "undefined" && row[vv] !== null) {
                                    attCount++;
                                }
                            });

                            const tempLate = (typeof row.total_late !== "undefined" && row.total_late !== null) ? parseFloat(row.total_late) : 0;
                            const tempUt = (typeof row.total_ut !== "undefined" && row.total_ut !== null) ? parseFloat(row.total_ut) : 0;
                            let tempData = (typeof data !== "undefined" && data !== null) ? parseFloat(data) : 0;
                            const focusClass = (tempLate > 0 || tempUt > 0) ? 'm--font-boldest2 m--font-danger' : 'm--font-bolder';

                            if(parseInt(row.is_holiday) === 1 && parseInt(row.paid_holiday) === 1 && tempData === 0) {
                                tempData = 480;
                            }

                            const hrs = (tempData / 60).toFixed(2);

                            const totalNdiffMinutes = row.total_ndiff_rendered ? row.total_ndiff_rendered : 0;
                            const totalNdiffHours = (totalNdiffMinutes / 60).toFixed(2);
                            const tooltip = parseFloat(totalNdiffHours) > 0 ? `<div>
                                <div class='text-left'>
                                    <span>Reg. Hrs.: </span>
                                    <span class='m--font-boldest'>${hrs}</span>
                                </div>
                                <div class='text-left'>
                                    <span>Night Diff. Hrs.: </span>
                                    <span class='m--font-boldest'>${totalNdiffHours}</span>
                                </div>
                            </div>` : `<strong>${hrs}</strong> hours`;

                            let tempHtml = data ? `<span class="${focusClass}" style="cursor: pointer;" data-toggle="m-tooltip" data-html="true"
                                                 data-original-title="${tooltip}" data-delay='{"show": 150}'>${hrs}</span>` : ``;
                            if (attCount > 0 && parseInt(row.verified) == 0 && tempHtml == '') { tempHtml = '0.00'; }
                            if (parseInt(row.verified) == 1 && tempHtml == '') { tempHtml = '0.00'; }
                            return tempHtml;
                        }
                    },
                    {
                        width: '8%',
                        data: 'total_accredited_ot_hrs',
                        className: 'text-center',
                        render: function (data, _type, row, _meta) {
                            const hasShift = parseInt(row.has_shift) == 1 && parseFloat(row.total_time_rendered) > 0;
                            const totalOTHrs = data && row.id ? parseFloat(data) + parseFloat(row.total_accredited_ndiff_ot_hrs) : 0;
                            const diff = Math.ceil(totalOTHrs) - Math.floor(totalOTHrs);
                            const totalOTHrsFormmatted = parseFloat(diff) >= 1 ? totalOTHrs.toFixed(2) : totalOTHrs;

                            const regOTHrs = data && row.id ? parseFloat(data) : 0;
                            const diffRegOTHrs = Math.ceil(regOTHrs) - Math.floor(regOTHrs);
                            const regOTHrsFormatted = parseFloat(diffRegOTHrs) >= 1 ? regOTHrs.toFixed(2) : regOTHrs;

                            const nDiffOTHrs = data && row.id ? parseFloat(row.total_accredited_ndiff_ot_hrs) : 0;
                            const diffNDiffOTHrs = Math.ceil(nDiffOTHrs) - Math.floor(nDiffOTHrs);
                            const nDiffOTHrsFormmatted = parseFloat(diffNDiffOTHrs) >= 1 ? nDiffOTHrs.toFixed(2) : nDiffOTHrs;
                            const hasShiftValue = hasShift === true ? '0' : '0';
                            const tooltip = parseFloat(totalOTHrs) > 0 ? `<div>
                                <div class='text-left'>
                                    <span>Reg. Hrs.: </span>
                                    <span class='m--font-boldest'>${regOTHrsFormatted}</span>
                                </div>
                                <div class='text-left'>
                                    <span>Night Diff. Hrs.: </span>
                                    <span class='m--font-boldest'>${nDiffOTHrsFormmatted}</span>
                                </div>
                            </div>` : hasShiftValue ?? 0;

                            return data ? `<span class="" style="cursor: pointer;" data-toggle="m-tooltip" data-html="true"
                                                 data-original-title="${tooltip}" data-delay='{"show": 150}'>${totalOTHrsFormmatted}</span>` : hasShiftValue ?? 0;
                        }
                    },
                    {
                        data: 'total_accredited_ndiff_ot_hrs',
                        visible: false,
                    },
                    {
                        width: '5%',
                        data: null,
                        defaultContent: '-',
                        className: 'text-center',
                        render: function (data, type, row, meta) {
                            const allowPaidHoliday = row.allow_paid_holiday;
                            /*** const hasOvertime = typeof row.has_overtime !== "undefined" && parseInt(row.has_overtime) === 1 ? true : false;
                            const scrub_status = parseInt(row.scrub_status); ***/
                            const pendingAdjustment = row.has_pending_adjustment;
                            const widthAdjustment = parseInt(row.with_adjustment);
                            const id = row.id ? parseInt(row.id) : null;
                            const hasShift = parseInt(row.has_shift) === 1;

                            const isMonthlyPaid = typeof row.is_monthly_paid !== "undefined" && row.is_monthly_paid ? row.is_monthly_paid : false;

                            let regenHiddenClass = parseInt(row.verified) === 0
                                && row.id && ((parseInt(row.is_manual) === 0 || parseInt(row.is_manual) === 1)
                                    && row.manual_mode !== "import") ? false : true;

                            const hideTimeAdjustmentLink = widthAdjustment === 0 || !id;
                            let hideTimeAdjustmentClass = false;
                            hideTimeAdjustmentClass = parseInt(row.verified) === 1;

                            let undoVerification = ``,
                                createTimeAdjustment = ``,
                                regenerateRecord = ``,
                                timeAdjustmentDetails = ``;
                                restDay = ``;

                            let isHolidayAction = ``;
                            /*** if (allowPaidHoliday && hasOvertime == false) { ***/
                            if (allowPaidHoliday) {
                                if (typeof row.is_holiday !== "undefined" && typeof row.paid_holiday !== "undefined"
                                    && parseInt(row.is_holiday) == 1 && parseInt(row.paid_holiday) == 0 && (parseInt(row.verified) === 0 || row.verified == null)) {
                                    isHolidayAction += `<li class="m-nav__item more-details-link">
                                        <a href="javascript:void(0)" class="m-nav__link"
                                        onclick="openIsHolidayDetailsModal('${row._date}', ${row._emp_id}, '${row.employee_name}', '${meta.row}', ${row.id})">
                                            <i class="m-nav__link-icon fa fa-flag"></i>
                                            <span class="m-nav__link-text">Set Holiday Pay</span>
                                        </a>
                                    </li>`;
                                    regenHiddenClass = true;
                                }

                                if (typeof row.is_holiday !== "undefined" && typeof row.paid_holiday !== "undefined"
                                    && parseInt(row.is_holiday) == 1 && parseInt(row.paid_holiday) == 1) {
                                    isHolidayAction += `<li class="m-nav__item more-details-link">
                                    <a href="javascript:void(0)" class="m-nav__link"
                                    onclick="undoIsHolidayDetailsModal('${row._date}', ${row._emp_id}, '${row.employee_name}', '${meta.row}', ${row.id})">
                                    <i class="m-nav__link-icon fa fa-undo"></i>
                                    <span class="m-nav__link-text">Undo Holiday Pay</span>
                                    </a>
                                    </li>`;
                                    regenHiddenClass = true;
                                }
                            } else {
                                regenHiddenClass = false;
                                hideTimeAdjustmentClass = false;
                            }

                            if (parseInt(row.verified) === 1) {
                                regenHiddenClass = true;
                                hideTimeAdjustmentClass = true;
                            }

                            let tempTemplate = ``;
                            if (row.is_posted === false) {
                                if (hideTimeAdjustmentClass === false) {
                                    createTimeAdjustment = `<li class="m-nav__item create-time-adjustment-link ${hideTimeAdjustmentClass == true ? 'm--hide' : ''}">
                                        <a href="javascript:void(0)" class="m-nav__link"
                                        onclick="createTimeAdjustment(${row.id}, ${row._emp_id}, '${row.employee_name}',
                                        '${row._date}', ${row.has_shift}, ${row.has_TO}, ${row.has_LOA}, ${meta.row})">
                                        <i class="m-nav__link-icon fa fa-calendar-plus-o"></i>
                                        <span class="m-nav__link-text">CREATE TIME ADJUSTMENT</span>
                                        </a>
                                    </li>`;
                                    regenHiddenClass = false;
                                }

                                if (parseInt(row.verified) === 1 && parseInt(row.paid_holiday) == 0) {
                                    undoVerification = `<li class="m-nav__item undo-verification-link">
                                        <a href="javascript:void(0)"
                                        class="m-nav__link"
                                        onclick="openConfirmationModal('undoVerification', 'undo_verification',
                                                        JSON.stringify({employee_name: '${row.employee_name}', date: '${row._date}', timesheet_id: ${row.id}}), ${meta.row})">
                                            <i class="m-nav__link-icon fa fa-undo"></i>
                                            <span class="m-nav__link-text">UNDO VERIFICATION</span>
                                        </a>
                                    </li>`;
                                }
                                
                                if (regenHiddenClass === false && pendingAdjustment === false) {
                                    regenerateRecord = `<li class="m-nav__item re-generate-button">
                                        <a href="javascript:void(0)" class="m-nav__link"
                                            onclick="confirmRegenerateRow('${row._date}', ${row._emp_id}, '${row.employee_name}', ${meta.row}, ${row.id})">
                                            <i class="m-nav__link-icon fa fa-refresh"></i>
                                            <span class="m-nav__link-text">Re-generate</span>
                                        </a>
                                    </li>`;
                                }

                                if (hideTimeAdjustmentLink === false) {
                                    timeAdjustmentDetails = `<li class="m-nav__separator m-nav__separator--fit time-adjustment-details-separator"></li>
                                    <li class="m-nav__item time-adjustment-details">
                                        <a href="javascript:void(0)" class="m-nav__link"
                                        data-id="${row.id}"
                                        onclick="openTimeAdjustmentListModal(this, ${row._date}, ${row._emp_id})">
                                            <i class="m-nav__link-icon fa fa-clock-o"></i>
                                            <span class="m-nav__link-text">TIME ADJUSTMENT DETAILS</span>
                                        </a>
                                    </li>`;
                                }

                                if (hasShift && (row.custom_shift_id == 0 || row.custom_shift_id == 1 || row.custom_shift_id == null) && (row.verified == 0 || row.verified == null)) {
                                    restDay = `
                                    <li class="m-nav__item restday-button">
                                        <a href="javascript:void(0)" class="m-nav__link"
                                        data-id="${row.id}"
                                        onclick="confirmRestDay(this, '${row._date}', ${row.has_shift }, ${row._emp_id}, ${row.tsID}, ${meta.row})">
                                            <i class="m-nav__link-icon fa fa-clock-o"></i>
                                            <span class="m-nav__link-text">REST DAY</span>
                                        </a>
                                    </li>`;
                                }

                                if (hasShift == 0 && (row.custom_shift_id == 0 || row.custom_shift_id > 0 || row.custom_shift_id == null) && (row.verified == 0 || row.verified == null) && row.altered_shift && row.altered_shift.has_shift == 0 && row.altered_shift.tag == 'timesheet'){
                                    restDay = `
                                    <li class="m-nav__item restday-button">
                                        <a href="javascript:void(0)" class="m-nav__link"
                                        data-id="${row.id}"
                                        onclick="undoRestDay(this, '${row._date}', ${row.has_shift }, ${row._emp_id}, ${row.tsID}, ${meta.row})">
                                            <i class="m-nav__link-icon fa fa-undo"></i>
                                            <span class="m-nav__link-text">UNDO REST DAY</span>
                                        </a>
                                    </li>`;
                                }

                                tempTemplate = `
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
                                                            ${isHolidayAction}
                                                            ${createTimeAdjustment}
                                                            <li class="m-nav__item more-details-link">
                                                                <a href="javascript:void(0)" class="m-nav__link"
                                                                onclick="openMoreDetailsModal(${row.id}, ${row._emp_id}, '${row._date}')">
                                                                    <i class="m-nav__link-icon fa fa-info-circle"></i>
                                                                    <span class="m-nav__link-text">MORE DETAILS</span>
                                                                </a>
                                                            </li>
                                                            ${undoVerification}
                                                            ${restDay}
                                                            ${regenerateRecord}
                                                            ${timeAdjustmentDetails}
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;

                            } else {
                                tempTemplate = `<a href="javascript:void(0)" 
                                    class="m-portlet__nav-link m-btn--icon m-btn--icon-only btn-sm m-btn--pill" 
                                    data-toggle="m-tooltip" data-original-title="More Details" data-skin="dark" data-delay="{&quot;show&quot;: 500}" 
                                    onclick="openMoreDetailsModal(${row.id}, ${row._emp_id}, '${row._date}')">
                                    <i class="fa fa-info-circle m--font-dark" style="font-size: 1.2rem;"></i>
                                </a>`;
                            }

                            return isMonthlyPaid === false ? tempTemplate: ``;
                        }
                    }
                ],
                pageLength: 15,
                autoWidth: false,
                columnDefs: [],
                ordering: false,
                lengthMenu: [[15, 25, 50, 100, 200, -1], [15, 25, 50, 100, 200, 'All']],
                drawCallback: function (settings) {
                    dtOvertimeRecords.clear();
                    dtOvertimeRecords.draw(false);
                    
                    vmTimsheetActions.has_overtime_request = false;

                    if(typeof settings.json != "undefined"){
                        const { has_existing_overtime, default_shift_employees } = settings.json;
                        
                        if(has_existing_overtime.length > 0){
                            const ctrOT = has_existing_overtime.length;
                            toastr.info("A total of ("+ctrOT+") Overtime Record/s Found!", "Overtime Record/s");
                            vmTimsheetActions.has_overtime_request = true;

                            dtOvertimeRecords.clear();
                            dtOvertimeRecords.rows.add(has_existing_overtime);
                            dtOvertimeRecords.draw(false);
                        }

                        let arrEmpRecord = [];
                        if(typeof default_shift_employees != "undefined" && Object.keys(default_shift_employees).length > 0){
                            $.each(default_shift_employees, (_index, row) => {
                                const { ctr } = row;
                                if(ctr > 0){ arrEmpRecord.push(row); }
                            });
                        }

                        if(arrEmpRecord.length > 0){
                            let _arrIds = [];
                            const ctr = arrEmpRecord.length;
                            let tempHtml = `<div class='row swal--custom-list'>`;
                            arrEmpRecord.forEach((row, _index) => {
                                tempHtml += `<div class='col-6 col-md-6 col-lg-6 col-sm-12'><span class='m--font-bolder text-left ml-1'>${row.employee_name}</span></div>`;
                                _arrIds.push(row.emp_id);
                            });
                            tempHtml += `</div>`;
                            Swal.fire({
                                title: 'Default Shift Record/s?',
                                html: `A TOTAL OF <b>${ctr}</b> DEFAULT SHIFT RECORD/s FOUND!<br>${tempHtml}<br>WOULD YOU LIKE TO GENERATE TIMESHEET RECORD/s?`,
                                icon: 'question',
                                width: '800px',
                                showCloseButton: true,
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, Generate it!',
                                timer: 10000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    setTimeout(() => {
                                        const popup = Swal.getPopup();
                                        popup.classList.add('swal2-fade-out');
                                    }, 9500);

                                    const confirmBtn = Swal.getConfirmButton();
                                    confirmBtn.addEventListener('click', () => {
                                    const popup = Swal.getPopup();
                                    popup.classList.add('swal2-fade-out');
                                        setTimeout(Swal.close(), 500);
                                    });
                                }, willClose: () => {
                                    return new Promise((resolve) => {
                                    setTimeout(resolve, 500);
                                    });
                                }
                            }).then((result) => {
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    toastr.info("Default shift record/s automatic generation has closed.", "Default Shift Record/s");
                                } else if (result.isConfirmed) {
                                    $.ajax({
                                        url: siteUrl("gcctime/timesheet/generate_default_timesheet"),
                                        type: "POST",
                                        dataType: "JSON",
                                        data: {
                                            [_csrf_token]: _csrf_hash,
                                            emp_id: _arrIds,
                                            dates: $('#date-range').val(),
                                        }, success: function (response) {
                                            if(response.success){ toastr.success(response.message, "Default Timesheet Record(s)"); }
                                            else{ toastr.error(response.message, "Default Timesheet Record(s)"); }
                                        }
                                    });
                                }
                            });
                        }
                    }

                    const api = this.api();
                    const rows = api.rows({ page: 'current' }).nodes();
                    const pageRows = api.rows({ page: 'current' }).data();

                    let last = null;

                    api.column(0, { page: 'current' })
                        .data()
                        .each(function (group, i) {
                            last = (last !== null) ? last.toUpperCase() : last;
                            group = (group !== null) ? group.toUpperCase() : group;
                            const empHeaderIndex = api.rows(i)[0];
                            const row = pageRows[empHeaderIndex];
                            const isMonthlyPaid = typeof row.is_monthly_paid !== "undefined" && row.is_monthly_paid ? row.is_monthly_paid : false;
                            const monthlyPaidIndicator = isMonthlyPaid ? `<small class='ml-5 mr-3 m--font-boldest'>Monthly Paid</small>
                                <i class="fa fa-calendar"></i>
                                <small class='ml-3'>[ System Generated Timesheet Data ]</small>`:``;
                            if (last !== group) {
                                let cbElement = '';
                                if (!row.all_verified && isMonthlyPaid === false) {
                                    cbElement = `<label class="m-checkbox m-checkbox--bold m-checkbox--state-light table-cb mr-3"
                                        style="margin-left: 5px;">
                                        <input type="checkbox" value="${row.id}" class="cb-emp-header"
                                            data-emp-id="${row._emp_id}"><span></span>
                                    </label>`;
                                }

                                $(rows).eq(i).before(
                                    `<tr class="${row.all_verified || isMonthlyPaid ? 'group--verified' : 'group'} tr-header-${row._emp_id}">
                                        <td colspan="12">
                                            ${cbElement}
                                            <span style="font-weight: normal; color: whitesmoke;">${row.biometricno}</span>
                                            <span class="ml-2">${group}</span>${monthlyPaidIndicator}
                                        </td>
                                    </tr>`
                                );

                                last = group;
                            }
                        });
                },
                createdRow: function (rowEl, rowData, _index) {
                    const scrub_status = (typeof rowData.scrub_status !== "undefined" && rowData.scrub_status !== null) ? parseInt(rowData.scrub_status) : 0;
                    const verified = (typeof rowData.verified !== "undefined" && rowData.verified !== null) ? parseInt(rowData.verified) : 0;
                    const hasTO = (typeof rowData.has_TO !== "undefined" && rowData.has_TO !== null) ? parseInt(rowData.has_TO) : 0;
                    const hasLOA = (typeof rowData.has_LOA !== "undefined" && rowData.has_LOA !== null) ? parseInt(rowData.has_LOA) : 0;
                    const hasWholeDayLoa = (typeof rowData.has_whole_day_LOA !== "undefined" && rowData.has_whole_day_LOA !== null) ? parseInt(rowData.has_whole_day_LOA) : 0;
                    const isHoliday = (typeof rowData.is_holiday !== "undefined" && rowData.is_holiday !== null) ? parseInt(rowData.is_holiday) : 0;
                    const paidHoliday = (typeof rowData.paid_holiday !== "undefined" && rowData.paid_holiday !== null) ? parseInt(rowData.paid_holiday) : 0;
                    const hasShift = (typeof rowData.has_shift !== "undefined" && rowData.has_shift !== null) ? parseInt(rowData.has_shift) : 0;
                    const hasOvertime = (typeof rowData.has_overtime !== "undefined" && rowData.has_overtime !== null) ? parseInt(rowData.has_overtime) : 0;
                    const isPosted = (typeof rowData.is_posted !== "undefined" && rowData.is_posted !== null) ? rowData.is_posted : false;

                    const isMonthlyPaid = (typeof rowData.is_monthly_paid !== "undefined" && rowData.is_monthly_paid) ? rowData.is_monthly_paid : false;
                    const completeAttendance = rowData.complete_attendance_count;
                    const { am_in, am_out, pm_in, pm_out, total_time_rendered } = rowData;

                    let currentRowClass = null;
                    let hasRendered = typeof total_time_rendered !== "undefined" && total_time_rendered !== null && parseFloat(total_time_rendered) > 0;
                    if (isHoliday == 1 && paidHoliday == 0) { hasRendered = false; }
                    if (scrub_status == 1 || scrub_status == 2) { hasRendered = true; }
                    if (isHoliday == 1 && hasOvertime == 1) { hasRendered = true; }
                    hasRendered = am_in || am_out || pm_in || pm_out;

                    if (hasRendered) {
                        if ((scrub_status === 1 && verified === 0) || ((hasLOA >= 1 && hasWholeDayLoa <= 0) || (hasTO >= 1 && completeAttendance === false))) {
                            currentRowClass = 'lacking lacking--contrast';
                        } else if (scrub_status === 2 && verified === 0) {
                            currentRowClass = 'multiple';
                        }
                    } else {
                        currentRowClass = 'absent absent--contrast';
                    }

                    if (((hasShift === 0 && (hasOvertime === 0 || hasOvertime === 1)) && (verified === 0 || !verified))
                        || ((!hasShift && !hasOvertime) && (verified === 0 || !verified))
                        || (isHoliday == 1 && rowData.allow_paid_holiday === false)) {
                        currentRowClass = 'no-shift';
                    }
                    
                    if (rowData.id && verified === 1 || isMonthlyPaid) { currentRowClass = 'verified verified--contrast'; }
                    if (isPosted) { currentRowClass = 'posted_entry--contrast'; }
                    if (currentRowClass) { $(rowEl).addClass(currentRowClass); }
                }
            });

        $.validate({
            form: $('#frm-filter'),
            lang: 'en',
            scrollToTopOnError: false,
            onSuccess: function (form) {
                const empVal = $("select#employees", form).val();
                const compVal = $("select#company", form).val();
                const psVal = $("select#payroll_group", form).val();
                if(typeof empVal !== "undefined" && typeof compVal !== "undefined" && typeof psVal !== "undefined"
                    && empVal.length == 0 && (compVal == null || compVal == '') && psVal.length == 0) {
                        Swal.fire({
                            title: 'Search All Timesheet?',
                            html: "Are you sure you want to search all timesheet record/s?",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, Search All!'
                        }).then((result) => {
                            if (result.isConfirmed) { dtTimesheet.ajax.reload(); }
                        });
                }else{ dtTimesheet.ajax.reload(); }
                return false;
            }
        });

        tblTimesheet
            .on('draw.dt', function () {
                checkCbSelectAll();
                checkEachCluster();
            });

        $(tblTimesheet)
            .on('change', 'tbody input[type=\'checkbox\']:not(.cb-emp-header)', function () {
                const emp_id = ($(this)[0].className).split('-').pop();
                checkCbSelectAll();
                checkCbEmpCluster(emp_id);
            });

        $(tblTimesheet)
            .on('change', 'tbody input[type=\'checkbox\'].cb-emp-header', function () {
                const el = this;
                const emp_id = $(el).attr('data-emp-id');
                const checkedValue = $(el)[0].checked;

                $(
                    `.cb-emp-${emp_id}`
                ).prop('checked', checkedValue);
                checkCbSelectAll();
                checkCbEmpCluster(emp_id);
            });

        overtimeManualEntryModal.find('#m--datetimepicker_ot_in').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'bottom-left',
            todayBtn: true,
            format: 'yyyy-mm-dd hh:ii'
        });

        overtimeManualEntryModal.find('#m--datetimepicker_ot_out').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'bottom-left',
            todayBtn: true,
            format: 'yyyy-mm-dd hh:ii'
        });
        overtimeManualEntryModal.find('#requested_by').select2({
            width: "100%",
            placeholder: "Select an option",
            dropdownParent: overtimeManualEntryModal,
            ajax: {
                url: siteUrl("eforms/overtime/get_employee_department_head"),
                dataType: "json",
                delay: 250,
                global: false,
                processResults: function (data) {
                    return data;
                }
            }
        }).on("select2:select", function (e) {
            const tempData = e.params.data;
            if (typeof tempData.text !== "undefined") {
                overtimeManualEntryModal.find("input#requested_by_name").val(tempData.text)
            }
        });

        if(typeof monthlyEmployeeModal !== "undefined" && monthlyEmployeeModal.length === 1){
            let _select2Data = typeof _tempContentData.monthly_employees !== "undefined" && _tempContentData.monthly_employees.length > 0 ? _tempContentData.monthly_employees: [];
            const select2MonthlyEmployee =  function(_tempData=[]){
                const s2MonthlyEmployee = monthlyEmployeeModal.find("#monthlyEmployeeSelect2");
                if(typeof s2MonthlyEmployee !== "undefined" && s2MonthlyEmployee.length == 1){
                    if(s2MonthlyEmployee.data("select2")){
                        s2MonthlyEmployee.select2("destroy");
                        s2MonthlyEmployee.empty();
                    }

                    s2MonthlyEmployee.select2({
                        width: "100%",
                        placeholder: "Select an option",
                        dropdownParent: monthlyEmployeeModal,
                        data: _tempData,
                        allowClear: true,
                    });

                    return s2MonthlyEmployee.val('').trigger("change");
                }else{ return false; }
            }

            select2MonthlyEmployee(_select2Data);
    
            const dtMonthlyEmployee = $("#tbl-monthly-employees").DataTable({
                dom: '<"row"<"col-12" rt>><"row"<"col-6" l><"col-6" p>>',
                searching: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: baseUrl('gcctime/timesheet/get_monthly_paid_employee_list'),
                    type: 'POST',
                    dataType: 'JSON',
                    data: function (_data) {
                        _data.csrf_token = _csrf_hash;
                        _data.search.value = $("#searchMonthlyPaidEmployees", monthlyEmployeeModal).val();
                    },
                    global: false
                }, columns: [
                    { data: "employee_name", render: function(data, _type, row){
                        const empStatus = row.employee_status;
                        const tempState = empStatus === 'ACTIVE' ? 'm--font-success':'m--font-danger';
                        const html = `<p class='mb-0'>${data}</p><p class='mb-0'><small class='m--font-boldest'>STATUS: <span class='${tempState}'>${empStatus}</span></small></p>`;
                        return html;
                    }},
                    { data: "created_by_name", render: function(data, _type, row){
                        const dateTime = moment(new Date(row.created_at), "YYYY-MM-DD HH:ii:ss").format("LLL");
                        const html = `<p class='mb-0'>${data}</p><p class='mb-0'><small>${dateTime}</small></p>`;
                        return html;
                    }},
                    { data: null, orderable: false, render: function(_data, _type, row){
                        const rawData = JSON.stringify(row);
                        return `<button class='btn m-btn btn-danger btn-sm btnDelete deleteMonthlyPaidRecord' data-row='${rawData}'><i class='la la-trash'></i></button>`;
                    }},
                ],
            });

            $("#searchMonthlyPaidEmployees", monthlyEmployeeModal).donetyping(function () { dtMonthlyEmployee.ajax.reload(null, false); });

            $.validate({
                form: "#frm-monthly_employee",
                lang: 'en',
                scrollToTopOnError: false,
                onSuccess: function (form) {
                    const formData = $(form).serialize();
                    $.ajax({
                        url: siteUrl("gcctime/timesheet/include_monthly_employee_timesheet"),
                        type: "post", 
                        data: formData,
                        dataType: "json",
                        success: function(json){
                            if(json.response){
                                select2MonthlyEmployee(json.data);
                                dtMonthlyEmployee.ajax.reload(null, false); 
                            }
                        }
                    });
            
                    return false;
                }
            });

            jQuery(document).on("click", ".deleteMonthlyPaidRecord", function(){
                const _this = $(this);
                const _data = _this.data("row");
                if(typeof _data !== "undefined" && Object.keys(_data).length > 0){
                    Swal.fire({
                        title: 'Remove Employee?',
                        text: "Are you sure you want to remove this monthly paid employee name `"+_data.employee_name+"` on the list?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Remove it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: siteUrl("gcctime/timesheet/remove_included_monthly_employee_timesheet"),
                                type: "post", 
                                data: { [_csrf_token]: _csrf_hash, id: _data.id },
                                dataType: "json",
                                success: function(json){
                                    if(json.response){
                                        toastr.info(json.toastr_msg, "Remove Monthly Paid Employee");
                                        select2MonthlyEmployee(json.data);
                                        dtMonthlyEmployee.ajax.reload(null, false); 
                                    }else{ toastr.error(json.toastr_msg, "Remove Monthly Paid Employee"); }
                                }
                            });
                        }
                    });
                }
            });
        }
    });

$.validate({
    form: $('#frm-timesheet-confirmation-modal'),
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const functionCall = $(form).attr('data-function');
        eval(functionCall)(form);
        confirmationModal.modal('hide');
        return false;
    }
});

$.validate({
    form: $('#frm-overtime-manual-entry'),
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const data = $(form).serializeArray();
        let tempData = {};
        data.forEach(element => {
            tempData = Object.assign({}, tempData, { [element.name]: element.value });
        });
        /*** vmTempCreateAdjustment.overtime = Object.assign({}, tempData); ***/
        overtimeManualEntryModal.modal("hide");
        return false;
    }
});

var vmTempCreateAdjustment = new Vue({
    el: "#temp-create-adjustment-content",
    data: { overtime: {}, has_overtime: false, },
    methods: {
        renderRequestedBySelect2: function (el) {
            var tempRequestedBy = $(el).find('#requested_by');
            if (typeof tempRequestedBy !== "undefined") {
                tempRequestedBy.select2({
                    width: "100%",
                    placeholder: "Select an option",
                    dropdownParent: createTimeAdjustmentModal,
                    ajax: {
                        url: siteUrl("eforms/overtime/get_employee_department_head"),
                        dataType: "json",
                        delay: 250,
                        global: false,
                        processResults: function (data) {
                            return data;
                        }
                    }
                });
            }
            return this;
        }
    }
});

function openIsHolidayDetailsModal(date, emp_id, employee, rowIndex, timesheetId) {
    $('.modal-title', confirmationModal).html(
        `<span class="m--font-bolder m--regular-font-size-lg3">Set Paid Holiday</span>`);
    $('.modal-body', confirmationModal).html(`<span class="m--font-bold m--regular-font-size-lg2">
        Are you sure to set the paid holiday for <span class="m--regular-font-size-lg1 m--font-boldest">${employee}</span> on
        <span class="m--regular-font-size-lg1 m--font-boldest">${moment(date).format("MM/DD/YYYY")}</span>?</span>`);
    $('.btnSave', confirmationModal).html(`Yes`);
    $('.btnClose', confirmationModal).html(`No`);
    $('form', confirmationModal).attr('data-function', 'setPaidHolidayRow');
    $('form', confirmationModal).attr('data-date', date);
    $('form', confirmationModal).attr('data-emp_id', emp_id);
    $('form', confirmationModal).attr('data-row_index', rowIndex);
    $('form', confirmationModal).attr('data-timesheet_id', timesheetId);
    confirmationModal.modal('show');
}

function undoIsHolidayDetailsModal(date, emp_id, employee, rowIndex, timesheetId) {
    $('.modal-title', confirmationModal).html(
        `<span class="m--font-bolder m--regular-font-size-lg3">Undo Paid Holiday</span>`);
    $('.modal-body', confirmationModal).html(`<span class="m--font-bold m--regular-font-size-lg2">
        Are you sure you want to undo the paid holiday for <span class="m--regular-font-size-lg1 m--font-boldest">${employee}</span> on
        <span class="m--regular-font-size-lg1 m--font-boldest">${moment(date).format("MM/DD/YYYY")}</span>?</span>`);
    $('.btnSave', confirmationModal).html(`Yes`);
    $('.btnClose', confirmationModal).html(`No`);
    $('form', confirmationModal).attr('data-function', 'undoPaidHolidayRow');
    $('form', confirmationModal).attr('data-date', date);
    $('form', confirmationModal).attr('data-emp_id', emp_id);
    $('form', confirmationModal).attr('data-row_index', rowIndex);
    $('form', confirmationModal).attr('data-timesheet_id', timesheetId);
    confirmationModal.modal('show');
}

function setPaidHolidayRow(form) {
    const date = $(form).attr('data-date');
    const emp_id = $(form).attr('data-emp_id');
    const timesheet_id = $(form).attr('data-timesheet_id');

    $.ajax({
        url: siteUrl("gcctime/timesheet/set_paid_holiday"),
        dataType: "json",
        type: "post",
        data: { emp_id: emp_id, date: date, id: timesheet_id, csrf_token: _csrf_hash },
        success: function (json) {
            if (json.response) {
                setTimeout(function () { dtTimesheet.ajax.reload(null, false); }, 250);
            }
        }
    });
}

function undoPaidHolidayRow(form) {
    const timesheet_id = $(form).attr('data-timesheet_id');
    $.ajax({
        url: siteUrl("gcctime/timesheet/undo_paid_holiday"),
        dataType: "json",
        type: "post",
        data: { id: timesheet_id, csrf_token: _csrf_hash },
        success: function (json) {
            if(json.response){
                setTimeout(function () { dtTimesheet.ajax.reload(null, false); }, 250);
            }
        }
    });
}

function confirmRegenerateRow(date, emp_id, employee, rowIndex, timesheet_id) {
    $('.modal-title', confirmationModal).html(
        `<span class="m--font-bolder m--regular-font-size-lg3">Re-generate Confirmation</span>`);
    $('.modal-body', confirmationModal).html(`<span class="m--font-bold m--regular-font-size-lg2">
        Are you sure to re-generate record for <span class="m--regular-font-size-lg1 m--font-boldest">${employee}</span> on
        <span class="m--regular-font-size-lg1 m--font-boldest">${moment(date).format("MM/DD/YYYY")}</span>?</span>`);
    $('.btnSave', confirmationModal).html(`Yes`);
    $('.btnClose', confirmationModal).html(`No`);
    $('form', confirmationModal).attr('data-function', 'regenerateRow');
    $('form', confirmationModal).attr('data-date', date);
    $('form', confirmationModal).attr('data-emp_id', emp_id);
    $('form', confirmationModal).attr('data-row_index', rowIndex);
    $('form', confirmationModal).attr('data-timesheet_id', timesheet_id);
    confirmationModal.modal('show');
}

function regenerateRow(form) {
    const date = $(form).attr('data-date');
    const emp_id = $(form).attr('data-emp_id');
    const row_index = (form).attr('data-row_index');
    const timesheet_id = (form).attr('data-timesheet_id');

    $.ajax({
        /*** url: baseUrl('gcctime/timesheet_cron/create_multiple/0/1'), ***/
        url: baseUrl('gcctime/timesheet_cron/create_multiple/null/1'),
        data: {
            dateStart: moment(date).format("YYYY-MM-DD"),
            dateEnd: moment(date).format("YYYY-MM-DD"),
            employees: [emp_id],
            csrf_token: _csrf_hash
        },
        dataType: "JSON",
        type: "POST",
        success: function (response) {
            if (response.length <= 0) {
                if (typeof timesheet_id !== "undefined" && timesheet_id) {
                    $.ajax({
                        url: baseUrl('gcctime/timesheet/get_timesheet_row/' + timesheet_id),
                        data: { emp_id, csrf_token: _csrf_hash },
                        dataType: "JSON",
                        type: "POST",
                        success: function (timesheet) {
                            if (Object.keys(timesheet).length > 0) {
                                const rowEl = dtTimesheet.row(row_index).node();
                                toastr.success(`Successfully re-generated record for
                                    <span class="m--font-boldest">${timesheet.employee_name}</span> on
                                    <span class="m--font-boldest">${moment(date).format("MM/DD/YYYY")}</span>`,
                                    "Successfully Re-generated.",
                                    { timeOut: 10000 });

                                const scrub_status = parseInt(timesheet.scrub_status);
                                const verified = parseInt(timesheet.verified);
                                const hasTO = parseInt(timesheet.has_TO);
                                const hasLOA = parseInt(timesheet.has_LOA);
                                const hasWholeDayLoa = parseInt(timesheet.has_whole_day_LOA);
                                const isHoliday = (typeof timesheet.is_holiday !== "undefined" && timesheet.is_holiday !== null) ? parseInt(timesheet.is_holiday) : 0;
                                const hasShift = (typeof timesheet.has_shift !== "undefined" && timesheet.has_shift !== null) ? parseInt(timesheet.has_shift) : 0;
                                const hasOvertime = (typeof timesheet.has_overtime !== "undefined" && timesheet.has_overtime !== null) ? parseInt(timesheet.has_overtime) : 0;

                                if (scrub_status === 1 && verified === 0) {
                                    $(rowEl).addClass('lacking lacking--contrast');
                                } else if (scrub_status === 2 && verified === 0) {
                                    $(rowEl).addClass('multiple');
                                } else {
                                    $(rowEl).hasClass("lacking lacking--contrast") && $(rowEl).removeClass("lacking lacking--contrast");
                                    $(rowEl).hasClass("multiple") && $(rowEl).removeClass("multiple");
                                }

                                if (!timesheet.id && parseInt(timesheet.has_shift) === 1) {
                                    if ((hasLOA >= 1 && hasWholeDayLoa === 1)) {
                                        $(rowEl).addClass('absent absent--contrast');
                                    } else if (hasLOA >= 1 && hasWholeDayLoa <= 0) {
                                        $(rowEl).addClass('lacking lacking--contrast');
                                    } else {
                                        $(rowEl).addClass('absent absent--contrast');
                                    }
                                    if (hasTO >= 1) {
                                        $(rowEl).addClass('lacking lacking--contrast');
                                    }
                                } else if (timesheet.id && parseInt(timesheet.has_shift) === 1){
                                    $(rowEl).removeClass('absent absent--contrast');
                                }

                                if (((hasShift === 0 && (hasOvertime === 0 || hasOvertime === 1)) && (verified === 0 || !verified))
                                    || ((!hasShift && !hasOvertime) && (verified === 0 || !verified))
                                    || (isHoliday == 1 && timesheet.allow_paid_holiday === false)) {
                                    $(rowEl).addClass('no-shift');
                                } else {
                                    $(rowEl).hasClass("no-shift") && $(rowEl).removeClass("no-shift");
                                }

                                if((timesheet.am_in && timesheet.am_out) || (timesheet.pm_in && timesheet.pm_out)){
                                    setTimeout(function(){
                                        toastr.info("Loading re-generated timesheet data, please wait!", "Loading Timesheet Data", { timeOut: 10000 });
                                    }, 250);
                                }
                                setTimeout(function(){ dtTimesheet.row(row_index).data(timesheet).draw(false); }, 750);
                            } else {
                                toastr.error("An error occurred while re-generating timesheet.", "Error occurred.", { timeOut: 10000 });
                            }
                        }
                    });
                } else {
                    dtTimesheet.ajax.reload(null, false);
                }
            }
        }
    });
}

function openConfirmationModal(functionCall, for_label = 'verification', data = null, dtRowIndex = null) {
    if (for_label === 'verification') {
        $('.modal-title', confirmationModal).html(
            `<span class="m--font-bolder m--regular-font-size-lg3">Confirm Verification</span>`);
        $('.modal-body', confirmationModal).html(`
                        <span class="m--font-bold m--regular-font-size-lg2">Are you sure to verify selected time record(s)?</span>`);
        $('form', confirmationModal).attr('data-function', functionCall);
    } else {
        const _data = JSON.parse(data);
        $('.modal-title', confirmationModal).html(
            `<span class="m--font-bolder m--regular-font-size-lg3">Confirm Undo Verification</span>`);
        $('.modal-body', confirmationModal).html(`
                        <span class="m--font-bold m--regular-font-size-lg2">
                            Are you sure to undo verification of
                            <span class="m--font-boldest">${moment(_data.date).format('ll')}</span> record for
                            <span class="m--font-boldest">${_data.employee_name}</span>?
                        </span>`);
        $('form', confirmationModal).attr('data-function', functionCall);
        $('form', confirmationModal).attr('data-timesheet_id', _data.timesheet_id);
        $("form", confirmationModal).attr("data-dt-row-index", dtRowIndex);
    }

    $('.btnSave', confirmationModal).html(`Yes`);
    $('.btnClose', confirmationModal).html(`No`);
    confirmationModal.modal('show');
}

function verifySelected(form = null) {
    const checkedCbCount = $('tbody input[type=\'checkbox\']:checked', tblTimesheet).length;
    if (parseInt(checkedCbCount) <= 0) {
        $('.modal-title', alertModal).html(`<span class="m--font-danger m--font-bolder m--regular-font-size-lg3">Oops! Unable to Verify.</span>`);
        $('.modal-body', alertModal).html(`<p class="m-0 m--regular-font-size-lg1 m--font-bolder">Please select/check at least one(1) record.</p>`);
        alertModal.modal('show');
    } else {
        const selectedCheckboxes = dtTimesheet.rows().nodes().to$().find('input[type="checkbox"]:checked');
        let cbIdArrays = [];
        $.each(selectedCheckboxes, function (i, cb) {
            cbIdArrays.push($(cb).val());
        });

        $.ajax({
            url: baseUrl('gcctime/timesheet/verify_selected_time_records'),
            type: 'post',
            dataType: 'JSON',
            data: {
                id: cbIdArrays,
                csrf_token: _csrf_hash,
                filter: {
                    cut_off: $('#cut-offs').val(),
                    dates: $('#date-range').val(),
                    employees: $('#employees').val(),
                    company: $('#company').val(),
                }
            },
            success: function (response) {
                if (response.success) {
                    $.each(selectedCheckboxes, function (i, cb) {
                        const tr = $(cb).closest('tr');
                        $(tr).removeClass(function () {
                            return $(this).attr('class').replace(/\b(?:even|odd)\b\s*/g, '');
                        });
                        tr.addClass("verified verified--contrast");

                        $(cb).closest('td').empty().html(`<i class="fa fa-check m--font-success m--regular-font-size-lg3"
                            data-toggle="m-tooltip" data-original-title="Verified"
                            data-skin="dark"
                            data-placement="left" style="cursor: pointer;">
                        </i>`);
                        $('.create-time-adjustment-link', tr).remove();
                        $('.re-generate-button', tr).remove();
                        $('.undo-verification-link', tr).removeClass('m--hide');
                    });

                    const verifiedEmpIds = response.data.verifiedEmpIds;
                    verifiedEmpIds.forEach((id, index) => {
                        const row = $(`.tr-header-${id}`);
                        row.removeClass('group').addClass('group--verified');
                        $('.m-checkbox', row).remove();
                    });
                }

                if (typeof response.is_posted !== "undefined" && response.is_posted || typeof response.is_below_latest_posted !== "undefined" && response.is_below_latest_posted) {
                    Swal.fire({
                        title: response.title,
                        text: `${response.message}`,
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ok',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (typeof response.is_posted !== "undefined" && response.is_posted ) {
                                dtTimesheet.ajax.reload();
                            }
                            $('#tbl-timesheet input[type=checkbox]').prop('checked', false);
                        }
                    });
                }

                const toast = response.success ? 'success' : 'error';
                toastr[toast](response.message, response.title);

                cbSelectAll.prop('checked', false);
            }
        });
    }
}

function undoVerification(form) {
    const timesheet_id = $("form", confirmationModal).attr("data-timesheet_id");
    const dtRowIndex = $("form", confirmationModal).attr("data-dt-row-index");

    $.ajax({
        url: baseUrl(`gcctime/timesheet/undo_timesheet_verification/${timesheet_id}`),
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            if (response) {
                const rowData = response.row;
                const scrub_status = parseInt(rowData.scrub_status);
                const verified = parseInt(rowData.verified);
                const hasTO = parseInt(rowData.has_TO);
                const hasLOA = parseInt(rowData.has_LOA);
                const hasWholeDayLoa = parseInt(rowData.has_whole_day_LOA);

                dtTimesheet.row(dtRowIndex).data(rowData).draw();
                const rowEl = dtTimesheet.row(dtRowIndex).node();
                let oddEvenClass = $(rowEl).hasClass("odd") ? "odd" : "even";
                $(rowEl)
                    .removeClass()
                    .addClass(oddEvenClass);

                let currentRowClass = null;

                if (scrub_status === 1 && verified === 0) {
                    currentRowClass = "lacking lacking--contrast";
                } else if (scrub_status === 2 && verified === 0) {
                    currentRowClass = "multiple";
                } else {
                    $(rowEl).hasClass("lacking lacking--contrast") && $(rowEl).removeClass("lacking lacking--contrast");
                    $(rowEl).hasClass("multiple") && $(rowEl).removeClass("multiple");
                }

                if ((parseInt(rowData.has_shift) === 0 && (verified === 0 || !verified))) {
                    currentRowClass = "no-shift";
                } else {
                    $(rowEl).hasClass("no-shift") && $(rowEl).removeClass("no-shift");
                }

                if (!rowData.id && parseInt(rowData.has_shift) === 1) {
                    if ((hasLOA >= 1 && hasWholeDayLoa === 1)) { currentRowClass = "absent absent--contrast"; }
                    else if (hasLOA >= 1 && hasWholeDayLoa <= 0) { currentRowClass = "lacking lacking--contrast"; }
                    else { currentRowClass = "absent absent--contrast"; }

                    if (hasTO >= 1) { currentRowClass = "lacking lacking--contrast"; }
                }

                if (currentRowClass) { $(rowEl).addClass(currentRowClass); }

                toastr[response.toast](response.message, response.title, { timeOut: 10000 });
            }
        }
    });
}

function checkCbSelectAll() {
    const cbCount = $('tbody input[type=\'checkbox\']', tblTimesheet).length;
    const checkedCbCount = $('tbody input[type=\'checkbox\']:checked', tblTimesheet).length;

    if (parseInt(checkedCbCount) >= 1) {
        $('#btn-verify').removeAttr('disabled');
    } else {
        $('#btn-verify').attr('disabled', 'true');
    }

    cbSelectAll.prop('checked', (parseInt(cbCount) === parseInt(checkedCbCount) && parseInt(checkedCbCount) >= 1));
}

function checkCbEmpCluster(emp_id) {
    const cbClusterCount = $(`tbody input[type='checkbox'].cb-emp-${emp_id}`, tblTimesheet).length;
    const checkedClusterCbCount = $(`tbody input[type='checkbox'].cb-emp-${emp_id}:checked`, tblTimesheet).length;

    if (parseInt(checkedClusterCbCount) >= 1) {
        $('#btn-verify').removeAttr('disabled');
    } else {
        $('#btn-verify').attr('disabled', 'true');
    }

    const cbHeader = $('.cb-emp-header[data-emp-id=\'' + emp_id + '\']');
    cbHeader.prop('checked', (parseInt(cbClusterCount) === parseInt(checkedClusterCbCount) && parseInt(checkedClusterCbCount) >= 1));
    checkCbSelectAll();
}

function checkEachCluster() {
    $('.cb-emp-header').each((i, cb) => {
        const emp_id = $(cb).attr('data-emp-id');
        checkCbEmpCluster(emp_id);
    });
}

// START TIME ADJUSTMENT FUNCTIONS
let timeRecord = { am_in: null, am_out: null, pm_in: null, pm_out: null };
let am_in = { modified: 0, manual: 0, value: null, prev_value: null, empty: null };
let am_out = { modified: 0, manual: 0, value: null, prev_value: null, empty: null };
let pm_in = { modified: 0, manual: 0, value: null, prev_value: null, empty: null };
let pm_out = { modified: 0, manual: 0, value: null, prev_value: null, empty: null };

const createTimeAdjustment = function (id, employee_id, employee_name, date, has_shift, has_TO, has_LOA, dtRow) {
    $.ajax({
        url: baseUrl('gcctime/timesheet/get_timesheet_record/' + id + '/' + employee_id),
        type: 'POST',
        dataType: 'JSON',
        data: {
            date,
            has_TO,
            has_LOA,
            csrf_token: _csrf_hash
        },
        success: function (response) {
            if (typeof response.record !== "undefined") {
                const tempRecords = response.record;
                if (tempRecords !== null && (typeof tempRecords.has_overtime !== "undefined")) {
                    const hasOvertimeRequest = parseInt(tempRecords.has_overtime) == 1 ? true : false;
                    setTimeout(function () { vmTempCreateAdjustment.has_overtime = hasOvertimeRequest; }, 500);
                }
            }

            initCreateTimeAdjustmentModal(response, id, employee_id, employee_name, date, has_shift, has_TO, has_LOA, dtRow);
        }
    });
};

function updateHrsConvert(id, tdIndex = 2) {
    const row = $(`tr#row-${id}`);
    const td = $(`td:eq(${tdIndex})`, row);
    const input = td.find('input');
    const p = td.find('p');

    p.html((input.val() ? (parseFloat(input.val()) * 60).toFixed(2) : 0) + ' MINS.');
}

function initCreateTimeAdjustmentModal(response, id, employee_id, employee_name, date, has_shift, has_TO, has_LOA, dtRow) {
    let attendance = [];
    let hasOvertimeRequest = false;
    const tblOvertimeBody = $('#tbl-overtime tbody', createTimeAdjustmentModal);
    const tblHolidayBody = $('#tbl-holiday tbody', createTimeAdjustmentModal);

    const tempRecords = response.record;
    if (tempRecords !== null && (typeof tempRecords.has_overtime !== "undefined")) {
        if (parseInt(tempRecords.has_overtime) == 1) { hasOvertimeRequest = true; }
    }

    if (response.attendance) {
        const _attendance = response.attendance;
        attendance = _attendance.map((item) => {
            return moment(item.datetime).format('hh:mm A');
        });
        attendance = attendance.filter((v, i, a) => a.indexOf(v) === i);
    }

    const record = response.record;
    const schedule = response.schedule;
    const overtime = response.overtime;
    const holiday = response.holiday_references;

    if (Object.keys(holiday).length > 0) {
        $('#holiday-container', createTimeAdjustmentModal).removeClass('m--hide');
        tblHolidayBody.empty();
        const dateFrom = moment(holiday.start_date);
        const dateTo = moment(holiday.end_date);
        const holiday_template = `<tr>
            <td>
                <div class="m--font-boldest">${holiday.description}</div>
                <div class="d-inline">
                    <div class="d-inline m--regular-font-size-sm1 m--font-bolder text-muted">
                        Start Date: ${dateFrom.format('YYYY/MM/DD')}
                    </div>
                    -
                    <div class="d-inline m--regular-font-size-sm1 m--font-bolder text-muted">
                        End Date: ${dateTo.format('YYYY/MM/DD')}
                    </div>
                </div>
            </td>
            <td>
                <p class="m--font-bolder m--regular-font-size-sm1">${holiday.classification}</p>
            </td>
        </tr> `;
        tblHolidayBody.append(holiday_template);
    }

    if (overtime.length) {
        $('#overtime-container', createTimeAdjustmentModal).removeClass('m--hide');
        tblOvertimeBody.empty();

        overtime.forEach((ot, index) => {
            const dateFrom = moment(ot.date_from);
            const dateTo = moment(ot.date_to);
            let strDetails = `<div>
                                <span class="mr-2 text-muted">REFERENCE #:</span>
                                <span class="m--font-boldest">${ot.reference_no}</span>
                            </div>
        <div>
            <span class="mr-2 text-muted">REQUESTOR:</span>
            <span class="m--font-bolder">${ot.requestor}</span>
        </div>`;

            if (dateFrom.format('YYYY-MM-DD') === dateTo.format('YYYY-MM-DD')) {
                strDetails += `<div>
                                    <span class="mr-2 text-muted">DATE:</span>
                                    <span class="m--font-bolder">${dateFrom.format('MMM DD,YYYY hh:mm A')} - ${dateTo.format('hh:mm A')}</span>
                               </div>`;
            } else {
                strDetails += `<div>
                                    <span class="mr-2 text-muted">DATE:</span>
                                    <span class="m--font-bolder">${dateFrom.format('MMM DD,YYYY hh:mm A')} - ${dateTo.format('MMM DD,YYYY hh:mm A')}</span>
                               </div> `;
            }

            strDetails += `<div class="mt-2">
                                <div class="mr-2 text-muted m--regular-font-size-sm1">PURPOSE</div>
                                <div class="m--font-bolder" style="text-align: justify;">${ot.purpose}</div>
                           </div>`;

            let otInValue = (ot.overtime_in !== "0000-00-00 00:00:00" && ot.overtime_in !== null) ? moment(ot.overtime_in).format('YYYY-MM-DD hh:mm A') : "0000-00-00 00:00";
            let otOutValue = (ot.overtime_out !== "0000-00-00 00:00:00" && ot.overtime_out !== null) ? moment(ot.overtime_out).format('YYYY-MM-DD hh:mm A') : "0000-00-00 00:00";

            const template = `<tr id = "row-${ot.id}" class="input-row">
                <td rowspan="2">${strDetails}</td>
                <td>
                    <input type="hidden"
                        id="overtime_in-${ot.id}"
                        name="overtime_in[]"
                        data-ot-id="${ot.id}"
                        data-input-old-value="${ot.overtime_in}"
                        value="${ot.overtime_in}" />
                    <input type="hidden"
                        id="overtime_out-${ot.id}"
                        name="overtime_out[]"
                        data-ot-id="${ot.id}"
                        data-input-old-value="${ot.overtime_out}"
                        value="${ot.overtime_out}" />
                    <div class="form-group">
                        <input type="text" disabled class="form-control form-control--table" value="${ot.total_hrs}">
                        <p class="m--regular-font-size-sm3 text-center mb-0 mt-1 m--font-boldest text-muted">
                            ${(ot.total_hrs * 60).toFixed(2)} Hrs.</p>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" disabled class="form-control form-control--table" name="otTime[]"
                                    data-ot-id="${ot.id}" value="${ot.accredited_hrs}" oninput="updateHrsConvert(${ot.id})"
                                    data-input-old-value="${ot.accredited_hrs}" autocomplete="off">
                            <div class="input-group-append">
                                <button type="button" id="btn-ot-${ot.id}"
                                    data-toggle="m-tooltip" data-original-title="Edit" data-skin="dark"
                                    class="btn btn-success btn-sm m-btn m-btn--icon btnUpdate updateMode"
                                    data-old-value="${ot.accredited_hrs}">
                                    <i class="fa fa-pencil"></i>
                                </button>
                            </div>
                        </div>
                        <p class="m--regular-font-size-sm3 text-center mb-0 mt-1 m--font-boldest text-muted">
                            ${(ot.accredited_hrs * 60).toFixed(2)} Mins.</p>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input type="text" disabled class="form-control form-control--table" value="${ot.ndiff_hrs}">
                        <p class="m--regular-font-size-sm3 text-center mb-0 mt-1 m--font-boldest text-muted">
                            ${(ot.ndiff_hrs * 60).toFixed(2)} Hrs.</p>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" disabled class="form-control form-control--table" name="otNDiffTime[]"
                                    data-ot-id="${ot.id}" value="${ot.accredited_ndiff_hrs}" oninput="updateHrsConvert(${ot.id}, 4)"
                                    data-input-old-value="${ot.accredited_ndiff_hrs}" autocomplete="off">
                            <div class="input-group-append">
                                <button type="button" id="btn-ot-${ot.id}"
                                        data-toggle="m-tooltip" data-original-title="Edit" data-skin="dark"
                                        class="btn btn-success btn-sm m-btn m-btn--icon btnUpdate updateMode"
                                        data-old-value="${ot.accredited_ndiff_hrs}">
                                        <i class="fa fa-pencil"></i>
                                </button>
                            </div>
                        </div>
                        <p class="m--regular-font-size-sm3 text-center mb-0 mt-1 m--font-boldest text-muted">
                            ${(ot.accredited_ndiff_hrs * 60).toFixed(2)} Mins.</p>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="row datetime-picker--container">
                        <div class="col-md-12">
                            <div class="form-group mb-0 text-center">
                                <label class="m--font-bolder">Overtime IN</label>
                                <div class="input-group date">
                                    <p id="temp_overtime_in-${ot.id}" class="form-control m--marginless" style="font-weight: 600;">${otInValue}</p>
                                    <span class="input-group-addon m--bg-success" style="border-color: #34bfa3;"
                                        data-toggle="m-tooltip"
                                        data-original-title="Edit"
                                        data-skin="dark"
                                        data-placement="top"
                                        data-delay='{"show": 500}'
                                        onclick="openManualOvertimeEntryModal('overtime_in-${ot.id}', 'Overtime In')">
                                        <i class="la la-calendar text-white"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td colspan="2">
                    <div class="row datetime-picker--container">
                        <div class="col-md-12">
                            <div class="form-group mb-0 text-center">
                                <label class="m--font-bolder">Overtime OUT</label>
                                <div class="input-group date">
                                    <p id="temp_overtime_out-${ot.id}" class="form-control m--marginless" style="font-weight: 600;">${otOutValue}</p>
                                    <span class="input-group-addon m--bg-success" style="border-color: #34bfa3;"
                                        data-toggle="m-tooltip"
                                        data-original-title="Edit"
                                        data-skin="dark"
                                        data-placement="top"
                                        data-delay='{"show": 500}'
                                        onclick="openManualOvertimeEntryModal('overtime_out-${ot.id}', 'Overtime Out')">
                                        <i class="la la-calendar text-white"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>`;
            tblOvertimeBody.append(template);
        });

    } else {
        $('#overtime-container', createTimeAdjustmentModal).addClass('m--hide');
    }

    Object.assign(timeRecord, {
        am_in: record ? record.am_in : null,
        am_out: record ? record.am_out : null,
        pm_in: record ? record.pm_in : null,
        pm_out: record ? record.pm_out : null
    });

    $('#am_in', createTimeAdjustmentModal)
        .select2({
            allowClear: true,
            width: '100%',
            placeholder: 'TIME',
            dropdownParent: createTimeAdjustmentModal,
            data: attendance
        })
        .on('select2:select', function (e) {
            const prev_value = timeRecord.am_in ? moment(timeRecord.am_in, 'HH:mm:ss').format('hh:mm A') : null;
            am_in.modified = 1;
            am_in.prev_value = prev_value;
        })
        .on('change', function (e) {
            const prev_value = timeRecord.am_in ? moment(timeRecord.am_in, 'HH:mm:ss').format('hh:mm A') : null;

            // evaluate if select is empty & has default or has record value
            if (!e.target.value && timeRecord.am_in) {
                am_in.modified = 1;
                am_in.prev_value = prev_value;
            } else {
                am_in.modified = 0;
                am_in.manual = 0;
                am_in.prev_value = null;
                am_in.empty = null;
            }
        })
        .on('select2:open', function () {
            let a = $(this).data('select2');
            if (!$('.select2-link').length) {
                const tempHtml = `<div class="select2-link"><a>
                    <i class="fa fa-clock-o mr-1"></i>
                    <span class="m--font-bolder">Add Time</span>
                    </a></div>`;
                a.$results.parents('.select2-results')
                    .append(tempHtml)
                    .on('click', function (b) {
                        a.trigger('close');
                        openManualEntryModal('am_in', 'Morning Time in');
                    });
            }
        });

    $('#am_out', createTimeAdjustmentModal)
        .select2({
            allowClear: true,
            width: '100%',
            placeholder: 'TIME',
            dropdownParent: createTimeAdjustmentModal,
            data: attendance
        })
        .on('select2:select', function (e) {
            const prev_value = timeRecord.am_out ? moment(timeRecord.am_out, 'HH:mm:ss').format('hh:mm A') : null;
            am_out.modified = 1;
            am_out.prev_value = prev_value;
        })
        .on('change', function (e) {
            const prev_value = timeRecord.am_out ? moment(timeRecord.am_out, 'HH:mm:ss').format('hh:mm A') : null;

            // evaluate if select is empty & has default or has record value
            if (!e.target.value && timeRecord.am_out) {
                am_out.modified = 1;
                am_out.prev_value = prev_value;
            } else {
                am_out.modified = 0;
                am_out.manual = 0;
                am_out.prev_value = null;
                am_out.empty = null;
            }
        })
        .on('select2:open', function () {
            let a = $(this).data('select2');
            if (!$('.select2-link').length) {
                const tempHtml = `<div class="select2-link"><a>
                    <i class="fa fa-clock-o mr-1"></i>
                    <span class="m--font-bolder">Add Time</span>
                    </a></div>`;
                a.$results.parents('.select2-results')
                    .append(tempHtml)
                    .on('click', function (b) {
                        a.trigger('close');
                        openManualEntryModal('am_out', 'Morning Time out');
                    });
            }
        });

    $('#pm_in', createTimeAdjustmentModal)
        .select2({
            allowClear: true,
            width: '100%',
            placeholder: 'TIME',
            dropdownParent: createTimeAdjustmentModal,
            data: attendance
        })
        .on('select2:select', function (e) {
            const prev_value = timeRecord.pm_in ? moment(timeRecord.pm_in, 'HH:mm:ss').format('hh:mm A') : null;
            pm_in.modified = 1;
            pm_in.prev_value = prev_value;
        })
        .on('change', function (e) {
            const prev_value = timeRecord.pm_in ? moment(timeRecord.pm_in, 'HH:mm:ss').format('hh:mm A') : null;

            // evaluate if select is empty & has default or has record value
            if (!e.target.value && timeRecord.pm_in) {
                pm_in.modified = 1;
                pm_in.prev_value = prev_value;
            } else {
                pm_in.modified = 0;
                pm_in.manual = 0;
                pm_in.prev_value = null;
                pm_in.empty = null;
            }
        })
        .on('select2:open', function () {
            let a = $(this).data('select2');
            if (!$('.select2-link').length) {
                const tempHtml = `<div class="select2-link"><a>
                    <i class="fa fa-clock-o mr-1"></i>
                    <span class="m--font-bolder">Add Time</span>
                    </a></div>`;
                a.$results.parents('.select2-results')
                    .append(tempHtml)
                    .on('click', function (b) {
                        a.trigger('close');
                        openManualEntryModal('pm_in', 'Afternoon Time in');
                    });
            }
        });

    $('#pm_out', createTimeAdjustmentModal)
        .select2({
            allowClear: true,
            width: '100%',
            placeholder: 'TIME',
            dropdownParent: createTimeAdjustmentModal,
            data: attendance
        })
        .on('select2:select', function (e) {
            const prev_value = timeRecord.pm_out ? moment(timeRecord.pm_out, 'HH:mm:ss').format('hh:mm A') : null;
            pm_out.modified = 1;
            pm_out.prev_value = prev_value;
        })
        .on('change', function (e) {
            const prev_value = timeRecord.pm_out ? moment(timeRecord.pm_out, 'HH:mm:ss').format('hh:mm A') : null;
            // evaluate if select is empty & has default or has record value
            if (!e.target.value && timeRecord.pm_out) {
                pm_out.modified = 1;
                pm_out.prev_value = prev_value;
            } else {
                pm_out.modified = 0;
                pm_out.manual = 0;
                pm_out.prev_value = null;
                pm_out.empty = null;
            }
        })
        .on('select2:open', function () {
            let a = $(this).data('select2');
            if (!$('.select2-link').length) {
                const tempHtml = `<div class="select2-link"><a>
                    <i class="fa fa-clock-o mr-1"></i>
                    <span class="m--font-bolder">Add Time</span>
                    </a></div>`;
                a.$results.parents('.select2-results')
                    .append(tempHtml)
                    .on('click', function (b) {
                        a.trigger('close');
                        openManualEntryModal('pm_out', 'Afternoon Time out');
                    });
            }
        });

    if (record) {
        const am_in = moment(record.am_in, 'HH:mm:ss').format('hh:mm A');
        const am_out = moment(record.am_out, 'HH:mm:ss').format('hh:mm A');
        const pm_in = moment(record.pm_in, 'HH:mm:ss').format('hh:mm A');
        const pm_out = moment(record.pm_out, 'HH:mm:ss').format('hh:mm A');

        $('#am_in').val(am_in).trigger('change');
        $('#am_out').val(am_out).trigger('change');
        $('#pm_in').val(pm_in).trigger('change');
        $('#pm_out').val(pm_out).trigger('change');
    }

    $('#employee-name', createTimeAdjustmentModal).html(employee_name);
    $('#record-date', createTimeAdjustmentModal).html(moment(date).format('MM/DD/YYYY, ddd'));
    $('#emp_id', createTimeAdjustmentModal).val(employee_id);
    $('#date', createTimeAdjustmentModal).val(date);
    $('#dtRow', createTimeAdjustmentModal).val(dtRow);
    $('form', createTimeAdjustmentModal).attr('action', baseUrl('gcctime/timesheet/save_time_adjustment_request/' + (id ? id : 0)));

    if (parseInt(has_shift) === 0 || !has_shift) {
        $('#manual-shift-schedule').removeClass('m--hide');
        // $('#am_start, #am_end, #pm_start, #pm_end').attr('data-validation', 'required');

        $('#shift-schedule').addClass(('m--hide'));
    } else {
        $('#manual-shift-schedule').addClass('m--hide');
        $('#am_start, #am_end, #pm_start, #pm_end').removeAttr('data-validation');

        if (schedule.shift_am_start !== null && schedule.shift_am_end !== null) {
            $('#shift_am_start').val(schedule.shift_am_start);
            $('#shift_am_end').val(schedule.shift_am_end);
            $('#am_in').removeAttr('disabled');
            $('#am_out').removeAttr('disabled');
        } else {
            $('#am_in').attr('disabled', 'disabled');
            $('#am_out').attr('disabled', 'disabled');
        }

        if (schedule.shift_pm_start !== null && schedule.shift_pm_end !== null) {
            $('#shift_pm_start').val(schedule.shift_pm_start);
            $('#shift_pm_end').val(schedule.shift_pm_end);
            $('#pm_in').removeAttr('disabled');
            $('#pm_out').removeAttr('disabled');
        } else {
            $('#pm_in').val("").trigger("change").attr('disabled', 'disabled');
            $('#pm_out').val("").trigger("change").attr('disabled', 'disabled');
        }

        $('#shift-schedule').removeClass(('m--hide'));
    }


    if (parseInt(has_TO) >= 1) {
        $('#to-container').removeClass('m--hide');
        $('#to-list')
            .attr('data-validation', 'required')
            .select2({
                placeholder: '',
                width: '100%',
                data: response.to,
                dropdownParent: createTimeAdjustmentModal,
                escapeMarkup: function (markup) {
                    return markup;
                },
            });

        if (record) {
            if (parseInt(record.scrub_status) > 0) {
                $('label[for=\'to-list\'] span.m--font-danger', $('#to-container')).removeClass('m--hide');
                $('#to-list').attr('data-validation', 'required');
            } else {
                $('label[for=\'to-list\'] span.m--font-danger', $('#to-container')).addClass('m--hide');
                $('#to-list').removeAttr('data-validation');
            }
        } else {
            $('label[for=\'to-list\'] span.m--font-danger', $('#to-container')).removeClass('m--hide');
            $('#to-list').attr('data-validation', 'required');
        }
    } else {
        $('#to-container').addClass('m--hide');
        $('#to-list').removeAttr('data-validation');
    }

    if (parseInt(has_LOA) >= 1) {
        $('#loa-container').removeClass('m--hide');
        $('#loa-list')
            .attr('data-validation', 'required')
            .select2({
                placeholder: '',
                width: '100%',
                data: response.loa,
                dropdownParent: createTimeAdjustmentModal,
                escapeMarkup: function (markup) {
                    return markup;
                },
            });

        if (record) {
            if (parseInt(record.scrub_status) > 0) {
                $('label[for=\'loa-list\'] span.m--font-danger', $('#loa-container')).removeClass('m--hide');
                $('#loa-list').attr('data-validation', 'required');
            } else {
                $('label[for=\'loa-list\'] span.m--font-danger', $('#loa-container')).addClass('m--hide');
                $('#loa-list').removeAttr('data-validation');
            }
        } else {
            $('label[for=\'loa-list\'] span.m--font-danger', $('#loa-container')).removeClass('m--hide');
            $('#loa-list').attr('data-validation', 'required');
        }
    } else {
        $('#loa-container').addClass('m--hide');
        $('#loa-list').removeAttr('data-validation');
    }

    $('#has-shift').val(has_shift);

    if (hasOvertimeRequest === false) {
        createTimeAdjustmentModal.find("#temp_overtime_in").text("");
        createTimeAdjustmentModal.find("#temp_overtime_out").text("");
        createTimeAdjustmentModal.find("#overtime_in").val("");
        createTimeAdjustmentModal.find("#overtime_out").val("");

        const tempRequestedBy = createTimeAdjustmentModal.find("#requested_by");
        if (typeof tempRequestedBy !== "undefined") {
            if (tempRequestedBy.val()) {
                tempRequestedBy.val("")
                    .trigger("change")
                    .select2("destroy");
            }
            tempRequestedBy.select2({
                width: "100%",
                placeholder: "Select an option",
                dropdownParent: createTimeAdjustmentModal,
                ajax: {
                    url: siteUrl("eforms/overtime/get_employee_department_head"),
                    dataType: "json",
                    delay: 250,
                    global: false,
                    processResults: function (data) {
                        return data;
                    }
                }
            });
        }
    }

    createTimeAdjustmentModal.modal('show');
}

createTimeAdjustmentModal.on('hidden.bs.modal', function () {
    $('#am_in > option').remove();
    $('#am_out > option').remove();
    $('#pm_in > option').remove();
    $('#pm_out > option').remove();
    $('#to-list > option').remove();
    $('#loa-list > option').remove();
});

function openManualEntryModal(el, title) {
    $('.modal-title', timeManualEntryModal).html(`MANUAL ENTRY - <span class="m--font-bolder">${title}</span>`);
    $('#field', timeManualEntryModal).val(el);
    timeManualEntryModal.modal('show');
}

function openManualOvertimeEntryModal(el, title) {
    $('.modal-title', timeManualOvertimeEntryModal).html(`MANUAL ENTRY - <span class="m--font-bolder">${title}</span>`);
    $('#field', timeManualOvertimeEntryModal).val(el);
    const tempElement = $(`#${el} `, createTimeAdjustmentModal);
    if (typeof tempElement === "undefined" && tempElement.length == 0) { validateManualOvertime(); }
    timeManualOvertimeEntryModal.modal('show');

    //------------START JV ----------
    //alert($("#record-date").html());
    timeManualOvertimeEntryModal.find("#date").val($("#record-date").html().split(",")[0]);
    var newdate = $("#record-date").html().split(",")[0].split("/");
    timeManualOvertimeEntryModal.find("#date").data('datepicker').setStartDate(newdate[2] + '-' + newdate[0] + '-' + newdate[1]);
    // timeManualOvertimeEntryModal.find("#date").data('datepicker').setDateFormat('yyyy-mm-dd');
    //------------END JV ----------
}

function openOvertimeManualEntryModal() {
    $('.modal-title', overtimeManualEntryModal).html(`MANUAL ENTRY - <span class="m--font-bolder">OVERTIME</span>`);
    overtimeManualEntryModal.modal('show');
}

// START ON SUBMIT OF REQUEST
$.validate({
    form: $('#frm-create-time-adjustment-modal'),
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const fields = ['am_in', 'am_out', 'pm_in', 'pm_out'];
        const data = keyPairSerializedArray($(form).serializeArray());
        const submit = $('button[type=\'submit\']', $(form));
        const url = $(form).attr('action');
        const id = url.split('/').pop();
        const has_shift = parseInt(data['has_shift']);
        const dtRow = $('#dtRow').val();
        let overtimeUpdates = [];

        const _shift = ['am_start', 'am_end', 'pm_start', 'pm_end'];
        let shifts = { am_start: null, am_end: null, pm_start: null, pm_end: null };

        const _manualOT = ["overtime_reg_hrs", "overtime_ndiff_hrs", "overtime_in", "overtime_out", "overtime_requested_by", "overtime_purpose"];
        let manualOvertime = {
            overtime_reg_hrs: 0, overtime_ndiff_hrs: 0, overtime_in: null,
            overtime_out: null, overtime_requested_by: 0, overtime_purpose: null
        };

        const to = $('#to-list').val();
        const loa = $('#loa-list').val();

        const emp_id = data['emp_id'];
        const date = data['date'];

        fields.forEach((field, i) => {
            const value = data[field];
            Object.assign(eval(field), {
                value: (value !== undefined ? value : null)
            });
        });

        _shift.forEach((field, i) => {
            shifts[field] = data[field];
        });

        _manualOT.forEach((field, i) => {
            manualOvertime[field] = data[field];
        });

        $('#tbl-overtime tbody tr.input-row').each((i, row) => {
            const origRegOT = $('td:eq(1) input', row).val();
            const regOT = $('input[name=\'otTime[]\']', row);
            const regOTPrevVal = regOT.attr('data-input-old-value');

            const orignDiffOT = $('td:eq(3) input', row).val();
            const nDiffOT = $('input[name=\'otNDiffTime[]\']', row);
            const nDiffOTPrevVal = nDiffOT.attr('data-input-old-value');

            const otIn = $('input[name=\'overtime_in[]\']', row);
            const otInPrevVal = otIn.attr('data-input-old-value');
            const otOut = $('input[name=\'overtime_out[]\']', row);
            const otOutPrevVal = otOut.attr('data-input-old-value');

            if (regOT.length || nDiffOT.length) {
                let insertTempOT = false;
                const id = regOT.attr('data-ot-id');
                const value = regOT.val();
                const n_diff_value = nDiffOT.val();

                let ot_in_value = otIn.val();
                let ot_out_value = otOut.val();

                ot_in_value = ot_in_value !== "" && ot_in_value !== null ? moment(ot_in_value).format('YYYY-MM-DD HH:mm:ss') : ot_in_value;
                ot_out_value = ot_out_value !== "" && ot_out_value !== null ? moment(ot_out_value).format('YYYY-MM-DD HH:mm:ss') : ot_out_value;

                let tempOtObject = {
                    id,
                    value,
                    prev_val: regOTPrevVal,
                    n_diff_value,
                    n_diff_prev_value: nDiffOTPrevVal,
                    ot_in_value,
                    ot_in_prev_value: otInPrevVal,
                    ot_out_value,
                    ot_out_prev_value: otOutPrevVal
                };

                if (origRegOT !== value || orignDiffOT !== n_diff_value) { insertTempOT = true; }
                if (otInPrevVal !== ot_in_value || otOutPrevVal !== ot_out_value) { insertTempOT = true; }
                if (insertTempOT) { overtimeUpdates.push(tempOtObject); }
            }
        });

        $.ajax({
            url,
            type: 'POST',
            dataType: 'JSON',
            data: {
                csrf_token: _csrf_hash,
                am_in, am_out, pm_in, pm_out,
                adjustment_override: data.adjustment_override,
                remarks: data.remarks,
                shifts,
                has_shift,
                travel_order: to.length >= 1 ? to : null,
                loa: loa.length >= 1 ? loa : null,
                emp_id,
                date,
                manualOvertime,
                overtimeUpdates
            },
            beforeSend: function () {
                submit.addClass(`m - btn--custom m - loader m - loader--light m - loader--left`);
            },
            success: function (response) {
                submit.removeClass(`m - btn--custom m - loader m - loader--light m - loader--left`);

                if (response) {
                    const toast = response.success ? 'success' : 'error';
                    toastr[toast](response.message, response.title);

                    const rowEl = $(`#ellipses - menu - ${dtRow} `).closest('tr');
                    $('td:eq(0)', rowEl)
                        .empty()
                        .html(`< i class="fa fa-exclamation-circle"
    data - toggle="m-tooltip"
    data - original - title="Has Time Adjustments."
    data - skin="dark"
    data - placement="left"
    style = "font-size: 20px; cursor: pointer; color: #000000;"
    data - id="${response.data.timesheet_id}"
    onclick = "openTimeAdjustmentListModal(this, ${emp_id})" ></i > `);

                    $('.time-adjustment-details', rowEl).removeClass('m--hide');
                    $('.time-adjustment-details a', rowEl).attr('data-id', response.data.timesheet_id);
                    $('.time-adjustment-details-separator', rowEl).removeClass('m--hide');

                    if (response.type === 'new') {
                        $(rowEl).addClass('lacking');
                        $('td', rowEl).css('color', '#000000');
                        $('td:eq(7)', rowEl).empty().html(0);
                        $('td:eq(8)', rowEl).empty().html(0);
                        $('td:eq(9)', rowEl).empty().html('0.00');
                    }

                    timeRecord = { am_in: null, am_out: null, pm_in: null, pm_out: null };
                    am_in = { modified: 0, manual: 0, value: null, prev_value: null, empty: null };
                    am_out = { modified: 0, manual: 0, value: null, prev_value: null, empty: null };
                    pm_in = { modified: 0, manual: 0, value: null, prev_value: null, empty: null };
                    pm_out = { modified: 0, manual: 0, value: null, prev_value: null, empty: null };
                }

                createTimeAdjustmentModal.modal('hide');
            }
        });

        return false;
    }
});
// END ON SUBMIT OF REQUEST

// START ON MANUAL TIME ENTRY SUBMIT
$.validate({
    form: $('#frm-time-manual-entry'),
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const data = keyPairSerializedArray($(form).serializeArray());
        const time = moment(data.time, 'HH:mm').format('hh:mm A');
        const field = data.field;
        const obj = eval(field);

        const prev_value = timeRecord[field] ? moment(timeRecord[field], 'HH:mm:ss').format('hh:mm A') : null;

        Object.assign(obj, {
            modified: 1,
            manual: 1,
            prev_value
        });

        const option = new Option(time, time, false, true);
        $(`#${field} `).append(option);

        timeManualEntryModal.modal('hide');
        setTimeout(() => {
            $(form).resetForm();
        }, 500);
        return false;
    }
});

var validateManualOvertime = function () {
    $.validate({
        form: $('#frm-time-manual-overtime-entry'),
        lang: 'en',
        scrollToTopOnError: false,
        onSuccess: function (form) {
            const data = keyPairSerializedArray($(form).serializeArray());
            const time = moment(data.time, 'HH:mm').format('hh:mm A');
            const date = moment(data.date).format('YYYY-MM-DD');
            const datetime = date + " " + time;
            const field = data.field;
            $(`#temp_${field} `, createTimeAdjustmentModal).text(datetime);
            $(`#${field} `, createTimeAdjustmentModal).val(datetime);
            timeManualOvertimeEntryModal.modal('hide');
            setTimeout(() => { $(form).resetForm(); }, 500);
            return false;
        }
    });
}

validateManualOvertime();
// END ON MANUAL TIME ENTRY SUBMIT

function keyPairSerializedArray(serializedArray) {
    let result = {};
    $.each(serializedArray, function () {
        result[this.name] = this.value;
    });

    return result;
}

function resetField(el) {
    const time = timeRecord[el] ? moment(timeRecord[el], 'HH:mm:ss').format('hh:mm A') : null;
    const obj = eval(el);
    Object.assign(obj, { modified: 0, manual: 0, prev_value: null });

    $(`#${el} `).val(time).trigger('change');
}

createTimeAdjustmentModal
    .on('hide.bs.modal', function () {
        $('form', this).resetForm();
    });

timeManualEntryModal
    .on('shown.bs.modal', function () {
        $('#time').focus();
    });

// END TIME ADJUSTMENT FUNCTIONS


function openTimeAdjustmentListModal(el, employee_id) {
    const timesheet_id = $(el).attr('data-id');
    $.ajax({
        url: baseUrl(`gcctime/timesheet/get_timesheet_time_adjustments_list/${timesheet_id}/${employee_id}`),
        type: 'GET',
        dataType: 'JSON',
        success: function (response) {
            const { data } = response;
            $('.modal-dialog', modalContainer).css('max-width', '80%');
            $('.modal-content', modalContainer).empty().append(response.modal);
            const dtTempTable = dtTimesheetAdjustment();
            dtTempTable.clear().rows.add(data).draw(false);
            dtTimeAdjustmentsListEvent(dtTempTable);
            setTimeout(() => {
                modalContainer.modal('show');
            }, 750);
        }
    });
}

function openTimeAdjustmentListModalByVarId(timesheet_id, employee_id) {
    $.ajax({
        url: baseUrl(`gcctime/timesheet/get_timesheet_time_adjustments_list/${timesheet_id}/${employee_id}`),
        type: 'GET',
        dataType: 'JSON',
        success: function (response) {
            const { data } = response;
            $('.modal-dialog', modalContainer).css('max-width', '70%');
            $('.modal-content', modalContainer).empty().append(response.modal);
            const dtTempTable = dtTimesheetAdjustment();
            dtTempTable.clear().rows.add(data).draw(false);
            dtTimeAdjustmentsListEvent(dtTempTable);
            setTimeout(() => {
                modalContainer.modal('show');
            }, 750);
        }
    });
}

const dtTimeAdjustmentsListEvent = function (dtTable) {
    if(typeof dtTable !== 'undefined'){
        $('#tbl-time-adjustments-list').on('click', 'tbody tr', function (e) {
            const row = dtTable.row($(this));
            const data = row.data();
            openTimeAdjustmentDetailsModal(data.time_adjustment_id);
        });
    }
}

const dtTimesheetAdjustment = function(){
    return $('#tbl-time-adjustments-list').DataTable({
        dom: '<\'row\'<\'col-12\' rt>><\'row\'<\'col-6\' l><\'col-6\' p>>',
        serverSide: false,
        destroy: true,
        columns: [{
                width: '8%',
                data: 'am_in',
                className: 'text-center',
                render: function (data, type, row) {
                    let style = 'm--font-bolder text-muted';
                    let tooltipText = '';

                    if (parseInt(row.am_in_is_requested) === 1) {
                        style = 'm--font-boldest2 m--font-danger';
                        tooltipText = row.am_in_prev ? `Previous: ${moment(row.am_in_prev, 'HH:mm:ss').format('hh:mm A')}` : 'No previous value.';
                    }

                    return data && data !== "empty" ? `<span data-toggle="m-tooltip" data-original-title="${tooltipText}" data-skin="dark"
                        class="${style} time-records">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '--:--';
                }
            }, {
                width: '8%',
                data: 'am_out',
                className: 'text-center',
                render: function (data, type, row) {
                    let style = 'm--font-bolder text-muted';
                    let tooltipText = '';

                    if (parseInt(row.am_out_is_requested) === 1) {
                        style = 'm--font-boldest2 m--font-danger';
                        tooltipText = row.am_out_prev ? `Previous: ${moment(row.am_out_prev, 'HH:mm:ss').format('hh:mm A')}` : 'No previous value.';
                    }

                    return data && data !== "empty" ? `<span data-toggle="m-tooltip" data-original-title="${tooltipText}" data-skin="dark"
                                    class="${style} time-records">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '--:--';
                }
            }, {
                width: '8%',
                data: 'pm_in',
                className: 'text-center',
                render: function (data, type, row) {
                    let style = 'm--font-bolder text-muted';
                    let tooltipText = '';

                    if (parseInt(row.pm_in_is_requested) === 1) {
                        style = 'm--font-boldest2 m--font-danger';
                        tooltipText = row.pm_in_prev ? `Previous: ${moment(row.pm_in_prev, 'HH:mm:ss').format('hh:mm A')}` : 'No previous value.';
                    }

                    return data && data !== "empty" ? `<span data-toggle="m-tooltip" data-original-title="${tooltipText}" data-skin="dark"
                                    class="${style} time-records">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '--:--';
                }
            }, {
                width: '8%',
                data: 'pm_out',
                className: 'text-center',
                render: function (data, type, row) {
                    let style = 'm--font-bolder text-muted';
                    let tooltipText = '';

                    if (parseInt(row.pm_out_is_requested) === 1) {
                        style = 'm--font-boldest2 m--font-danger';
                        tooltipText = row.pm_out_prev ? `Previous: ${moment(row.pm_out_prev, 'HH:mm:ss').format('hh:mm A')}` : 'No previous value.';
                    }

                    return data && data !== "empty" ? `<span data-toggle="m-tooltip" data-original-title="${tooltipText}" data-skin="dark"
                                    class="${style} time-records">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '--:--';
                }
            }, {
                width: '8%',
                data: 'am_start',
                className: 'text-center',
                render: function (data, type, row) {
                    const style = parseInt(row.has_shift) === 0 ? 'm--font-boldest2 m--font-danger': 'm--font-bolder text-muted';
                    if ((data && data === null) || !data) {
                        return `<span class="text-muted">N/A</span>`;
                    }

                    return data ? `<span class="${style}">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '';
                }
            }, {
                width: '8%',
                data: 'am_end',
                className: 'text-center',
                render: function (data, type, row) {
                    const style = parseInt(row.has_shift) === 0 ? 'm--font-boldest2 m--font-danger': 'm--font-bolder text-muted';
                    if ((data && data === null) || !data) {
                        return `<span class="text-muted">N/A</span>`;
                    }

                    return data ? `<span class="${style}">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '';
                }
            }, {
                width: '8%',
                data: 'pm_start',
                className: 'text-center',
                render: function (data, type, row) {
                    const style = parseInt(row.has_shift) === 0 ? 'm--font-boldest2 m--font-danger': 'm--font-bolder text-muted';
                    if ((data && data === null) || !data) {
                        return `<span class="text-muted">N/A</span>`;
                    }

                    return data ? `<span class="${style}">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '';
                }
            }, {
                width: '8%',
                data: 'pm_end',
                className: 'text-center',
                render: function (data, type, row) {
                    const style = parseInt(row.has_shift) === 0 ? 'm--font-boldest2 m--font-danger': 'm--font-bolder text-muted';
                    if ((data && data === null) || !data) {
                        return `<span class="text-muted">N/A</span>`;
                    }

                    return data ? `<span class="${style}">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '';
                }
            }, {
                width: '7%', // LATE
                data: null,
                className: 'text-center',
                render: function (data, type, row) {
                    let total_late = 0;
                    total_late = calc_late(row);
                    return `<span class="${total_late > 0 ? 'm--font-boldest' : 'm--font-bolder'}">${total_late}</span>`;
                }
            }, {
                width: '7%',
                data: null,
                className: 'text-center',
                render: function (data, type, row) {
                    let total_ut = 0;
                    total_ut = calc_ut(row);
                    return `<span class="${total_ut > 0 ? 'm--font-boldest' : 'm--font-bolder'}">${total_ut}</span>`;
                }
            }, {
                width: '7%',
                data: null,
                className: 'text-center',
                render: function (data, type, row) {
                    const total = calc_reghr(row);

                    return `<div class="m--font-boldest">
                                ${total.split(",")[0]}
                            </div>
                            <div class="m--regular-font-size-sm1 text-muted">
                                <span>${total.split(",")[1]}</span>
                                <span style="text-transform: none;">mins.</span>
                            </div>`;
                }
            }, {
                width: '7%',
                data: 'ot_adj_value',
                className: 'text-center',
                render: function (data, type, row, meta) {
                    /*** let originalRegOTHrs = parseFloat(row.ot_original_value) || 0;
                    originalRegOTHrs = formatDecimal(originalRegOTHrs, 2);
                    let originalNDiffOTHrs = parseFloat(row.ot_ndiff_original_value) || 0;
                    originalNDiffOTHrs = formatDecimal(originalNDiffOTHrs, 2);

                    let originalTotalOTHrs = parseFloat(originalRegOTHrs) + parseFloat(originalNDiffOTHrs);
                    originalTotalOTHrs = formatDecimal(originalTotalOTHrs, 2);
                    const originalTotalOTHrs_inMinutes = formatDecimal(originalTotalOTHrs * 60, 2); ***/

                    let adjRegOTHrs = parseFloat(row.ot_adj_value) || 0;
                    adjRegOTHrs = formatDecimal(adjRegOTHrs, 2);
                    let adjNDiffOTHrs = parseFloat(row.ot_ndiff_adj_value) || 0;
                    adjNDiffOTHrs = formatDecimal(adjNDiffOTHrs, 2);

                    let adjTotalOTHrs = parseFloat(adjRegOTHrs) + parseFloat(adjNDiffOTHrs);
                    adjTotalOTHrs = formatDecimal(adjTotalOTHrs, 2);
                    const adjTotalOTHrs_inMinutes = formatDecimal(adjTotalOTHrs * 60, 2);

                    let tooltipTemplate = ``;
                    const highlightClass = row.has_overtime_request ? `m--font-danger` : ``;

                    tooltipTemplate += `<div class='text-left'>
                                            <div>
                                                <span>Reg.Hrs: </span>
                                                <span class='m--font-boldest'>${adjRegOTHrs}</span>
                                            </div>
                                            <div>
                                                <span>Night Diff. Hrs: </span>
                                                <span class='m--font-boldest'>${adjNDiffOTHrs}</span>
                                            </div>
                                        </div>`;

                    return `<div data-toggle="m-tooltip"
                            data-html="true"
                            data-original-title="${parseFloat(adjTotalOTHrs) > 0 ? tooltipTemplate : ``}"
                            style="cursor: pointer;">
                        <div class="m--font-boldest ${highlightClass}">
                                ${adjTotalOTHrs}
                        </div>
                        <div class="m--regular-font-size-sm1 text-muted ${highlightClass}"
                                style="text-transform: none;">
                            ${adjTotalOTHrs_inMinutes} mins.
                        </div>
                    </div>`;
                }
            }, {
                data: 'status',
                className: 'text-center',
                render: function (data, type, row, meta) {
                    let status = null;
                    switch (parseInt(data)) {
                        case 1:
                            status = { class: 'm-badge--success', text: 'Approved' };
                            break;
                        case 2:
                            status = { class: 'm-badge--danger', text: 'Declined' };
                            break;
                        case 3:
                            status = { class: 'm-badge--metal', text: 'Cancelled' };
                            break;
                        default:
                            status = { class: 'm-badge--warning', text: 'Pending' };
                            break;
                    }

                    return `<span class="m-badge m-badge--wide m--font-boldest ${status.class}">${status.text}</span>`;
                }
            }
        ], columnDefs: [{
            targets: "_all",
            defaultContent: "",
        }],
        ordering: false,
        pageLength: 15,
        autoWidth: false,
        lengthMenu: [[15, 25, 50, 100, 200, -1], [15, 25, 50, 100, 200, 'All']],
    });
}

/*** modalContainer.on('shown.bs.modal', function (e) {
    if ($('#tbl-time-adjustments-list', this).length >= 1) {
        const employee_id = $('#employee_id', this).val();
        const timesheet_id = $('#timesheet_id', this).val();

        const dtTimeAdjustmentsList = $('#tbl-time-adjustments-list').DataTable({
            dom: '<\'row\'<\'col-12\' rt>><\'row\'<\'col-6\' l><\'col-6\' p>>',
            serverSide: true,
            destroy: true,
            ajax: {
                url: baseUrl('gcctime/timesheet/get_time_adjustments_request/' + employee_id + '/' + timesheet_id),
                type: 'POST',
                dataType: 'JSON',
                data: function (_data) {
                    _data.csrf_token = _csrf_hash;
                }
            },
            columns: [
                {
                    width: '8%',
                    data: 'am_in',
                    className: 'text-center',
                    render: function (data, type, row) {
                        let style = 'm--font-bolder text-muted';
                        let tooltipText = '';

                        if (parseInt(row.am_in_is_requested) === 1) {
                            style = 'm--font-boldest2 m--font-danger';
                            tooltipText = row.am_in_prev ? `Previous: ${moment(row.am_in_prev, 'HH:mm:ss').format('hh:mm A')}` : 'No previous value.';
                        }

                        return data && data !== "empty" ? `<span data-toggle="m-tooltip" data-original-title="${tooltipText}" data-skin="dark"
                                     class="${style} time-records">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '--:--';
                    }
                },
                {
                    width: '8%',
                    data: 'am_out',
                    className: 'text-center',
                    render: function (data, type, row) {
                        let style = 'm--font-bolder text-muted';
                        let tooltipText = '';

                        if (parseInt(row.am_out_is_requested) === 1) {
                            style = 'm--font-boldest2 m--font-danger';
                            tooltipText = row.am_out_prev ? `Previous: ${moment(row.am_out_prev, 'HH:mm:ss').format('hh:mm A')}` : 'No previous value.';
                        }

                        return data && data !== "empty" ? `<span data-toggle="m-tooltip" data-original-title="${tooltipText}" data-skin="dark"
                                     class="${style} time-records">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '--:--';
                    }
                },
                {
                    width: '8%',
                    data: 'pm_in',
                    className: 'text-center',
                    render: function (data, type, row) {
                        let style = 'm--font-bolder text-muted';
                        let tooltipText = '';

                        if (parseInt(row.pm_in_is_requested) === 1) {
                            style = 'm--font-boldest2 m--font-danger';
                            tooltipText = row.pm_in_prev ? `Previous: ${moment(row.pm_in_prev, 'HH:mm:ss').format('hh:mm A')}` : 'No previous value.';
                        }

                        return data && data !== "empty" ? `<span data-toggle="m-tooltip" data-original-title="${tooltipText}" data-skin="dark"
                                     class="${style} time-records">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '--:--';
                    }
                },
                {
                    width: '8%',
                    data: 'pm_out',
                    className: 'text-center',
                    render: function (data, type, row) {
                        let style = 'm--font-bolder text-muted';
                        let tooltipText = '';

                        if (parseInt(row.pm_out_is_requested) === 1) {
                            style = 'm--font-boldest2 m--font-danger';
                            tooltipText = row.pm_out_prev ? `Previous: ${moment(row.pm_out_prev, 'HH:mm:ss').format('hh:mm A')}` : 'No previous value.';
                        }

                        return data && data !== "empty" ? `<span data-toggle="m-tooltip" data-original-title="${tooltipText}" data-skin="dark"
                                     class="${style} time-records">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '--:--';
                    }
                },
                {
                    width: '8%',
                    data: 'am_start',
                    className: 'text-center',
                    render: function (data, type, row) {
                        let style = 'm--font-bolder text-muted';
                        if (parseInt(row.has_shift) === 0) {
                            style = 'm--font-boldest2 m--font-danger';
                        }

                        if ((data && data === null) || !data) {
                            return `<span class="text-muted">N/A</span>`;
                        }

                        return data ? `<span class="${style}">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '';
                    }
                },
                {
                    width: '8%',
                    data: 'am_end',
                    className: 'text-center',
                    render: function (data, type, row) {
                        let style = 'm--font-bolder text-muted';
                        if (parseInt(row.has_shift) === 0) {
                            style = 'm--font-boldest2 m--font-danger';
                        }

                        if ((data && data === null) || !data) {
                            return `<span class="text-muted">N/A</span>`;
                        }

                        return data ? `<span class="${style}">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '';
                    }
                },
                {
                    width: '8%',
                    data: 'pm_start',
                    className: 'text-center',
                    render: function (data, type, row) {
                        let style = 'm--font-bolder text-muted';
                        if (parseInt(row.has_shift) === 0) {
                            style = 'm--font-boldest2 m--font-danger';
                        }

                        if ((data && data === null) || !data) {
                            return `<span class="text-muted">N/A</span>`;
                        }

                        return data ? `<span class="${style}">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '';
                    }
                },
                {
                    width: '8%',
                    data: 'pm_end',
                    className: 'text-center',
                    render: function (data, type, row) {
                        let style = 'm--font-bolder text-muted';
                        if (parseInt(row.has_shift) === 0) {
                            style = 'm--font-boldest2 m--font-danger';
                        }

                        if ((data && data === null) || !data) {
                            return `<span class="text-muted">N/A</span>`;
                        }

                        return data ? `<span class="${style}">${moment(data, 'HH:mm:ss').format('hh:mm A')}</span>` : '';
                    }
                },
                {
                    width: '7%', // LATE
                    data: null,
                    className: 'text-center',
                    render: function (data, type, row) {
                        var total_late = 0;
                        total_late = calc_late(row);
                        return `<span class="${total_late > 0 ? 'm--font-boldest' : 'm--font-bolder'}">${total_late}</span>`;
                    }
                },
                {
                    width: '7%',
                    data: null,
                    className: 'text-center',
                    render: function (data, type, row) {
                        var total_ut = 0;
                        total_ut = calc_ut(row);
                        return `<span class="${total_ut > 0 ? 'm--font-boldest' : 'm--font-bolder'}">${total_ut}</span>`;
                    }
                },
                {
                    width: '7%',
                    data: null,
                    className: 'text-center',
                    render: function (data, type, row) {
                        var total = calc_reghr(row);

                        return `<div class="m--font-boldest">
                                    ${total.split(",")[0]}
                                </div>
                                <div class="m--regular-font-size-sm1 text-muted">
                                    <span>${total.split(",")[1]}</span>
                                    <span style="text-transform: none;">mins.</span>
                                </div>`;
                    }
                },
                {
                    width: '7%',
                    data: 'ot_adj_value',
                    className: 'text-center',
                    render: function (data, type, row, meta) {
                        let originalRegOTHrs = parseFloat(row.ot_original_value) || 0;
                        originalRegOTHrs = formatDecimal(originalRegOTHrs, 2);
                        let originalNDiffOTHrs = parseFloat(row.ot_ndiff_original_value) || 0;
                        originalNDiffOTHrs = formatDecimal(originalNDiffOTHrs, 2);

                        let originalTotalOTHrs = parseFloat(originalRegOTHrs) + parseFloat(originalNDiffOTHrs);
                        originalTotalOTHrs = formatDecimal(originalTotalOTHrs, 2);
                        const originalTotalOTHrs_inMinutes = formatDecimal(originalTotalOTHrs * 60, 2);

                        let adjRegOTHrs = parseFloat(row.ot_adj_value) || 0;
                        adjRegOTHrs = formatDecimal(adjRegOTHrs, 2);
                        let adjNDiffOTHrs = parseFloat(row.ot_ndiff_adj_value) || 0;
                        adjNDiffOTHrs = formatDecimal(adjNDiffOTHrs, 2);

                        let adjTotalOTHrs = parseFloat(adjRegOTHrs) + parseFloat(adjNDiffOTHrs);
                        adjTotalOTHrs = formatDecimal(adjTotalOTHrs, 2);
                        const adjTotalOTHrs_inMinutes = formatDecimal(adjTotalOTHrs * 60, 2);

                        let tooltipTemplate = ``;
                        let highlightClass = ``;

                        if (row.has_overtime_request) {
                            highlightClass = `m--font-danger`;
                        }

                        tooltipTemplate += `<div class='text-left'>
                                                <div>
                                                    <span>Reg.Hrs: </span>
                                                    <span class='m--font-boldest'>${adjRegOTHrs}</span>
                                                </div>
                                                <div>
                                                    <span>Night Diff. Hrs: </span>
                                                    <span class='m--font-boldest'>${adjNDiffOTHrs}</span>
                                                </div>
                                           </div>`;

                        return `<div data-toggle="m-tooltip"
                             data-html="true"
                             data-original-title="${parseFloat(adjTotalOTHrs) > 0 ? tooltipTemplate : ``}"
                             style="cursor: pointer;">
                            <div class="m--font-boldest ${highlightClass}">
                                 ${adjTotalOTHrs}
                            </div>
                            <div class="m--regular-font-size-sm1 text-muted ${highlightClass}"
                                 style="text-transform: none;">
                                ${adjTotalOTHrs_inMinutes} mins.
                            </div>
                        </div>`;
                    }
                },
                {
                    data: 'status',
                    className: 'text-center',
                    render: function (data, type, row, meta) {
                        let status = null;
                        switch (parseInt(data)) {
                            case 1:
                                status = { class: 'm-badge--success', text: 'Approved' };
                                break;
                            case 2:
                                status = { class: 'm-badge--danger', text: 'Declined' };
                                break;
                            case 3:
                                status = { class: 'm-badge--metal', text: 'Cancelled' };
                                break;
                            default:
                                status = { class: 'm-badge--warning', text: 'Pending' };
                                break;
                        }

                        return `<span class="m-badge m-badge--wide m--font-boldest ${status.class}">${status.text}</span>`;
                    }
                }
            ],
            ordering: false,
            pageLength: 15,
            autoWidth: false,
            lengthMenu: [[15, 25, 50, 100, 200, -1], [15, 25, 50, 100, 200, 'All']],
        });
    }
}); ***/

function calc_late(row) {
    let am_late = 0;
    let pm_late = 0;
    let total_late = 0;

    const tempRecord = generateAttendanceShiftRecord(row);
    let am_in = (typeof tempRecord.am_in !== "undefined" && tempRecord.am_in) ? tempRecord.am_in : null;
    let am_out = (typeof tempRecord.am_out !== "undefined" && tempRecord.am_out) ? tempRecord.am_out : null;
    let pm_in = (typeof tempRecord.pm_in !== "undefined" && tempRecord.pm_in) ? tempRecord.pm_in : null;
    let pm_out = (typeof tempRecord.pm_out !== "undefined" && tempRecord.pm_out) ? tempRecord.pm_out : null;

    let am_start = (typeof tempRecord.am_start !== "undefined" && tempRecord.am_start) ? tempRecord.am_start : null;
    let am_end = (typeof tempRecord.am_end !== "undefined" && tempRecord.am_end) ? tempRecord.am_end : null;
    let pm_start = (typeof tempRecord.pm_start !== "undefined" && tempRecord.pm_start) ? tempRecord.pm_start : null;
    let pm_end = (typeof tempRecord.pm_end !== "undefined" && tempRecord.pm_end) ? tempRecord.pm_end : null;

    let has_perhour = (typeof tempRecord.has_perhour !== "undefined" && parseInt(tempRecord.has_perhour) == 1) ? 1 : 0;
    let adjustment_override = (typeof tempRecord.adjustment_override !== "undefined") ? tempRecord.adjustment_override : 0;

    if ((am_start && am_end) && (am_in && am_out)) {
        const tempAmIn = moment(am_in).format("HH:mm");
        const tempAmEnd = moment(am_end).format("HH:mm");
        const tempAmStart = moment(am_start).format("HH:mm");

        if (moment(tempAmIn).isSameOrAfter(moment(tempAmEnd))) {
            am_late = 0;
        } else {
            if (moment(tempAmIn, "HH:mm").isAfter(moment(tempAmStart, "HH:mm")) && am_in) {
                let _am_in = moment(tempAmIn, 'HH:mm');
                let _am_start = moment(tempAmStart, 'HH:mm');

                if (_am_in.isAfter(_am_start)) {
                    const duration = moment.duration(_am_in.diff(_am_start));
                    am_late = duration.asMinutes();
                }
            }
        }
    }

    if ((pm_start && pm_end) && (pm_in && pm_end)) {
        const tempPmIn = moment(pm_in).format("HH:mm");
        const tempPmEnd = moment(pm_end).format("HH:mm");
        const tempPmStart = moment(pm_start).format("HH:mm");

        if (moment(tempPmIn).isSameOrAfter(moment(tempPmEnd))) {
            pm_late = 0;
        } else {
            if (moment(tempPmIn, "HH:mm").isAfter(moment(tempPmStart, "HH:mm")) && pm_in) {
                let _pm_in = moment(tempPmIn, 'HH:mm');
                let _pm_start = moment(tempPmStart, 'HH:mm');

                if (_pm_in.isAfter(_pm_start)) {
                    const duration = moment.duration(_pm_in.diff(_pm_start));
                    pm_late = duration.asMinutes();
                }
            }
        }
    }

    total_late = am_late + pm_late;

    if (typeof row.is_flex !== "undefined") {
        let l_am_totallate = am_late;
        let l_pm_totallate = pm_late;

        if (parseFloat(am_late) > 30) {
            l_am_totallate = 2 * 60;
            if (parseInt(adjustment_override) === 1) { l_am_totallate = am_late; }
            if (row.is_flex === "1") {
                l_am_totallate = 0;
                if (parseFloat(am_late) > 30) {
                    l_am_totallate = 2 * 60;
                    if (parseInt(adjustment_override) === 1) { l_am_totallate = am_late; }
                }
            }
            if (parseFloat(am_late) >= 60) {
                l_am_totallate = 0;
                if (parseInt(adjustment_override) === 1) { l_am_totallate = am_late; }
            }
        } else {
            if (row.is_flex === "1") {
                l_am_totallate = 0;
            }
        }

        if (parseFloat(pm_late) > 30) {
            l_pm_totallate = 0;
            if (row.is_flex === "1") { l_pm_totallate = 0; }
            if (parseInt(adjustment_override) === 1) { l_pm_totallate = pm_late; }
        } else {
            if (row.is_flex === "1") { l_pm_totallate = 0; }
        }

        total_late = (parseInt(l_am_totallate)) + (parseInt(l_pm_totallate));
    }

    if (parseInt(has_perhour) == 1) { total_late = 0; }
    return total_late;
}

function calc_ut(row) {
    const tempRecord = generateAttendanceShiftRecord(row);
    let am_in = (typeof tempRecord.am_in !== "undefined" && tempRecord.am_in) ? tempRecord.am_in : null;
    let am_out = (typeof tempRecord.am_out !== "undefined" && tempRecord.am_out) ? tempRecord.am_out : null;
    let pm_in = (typeof tempRecord.pm_in !== "undefined" && tempRecord.pm_in) ? tempRecord.pm_in : null;
    let pm_out = (typeof tempRecord.pm_out !== "undefined" && tempRecord.pm_out) ? tempRecord.pm_out : null;

    let am_start = (typeof tempRecord.am_start !== "undefined" && tempRecord.am_start) ? tempRecord.am_start : null;
    let am_end = (typeof tempRecord.am_end !== "undefined" && tempRecord.am_end) ? tempRecord.am_end : null;
    let pm_start = (typeof tempRecord.pm_start !== "undefined" && tempRecord.pm_start) ? tempRecord.pm_start : null;
    let pm_end = (typeof tempRecord.pm_end !== "undefined" && tempRecord.pm_end) ? tempRecord.pm_end : null;

    let has_perhour = (typeof tempRecord.has_perhour !== "undefined" && parseInt(tempRecord.has_perhour) == 1) ? 1 : 0;
    let adjustment_override = (typeof tempRecord.adjustment_override !== "undefined") ? tempRecord.adjustment_override : 0;

    const amExpectedWorkHours = am_start && am_end ? moment.duration(moment(am_end).diff(moment(am_start))).asMinutes() : 0;
    const pmExpectedWorkHours = pm_start && pm_end ? moment.duration(moment(pm_end).diff(moment(pm_start))).asMinutes() : 0;
    const totalExpectedWorkHours = amExpectedWorkHours + pmExpectedWorkHours;

    let am_ut = 0;
    let pm_ut = 0;
    let total_ut = 0;

    /*** late checker ***/
    let am_late = 0;
    let pm_late = 0;
    const tempAmIn = moment(am_in).format("HH:mm");
    const tempAmEnd = moment(am_end).format("HH:mm");
    const tempAmStart = moment(am_start).format("HH:mm");

    if (moment(tempAmIn).isSameOrAfter(moment(tempAmEnd))) {
        am_late = 0;
    } else {
        if (moment(tempAmIn, "HH:mm").isAfter(moment(tempAmStart, "HH:mm")) && am_in) {
            let _am_in = moment(tempAmIn, 'HH:mm');
            let _am_start = moment(tempAmStart, 'HH:mm');

            if (_am_in.isAfter(_am_start)) {
                const duration = moment.duration(_am_in.diff(_am_start));
                am_late = duration.asMinutes();
            }
        }
    }

    if ((pm_start && pm_end) && (pm_in && pm_end)) {
        const tempPmIn = moment(pm_in).format("HH:mm");
        const tempPmEnd = moment(pm_end).format("HH:mm");
        const tempPmStart = moment(pm_start).format("HH:mm");

        if (moment(tempPmIn).isSameOrAfter(moment(tempPmEnd))) {
            pm_late = 0;
        } else {
            if (moment(tempPmIn, "HH:mm").isAfter(moment(tempPmStart, "HH:mm")) && pm_in) {
                let _pm_in = moment(tempPmIn, 'HH:mm');
                let _pm_start = moment(tempPmStart, 'HH:mm');

                if (_pm_in.isAfter(_pm_start)) {
                    const duration = moment.duration(_pm_in.diff(_pm_start));
                    pm_late = duration.asMinutes();
                }
            }
        }
    }
    /*** late checker ***/

    if (moment(am_in, "YYYY-MM-DD HH:mm").isSameOrAfter(moment(am_end, "YYYY-MM-DD HH:mm"))) {
        am_ut = amExpectedWorkHours;
    } else {
        if (am_out && am_end) {
            am_out = moment(am_out);
            am_end = moment(am_end);

            if (am_out.isBefore(am_end)) {
                const duration = moment.duration(am_end.diff(am_out));
                am_ut = duration.asMinutes();
            }
        } else {
            if (am_start !== null && am_end !== null) {
                am_ut = amExpectedWorkHours;
            }
        }
    }
    if (moment(pm_in, "YYYY-MM-DD HH:mm").isSameOrAfter(moment(pm_end, "YYYY-MM-DD HH:mm"))) {
        pm_ut = pmExpectedWorkHours;
    } else {
        if (pm_out && pm_end) {
            pm_out = moment(pm_out);
            pm_end = moment(pm_end);

            if (pm_out.isBefore(pm_end)) {
                const duration = moment.duration(pm_end.diff(pm_out));
                pm_ut = duration.asMinutes();
            }
        } else {
            if ((pm_start && pm_start !== null) && (pm_end && pm_end !== null)) {
                pm_ut = pmExpectedWorkHours;
            }
        }
    }

    /*** late checker ***/
    if (parseFloat(am_late) >= 60 && parseInt(adjustment_override) === 0) {
        am_ut = amExpectedWorkHours;
    }
    if (parseFloat(pm_late) > 30 && parseInt(adjustment_override) === 0) {
        pm_ut = pmExpectedWorkHours;
    }
    /*** late checker ***/
    total_ut = am_ut + pm_ut;
    total_ut = parseFloat(total_ut) > 0 ? total_ut : 0;
    if (parseInt(has_perhour) == 1) { total_ut = 0; }
    return total_ut;
}


function calc_reghr(row) {
    const tempRecord = generateAttendanceShiftRecord(row);
    let am_in = (typeof tempRecord.am_in !== "undefined" && tempRecord.am_in) ? tempRecord.am_in : null;
    let am_out = (typeof tempRecord.am_out !== "undefined" && tempRecord.am_out) ? tempRecord.am_out : null;
    let pm_in = (typeof tempRecord.pm_in !== "undefined" && tempRecord.pm_in) ? tempRecord.pm_in : null;
    let pm_out = (typeof tempRecord.pm_out !== "undefined" && tempRecord.pm_out) ? tempRecord.pm_out : null;

    let am_start = (typeof tempRecord.am_start !== "undefined" && tempRecord.am_start) ? tempRecord.am_start : null;
    let am_end = (typeof tempRecord.am_end !== "undefined" && tempRecord.am_end) ? tempRecord.am_end : null;
    let pm_start = (typeof tempRecord.pm_start !== "undefined" && tempRecord.pm_start) ? tempRecord.pm_start : null;
    let pm_end = (typeof tempRecord.pm_end !== "undefined" && tempRecord.pm_end) ? tempRecord.pm_end : null;

    let has_perhour = (typeof tempRecord.has_perhour !== "undefined" && parseInt(tempRecord.has_perhour) == 1) ? 1 : 0;
    let adjustment_override = (typeof tempRecord.adjustment_override !== "undefined") ? tempRecord.adjustment_override : 0;

    const amExpectedWorkHours = am_start && am_end ? moment.duration(moment(am_end).diff(moment(am_start))).asMinutes() : 0;
    const pmExpectedWorkHours = pm_start && pm_end ? moment.duration(moment(pm_end).diff(moment(pm_start))).asMinutes() : 0;
    const totalExpectedWorkHours = amExpectedWorkHours + pmExpectedWorkHours;

    let amWorkedHours = 0;
    let pmWorkedHours = 0;
    let totalWorkedHours = 0;
    let totalWorkedHoursInMinutes = 0;

    /*** late checker ***/
    let am_late = 0;
    let pm_late = 0;
    const tempAmIn = moment(am_in).format("HH:mm");
    const tempAmEnd = moment(am_end).format("HH:mm");
    const tempAmStart = moment(am_start).format("HH:mm");

    if (moment(tempAmIn).isSameOrAfter(moment(tempAmEnd))) {
        am_late = 0;
    } else {
        if (moment(tempAmIn, "HH:mm").isAfter(moment(tempAmStart, "HH:mm")) && am_in) {
            let _am_in = moment(tempAmIn, 'HH:mm');
            let _am_start = moment(tempAmStart, 'HH:mm');

            if (_am_in.isAfter(_am_start)) {
                const duration = moment.duration(_am_in.diff(_am_start));
                am_late = duration.asMinutes();
            }
        }
    }

    if ((pm_start && pm_end) && (pm_in && pm_end)) {
        const tempPmIn = moment(pm_in).format("HH:mm");
        const tempPmEnd = moment(pm_end).format("HH:mm");
        const tempPmStart = moment(pm_start).format("HH:mm");

        if (moment(tempPmIn).isSameOrAfter(moment(tempPmEnd))) {
            pm_late = 0;
        } else {
            if (moment(tempPmIn, "HH:mm").isAfter(moment(tempPmStart, "HH:mm")) && pm_in) {
                let _pm_in = moment(tempPmIn, 'HH:mm');
                let _pm_start = moment(tempPmStart, 'HH:mm');

                if (_pm_in.isAfter(_pm_start)) {
                    const duration = moment.duration(_pm_in.diff(_pm_start));
                    pm_late = duration.asMinutes();
                }
            }
        }
    }
    /*** late checker ***/

    if (am_start && am_end) {
        if (moment(am_in, "YYYY-MM-DD HH:mm").isSameOrAfter(moment(am_end, "YYYY-MM-DD HH:mm"))) {
            amWorkedHours = 0;
        } else {
            if (am_in && am_out) {
                am_in = moment(am_in);
                am_out = moment(am_out);
                am_start = moment(am_start);
                am_end = moment(am_end);

                const start = am_in.isBefore(am_start) ? am_start : am_in;
                const end = am_out.isAfter(am_end) ? am_end : am_out;

                const duration = moment.duration(end.diff(start));
                amWorkedHours = duration.asMinutes();
            }
        }
    }

    if (pm_start && pm_end) {
        if (moment(pm_in, "YYYY-MM-DD HH:mm").isSameOrAfter(moment(pm_end, "YYYY-MM-DD HH:mm"))) {
            pmWorkedHours = 0;
        } else {

            if (pm_in && pm_out) {
                pm_in = moment(pm_in);
                pm_out = moment(pm_out);
                pm_start = moment(pm_start);
                pm_end = moment(pm_end);

                const start = pm_in.isBefore(pm_start) ? pm_start : pm_in;
                const end = pm_out.isAfter(pm_end) ? pm_end : pm_out;

                const duration = moment.duration(end.diff(start));
                pmWorkedHours = duration.asMinutes();
            }
        }
    }

    /*** late checker ***/
    if (parseFloat(am_late) >= 60 && parseInt(adjustment_override) === 0 && has_perhour == 0) { amWorkedHours = 0; }
    if (parseFloat(pm_late) > 30 && parseInt(adjustment_override) === 0 && has_perhour == 0) { pmWorkedHours = 0; }
    /*** late checker ***/

    totalWorkedHours = amWorkedHours + pmWorkedHours;
    totalWorkedHours = parseFloat(totalWorkedHours) > 0 ? totalWorkedHours : 0;
    totalWorkedHoursInMinutes = totalWorkedHours;

    totalWorkedHours = formatDecimal(totalWorkedHours / 60, 2);

    return totalWorkedHours + "," + totalWorkedHoursInMinutes;
}

function generateAttendanceShiftRecord(row) {
    let tempData = {};
    if (typeof row !== "undefined" && typeof row == "object") {
        const _date = row.date;

        let _tempDate0 = _date;
        let _tempDate1 = _date;
        let _tempDate2 = _date;

        let am_in = row.am_in && row.am_in !== "empty" ? (_date + " " + row.am_in) : null;
        let am_out = row.am_out && row.am_out !== "empty" ? (_date + " " + row.am_out) : null;
        let pm_in = row.pm_in && row.pm_in !== "empty" ? (_date + " " + row.pm_in) : null;
        let pm_out = row.pm_out && row.pm_out !== "empty" ? (_date + " " + row.pm_out) : null;

        let hasNextDay = false;

        if ((row.am_in || row.am_in !== null || row.am_in !== "empty") && (row.am_out || row.am_out !== null || row.am_out !== "empty")) {
            const temp0 = moment(_date + " " + row.am_in, "YYYY-MM-DD HH:mm");
            const temp1 = moment(_date + " " + row.am_out, "YYYY-MM-DD HH:mm");

            if (temp1.unix() < temp0.unix()) {
                const tempDate = moment(_date).add(1, 'd').format("YYYY-MM-DD");
                _tempDate0 = tempDate;
                hasNextDay = true;
            }
        }

        _tempDate0 = _tempDate0 !== _date ? _tempDate0 : _date;
        am_out = row.am_out && row.am_out !== "empty" ? _tempDate0 + " " + row.am_out : null;

        if ((row.am_out || row.am_out !== null || row.am_out !== "empty") && (row.pm_in || row.pm_in !== null || row.pm_in !== "empty")) {
            const temp0 = moment(_date + " " + row.am_out, "YYYY-MM-DD HH:mm");
            const temp1 = moment(_date + " " + row.pm_in, "YYYY-MM-DD HH:mm");
            if (temp1.unix() < temp0.unix() || hasNextDay) {
                const tempDate = moment(_date).add(1, 'd').format("YYYY-MM-DD");
                _tempDate1 = tempDate;
                hasNextDay = true;
            }
        }

        _tempDate1 = _tempDate1 !== _date ? _tempDate1 : _date;
        pm_in = row.pm_in && row.pm_in !== "empty" ? _tempDate1 + " " + row.pm_in : null;

        if ((row.pm_in || row.pm_in !== null || row.pm_in !== "empty") && (row.pm_out || row.pm_out !== null || row.pm_out !== "empty")) {
            const temp0 = moment(_date + " " + row.pm_in, "YYYY-MM-DD HH:mm");
            const temp1 = moment(_date + " " + row.pm_out, "YYYY-MM-DD HH:mm");
            if (temp1.unix() < temp0.unix() || hasNextDay) {
                const tempDate = moment(_date).add(1, 'd').format("YYYY-MM-DD");
                _tempDate2 = tempDate;
                hasNextDay = true;
            }
        }

        _tempDate2 = _tempDate2 !== _date ? _tempDate2 : _date;
        pm_out = row.pm_out && row.pm_out !== "empty" ? _tempDate2 + " " + row.pm_out : null;
        /*** end attendance shift ***/
        /*** start shift schedule ***/
        let _temp_Date0 = _date;
        let _temp_Date1 = _date;
        let _temp_Date2 = _date;
        let hasNextDayShift = false;

        let am_start = !row.am_start || row.am_start == null ? null : _date + " " + row.am_start;
        let am_end = !row.am_end || row.am_end == null ? null : _date + " " + row.am_end;

        if ((row.am_start || row.am_start !== null || row.am_start !== "empty") && (row.am_end || row.am_end !== null || row.am_end !== "empty")) {
            const temp0 = moment(_date + " " + row.am_start, "YYYY-MM-DD HH:mm");
            const temp1 = moment(_date + " " + row.am_end, "YYYY-MM-DD HH:mm");

            if (temp1.unix() < temp0.unix()) {
                const tempDate = moment(_date).add(1, 'd').format("YYYY-MM-DD");
                _temp_Date0 = tempDate;
                hasNextDayShift = true;
            }
        }

        am_end = row.am_end && row.am_end !== "empty" && row.am_end !== null ? _temp_Date0 + " " + row.am_end : null;

        if ((row.am_end || row.am_end !== null || row.am_end !== "empty") && (row.pm_start || row.pm_start !== null || row.pm_start !== "empty")) {
            const temp0 = moment(_date + " " + row.am_end, "YYYY-MM-DD HH:mm");
            const temp1 = moment(_date + " " + row.pm_start, "YYYY-MM-DD HH:mm");
            if (temp1.unix() < temp0.unix() || hasNextDayShift) {
                const tempDate = moment(_date).add(1, 'd').format("YYYY-MM-DD");
                _temp_Date1 = tempDate;
                hasNextDayShift = true;
            }
        }

        _temp_Date1 = _temp_Date1 !== _date ? _temp_Date1 : _date;
        let pm_start = row.pm_start && row.pm_start !== "empty" && row.pm_start !== null ? _temp_Date1 + " " + row.pm_start : null;
        if ((row.pm_start || row.pm_start !== null || row.pm_start !== "empty") && (row.pm_end || row.pm_end !== null || row.pm_end !== "empty")) {
            var temp0 = moment(_date + " " + row.pm_start, "YYYY-MM-DD HH:mm");
            var temp1 = moment(_date + " " + row.pm_end, "YYYY-MM-DD HH:mm");

            if (temp1.unix() < temp0.unix() || hasNextDayShift) {
                var tempDate = moment(_date).add(1, 'd').format("YYYY-MM-DD");
                _temp_Date2 = tempDate;
                hasNextDayShift = true;
            }
        }
        _temp_Date2 = _temp_Date2 !== _date ? _temp_Date2 : _date;
        let pm_end = row.pm_end && row.pm_end !== "empty" && row.pm_end !== null ? _temp_Date2 + " " + row.pm_end : null;
        /*** end shift schedule ***/

        tempData.am_in = am_in;
        tempData.am_out = am_out;
        tempData.pm_in = pm_in;
        tempData.pm_out = pm_out;

        tempData.am_start = am_start;
        tempData.am_end = am_end;
        tempData.pm_start = pm_start;
        tempData.pm_end = pm_end;

        tempData.has_perhour = (typeof row.has_perhour !== "undefined" && parseInt(row.has_perhour) == 1) ? 1 : 0;
        tempData.adjustment_override = (typeof row.adjustment_override !== "undefined") ? row.adjustment_override : 0;
    }

    return tempData;
}

function openTimeAdjustmentDetailsModal(time_adjustment_id) {
    $.ajax({
        url: baseUrl(`gcctime/timesheet/get_time_adjustment_details/${time_adjustment_id}/1`),
        type: 'GET',
        dataType: 'JSON',
        success: function (response) {
            $('.modal-content', timeAdjustmentDetailsContainerModal).empty().append(response.modal);
            $('.mass-actions', timeAdjustmentDetailsContainerModal).remove();
            timeAdjustmentDetailsContainerModal.modal('show');
        }
    });
}

function openMoreDetailsModal(timesheet_id, employee_id, date) {
    $.ajax({
        url: baseUrl(`gcctime/timesheet/get_timesheet_more_details/${timesheet_id}/${employee_id}/${date}`),
        type: 'GET',
        dataType: 'JSON',
        success: function (response) {
            const { overtime, loa_references, to_references, holiday_references } = response;
            $('.modal-content', modalContainer).empty();
            $('.modal-dialog', modalContainer).addClass('modal-lg');
            $('.modal-dialog', modalContainer).css('max-width', '');
            setTimeout(() => {
                $('.modal-content', modalContainer).html(response.modal);
                if(typeof overtime !== 'undefined' && overtime.length > 0) {
                    $('.modal-content', modalContainer).find("#overtime-container").removeClass('m--hide');
                }
                if(typeof loa_references !== 'undefined' && loa_references.length > 0) {
                    $('.modal-content', modalContainer).find("#loa-container").removeClass('m--hide');
                }
                if(typeof to_references !== 'undefined' && to_references.length > 0) {
                    $('.modal-content', modalContainer).find("#travelorder-container").removeClass('m--hide');
                }
                if(typeof holiday_references !== 'undefined' && holiday_references !== null && Object.keys(holiday_references).length > 0) {
                    $('.modal-content', modalContainer).find("#holiday-container").removeClass('m--hide');
                }
            }, 250);
        }
    }).done(function () { modalContainer.modal('show'); });
}

$('#tbl-overtime').on('click', 'tbody .btnUpdate', function () {
    const input = $(this).parent().parent().children('input');
    const defaultvalue = $(this).attr('data-old-value');
    const id = $(this).attr('id').split('-').pop();
    const tdIndex = $(this).closest("td").index();

    if ($(this).hasClass('updateMode')) {
        input.removeAttr('disabled');
        input.attr('data-validation', 'required');
        input.focus();
        $(this).attr('data-original-title', 'Cancel');
    } else {
        input.attr('disabled', '');
        input.removeAttr('data-validation');
        $(this).attr('data-original-title', 'Edit');
        input.val(defaultvalue);
        updateHrsConvert(id, tdIndex);
    }

    $(this).toggleClass('btn-success btn-danger');
    $(this).toggleClass('updateMode cancelMode');
    $('i', this).toggleClass('fa-pencil fa-ban');
});


// START IMPORT FUNCTIONS
$('#file-import')
    .on('change', function () {
        const _this = this;
        const file = this.files[0];
        const filename = file !== undefined ? file.name : 'CHOOSE FILE...';
        $('.custom-file-control', importModal).html(filename);
        setTimeout(() => { $(_this).validate(); }, 250);
    });

function openImportModal(type) {
    let btnText = null;
    let modalTitle = null;
    $("form", importModal).resetForm();
    $('#import-inclusive-dates', importModal).data('daterangepicker').setStartDate(moment());
    $('#import-inclusive-dates', importModal).data('daterangepicker').setEndDate(moment());
    
    if (type === 'attendance') {
        btnText = `Import & Generate`;
        modalTitle = 'Import Attendance';
        $("#device-id", importModal).siblings("label").addClass("required");
        $("#device-id", importModal).prop("disabled", false);
        $("#device-id", importModal).attr("data-validation", "required");
    } else {
        btnText = `Import`;
        modalTitle = 'Import Timesheet';
        $("#device-id", importModal).val("").trigger("change");
        $("#device-id", importModal).siblings("label").removeClass("required");
        $("#device-id", importModal).prop("disabled", true);
        $("#device-id", importModal).removeAttr("data-validation");
    }

    $(".custom-file-control", importModal).text("");
    $('#type', importModal).val(type);
    $('.modal-title', importModal).html(modalTitle);
    $('.btn-import__text', importModal).html(btnText);
    importModal.modal('show');
}

const vmInvalidImport = new Vue({
    el: '#invalid-content',
    data: { rows: {}, count: 0 }
})

$.validate({
    form: $('#frm-timesheet-import-modal'),
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const formData = new FormData($(form)[0]);
        formData.append('csrf_token', _csrf_hash);
        const btnSubmit = $('button[type="submit"]', form);

        $.ajax({
            url: baseUrl(`gcctime/timesheet/import_and_generate_timesheet`),
            data: formData,
            type: 'POST',
            dataType: 'JSON',
            contentType: false,
            processData: false,
            global: false,
            beforeSend: function () {
                btnSubmit.addClass('m-btn--custom m-loader m-loader--light m-loader--left');
                $('#importing-alert-message').fadeIn();
                $(':input', form).prop('disabled', true);
            },
            success: function (response) {
                if (response) {
                    if (response.success) {
                        $('.biometric-list-container .list').empty();
                        $('.no-shifts-container .list').empty();

                        if (response.emp_id_to_generate.length <= 0) {
                            $("#message", noEmployeeBiometricModal)
                                .html("Attendance successfully imported but <span class='m--font-boldest'>no timesheet was generated</span>. Please resolve the issues below.");
                        }

                        if (response.type === 'attendance') {
                            if (parseInt(response.non_existing.length) >= 1 || (parseInt(response.no_shifts.length) >= 1)) {
                                if (parseInt(response.non_existing.length) >= 1) {
                                    response.non_existing.forEach((row) => {
                                        const biometric_id = row.biometric;
                                        const in_employees = parseInt(row.in_employees);
                                        let info_button = ``;
                                        if (in_employees) {
                                            info_button = `<button type="button"
                                                                class="btn btn-sm btn-outline-info m-btn m-btn--outline-2x
                                                                       m-btn--sm m-btn--icon m-btn--icon-only m-btn--pill
                                                                       m--font-boldest2 btn-possible-match-${biometric_id}"
                                                                title="${in_employees} Possible match(es)."
                                                                onclick="openPossibleMatchesModal(${biometric_id}, 'import', ${response.timesheet_imports_id})">
                                                            ${in_employees}
                                                        </button>`;
                                        }

                                        const listTemplate = `
                                                <div class="list__item" style="flex: 0 0 33%;"
                                                     data-biometric="${biometric_id}">
                                                    <div class="list__item__cell">
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-success m-btn m-btn--outline-2x
                                                                       m-btn--sm m-btn--icon m-btn--icon-only m-btn--pill
                                                                       btnNew btn-new-employee-${biometric_id}"
                                                                title="Add Employee"
                                                                onclick="openAddEmployeeModal(${biometric_id}, 'import',  ${response.timesheet_imports_id})">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                        ${info_button}
                                                    </div>
                                                    <div class="list__item__cell list__item__cell--bolder">
                                                        <span class="m-badge m-badge--default m-badge--wide m--font-boldest2">${biometric_id}</span>
                                                    </div>
                                                </div>`;
                                        $('.biometric-list-container .list').append(listTemplate);
                                    });
                                }

                                if (parseInt(response.no_shifts.length) >= 1) {
                                    response.no_shifts.forEach((biometric_id, i) => {
                                        let employee_name;
                                        if (response.no_shifts_name && response.no_shifts_name.length) {
                                            if (response.no_shifts_name[i]) {
                                                employee_name = response.no_shifts_name[i];
                                            }
                                        }
                                        const listTemplate = `
                                                <div class="list__item" style="flex: 0 0 33%;"
                                                     data-biometric="${biometric_id}">
                                                    <div class="list__item__cell">
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-brand m-btn m-btn--outline-2x
                                                                       m-btn--sm m-btn--icon m-btn--icon-only m-btn--pill
                                                                       btnNew btn-add-shift-${biometric_id}"
                                                                title="Add Shift Schedule"
                                                                onclick="openAddShiftModal(${biometric_id}, 'import', ${response.timesheet_imports_id})">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <div class="list__item__cell list__item__cell--bolder">
                                                        <span class="m-badge m-badge--default m-badge--wide m--font-boldest2"
                                                              title="${employee_name}">${biometric_id}</span>
                                                    </div>
                                                </div>`;
                                        $('.no-shifts-container .list').append(listTemplate);
                                    });
                                }

                                $('.modal-title', noEmployeeBiometricModal).html(`<span class="m--font-bold">Import Successful!</span>`);
                                $('#biometric_no_array', noEmployeeBiometricModal).val(JSON.stringify(response.non_existing));
                                $('#no_shifts_array', noEmployeeBiometricModal).val(JSON.stringify(response.no_shifts));
                                $('#timesheet-imports-id', noEmployeeBiometricModal).val(response.timesheet_imports_id);
                                noEmployeeBiometricModal.modal('show');
                            } else {
                                const toast = response.success ? 'success' : 'error';
                                toastr[toast](response.message, response.title, { timeOut: 10000 });
                            }
                        } else {
                            if (parseInt(response.possible_duplicate.length) >= 1) {
                                const dt = $("table", tsPossibleDuplicatesModal)
                                    .DataTable({
                                        dom: "rtlp",
                                        serverSide: false,
                                        destroy: true,
                                        columns: [
                                            { data: "current", },
                                            { data: "changes", },
                                            { data: "actions", },
                                        ],
                                        columnDefs: [
                                            {
                                                targets: [2],
                                                orderable: false,
                                                className: "text-center"
                                            }
                                        ]
                                    });

                                dt.clear().draw();
                                response.possible_duplicate.forEach((row, i) => {
                                    const current = row.current;
                                    const changes = row.changes;

                                    var row_verified = row.current.verified;

                                    let tempChangeAmIn = [];
                                    let tempChangeAmOut = [];
                                    let tempChangePmIn = [];
                                    let tempChangePmOut = [];

                                    if (typeof changes.am_in !== "undefined" && changes.am_in !== null) {
                                        const _tempChangeAmIn = changes.am_in;
                                        tempChangeAmIn = _tempChangeAmIn.split(" ");
                                    }
                                    if (typeof changes.am_out !== "undefined" && changes.am_out !== null) {
                                        const _tempChangeAmOut = changes.am_out;
                                        tempChangeAmOut = _tempChangeAmOut.split(" ");
                                    }
                                    if (typeof changes.pm_in !== "undefined" && changes.pm_in !== null) {
                                        const _tempChangePmIn = changes.pm_in;
                                        tempChangePmIn = _tempChangePmIn.split(" ");
                                    }
                                    if (typeof changes.pm_out !== "undefined" && changes.pm_out !== null) {
                                        const _tempChangePmOut = changes.pm_out;
                                        tempChangePmOut = _tempChangePmOut.split(" ");
                                    }

                                    const currentTemplate = `<div>
                                        <div class="pb-1">
                                            <span class="m--font-boldest">${current.emp_name}</span>
                                        </div>
                                        <div class="">
                                            <span class="m--font-boldest">${moment(current.date).format("MM/DD/YYYY")}</span>
                                        </div>
                                        <div class="d-flex">
                                            <div class="" style="flex: 1;">
                                                <span class="m--font-bolder text-muted">AM IN:</span>
                                                <span class="m--font-bolder">
                                                    ${current.am_in !== null && current.am_out !== null ? moment(current.am_in, "hh:mm:ss").format("hh:mm A") : '---'}
                                                </span>
                                            </div>
                                            <div class="" style="flex: 1;">
                                                <span class="m--font-bolder text-muted">AM OUT:</span>
                                                <span class="m--font-bolder">
                                                    ${current.am_in !== null && current.am_out !== null ? moment(current.am_out, "hh:mm:ss").format("hh:mm A") : '---'}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="" style="flex: 1;">
                                                <span class="m--font-bolder text-muted">PM IN:</span>
                                                <span class="m--font-bolder">
                                                    ${current.pm_in !== null && current.pm_out !== null ? moment(current.pm_in, "hh:mm:ss").format("hh:mm A") : '---'}
                                                </span>
                                            </div>
                                            <div class="" style="flex: 1;">
                                                <span class="m--font-bolder text-muted">PM OUT:</span>
                                                <span class="m--font-bolder">
                                                    ${current.pm_in !== null && current.pm_out !== null ? moment(current.pm_out, "hh:mm:ss").format("hh:mm A") : '---'}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="" style="flex: 1;">
                                                <span class="m--font-bolder text-muted">OT:</span>
                                                <span class="m--font-bolder">
                                                    ${current.total_accredited_ot_hrs}
                                                </span>
                                            </div>
                                            <div class="" style="flex: 1;">
                                                <span class="m--font-bolder text-muted">NDOT:</span>
                                                <span class="m--font-bolder">
                                                    ${current.total_accredited_ndiff_ot_hrs}
                                                </span>
                                            </div>
                                        </div>
                                        </div>`;

                                    const changesTemplate = `<div>
                                        <div class="pb-1">
                                            <span class="m--font-boldest">${changes.emp_name}</span>
                                        </div>
                                        <div class="">
                                            <span class="m--font-boldest">${moment(changes.date).format("MM/DD/YYYY")}</span>
                                        </div>
                                        <div class="d-flex">
                                            <div class="" style="flex: 1;">
                                                <span class="m--font-bolder text-muted">AM IN:</span>
                                                <span class="m--font-bolder">
                                                    ${(tempChangeAmIn[1] !== null && tempChangeAmOut[1] !== null) && (tempChangeAmIn.length > 0 && tempChangeAmOut.length > 0) ? moment(changes.am_in, "hh:mm:ss").format("hh:mm A") : "---"}
                                                </span>
                                            </div>
                                            <div class="" style="flex: 1;">
                                                <span class="m--font-bolder text-muted">AM OUT:</span>
                                                <span class="m--font-bolder">
                                                    ${(tempChangeAmIn[1] !== null && tempChangeAmOut[1] !== null) && (tempChangeAmIn.length > 0 && tempChangeAmOut.length > 0) ? moment(changes.am_out, "hh:mm:ss").format("hh:mm A") : "---"}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="" style="flex: 1;">
                                                <span class="m--font-bolder text-muted">PM IN:</span>
                                                <span class="m--font-bolder">
                                                    ${(tempChangePmIn[1] !== null && tempChangePmOut[1] !== null) && (tempChangePmIn.length > 0 && tempChangePmOut.length > 0) ? moment(changes.pm_in, "hh:mm:ss").format("hh:mm A") : "---"}
                                                </span>
                                            </div>
                                            <div class="" style="flex: 1;">
                                                <span class="m--font-bolder text-muted">PM OUT:</span>
                                                <span class="m--font-bolder">
                                                    ${(tempChangePmIn[1] !== null && tempChangePmOut[1] !== null) && (tempChangePmIn.length > 0 && tempChangePmOut.length > 0) ? moment(changes.pm_out, "hh:mm:ss").format("hh:mm A") : "---"}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="" style="flex: 1;">
                                                <span class="m--font-bolder text-muted">OT:</span>
                                                <span class="m--font-bolder">
                                                    ${changes.total_accredited_ot_hrs}
                                                </span>
                                            </div>
                                            <div class="" style="flex: 1;">
                                                <span class="m--font-bolder text-muted">NDOT:</span>
                                                <span class="m--font-bolder">
                                                    ${changes.total_accredited_ndiff_ot_hrs}
                                                </span>
                                            </div>
                                        </div>
                                        </div>`;

                                    const changesStrData = JSON.stringify(changes);

                                    if (row_verified <= 0) {
                                        dt.row.add({
                                            current: currentTemplate,
                                            changes: changesTemplate,
                                            actions: `<button class="btn btn-success m-btn m-btn--icon m-btn--icon-only m-btn--pill btn-sm"
                                                          data-toggle="m-tooltip"
                                                          data-original-title="Accept Changes"
                                                          data-skin="dark"
                                                          data-delay='{"show": 300}'
                                                          data-changes='${changesStrData}'
                                                          id="btn-accept-${i}"
                                                          onclick="confirmAcceptUpdates(this, ${response.timesheet_imports_id}, ${current.id}, ${i})">
                                                        <i class="fa fa-check"></i>
                                                  </button>`,
                                        }).draw();
                                    }
                                });
                                tsPossibleDuplicatesModal.modal("show");
                            } else {
                                const toast = response.success ? 'success' : 'error';
                                toastr[toast](response.message, response.title, { timeOut: 10000 });
                            }
                        }

                        vmInvalidImport.rows = response.invalid_records;
                        vmInvalidImport.count = response.invalid_count;
                        if(response.invalid_count > 0){ importInvalidModal.modal('show'); }

                        btnSubmit.removeClass('m-btn--custom m-loader m-loader--light m-loader--left');
                        $(':input', form).prop('disabled', false);
                        $('#importing-alert-message').fadeOut();

                        $('#device-id', importModal).val(null);
                        $('.custom-file-control', importModal).html('CHOOSE FILE...');
                        $('#device-id', importModal).val(null).trigger('change');

                        //here
                    } else {
                        const toast = response.success ? 'success' : 'error';
                        toastr[toast](response.message, response.title, { timeOut: 10000 });
                    }
                }

                $(form).resetForm();
                importModal.modal('hide');
            }
        });
        return false;
    }
});

function openAddShiftModal(biometric_id, modal_origin = "import", history_id = null) {
    $("form", addShiftModal).attr("data-personnel_biometric_no", biometric_id);
    $("form", addShiftModal).attr("data-mode", "add-shift");
    $("form", addShiftModal).attr("data-modal_origin", modal_origin);
    $("form", addShiftModal).attr("data-history_id", history_id);

    $(".modal-title", addShiftModal).html("ADD SHIFT SCHEDULE");
    $("#message-info", addShiftModal).html("PLEASE DEFINE SHIFT SCHEDULE TO CONTINUE LINK.");
    $("button[type='submit']", addShiftModal).html("SAVE");

    $("#shift_id", addShiftModal)
        .select2({
            width: "100%",
            placeholder: "SELECT SCHEDULE",
            ajax: {
                url: baseUrl(`gcctime/timesheet/get_shift_schedules`),
                type: 'GET',
                dataType: "JSON",
                delay: 1000,
            },
            dropdownParent: addShiftModal
        });

    addShiftModal.modal("show");
}

function confirmAcceptUpdates(el, timesheet_imports_id, id, rowIdx) {
    const changes = $(el).data("changes");



    $(".modal-title", confirmationModal).html("Delete Confirmation");
    $(".modal-body", confirmationModal).html(`<p class="m--regular-font-size-lg2 m--font-bold">
                                                    Are you sure to accept timesheet changes for
                                                    <u><span class="m--font-boldest">${changes.emp_name} (${moment(changes.date).format("MM/DD/YYYY")})</span></u>?
                                              </p>`);

    $("form", confirmationModal).attr("action", baseUrl(`gcctime/timesheet/accept_import_updates/${id}/${timesheet_imports_id}`));
    $("form", confirmationModal).attr("data-function", 'acceptImportUpdates');
    // $("form", confirmationModal).attr("data-changes", JSON.stringify(changes));
    $("form", confirmationModal).attr("data-prepared", JSON.stringify(changes));
    $("form", confirmationModal).attr("data-row-index", rowIdx);
    $(".btnSave", confirmationModal).html("Yes");
    $(".btnClose", confirmationModal).html("No");
    confirmationModal.modal("show");
}

function acceptImportUpdates(form) {
    // const changes = $(form).data("changes");
    const changes = $(form).data("prepared");
    const rowIdx = $(form).attr("data-row-index");
    const url = $(form).attr("action");
    const tr = $(`#btn-accept-${rowIdx}`).closest("tr");


    $.ajax({
        url,
        type: "POST",
        dataType: "JSON",
        data: {
            changes: changes,
            csrf_token: _csrf_hash
        },
        success: function (response) {
            // alert(JSON.stringify(changes));
            if (response) {
                if (response.success) {
                    $(form).removeData();

                    $(tr).remove();
                }
                toastr[response.toast](response.message, response.title, { timeOut: '10000' });
            }
        }
    });
}

$('#import-inclusive-dates', importModal)
    .daterangepicker({
        buttonClasses: 'm-btn btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',
        autoUpdateInput: true,
        container: $(this, importModal).parent(),
    }, function (start, end) {
        $('#import-inclusive-dates .form-control', importModal)
            .val(start.format('MMM DD, YYYY') + ' / ' + end.format('MMM DD, YYYY'));
    }).on('apply.daterangepicker', function(ev) {
        setTimeout(function() { 
            $(ev.target).validate();
            $("input", ev.target).validate();
        }, 250);
    });

$('#device-id', importModal)
    .select2({
        placeholder: 'CHOOSE A DEVICE',
        width: '100%',
        dropdownParent: importModal
    }).on("select2:select", function (e){
        $(e.target).validate();
    });

function exportAsCsv(arrayStr) {
    const csv_values = JSON.parse(arrayStr).join(',');
    const csvContent = 'data:text/csv;charset=utf-8,' + csv_values;
    const encodedUri = encodeURI(csvContent);
    let link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', 'no_employee_biometric_no.csv');
    document.body.appendChild(link);

    link.click();
}

// ADD EMPLOYEE VIA MODAL FROM SELECTED BIOMETRIC AFTER IMPORT
function openAddEmployeeModal(biometric_id, origin, history_id = null) {
    $.ajax({
        url: baseUrl(`gcctime/timesheet/check_biometric_no/${biometric_id}`),
        type: 'GET',
        dataType: 'JSON',
        global: false,
        success: function (response) {
            const existInPersonnelList = response.personnel_list.length >= 1 ? true : false;

            $('form', newEmployeeModal).attr('data-history-id', history_id);
            $('#existInPersonnelList', newEmployeeModal).val(existInPersonnelList);
            $('#biometricno', newEmployeeModal).val(biometric_id);
            $('#origin', newEmployeeModal).val(origin);
            newEmployeeModal.modal('show');
        }
    });
}

function openPossibleMatchesModal(biometric_id, origin = 'import', history_id = null) {
    possibleMatchesModal.attr("data-biometric_id", biometric_id);
    possibleMatchesModal.attr("data-modal_origin", origin);
    possibleMatchesModal.attr("data-history_id", history_id);

    $(".modal-title", possibleMatchesModal).html(`Possible Matches for Biometric No. <span class="m--font-boldest">${biometric_id}</span>`);
    possibleMatchesModal.modal("show");
}

function openLookUpAndUpdateModal(biometric_id, origin = 'import', history_id = null) {
    lookUpAndUpdateModal.attr("data-biometric_id", biometric_id);
    lookUpAndUpdateModal.attr("data-modal_origin", origin);
    lookUpAndUpdateModal.attr("data-history_id", history_id);

    $("#biometric-no", lookUpAndUpdateModal).html(biometric_id);
    lookUpAndUpdateModal.modal("show");
}

lookUpAndUpdateModal.on("show.bs.modal", function () {
    const biometricno = $(this).attr("data-biometric_id");
    const modal_origin = $(this).attr("data-modal_origin");
    const history_id = $(this).attr("data-history_id");

    const dt = $("table", lookUpAndUpdateModal)
        .DataTable({
            dom: 'rtlp',
            serverSide: true,
            destroy: true,
            ajax: {
                url: baseUrl(`gcctime/timesheet/get_employees_for_look_up_and_update_biometric_no`),
                type: "POST",
                dataType: "JSON",
                data: function (d) {
                    d.csrf_token = _csrf_hash;
                    d.search.value = $("#search", lookUpAndUpdateModal).val();
                }
            },
            autoWidth: false,
            columns: [
                {
                    width: "23%",
                    data: "biometricno",
                    render: function (data) {
                        return `<span class="m--font-boldest">${data}</span>`;
                    }
                },
                {
                    data: "lastname",
                    render: function (data, type, row) {
                        return `<div class="m--font-boldest2">${data}, ${row.firstname}</div>
                                <div class="m--regular-font-size-sm1 m--font-bolder">${row.work_status}</div>
                                <div class="m--regular-font-size-sm1 text-muted">STATUS: ${row.employee_status}</div>`;
                    }
                },
                {
                    width: "7%",
                    data: null,
                    render: function (data, type, row) {
                        // onclick="linkEmployeeToBiometricNo(${row.id}, '${row.biometricno}', ${biometricno}, '${modal_origin}', ${history_id}, ${row.shift_id}, 'link')"
                        return `<button type="button"
                                        class="btn btn-sm btn-outline-brand m-btn--outline-2x m-btn m-btn--icon m-btn--icon-only m-btn--pill"
                                        title="CLICK TO UPDATE EMPLOYEE BIOMETRIC NO."
                                        onclick="updateEmployeeBiometricFromImport(${row.id}, '${biometricno}', '${modal_origin}', ${history_id}, ${row.personnel_id}, ${row.shift_id}, 'look up')">
                                    <i class="fa fa-pencil"></i>
                                </button>`;
                    },
                    orderable: false,
                    className: 'text-center'
                }
            ],
            order: [[1, "ASC"]]
        });

    $("#search", lookUpAndUpdateModal)
        .donetyping(function () {
            dt.ajax.reload();
        });
});

function updateEmployeeBiometricFromImport(emp_id, emp_biometric_no, modal_origin, history_id = null, personnel_id, shift_id, mode = "link", is_flexi = null) {
    if (shift_id && parseInt(shift_id) > 0) {
        $.ajax({
            url: baseUrl(`gcctime/timesheet/update_employee_biometric_no_from_timesheet_imports`),
            type: "POST",
            dataType: "JSON",
            data: {
                csrf_token: _csrf_hash,
                emp_id,
                emp_biometric_no,
                personnel_biometric_no: emp_biometric_no,
                modal_origin,
                history_id,
                shift_id,
                mode,
                is_flexi,
                personnel_id
            },
            success: function (response) {
                if (response) {
                    if (response.success) {
                        const tableRow = $(`#import-row-${history_id}`).attr('data-row');
                        dtImportHistory.row(tableRow).data(response.import_history).draw();
                    }

                    lookUpAndUpdateModal.modal("hide");
                }
            }
        });
    } else {
        $(".modal-title", addShiftModal).html("ADD SHIFT SCHEDULE");
        $("#message-info", addShiftModal).html("PLEASE DEFINE SHIFT SCHEDULE TO CONTINUE LINK.");
        $("form", addShiftModal).attr("data-emp_id", emp_id);
        $("form", addShiftModal).attr("data-emp_biometric_no", emp_biometric_no);
        $("form", addShiftModal).attr("data-personnel_biometric_no", emp_biometric_no);
        $("form", addShiftModal).attr("data-personnel_id", personnel_id);
        $("form", addShiftModal).attr("data-modal_origin", modal_origin);
        $("form", addShiftModal).attr("data-history_id", history_id);
        $("form", addShiftModal).attr("data-mode", mode);
        $("button[type='submit']", addShiftModal).html("SAVE & UPDATE BIOMETRIC NO.");

        $("#shift_id", addShiftModal)
            .select2({
                width: "100%",
                placeholder: "SELECT SCHEDULE",
                ajax: {
                    url: baseUrl(`gcctime/timesheet/get_shift_schedules`),
                    type: 'GET',
                    dataType: "JSON",
                    delay: 1000,
                },
                dropdownParent: addShiftModal
            });

        addShiftModal.modal("show");
    }
}

possibleMatchesModal.on("show.bs.modal", function () {
    const biometricno = $(this).attr("data-biometric_id");
    const modal_origin = $(this).attr("data-modal_origin");
    const history_id = $(this).attr("data-history_id");

    $("table", possibleMatchesModal)
        .DataTable({
            dom: 'rt',
            serverSide: false,
            destroy: true,
            ajax: {
                url: baseUrl(`gcctime/timesheet/get_biometric_possible_matches_from_employees/${biometricno}`),
                type: "GET",
                dataType: "JSON"
            },
            autoWidth: false,
            columns: [
                {
                    width: "25%",
                    data: "biometricno",
                    render: function (data) {
                        return `<span class="m--font-boldest">${data}</span>`;
                    }
                },
                {
                    data: "lastname",
                    render: function (data, type, row) {
                        return `<div class="m--font-boldest2">${data}, ${row.firstname}</div>
                                <div class="m--regular-font-size-sm1 m--font-bolder">${row.work_status}</div>
                                <div class="m--regular-font-size-sm1 text-muted">WORK STATUS: ${row.employee_status}</div>`;
                    }
                },
                {
                    width: "7%",
                    data: null,
                    render: function (data, type, row) {
                        return `<button type="button"
                                        class="btn btn-sm btn-outline-brand m-btn--outline-2x m-btn m-btn--icon m-btn--icon-only m-btn--pill"
                                        title="LINK THIS EMPLOYEE TO BIOMETRIC NO. ${biometricno}"
                                        onclick="linkEmployeeToBiometricNo(${row.id}, '${row.biometricno}', ${biometricno}, '${modal_origin}', ${history_id}, ${row.shift_id}, 'link')">
                                    <i class="fa fa-link"></i>
                                </button>`;
                    },
                    orderable: false,
                    className: 'text-center'
                }
            ],
            order: [[1, "ASC"]]
        });
});

function linkEmployeeToBiometricNo(emp_id, emp_biometric_no, personnel_biometric_no, modal_origin = "import", history_id = null, shift_id = null, mode = "link", is_flexi = null) {
    if (shift_id && parseInt(shift_id) > 0) {
        $.ajax({
            url: baseUrl(`gcctime/timesheet/link_employee_to_biometric_no_in_personnel`),
            type: "POST",
            dataType: "JSON",
            data: {
                csrf_token: _csrf_hash,
                emp_id,
                emp_biometric_no,
                personnel_biometric_no,
                modal_origin,
                history_id,
                shift_id,
                mode,
                is_flexi
            },
            success: function (response) {
                if (response) {
                    if (response.success) {
                        if (modal_origin === "import") {
                            if (mode === "link") {
                                const btnCell = $(`.list__item[data-biometric="${personnel_biometric_no}"] .list__item__cell:first-child`, noEmployeeBiometricModal);
                                btnCell.html(`<i class="m--font-success fa fa-check"></i>`);
                                $(`.btn-possible-matches-${personnel_biometric_no}`).remove();

                                let biometric_no_array = $('#biometric_no_array', noEmployeeBiometricModal).val();
                                biometric_no_array = biometric_no_array ? JSON.parse(biometric_no_array) : null;

                                if (biometric_no_array) {
                                    const index = biometric_no_array.findIndex((row) => parseInt(row.biometric) === parseInt(personnel_biometric_no));
                                    biometric_no_array.splice(index, 1);
                                    $('#biometric_no_array', noEmployeeBiometricModal).val(JSON.stringify(biometric_no_array));
                                }
                            } else {
                                const btnCell = $(`.btn-add-shift-${personnel_biometric_no}`).parent();
                                btnCell.html(`<i class="m--font-success fa fa-check"></i>`);

                                let no_shifts_array = $('#no_shifts_array', noEmployeeBiometricModal).val();
                                no_shifts_array = no_shifts_array ? JSON.parse(no_shifts_array) : null;

                                if (no_shifts_array) {
                                    const index = no_shifts_array.findIndex((biometric) => parseInt(biometric) === parseInt(personnel_biometric_no));
                                    no_shifts_array.splice(index, 1);
                                    $('#no_shifts_array', noEmployeeBiometricModal).val(JSON.stringify(no_shifts_array));
                                }
                            }
                        } else {
                            const tableRow = $(`#import-row-${history_id}`).attr('data-row');
                            dtImportHistory.row(tableRow).data(response.import_history).draw();
                        }
                    }

                    possibleMatchesModal.modal("hide");
                }
            }
        });
    } else {
        $(".modal-title", addShiftModal).html("ADD SHIFT SCHEDULE");
        $("#message-info", addShiftModal).html("PLEASE DEFINE SHIFT SCHEDULE TO CONTINUE LINK.");
        $("form", addShiftModal).attr("data-emp_id", emp_id);
        $("form", addShiftModal).attr("data-emp_biometric_no", emp_biometric_no);
        $("form", addShiftModal).attr("data-personnel_biometric_no", personnel_biometric_no);
        $("form", addShiftModal).attr("data-modal_origin", modal_origin);
        $("form", addShiftModal).attr("data-history_id", history_id);
        $("form", addShiftModal).attr("data-mode", "link");
        $("button[type='submit']", addShiftModal).html("SAVE & CONTINUE LINK");

        $("#shift_id", addShiftModal)
            .select2({
                width: "100%",
                placeholder: "SELECT SCHEDULE",
                ajax: {
                    url: baseUrl(`gcctime/timesheet/get_shift_schedules`),
                    type: 'GET',
                    dataType: "JSON",
                    delay: 1000,
                },
                dropdownParent: addShiftModal
            });

        addShiftModal.modal("show");
    }
}

$.validate({
    form: $("#frm-add-shift-modal"),
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const emp_id = $(form, addShiftModal).attr("data-emp_id");
        const emp_biometric_no = $(form, addShiftModal).attr("data-emp_biometric_no");
        const personnel_biometric_no = $(form, addShiftModal).attr("data-personnel_biometric_no");
        const personnel_id = $(form, addShiftModal).attr("data-personnel_id");
        const modal_origin = $(form, addShiftModal).attr("data-modal_origin");
        const history_id = $(form, addShiftModal).attr("data-history_id");
        const mode = $(form, addShiftModal).attr("data-mode");
        const shift_id = $("#shift_id", form).val();
        const is_flexi = $("#add-shift-is_flexi", form)[0].checked ? "on" : null;

        if (mode === 'link' || mode === 'add-shift') {
            linkEmployeeToBiometricNo(emp_id, emp_biometric_no, personnel_biometric_no, modal_origin, history_id, shift_id, mode, is_flexi);
        } else if (mode === 'look up') {
            updateEmployeeBiometricFromImport(emp_id, emp_biometric_no, modal_origin, history_id, personnel_id, shift_id, mode, is_flexi);
        }

        $("#add-shift-is_flexi-label", addShiftModal).html("NO");
        $("#add-shift-is_flexi-label", addShiftModal).removeClass('m--font-info m--font-boldest');
        $("#add-shift-is_flexi-label", addShiftModal).addClass('m--font-metal');

        $("#shift_id", form).empty();
        $(form, addShiftModal).resetForm();

        addShiftModal.modal("hide");

        return false;
    }
});

// SAVE NEW EMPLOYEE & NEW EMPLOYEE MODEL FUNCTIONS
$.validate({
    form: $('#frm-new-employee-modal'),
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const biometricno = $('#biometricno', newEmployeeModal).val();
        const origin = $('#origin', newEmployeeModal).val();
        const history_id = $(form).attr('data-history-id') || null;

        let data = new FormData($(form)[0]);
        data.append('csrf_token', _csrf_hash);
        data.append('history_id', history_id);
        data.append('origin', origin);

        if (employeeImage) {
            urltoFile(employeeImage.base64data, employeeImage.name)
                .then(function (file) {
                    data.append('image', file);
                    save();
                });
        } else {
            save();
        }

        function save() {
            $.ajax({
                url: baseUrl(`gcctime/timesheet/save_employee`),
                type: 'POST',
                dataType: 'JSON',
                data,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (origin === 'import') {
                        if (response) {
                            const emp_id = response.data;
                            const biometricno = response.biometricno;
                            const toast = response.success ? 'success' : 'warning';

                            const btnCell = $(`.list__item[data-biometric="${biometricno}"] .list__item__cell:first-child`, noEmployeeBiometricModal);
                            btnCell.html(`<i class="m--font-success fa fa-check"></i>`);
                            $(`.btn-possible-matches-${biometricno}`).remove();

                            let biometric_no_array = $('#biometric_no_array', noEmployeeBiometricModal).val();
                            biometric_no_array = biometric_no_array ? JSON.parse(biometric_no_array) : null;

                            if (biometric_no_array) {
                                const index = biometric_no_array.findIndex((row) => parseInt(row.biometric) === parseInt(biometricno));
                                biometric_no_array.splice(index, 1);
                                $('#biometric_no_array', noEmployeeBiometricModal).val(JSON.stringify(biometric_no_array));
                            }

                            toastr[toast](response.message, response.title, { timeOut: '10000' });
                        }
                    } else {
                        if (response) {
                            const toast = response.success ? 'success' : 'warning';
                            toastr[toast](response.message, response.title, { timeOut: '10000' });

                            const tableRow = $(`#import-row-${history_id}`).attr('data-row');
                            dtImportHistory.row(tableRow).data(response.import_history).draw();
                        }
                    }

                    if (response) {
                        $('#image--holder', newEmployeeModal).attr('src', baseUrl('assets/images/profile/no_image.jpg'));
                        $('#company, #department, #position, #work-status, #civil_stat, #classification, #work-status', newEmployeeModal).val('').trigger('change');
                        $('#shift_id_new_employee', newEmployeeModal).empty();

                        $("#is_flexi-label", newEmployeeModal).html("NO");
                        $("#is_flexi-label", newEmployeeModal).removeClass('m--font-info m--font-boldest');
                        $("#is_flexi-label", newEmployeeModal).addClass('m--font-metal');

                        $(form).resetForm();
                        newEmployeeModal.modal('hide');
                    }
                }
            });
        }

        return false;
    }
});

$('#dob', newEmployeeModal)
    .datepicker({
        todayHighlight: true,
        orientation: 'bottom left',
        autoclose: true,
        templates: {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        },
        format: 'yyyy-mm-dd',
        container: newEmployeeModal
    });

$('#is-laborer', newEmployeeModal)
    .on('input', function () {
        const checked = $(this)[0].checked;
        if (checked) {
            const option = $('#position').find('option:contains("LABORER")');
            const value = $(option)[0].value;
            $('#position').val(value).trigger('change');
        } else {
            $('#position').val(null).trigger('change');
        }
    });

$('#company, #department, #position, #work-status, #civil_stat', newEmployeeModal)
    .select2({
        width: '100%',
        placeholder: 'LIST OF OPTIONS',
        dropdownParent: newEmployeeModal,
        allowClear: true
    });

$("#is_flexi", newEmployeeModal)
    .on('input', function () {
        const checked = $(this)[0].checked;
        if (checked) {
            $("#is_flexi-label", newEmployeeModal).html("YES");
            $("#is_flexi-label", newEmployeeModal).removeClass('m--font-metal');
            $("#is_flexi-label", newEmployeeModal).addClass('m--font-info m--font-boldest');
        } else {
            $("#is_flexi-label", newEmployeeModal).html("NO");
            $("#is_flexi-label", newEmployeeModal).removeClass('m--font-info m--font-boldest');
            $("#is_flexi-label", newEmployeeModal).addClass('m--font-metal');
        }
    });

$("#add-shift-is_flexi", addShiftModal)
    .on('input', function () {
        const checked = $(this)[0].checked;
        if (checked) {
            $("#add-shift-is_flexi-label", addShiftModal).html("YES");
            $("#add-shift-is_flexi-label", addShiftModal).removeClass('m--font-metal');
            $("#add-shift-is_flexi-label", addShiftModal).addClass('m--font-info m--font-boldest');
        } else {
            $("#add-shift-is_flexi-label", addShiftModal).html("NO");
            $("#add-shift-is_flexi-label", addShiftModal).removeClass('m--font-info m--font-boldest');
            $("#add-shift-is_flexi-label", addShiftModal).addClass('m--font-metal');
        }
    });

$('#classification', newEmployeeModal)
    .select2({
        width: '100%',
        placeholder: 'SELECT CLASSIFICATION',
        dropdownParent: newEmployeeModal
    })
    .on('select2:select', function (e) {
        const data = e.params.data;
        const workStatus = $('#work-status');

        workStatus.find('option').remove();
        if (data.text === 'ACTIVE') {
            workStatus.append(activeStatusOptions);
        } else {
            workStatus.append(inactiveStatusOptions);
        }

        workStatus.removeAttr('disabled');
    });

$("#shift_id_new_employee", newEmployeeModal)
    .select2({
        width: "100%",
        placeholder: "SELECT SCHEDULE",
        ajax: {
            url: baseUrl(`gcctime/timesheet/get_shift_schedules`),
            type: 'GET',
            dataType: "JSON",
            delay: 1000,
        },
        dropdownParent: $("#shift_id_new_employee", newEmployeeModal).parent()
    });

// SAVE BIOMETRIC NOS. NOT FOUND FROM EMPLOYEE LIST/HRIS
noEmployeeBiometricModal.on('hide.bs.modal', function (e) {
    const biometricNoArray = JSON.parse($('#biometric_no_array', noEmployeeBiometricModal).val());
    const noShiftsArray = JSON.parse($('#no_shifts_array', noEmployeeBiometricModal).val());
    const timesheetImportsId = $('#timesheet-imports-id', noEmployeeBiometricModal).val();

    $.ajax({
        url: baseUrl(`gcctime/timesheet/save_no_employee_biometric_no/${timesheetImportsId}`),
        type: 'POST',
        dataType: 'JSON',
        data: {
            csrf_token: _csrf_hash,
            no_employee_biometric_no: biometricNoArray,
            noShiftsArray
        },
        global: false,
        success: function (response) {
            // on success update
        }
    });
})

// MODAL IMPORT HISTORY LIST
importHistoryModal.on('shown.bs.modal', function (e) {
    const table = $('#import-history-modal__list');
    dtImportHistory = table.DataTable({
        dom: '<"row"<"col-12" rt>><"row"<"col-6" l><"col-6" p>>',
        serverSide: true,
        destroy: true,
        ajax: {
            url: baseUrl(`gcctime/timesheet/get_import_history`),
            type: 'POST',
            dataType: 'JSON',
            data: function (_data) {
                _data.csrf_token = _csrf_hash;
            }
        },
        columns: [
            {
                width: '15%',
                data: 'filename',
                render: function (data, type, row, meta) {
                    return `<span id="import-row-${row.id}" data-row="${meta.row}">${data}</span>`;
                }
            },
            {
                width: '18%',
                data: 'start_date',
                render: function (data, type, row) {
                    return moment(row.start_date).format('MMM. DD, YYYY') + ' - ' + moment(row.end_date).format('MMM. DD, YYYY');
                }
            },
            {
                data: 'biometric_no',
                render: function (data, type, row, meta) {
                    let template = ``;

                    if (data) {
                        if (data.length) {
                            data.forEach((biometric) => {
                                template += biometric;
                            });
                        }
                    }

                    return template ? template : `---`;
                }
            },
            {
                data: 'no_shifts',
                render: function (data) {
                    let template = ``;

                    if (data) {
                        if (data.length) {
                            data.forEach((biometric) => {
                                template += biometric;
                            });
                        }
                    }

                    return template ? template : `---`;
                }
            },
            {
                width: '18%',
                data: 'created_at',
                render: function (data, type, row) {
                    return `<div>${row.remarks}</div>
                            <div class="m--regular-font-size-sm1">${row.creator}</div>
                            <div class="m--regular-font-size-sm2 text-muted">${moment(data).format('lll')}</div>`;
                }
            },
            {
                data: "resolved",
                render: function (data) {
                    return parseInt(data) >= 1 ?
                        `<span class="m-badge m-badge--success m-badge--wide m--font-boldest">Yes</span>` :
                        `<span class="m-badge m-badge--danger m-badge--wide m--font-boldest">No</span>`;
                },
                orderable: false
            }
        ],
        autoWidth: false,
        order: [[4, 'desc']],
    });
});
// END MODAL IMPORT HISTORY LIST

// END IMPORT FUNCTIONS

function formatDecimal(value, decimal) {
    const diff = Math.ceil(value) - Math.floor(value);
    return parseFloat(diff) > 0 ? parseFloat(value).toFixed(decimal) : value;
}

$('input[name="inclusive_filter"]')
    .on("input", function () {
        dtTimesheet.ajax.reload();
    });

$("#excluded-employees-list-modal")
    .on("shown.bs.modal", function () {
        dtExcludedEmployees = $("table", this).DataTable({
            dom: '<"row"<"col-12" rt>><"row"<"col-6" l><"col-6" p>>',
            serverSide: true,
            destroy: true,
            ajax: {
                url: baseUrl('gcctime/timesheet/get_excluded_employee_list'),
                type: 'POST',
                dataType: 'JSON',
                data: function (_data) {
                    _data.csrf_token = _csrf_hash;
                    _data.search.value = $("#search", $("#excluded-employees-list-modal")).val();
                },
                global: false
            },
            columns: [
                {
                    data: null,
                    width: "40px",
                    orderable: false,
                    render: function (data, type, row) {
                        return `<label class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                                           <input type="checkbox" name="selected[]" value="${row.id}"
                                                  class="cb-row-${row.id}"><span></span>
                                      </label>`;
                    },
                    className: "text-center",
                },
                {
                    width: "70px",
                    data: "pic_url",
                    render: function (data) {
                        return `<div class="table-avatar"><img src="${data}" alt="profile-image"></div>`;
                    },
                    orderable: false,
                    className: "text-center"
                },
                {
                    width: "30%",
                    data: "employee_name",
                    render: function (data, type, row, meta) {
                        return `<div class="m--font-bolder" id="row-${row.id}" data-row="${meta.row}">
                                <div class="mb-1">${data}</div>
                                <div class="m--regular-font-size-sm1 text-muted">${row.position}</div>
                                <div class="m--regular-font-size-sm1 text-muted">${row.company}</div>
                            </div>`;
                    }
                },
                {
                    data: "reason"
                },
                {
                    width: "25%",
                    data: "_created_by",
                    render: function (data, type, row, meta) {
                        return `<div class="mb-1">${data}</div>
                            <div class="m--regular-font-size-sm1 text-muted">
                                ${moment(row.created_at).format("lll")}
                            </div>`;
                    }
                },
                {
                    width: "8%",
                    data: null,
                    render: function (data, type, row, meta) {
                        let buttons = ``;
                        if (_currentActions.includes('delete')) {
                            buttons += `<button type="button" onclick="openModal('confirmation', '${row.employee_name}', ${row.id})"
                                            class="btn btn-default btn-sm m-btn m-btn--pill
                                                   m-btn--icon m-btn--icon-only m-btn--hover-danger
                                                   mr-2
                                                   btnDelete"
                                            data-toggle="m-tooltip"
                                            data-original-title="Delete"
                                            data-skin="dark"
                                            data-delay='{"show": 300}'>
                                        <i class="fa fa-trash"></i>
                                    </button>`;
                        }

                        if (_currentActions.includes('edit')) {
                            buttons += `<button type="button" onclick="openModal('edit', '${row.employee_name}', ${row.id})"
                                            class="btn btn-default btn-sm m-btn m-btn--pill
                                                   m-btn--icon m-btn--icon-only m-btn--hover-info
                                                   btnEdit"
                                            data-toggle="m-tooltip"
                                            data-original-title="Edit Reason"
                                            data-skin="dark"
                                            data-delay='{"show": 300}'>
                                        <i class="fa fa-pencil"></i>
                                    </button>`;
                        }

                        return buttons;
                    },
                    orderable: false,
                    className: "text-center"
                },
            ],
            autoWidth: false,
            order: [[2, 'ASC']],
        });

        $("#search", this)
            .donetyping(function () {
                dtExcludedEmployees.ajax.reload();
            });
    });

generateManuallyModal
    .on("show.bs.modal", function (e) {
        const tempMinDate = new Date(moment().subtract(1, 'month').startOf('month'));
        const tempMaxDate = new Date(moment());

        let tempNextDate = tempMinDate;

        $('#employees', generateManuallyModal)
            .select2({
                width: '100%',
                dropdownParent: generateManuallyModal,
                ajax: {
                    url: function () {
                        return baseUrl('gcctime/timesheet/get_employees_for_filter/1/0/') + getUrl()
                    },
                    delay: 1000,
                    global: false,
                    dataType: 'JSON',
                    type: 'GET',
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

        $("#date-start", generateManuallyModal)
            .datepicker({
                startDate: tempMinDate,
                endDate: tempMaxDate,
                autoclose: true,
                todayHighlight: true,
                format: "M. dd, yyyy",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                },
                container: generateManuallyModal
            })
            .on('changeDate', function (ev) {
                onDateSelected(this);
                if(ev.date){ 
                    tempNextDate = new Date(ev.date); 
                    $("#date-end", generateManuallyModal)
                    .datepicker("setStartDate", tempNextDate);
                }
            });

            $("#date-end", generateManuallyModal)
                .datepicker({
                    startDate: tempNextDate,
                    endDate: tempMaxDate,
                    autoclose: true,
                    todayHighlight: true,
                    format: "M. dd, yyyy",
                    templates: {
                        leftArrow: '<i class="la la-angle-left"></i>',
                        rightArrow: '<i class="la la-angle-right"></i>'
                    },
                    container: generateManuallyModal
                })
                .on('changeDate', function (ev) {
                    onDateSelected(this);
                });
    });

function getUrl() {
    return $("#cb-newly")[0].checked ? 1 : 0;
}

$.validate({
    form: $("#frm-generate-manually"),
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const url = $(form).attr("action");
        const type = $(form).attr("method");
        const data = new FormData($(form)[0]);
        data.append("csrf_token", _csrf_hash);
        const btnSubmit = $("button[type='submit']", form);
        const tempEmployeeContainer = $(form).find("select#employees");
        if(typeof tempEmployeeContainer !== "undefined" && !tempEmployeeContainer.serialize() && tempEmployeeContainer.val()){
            data.append('grouped_employees', tempEmployeeContainer.val());
        }
        
        $.ajax({
            url, data, type,
            dataType: "JSON",
            processData: false,
            contentType: false,
            global: false,
            beforeSend: function () {
                btnSubmit.addClass('m-btn--custom m-loader m-loader--light m-loader--left');
                btnSubmit.html("Please wait...");
                $(':input', form).prop('disabled', true);
            },
            success: function (response) {
                btnSubmit.removeClass('m-btn--custom m-loader m-loader--light m-loader--left');
                btnSubmit.html("Generate");
                $(':input', form).prop('disabled', false);

                if (response.length <= 0) {
                    toastr.success("Timesheet was generated from the selected parameters.", "Done Generating!", { timeOut: 10000 });
                    $("#employees", form).val([]).trigger('change');
                    $("#date-start", form).datepicker("clearDates");
                    $("#date-end", form).datepicker("clearDates");
                    $(form).resetForm();
                    $("#generate-manually-modal").modal("hide");
                } else {
                    response.forEach((row, i) => {
                        setTimeout(() => {
                            toastr.error(row.message, row.title, { timeOut: 10000 });
                        }, (i * 300));
                    });
                }
            }
        });

        return false;
    }
});

function onDateSelected(element) {
    $(element).siblings(".help-block").remove();
    $(".form-control", element).removeClass("error").addClass("valid");
    $(".form-control", element).css("border", "1px solid rgba(0, 0, 0, 0.15)");
}

function openModal(modal, employee_name, id) {
    if (modal === 'confirmation') {
        $(".modal-title", confirmationModal).html("Delete Confirmation");
        $(".modal-body", confirmationModal).html(`<p class="m--regular-font-size-lg2 m--font-bold">
                                          Are you sure to delete <u><span class="m--font-boldest">${employee_name}</span></u> from exclusion list?
                                       </p>`);
        $("form", confirmationModal).attr("action", baseUrl(`gcctime/timesheet/delete_employee_from_exclusion/${id}`));
        $("form", confirmationModal).attr("data-function", 'removeFromExcluded');
        $(".btnSave", confirmationModal).html("Yes");
        $(".btnClose", confirmationModal).html("No");
        confirmationModal.modal("show");
    } else {
        $("form", editExcludedEmployeeModal).attr("action", baseUrl(`gcctime/timesheet/update_excluded_employee/${id}`));

        $.ajax({
            url: baseUrl(`gcctime/timesheet/get_excluded_employee_detail/${id}`),
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                const option = new Option(data.employee_name, data.emp_id, false, true);
                $('#employee-list', editExcludedEmployeeModal).append(option);
                $("#exclusion-reason", editExcludedEmployeeModal).val(data.reason);
                editExcludedEmployeeModal.modal("show");
            }
        });
    }
}

function removeFromExcluded(form) {
    const url = $(form).attr("action");
    const id = url.split("/").pop();

    $.ajax({
        url,
        dataType: "JSON",
        type: "GET",
        processData: false,
        contentType: false,
        success: function (response) {
            if (response) {
                const toast = response.success ? "success" : "error";
                const count = parseInt(response.count);
                if (count <= 0) {
                    $("#excluded-employees").hide();
                } else {
                    $("#excluded-employees a").html(response.count_message);
                }

                $("#frm-filter").trigger("submit");

                toastr[toast](response.message, response.title, { timeOut: 10000 });
            }

            const tr = $(`#row-${id}`).closest("tr");
            tr.remove();
            const tbody = tr.parent();

            if (parseInt($("tr", tbody).length) <= 0) {
                dtExcludedEmployees.ajax.reload();
            }

            $("form", confirmationModal).removeAttr("action");
        }
    });
}

$('#employee-list', addExcludedEmployeeModal).select2({
    width: '100%',
    ajax: {
        delay: 1000,
        global: false,
        url: baseUrl('gcctime/timesheet/get_employees_for_filter/0'),
        dataType: 'JSON',
        type: 'GET'
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

$('#employee-list', editExcludedEmployeeModal).select2({
    width: '100%',
    ajax: {
        delay: 1000,
        global: false,
        url: baseUrl('gcctime/timesheet/get_employees_for_filter/0'),
        dataType: 'JSON',
        type: 'GET'
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

$.validate({
    form: $("#frm-add-excluded-employee-from-timesheet"),
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const submit = $('button[type="submit"]', $(form));
        const formData = new FormData($(form)[0]);
        formData.append("csrf_token", _csrf_hash);

        $.ajax({
            url: baseUrl(`gcctime/timesheet/add_excluded_employee_from_timesheet`),
            data: formData,
            type: "POST",
            dataType: "JSON",
            processData: false,
            contentType: false,
            beforeSend: function () {
                submit.addClass(`m-btn--custom m-loader m-loader--light m-loader--left`);
            },
            success: function (response) {
                const excludedEmployeesEl = $("#excluded-employees");
                if (response) {
                    const toast = response.success ? "success" : "error";
                    const count = parseInt(response.count);
                    if (count <= 0) {
                        excludedEmployeesEl.hide();
                    } else {
                        if (excludedEmployeesEl.hasClass("m--hide")) {
                            excludedEmployeesEl.removeClass("m--hide");
                        }
                        $("a", excludedEmployeesEl).html(response.count_message);
                    }

                    toastr[toast](response.message, response.title, { timeOut: 10000 });

                    $(form).resetForm();
                    $("#employee-list", addExcludedEmployeeModal).val([]).trigger('change');
                    addExcludedEmployeeModal.modal("hide");
                    dtExcludedEmployees.ajax.reload();

                    $("#frm-filter").trigger("submit");
                }

                submit.removeClass(`m-btn--custom m-loader m-loader--light m-loader--left`);
            }
        });

        return false;
    }
});

$.validate({
    form: $("#frm-edit-excluded-employee-from-timesheet"),
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const url = $(form).attr("action");
        const submit = $('button[type="submit"]', $(form));
        const formData = new FormData($(form)[0]);
        formData.append("csrf_token", _csrf_hash);
        const id = url.split("/").pop();
        const row = $("#row-" + id).attr("data-row");

        $.ajax({
            url,
            data: formData,
            type: "POST",
            dataType: "JSON",
            processData: false,
            contentType: false,
            beforeSend: function () {
                submit.addClass(`m-btn--custom m-loader m-loader--light m-loader--left`);
            },
            success: function (response) {
                if (response) {
                    toastr[response.toast](response.message, response.title, { timeOut: 10000 });

                    if (response.toast !== 'warning') {
                        dtExcludedEmployees.row(row).data(response.data).draw();
                        editExcludedEmployeeModal.modal("hide");
                    }

                    $("#frm-filter").trigger("submit");
                }

                submit.removeClass(`m-btn--custom m-loader m-loader--light m-loader--left`);
            }
        });

        return false;
    }
});

function exportAs(type) {
    dropdown.removeClass();
    dropdown.addClass("fa fa-spinner fa-spin");
    dropdown.css({ right: 0, left: 0 });

    setTimeout(() => {
        switch (type) {
            case "excel":
                dtTimesheet.button(".buttons-excel").trigger();
                dropdown.removeClass("fa fa-spinner fa-spin").addClass("la la-external-link");
                dropdown.css({ top: "50%", left: "50%" });
                break;
            case "pdf":
                dtTimesheet.button(".buttons-pdf").trigger();
                dropdown.removeClass("fa fa-spinner fa-spin").addClass("la la-external-link");
                dropdown.css({ top: "50%", left: "50%" });
                break;
        }
    }, 150);
}

function printTimesheet(el) {
    $("i", el).removeClass();
    $("i", el).addClass("fa fa-spinner fa-spin");
    $("i", el).css({ right: 0, left: 0 });

    setTimeout(() => {
        dtTimesheet.button(".buttons-print").trigger();
        $("i", el).removeClass("fa fa-spinner fa-spin").addClass("fa fa-print");
        $("i", el).css({ top: "50%", left: "50%" });
    }, 150);
}

function downloadTimesheetExcelTemplate() {
    $.ajax({
        url: baseUrl("gcctime/timesheet/download_timesheet_excel_template"),
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            const a = document.createElement("a");
            a.href = data.file;
            a.download = "TIMESHEET_TEMPLATE.xlsx";
            document.body.appendChild(a);
            a.click()
        }
    });
}

$(document)
    .on('show.bs.modal', '.modal', function () {
        /*var zIndex = Math.max.apply(null, Array.prototype.map.call(document.querySelectorAll('*'), function (el) {
            return +el.style.zIndex;
        })) + 200;*/

        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex - 1);

        setTimeout(function () {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 2).addClass('modal-stack');
        }, 0);
    });

$(document).on('hidden.bs.modal', '.modal', function () {
    $('.modal:visible').length && $(document.body).addClass('modal-open');
});

$("#cb-select-all-excluded")
    .on('change', function (e) {
        const checkedValue = e.target.checked;
        $('tbody input[type=\'checkbox\']', tblExcludedEmployees).prop('checked', checkedValue);

        if (checkedValue) {
            $('#btn-mass-delete').removeAttr('disabled');
        } else {
            $('#btn-mass-delete').attr('disabled', 'true');
        }
    });

$(tblExcludedEmployees)
    .on('change', 'tbody input[type=\'checkbox\']', function () {
        const el = this;
        const id = $(el).val();
        const checkedValue = $(el)[0].checked;

        $(`.cb-row-${id}`).prop('checked', checkedValue);
        checkCbSelectAllExcluded();
    });

function checkCbSelectAllExcluded() {
    const cbCount = $('tbody input[type=\'checkbox\']', tblExcludedEmployees).length;
    const checkedCbCount = $('tbody input[type=\'checkbox\']:checked', tblExcludedEmployees).length;

    if (parseInt(checkedCbCount) >= 1) {
        $('#btn-mass-delete').removeAttr('disabled');
    } else {
        $('#btn-mass-delete').attr('disabled', 'true');
    }

    $("#cb-select-all-excluded").prop('checked', (parseInt(cbCount) === parseInt(checkedCbCount) && parseInt(checkedCbCount) >= 1));
}

function massDelete(form = null) {
    const checkedCbCount = $('tbody input[type=\'checkbox\']:checked', tblExcludedEmployees).length;
    if (parseInt(checkedCbCount) <= 0) {
        $('.modal-title', alertModal).html(`<span class="m--font-danger m--font-bolder m--regular-font-size-lg3">Oops! Unable to Delete.</span>`);
        $('.modal-body', alertModal).html(`<p class="m-0 m--regular-font-size-lg1 m--font-bolder">Please select/check at least one(1) record.</p>`);
        alertModal.modal('show');
    } else {
        const selectedCheckboxes = dtExcludedEmployees.rows().nodes().to$().find('input[type="checkbox"]:checked');
        let cbIdArrays = [];
        $.each(selectedCheckboxes, function (i, cb) {
            cbIdArrays.push($(cb).val());
        });

        $.ajax({
            url: baseUrl('gcctime/timesheet/delete_employee_from_exclusion/0/1'),
            type: 'post',
            dataType: 'JSON',
            data: {
                id: cbIdArrays,
                csrf_token: _csrf_hash,
            },
            success: function (response) {
                $.each(selectedCheckboxes, function (i, cb) {
                    const tr = $(cb).closest('tr');
                    tr.remove();
                });

                if ($("tbody tr", tblExcludedEmployees).length <= 0) {
                    dtExcludedEmployees.ajax.reload();
                }

                $("#cb-select-all-excluded").prop('checked', false);

                if (response) {
                    const toast = response.success ? "success" : "error";
                    const count = parseInt(response.count);
                    if (count <= 0) {
                        $("#excluded-employees").hide();
                    } else {
                        $("#excluded-employees a").html(response.count_message);
                    }

                    $("#frm-filter").trigger("submit");
                    toastr[toast](response.message, response.title, { timeOut: 10000 });
                }

                $("form", confirmationModal).removeAttr("action");
            }
        });
    }
}

$("#time-manual-overtime-entry-modal #date").datepicker({
    format: "yyyy-mm-dd",
    todayBtn: "linked",
    clearBtn: true,
    todayHighlight: true,
    autoclose: true,
    orientation: "bottom left",
    templates: {
        leftArrow: '<i class="la la-angle-left"></i>',
        rightArrow: '<i class="la la-angle-right"></i>'
    }
});

const resetFilter = function (event) {
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        const select2 = form.find("#employees, #payroll_group, #company");
        if (typeof select2 !== "undefined" && select2.length > 0) {
            $.each(select2, function (i, v) {
                const multi = $(v)[0].multiple;
                if (multi) {
                    $(v).val([])
                        .trigger("change")
                        .prop("disabled", false);
                } else {
                    $(v).val("")
                        .trigger("change");
                }
            });
        }
    }
}

const confirmRestDay = function (e, date, has_shift, id, tsId, dtRowIndex) {
    Swal.fire({
        icon : 'question',
        title : 'Rest Day',
        html: 'Are you sure you want to tag this date as `<b>Rest Day</b>`?',
        input: "textarea",
        inputLabel: "Reason for tagging the day as rest day.",
        inputValidator: (result) => {
            return !result && "Reason is required!";
        },
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
    }).then((result) => {
        if (result.isConfirmed && typeof result.value != undefined && result.value) {
            $.ajax({
                url: baseUrl('gcctime/timesheet/tag_date_restday'),
                type: 'post',
                data: {
                    csrf_token: _csrf_hash,
                    date,
                    has_shift,
                    id,
                    timesheetId: tsId,
                    reason : result.value
                },
                dataType: 'json',
                success: function(response) {
                    const rowData = response.row;
                    if (typeof tsId != "undefined" && tsId) {
                        const scrub_status = parseInt(rowData.scrub_status);
                        const verified = parseInt(rowData.verified);
                        const hasTO = parseInt(rowData.has_TO);
                        const hasLOA = parseInt(rowData.has_LOA);
                        const hasWholeDayLoa = parseInt(rowData.has_whole_day_LOA);
    
                        dtTimesheet.row(dtRowIndex).data(rowData).draw();
                        const rowEl = dtTimesheet.row(dtRowIndex).node();
                        let oddEvenClass = $(rowEl).hasClass("odd") ? "odd" : "even";
                        $(rowEl)
                            .removeClass()
                            .addClass(oddEvenClass);
    
                        let currentRowClass = null;
    
                        if (scrub_status === 1 && verified === 0) {
                            currentRowClass = "lacking lacking--contrast";
                        } else if (scrub_status === 2 && verified === 0) {
                            currentRowClass = "multiple";
                        } else {
                            $(rowEl).hasClass("lacking lacking--contrast") && $(rowEl).removeClass("lacking lacking--contrast");
                            $(rowEl).hasClass("multiple") && $(rowEl).removeClass("multiple");
                        }
    
                        if ((parseInt(rowData.has_shift) === 0 && (verified === 0 || !verified))) {
                            currentRowClass = "no-shift";
                        } else {
                            $(rowEl).hasClass("no-shift") && $(rowEl).removeClass("no-shift");
                        }
    
                        if (!rowData.id && parseInt(rowData.has_shift) === 1) {
                            if ((hasLOA >= 1 && hasWholeDayLoa === 1)) { currentRowClass = "absent absent--contrast"; }
                            else if (hasLOA >= 1 && hasWholeDayLoa <= 0) { currentRowClass = "lacking lacking--contrast"; }
                            else { currentRowClass = "absent absent--contrast"; }
    
                            if (hasTO >= 1) { currentRowClass = "lacking lacking--contrast"; }
                        }
    
                        if (currentRowClass) { $(rowEl).addClass(currentRowClass); }
                    } else {
                        dtTimesheet.ajax.reload(null, false);
                    }

                    if (response.state) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Rest Day',
                            html: 'Successfully tagged the day as `<b>Rest Day</b>`!'
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Rest Day',
                            html: 'Failed to tag the day as `<b>Rest Day</b>`!'
                        })
                    }
                }
            })
        }
    })
}

const undoRestDay = function (e, date, has_shift, id, tsId, dtRowIndex) {
    Swal.fire({
        icon : 'question',
        title : 'Undo Rest Day',
        html: 'Are you sure you want to `<b>UNDO</b>` rest day for this date?',
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        allowOutsideClick: false,
        input: "textarea",
        inputLabel: "Reason for undo rest day.",
        inputValidator: (result) => {
            return !result && "Reason is required!";
        },
        showLoaderOnConfirm: true,
    }).then((result) => {
        if (result.isConfirmed && typeof result.value != undefined && result.value) {
            $.ajax({
                url: baseUrl('gcctime/timesheet/undo_restday'),
                type: 'post',
                data: {
                    csrf_token: _csrf_hash,
                    date,
                    has_shift,
                    id,
                    timesheetId: tsId,
                    reason: result.value
                },
                dataType: 'json',
                success: function(response) {
                    const rowData = response.row;

                    if (typeof tsId != "undefined" && tsId) {
                        const scrub_status = parseInt(rowData.scrub_status);
                        const verified = parseInt(rowData.verified);
                        const hasTO = parseInt(rowData.has_TO);
                        const hasLOA = parseInt(rowData.has_LOA);
                        const hasWholeDayLoa = parseInt(rowData.has_whole_day_LOA);
    
                        dtTimesheet.row(dtRowIndex).data(rowData).draw();
                        const rowEl = dtTimesheet.row(dtRowIndex).node();
                        let oddEvenClass = $(rowEl).hasClass("odd") ? "odd" : "even";
                        $(rowEl)
                            .removeClass()
                            .addClass(oddEvenClass);
    
                        let currentRowClass = null;
    
                        if (scrub_status === 1 && verified === 0) {
                            currentRowClass = "lacking lacking--contrast";
                        } else if (scrub_status === 2 && verified === 0) {
                            currentRowClass = "multiple";
                        } else {
                            $(rowEl).hasClass("lacking lacking--contrast") && $(rowEl).removeClass("lacking lacking--contrast");
                            $(rowEl).hasClass("multiple") && $(rowEl).removeClass("multiple");
                        }
    
                        if ((parseInt(rowData.has_shift) === 0 && (verified === 0 || !verified))) {
                            currentRowClass = "no-shift";
                        } else {
                            $(rowEl).hasClass("no-shift") && $(rowEl).removeClass("no-shift");
                        }
    
                        if (!rowData.id && parseInt(rowData.has_shift) === 1) {
                            if ((hasLOA >= 1 && hasWholeDayLoa === 1)) { currentRowClass = "absent absent--contrast"; }
                            else if (hasLOA >= 1 && hasWholeDayLoa <= 0) { currentRowClass = "lacking lacking--contrast"; }
                            else { currentRowClass = "absent absent--contrast"; }
    
                            if (hasTO >= 1) { currentRowClass = "lacking lacking--contrast"; }
                        }
    
                        if (currentRowClass) { $(rowEl).addClass(currentRowClass); }
                    } else {
                        dtTimesheet.ajax.reload(null, false);
                    }

                    if (response.state) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Undo Rest Day',
                            html: 'Successfully `<b>UNDO</b>` rest day for this date!'
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Undo Rest Day',
                            html: 'Failed to `<b>UNDO</b>` rest day for this date!'
                        })
                    }
                }
            })
        }
    })
}