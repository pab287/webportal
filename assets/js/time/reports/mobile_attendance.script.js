const modalPreview = $("#modal-preview-mobile_attendance");
let _companies = [];
let psEmployeeGroup = [];
//const $tempDate = "2025-07-13";
const defaultDate = moment().subtract('1', 'days').format("MMM. DD, YYYY");
const nDate = defaultDate + " - " + defaultDate;
$("#date-range").val(nDate);

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.companies !== "undefined" && _tempContentData.companies.length > 0){ _companies = _tempContentData.companies; }
}
$("#company")
.select2({
    placeholder: 'Select an option',
    width: '100%',
    data: _companies,
    allowClear: true,
}).on("select2:select", function (data) {
    /*** $(data.target).validate(); ***/
});

$("#employees")
.select2({
    placeholder: 'Select an option',
    width: '100%',
    ajax: {
        url: baseUrl("gcctime/attendance/select_employee"),
        dataType: "json",
        delay: 250,
        global: false,
        processResults: function (data) {
            return data;
        }
    }
});

$("#payroll_group").select2({
    placeholder: 'Select an option',
    width: '100%',
    ajax: {
        url: baseUrl("gcctime/attendance/select_payroll_group"),
        dataType: "json",
        type: 'get',
        delay: 250,
        global: false,
        data: function (params) {
            params.company_id = $("form#frm-filter select#company").val();
            return params;
        },
        processResults: function (data) {
            return data;
        }
    }
}).on("select2:select", function (e) {
    const _this = this;
    const tempVal = $(_this).val();
    const data = e.params.data;
    let employees = [];
    if (typeof data.employees == "object" && typeof data.employees !== "undefined") { employees = data.employees; }
    if (tempVal.length > 1) {
        $.ajax({
            url: baseUrl("gcctime/attendance/get_payroll_group_multiple"),
            type: "post",
            dataType: "json",
            data: { group_id: tempVal, [_csrf_token]: _csrf_hash },
            success: function (json) {
                if (json.response) {
                    const tempData = json.data;
                    if (typeof tempData == "object" && typeof tempData !== "undefined") {
                        const tempEmployeeSelector = $("form#frm-filter select#employees");
                        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                            tempEmployeeSelector.empty();
                            $.each(tempData, function (ii, vv) {
                                const tempOption = new Option(vv.text, vv.id, true, true);
                                tempEmployeeSelector.append(tempOption);
                            });
                            tempEmployeeSelector.prop("disabled", true);
                        }
                    }
                }
            }
        });
    } else {
        if (typeof employees == "object" && typeof employees !== "undefined") {
            const tempEmployeeSelector = $("form#frm-filter select#employees");
            if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                tempEmployeeSelector.empty();
                $.each(employees, function (ii, vv) {
                    const tempOption = new Option(vv.text, vv.id, true, true);
                    tempEmployeeSelector.append(tempOption);
                });
                tempEmployeeSelector.prop("disabled", true);
            }
        }
    }
    if (typeof data.text !== "undefined" && data.text) {
        const tempEmpGroup = data.text;
        let tempIsInArray = $.inArray(tempEmpGroup, psEmployeeGroup);
        if (tempIsInArray == -1) {
            psEmployeeGroup.push(tempEmpGroup);
        }
    }

}).on("select2:unselect", function (e) {
    const _this = this;
    const tempValUnselected = $(_this).val();
    const data = e.params.data;
    if (tempValUnselected.length == 0) {
        const tempEmployeeSelector = $("form#frm-filter select#employees");
        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
            tempEmployeeSelector.prop("disabled", false);
        }
    } else {
        $.ajax({
            url: baseUrl("gcctime/attendance/get_payroll_group_multiple"),
            type: "post",
            dataType: "json",
            data: { group_id: tempValUnselected, [_csrf_token]: _csrf_hash },
            success: function (json) {
                if (json.response) {
                    const tempData = json.data;
                    if (typeof tempData == "object" && typeof tempData !== "undefined") {
                        const tempEmployeeSelector = $("form#frm-filter select#employees");
                        if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                            tempEmployeeSelector.empty();
                            $.each(tempData, function (ii, vv) {
                                const tempOption = new Option(vv.text, vv.id, true, true);
                                tempEmployeeSelector.append(tempOption);
                            });
                            tempEmployeeSelector.prop("disabled", true);
                        }
                    }
                }
            }
        });
    }

    if (typeof data.text !== "undefined" && data.text) {
        const tempEmpGroup = data.text;
        let tempIsInArray = $.inArray(tempEmpGroup, psEmployeeGroup);
        if (tempIsInArray !== -1) {
            const index = psEmployeeGroup.indexOf(tempEmpGroup);
            if (index > -1) { psEmployeeGroup.splice(index, 1); }
        }

    }
});

$("#date-picker").daterangepicker({
    buttonClasses: 'm-btn btn',
    applyClass: 'btn-primary',
    cancelClass: 'btn-secondary',
    locale: { format: 'MM/DD/YYYY' },
    maxDate: moment().format("MM/DD/YYYY")
}).on('apply.daterangepicker', function (ev, picker) {
    $("#date-range").val(picker.startDate.format('MMM. DD, YYYY') + ' - ' + picker.endDate.format('MMM. DD, YYYY'))
    /*** $("#date-range").validate(); ***/
});

const dtTable = $("#mobile_attendance_logs").DataTable({
    dom: 'frtlip',
    searching: false,
    serverSide: true,
    processing: true,
    autoWidth: false,
    order: [[3, "desc"]],
    ajax: {
        url: baseUrl("gcctime/attendance/get_mobile_attendance_list"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d[_csrf_token] = _csrf_hash;
            d["date_range"] = $("#date-range").val();
            return d;
        }
    }, columns: [{
            data: "employee_name", visible: false,
            render: function (data, type, row) {
                return row.employee_name;
        }},
        { data: "biometricno", visible: false },
        
        { data: "updated_at", orderable: false, visible: false,
            render: function (data) {
                return moment(data).format("MM/DD/YYYY hh:mm A");
            }
        },
        { data: "date", width: "10%", className: "text-center", render: function (data) { return moment(data).format("MM/DD/YYYY"); } },
        { data: "time", width: "10%", className: "text-center", render: function (data) { return moment(data, "HH:mm:ss").format("hh:mm A"); } },
        { data: "time_status", width: "14%", className: "text-center", render: function (data) { return data != null ? data : "---"; } },
        { data: "in_location", width: "11%", className: "text-center", orderable: false },
        { data: "address", width: "*", orderable: false },
        { data: null, width: "7%", className: "text-center", orderable: false, render: function (data, type, row) {
            const rawData = JSON.stringify(row);
            return `<button class="btn btn-sm btn-outline-info m-btn m-btn--icon m-btn--icon-only m-btn--custom m-btn--pill m-btn--air btnView btnPreviewMobileAttendance" data-row='${rawData}'
            data-toogle="m-tooltip" data-placement="top" title="View Mobile Attendance">
            <i class="la la-map-marker"></i>
            </button>`;
        }},
    ],
        drawCallback: function (settings) {
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
                if (last !== group) {
                    let cbElement = '';
                    $(rows).eq(i).before(
                        `<tr class="group tr-header-${row.emp_id}">
                            <td colspan="12">
                                ${cbElement}
                                <span style="font-weight: normal; color: whitesmoke;">${row.biometricno}</span>
                                <span class="ml-2">${group}</span>
                            </td>
                        </tr>`
                    );

                    last = group;
                }
            });

            const { geofence } = settings.json;
            vmPreviewMobileAttendance.geofences = { ...geofence };
        }
});

const vmPreviewMobileAttendance = new Vue({
    el: "#preview-mobile_attendance", 
    data: { row: {}, map: null, marker: null, geofences: {} },
    methods: {
        dateTimeFormatter: function(){
            const { date, time } = this.row;
            const dateTime = date + " " + time;
            return moment(new Date(dateTime), "YYYY-MM-DD HH:mm:ss").format("LLL");
        }, initTempMap: function () {
            const _this = this;
            const { latitude: lat, longtitude: lng, employee_id } = _this.row;
            const location = { lat: parseFloat(lat), lng: parseFloat(lng) };

            _this.map = new google.maps.Map(_this.$refs.googleMap, {
                center: location,
                zoom: 18,
                fullscreenControl: false,
                streetViewControl: false,
                zoomControl: false,
                gestureHandling: "none",
                mapTypeId: 'satellite',
                mapTypeControl: false,
                mapId: "61eadc851067d069",
            });

            const { AdvancedMarkerElement } = google.maps.marker;
            _this.marker = new AdvancedMarkerElement({
                position: location,
                map: _this.map,
                title: "Mobile Attendance Location"
            });

            if(typeof _this.geofences[employee_id] != "undefined" && _this.geofences[employee_id].length > 0){
                const polygonData = _this.geofences[employee_id];
                polygonData.forEach(coords => {
                    coords = coords.map(item => { return { lat: parseFloat(item.lat), lng: parseFloat(item.lng) }; });
                    return new google.maps.Polygon({
                        paths: coords,
                        strokeColor: "#ffd000ff",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: "#00ff6aff",
                        fillOpacity: 0.35,
                        map: _this.map
                    });
                });

            }
        }
    }
});
jQuery(document).on("click", ".btnPreviewMobileAttendance", function () {
    const _self = $(this);
    const data = _self.data("row");
    vmPreviewMobileAttendance.row = { ...data };
    vmPreviewMobileAttendance.initTempMap();
    modalPreview.modal("show"); 
});

$.validate({
    form: "#frm-filter",
    lang: "en",
    onSuccess: function (form) {
        const currentForm = form[0];
        let propDisabled = false;
        const tempEmployeeFilter = $(currentForm).find("select#employee");
        if(typeof tempEmployeeFilter !== "undefined"){
            propDisabled = tempEmployeeFilter.is(":disabled");
            if(propDisabled){ tempEmployeeFilter.prop("disabled", false); }
        }

        const formData = $(currentForm).serialize();
        if(propDisabled){ tempEmployeeFilter.prop("disabled", true); }
        console.log(formData);
        return false;
    }
});

const resetFilter = function (event) {
    const form = $(event).closest("form");
    if (typeof form !== "undefined" && form.length == 1) {
        const select2 = form.find("#employees, #payroll_group");
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
        psEmployeeGroup = [];
    }
}