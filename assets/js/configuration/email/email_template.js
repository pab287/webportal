var search_val = "";
var tblEmailTemplate = $("#table-email-template").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
    processing: true,
    ajax: {
		url: baseUrl("configuration/email_template_list/"),
		type: "post",
        dataType: "json",
        data: function(d){
			d.csrf_token = _csrf_hash,
			d.search['value'] = search_val
		}
    },
    searching: false,
    columns: [
        { data: "name", width: '7%'},
        { data: "description", width: '7%'},
        { data: "module", width: '5%'},
        { data: "send_to", width: '5%'},
        { data: "cc_to", width: '5%'},
        { data: "bcc_to", width: '5%'},
        { data: null, className: "text-center"},
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            width: '2%',
            render: function ( data, type, row, meta ) { return itemDatatableActions(row.id); },
        }
    ]
});

function itemDatatableActions($id){
	if($id){
		var _actionButton ="";
            _actionButton += "<button type='button' onclick='edit_template("+$id+")' data-toggle='modal' data-target='#edit_modal' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-pencil-square'></i></button>";	
            _actionButton += "<button type='button' onclick='send_email("+$id+")' data-toggle='modal' data-target='#send_modal' class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='fa fa-envelope'></i></button>";
            _actionButton += "<button type='button' onclick='delete_template("+$id+")' data-toggle='modal' data-target='#delete_modal' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-trash'></i></button>";			
		return _actionButton;
	}else{ return false; }
}

//custom global search init
$('#generalSearch').donetyping(function(callback) {
	search_val = $(this).val();
    tblEmailTemplate.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
	tblEmailTemplate.ajax.reload();
});

$("#new_modal").hide();
$("#edit_modal").hide();
$("#delete_modal").hide();
$("#send_modal").hide();

$("#send_to").select2({
    placeholder: 'Select. .',
    width: '100%',
    multiple: true,
    dataType: "json",
    ajax: {
      url: baseUrl("configuration/email_lookup"),
      delay: 250,
      data: function(param) {
        param.email = select2Val();
        return param;
      },
      global: false,
      processResults: function (data) {
        return data;
      }
    }
}); 

$("#cc_to").select2({
    placeholder: 'Select. .',
    width: '100%',
    multiple: true,
    dataType: "json",
    ajax: {
      url: baseUrl("configuration/email_lookup"),
      delay: 250,
      data: function(param) {
        param.email = select2Val();
        return param;
      },
      global: false,
      processResults: function (data) {
        return data;
      }
    }
}); 

$("#bcc_to").select2({
    placeholder: 'Select. .',
    width: '100%',
    multiple: true,
    dataType: "json",
    ajax: {
      url: baseUrl("configuration/email_lookup"),
      delay: 250,
      data: function(param) {
        param.email = select2Val();
        return param;
      },
      global: false,
      processResults: function (data) {
        return data;
      }
    }
}); 

$("#edit_send_to").select2({
    placeholder: 'Select. .',
    width: '100%',
    multiple: true,
    dataType: "json",
    ajax: {
      url: baseUrl("configuration/email_lookup"),
      delay: 250,
      data: function(param) {
        param.email = select2Val2();
        return param;
      },
      global: false,
      processResults: function (data) {
        return data;
      }
    }
}); 

$("#edit_cc_to").select2({
    placeholder: 'Select. .',
    width: '100%',
    multiple: true,
    dataType: "json",
    ajax: {
      url: baseUrl("configuration/email_lookup"),
      delay: 250,
      data: function(param) {
        param.email = select2Val2();
        return param;
      },
      global: false,
      processResults: function (data) {
        return data;
      }
    }
}); 

$("#edit_bcc_to").select2({
    placeholder: 'Select. .',
    width: '100%',
    multiple: true,
    dataType: "json",
    ajax: {
      url: baseUrl("configuration/email_lookup"),
      delay: 250,
      data: function(param) {
        param.email = select2Val2();
        return param;
      },
      global: false,
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
                url: baseUrl("configuration/save_email"),
                type: "POST",
                dataType: "json",
                data: $("#new_form").find("input,select").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data){
                        $('#new_modal').modal('hide');
                        $('#new_form')[0].reset();
                        tblEmailTemplate.ajax.reload();
                        toastr.success(data.toastr_msg, "Notification: Successfully saved", 5000);
                    }else{
                        toastr.error(data.toastr_msg, "Notification: Error", 5000);
                    }
                    $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
                }
            });
        return false;
    },
});

function edit_template($id){
    $.ajax({
        url: baseUrl("configuration/edit_details/") +$id,
        type: "GET",
        success: function(data){
            $('#edit_send_to').empty();
            $('#edit_cc_to').empty();
            $('#edit_bcc_to').empty();
            $("#edit_name").val(data.name);
            $("#edit_description").val(data.description);
            $("#edit_module").val(data.module);
            for (i = 0; i < data.send_to.length; i++) {
                var edit_send_to = new Option(data.send_to[i], data.send_to[i], true, true);
                $('#edit_send_to').append(edit_send_to).trigger('change');  
            }
            for (i = 0; i < data.cc_to.length; i++) {
                var edit_cc_to = new Option(data.cc_to[i], data.cc_to[i], true, true);
                $('#edit_cc_to').append(edit_cc_to).trigger('change');
            }
            for (i = 0; i < data.cc_to.length; i++) {
                var edit_bcc_to = new Option(data.bcc_to[i], data.bcc_to[i], true, true);
                $('#edit_bcc_to').append(edit_bcc_to).trigger('change');  
            }
        }
    });

    $.validate({
        form : '#edit_form',
        lang: 'en',
        onSuccess : function(form) {
                $.ajax({
                    url: baseUrl("configuration/edit_email/")+$id,
                    type: "POST",
                    dataType: "json",
                    data: $("#edit_form").find("input,select").serialize(),
                    beforeSend: function(){
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function(data){
                        if(data){
                            $('#edit_modal').modal('hide');
                            $('#edit_form')[0].reset();
                            tblEmailTemplate.ajax.reload();
                            toastr.success(data.toastr_msg, "Notification: Successfully saved", 5000);
                        }else{
                            toastr.error(data.toastr_msg, "Notification: Error", 5000);
                        }
                        $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
                    }
                });
            return false;
        },
    });    
}

function delete_template($id){
    $.validate({
        form : '#delete_form',
        lang: 'en',
        onSuccess : function(form) {
                $.ajax({
                    url: baseUrl("configuration/delete_email/")+$id,
                    type: "POST",
                    dataType: "json",
                    data: $("#delete_form").find("input,select").serialize(),
                    beforeSend: function(){
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function(data){
                        if(data){
                            $('#delete_modal').modal('hide');
                            $('#delete_form')[0].reset();
                            tblEmailTemplate.ajax.reload();
                            toastr.success(data.toastr_msg, "Notification: Successfully saved", 5000);
                        }else{
                            toastr.error(data.toastr_msg, "Notification: Error", 5000);
                        }
                        $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
                    }
                });
            return false;
        },
    });    
}

function send_email($id){
    $.validate({
        form : '#send_form',
        lang: 'en',
        onSuccess : function(form) {
                $.ajax({
                    url: baseUrl("configuration/send_email/")+$id,
                    type: "POST",
                    dataType: "json",
                    data: $("#send_form").find("input,select").serialize(),
                    beforeSend: function(){
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function(data){
                        if(data){
                            $('#send_modal').modal('hide');
                            tblEmailTemplate.ajax.reload();
                            toastr.success(data.toastr_msg, "Notification: Successfully saved", 5000);
                        }else{
                            toastr.error(data.toastr_msg, "Notification: Error", 5000);
                        }
                        $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
                    }
                });
            return false;
        },
    });    
}

function select2Val(){
    var _send_to = $("#send_to").val();
    var _cc_to = $("#cc_to").val();
    var _bcc_to = $("#bcc_to").val();

    const arr = [];
    arr.push(_send_to, _cc_to, _bcc_to);

    var newArr = arr.filter( function(value){
        return value != '' && value != null;
    });

    const _arr = newArr.toString();
    return _arr;
}

function select2Val2(){
    var _send_to = $("#edit_send_to").val();
    var _cc_to = $("#edit_cc_to").val();
    var _bcc_to = $("#edit_bcc_to").val();

    const arr = [];
    arr.push(_send_to, _cc_to, _bcc_to);

    var newArr = arr.filter( function(value){
        return value != '' && value != null;
    });

    const _arr = newArr.toString();
    return _arr;
}