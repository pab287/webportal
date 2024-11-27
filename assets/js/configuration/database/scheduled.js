var search_val = "";
var tblScheduledBackup = $("#scheduled-backup").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
    processing: true,
    ajax: {
		url: baseUrl("configuration/database_backup/"),
		type: "post",
        dataType: "json",
        data: function(d){
			d.csrf_token = _csrf_hash,
			d.search['value'] = search_val
		}
    },
    searching: false,
    columns: [
        { data: "filename"},
        { data: "filepath"},
        { data: "created_at", render: function(data){ return dateFormat(data); }},
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


function dateFormat(data){
    if(data=="0000-00-00 00:00:00"){
        return "";
    }
    else{
        return moment(data).format("MMM DD YYYY, h:mm:ss a");
    }
}

function itemDatatableActions($id, $filepath){
	if($id){
		var _actionButton ="";
            _actionButton += "<button type='button' onclick='file_download("+$id+")' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-download'></i></button> ";		
            _actionButton += "<button type='button' onclick='delete_file("+$id+")' data-toggle='modal' data-target='#delete_modal' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-trash'></i></button>";			
		return _actionButton;
	}else{ return false; }
}

$("#new_modal").hide();
$("#delete_modal").hide();

$("#database_name").select2({
    placeholder: 'Select. .',
    width: '100%',
    dataType: "json",
    delay: 250,
    ajax: {
      url: baseUrl("configuration/database_lookup"),
      processResults: function (data) {
        return data;
      }
    }
}); 

$.validate({
    form : '#new_form',
    lang: 'en',
    onSuccess : function(form) {
            $.ajax({
                url: baseUrl("configuration/save_scheduled"),
                type: "POST",
                dataType: "json",
                data: $("#new_form").find("input,select").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    $('#new_modal').modal('hide');
                    $('#new_form')[0].reset();
                    tblScheduledBackup.ajax.reload();
                    if(data){
                        toastr.success(data.msg, "Notification: Successfully saved", 5000);
                    }else{
                        toastr.error(data.msg, "Notification: Error", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        return false;
    },
});

function file_download($id){
    $.ajax({
		url: baseUrl("configuration/database_details/") +$id,
		type: "get",
        dataType: "json",
        success: function(data){
            window.open(baseUrl(data.filepath),'_blank');
        }
    });
}

function delete_file($id){
    $.validate({
        form : '#delete_form',
        lang: 'en',
        onSuccess : function(form) {
                $.ajax({
                    url: baseUrl("configuration/delete_file/") + $id,
                    type: "POST",
                    dataType: "json",
                    data: $("#delete_form").find("input,textarea").serialize(),
                    beforeSend: function(){
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function(data){
                        if(data){
                            $('#delete_modal').modal('hide');
                            tblScheduledBackup.ajax.reload();
                            toastr.success(data.toastr_msg, "Notification: Removed successfully", 5000);
                        }else{
                            toastr.error(data.toastr_msg, "Notification: Error", 5000);
                        }
                        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                });
            return false;
        },
    });
}
