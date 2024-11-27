$.ajax({
    url: baseUrl("archiving/count_document/"),
    type: "GET",
    dataType: "JSON",
    success: function (data) {
        var percent = parseInt((data.count / data.total) * 100);
        $("#document").append(data.count);
        $("#progress_document").css("width", percent + "%");
        $("#percent_document").append(percent + "%");
    }
});

$.ajax({
    url: baseUrl("archiving/count_recent/"),
    type: "GET",
    dataType: "JSON",
    success: function (data) {
        var percent = parseInt((data.count / data.total) * 100);
        $("#archive").append(data.count);
        $("#progress_archive").css("width", percent + "%");
        $("#percent_archive").append(percent + "%");
    }
});
var search_val = "";
var tblClass = $("#table-class").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("archiving/get_count/"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.search['value'] = search_val
        }
    },
    searching: true,
    columns: [

        {data: "description", width: "50%"},
        {data: "total", width: "50%"},

    ],

});