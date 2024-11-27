var getUrlParameter = function getUrlParameter(sParam) {
  var sPageURL = decodeURIComponent(window.location.search.substring(1)),
    sURLVariables = sPageURL.split("&"),
    sParameterName,
    i;

  for (i = 0; i < sURLVariables.length; i++) {
    sParameterName = sURLVariables[i].split("=");
    if (sParameterName[0] === sParam) {
      return sParameterName[1] === undefined ? true : sParameterName[1];
    }
  }
};

var search_val = "";
param_id = getUrlParameter("id");
// var tblContent = $("#table-content").DataTable({
//   dom: '<"toolbar">rt',
//   serverSide: true,
//   processing: true,
//   ajax: {
//     url: baseUrl("eforms/borrowing/get_content_request/") + param_id,
//     type: "post",
//     dataType: "json",
//     data: function (d) {
//       (d.csrf_token = _csrf_hash), (d.search["value"] = search_val);
//     }
//   },
//   searching: true,
//   columns: [
//     { data: "asset" },
//     { data: "pieces" },
//     { data: "date_borrowed", render: function (data) { return formatCalendarDate(data) } },
//     { data: "date_due", render: function (data) { return formatCalendarDate(data) } },
//     { data: "date_returned", render: function (data) { return formatCalendarDate(data) } },
//     { data: "due_reason" },],

// });
function formatCalendarDate(data) {
  if (data == "0000-00-00 00:00:00") {
    return "";
  }
  else {
    return moment(data).format("lll");
  }

}
var tempData = {};
var stat = "m-badge--metal";
$.ajax({
  url: baseUrl("eforms/borrowing/ajax_borrowing_details/") + param_id,
  type: "GET",
  dataType: "JSON",
  success: function (data) {
    var tempData = data.data;
    var date1 = new Date(data.data.date_trans);
    var date2 = new Date(data.check);
    data.data.created_dt = moment(data.data.created_dt).format(
      "MMMM DD, YYYY hh:mm A"
    );
    data.data.last_edited_dt = moment(data.data.last_edited_dt).format(
      "MMMM DD, YYYY hh:mm A"
    );
    data.data.approved_dt = moment(data.data.approved_dt).format(
      "MMMM DD, YYYY hh:mm A"
    );

    data.data.cancelled_dt = moment(data.data.cancelled_dt).format(
      "MMMM DD, YYYY hh:mm A"
    );
    data.data.released_dt = moment(data.data.received_dt).format(
      "MMMM DD, YYYY hh:mm A"
    );

    data.data.borrower = data.name;
    data.data.created_by_print = data.data.created_by;
    data.data.created_by = data.data.created_by + ' on ' + data.data.created_dt;
    if (data.data.last_edited_by) {
      data.data.last_edited_by = data.data.last_edited_by + ' on ' + data.data.last_edited_dt;
    } else {
      data.data.last_edited_by = "N/A"
    }

    
    if(data.data.approved_by != '0000-00-00 00:00:00' && data.data.approved_by){
      data.data.approved_by = data.data.approved_by + ' on ' + data.data.approved_dt;
    }else{
      data.data.approved_by = '---';
    }

    if (data.data.cancelled_by) {
      data.data.cancelled_by = data.data.cancelled_by + ' on ' + data.data.cancelled_dt;
    } else {
      data.data.cancelled_by = "N/A";
    }
    
    data.data.released_by = data.data.released_by + ' on ' + data.data.released_dt;
    switch (data.data.status) {
      case "Pending":
        stat = "m-badge--warning";
        $('#status').append('<b class="m-badge m-badge--warning text-white m-badge--wide" role="alert">Pending</b>');
        if (date1 >= date2) {
          if (typeof $("#btnapprove") !== "undefined") { $("#btnapprove").removeClass("m--hide").addClass("m-animate-fade-in"); }
          if (typeof $("#btnedit") !== "undefined") { $("#btnedit").removeClass("m--hide").addClass("m-animate-fade-in"); }
          if (typeof $("#btncancel") !== "undefined") { $("#btncancel").removeClass("m--hide").addClass("m-animate-fade-in"); }
        } else {
          if (typeof $("#btnback") !== "undefined") { $("#btnback").addClass("m--hide"); }
          if (typeof $("#btnback2") !== "undefined") { $("#btnback2").removeClass("m--hide").addClass("m-animate-fade-in"); }
        }
        break;
      case "Approved":
        stat = "m-badge--success";
        $('#status').append('<b class="m-badge m-badge--success m-badge--wide " role="alert">Approved</b>');
        if (date1 >= date2) {
          if (typeof $("#btnundoapprove") !== "undefined") { $("#btnundoapprove").removeClass("m--hide").addClass("m-animate-fade-in"); }
          if (typeof $("#btnrelease") !== "undefined") { $("#btnrelease").removeClass("m--hide").addClass("m-animate-fade-in"); }
        } else {
          if (typeof $("#btnback") !== "undefined") { $("#btnback").addClass("m--hide"); }
          if (typeof $("#btnback2") !== "undefined") { $("#btnback2").removeClass("m--hide").addClass("m-animate-fade-in"); }
        }
        if (typeof $("#approve") !== "undefined") { $("#approve").removeClass("m--hide").addClass("m-animate-fade-in"); }
        if (typeof $("#btnprint") !== "undefined") { $("#btnprint").removeClass("m--hide").addClass("m-animate-fade-in"); }
        break;
      case "Released":
        stat = "m-badge--accent";
        $('#status').append('<div class="m-badge m-badge--accent m-badge--wide" role="alert">Received</b>');
        if (date1 >= date2) {
          if (typeof $("#btnundorelease") !== "undefined") { $("#btnundorelease").removeClass("m--hide").addClass("m-animate-fade-in"); }
        } else {
          if (typeof $("#btnback") !== "undefined") { $("#btnback").addClass("m--hide"); }
          if (typeof $("#btnback2") !== "undefined") { $("#btnback2").removeClass("m--hide").addClass("m-animate-fade-in"); }
        }
        if (typeof $("#approve") !== "undefined") { $("#approve").removeClass("m--hide").addClass("m-animate-fade-in"); }
        if (typeof $("#release") !== "undefined") { $("#release").removeClass("m--hide").addClass("m-animate-fade-in"); }
        if (typeof $("#release_remark") !== "undefined") { $("#release_remark").removeClass("m--hide").addClass("m-animate-fade-in"); }
        if (typeof $("#btnprint") !== "undefined") { $("#btnprint").removeClass("m--hide").addClass("m-animate-fade-in"); }
        break;
      default:
        stat = "m-badge--metal";
        $('#status').append('<div class="m-badge m-badge--metal text-white m-badge--wide m--margin-top-5" role="alert"><strong>Cancelled</strong></div>');
        if (date1 >= date2) {
          if (typeof $("#btnundocancel") !== "undefined") { $("#btnundocancel").removeClass("m--hide").addClass("m-animate-fade-in"); }
        }
        if (typeof $("#btnback") !== "undefined") { $("#btnback").addClass("m--hide"); }
        if (typeof $("#btnback2") !== "undefined") { $("#btnback2").removeClass("m--hide").addClass("m-animate-fade-in"); }
        if (typeof $("#cancel") !== "undefined") { $("#cancel").removeClass("m--hide").addClass("m-animate-fade-in"); }
        if (typeof $("#cancel_remark") !== "undefined") { $("#cancel_remark").removeClass("m--hide").addClass("m-animate-fade-in"); }
        break;
    }
    var total_amount = 0;
    $.each(data.data_body.data, function(i ,val){
      val.date_borrowed = formatCalendarDate(val.date_borrowed);
      val.date_due = formatCalendarDate(val.date_due);
      val.date_returned = formatCalendarDate(val.date_returned);
      total_amount += parseFloat(val.amount);
    });

    data.data.total_amount = total_amount.toLocaleString('en-US', { style: 'currency', currency: 'Php' });
    vmTab1.vm_tab1 = Object.assign({}, data.data);
    vmTab1.vm_tab_contents = Object.assign({}, data.data_body);

    vmBorrowingDetails.vm_tab_contents = Object.assign({}, data.data_body);
    vmBorrowingDetails.vm_tab_main = Object.assign({}, data.data);
    vmTab1.class_name = stat;
    
  },
  error: function (jqXHR, textStatus, errorThrown) {
    alert("Error get data from ajax");
  }
});

var vmTab1 = new Vue({
  el: "#BorrowingDataRenderer",
  data: { vm_tab1: tempData, vm_tab_contents: {}, class_name:  "m-badge--metal" }
});

var vmBorrowingDetails = new Vue({
  el: "#printableArea",
  data: { vm_tab_main: {}, vm_tab_contents: {} },
})
function edit_borrowing() {
  window.location.replace(baseUrl("eforms/borrowing/edit_borrowing?id=") + param_id);
}
function print_borrowing() {
  window.open(baseUrl("eforms/borrowing/print_borrowing?id=") + param_id);
}

function approve() {
  $('#approve_modal').modal('show');
  $('.modal-title').text('Approve Borrowing'); // Set Title to Bootstrap modal title
  $.validate({
    form: '#approve_form',
    lang: 'en',
    onSuccess: function (form) {
      $.ajax({
        url: baseUrl("eforms/borrowing/approve_borrowing/") + param_id,
        type: "POST",
        dataType: "json",
        data: $("#approve_form").find("input,textarea").serialize(),
        beforeSend: function () {
          $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function (data) {
          if (data) {
            $('#approve_modal').modal('hide');
            $('#approve_form')[0].reset();
            location.reload();
            toastr.success(data.toastr_msg, "Notification: Successfully saved", 5000);
          } else {
            toastr.error(data.toastr_msg, "Notification: Error", 5000);
          }
          $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }
      });
      return false;
    },
  });
}

function open_cancel() {
  $('#modal_form_cancel').modal('show'); // show bootstrap modal
  $('.modal-title').text('Cancel Borrowing'); // Set Title to Bootstrap modal title
}
function cancel() {
  $.ajax({
    url: baseUrl("eforms/borrowing/cancel_borrowing/") + param_id,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash, cancelled_remarks: $('[name="cancelled_remarks"]').val() },
    beforeSend (){ mapBlockUI(); },
    success: function (data) {
      $('#modal_form_cancel').modal('hide');
      // location.reload();
      window.location.href = siteUrl('eforms/borrowing/view_borrowing?id=' + param_id);
      toastr.success(data.toastr_msg, "Notification: Successfully saved", 5000);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error: "ajax_approve"');
    }
  });
}

function open_release() {
  $('#modal_form_released').modal('show'); // show bootstrap modal
  $('.modal-title').text('Release Borrowing'); // Set Title to Bootstrap modal title
}
function released() {
  $.ajax({
    url: baseUrl("eforms/borrowing/release_borrowing/") + param_id,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash, released_remarks: $('[name="released_remarks"]').val() },
    beforeSend (){ mapBlockUI(); },
    success: function (data) {
      $('#modal_form_released').modal('hide');
      // location.reload();
      window.location.href = siteUrl('eforms/borrowing/view_borrowing?id=' + param_id);
      toastr.success(data.toastr_msg, "Notification: Successfully saved", 5000);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error: "ajax_approve"');
    }
  });
  return false;
}

function undo_approve() {
  $('#undo_approve_modal').modal('show');
  $('.modal-title').text('Undo Approval Form'); // Set Title to Bootstrap modal title
  $.validate({
    form: '#undo_approve_form',
    lang: 'en',
    onSuccess: function (form) {
      $.ajax({
        url: baseUrl("eforms/borrowing/undo_approve/") + param_id,
        type: "POST",
        dataType: "json",
        data: $("#undo_approve_form").find("input,textarea").serialize(),
        beforeSend: function () {
          $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function (data) {
          if (data) {
            $('#undo_approve_modal').modal('hide');
            $('#undo_approve_form')[0].reset();
            location.reload();
            toastr.success(data.toastr_msg, "Notification: Successfully saved", 5000);
          } else {
            toastr.error(data.toastr_msg, "Notification: Error", 5000);
          }
          $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }
      });
      return false;
    },
  });
}

function restore() {
  $('#restore_modal').modal('show');
  $('.modal-title').text('Restore Form'); // Set Title to Bootstrap modal title
  $.validate({
    form: '#restore_form',
    lang: 'en',
    onSuccess: function (form) {
      $.ajax({
        url: baseUrl("eforms/borrowing/undo_cancel/") + param_id,
        type: "POST",
        dataType: "json",
        data: $("#restore_form").find("input,textarea").serialize(),
        beforeSend: function () {
          $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function (data) {
          if (data) {
            $('#restore_modal').modal('hide');
            $('#restore_form')[0].reset();
            location.reload();
            toastr.success(data.toastr_msg, "Notification: Successfully saved", 5000);
          } else {
            toastr.error(data.toastr_msg, "Notification: Error", 5000);
          }
          $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }
      });
      return false;
    },
  });
}

function undo_release() {
  $('#undo_release_modal').modal('show');
  $('.modal-title').text('Undo Release Form'); // Set Title to Bootstrap modal title
  $.validate({
    form: '#undo_release_form',
    lang: 'en',
    onSuccess: function (form) {
      $.ajax({
        url: baseUrl("eforms/borrowing/undo_release/") + param_id,
        type: "POST",
        dataType: "json",
        data: $("#undo_release_form").find("input,textarea").serialize(),
        beforeSend: function () {
          $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function (data) {
          if (data) {
            $('#undo_release_modal').modal('hide');
            $('#undo_release_form')[0].reset();
            location.reload();
            toastr.success(data.toastr_msg, "Notification: Successfully saved", 5000);
          } else {
            toastr.error(data.toastr_msg, "Notification: Error", 5000);
          }
          $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }
      });
      return false;
    },
  });
}
var _tempPrivileges = ['approve', 'cancel', 'release', 'cancel_remark', 'release_remark',
  'btnapprove', 'btnedit', 'btncancel', 'btnrelease', 'btnundoapprove', 'btnundocancel',
  'btnundorelease', 'btnprint', 'btnback2'];

$(document).ready(function () {
  $.each(_tempPrivileges, function (i, v) {
    if (typeof document.getElementById(v) !== "undefined" || typeof document.getElementById(v) !== null) {
      var x = document.getElementById(v);
      /*** $(x).css({ display: "none" }); ***/
    }
  });

  /** document.getElementById('approve').style.display = 'none';
  document.getElementById('cancel').style.display = 'none';
  document.getElementById('release').style.display = 'none';
  document.getElementById('cancel_remark').style.display = 'none';
  document.getElementById('release_remark').style.display = 'none';
  document.getElementById('btnapprove').style.display = 'none';
  document.getElementById('btnedit').style.display = 'none';
  document.getElementById('btncancel').style.display = 'none';
  document.getElementById('btnrelease').style.display = 'none';
  document.getElementById('btnundoapprove').style.display = 'none';
  document.getElementById('btnundocancel').style.display = 'none';
  document.getElementById('btnundorelease').style.display = 'none';
  document.getElementById('btnprint').style.display = 'none';
  document.getElementById('btnback2').style.display = 'none'; **/
});

$("#approve_modal").hide();
$("#undo_approve_modal").hide();
$("#restore_modal").hide();
$("#undo_release_modal").hide();

function printArea() {
    win = window.open();
    var divToPrint = document.getElementById("printableArea");
    win.document.write(divToPrint.outerHTML);
    win.focus();
    win.print();
    win.close();
}
