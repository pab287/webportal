var search_val = "";

var tblLoadLogs = $("#load-logs").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("ams/inventory/inventory_log/"),
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
        { data: "name"},
        { data: "notification"},
        { 
            data: "created_at",
            render: function (data) {
                return dateFormatter(new Date(data));
            }
        },
    ],
});

//custom global search init
$('#generalSearch').donetyping(function(callback) {
	search_val = $(this).val();
    tblLoadLogs.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
	tblLoadLogs.ajax.reload();
});


function dateFormatter(date) {
    const month_names = ["January", "Febuary", "March",
        "April", "May", "June",
        "July", "August", "September",
        "October", "November", "December"];

    const day = date.getDate();
    const month_index = date.getMonth();
    const year = date.getFullYear();

    return year + " " + month_names[month_index] + ", " + day;
}