var search_val = "";

var tblAllowance = $("#table-allowance").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("payroll/allowance/masterfile/"),
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
            _actionButton += " <button type='button' onclick='Edit("+$id+")' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit' data-toggle='m-tooltip' data-original-title='Edit' data-placement='bottom' data-delay='{\"show\": 300}'><i class='la la-pencil-square'></i></button>";		
            _actionButton += " <button type='button' onclick='Archive("+$id+")' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnArchive' data-toggle='m-tooltip' data-original-title='Archive' data-placement='bottom' data-delay='{\"show\": 300}'><i class='la la-file-archive-o'></i></button>";					
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

function Save(){
    $.validate({
      form : '#frm-add',
      lang: 'en',
      onSuccess : function(form) {
            $.ajax({
                url: baseUrl("payroll/allowance/save"),
                type: "POST",
                dataType: "json",
                data: $("#frm-add").find("input, select").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data.state){
                        toastr.success(data.toastr_msg, "Successfully saved!", 5000);
                        $("#modal-add").modal("hide");
                        $('#frm-add')[0].reset();
                        $('#employee').text("");
                        tblAllowance.ajax.reload();
                    }else{
                        toastr.error(data.toastr_msg, "Error!", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
}

function Edit(id){
    $("#modal-edit").modal("show");

    $.ajax({
        url: baseUrl("payroll/allowance/edit/")+id,
        type: "GET",
        dataType: "json",
        success: function(data){
            $("#code").val(data.code);
            $("#name").val(data.allowance_name);
        }
    });
    
    $.validate({
        form : '#frm-edit',
        lang: 'en',
        onSuccess : function(form) {
            $.ajax({
                url: baseUrl("payroll/allowance/update/")+id,
                type: "POST",
                dataType: "json",
                data: $("#frm-edit").find("input, select, textarea").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data.state){
                        toastr.success(data.toastr_msg, "Successfully saved!", 5000);
                        $("#modal-edit").modal("hide");
                        tblAllowance.ajax.reload();
                    }else{
                        toastr.error(data.toastr_msg, "Error!", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
}

function Archive(id){
    $("#modal-delete").modal("show");

    $.validate({
        form: '#frm-delete',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("payroll/allowance/delete/")+id,
                type: "POST",
                dataType: "json",
                data: $("#frm-delete").find("input").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data.state) {
                        $('#modal-delete').modal('hide');
                        toastr.success(data.toastr_msg, "Successfully archived", 5000);
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