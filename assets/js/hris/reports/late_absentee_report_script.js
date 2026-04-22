let _years = [];
let _companies = [];
let filterExport = {};
let totalEntries = 0;
let typeReport ="";
toastr.options = { newestOnTop: true, positionClass: "toast-bottom-right" };

const hrisFilterLateAbsenteeReport = $("#frm-filter-hris-late_absentee_report");
const dtTableLateAbsentee = $("#table-late_absentee_report");
const _tblPortletReports = $("#m_portlet_tools-late_absentee_report").mPortlet();
const modalLateAbsenteePreview = $("#modalLateAbsenteePreview");
const late_and_absentee_view = $("#late_and_absentee_view");

let dtTableLateAbsenteeReport;
let filterOptionsLateAbsentee = {};
let globalLoaReference = {};

let isCollapsedPortlet = true;

const months = [
    { id: 1, text: "January" },
    { id: 2, text: "February" },
    { id: 3, text: "March" },
    { id: 4, text: "April" },
    { id: 5, text: "May" },
    { id: 6, text: "June" },
    { id: 7, text: "July" },
    { id: 8, text: "August" },
    { id: 9, text: "September" },
    { id: 10, text: "October" },
    { id: 11, text: "November" },
    { id: 12, text: "December" }
];

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.years !== "undefined" && _tempContentData.years.length > 0){
        _years = _tempContentData.years;
    }
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){
        _companies = _tempContentData.company;
    }
}

const vmLateAbsenteeReport = new Vue({
    el: "#tempFilterByLateAbsenteeReport",
    data: { filter_by: "date_range", report_type: "late", active_employee: false },
    watch: {
        filter_by(value) {
            const currentElement = this.$el;
            if (value === 'date_range') {
                setTimeout(() => this.renderRangeDatePicker(), 500);
            } else {
                setTimeout(() => {
                    $(currentElement).find("select[name='filter_month']")
                    .select2({
                        width: '100%',
                        data: months,
                        placeholder: 'Search',
                        allowClear: true
                    })
                    .on('select2:select', (e) => $(e.target).validate())
                    .on('select2:unselect', (e) => $(e.target).validate()); 

                    $(currentElement).find("select[name='filter_year']")
                    .select2({
                        width: '100%',
                        data: _years,
                        placeholder: 'Search',
                        allowClear: true
                    })
                    .on('select2:select', (e) => $(e.target).validate())
                    .on('select2:unselect', (e) => $(e.target).validate()); 
                }, 250);
            }
        },

        report_type(type) {
            rebuildLateAbsenteeTable(type);
        }
    }, methods: {
        renderRangeDatePicker(){
            const currentElement = this.$el;
            const dtPickerElement = $(currentElement).find("#date-picker")
            .daterangepicker({
                buttonClasses: 'm-btn btn',
                applyClass: 'btn-primary',
                cancelClass: 'btn-secondary',
                locale: {
                    format: 'MM/DD/YYYY'
                }, maxDate: new Date,
            })
            .on('apply.daterangepicker', function (ev, picker) {
                const tempStartDate = picker.startDate.format('MMM DD, YYYY');
                const tempEndDate = picker.endDate.format('MMM DD, YYYY');
                const tempFormat = tempStartDate + ' - ' + tempEndDate;
                const dtRange = $(currentElement).find("#date-range");
                dtRange.val(tempFormat);
                setTimeout( function () { dtRange.validate(); }, 500 );
            });

            return dtPickerElement;
        }
    },
    mounted(){
        this.renderRangeDatePicker();
        late_absentee_column_report(this.report_type);
    }
});

const vmLateAbsenteePreview = new Vue({
    el: "#modalLateAbsenteeContainer",
    data: { row: {}, attlogs: [], reference: {}, report_type: "late" },
    watch: {
        'row.attendance_logs': function(value){
            let _this = this;
            let arrLogx = [];
            if(typeof value != "undefined" && value){
                const arrLogs = value.split(",");
                arrLogs.forEach(function(log){
                    if(log){
                        const attxLogs = log.split("~");
                        if(attxLogs.length == 2){
                            const stDate = moment(new Date(attxLogs[0]), "YYYY-MM-DD HH:mm:ss").format("LLLL");
                            const edDate = moment(new Date(attxLogs[1]), "YYYY-MM-DD HH:mm:ss").format("LLL");
                            const formatted = `${stDate} - ${edDate}`;
                            arrLogx.push(formatted);
                        }else{
                            const stDate = moment(new Date(attxLogs[0]), "YYYY-MM-DD HH:mm:ss").format("LLLL");
                            arrLogx.push(stDate);
                        }
                    }
                });
                arrLogx.sort(function(a, b){ return new Date(a) - new Date(b); });
            }
            _this.attlogs = [];
            if(arrLogx.length > 0){
                arrLogx.forEach(function(item){ _this.attlogs.push(item); });
            }

            return _this;
        }
    },
    methods: {
        dateFormatted(date){
            return date ? moment(new Date(date), "YYYY-MM-DD").format("LL"): null;
        }, 
        backgroundClass(date){
            let className = '';
            const nDate = date.split(" - ");
            if(nDate.length == 2){
                const meridian = moment(new Date(nDate[0]), "dddd, MMMM D, YYYY h:m A").format("A");
                if(meridian){ className = meridian == 'AM' ? 'alert-primary':'alert-danger'; }
            }else{
                const meridian = moment(new Date(date), "dddd, MMMM D, YYYY h:m A").format("A");
                if(meridian){ className = meridian == 'AM' ? 'alert-primary':'alert-danger'; }
            }

            return className;
        }, getLoaReference(employeeId, date) {
            let referenceNumber = null;
            const [startDate] = date.split(' - ');
            const startDateObj = moment(new Date(startDate), 'dddd, MMMM D, YYYY h:m A');
            let meridian = startDateObj.format('A');
            const keyDate = startDateObj.format('YYYY-MM-DD');

            if (globalLoaReference[employeeId] && globalLoaReference[employeeId][keyDate]) {
                const { reference, whole_day, half_day, _meridian, loa_type } = globalLoaReference[employeeId][keyDate];
                if (reference) {
                    if ((half_day && _meridian === meridian) ||
                        (whole_day && half_day === false) ||
                        (loa_type == 4 && half_day === false)) {
                        referenceNumber = reference;
                    }
                }
            }

            return referenceNumber;
        }
    }
});

const vm_late_and_absentee = new Vue({
    el: "#late_and_absentee_container",
    data: { row: {}, attlogs: [], reference: {}, report_type: "late" },
    computed: {
        lateDates() {
            if (!this.row.late || !this.row.late.late_dates) return [];

            return this.row.late.late_dates
                .split(',')
                .filter(Boolean)
                .map(d => moment(d, "YYYY-MM-DD HH:mm:ss").format("LLL"));
        },

        absentDates() {
            if (!this.row.absent || !this.row.absent.absent_dates) return [];

            return this.row.absent.absent_dates
                .split(',')
                .filter(Boolean)
                .map(d => moment(d, "YYYY-MM-DD").format("LL"));
        },

        attendanceLogs() {
            if (!this.row.absent || !this.row.absent.attendance_logs) return [];

            return this.row.absent.attendance_logs
                .split(',')
                .filter(Boolean)
                .map(log => {
                    const parts = log.split('~');
                    if (parts.length === 2) {
                        const start = moment(parts[0], "YYYY-MM-DD HH:mm:ss").format("LLL");
                        const end   = moment(parts[1], "YYYY-MM-DD HH:mm:ss").format("LLL");
                        return `${start} - ${end}`;
                    }
                    return moment(parts[0], "YYYY-MM-DD HH:mm:ss").format("LLL");
                });
        },

        lateRecords() {
            if (!this.row.late || !this.row.late.late_dates) return [];

            const dates = this.row.late.late_dates
                .split(',')
                .filter(Boolean);

            return dates.map(d => {
                const mDate = moment(d, "YYYY-MM-DD HH:mm:ss");

                let expectedHour = 8;
                let expectedMin = 0;

                // Detect afternoon shift
                if (mDate.hour() >= 12) {
                    expectedHour = 13;
                    expectedMin = 0;
                }

                const expected = moment(mDate)
                    .startOf('day')
                    .hour(expectedHour)
                    .minute(expectedMin)
                    .second(0);

                let minutesLate = mDate.diff(expected, 'minutes');
                if (minutesLate < 0) minutesLate = 0;

                const isMorning = expectedHour === 8;
                const isAfternoon = expectedHour === 13;

                // MORNING: 9:01+ = Half Day Absent
                if (isMorning && mDate.isAfter(moment(mDate).hour(9).minute(0).second(59))) {
                    return {
                        date: mDate.format("LLL"),
                        minutes: this.formatMinutes(minutesLate),
                        status: "Absent"
                    };
                }

                // MORNING: 8:31–9:00 = 120 mins
                if (isMorning && minutesLate > 30) {
                    return {
                        date: mDate.format("LLL"),
                        minutes: this.formatMinutes(120),
                        status: "Late"
                    };
                }

                // AFTERNOON: 1:31+ = Half Day Absent
                if (isAfternoon && minutesLate > 30) {
                    return {
                        date: mDate.format("LLL"),
                        minutes: this.formatMinutes(minutesLate),
                        status: "Absent"
                    };
                }

                return {
                    date: mDate.format("LLL"),
                    minutes: minutesLate + " mins",
                    status: "Late"
                };
            });
        },

        loaMap() {
            return this.row.absent?.loa_reference || {};
        }
    },
    methods: {
        formatMinutes(totalMinutes) {
            if (typeof totalMinutes !== "number") return totalMinutes;

            const hours = Math.floor(totalMinutes / 60);
            const minutes = totalMinutes % 60;

            let result = "";

            if (hours > 0) {
                result += hours + " hr" + (hours > 1 ? "s" : "");
            }

            if (minutes > 0) {
                if (result) result += " ";
                result += minutes + " min" + (minutes > 1 ? "s" : "");
            }

            if (!result) result = "0 mins";

            return result;
        },

        dateFormatted(date){
            return date ? moment(new Date(date), "YYYY-MM-DD").format("LL"): null;
        }, 

        backgroundClass(date){
            let className = '';
            const nDate = date.split(" - ");
            if(nDate.length == 2){
                const meridian = moment(new Date(nDate[0]), "dddd, MMMM D, YYYY h:m A").format("A");
                if(meridian){ className = meridian == 'AM' ? 'alert-primary':'alert-danger'; }
            }else{
                const meridian = moment(new Date(date), "dddd, MMMM D, YYYY h:m A").format("A");
                if(meridian){ className = meridian == 'AM' ? 'alert-primary':'alert-danger'; }
            }

            return className;
        }, 
        
        getLoaReference(employeeId, date) {
            let referenceNumber = null;
            const [startDate] = date.split(' - ');
            const startDateObj = moment(new Date(startDate), 'dddd, MMMM D, YYYY h:m A');
            let meridian = startDateObj.format('A');
            const keyDate = startDateObj.format('YYYY-MM-DD');

            if (globalLoaReference[employeeId] && globalLoaReference[employeeId][keyDate]) {
                const { reference, whole_day, half_day, _meridian, loa_type } = globalLoaReference[employeeId][keyDate];
                if (reference) {
                    if ((half_day && _meridian === meridian) ||
                        (whole_day && half_day === false) ||
                        (loa_type == 4 && half_day === false)) {
                        referenceNumber = reference;
                    }
                }
            }

            return referenceNumber;
        },

        getLoaByAbsentDate(dateFormatted) {
            if (!this.loaMap) return null;

            const key = moment(dateFormatted, "LL").format("YYYY-MM-DD");
            const loa = this.loaMap[key];

            if (!loa) {
                const slot = this.getAbsentSlotInfo(dateFormatted);

                if (slot.type === "whole") return "NO LOA (Whole Day)";
                if (slot.type === "half") return `NO LOA (Half Day - ${slot.meridian})`;

                return "ABSENT";
            }

            let label = loa.reference;

            if (loa.loa_type === 4) {
                const slot = this.getAbsentSlotInfo(dateFormatted);

                if (slot.type === "whole") label += " (Whole Day)";
                else if (slot.type === "half") label += ` (Half Day - ${slot.meridian})`;

                return label;
            }

            if (loa.whole_day) label += " (Whole Day)";
            else if (loa.half_day) label += ` (Half Day - ${loa._meridian})`;

            return label;
        },

        getLoaDayValue(dateFormatted) {
            const key = moment(dateFormatted, "LL").format("YYYY-MM-DD");
            const loa = this.loaMap[key];

            if (loa && loa.loa_type !== 4) {
                if (loa.loa_type === 3 || loa.whole_day) return 1;
                if (loa.loa_type === 2 || loa.half_day) return 0.5;
            }

            const slot = this.getAbsentSlotInfo(dateFormatted);

            return slot.type === "whole" ? 1 : 0.5;
        },

        getAbsentSlotInfo(dateFormatted) {
            const key = moment(dateFormatted, "LL").format("YYYY-MM-DD");

            const logs = (this.row.absent?.attendance_logs || "")
                .split(',')
                .filter(log => log.trim().startsWith(key));

            if (logs.length === 0) {
                return { type: "whole" }; // no logs = whole day absent
            }

            const slots = logs.reduce((acc, log) => {
                const hour = moment(log.split('~')[0].trim(), "YYYY-MM-DD HH:mm:ss").hour();
                hour < 12 ? acc.am = true : acc.pm = true;
                return acc;
            }, { am: false, pm: false });

            if (slots.am && slots.pm) return { type: "whole" };
            if (slots.am) return { type: "half", meridian: "AM" };
            if (slots.pm) return { type: "half", meridian: "PM" };

            return { type: "whole" };
        }
    }
});

const tempSelectorClear = function (tempSelector, disabled=false) {
    if (typeof tempSelector !== "undefined" && tempSelector.length == 1) {
        tempSelector.val([]).trigger("change");
        return tempSelector.prop("disabled", disabled);
    }else{ return false; }
}

if(typeof hrisFilterLateAbsenteeReport !== "undefined" && hrisFilterLateAbsenteeReport.length == 1){
    hrisFilterLateAbsenteeReport.find("select#company")
    .select2({
        width: '100%',
        data: _companies,
        placeholder: "Search",
        allowClear: true,
    }).on("select2:select", function (e) {
        const tempDepartmentSelector = hrisFilterLateAbsenteeReport.find("select#department");
        const tempEmployeeSelector = hrisFilterLateAbsenteeReport.find("select#employee");
        const tempPayrollGroupSelector = hrisFilterLateAbsenteeReport.find("select#payroll_group");

        tempSelectorClear(tempDepartmentSelector);
        tempSelectorClear(tempEmployeeSelector);
        tempSelectorClear(tempPayrollGroupSelector);
        
        $(e.target).validate();
    }).on("select2:unselect", function (e) {
        const tempDepartmentSelector = hrisFilterLateAbsenteeReport.find("select#department");
        const tempEmployeeSelector = hrisFilterLateAbsenteeReport.find("select#employee");
        const tempPayrollGroupSelector = hrisFilterLateAbsenteeReport.find("select#payroll_group");

        setTimeout(() => {
            tempSelectorClear(tempDepartmentSelector, true);
            tempSelectorClear(tempEmployeeSelector, true);
            tempSelectorClear(tempPayrollGroupSelector, true);
        }, 250);
    });

    hrisFilterLateAbsenteeReport.find("select#department")
    .select2({
        width: '100%',
        placeholder: "Search",
        allowClear: true,
        ajax: {
            url: baseUrl('hris/reports/get_select2_department_data'),
            dataType: 'json',
            global: false,
            delay: 250,
            data: function ({ term }) {
                return { q: term, company_id: hrisFilterLateAbsenteeReport.find("select#company").val() };
            },
            processResults: function (data) { return data; }
        }, language: { errorLoading: function () { return "Searching..." } }
    }).on("select2:select", function (e) {
        const tempEmployeeSelector = hrisFilterLateAbsenteeReport.find("select#employee");
        const tempPayrollGroupSelector = hrisFilterLateAbsenteeReport.find("select#payroll_group");

        tempSelectorClear(tempEmployeeSelector);
        tempSelectorClear(tempPayrollGroupSelector);
        
        $(e.target).validate();
    }).on("select2:unselect", function (e) {
        const tempEmployeeSelector = hrisFilterLateAbsenteeReport.find("select#employee");
        const tempPayrollGroupSelector = hrisFilterLateAbsenteeReport.find("select#payroll_group");

        setTimeout(() => {
            tempSelectorClear(tempEmployeeSelector, true);
            tempSelectorClear(tempPayrollGroupSelector, true);
        }, 250);
    });

    hrisFilterLateAbsenteeReport.find("select#employee")
    .select2({
        width: '100%',
        placeholder: "Search",
        ajax: {
            url: baseUrl('hris/reports/get_select2_employee_data'),
            dataType: 'json',
            global: false,
            delay: 250,
            data: function (params) {
                params.q = params.term;
                params.company_id = hrisFilterLateAbsenteeReport.find("select#company").val();
                params.department_id = hrisFilterLateAbsenteeReport.find("select#department").val(); 
                params.employee_status = hrisFilterLateAbsenteeReport.find("input[name='employee_status']:checked").val();
                return params;
            },
            processResults: function (data) {
                return data;
            }
        }, language: { errorLoading: function () { return "Searching..." } }
    });

    hrisFilterLateAbsenteeReport.find("select#payroll_group").select2({
        placeholder: 'Search',
        width: '100%',
        allowClear: true,
        minimumInputLength: 3,
        ajax: {
            url: siteUrl("hris/reports/select_payroll_group"),
            dataType: "json",
            type: 'get',
            delay: 250,
            global: false,
            data: function (params) {
                params.company_id = hrisFilterLateAbsenteeReport.find("select#company").val();
                params.employee_status = hrisFilterLateAbsenteeReport.find("input[name='employee_status']:checked").val();
                return params;
            }, 
            processResults: function (data) {
                return data;
            }
        }
    }).on("select2:select", function (e) {
        const data = e.params.data;
        if (typeof data.employees == "object" && typeof data.employees !== "undefined") {
            const tempEmployeeSelector = hrisFilterLateAbsenteeReport.find("select#employee");
            if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                $.each(data.employees, function (ii, vv) {
                    const tempOption = new Option(vv.text, vv.id, true, true);
                    tempEmployeeSelector.append(tempOption);
                });
                tempEmployeeSelector.prop("disabled", true);
            }
        }
    }).on("select2:unselect", function (e) {
        const tempData = $(this).select2("data");

        const tempEmployeeSelector = hrisFilterLateAbsenteeReport.find("select#employee");
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
}

$.validate({
    form: "#frm-filter-hris-late_absentee_report",
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        let propDisabled = false;
        const currentForm = form[0];

        const tempEmployeeFilter = $(currentForm).find("select#employee");
        if(typeof tempEmployeeFilter !== "undefined"){
            propDisabled = tempEmployeeFilter.is(":disabled");
            if(propDisabled){ tempEmployeeFilter.prop("disabled", false); }
        }

        const formData = $(currentForm).serialize();
        if(propDisabled){ tempEmployeeFilter.prop("disabled", true); }
        filterExport = $(currentForm).serialize();
        $.ajax({
            url: siteUrl("hris/reports/generate_late_absentee_report"),
            type: "post",
            dataType: "json",
            data: formData,
            success: function(json){
                if(json.response){
                    totalEntries = dtTableLateAbsenteeReport.rows().count();
                    filterOptionsLateAbsentee = { ...json.filters };
                    globalLoaReference ={ ...json.loa_reference };
                    rebuildLateAbsenteeTable(vmLateAbsenteeReport.report_type, json.data);

                    setTimeout(function () {
                        const rowCount = dtTableLateAbsenteeReport.rows().count();
                        if (rowCount > 0 && isCollapsedPortlet === true) { isCollapsedPortlet = _tblPortletReports.expand(); }
                    }, 500);
                }else{
                    rebuildLateAbsenteeTable(vmLateAbsenteeReport.report_type, []);
                }
                const { report_type, company_code, filter_by } = json.filters;
                let tempHtml = `
                <div class="row">
                    <div class="col-sm-12 col-12 col-md-3 col-lg-3 col-xl-3">
                        <div class="m-widget1 p-0">
                            <div class="m-widget1__item">
                                <div class="row m-row--no-padding align-items-center">
                                    <div class="col">
                                        <h3 class="m-widget1__title">${report_type}</h3>
                                        <span class="m-widget1__desc">REPORT TYPE</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-12 col-md-2 col-lg-2 col-xl-2">
                        <div class="m-widget1 p-0">
                            <div class="m-widget1__item">
                                <div class="row m-row--no-padding align-items-center">
                                    <div class="col">
                                        <h3 class="m-widget1__title">${filter_by}</h3>
                                        <span class="m-widget1__desc">FILTER BY</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-12 col-md-3 col-lg-3 col-xl-3">
                        <div class="m-widget1 p-0">
                            <div class="m-widget1__item">
                                <div class="row m-row--no-padding align-items-center">
                                    <div class="col">
                                        <h3 class="m-widget1__title">${company_code}</h3>
                                        <span class="m-widget1__desc">COMPANY</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
                $(".dtDetails").empty().html(tempHtml);

                const state = json.response ? "success" : "error";
                toastr[state](json.toastr_msg, "Filtered Late/Absentee Report");
            }
        });

        return false;
    }
});

function late_absentee_column_report(type) {
    const cols = [
        { title: "ID Number", data: "idno", width: "8%" },
        { title: "Employee Name", data: "employee_name", width: "20%" },
        { title: "Department", data: "department", width: "*" },
        { title: "Position", data: "position", width: "*" },
    ];

    if (type === "late") {
        cols.push({
            title: "Total Late",
            data: "reports_total",
            width: "8%",
            className: "text-right"
        });
    }

    if (type === "absentee") {
        cols.push({
            title: "W-LOA",
            data: null,
            width: "8%",
            className: "text-right",
            render: function (data, type, row) {
                const emp_id = row.emp_id;
                const total_absent_w_loa = Object.keys(globalLoaReference[emp_id] ?? {}).length;
                return total_absent_w_loa ? total_absent_w_loa : 0;
            }
        },
        {
            title: "WO-LOA",
            data: null,
            width: "8%",
            className: "text-right",
            render: function (data, type, row) {
                const emp_id = row.emp_id;
                const total_absent_w_loa = Object.keys(globalLoaReference[emp_id] ?? {}).length;
                const total_absent = row.reports_total || 0;
                return total_absent - total_absent_w_loa || 0;
            }
        },
        {
            title: "Total Absent",
            data: "reports_total",
            width: "8%",
            className: "text-right"
        });
    }

    if (type === "late_absentee") {
        cols.push({
            title: "Late",
            data: null,
            width: "8%",
            className: "text-right",
            render: function (data, type, row) {
                return row.late.total_late ? row.late.total_late : 0;
            }
        },
        {
            title: "Abs-w-loa",
            data: null,
            width: "8%",
            className: "text-right",
            render: function (data, type, row) {
                const total_absent_w_loa = row.absent.loa_reference ? Object.keys(row.absent.loa_reference).length : 0;
                return total_absent_w_loa ? total_absent_w_loa : 0;
            }
        },
        {
            title: "abs-wo-loa",
            data: null,
            width: "8%",
            className: "text-right",
            render: function (data, type, row) {
                const total_absent_w_loa = row.absent.loa_reference ? Object.keys(row.absent.loa_reference).length : 0;
                const total_absent = row.absent.total_absent || 0;
                return total_absent - total_absent_w_loa || 0;
            }
        },
        {
            title: "Total Absent",
            data: null,
            width: "8%",
            className: "text-right",
            render: function (data, type, row) {
                return row.absent.total_absent ? row.absent.total_absent : 0;
            }
        });
    }

    cols.push({
        title: "",
        width: "6%",
        className: "text-center",
        render: function(_data, _type, row){
            let classPreview = "btnLateAbsenteePreview";

            let cleanedRow = {};
            let objResponse;
            if (type === "late_absentee") {
                classPreview = "late_and_absentee_preview";

                for (let key in row) {
                    if (typeof row[key] === "string") {
                        cleanedRow[key] = row[key].replace(/[^\p{L}0-9 .,~\-_:\/]/gu, '');
                    } else {
                        cleanedRow[key] = row[key]; // keep objects intact
                    }
                }

                objResponse = encodeURIComponent(JSON.stringify(row));
            } else {
                for (let key in row) {
                    cleanedRow[key] = String(row[key]).replace(/[^\p{L}0-9 .,~\-_:\/]/gu, '');
                }

                objResponse = encodeURIComponent(JSON.stringify(cleanedRow));
            }
            
            return `<button class='btn btn-secondary m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill btnView ${classPreview}' data-raw='${objResponse}'>
                        <i class='fa fa-hourglass-half'></i>
                    </button>`;
        }
    });

    return cols;
}

function rebuildLateAbsenteeTable(type, data = []) {
    const table = $("#table-late_absentee_report");

    if ($.fn.DataTable.isDataTable(table)) {
        dtTableLateAbsenteeReport.clear().destroy();
        table.empty(); // remove old auto-generated thead/tbody
    }

    dtTableLateAbsenteeReport = table.DataTable({
        dom: "<'row'<'col-md-9 dtDetails'><'col-md-3 dtActions m--hide'B>>rt",
        ordering: false,
        paging: false,
        columns: late_absentee_column_report(type),
        data: data,
        autoWidth: false,
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fa fa-download"></i><span class="m--font-boldest">EXPORT EXCEL</span>',
                className: "pull-right exportTempReportAction btnExport",
                messageTop: function(){
                    const {report_type } = filterOptionsLateAbsentee;
                    return report_type.toUpperCase();
                },
                exportOptions: {
                    columns: getExportColumnIndexes(type),
                    stripHtml: true,
                },
                customize: function (xlsx) {
                    export_log(filterExport, `${typeReport} Report`, "excel", totalEntries);
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i><span class="m--font-boldest">PRINT</span>',
                className: "pull-right printTempReportAction btnPrint",
                title: function () {
                    const { filter_by, filter_date, company_code, payroll_group, report_type } = filterOptionsLateAbsentee;
                    const tempTitle = typeof report_type != "undefined" ? report_type: 'Attendance Report';

                    const filterType = `<div>
                        <div class="m--regular-font-size-sm1 mt-1">FILTER BY: ${filter_by}</div>
                        <div class="m--regular-font-size-sm1 mt-1">FILTER DATE: ${filter_date}</div>
                    </div>`;
                    const companyCode = typeof company_code != "undefined" ? `<div>
                        <div class="m--regular-font-size-sm1 mt-1">COMPANY: ${company_code}</div>
                    </div>`:``;
                    const payrollGroup = typeof payroll_group != "undefined" ? `<div>
                        <div class="m--regular-font-size-sm1 mt-1">PAYROLL GROUP: ${payroll_group}</div>
                    </div>`:``;

                    return `<div class="m--regular-font-size-lg1">${tempTitle.toUpperCase()}</div>
                        <div class='mb-3'>${companyCode}${filterType}${payrollGroup}</div>`;
                }, customize: function (win) {
                    const css = `@page { size: portrait; margin: 0.5cm; }
                        table { font-size: 12px; }
                        .print-size-auto{ width: auto }
                        .print-size-8{ width: 8% }
                        .print-size-10{ width: 10% }
                        .print-size-25{ width: 25% }`,
                        head = win.document.head || win.document.getElementsByTagName('head')[0],
                        style = win.document.createElement('style');

                    style.type = 'text/css';
                    style.media = 'print';

                    if (style.styleSheet) { style.styleSheet.cssText = css; } 
                    else { style.appendChild(win.document.createTextNode(css)); }

                    head.appendChild(style);
                    win.document.title = "Late/Absentee Report Printable Page";
                    export_log(filterExport, `${typeReport} Report`, "print", totalEntries);
                }, exportOptions: {
                    columns: getExportColumnIndexes(type),
                    stripHtml: true,
                }
            }
        ],
        drawCallback: function(settings) {
            $(".btnLateAbsenteePreview").on("click", function(){
                const { report_type } = filterOptionsLateAbsentee;
                const tempReportType = report_type.search("Late") > -1 ? "late" : "absentee";

                vmLateAbsenteePreview.report_type = tempReportType;

                const raw = $(this).attr("data-raw");
                const data = JSON.parse(decodeURIComponent(raw));

                vmLateAbsenteePreview.row = data;
                modalLateAbsenteePreview.modal();
            });

            $(".late_and_absentee_preview").on("click", function(){
                const { report_type } = filterOptionsLateAbsentee;
                const tempReportType = report_type.search("Late") > -1 ? "late" : "absentee";

                vm_late_and_absentee.report_type = tempReportType;
                const data = JSON.parse(decodeURIComponent($(this).data("raw")));

                vm_late_and_absentee.row = data;
                late_and_absentee_view.modal();
            });

            const api = this.api();
            const tempData = api.data();
            const btnPrint = $(settings.nTableWrapper).find(".printTempReportAction");
            const btnExport = $(settings.nTableWrapper).find(".exportTempReportAction");
            const dtActions = $(settings.nTableWrapper).find(".dtActions");

            drawCallbackRequestAction(btnPrint, dtActions, tempData);
            drawCallbackRequestAction(btnExport, dtActions, tempData);
        }
    });
}

function getExportColumnIndexes(type){
    const map = {
        late_absentee: [0,1,2,3,4,5],
        late: [0, 1, 2, 3, 4],
        absentee: [0, 1, 2, 3, 4, 5 ,6]
    };
    return map[type] ?? [];
}

_tblPortletReports.on('afterExpand', function () {
    setTimeout(function () { isCollapsedPortlet = true; }, 500);
}).on('afterCollapse', function () {
    setTimeout(function () { isCollapsedPortlet = false; }, 500);
});


const drawCallbackRequestAction = function (btnAction, dtActions, tempData) {
    if (!btnAction || !dtActions) return;
    btnAction.addClass("btn m-btn btn-brand m-btn--icon m--hide animated fadeIn mr-1");

    const showElements = () => {
        btnAction.removeClass('m--hide');
        dtActions.removeClass('m--hide');
    };

    const hideElements = () => {
        btnAction.addClass('m--hide');
        dtActions.addClass('m--hide');
    };

    if (tempData.length > 0) { showElements(); } 
    else { hideElements(); }       
}

const resetFilterLateAbsenteeReport = function(event){
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        const select2 = form.find("#employee, #payroll_group, #company, #department, [name='filter_month'], [name='filter_year']");
        if (typeof select2 !== "undefined" && select2.length > 0) {
            $.each(select2, function (_i, v) {
                const multi = $(v)[0].multiple;
                if (multi) { $(v).val([]).trigger("change").prop("disabled", false); } 
                else { $(v).val("").trigger("change"); }
            });
        }
        const dateRange = form.find("#date-range");
        if (typeof dateRange !== "undefined" && dateRange.length == 1) {
            const attr = form.attr('id');
            if(attr == "frm-filter-hris-late_report"){
                vmLateReport.renderRangeDatePicker();
            }else if(attr == "frm-filter-hris-absentee_report"){
                vmAbsenteeReport.renderRangeDatePicker();
            }
            dateRange.val("");
        }

        const select2Containers = form.find("#employee, #payroll_group, #department");
        if (typeof select2Containers !== "undefined" && select2Containers.length > 0) {
            $.each(select2Containers, function (i, v) {
                tempSelectorClear($(v), true);
            });
        }

        form[0].reset();
    }

}

const submitLateAbsenteeFilterForm = function(event){
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        $(form).validate();
        $(form).submit(); 
    }
}

const vmLateAction = new Vue({
    el: "#lateReportActions",
    data: { has_actions: false },
});

const vmAbsentAction = new Vue({
    el: "#absentReportActions",
    data: { has_actions: false },
});

if(typeof _currentActions != "undefined" && _currentActions.includes("advance_search")){
    vmLateAction.has_actions = true;
    vmAbsentAction.has_actions = true;
}

$("#toggleCollapse").on("click", function(){
    if(isCollapsedPortlet){
        _tblPortletReports.expand();
        isCollapsedPortlet = false;
    }else{        
        _tblPortletReports.collapse();
        isCollapsedPortlet = true;
    }
});

async function export_log(datas, type, name, count) {
    const filters = {};
    datas.split('&').forEach(pair => {
        const [key, value] = pair.split('=');
        filters[key] = decodeURIComponent(value);
    });

    try {
        const response = await $.ajax({
            url: siteUrl("hris/reports/log_export") + '?t=' + new Date().getTime(),
            type: "POST",
            data: { 
                filters,
                type: type,
                name: name,
                count: count,
                csrf_token: _csrf_hash 
            },
            // dataType: 'json'
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            },
        });
        return response;
    } catch (error) {
        console.error('Error exporting log:', error);
        throw error;
    }
}