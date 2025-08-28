const modalPreview = $("#modal-preview-mobile_attendance");
let _companies = [];
let psEmployeeGroup = [];
let globalFormData = null;
const defaultDate = moment().subtract('1', 'days').format("MMM. DD, YYYY");
const nDate = defaultDate + " - " + defaultDate;
$("#date-range").val(nDate);
toastr.options = { "positionClass": "toast-bottom-right" }
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
    $(data.target).validate();
    const form = $(data.target).closest("form");
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
    dom: '<"row"<"outsideLocation col-sm-12 col-md-8 col-lg-8"><"col-sm-12 col-md-4 col-lg-4"f>>rtlip',
    destroy: true,
    serverSide: false,
    processing: false,
    autoWidth: false,
    ordering: false,
    columns: [{
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
            const tempClass = row.in_location.toLowerCase() == 'no' ? 'btn-outline-warning' : 'btn-outline-success';
            return `<button class="btn btn-sm ${tempClass} m-btn m-btn--icon m-btn--icon-only m-btn--custom m-btn--pill m-btn--air btnView btnPreviewMobileAttendance" data-row='${rawData}'
            data-toogle="m-tooltip" data-placement="top" title="View Mobile Attendance">
            <i class="la la-map-marker"></i>
            </button>`;
        }},
    ], createdRow: function (row, data) {
        const { in_location } = data;
        if(in_location.toLowerCase() == 'no'){ $(row).addClass('bg-danger text-white'); }
    }, initComplete: function () {
        $("#mobile_attendance_logs_filter input[type='search']").removeClass("form-control-sm");
    },
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
            const groupKey = row.biometricno + "--" + moment(row.date).format("YYYYMMDD");
            if (last !== groupKey) {
                let cbElement = '';
                if(row.in_location.toLowerCase() == 'no'){ cbElement = '<i class="la la-exclamation-circle m--regular-font-size-lg2 mr-2"></i> '; }
                $(rows).eq(i).before(
                    `<tr class="group tr-header-${row.employee_id}">
                        <td colspan="12">
                            ${cbElement}
                            <span style="font-weight: normal; color: whitesmoke;">${row.biometricno}</span>
                            <span class="ml-2">${group}</span>
                        </td>
                    </tr>`
                );

                last = groupKey;
            }
        });
        setTimeout(mapUnblockUI, 100);
    }
});

const vmFilter = new Vue({
    el: "#statusFilter",
    data: { status: "all", count: 0 },
    watch: {
        status() {
            mapBlockUI();
            setTimeout(()=>{ 
                dtTable.draw();
            }, 100);
        }
    }
});

$.fn.dataTable.ext.search.push(function(_settings, data, _dataIndex) {
    const filter = vmFilter.status;
    const inLocation = data[6];
    if (filter === "all") return true;
    return inLocation === filter; 
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
        const tempEmployeeFilter = $(currentForm).find("select#employees");
        if(typeof tempEmployeeFilter !== "undefined"){
            propDisabled = tempEmployeeFilter.is(":disabled");
            if(propDisabled){ tempEmployeeFilter.prop("disabled", false); }
        }

        const formData = $(currentForm).serialize();
        if(propDisabled){ tempEmployeeFilter.prop("disabled", true); }
        getScriptRendering(currentForm.action, formData, currentForm);
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

const getScriptRendering = function (formUrl, formData, currentForm) {
    $.ajax({
        url: formUrl,
        type: "POST",
        dataType: "JSON",
        data: formData,
        beforeSend: function () {
            $(currentForm)
                .find(".btn-submit")
                .addClass("m-btn--custom m-loader m-loader--light m-loader--right")
                .prop("disabled", true);
        },
        success: function (json) {
            const { geofence, outside_location } = json;
            vmPreviewMobileAttendance.geofences = {};
            vmFilter.count = 0;
            if (json.response) {
                vmPreviewMobileAttendance.geofences = { ...geofence }
                vmFilter.count = outside_location;
                vmFilter.status = "all";
                if(json.data.length > 0){
                    const tempHtml = outside_location > 0 ? `A total of <b>${outside_location}</b> attendance record(s) detected outside of site location.`
                    : `A total of <b>${json.data.length}</b> attendance record(s) found.`;
                    $(".outsideLocation").html(tempHtml).addClass("text-uppercase m-animate-fade-in");
                }else{
                    $(".outsideLocation").html("").removeClass("text-uppercase");
                }
                dtTable.clear().rows.add(json.data).draw(false);
                if(parseInt(outside_location) > 0){
                    setTimeout(() => {
                        Swal.fire({
                            title: 'Outside Site Location Detected!',
                            html: `A total of <b>${outside_location}</b> attendance record(s) detected outside of site location.`,
                            icon: 'warning',
                        });
                    }, 1000);
                }
            }else{
                $(".outsideLocation").html("").removeClass("text-uppercase");
                dtTable.clear().rows.add([]).draw(false);
                toastr.error("No data found!", "Filter Search", 5000);
            }
        }
    });
}