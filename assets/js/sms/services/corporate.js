var search_val = "";
var tblOutbox = $("#table-outbox").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("sms/corporate_outbox/"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
        }
    },
    searching: false,
    columns: [
        {
            data: "cp_no",
            width: "100px"
        },
        {
            data: "recipient",
            width: "130px"
        },
        {data: "msg"},
        // {
        //     data: "mid",
        //     width: "100px"
        // },
        {
            width: "6%", className: "text-center",
            data: "status", render: function (data) {
                return renderStatusHtml(data)
            }
        },
        {data: "date_sent", width: "10%"},
        {data: null, width: "5%", className: "text-center"},
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


function renderStatusHtml(data) {
    if (data) {
        return '<div class="m-badge text-white m-badge--accent m-badge--wide" role="alert"><strong>Sent</strong></div>';
    } else {
        return '<div class="m-badge m-badge--danger m-badge--wide" role="alert"><strong>Failed</strong></div>';
    }
}

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += " " +
            "<button " +
            "   type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
            "   onclick='resend(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='top' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Resend SMS.'" +
            "   data-skin='dark'" +
            "><i class='la la-rotate-right'></i></button>";
        return _actionButton;
    } else {
        return "";
    }
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblOutbox.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblOutbox.ajax.reload();
});

$("#group").select2({
    placeholder: 'Select Group',
    width: '100%',
    ajax: {
        url: baseUrl("sms/group"),
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});

$("#contact").select2({
    placeholder: 'Select Contacts',
    width: '100%',
    multiple: true,
    ajax: {
        url: baseUrl("sms/contact"),
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});

$("#template").select2({
    placeholder: 'Select Template',
    width: '100%',
    ajax: {
        delay: 500,
        url: baseUrl("sms/template"),
        processResults: function (data) {
            return data;
        }
    }
});

$("#temp_button").on("click", function () {
    $.ajax({
        url: baseUrl('sms/add_template_message/') + $("#template").val(),
        type: "GET",
        dataType: "json",
        success: function (data) {
            $("#message").val(data.message);
        }
    });
});

$("#group_button").on("click", function () {
  console.log($("#group").val());
      // Check if the value of #cp_no is empty
      if ($("#group").val() == null) {
        // If it's empty, show a toastr notification and return early to prevent the AJAX request
        toastr.error("Please enter a number.", "Notification: Error", 5000);
        return; // Prevent the AJAX request
    }
    $.ajax({
        url: baseUrl('sms/add_group_table/') + $("#group").val(),
        type: "GET",
        dataType: "json",
        success: function (data) {
            $("#group").val("").trigger('change');
            tblRecipients.ajax.reload();
        }
    });
});

$("#number_button").on("click", function () {

    if ($("#cp_no").val().trim() == "") {
      toastr.error("Please enter a number.", "Notification: Error", 5000);
      return; 
  }

    $.ajax({
        url: baseUrl("sms/add_number_table/") + $("#cp_no").val(),
        type: "GET",
        dataType: "json",
        success: function (data) {
            if (data) {
                $("#cp_no").val("");
                tblRecipients.ajax.reload();
                toastr.success(data.toastr_msg, "Notification: Successfully added", 5000);
            } else {
                toastr.error(data.toastr_msg, "Notification: Error", 5000);
            }
        }
    });
});

$("#contact_button").on("click", function () {

      // Check if the value of #cp_no is empty
      if ($("#contact").val() == "") {
        // If it's empty, show a toastr notification and return early to prevent the AJAX request
        toastr.error("Please enter a number.", "Notification: Error", 5000);
        return; // Prevent the AJAX request
    }

    $.ajax({
        url: baseUrl('sms/add_contact_table/'),
        type: "GET",
        data: {data: $("#contact").val()},
        dataType: "json",
        success: function (data) {
            $("#contact").val("").trigger('change');
            tblRecipients.ajax.reload();
            return data;
        }
    });
});

$("#add_all_button").on("click", function () {
    $.ajax({
        url: baseUrl('sms/add_all_table/'),
        type: "GET",
        dataType: "json",
        success: function (data) {
            tblRecipients.ajax.reload();
        }
    });
});

var tblRecipients = $("#tbl-recipients").DataTable({
    dom: '<"toolbars">frtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("sms/corporate_recipients/"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
        }
    },
    searching: false,
    columns: [
        {data: "cp_no", width: "20%"},
        {data: "name"},
        {data: null, width: "10%", className: "text-center"},
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,

            render: function (data, type, row, meta) {
                return itemRecipientsActions(row.id);
            },
        }
    ]
});

/*$("div.toolbars").html('' +
    '<button ' +
    '   type="button" ' +
    '   class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew" ' +
    '   onclick="remove_all()">' +
    '   Remove All Recipients</span></span>' +
    '</button>');*/

function itemRecipientsActions($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='delete_recipient(" + $id + ")'><i class='la la-close'></i></button>";
        return _actionButton;
    } else {
        return false;
    }
}

function delete_recipient($id) {
    $.ajax({
        url: baseUrl('sms/delete_recipient/') + $id,
        type: "GET",
        dataType: "json",
        success: function (data) {
            tblRecipients.ajax.reload();
        }
    });
}

function remove_all() {
    $.ajax({
        url: baseUrl('sms/remove_all/'),
        type: "GET",
        dataType: "json",
        success: function (data) {
            tblRecipients.ajax.reload();
        }
    });
}

$.validate({
    form: '#frmSendSms',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("sms/corporate_send_msg"),
            type: "POST",
            dataType: "json",
            data: $("#frmSendSms").find("input,select,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
              if (data.response=="No Recipient Found"){
                toastr.error(data.toastr_msg, data.response, 5000);
              }
              else{
                var total = data.data.length, totalSuccess = 0;
                data.data.forEach(element => {
                    if(element.status){
                        totalSuccess++;
                    }
                });
                if(total == totalSuccess){
                    tblRecipients.ajax.reload();
                    tblOutbox.ajax.reload();
                    $("#message").val("");
                    toastr.success(data.toastr_msg, "Notification: Successfully sent", 5000);
                } else if(total != totalSuccess && totalSuccess > 0){
                    tblRecipients.ajax.reload();
                    tblOutbox.ajax.reload();
                    toastr.warning(data.toastr_msg, "Notification: Successfully sent, but some failed to send.", 5000);
                }
                
              }
              $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});


function resend($id) {
    $.ajax({
        url: baseUrl("sms/corporate_resend/") + $id,
        type: "GET",
        dataType: "json",
        success: function (data) {
            if (data) {
                tblOutbox.ajax.reload();
                toastr.success(data.toastr_msg, "Notification: Message resent.", 5000);
            } else {
                toastr.error(data.toastr_msg, "Notification: Failed to send.", 5000);
            }
            $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }
    });
}

$("#search-recipient")
    .donetyping(function () {
        const val = $(this).val();
        search_val = val;
        tblRecipients.ajax.reload();
});

function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode > 31 
    && (charCode < 48 || charCode > 57))
    return false;
    return true;
}