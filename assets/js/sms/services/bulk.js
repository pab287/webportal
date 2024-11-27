jQuery(document).ready(function () {

  fileUploadPhoto();

});

var search_val = "";
var tblOutbox = $("#table-outbox").DataTable({
  dom: '<"toolbar">rtlip',
  serverSide: true,
  processing: true,
  ajax: {
      url: baseUrl("sms/bulk_outbox/"),
      type: "post",
      dataType: "json",
      data: function (d) {
          d.csrf_token = _csrf_hash,
              d.search['value'] = search_val
      }
  },
  searching: false,
  order: [[ 3, "desc" ]],
  columns: [
      {data: "cp_no", width: "10%"},
      {data: "recipient", width: "12%"},
      {data: "msg"},
      //{data: "mid", width: "100px"},
      {
          width: "6%", className: "text-center",
          data: "status", render: function (data, type, row, meta) {
              return renderStatusHtml(row.status);
          }
      },
      {data: "created_dt", width: "10%"},
  ],
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
      _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_template(" + $id + ")' data-toggle='modal' data-target='#edit_template_modal'><i class='la la-rotate-right'></i></button>";
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

var tblRecipients = $("#tbl-recipients").DataTable({
  dom: '<"toolbars">frtlip',
  serverSide: true,
  processing: true,
  ajax: {
      url: baseUrl("sms/bulk_recipients/"),
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

/*$("div.toolbars").html('<button type="button" class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew" onclick="remove_all()">Remove All Recipients</span></span></button>');*/

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
      url: baseUrl('sms/delete_bulk_recipient/') + $id,
      type: "GET",
      dataType: "json",
      success: function (data) {
          tblRecipients.ajax.reload();
      }
  });
}

function remove_all() {
  $.ajax({
      url: baseUrl('sms/remove_bulk_all/'),
      type: "GET",
      dataType: "json",
      success: function (data) {
          tblRecipients.ajax.reload();
      }
  });
}

var fileUploadPhoto = function () {
  var url = baseUrl("sms/upload_recipients");
  $("#fileupload").fileupload({
          url: url,
          dataType: "json",
          type: "POST",
          formData: {csrf_token: _csrf_hash},
          done: function (e, data) {
              var result = data.result;
              if (result.response) {
                  var filePath = result.added_file;
                  var renderFile = result.render_file;
                  var full_path = result.full_path;
                  $("#file_append").text(renderFile);
                  $("#upload").on("click", function () {
                      if(filePath){
                          $.ajax({
                              url: baseUrl("sms/import_uploads/"),
                              type: "POST",
                              data: {
                                csrf_token: _csrf_hash,
                                filePath: filePath, 
                                renderFile: renderFile,
                                full_path:full_path, 
                            },
                              dataType: "json",
                              beforeSend: function data(){
                                  filePath = "";
                              },
                              success: function data(data) {
                                  if(data){
                                      filePath = "";
                                      toastr.success(result.toastr_msg, "Upload Successfully", 5000);
                                      tblRecipients.ajax.reload();
                                      $("#fileupload").val(null);
                                      $("#file_append").text("");
                                      $("#file_append").val(null);
                                  }else{
                                      toastr.error("Data must not exceed by 100", "Error", 5000);
                                  }
                              }
                          });
                      }
                  });
              } else {
                  toastr.error(result.toastr_msg, "File error", 5000);
              }
          }
      });
}

$.validate({
  form: '#frmSendSms',
  lang: 'en',
  onSuccess: function (form) {
      $.ajax({
          url: baseUrl("sms/bulk_send_msg"),
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
                  $("#msg").val("");
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

$("#search-recipient")
  .donetyping(function () {
      const val = $(this).val();
      search_val = val;
      tblRecipients.ajax.reload();
  });