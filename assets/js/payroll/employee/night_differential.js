const dtTable = $('#tbl-payroll-night-differential').DataTable({
    dom: '<"toolbar">frtlip',
    processing: true,
    serverSide: true,
    ordering: false,
    ajax: {
        url: baseUrl('payroll/employee/get_employee_nightdiff_list'),
        type: 'POST',
        dataType: 'JSON',
        data: function (d) { d.csrf_token = _csrf_hash; }
    },
    columns: [
        { data: 'idno', title: "ID No" },
        { data: 'employee_name', title: "Employee Name" },
        { data: 'company_code', title: "Company" },
        { data: 'allow_ndiff', title: "Allow NDiff." },
        { data: 'last_updated_at', title: "Last Updated At", 
            render: function (data) {
            return data != null ? data : '---';
        }},
        { data: null, title: 'Action',
            render: function (data, type, row) {
            return '---';
        }}
    ]
});