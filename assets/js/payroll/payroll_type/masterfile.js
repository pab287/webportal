var search_val = "";

var tblPayrolltype = $("#table-payroll-type").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("payroll/payroll_type/masterfile/"),
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
        { data: "name" },
        { data: "status", width: "15%", className: "text-center"},
        { data: null, width: "10%", className: "text-center"},
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
		return _actionButton;
	}else{ return false; }
} 

$('#generalSearch').donetyping(function(callback) {
	search_val = $(this).val();
    tblPayrolltype.ajax.reload();
});

$("#reload_dtTbl").on("click",function(){
	tblPayrolltype.ajax.reload();
});

function Save(){
    $.validate({
      form : '#frm-add-type',
      lang: 'en',
      onSuccess : function(form) {
            $.ajax({
                url: baseUrl("payroll/payroll_type/save"),
                type: "POST",
                dataType: "json",
                data: $("#frm-add-type").find("input").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data.state){
                        toastr.success(data.toastr_msg, "Successfully saved!", 5000);
                        $("#modal-add-type").modal("hide");
                        $('#frm-add-type')[0].reset();
                        tblPayrolltype.ajax.reload();
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
    $("#modal-edit-type").modal("show");

    $.ajax({
        url: baseUrl("payroll/payroll_type/edit/")+id,
        type: "GET",
        dataType: "json",
        success: function(data){
            $("#code").val(data.code);
            $("#name").val(data.name);
            if(data.status == 1){
                $("#status1").prop('checked',true)
            }else{
                $("#status0").prop('checked',true)
            }
        }
    });
    
    $.validate({
        form : '#frm-edit-type',
        lang: 'en',
        onSuccess : function(form) {
            $.ajax({
                url: baseUrl("payroll/payroll_type/update/")+id,
                type: "POST",
                dataType: "json",
                data: $("#frm-edit-type").find("input").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data.state){
                        toastr.success(data.toastr_msg, "Successfully saved!", 5000);
                        $("#modal-edit-type").modal("hide");
                        tblPayrolltype.ajax.reload();
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

