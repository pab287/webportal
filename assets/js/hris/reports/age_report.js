let dropdownEl = null;
let search_val = "";
let thisMonth = moment();
let _companies = [], _stations = [], _departments =[];
let station = 0;
let company = 0;
let department = 0;
let ageRange ="18-25";
if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){ _companies = _tempContentData.company; }
    if(typeof _tempContentData.station !== "undefined" && _tempContentData.station.length > 0){ _stations = _tempContentData.station; }
    if(typeof _tempContentData.department !== "undefined" && _tempContentData.department.length > 0){ _departments = _tempContentData.department; }
}

const tblHrisAgeReport = $('#hris_age_reports')
    .DataTable({
        dom: "<'row mb-3'<'col-xl-3 col-lg-3 col-md-3 col-sm-12 exportDropdown'><'col-xl-9 col-lg-9 col-md-9 col-sm-12 p-0 exportSearch'f>>" +
            "<'row'<'col-12'rt>>" +
            "<'row mt-3'<'col-xl-6 col-lg-6 col-md-6 col-sm-12 pl-0'l><'col-xl-6 col-lg-6 col-md-6 col-sm-12'p>>",
        serverSide: true,
        ordering: true,
        searching: false,
        processing: true,
        order:[0,'desc'],
        ajax: {
            url: baseUrl('hris/reports/get_age_report'),
            type: 'post',
            dataType: 'json',
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                d.company = company;
                d.station = station;
                d.department = department;
                d.ageRange = ageRange;
            }
        },
        columns: [
            { data: 'empid', visible: false },
            {
                data: 'firstname', width: '15%',
                title: 'EMPLOYEE NAME',
                render: function(data, type, row, meta) {
                    const middleInitial = row['middlename'] ? 
                        row['middlename'].charAt(0) + '.' : '';
                    
                    const suffix = row['suffix'] ? 
                        ' ' + row['suffix'] : '';
            
                    return `${row['firstname']} ${middleInitial}${middleInitial ? ' ' : ''}${row['lastname']}${suffix}`;
                }
            },
            { data: 'company', title: 'COMPANY',},
            { data: 'department', title: 'DEPARTMENT',},
            { data: 'position', title: 'Position',},
            { data: 'station', title: 'Station',
                render: function(data, type, row) {
                    return data ? data : '---';
                }
            },
            { data: 'birthday', title: 'Age',
                render: function(data, type, row, meta) {
                    return calculateAge(data);
                }
            },            
            { data: 'birthday',title: 'Birthdate',
                render: function(data, type, row, meta) {
                    return formatDate(data);
                }
            },
            { data: 'hired_date',title: 'Date hired',
                render: function(data, type, row, meta) {
                    return formatDate(data);
                }
            },
            { data: 'tin_no', title: 'TIN', orderable: false,  width: '7%',
                render: function(data, type, row) {
                    return data ? data : "---";
                }
            },
            { data: 'sss_no', title: 'SSS', orderable: false, width: '7%',
                render: function(data, type, row) {
                    return data ? data : "---";
                }
            },
            { data: 'pagibig_no', title: 'PAG-IBIG', orderable: false, width: '7%',
                render: function(data, type, row) {
                    return data ? data : "---";
                }
            },
            { data: 'phealth_no', title: 'PhilHealth', orderable: false, width: '7%',
                render: function(data, type, row) {
                    return data ? data : "---";
                }
            },
        ],
        initComplete: function () {
            const dropdown = '' +
            '       <div class="m-dropdown m-dropdown--inline m-dropdown--align-left" ' +
            '             data-dropdown-toggle="hover" aria-expanded="true">' +
            '            <button class="m-dropdown__toggle btn btn-success dropdown-toggle export-as">' +
            '                EXPORT AS' +
            '            </button>' +
            '            <div class="m-dropdown__wrapper">' +
            '                <div class="m-dropdown__inner">' +
            '                    <div class="m-dropdown__body">' +
            '                        <div class="m-dropdown__content">' +
            '                            <ul class="m-nav">' +
            '                                <li class="m-nav__item">' +
            '                                    <a id="export-as-excel" style="cursor:pointer;" ' +
            '                                       onclick="exportAs(\'excel\'); return false;" class="m-nav__link">' +
            '                                        <i class="m-nav__link-icon fa fa-file-excel-o m--font-success"></i>' +
            '                                        <span class="m-nav__link-text" style="text-transform: none;">' +
            '                                           Excel File' +
            '                                        </span>' +
            '                                    </a>' +
            '                                </li>' +
            '                                <li class="m-nav__item">' +
            '                                    <a id="export-as-pdf" style="cursor:pointer;" ' +
            '                                       onclick="exportAs(\'pdf\'); return false;" class="m-nav__link">' +
            '                                        <i class="m-nav__link-icon fa fa-file-pdf-o m--font-danger"></i>' +
            '                                        <span class="m-nav__link-text" style="text-transform: none;">' +
            '                                          PDF File' +
            '                                        </span>' +
            '                                    </a>' +
            '                                </li>' +
            '                                <li class="m-nav__item">' +
            '                                    <a id="export-as-print" style="cursor:pointer;" ' +
            '                                       onclick="exportAs(\'print\'); return false;" class="m-nav__link">' +
            '                                        <i class="m-nav__link-icon fa fa-print m--font-info"></i>' +
            '                                        <span class="m-nav__link-text" style="text-transform: none;">' +
            '                                          Print' +
            '                                        </span>' +
            '                                    </a>' +
            '                                </li>' +
            '                            </ul>' +
            '                        </div>' +
            '                    </div>' +
            '                </div>' +
            '            </div>' +
            '        </div>';

        $(dropdown).appendTo("#hris_age_reports_wrapper .exportDropdown");
        dropdownEl = $(".m-dropdown__toggle.export-as");
        const filterDiv = $('<div>').addClass('dataTables_filter');
        const searchInput = $('<input>').attr('type', 'text').addClass('form-control').attr('placeholder', 'Search...').attr('id', 'generalSearch');
        filterDiv.append(searchInput);
        $(filterDiv).appendTo("#hris_age_reports_wrapper .exportSearch");
        $('#generalSearch').donetyping(function(callback) {
            search_val = $(this).val();
            tblHrisAgeReport.ajax.reload();
          },1000,3);
          $("#generalSearch").on('keyup', function (e) {
            var val = $(this).val();
            if (val == ""){
                search_val="";
                tblHrisAgeReport.ajax.reload();
            }
        });
        },
    });

    function exportAs(type) {
        dropdownEl.addClass("m-btn--custom m-loader m-loader--light m-loader--left");
        let clearHere = false;
    
        setTimeout(() => {
            switch (type) {
                case "excel":
                    tblHrisAgeReport.button(".buttons-excel").trigger();
                    break;
                case "pdf":
                    tblHrisAgeReport.button(".buttons-pdf").trigger();
                    break;
                case "print":
                    tblHrisAgeReport.button(".buttons-print").trigger();
                    break;
            }
        }, 150);
    }

    async function getExportData(e, dt, node, config, self, url, type) {
        const info = dt.page.info();
        const totalRecords = info.recordsTotal;
        const result = await $.ajax({
            url,
            type: "POST",
            dataType: "JSON",
            data: {
                csrf_token : _csrf_hash,
                total : totalRecords,
            },
            success: function (response) {
                $.fn.dataTable.ext.buttons[type].action.call(self, e, dt, node, config);
            }
        });
        return result;
    }


    function formatDate(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '';
    
        const options = {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        };
        
        return date.toLocaleDateString('en-US', options);
    }

    function calculateAge(birthdate) {
        if (!birthdate) return '';
        
        const birthDate = new Date(birthdate);
        if (isNaN(birthDate.getTime())) return '';
    
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        
        // Adjust age if birthday hasn't occurred this year
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        return `${age} Years Old`;
    }


    $("#company").select2({
        width: '100%',
        data: _companies,
        placeholder: 'Select an option',
        allowClear: true,
    })
    .on("select2:select", function(e) {
        const { id } = e.params?.data || {};
        company = id;
        tblHrisAgeReport.ajax.reload();
    }).on("select2:unselecting", function(e) {
        company = 0; 
        tblHrisAgeReport.ajax.reload();
    });

    $("#station").select2({
        width: "100%",
        placeholder: "Select an option",
        data: _stations,
        allowClear: true,
    }).on("select2:select", function(e) {
        const { id } = e.params?.data || {};
        station = id;
        tblHrisAgeReport.ajax.reload();
    }).on("select2:unselecting", function(e) {
        station = 0;
        tblHrisAgeReport.ajax.reload();
    });

    $("#department").select2({
        width: "100%",
        placeholder: "Select an option",
        data: _departments,
        allowClear: true,
    }).on("select2:select", function(e) {
        const { id } = e.params?.data || {};
        department = id || 0;
        tblHrisAgeReport.ajax.reload();
    }).on("select2:unselecting", function(e) {
        department = 0;
        tblHrisAgeReport.ajax.reload();
    });

    // $("#age_range").select2({
    //     width: "100%",
    //     minimumResultsForSearch: -1,
    //     placeholder: "Select an option",
    //     allowClear: true,
    // }).on("select2:select select2:unselect", function(e){
    //     const { id } = e.params?.data || {};
    //     ageRange = id || null;
    //     tblHrisAgeReport.ajax.reload();
    // });

    let slider = document.getElementById('age_range_slider');
    noUiSlider.create(slider, {
        start: [18, 25],
        step: 1,
        connect: true,
        range: {
            'min': 18,
            'max': 85
        },
        format: {
            to: function (value) {
                return Math.round(value);
            },
            from: function (value) {
                return Number(value);
            }
        }
    });

    slider.noUiSlider.on('update', function (values, handle) {
        if (handle === 0) {
            $('#age_range_min').text(values[0]);
        } else {
            $('#age_range_max').text(values[1]);
        }
    });

    let ageRangeTimeout;
    slider.noUiSlider.on('change', function (values) {
        ageRange = values[0] + '-' + values[1];
        clearTimeout(ageRangeTimeout);
        ageRangeTimeout = setTimeout(function() {
            tblHrisAgeReport.ajax.reload();
        }, 2000); 
    });