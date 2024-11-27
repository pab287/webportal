var search_val = "";

var tblAllowance = $("#table-allowance").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("payroll/allowance/archive_list/"),
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
        { data: "code" },
        { data: "allowance_name"},
        { data: null, width: "15%", className: "text-center"},
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function ( data, type, row, meta ) { return itemDatatableActions(row.id); },
        }
    ]
});

function itemDatatableActions($id){
	if($id){
		var _actionButton ="";
            _actionButton += " <button type='button' onclick='Restore("+$id+")' class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnRestore' data-toggle='m-tooltip' data-original-title='Restore' data-placement='bottom' data-delay='{\"show\": 300}'><i class='la la-reply'></i></button>";						
		return _actionButton;
	}else{ return false; }
} 


$('#generalSearch').donetyping(function(callback) {
	search_val = $(this).val();
    tblAllowance.ajax.reload();
});

$("#reload_dtTbl").on("click",function(){
	tblAllowance.ajax.reload();
});

function Restore(id){
    $("#modal-restore").modal("show");

    $.validate({
        form: '#frm-restore',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("payroll/allowance/restore/")+id,
                type: "POST",
                dataType: "json",
                data: $("#frm-restore").find("input").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data.state) {
                        $('#modal-restore').modal('hide');
                        toastr.success(data.toastr_msg, "Successfully restored", 5000);
                        tblAllowance.ajax.reload();
                    } else {
                        toastr.error(data.toastr_msg, "Error!", 5000);
                    }
                    $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
}