var search_val = "";

var tblPayrollSheet = $("#table-payroll").DataTable({
    dom: '<"toolbar">frtlip',
	  serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("payroll/payroll_masterfile/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function(d){
			d.csrf_token = _csrf_hash,
            d.search['value'] = search_val
		}
    },
    searching: false,
    columns: [
        { data: "date_from" },
        { data: "date_to" },
        { data: "company" },
        {data: null, width: "5%", className: "text-center",},
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id);
            },
        }
    ]
});

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += "<a class='m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl btnNew' href='#' data-toggle='modal' data-target='#modal-payroll'><i class='la la-expand'></i></a>";
        return _actionButton;
    } else {
        return false;
    }
}

$('#generalSearch').donetyping(function(callback) {
	search_val = $(this).val();
    tblPayrollSheet.ajax.reload();
});

$("#reload_dtTbl").on("click",function(){
	tblPayrollSheet.ajax.reload();
});
