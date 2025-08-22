let tblExpiringProbees = null;
let search_val = "";

tblExpiringProbees = $('#table-expiring-probees').DataTable({
    dom: 'frtlip',
    buttons: [
        {
            extend: 'excelHtml5',
            title: 'EXPIRING PROBATIONARY EMPLOYEES',
        },
    ],
    serverSide: false,
    processing: true,
    searching: true,
    ordering: true,
    destroy: true,
    data: [],
    columns: [
        {
            data: 'company',
            width: '10%'
        },
        {
            data: 'position',
            width: '10%'
        },
        {
            data: 'idno',
            width: '7%'
        },
        {
            data: 'name',
            width: '15%'
        },
        {
            data: 'head',
            width: '15%'
        },
        {
            data: 'date_hired',
            width: '8%',
            render: function (data) {
                return moment(data).format('MMM DD, YYYY')
            }
        },
        {
            data: 'firstEvaluation',
            width: '8%',
            render: function (data) {
                return moment(data).format('MMM DD, YYYY')
            }
        },
        {
            data: 'finalEvaluation',
            width: '8%',
            render: function (data) {
                return moment(data).format('MMM DD, YYYY')
            }
        },
        {
            data: 'end_of_contract',
            width: '8%',
            render: function (data) {
                return data && data != "0000-00-00" ? moment(data).format('MMM DD, YYYY') : "";
            }
        },
        {
            data: 'daysBeforeEvaluation',
            width: '5%',
            className: 'text-right',
            render: function (data) {
                return parseFloat(data) < 0 ? '<span class="text-danger m--font-bolder">' + data + '</span>' : '<span class="font-weight-bold">' + data + '</span>'
            }
        },
    ],
    columnDefs: [{
        searchable: false,
        orderable: false,
        targets: 0
    }],
    order: [[5, 'asc']],
    pageLength: 10,
    initComplete: function () {
        $('#table-expiring-probees_wrapper').find('input[type="search"]').removeClass('form-control-sm');
    },
    drawCallback: function (settings) {
        swal.close();
    }
});

loadExpiringProbees();

function loadExpiringProbees() {
    // Show loader modal
    Swal.fire({
        title: 'Loading...',
        text: 'Fetching expiring employees, please wait.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: baseUrl('hris/reports/get_expiring_employees/?work_status=PROBATIONARY'),
        type: 'POST',
        dataType: 'json',
        data: { csrf_token: _csrf_hash },
        success: function (response) {
            const rows = (response && Array.isArray(response.data)) ? response.data : [];
            tblExpiringProbees.clear().rows.add(rows).draw(false);

            // Close only AFTER datatable finishes rendering
            tblExpiringProbees.one('draw', function () {
                Swal.close();
            });
        },
        error: function (xhr, status, error) {
            console.error("Failed to load data:", error);
            Swal.fire("Error", "Failed to fetch data.", "error");
        }
    });
}

$("#ExportExcel").on("click", function() {
    tblExpiringProbees.button( '.buttons-excel' ).trigger();
});

$("#ExportCSV").on("click", function() {
    tblExpiringProbees.button( '.buttons-csv' ).trigger();
});

$("#ExportPDF").on("click", function() {
    tblExpiringProbees.button( '.buttons-pdf' ).trigger();
});