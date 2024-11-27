jQuery(document).ready(function () {
  fileUpload();
});
var filePath = "";
var renderFile = "";
var search_val = "";
var result = "";
var template1 = false;
var tblOutbox = $("#table-outbox").DataTable({
  dom: '<"toolbar">rtlip',
  serverSide: true,
  processing: true,
  ajax: {
      url: baseUrl("sms/client_outbox/"),
      type: "post",
      dataType: "json",
      data: function (d) {
          d.csrf_token = _csrf_hash,
              d.search['value'] = search_val
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
          width: "160px"
      },
      {
          data: "msg"
      },
      // {
      //     data: "mid",
      //     width: "110px"
      // },
      {
          width: "6%", className: "text-center",
          data: "status", render: function (data) {
              return renderStatusHtml(data)
          }
      },
      {data: "date_sent", width: "10%"},
      {data: null, className: "text-center"},
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
      _actionButton += "" +
          "<button type='button' " +
          "        class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
          "        onclick='resend(" + $id + ")'" +
          "        data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
          "        data-original-title='Resend SMS.'" +
          "        data-skin='dark'>" +
          "       <i class='la la-rotate-right'></i>" +
          "</button>";
      return _actionButton;
  } else {
      return false;
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

$("#template").select2({
  placeholder: 'Select. .',
  width: '100%',
  ajax: {
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
          template1 = true;
      }
  });
});

var search_val = "";
var messageTemp = [];
var tblRecipients = $("#tbl-recipients").DataTable({
  dom: '<"toolbars">frtlip',
  serverSide: true,
  processing: true,
  ajax: {
      url: baseUrl("sms/client_recipients/"),
      type: "post",
      dataType: "json",
      data: function (d) {
          d.csrf_token = _csrf_hash;
          d.search['value'] = search_val;
      }
  },
  searching: false,
  columns: [
      {data: "acc_no"},
      {data: "cp_no"},
      {data: "name"},
      {data: "data1"},
      {data: "data2"},
      {data: "data3"},
      {data: null, className: "text-center"},
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
  ],
  drawCallback: function(settings) {

    messageTemp = [];
    this.api().rows().every(function() {
        var rowData = this.data();
        messageTemp.push({
            data1: rowData.data1,
            data2: rowData.data2,
            data3: rowData.data3
        });
    });
}
});

/*$("div.toolbars").html('<button type="button" class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew" onclick="remove_all()">Remove All Recipients</span></span></button>');*/

function itemRecipientsActions($id) {
  if ($id) {
      var _actionButton = "";
      _actionButton += "" +
          "<button type='button' " +
          "        class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
          "        onclick='delete_recipient(" + $id + ")'" +
          "        data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
          "        data-original-title='Remove Recipient.'" +
          "        data-skin='dark'><i class='la la-close'></i></button>";
      return _actionButton;
  } else {
      return false;
  }
}


function delete_recipient($id) {
  $.ajax({
      url: baseUrl('sms/delete_client_recipient/') + $id,
      type: "GET",
      dataType: "json",
      success: function (data) {
          tblRecipients.ajax.reload();
      }
  });
}

function remove_all() {
  $.ajax({
      url: baseUrl('sms/remove_client_all/'),
      type: "GET",
      dataType: "json",
      success: function (data) {
          tblRecipients.ajax.reload();
      }
  });
}

var fileUpload = function () {
  var url = baseUrl("sms/upload_recipients_client");
  $("#fileupload").fileupload({
          url: url,
          type: "POST",
          dataType: "json",
          formData: {csrf_token: _csrf_hash},
          done: function (e, data) {
              result = data.result;
              if (result.response) {
                  filePath = result.added_file;
                  renderFile = result.render_file;
                  $("#file_append").text(renderFile);
                  $("#upload").prop('disabled', false);
              } else {
                  tblRecipients.ajax.reload();
                  toastr.error(result.toastr_msg, "File error", 5000);
              }
          }
      });
}
$("#upload").on("click", function (e) {
  e.preventDefault();       
    $.ajax({
        url: baseUrl("sms/import_uploads_client/"),
        type: "POST",
        data: {
          csrf_token: _csrf_hash,
          filePath: filePath, 
          renderFile: renderFile 
      },
        dataType: "json",
        success: function data(data) {
            toastr.success(result.toastr_msg, "Upload Successfully", 5000);
            tblRecipients.ajax.reload();
            $("#file_append").text("");
            $("#upload").prop('disabled', true);
            filePath = "";
            renderFile = "";
            result="";
        }
    });
});

$.validate({
  form: '#frmSendSms',
  lang: 'en',
  onSuccess: function (form) {
    var formData = $("#frmSendSms").find("input,select,textarea").serialize();
    var postData = formData + "&template1=" + template1;
      $.ajax({
          url: baseUrl("sms/client_send_msg"),
          type: "POST",
          dataType: "json",
          data: postData,
          beforeSend: function () {
              $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
          },
          success: function (data) {
              if (data.response=="No Recipient Found"){
                toastr.error(data.toastr_msg, data.response, 5000);
              }
              else{
                console.log(data);
                if (data.missing.length === 0 && data.not_sent.length === 0) {
                    toastr.success("Success", "All messages are sent", 5000);
                } 
                else if (data.sent.length > 0) {
                    toastr.warning("Error", "Some Messages are not sent", 5000);
                }
                else if (data.sent.length == 0){
                    toastr.error("Error", "No Messages sent", 5000);
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
      url: baseUrl("sms/client_resend/") + $id,
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