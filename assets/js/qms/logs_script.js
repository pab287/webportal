var logTbl = $("#table-logs");
var search_val = '';

if(typeof logTbl != 'undefined'){
    var table = logTbl.DataTable({
        searching: false,
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        order: [[3, 'desc']],
        ajax: {
            url: baseUrl("qms/logs/get_logs_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                d.search['value'] = search_val
            }
        },
        columns: [
            { data: 'title', width: '30%' },
            { data: "employee_name" },
            { data: "type", orderable: false },
            { data: "added_dt", 
                render: function(data, type, row, meta){
                    return moment(data).format('LLL');
                }
            },
        ],
    });

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        table.ajax.reload();
    });
}