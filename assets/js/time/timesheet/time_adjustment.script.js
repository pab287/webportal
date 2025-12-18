const tblTimeAdjustments = $('#tbl-time-adjustments');
const cbSelectAll = $('#cb-select-all');
const confirmModalWithRemarks = $('#timesheet-confirmation-modal-with-remarks');
const alertModal = $('#alert-modal');
const editTimeAdjustmentModal = $('#edit-time-adjustment-modal');
const timeManualEntryModal = $('#edit-time-adjustment-time-manual-entry-modal');
const timeManualOvertimeEntryModal = $('#time-manual-overtime-entry-modal');
const containerModal = $('#container-modal');
const timeAdjustmentsFilterModal = $('#time-adjustments-filter-modal');
let globalRequestedBy;
let employees = null;
let company = null;
let date = null;

let dtTimeAdjustments;

// momentjs function convert minutes to hour format
/* const h = mins / 60 | 0, m = mins % 60 | 0;
    return moment.utc().hours(h).minutes(m).format("hh:mm A"); */

toastr.options = {
    timeOut: 10000,
    positionClass: 'toast-top-right toast-opacity-1',
    closeButton: true
};

$(document).tooltip({
    selector: '[data-toggle=\'m-tooltip\']',
    container: 'body'
});

$(document).on('show.bs.modal', '.modal', function () {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function () {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

$(document).on('hidden.bs.modal', '.modal', function () {
    $('.modal:visible').length && $(document.body).addClass('modal-open');
});

$(document).ready(function () {
    $("#filter-payroll_group").select2({
        placeholder: 'Select',
        width: '100%',
        allowClear: true,
        minimumInputLength: 1,
        dropdownParent: $("#time-adjustments-filter-modal"),
        ajax: {
            url: siteUrl("gcctime/timesheet/select_payroll_group"),
            dataType: "json",
            type: 'get',
            delay: 250,
            global: false,
            data: function (params) {
                params.company_id = $("form#frm-time-adjustments-filter select#filter-company").val();
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
            const tempEmployeeSelector = $("form#frm-time-adjustments-filter select#filter-employees");
            if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                tempEmployeeSelector.empty();
                $.each(data.employees, function (ii, vv) {
                    var tempOption = new Option(vv.text, vv.id, true, true);
                    tempEmployeeSelector.append(tempOption);
                });
                tempEmployeeSelector.prop("disabled", true);
            }
        }
    }).on("select2:unselect", function (e) {
        const tempEmployeeSelector = $("form#frm-time-adjustments-filter select#filter-employees");
        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
            tempEmployeeSelector.prop("disabled", false);
        }
    });
});

$('.cb-statuses-container input[type=\'checkbox\']')
    .on('input', function () {
        dtTimeAdjustments.ajax.reload();
    });

dtTimeAdjustments = tblTimeAdjustments.DataTable({
    dom: '<\'row\'<\'col-12\' rt>><\'row\'<\'col-6\' l><\'col-6\' p>>',
    serverSide: true,
    destroy: true,
    ajax: {
        url: baseUrl('gcctime/timesheet/get_time_adjustments_request'),
        type: 'POST',
        dataType: 'JSON',
        data: function (_data) {
            let statuses = [];
            $('.cb-statuses-container input[type=\'checkbox\']:checked')
                .map(function () {
                    statuses.push($(this).val());
                });

            _data.csrf_token = _csrf_hash;
            _data.status = statuses;
            _data.employees = employees;
            _data.company = company;
            _data.date = date;
        }
    },
    columns: [
        { data: 'employee_name', visible: false },
        {
            width: '4%',
            data: null,
            className: 'text-center',
            render: function (data, type, row) {
                if (parseInt(row.status) == 3) {
                    return `<i class="fa fa-square m--font-metal" style="font-size: 20px;"></i>`;
                } else {
                    return `<label class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                    <input type="checkbox" name="selected[]" value="${row.time_adjustment_id}"
                        id="cb${row.time_adjustment_id}"><span></span>
                    </label>`;
                }
            }
        }, {
            data: null,
            className: 'text-center',
            render: function (data, type, row) {
                switch (parseInt(row.status)) {
                    case 1:
                        return `<i class="fa fa-check m--font-success" style="font-size: 20px;"
                            data-toggle="m-tooltip" data-original-title="Approved" data-skin="dark"></i>`;
                    case 2:
                        return `<div class="status-indicator status-indicator--${row.status}"
                            data-toggle="m-tooltip" data-original-title="Declined" data-skin="dark"></div>`;
                    case 3:
                        return `<div class="status-indicator status-indicator--${row.status}"
                            data-toggle="m-tooltip" data-original-title="Cancelled" data-skin="dark"></div>`;
                    default:
                        return `<div class="status-indicator status-indicator--${row.status}"
                            data-toggle="m-tooltip" data-original-title="Pending" data-skin="dark"></div>`;
                }
            },
        }, {
            width: '10%',
            data: 'date',
            className: 'text-center',
            render: function (data, type, row) {
                let str = `<span class="m--font-bolder clickable-date"
                                 onclick="openTimeAdjustmentDetailsModal(${row.time_adjustment_id})">${moment(data).format('MM/DD/YYYY')}</span>`;

                if (row.loa.length >= 1) {
                    str += `<i class="fa fa-calendar ml-2" style="cursor: pointer;"
                               data-toggle="m-tooltip" data-original-title="with LOA" data-skin="dark"
                               onclick="openTimeAdjustmentDetailsModal(${row.time_adjustment_id})"></i>`;
                }

                if (row.travel_order.length >= 1) {
                    str += `<i class="fa fa-car ml-2" style="cursor: pointer;"
                               data-toggle="m-tooltip" data-original-title="with Travel Order" data-skin="dark"
                               onclick="openTimeAdjustmentDetailsModal(${row.time_adjustment_id})"></i>`;
                }

                return str;
            }
        },
        {
            width: '4%',
            data: 'weekday',
            className: 'text-center',
            render: function (data, type, row) {
                return `<div class="m--font-bolder">${data.substr(0, 3)}</div>`;
            }
        },
        {
            width: '7%',
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
            width: '7%',
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
            width: '7%',
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
            width: '7%',
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
            width: '7%',
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
            width: '7%',
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
            width: '7%',
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
            width: '6%',
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
            width: '6%', // LATE
            data: null,
            className: 'text-center',
            render: function (data, type, row) {
                var total_late = 0;
                total_late = calc_late(row);
                return `<span class="${total_late > 0 ? 'm--font-boldest' : 'm--font-bolder'}">${total_late}</span>`;
            }
        },
        {
            width: '6%', // UT
            data: null,
            className: 'text-center',
            render: function (data, type, row) {
                var total_ut = 0;
                total_ut = calc_ut(row);
                return `<span class="${total_ut > 0 ? 'm--font-boldest' : 'm--font-bolder'}">${total_ut}</span>`;
            }
        },
        {
            width: '6%',
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
            width: '6%',
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
                let adjTotalOTHrs_inMinutes = formatDecimal(adjTotalOTHrs * 60, 2);

                let manualOtRegHrs = (typeof row.mn_ot_reg_hrs !== "undefined" && row.mn_ot_reg_hrs && parseFloat(row.mn_ot_reg_hrs) > 0) ? parseFloat(row.mn_ot_reg_hrs) : 0;
                let manualOtNdiffHrs = (typeof row.mn_ot_ndiff_hrs !== "undefined" && row.mn_ot_ndiff_hrs && parseFloat(row.mn_ot_ndiff_hrs) > 0) ? parseFloat(row.mn_ot_ndiff_hrs) : 0;
                let manualTotalOTHrs = manualOtRegHrs + manualOtNdiffHrs;
                let hasManualOtHours = row.has_manual_overtime;

                let tooltipTemplate = ``;
                let highlightClass = ``;

                if (row.has_overtime_request) {
                    highlightClass = `m--font-danger`;
                }

                if (hasManualOtHours) {
                    adjRegOTHrs = manualOtRegHrs;
                    adjNDiffOTHrs = manualOtNdiffHrs;
                    adjTotalOTHrs = manualTotalOTHrs;
                    adjTotalOTHrs_inMinutes = formatDecimal(adjTotalOTHrs * 60, 2);
                }

                tooltipTemplate = `<div class='text-left'>
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
            width: '5%',
            data: null,
            className: 'text-center',
            render: function (data, type, row, meta) {
                const status = parseInt(row.status);
                const approvalButtonHiddenClass = status !== 0 ? 'm--hide' : '';
                let approvalActions = '';
                if (_currentActions.includes('approve_action')) {
                    approvalActions += `<li class="m-nav__separator m-nav__separator--fit edit-button mt-2 mb-2 ${status === 0 ? '' : 'm--hide'}"></li>
                    <li class="m-nav__item approval-buttons ${approvalButtonHiddenClass}">
                        <a href="javascript:void(0)" class="m-nav__link btnApprove_action"
                        onclick="openConfirmationModal(false, 1, ${row.time_adjustment_id})">
                            <i class="m-nav__link-icon fa fa-thumbs-o-up"></i>
                            <span class="m-nav__link-text" style="font-weight: 500;">
                                Approve
                            </span>
                        </a>
                    </li>
                    <li class="m-nav__item approval-buttons ${approvalButtonHiddenClass}">
                        <a href="javascript:void(0)" class="m-nav__link btnApprove_action"
                        onclick="openConfirmationModal(false, 2, ${row.time_adjustment_id})">
                            <i class="m-nav__link-icon fa fa-thumbs-o-down"></i>
                            <span class="m-nav__link-text" style="font-weight: 500;">
                                Decline
                            </span>
                        </a>
                    </li>`;
                }

                if (_currentActions.includes('approve_action') || _currentActions.includes('cancel_action')) {
                    approvalActions += `<li class="m-nav__item approval-buttons ${approvalButtonHiddenClass}">
                        <a href="javascript:void(0)" class="m-nav__link btnCancel_action"
                        onclick="openConfirmationModal(false, 3, ${row.time_adjustment_id})">
                            <i class="m-nav__link-icon fa fa-ban"></i>
                            <span class="m-nav__link-text" style="font-weight: 500;">
                                Cancel
                            </span>
                        </a>
                    </li>`;
                }

                const template = `<div class="m-dropdown m-dropdown--inline m-dropdown--align-right m-dropdown--small"
                                       data-dropdown-toggle="click" aria-expanded="true">
                                       <a href="javascript:void(0)" class="m-dropdown__toggle btn m-btn--icon m-btn--icon-only btn-sm m-btn--pill"
                                          id="ellipses-menu-${row.time_adjustment_id}" data-row="${meta.row}"
                                          data-toggle="m-tooltip" data-original-title="More Options" data-skin="dark"
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

                                                            <li class="m-nav__item edit-button ${status === 0 && _currentActions.includes('edit') ? '' : 'm--hide'}">
                                                                <a href="javascript:void(0)" class="m-nav__link" onclick="openEditTimeAdjustmentModal(${row.time_adjustment_id})">
                                                                    <i class="m-nav__link-icon fa fa-pencil"></i>
                                                                    <span class="m-nav__link-text">Edit</span>
                                                                </a>
                                                            </li>

                                                            <li class="m-nav__item details-button">
                                                                <a href="javascript:void(0)" class="m-nav__link"
                                                                   onclick="openTimeAdjustmentDetailsModal(${row.time_adjustment_id})">
                                                                    <i class="m-nav__link-icon fa fa-info-circle"></i>
                                                                    <span class="m-nav__link-text">Details</span>
                                                                </a>
                                                            </li>

                                                            ${approvalActions}
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                       </div>
                                  </div>`;

                return template;
            }
        }
    ],
    ordering: false,
    pageLength: 15,
    autoWidth: false,
    lengthMenu: [[15, 25, 50, 100, 200, -1], [15, 25, 50, 100, 200, 'All']],
    drawCallback: function (settings) {
        const api = this.api();
        const rows = api.rows({ page: 'current' }).nodes();
        let last = null;
        const colspan = 17;

        api.column(0, { page: 'current' })
            .data()
            .each(function (group, i) {
                if (last !== group) {
                    $(rows).eq(i).before(
                        '<tr class="group group--light"><td colspan="' + colspan + '">' + group + '</td></tr>'
                    );

                    last = group;
                }
            });
    },
});

if (!_currentActions.includes("approve_action")) {
    dtTimeAdjustments.column(1).visible(false);
}

cbSelectAll.on('change', function (e) {
    const checkedValue = e.target.checked;
    $('tbody input[type=\'checkbox\']', tblTimeAdjustments).prop('checked', checkedValue);

    if (checkedValue) {
        $('.mass-actions > button').removeAttr('disabled');
    } else {
        $('.mass-actions > button').attr('disabled', 'true');
    }
});


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
            var temp0 = moment(_date + " " + row.am_in);
            var temp1 = moment(_date + " " + row.am_out);

            if (temp1.unix() < temp0.unix()) {
                var tempDate = moment(_date).add(1, 'd').format("YYYY-MM-DD");
                _tempDate0 = tempDate;
                hasNextDay = true;
            }
        }

        _tempDate0 = _tempDate0 !== _date ? _tempDate0 : _date;
        am_out = row.am_out && row.am_out !== "empty" ? _tempDate0 + " " + row.am_out : null;

        if ((row.am_out || row.am_out !== null || row.am_out !== "empty") && (row.pm_in || row.pm_in !== null || row.pm_in !== "empty")) {
            const temp0 = moment(_date + " " + row.am_out);
            const temp1 = moment(_date + " " + row.pm_in);
            if (temp1.unix() < temp0.unix() || hasNextDay) {
                const tempDate = moment(_date).add(1, 'd').format("YYYY-MM-DD");
                _tempDate1 = tempDate;
                hasNextDay = true;
            }
        }

        _tempDate1 = _tempDate1 !== _date ? _tempDate1 : _date;
        pm_in = row.pm_in && row.pm_in !== "empty" ? _tempDate1 + " " + row.pm_in : null;

        if ((row.pm_in || row.pm_in !== null || row.pm_in !== "empty") && (row.pm_out || row.pm_out !== null || row.pm_out !== "empty")) {
            const temp0 = moment(_date + " " + row.pm_in);
            const temp1 = moment(_date + " " + row.pm_out);
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
            const temp0 = moment(_date + " " + row.am_start);
            const temp1 = moment(_date + " " + row.am_end);

            if (temp1.unix() < temp0.unix()) {
                const tempDate = moment(_date).add(1, 'd').format("YYYY-MM-DD");
                _temp_Date0 = tempDate;
                hasNextDayShift = true;
            }
        }

        am_end = row.am_end && row.am_end !== "empty" && row.am_end !== null ? _temp_Date0 + " " + row.am_end : null;

        if ((row.am_end || row.am_end !== null || row.am_end !== "empty") && (row.pm_start || row.pm_start !== null || row.pm_start !== "empty")) {
            const temp0 = moment(_date + " " + row.am_end);
            const temp1 = moment(_date + " " + row.pm_start);
            if (temp1.unix() < temp0.unix() || hasNextDayShift) {
                const tempDate = moment(_date).add(1, 'd').format("YYYY-MM-DD");
                _temp_Date1 = tempDate;
                hasNextDayShift = true;
            }
        }

        _temp_Date1 = _temp_Date1 !== _date ? _temp_Date1 : _date;
        let pm_start = row.pm_start && row.pm_start !== "empty" && row.pm_start !== null ? _temp_Date1 + " " + row.pm_start : null;
        if ((row.pm_start || row.pm_start !== null || row.pm_start !== "empty") && (row.pm_end || row.pm_end !== null || row.pm_end !== "empty")) {
            var temp0 = moment(_date + " " + row.pm_start);
            var temp1 = moment(_date + " " + row.pm_end);

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

function formatDecimal(value, decimal) {
    const diff = Math.ceil(value) - Math.floor(value);
    return parseFloat(diff) > 0 ? parseFloat(value).toFixed(decimal) : value;
}

function checkCbSelectAll() {
    const cbCount = $('tbody input[type=\'checkbox\']', tblTimeAdjustments).length;
    const checkedCbCount = $('tbody input[type=\'checkbox\']:checked', tblTimeAdjustments).length;

    if (parseInt(checkedCbCount) >= 1) {
        $('.mass-actions > button').removeAttr('disabled');
    } else {
        $('.mass-actions > button').attr('disabled', 'true');
    }

    cbSelectAll.prop('checked', (parseInt(cbCount) === parseInt(checkedCbCount) && parseInt(checkedCbCount) >= 1));
}

/*tblTimeAdjustments
    .on('draw.dt', function () {
        const pageInfo = dtTimeAdjustments.page.info();
        checkCbSelectAll();
    });*/

tblTimeAdjustments
    .on('change', 'tbody input[type=\'checkbox\']', function () {
        checkCbSelectAll();
    });

function openConfirmationModal(multiple = false, status, id = null, modalParent = null) {
    let title = null, message = null;
    const selected = $('tbody input[type=\'checkbox\']:checked', tblTimeAdjustments);
    let _id = [];

    switch (parseInt(status)) {
        case 1:
            title = `<span class="m--font-bolder m--regular-font-size-lg3">APPROVE CONFIRMATION</span>`;
            message = `<p class="m--regular-font-size-lg2 m--font-bolder">ARE YOU SURE TO <span class="m--font-success">APPROVE</span> SELECTED REQUEST(S)?</p>`;
            break;
        case 2:
            title = `<span class="m--font-bolder m--regular-font-size-lg3">DECLINE CONFIRMATION</span>`;
            message = `<p class="m--regular-font-size-lg2 m--font-bolder">ARE YOU SURE TO <span class="m--font-danger">DECLINE</span> SELECTED REQUEST(S)?</p>`;
            break;
        case 3:
            title = `<span class="m--font-bolder m--regular-font-size-lg3">CANCEL CONFIRMATION</span>`;
            message = `<p class="m--regular-font-size-lg2 m--font-bolder">ARE YOU SURE TO <span>CANCEL</span> SELECTED REQUEST(S)?</p>`;
            break;
        default:
            title = `<span class="m--font-bolder m--regular-font-size-lg3">UNDO CURRENT STATUS</span>`;
            message = `<p class="m--regular-font-size-lg2 m--font-bolder">ARE YOU SURE TO <span>UNDO</span> SELECTED REQUEST(S) CURRENT STATUS?</p>`;
            break;
    }

    if (multiple) {
        $('tbody input[type=\'checkbox\']:checked', tblTimeAdjustments)
            .each(function () {
                _id.push($(this).val());
            });
    } else {
        _id.push(id);
    }

    if (_id.length <= 0) {
        $('.modal-title', alertModal).html(`<span class="m--font-danger m--font-bolder m--regular-font-size-lg3">Oops! Unable to Approve.</span>`);
        $('.modal-body', alertModal).html(`<p class="m-0 m--regular-font-size-lg1 m--font-bolder">Please select/check at least one(1) record.</p>`);
        alertModal.modal('show');
        return;
    }

    $('#id', confirmModalWithRemarks).val(_id);

    $('#status', confirmModalWithRemarks).val(status);
    $('.modal-title', confirmModalWithRemarks).html(title);
    $('.modal-body > .message', confirmModalWithRemarks).html(message);
    $('.btnSave', confirmModalWithRemarks).html('Yes');
    $('.btnClose', confirmModalWithRemarks).html('No');

    $('#modal-parent', confirmModalWithRemarks).val(modalParent);

    confirmModalWithRemarks.modal('show');
}

$.validate({
    form: $('#frm-timesheet-confirmation-modal-with-remarks'),
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const data = $(form).serializeArray();
        const button = $('button[type=\'submit\']', form);
        data.push({ name: 'csrf_token', value: _csrf_hash });

        const modalParent = $('#modal-parent', form).val();

        $.ajax({
            url: baseUrl('gcctime/timesheet/confirm_time_adjustment_request'),
            type: 'POST',
            dataType: 'JSON',
            data,
            beforeSend: function () {
                button.addClass(`m-btn--custom m-loader m-loader--light m-loader--left`);
            },
            success: function (response) {

                button.removeClass(`m-btn--custom m-loader m-loader--light m-loader--left`);

                if (response) {
                    const toast = response.success ? 'success' : 'error';
                    toastr[toast](response.message, response.title, { timeOut: 10000 });
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
                                dtTimeAdjustments.ajax.reload();
                            }

                            $('#tbl-timesheet input[type=checkbox]').prop('checked', false);
                        }
                    });
                }

                if (response.data) {
                    const status = response.data.status;
                    response.data.id.forEach((_id) => {
                        const tr = $(`#cb${_id}`).closest('tr');
                        const checkboxCol = $(tr).find('td').eq(0);
                        const checkboxStatus = $(tr).find('td').eq(1);

                        let statusStr = null;
                        let isCancelled = false;
                        switch (status) {
                            case 1:
                                statusStr = `<i class="fa fa-check m--font-success" style="font-size: 20px;"
                                   data-toggle="m-tooltip" data-original-title="Approved" data-skin="dark"></i>`;
                                break;
                            case 2:
                                statusStr = `<div class="status-indicator status-indicator--${status}"
                                     data-toggle="m-tooltip" data-original-title="Declined" data-skin="dark"></div>`;
                                break;
                            case 3:
                                statusStr = `<div class="status-indicator status-indicator--${status}"
                                 data-toggle="m-tooltip" data-original-title="Cancelled" data-skin="dark"></div>`;
                                isCancelled = true;
                                break;
                            default:
                                statusStr = `<div class="status-indicator status-indicator--${status}"
                                 data-toggle="m-tooltip" data-original-title="Pending" data-skin="dark"></div>`;
                                break;
                        }

                        checkboxStatus.empty().append(statusStr);
                        if (isCancelled) {
                            checkboxCol.empty().append('<i class="fa fa-square m--font-metal" style="font-size: 20px;"></i>');
                        }
                        $('.approval-buttons').addClass('m--hide');
                        $('.edit-button').addClass('m--hide');
                        $('.details-button').removeClass('m--hide');
                    });
                    dtTimeAdjustments.ajax.reload(null, false);
                }

                if (modalParent) {
                    $(modalParent).modal('hide');
                }

                checkCbSelectAll();
                $(form).resetForm();
                confirmModalWithRemarks.modal('hide');
            }
        });

        return false;
    }
});

// start edit modal functions
let timeRecord = { am_in: null, am_out: null, pm_in: null, pm_out: null };
let am_in_obj = { modified: 0, manual: 0, value: null, prev_value: null };
let am_out_obj = { modified: 0, manual: 0, value: null, prev_value: null };
let pm_in_obj = { modified: 0, manual: 0, value: null, prev_value: null };
let pm_out_obj = { modified: 0, manual: 0, value: null, prev_value: null };

var vmTempCreateAdjustment = new Vue({
    el: "#temp-create-adjustment-content",
    data: { overtime: {}, override_adjustment: false }
});

function openEditTimeAdjustmentModal(time_adjustment_id) {
    $.ajax({
        url: baseUrl('gcctime/timesheet/get_time_adjustment_details/' + time_adjustment_id),
        type: 'GET',
        dataType: 'JSON',
        success: function (response) {
            vmTempCreateAdjustment.overtime = Object.assign({});
            initEditTimeAdjustmentModal(response);
        }
    });
}

function initEditTimeAdjustmentModal(data) {
    const row = data.row;
    const attendance = data.attendance;
    const loa_list = data.loa_list;
    const travel_order_list = data.travel_order_list;
    const overtime = data.overtime;
    const overtimeContainer = $('#overtime-container', editTimeAdjustmentModal);
    const tblOvertimeBody = $('#tbl-overtime tbody', editTimeAdjustmentModal);

    if (typeof row.has_manual_overtime !== "undefined" && row.has_manual_overtime == true) {
        let tempOvertime = {
            has_manual_overtime: row.has_manual_overtime,
            ot_in: row.overtime_in,
            ot_out: row.overtime_out,
            reg_ot: row.regular_hrs,
            ndiff_ot: row.ndiff_hrs,
            requested_by: row.requested_by,
            requested_by_name: row.requestor_name,
            purpose: row.purpose,
        };
        vmTempCreateAdjustment.overtime = Object.assign({}, tempOvertime);
    }
    vmTempCreateAdjustment.override_adjustment = parseInt(row.adjustment_override);

    $('#employee-name', editTimeAdjustmentModal).html(row.employee_name);
    $('#record-date', editTimeAdjustmentModal).html(moment(row.date).format('MM/DD/YYYY, ddd'));

    var shiftAmStart = (row.am_start && row.am_start !== null) ? row.am_start : null;
    var shiftAmEnd = (row.am_end && row.am_end !== null) ? row.am_end : null;
    var shiftPmStart = (row.pm_start && row.pm_start !== null) ? row.pm_start : null;
    var shiftPmEnd = (row.pm_end && row.pm_end !== null) ? row.pm_end : null;

    if (parseInt(row.has_shift) === 0) {
        $('#manual-shift-schedule', editTimeAdjustmentModal).removeClass('m--hide');
        $('#shift-schedule', editTimeAdjustmentModal).addClass('m--hide');

        $('#am_start, #am_end, #pm_start, #pm_end', editTimeAdjustmentModal)
            .val(null);

        /*** $('#am_start', editTimeAdjustmentModal)
            .val(row.am_start);

        $('#am_end', editTimeAdjustmentModal)
            .val(row.am_end);

        $('#pm_start', editTimeAdjustmentModal)
            .val(row.pm_start);

        $('#pm_end', editTimeAdjustmentModal)
            .val(row.pm_end); ***/

        $('#shift_am_start, #shift_am_end, #shift_pm_start, #shift_pm_end', editTimeAdjustmentModal)
            .removeAttr('data-validation')
            .val(null);

        /*** $('#shift_am_start', editTimeAdjustmentModal)
            .removeAttr('data-validation')
            .val(null);

        $('#shift_am_end', editTimeAdjustmentModal)
            .removeAttr('data-validation')
            .val(null);

        $('#shift_pm_start', editTimeAdjustmentModal)
            .removeAttr('data-validation')
            .val(null);

        $('#shift_pm_end', editTimeAdjustmentModal)
            .removeAttr('data-validation')
            .val(null); ***/
    } else {
        $('#manual-shift-schedule', editTimeAdjustmentModal).addClass('m--hide');
        $('#shift-schedule', editTimeAdjustmentModal)
            .removeClass('m--hide');

        $('#am_start', editTimeAdjustmentModal)
            .removeAttr('data-validation')
            .val(null);

        $('#am_end', editTimeAdjustmentModal)
            .removeAttr('data-validation')
            .val(null);

        $('#pm_start', editTimeAdjustmentModal)
            .removeAttr('data-validation')
            .val(null);

        $('#pm_end', editTimeAdjustmentModal)
            .removeAttr('data-validation')
            .val(null);

        $('#shift_am_start', editTimeAdjustmentModal)
            .removeAttr('data-validation')
            .val(shiftAmStart);

        $('#shift_am_end', editTimeAdjustmentModal)
            .removeAttr('data-validation')
            .val(shiftAmEnd);

        $('#shift_pm_start', editTimeAdjustmentModal)
            .removeAttr('data-validation')
            .val(shiftPmStart);

        $('#shift_pm_end', editTimeAdjustmentModal)
            .removeAttr('data-validation')
            .val(shiftPmEnd);
    }

    timeRecord.am_in = row.am_in;
    timeRecord.am_out = row.am_out;
    timeRecord.pm_in = row.pm_in;
    timeRecord.pm_out = row.pm_out;

    const am_in = row.am_in && row.am_in !== "empty" ? moment(row.am_in, 'HH:mm:ss').format('hh:mm A') : null;
    const am_out = row.am_out && row.am_out !== "empty" ? moment(row.am_out, 'HH:mm:ss').format('hh:mm A') : null;
    const pm_in = row.pm_in && row.pm_in !== "empty" ? moment(row.pm_in, 'HH:mm:ss').format('hh:mm A') : null;
    const pm_out = row.pm_out && row.pm_out !== "empty" ? moment(row.pm_out, 'HH:mm:ss').format('hh:mm A') : null;

    (!attendance.includes(am_in) && am_in) && attendance.push(am_in);
    (!attendance.includes(am_out) && am_out) && attendance.push(am_out);
    (!attendance.includes(pm_in) && pm_in) && attendance.push(pm_in);
    (!attendance.includes(pm_out) && pm_out) && attendance.push(pm_out);

    attendance.sort(function (prev, next) {
        return new Date('1970/01/01 ' + prev) - new Date('1970/01/01 ' + next);
    });

    $('#am_in')
        .select2({
            allowClear: true,
            placeholder: 'TIME',
            width: '100%',
            dropdownParent: editTimeAdjustmentModal,
            data: attendance
        })
        .on('select2:select', function (e) {
            const prev_value = timeRecord.am_in ? moment(timeRecord.am_in, 'HH:mm:ss').format('hh:mm A') : null;
            am_in_obj.modified = 1;
            am_in_obj.prev_value = prev_value;
            pm_in_obj.value = (typeof e.params.data !== "undefined") ? e.params.data.id : null;
        })
        .on('select2:unselecting', function (e) {
            const prev_value = timeRecord.am_in ? moment(timeRecord.am_in, 'HH:mm:ss').format('hh:mm A') : null;
            am_in_obj.modified = 1;
            am_in_obj.prev_value = prev_value;
            pm_in_obj.value = null;
        })
        .on('select2:open', function () {
            let a = $(this).data('select2');
            if (!$('.select2-link').length) {
                a.$results.parents('.select2-results')
                    .append(`<div class="select2-link">
                                <a>
                                    <i class="fa fa-clock-o mr-1"></i>
                                    <span class="m--font-bolder">Add Time</span>
                                </a>
                             </div>`)
                    .on('click', function (b) {
                        a.trigger('close');
                        openManualEntryModal('am_in', 'Morning Time in');
                    });
            }
        });

    $('#am_out')
        .select2({
            allowClear: true,
            placeholder: 'TIME',
            width: '100%',
            dropdownParent: editTimeAdjustmentModal,
            data: attendance
        })
        .on('select2:select', function (e) {
            const prev_value = timeRecord.am_out ? moment(timeRecord.am_out, 'HH:mm:ss').format('hh:mm A') : null;
            am_out_obj.modified = 1;
            am_out_obj.prev_value = prev_value;
            pm_in_obj.value = (typeof e.params.data !== "undefined") ? e.params.data.id : null;
        })
        .on('select2:unselecting', function () {
            const prev_value = timeRecord.am_out ? moment(timeRecord.am_out, 'HH:mm:ss').format('hh:mm A') : null;
            am_out_obj.modified = 1;
            am_out_obj.prev_value = prev_value;
            pm_in_obj.value = null;
        })
        .on('select2:open', function () {
            let a = $(this).data('select2');
            if (!$('.select2-link').length) {
                a.$results.parents('.select2-results')
                    .append(`<div class="select2-link">
                                <a>
                                    <i class="fa fa-clock-o mr-1"></i>
                                    <span class="m--font-bolder">Add Time</span>
                                </a>
                             </div>`)
                    .on('click', function (b) {
                        a.trigger('close');
                        openManualEntryModal('am_out', 'Morning Time out');
                    });
            }
        });

    $('#pm_in')
        .select2({
            allowClear: true,
            placeholder: 'TIME',
            width: '100%',
            dropdownParent: editTimeAdjustmentModal,
            data: attendance
        })
        .on('select2:select', function (e) {
            const prev_value = timeRecord.pm_in ? moment(timeRecord.pm_in, 'HH:mm:ss').format('hh:mm A') : null;
            pm_in_obj.modified = 1;
            pm_in_obj.prev_value = prev_value;
            pm_in_obj.value = e.params.data.id;
        }).on('select2:unselecting', function () {
            const prev_value = timeRecord.pm_in ? moment(timeRecord.pm_in, 'HH:mm:ss').format('hh:mm A') : null;
            pm_in_obj.modified = 1;
            pm_in_obj.prev_value = prev_value;
            pm_in_obj.value = null;
        }).on('select2:open', function () {
            let a = $(this).data('select2');
            if (!$('.select2-link').length) {
                a.$results.parents('.select2-results')
                    .append(`<div class="select2-link">
            <a>
            <i class="fa fa-clock-o mr-1"></i>
            <span class="m--font-bolder">Add Time</span>
            </a>
            </div>`)
                    .on('click', function (b) {
                        a.trigger('close');
                        openManualEntryModal('pm_in', 'Afternoon Time in');
                    });
            }
        });

    $('#pm_out')
        .select2({
            allowClear: true,
            placeholder: 'TIME',
            width: '100%',
            dropdownParent: editTimeAdjustmentModal,
            data: attendance
        })
        .on('select2:select', function (e) {
            const prev_value = timeRecord.pm_out ? moment(timeRecord.pm_out, 'HH:mm:ss').format('hh:mm A') : null;
            pm_out_obj.modified = 1;
            pm_out_obj.prev_value = prev_value;
            pm_in_obj.value = (typeof e.params.data !== "undefined") ? e.params.data.id : null;
        })
        .on('select2:unselecting', function () {
            const prev_value = timeRecord.pm_out ? moment(timeRecord.pm_out, 'HH:mm:ss').format('hh:mm A') : null;
            pm_out_obj.modified = 1;
            pm_out_obj.prev_value = prev_value;
            pm_in_obj.value = null;
        })
        .on('select2:open', function () {
            let a = $(this).data('select2');
            if (!$('.select2-link').length) {
                a.$results.parents('.select2-results')
                    .append(`<div class="select2-link">
                                <a>
                                    <i class="fa fa-clock-o mr-1"></i>
                                    <span class="m--font-bolder">Add Time</span>
                                </a>
                             </div>`)
                    .on('click', function (b) {
                        a.trigger('close');
                        openManualEntryModal('pm_out', 'Afternoon Time out');
                    });
            }
        });

    if (shiftAmStart) {
        $('#am_in').val(am_in).trigger('change').prop("disabled", false);
    } else {
        $('#am_in').val(null).trigger('change').prop("disabled", true);
    }

    if (shiftAmEnd) {
        $('#am_out').val(am_out).trigger('change').prop("disabled", false);
    } else {
        $('#am_out').val(null).trigger('change').prop("disabled", true);
    }

    if (shiftPmStart) {
        $('#pm_in').val(pm_in).trigger('change').prop("disabled", false);
    } else {
        $('#pm_in').val(null).trigger('change').prop("disabled", true);
    }

    if (shiftPmEnd) {
        $('#pm_out').val(pm_out).trigger('change').prop("disabled", false);
    } else {
        $('#pm_out').val(null).trigger('change').prop("disabled", true);
    }

    if (travel_order_list.length >= 1) {
        const toContainer = $('#to-container', editTimeAdjustmentModal);
        toContainer.removeClass('m--hide');
        $('#to-list', editTimeAdjustmentModal)
            .attr('data-validation', 'required')
            .select2({
                width: '100%',
                data: travel_order_list,
                escapeMarkup: function (markup) {
                    return markup;
                },
            })
            .val(row.travel_order)
            .trigger('change');

        if (parseInt(row.scrub_status) > 0) {
            $('label[for=\'to-list\'] span.m--font-danger', toContainer).removeClass('m--hide');
            $('#to-list').attr('data-validation', 'required');
        } else {
            $('label[for=\'to-list\'] span.m--font-danger', toContainer).addClass('m--hide');
            $('#to-list').removeAttr('data-validation');
        }
    } else {
        $('#to-container', editTimeAdjustmentModal).addClass('m--hide');
        $('#to-list', editTimeAdjustmentModal).removeAttr('data-validation');
    }

    if (loa_list.length >= 1) {
        const loaContainer = $('#loa-container', editTimeAdjustmentModal);
        loaContainer.removeClass('m--hide');
        $('#loa-list', editTimeAdjustmentModal)
            .attr('data-validation', 'required')
            .select2({
                width: '100%',
                data: loa_list,
                escapeMarkup: function (markup) {
                    return markup;
                },
            })
            .val(row.loa)
            .trigger('change');

        if (parseInt(row.scrub_status) > 0) {
            $('label[for=\'loa-list\'] span.m--font-danger', loaContainer).removeClass('m--hide');
            $('#loa-list').attr('data-validation', 'required');
        } else {
            $('label[for=\'loa-list\'] span.m--font-danger', loaContainer).addClass('m--hide');
            $('#loa-list').removeAttr('data-validation');
        }
    } else {
        $('#loa-container', editTimeAdjustmentModal).addClass('m--hide');
        $('#loa-list', editTimeAdjustmentModal).removeAttr('data-validation');
    }

    if (overtime.length >= 1) {
        overtimeContainer.removeClass('m--hide');
        tblOvertimeBody.empty();

        overtime.forEach((ot) => {
            const dateFrom = moment(ot.date_from);
            const dateTo = moment(ot.date_to);
            const highlightROT = parseFloat(ot.total_hrs) !== parseFloat(ot.adj_value) ? 'm--font-danger' : '';
            const highlightNDOT = parseFloat(ot.ndiff_hrs) !== parseFloat(ot.ndiff_adj_value) ? 'm--font-danger' : '';

            let strDetails = `<div>
                                <span class="mr-2 text-muted">REFERENCE #:</span>
                                <span class="m--font-boldest">${ot.reference_no}</span>
                            </div
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
                               </div>`;
            }

            strDetails += `<div class="mt-2">
                                <div class="mr-2 text-muted m--regular-font-size-sm1">PURPOSE</div>
                                <div class="m--font-bolder" style="text-align: justify;">${ot.purpose}</div>
                           </div>`;

            let adj_ot_in_value = ot.adj_ot_in_value !== "0000-00-00 00:00:00" && ot.adj_ot_in_value !== null ?
                moment(ot.adj_ot_in_value).format("YYYY-MM-DD hh:mm A") : ot.adj_ot_in_value;
            let adj_ot_out_value = ot.adj_ot_out_value !== "0000-00-00 00:00:00" && ot.adj_ot_out_value !== null ?
                moment(ot.adj_ot_out_value).format("YYYY-MM-DD hh:mm A") : ot.adj_ot_out_value;

            const template = `<tr id="row-${ot.id}" class="input-row">
                                <td rowspan="2">${strDetails}</td>
                                <td>
                                    <input type="hidden"
                                        id="overtime_in-${ot.id}"
                                        name="overtime_in[]"
                                        data-ts-ot-id="${ot.id}"
                                        data-adj-ot-id="${ot.adj_id}"
                                        data-input-old-value="${ot.adj_ot_in_value}"
                                        value="${ot.adj_ot_in_value}" />
                                    <input type="hidden"
                                        id="overtime_out-${ot.id}"
                                        name="overtime_out[]"
                                        data-ts-ot-id="${ot.id}"
                                        data-adj-ot-id="${ot.adj_id}"
                                        data-input-old-value="${ot.adj_ot_out_value}"
                                        value="${ot.adj_ot_out_value}" />
                                    <div class="form-group">
                                        <input type="text" disabled class="form-control form-control--table" value="${ot.total_hrs}">
                                        <p class="m--regular-font-size-sm3 text-center mb-0 mt-1 m--font-boldest text-muted">
                                            ${(ot.total_hrs * 60).toFixed(2)} Mins.
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" disabled
                                                   class="form-control form-control--table ${highlightROT}"
                                                   name="otTime[]"
                                                   data-ts-ot-id="${ot.id}"
                                                   data-adj-ot-id="${ot.adj_id}"
                                                   value="${ot.adj_value}"
                                                   oninput="updateHrsConvert(${ot.id})"
                                                   data-input-old-value="${ot.adj_value}"
                                                   autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" id="btn-ts-ot-${ot.id}"
                                                        data-toggle="m-tooltip" data-original-title="Edit" data-skin="dark"
                                                        class="btn btn-success btn-sm m-btn m-btn--icon btnUpdate updateMode"
                                                        data-old-value="${ot.adj_value}">
                                                        <i class="fa fa-pencil"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="m--regular-font-size-sm3 text-center mb-0 mt-1 m--font-boldest text-muted">
                                            ${(ot.adj_value * 60).toFixed(2)} Mins.
                                        </p>
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
                                            <input type="text" disabled
                                                   class="form-control form-control--table ${highlightNDOT}"
                                                   name="otNDiffTime[]"
                                                   data-ts-ot-id="${ot.id}"
                                                   data-adj-ot-id="${ot.adj_id}"
                                                   value="${ot.ndiff_adj_value}"
                                                   oninput="updateHrsConvert(${ot.id}, 4)"
                                                   data-input-old-value="${ot.ndiff_adj_value}"
                                                   autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" id="btn-ts-ot-${ot.id}"
                                                        data-toggle="m-tooltip" data-original-title="Edit" data-skin="dark"
                                                        class="btn btn-success btn-sm m-btn m-btn--icon btnUpdate updateMode"
                                                        data-old-value="${ot.ndiff_adj_value}">
                                                        <i class="fa fa-pencil"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="m--regular-font-size-sm3 text-center mb-0 mt-1 m--font-boldest text-muted">
                                            ${(ot.ndiff_adj_value * 60).toFixed(2)} Mins.
                                        </p>
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
                                                    <p id="temp_overtime_in-${ot.id}" class="form-control m--marginless" style="font-weight: 600;">${adj_ot_in_value}</p>
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
                                                <label  class="m--font-bolder">Overtime OUT</label>
                                                <div class="input-group date">
                                                    <p id="temp_overtime_out-${ot.id}" class="form-control m--marginless" style="font-weight: 600;">${adj_ot_out_value}</p>
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
        overtimeContainer.addClass('m--hide');
    }

    $('form', editTimeAdjustmentModal).attr('action', baseUrl('gcctime/timesheet/update_time_adjustment/' + row.time_adjustment_id + '/' + row.has_shift));
    $('#remarks', editTimeAdjustmentModal).val(row.remarks);

    if (typeof row.has_manual_overtime !== "undefined" && row.has_manual_overtime == true) {
        setTimeout(() => {
            const tempRequestedBy = editTimeAdjustmentModal.find("#requested_by");
            if (typeof tempRequestedBy !== "undefined") {
                if (typeof row.requested_by !== "undefined" && parseInt(row.requested_by) > 0 && typeof row.requestor_name !== "undefined" && row.requestor_name !== null) {
                    let tempOption = new Option(row.requestor_name, row.requested_by, true, true);
                    tempRequestedBy.empty();
                    tempRequestedBy.html(tempOption);
                    tempRequestedBy.trigger("change");
                }

                globalRequestedBy = tempRequestedBy.select2({
                    width: "100%",
                    placeholder: "Select an option",
                    dropdownParent: editTimeAdjustmentModal,
                    ajax: {
                        url: siteUrl("eforms/overtime/get_employee_department_head"),
                        dataType: "json",
                        delay: 250,
                        global: false,
                        processResults: function (data) {
                            return data;
                        }, error: function (jqXHR, status, error) {
                            if (status == "parsererror") { toastr.info(error, "AJAX REQUEST ERROR"); }
                        },
                    }
                });
            }
        }, 500);
    }

    $(editTimeAdjustmentModal).modal('show');
}

function updateHrsConvert(id, tdIndex = 2) {
    const row = $(`tr#row-${id}`);
    const td = $(`td:eq(${tdIndex})`, row);
    const input = td.find('input');
    const p = td.find('p');

    p.html((input.val() ? (parseFloat(input.val()) * 60).toFixed(2) : 0) + ' MINS.');
}

$.validate({
    form: $('#frm-edit-time-adjustment-modal'),
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const to = $('#to-list').val();
        const loa = $('#loa-list').val();
        const data = keyPairSerializedArray($(form).serializeArray());
        const url = $(form).attr('action');
        let overtimeUpdates = [];

        const _shift = ['am_start', 'am_end', 'pm_start', 'pm_end'];
        let shifts = { am_start: null, am_end: null, pm_start: null, pm_end: null };

        const _manualOT = ["overtime_reg_hrs", "overtime_ndiff_hrs", "overtime_in", "overtime_out", "overtime_requested_by", "overtime_purpose"];
        let manualOvertime = {
            overtime_reg_hrs: 0, overtime_ndiff_hrs: 0, overtime_in: null,
            overtime_out: null, overtime_requested_by: 0, overtime_purpose: null
        };

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
            const ts_overtime_id = regOT.attr('data-ts-ot-id');

            const orignDiffOT = $('td:eq(3) input', row).val();
            const nDiffOT = $('input[name=\'otNDiffTime[]\']', row);
            const nDiffOTPrevVal = nDiffOT.attr('data-input-old-value');

            const otIn = $('input[name=\'overtime_in[]\']', row);
            const otInPrevVal = otIn.attr('data-input-old-value');
            const otOut = $('input[name=\'overtime_out[]\']', row);
            const otOutPrevVal = otOut.attr('data-input-old-value');

            if (regOT.length || nDiffOT.length) {
                let insertTempOT = false;
                const id = regOT.attr('data-adj-ot-id');
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
                    ot_out_prev_value: otOutPrevVal,
                    ts_overtime_id
                };

                if (origRegOT !== value || orignDiffOT !== n_diff_value) { insertTempOT = true; }
                if (otInPrevVal !== ot_in_value || otOutPrevVal !== ot_out_value) { insertTempOT = true; }
                if (insertTempOT) { overtimeUpdates.push(tempOtObject); }
            }
        });

        $.ajax({
            url,
            dataType: 'JSON',
            type: 'POST',
            data: {
                csrf_token: _csrf_hash,
                am_in_obj,
                am_out_obj,
                pm_in_obj,
                pm_out_obj,
                remarks: data.remarks,
                adjustment_override: data.adjustment_override,
                travel_order: to.length >= 1 ? to : null,
                loa: loa.length >= 1 ? loa : null,
                shifts,
                manualOvertime,
                overtimeUpdates,
            },
            success: function (response) {
                const toast = response.success ? 'success' : 'error';
                toastr[toast](response.message, response.title);

                const row = response.data.row;
                // DETERMINE ROW INDEX FROM TABLE AND REDRAW ROW DATA
                const tableRow = $(`#ellipses-menu-${row.time_adjustment_id}`).attr('data-row');
                /*** dtTimeAdjustments.row(tableRow).data(row).draw(); ***/
                dtTimeAdjustments.ajax.reload(null, false);
                $(form).resetForm();
                editTimeAdjustmentModal.modal('hide');
            }
        });

        return false;
    }
});

editTimeAdjustmentModal
    .on('hidden.bs.modal', function () {
        $('#am_in > option').remove();
        $('#am_out > option').remove();
        $('#pm_in > option').remove();
        $('#pm_out > option').remove();
        $('#to-list > option').remove();
        $('#loa-list > option').remove();
    });

function resetField(el) {
    const time = timeRecord[el] ? moment(timeRecord[el], 'HH:mm:ss').format('hh:mm A') : null;
    const obj = eval(el + '_obj');
    Object.assign(obj, { modified: 0, manual: 0, prev_value: null, value: null });

    $(`#${el}`).val(time).trigger('change');
}

function openManualEntryModal(el, title) {
    $('.modal-title', timeManualEntryModal).html(`MANUAL ENTRY - <span class="m--font-bolder">${title}</span>`);
    $('#field', timeManualEntryModal).val(el);
    timeManualEntryModal.modal('show');
}

function openManualOvertimeEntryModal(el, title) {
    $('.modal-title', timeManualOvertimeEntryModal).html(`MANUAL ENTRY - <span class="m--font-bolder">${title}</span>`);
    $('#field', timeManualOvertimeEntryModal).val(el);
    const tempElement = $(`#${el}`, editTimeAdjustmentModal);
    if (typeof tempElement === "undefined" && tempElement.length == 0) { validateManualOvertime(); }
    timeManualOvertimeEntryModal.modal('show');
}

timeManualEntryModal
    .on('shown.bs.modal', function () {
        $('#time').focus();
    });

$.validate({
    form: $('#frm-edit-time-adjustment-time-manual-entry'),
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const data = keyPairSerializedArray($(form).serializeArray());
        const time = moment(data.time, 'HH:mm').format('hh:mm A');
        const field = data.field;
        const obj = eval(field + '_obj');

        const prev_value = timeRecord[field] ? moment(timeRecord[field], 'HH:mm:ss').format('hh:mm A') : null;

        Object.assign(obj, {
            modified: 1,
            manual: 1,
            prev_value,
            value: time
        });

        const option = new Option(time, time, false, true);
        $(`#${field}`).append(option);

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
            $(`#temp_${field}`, editTimeAdjustmentModal).text(datetime);
            $(`#${field}`, editTimeAdjustmentModal).val(datetime);
            timeManualOvertimeEntryModal.modal('hide');
            setTimeout(() => { $(form).resetForm(); }, 500);
            return false;
        }
    });
}

validateManualOvertime();


function openTimeAdjustmentDetailsModal(time_adjustment_id) {
    $.ajax({
        url: baseUrl(`gcctime/timesheet/get_time_adjustment_details/${time_adjustment_id}/1`),
        type: 'GET',
        dataType: 'JSON',
        success: function (response) {
            $('.modal-dialog', containerModal).addClass('modal-lg');
            $('.modal-content', containerModal).empty().append(response.modal);
            containerModal.modal('show');
        }
    });
}

// end edit modal functions

function keyPairSerializedArray(serializedArray) {
    let result = {};
    $.each(serializedArray, function () {
        result[this.name] = this.value;
    });

    return result;
}

function expandConfirmationRemarks(el) {
    $(el).toggleClass('line-clamp-2');
    if ($(el).hasClass('line-clamp-2')) {
        $(el).attr('data-original-title', 'Click to Expand.');
    } else {
        $(el).attr('data-original-title', 'Click to Collapse.');
    }
}

$('#tbl-overtime').on('click', 'tbody .btnUpdate', function () {
    const input = $(this).parent().parent().children('input');
    const defaultValue = $(this).attr('data-old-value');
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
        input.val(defaultValue);
        updateHrsConvert(id, tdIndex);
    }

    $(this).toggleClass('btn-success btn-danger');
    $(this).toggleClass('updateMode cancelMode');
    $('i', this).toggleClass('fa-pencil fa-ban');
});

function openFilterModal() {
    timeAdjustmentsFilterModal.modal('show');
}

timeAdjustmentsFilterModal.on('show.bs.modal', function (e) {
    $('#filter-employees').select2({
        placeholder: '',
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

    $('#filter-company').select2({
        placeholder: 'SELECT COMPANY',
        allowClear: true,
        width: '100%'
    });

    const selectedDateRange = $('#filter-date-range .form-control', this).val();
    let startDate = moment(), endDate = moment();
    if (selectedDateRange) {
        const selectedDateRageArr = selectedDateRange.split('/');
        startDate = moment(selectedDateRageArr[0]);
        endDate = moment(selectedDateRageArr[1]);
    }

    $('#filter-date-range')
        .daterangepicker({
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            startDate,
            endDate,
        }, function (start, end, label) {
            $('#filter-date-range .form-control')
                .val(start.format('MMMM DD,YYYY') + ' / ' + end.format('MMMM DD,YYYY'));
        });
});


$.validate({
    form: $('#frm-time-adjustments-filter'),
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        employees = $('#filter-employees').val();
        company = $('#filter-company').val();
        date = $('#filter-date-range .form-control').val();

        dtTimeAdjustments.ajax.reload();
        timeAdjustmentsFilterModal.modal('hide');
        return false;
    }
});

function clearDateRange(e) {
    $('#filter-date-range .form-control').val(null);

    e.stopPropagation();
}

function clearFilter() {
    $('#filter-date-range .form-control').val(null);
    $('#filter-company').val(null).trigger('change');
    $('#filter-employees').val(null).trigger('change');
    employees = null
    company = null;
    date = null;

    dtTimeAdjustments.ajax.reload();
    timeAdjustmentsFilterModal.modal('hide');
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

var resetFilter = function (event) {
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        const select2 = form.find("#filter-employees, #filter-payroll_group, #filter-company");
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