var msnry;

$(document).on("click", ".module_redirect", function () {
    var self = $(this);
    var dataId = self.data("id");
    $.ajax({
        url:  siteUrl("portal/redirect_to_module"),
        type: "post",
        dataType: "json",
        data: { id: dataId, csrf_token: _csrf_hash },
        success: function (json) {
            if (json.response) {
                window.location.href = json.redirect;
            } else {
                toastr.warning("Nothing to redirect!", "Portal Redirect", 5000);
            }
        }
    });
});

if (typeof idleTimerTrigger !== "undefined" && typeof idleTimerTrigger == "function") {
    idleTimerTrigger();
}

if(typeof getAcctgcount !== "undefined" && typeof getAcctgcount == "function"){
    getAcctgcount();
}

if(window.location == siteUrl("portal/index")){
    document.addEventListener('DOMContentLoaded', function () {
        msnry = new Masonry('.row.second-section', {
            itemSelector: '.grid-item',
            columnWidth: '.grid-sizer',
            percentPosition: true,
            resize: true
        });
    });

    $(document).on("click", "#attendanceLegendToggle", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const menu = $("#attendance-legend-options");
        menu.toggleClass("show");
        $(this).attr("aria-expanded", menu.hasClass("show") ? "true" : "false");
    });

    $(document).on("click", function (e) {
        if (!$(e.target).closest("#attendanceLegendToggle, #attendance-legend-options").length) {
            $("#attendance-legend-options").removeClass("show");
            $("#attendanceLegendToggle").attr("aria-expanded", "false");
        }
    });

    var vmTab1 = new Vue({
        el: "#portal_notifications",
        data: { 
            vm_tab1: {show:false,}, 
            vm_travel_order: {show:false,}, 
            vm_acct: {show:false,}, 
            vm_borrowing: {show:false,}, 
            vm_overtime: {show:false,}, 
            vm_transmittal: {show:false,}, 
            vm_shipping : {show:false,},
            vm_ca : {show:false,},
            payslip: {show:false,data:[]},
            is_loading: true,
            attendance: [],
            table: null
        },
        mounted(){
            this.getPayslip();
            this.getTimesheetAttendance();
        },
        watch: {
            is_loading(value) {
                if (!value) {
                    this.$nextTick(() => {
                        this.timesheetTable();
                    });
                }
            },
            attendance() {
                if (!this.is_loading) {
                    this.$nextTick(() => {
                        this.timesheetTable();
                    });
                }
            }
        }, methods: {
            styles: {
                width: '50%',
            }, getUnapprovedLoa(){
                $.ajax({
                    url:  siteUrl("portal/get_unapproved_loa"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.vm_tab1 = Object.assign({}, json);
                        vmTab1.vm_tab1.show = true;
                        vmTab1.updateMasonryLayout();
                    }
                });
            }, getTravelOrder(){
                $.ajax({
                    url:  siteUrl("portal/get_to_recommendation"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.vm_travel_order = Object.assign({}, json);
                        vmTab1.vm_travel_order.show = true;
                        vmTab1.updateMasonryLayout();
                    }
                });
            }, getAccountability(){
                $.ajax({
                    url:  siteUrl("portal/get_accountability"),
                    type: "post",
                    dataType: "json",
                    data: {
                        csrf_token : _csrf_hash,
                        company : $("#companySelect option:selected").val(),
                    },
                    success: function (json) {
                        vmTab1.vm_acct = Object.assign({}, json);
                        vmTab1.vm_acct.show = true;
                        vmTab1.updateMasonryLayout();
                    }
                });
            }, getBorrowing(){
                $.ajax({
                    url:  siteUrl("portal/get_borrowing"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.vm_borrowing = Object.assign({}, json);
                        vmTab1.vm_borrowing.show = true;
                        vmTab1.updateMasonryLayout();
                    }
                });
            }, getOvertime(){
                $.ajax({
                    url:  siteUrl("portal/get_overtime"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.vm_overtime = Object.assign({}, json);
                        vmTab1.vm_overtime.show = true;
                        vmTab1.updateMasonryLayout();
                    }
                });
            }, getTransmittal(){
                $.ajax({
                    url:  siteUrl("portal/get_transmittal"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.vm_transmittal = Object.assign({}, json);
                        vmTab1.vm_transmittal.show=true;
                        vmTab1.updateMasonryLayout();
                    }
                });
            }, getShipping(){
                $.ajax({
                    url:  siteUrl("portal/get_shipping"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.vm_shipping = Object.assign({}, json);
                        vmTab1.vm_shipping.show = true;
                        vmTab1.updateMasonryLayout();
                    }
                });
            }, getCashadvance(){
                $.ajax({
                    url:  siteUrl("portal/get_cashadvance"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.vm_ca = Object.assign({}, json);
                        vmTab1.vm_ca.show = true;

                        vmTab1.updateMasonryLayout();
                    }
                });
            }, getPayslip(){
                $.ajax({
                    url:  siteUrl("portal/get_payslip"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        let tempLoan = [];

                        if (json.data) {
                            const tempCreatedAdjustments = json.data.created_adjustments;

                            if (json.data.loans.length > 0) {
                                $.each(json.data.loans, function (index, item) {
                                    if (item.loan_name.toLowerCase() != 'charges' && item.loan_name.toLowerCase() != 'under deduction' && item.loan_name.toLowerCase() != 'medical loan') {
                                        var temp_amount = parseFloat(item.amount_due.replace(/,/g, ''));
            
                                        // for adding cash advance with loan adjustments
                                        if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                                            const created_adjustments = tempCreatedAdjustments.split(",");
                                            var tempAdj = 0;
                                            created_adjustments.forEach((row, i) => {
                                                const temp_adjustment = row.split("||");
                                                const adj_type = parseInt(temp_adjustment[2]);
                                                const temp_status = parseInt(temp_adjustment[3]);
                                                let _temp = parseFloat(item.amount_due);
                                                if (adj_type == 1) {
                                                    _temp = parseFloat(temp_amount) + parseFloat(temp_adjustment[1]);
                                                } else {
                                                    _temp = parseFloat(temp_amount) - parseFloat(temp_adjustment[1]);
                                                }
        
                                                tempAdj = _temp;
                                                _temp = _temp;
        
                                                if (typeof item.loan_name !== "undefined" && item.loan_name.toLowerCase() == 'cash advance') {
                                                    if (temp_adjustment[0] == "LOAN" && temp_status === 1) {
                                                        temp_amount = _temp;
                                                    }
                                                } else {
                                                    // includes loan adjustments when employee has no cash advance
                                                    if (temp_adjustment[0] == "LOAN" && temp_status === 1) {
                                                        if (!tempLoan.some(el => el.loan_name === 'CASH ADVANCE')) {
                                                            tempLoan.push({
                                                                'loan_name' : 'CASH ADVANCE',
                                                                'amount_due' : temp_adjustment[1],
                                                                'loan_type' : adj_type
                                                            });
                                                        }
                                                    }
                                                }
                                            });
                                        }
            
                                        tempLoan.push({
                                            'loan_name' : item.loan_name,
                                            'amount_due' : temp_amount,
                                            'loan_type' : item.loan_type
                                        });
                                    }
            
                                    // for adding the charges to Other Deductions
                                    if (item.loan_name.toLowerCase() == 'charges' || item.loan_name.toLowerCase() == 'under deduction' || item.loan_name.toLowerCase() == 'medical loan') {
                                        json.data.adjustment_deductions.push({
                                            'label' : item.loan_name,
                                            'display_value' : item.amount_due,
                                            'value' : item.amount_due,
                                            'adj_type' : 0
                                        });
                                    }
                                });
                            } else {
                                if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                                    const created_adjustments = tempCreatedAdjustments.split(",");
                                    var tempAdj = 0;
                                    created_adjustments.forEach((row, i) => {
                                        const temp_adjustment = row.split("||");
                                        const adj_type = parseInt(temp_adjustment[2]);
                                        const temp_status = parseInt(temp_adjustment[3]);
                                        let _temp = parseFloat(temp_adjustment[1]);
            
                                        tempAdj = _temp;
                                        _temp = _temp;
            
                                        if (temp_adjustment[0] == "LOAN" && temp_status === 1) {
                                            tempLoan.push({
                                                'loan_name' : 'CASH ADVANCE',
                                                'amount_due' : _temp,
                                                'loan_type' : adj_type
                                            });
                                        }
                                    });
                                }
                            }

                            json.data.loans = tempLoan;
                            json.data.adjustment_d_count = json.data.adjustment_deductions.length;
                            vmTab1.payslip.data = Object.assign({}, json.data);
                            vmTab1.payslip.show = false;
                            
                            vmTab1.updateMasonryLayout();
                        }

                        vmTab1.is_loading = false;
                    }
                });
            }, formatDate(date){
                if(!date) return "---";
                return moment(date).format("MMM DD, YYYY");
            }, formatDateCoverage(start, end) {
                if(!start || !end) return "---";
                const startMoment = moment(start);
                const endMoment = moment(end);
            
                if (startMoment.month() === endMoment.month() && startMoment.year() === endMoment.year()) {
                    return `${startMoment.format("MMM DD")} - ${endMoment.format("DD, YYYY")}`;
                } else if (startMoment.year() === endMoment.year()) {
                    return `${startMoment.format("MMM DD")} - ${endMoment.format("MMM DD, YYYY")}`;
                } else {
                    return `${startMoment.format("MMM DD, YYYY")} - ${endMoment.format("MMM DD, YYYY")}`;
                }
            }, formatCurrency(amount){
                if(!amount) return "₱0.00";
                return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(amount);
            }, updateMasonryLayout() {
                new ResizeObserver(() => {
                    msnry.reloadItems();
                    msnry.layout();
                }).observe(document.querySelector('.row'));
            }, isEmpty(arr) {
                return jQuery.isEmptyObject(arr);
            }, formatTimesheetTime(time, has_shift = 1) {
                if (has_shift == 0 || !has_shift) {
                    return '';
                }

                if (!time || time === "00:00:00") {
                    return "--:--";
                }

                const parsed = moment(time, ["HH:mm:ss", "HH:mm", "YYYY-MM-DD HH:mm:ss"], true);
                if (parsed.isValid()) {
                    return parsed.format("hh:mm A");
                }

                return time;
            }, formatTimesheetNumber(value) {
                if (value === null || value === undefined || value === "") {
                    return "0";
                }

                const numeric = parseFloat(value);
                return Number.isNaN(numeric) ? "0" : numeric.toFixed(2).replace(/\.00$/, "");
            }, attendanceMobileStatusText(row) {
                const scrub_status = typeof row.scrub_status != 'undefined' && row.scrub_status !== null ? parseInt(row.scrub_status) : 0;
                const verified = (typeof row.verified !== "undefined" && row.verified !== null) ? parseInt(row.verified) : 0;
                const hasShift = typeof row.has_shift !== "undefined" ? parseInt(row.has_shift) : 0;
                const isHoliday = typeof row.is_holiday !== "undefined" ? parseInt(row.is_holiday) : 0;
                const paidHoliday = (typeof row.paid_holiday !== "undefined" && row.paid_holiday !== null) ? parseInt(row.paid_holiday) : 0;
                const totalLate = typeof row.total_late !== "undefined" && row.total_late !== null ? parseFloat(row.total_late) : 0;
                const { am_in, am_out, pm_in, pm_out, total_time_rendered } = row;
                const hasLOA = (typeof row.has_LOA !== "undefined" && row.has_LOA !== null) ? parseInt(row.has_LOA) : 0;
                const hasOvertime = (typeof row.has_overtime !== "undefined" && row.has_overtime !== null) ? parseInt(row.has_overtime) : 0;
                const hasTO = (typeof row.has_TO !== "undefined" && row.has_TO !== null) ? parseInt(row.has_TO) : 0;
                const hasWholeDayLoa = (typeof row.has_whole_day_LOA !== "undefined" && row.has_whole_day_LOA !== null) ? parseInt(row.has_whole_day_LOA) : 0;
                const completeAttendance = row.complete_attendance_count;
                
                let text = 'On Time';
                let hasRendered = typeof total_time_rendered !== "undefined" && total_time_rendered !== null && parseFloat(total_time_rendered) > 0;
                if (isHoliday == 1 && paidHoliday == 0) { hasRendered = false; }
                if (scrub_status == 1 || scrub_status == 2) { hasRendered = true; }
                if (isHoliday == 1 && hasRendered > 0) { hasRendered = true; }
                hasRendered = am_in || am_out || pm_in || pm_out;

                if (hasRendered) {
                    console.log(row._date, scrub_status, completeAttendance);
                    if ((scrub_status === 1 && verified === 0) || ((hasLOA >= 1 && hasWholeDayLoa <= 0) || (hasTO >= 1 && completeAttendance === false))) {
                        text =  'Lacking Entries';
                    }
                    
                    if (scrub_status === 2){
                        text = 'Multiple Entries';
                    }
                } else if(!hasRendered && hasShift === 1 && isHoliday === 0) {
                    text =  "Absent";
                }

                if (((hasShift === 0 && (hasOvertime === 0 || hasOvertime === 1)) && (verified === 0 || !verified))
                    || ((!hasShift && !hasOvertime) && (verified === 0 || !verified))
                    || (isHoliday == 1 && row.allow_paid_holiday === false)) {
                    text = "Rest Day";
                }

                if (totalLate > 0) {
                    text = `${this.formatTimesheetNumber(totalLate)} min(s) late`;
                }

                if (isHoliday === 1) {
                    text = "Holiday";
                }

                return text;
            }, attendanceMobileHasTime(row) {
                return !!(row.am_in || row.am_out || row.pm_in || row.pm_out);
            }, attendanceMobileDate(row) {
                if (!row || !row._date) {
                    return "---";
                }
                return moment(row._date).format("dddd, MMM DD");
            }, attendanceMobileRenderedHours(row) {
                if (!row || row.total_time_rendered === null || typeof row.total_time_rendered === "undefined" || row.total_time_rendered === "") {
                    return "0h 00m";
                }

                const raw = parseFloat(row.total_time_rendered);
                if (Number.isNaN(raw)) {
                    return "0h 00m";
                }

                const totalMinutes = raw > 24 ? Math.round(raw) : Math.round(raw * 60);
                const hours = Math.floor(totalMinutes / 60);
                const mins = totalMinutes % 60;
                return `${hours}h ${String(mins).padStart(2, "0")}m`;
            }, attendanceMobileStatusClass(row) {
                const status = this.attendanceMobileStatusText(row);

                console.log(row);

                if (status === "Holiday") return "is-holiday";
                if (status === "Rest Day") return "is-restday";
                if (status === "Absent") return "is-absent";
                if (status === "On Time") return "is-ontime";
                if (status === "Lacking Entries") return "is-lacking";
                if (status === "Multiple Entries") return "is-multiple";
                if (status.indexOf("late") > -1) return "is-late";

                return "";
            }, attendanceMobileCardClass(row) {
                const statusClass = this.attendanceMobileStatusClass(row);
                if (statusClass === "is-late") return "is-late";
                if (statusClass === "is-absent") return "is-absent";
                if (statusClass === "is-ontime") return "is-ontime";
                if (statusClass === 'is-lacking') return 'is-lacking';
                if (statusClass === 'is-multiple') return 'is-multiple';
                return "";
            }, attendanceMobileTimeClass(row, timeKey) {
                const statusClass = this.attendanceMobileStatusClass(row);
                if (statusClass === "is-late" && timeKey === "am_in") {
                    return "is-late";
                }
                if (statusClass === "is-ontime" && timeKey === "am_in") {
                    return "is-accent";
                }
                return "";
            }, timesheetTableShow() {
                this.$nextTick(() => {
                    this.updateMasonryLayout();
                });
            }, bindTimesheetTableEvents() {
                const tableEl = $("#timesheet-table");
                if (!tableEl.length) {
                    return;
                }

                tableEl.off("length.dt.portal");
                tableEl.on("length.dt.portal", () => {
                    this.timesheetTableShow();
                });
            }, timesheetTable() {
                const self = this;
                const tableEl = $("#timesheet-table");
                const rows = Array.isArray(this.attendance) ? this.attendance : [];

                if (!tableEl.length) {
                    return;
                }

                if ($.fn.DataTable.isDataTable("#timesheet-table")) {
                    this.table = tableEl.DataTable();
                    this.table.clear();
                    this.table.rows.add(rows);
                    this.table.draw();
                    this.bindTimesheetTableEvents();
                    return;
                }

                this.table = tableEl.DataTable({
                    dom: '<"toolbar">frtlip',
                    data: rows,
                    searching: false,
                    processing: false,
                    serverSide: false,
                    order: [[0, "desc"]],
                    pageLength: 5,
                    lengthMenu: [[5, 10, -1], [5, 10, "All"]],
                    columns: [
                        {
                            data: "_date",
                            render: function (data, type, row, meta) {
                                let html = ``;
                                html = self.formatDate(data);

                                if (row.is_holiday == 1) {
                                    const paid_holiday = row.paid_holiday ? 'text-success' : '';
                                    html += ` <span class="fa fa-flag ${paid_holiday}" data-original-title="${row.holiday_classification.toUpperCase()}" data-toggle="m-tooltip" data-skin="dark" data-delay='{"show": 300}'></span>`;
                                }

                                if (row.has_loa == 1) {
                                    html += ` <span class="flaticon-event-calendar-symbol"></span>`;
                                }

                                return html;
                            }
                        },
                        { data: "_weekday", defaultContent: "---", className: 'text-center', orderable: false, render: (data) => `<span class="m--font-bolder">${data}</span>` },
                        { data: "am_in", orderable: false, className: 'text-center', 
                            render: function (data, type, row, meta) {
                                return self.formatTimesheetTime(data, row.has_shift);
                            }
                        },
                        { data: "am_out", orderable: false, className: 'text-center', 
                            render: function (data, type, row, meta) {
                                return self.formatTimesheetTime(data, row.has_shift);
                            }
                        },
                        { data: "pm_in", orderable: false, className: 'text-center', 
                            render: function (data, type, row, meta) {
                                return self.formatTimesheetTime(data, row.has_shift);
                            }
                        },
                        { data: "pm_out", orderable: false, className: 'text-center', 
                            render: function (data, type, row, meta) {
                                return self.formatTimesheetTime(data, row.has_shift);
                            }
                        },
                        { data: "total_late", className: 'text-center', orderable: false, 
                            render: function (data, type, row, meta) {
                                let html = ``;
                                const { am_in, am_out, pm_in, pm_out, total_time_rendered } = row;
                                let hasRendered = typeof total_time_rendered !== "undefined" && total_time_rendered !== null && parseFloat(total_time_rendered) > 0;
                                hasRendered = am_in || am_out || pm_in || pm_out;

                                if (row.is_holiday) {
                                    return 'Holiday';
                                }

                                if (!row.has_shift || row.has_shift == 0) {
                                    return 'Rest Day';
                                }

                                if (hasRendered) {
                                    if (data == 0) {
                                        html = `<span class="m-badge m-badge--success m-badge--wide">On Time</span>`;
                                    } else {
                                        if (row.has_shift == 0 || !row.has_shift) {
                                            html = '';
                                        } else {
                                            const time = self.formatTimesheetNumber(data);
                                            html = data ? `<span class="m-badge m-badge--warning m-badge--wide">${time}min(s) late</span>` : ' --- ';
                                        }
                                    }
                                } else {
                                    return '<p class="text-white m-0">Absent</p>';
                                }

                                return html;
                            }
                        },
                    ],
                    rowCallback: function (row, data) {
                        if ($(row).hasClass('lacking')) {
                            $(row).find('.m-badge').removeClass('m-badge').removeClass('m-badge--success');
                        }
                    },
                    createdRow: function (rowEl, rowData, _index) {
                        const scrub_status = (typeof rowData.scrub_status !== "undefined" && rowData.scrub_status !== null) ? parseInt(rowData.scrub_status) : 0;
                        const verified = (typeof rowData.verified !== "undefined" && rowData.verified !== null) ? parseInt(rowData.verified) : 0;
                        const isHoliday = (typeof rowData.is_holiday !== "undefined" && rowData.is_holiday !== null) ? parseInt(rowData.is_holiday) : 0;
                        const paidHoliday = (typeof rowData.paid_holiday !== "undefined" && rowData.paid_holiday !== null) ? parseInt(rowData.paid_holiday) : 0;
                        const hasShift = (typeof rowData.has_shift !== "undefined" && rowData.has_shift !== null) ? parseInt(rowData.has_shift) : 0;
                        const completeAttendance = rowData.complete_attendance_count;
                        const hasOvertime = (typeof rowData.has_overtime !== "undefined" && rowData.has_overtime !== null) ? parseInt(rowData.has_overtime) : 0;
                        const hasTO = (typeof rowData.has_TO !== "undefined" && rowData.has_TO !== null) ? parseInt(rowData.has_TO) : 0;
                        const hasLOA = (typeof rowData.has_LOA !== "undefined" && rowData.has_LOA !== null) ? parseInt(rowData.has_LOA) : 0;
                        const hasWholeDayLoa = (typeof rowData.has_whole_day_LOA !== "undefined" && rowData.has_whole_day_LOA !== null) ? parseInt(rowData.has_whole_day_LOA) : 0;
                        const { am_in, am_out, pm_in, pm_out, total_time_rendered } = rowData;

                        let currentRowClass = null;
                        let hasRendered = typeof total_time_rendered !== "undefined" && total_time_rendered !== null && parseFloat(total_time_rendered) > 0;
                        if (isHoliday == 1 && paidHoliday == 0) { hasRendered = false; }
                        if (scrub_status == 1 || scrub_status == 2) { hasRendered = true; }
                        if (isHoliday == 1 && hasRendered > 0) { hasRendered = true; }
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

                        if (isHoliday == 1 && paidHoliday == 1) {
                            currentRowClass = '';
                        }

                        if (currentRowClass) { $(rowEl).addClass(currentRowClass); }
                    }
                });
                this.bindTimesheetTableEvents();
            }, getTimesheetAttendance() {
                $.ajax({
                    url:  siteUrl("portal/get_timesheet_attendance"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.attendance = json && Array.isArray(json.data) ? json.data : [];
                        vmTab1.updateMasonryLayout();
                    },
                    error: function () {
                        vmTab1.attendance = [];
                    }
                });
            }
        }
    });
}
