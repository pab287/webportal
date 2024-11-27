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
//     url: baseUrl("eforms/transmittal/get_content_request/") + param_id,
//     type: "post",
//     dataType: "json",
//     data: function (d) {
//       (d.csrf_token = _csrf_hash), (d.search["value"] = search_val);
//     }
//   },
//   searching: true,
//   columns: [{ data: "description" }],
//   columnDefs: [
//     { targets: [0], width: "50%" },
//   ]
// });

var tempData = {};
$.ajax({
  url: baseUrl("eforms/transmittal/ajax_transmittal_details/") + param_id,
  type: "GET",
  dataType: "JSON",
  success: function (data) {

    if (data.data.last_edited_by) {
      $("#last_edited_by").text(data.data.last_edited_by + " ON " + moment(data.data.last_edited_dt).format("MMMM DD, YYYY hh:mm A"))
    } else {
      $("#last_edited_by").text("N/A");
    }

    if (data.data.approved_by) {
      $("#approve").show();
    }

    if (data.data.received_by) {
      $("#received").show();
    }


    var date1 = new Date(data.data.ship_date);
    var date2 = new Date(data.check);
    data.data.ship_date = moment(data.data.ship_date).format(
      "MMMM DD, YYYY hh:mm A"
    );
    data.data.created_dt = moment(data.data.created_dt).format(
      "MMMM DD, YYYY hh:mm A"
    );
    data.data.approved_dt = moment(data.data.approved_dt).format(
      "MMMM DD, YYYY hh:mm A"
    );
    data.data.disapproved_dt = moment(data.data.disapproved_dt).format(
      "MMMM DD, YYYY hh:mm A"
    );
    data.data.cancelled_dt = moment(data.data.cancelled_dt).format(
      "MMMM DD, YYYY hh:mm A"
    );
    data.data.received_dt = moment(data.data.received_dt).format(
      "MMMM DD, YYYY hh:mm A"
    );
    data.data.ship_date = moment(data.data.ship_date).format(
      "MMMM DD, YYYY hh:mm A"
    );
    data.check = moment(data.check).format(
      "MMMM DD, YYYY hh:mm A"
    );

    data.data.ref_yr = data.plateno;
    data.data.created_by = data.data.created_by + ' on ' + data.data.created_dt;
    data.data.last_edited_by = data.data.last_edited_by + ' on ' + data.data.last_edited_dt;
    data.data.approved_by = data.data.approved_by + ' on ' + data.data.approved_dt;
    data.data.disapproved_by = data.data.disapproved_by + ' on ' + data.data.disapproved_dt;
    data.data.cancelled_by = data.data.cancelled_by + ' on ' + data.data.cancelled_dt;
    data.data.received_by = data.data.received_by + ' on ' + data.data.received_dt;
    switch (data.data.status) {
      case "Pending":
        $('#status').append('<div class="m-badge m-badge--warning m-badge--wide text-white" role="alert"><strong>Pending</strong></div>');
        if (date1 >= date2) {
          $("#btnapprove").css("display", "block");
          $("#btndisapprove").css("display", "block");
          $("#btnedit").css("display", "block");
          $("#btncancel").css("display", "block");
        } else {
          $("#btnback2").css("display", "block");
          $("#btnback").css("display", "none");
        }
        break;
      case "Approved":
        $('#status').append('<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Approved</strong></div>');
        if (date1 >= date2) {
          $("#btnundoapprove").css("display", "block");
          $("#btnreceive").css("display", "block");

        } else {
          $("#btnback2").css("display", "block");
          $("#btnback").css("display", "none");
        }
        document.getElementById('approve').style.removeProperty('display');
        $("#btnprint").css("display", "block");
        break;
      case "Disapproved":
        $('#status').append('<div class="m-badge m-badge--danger m-badge--wide" role="alert"><strong>Disapproved</strong></div>');
        if (date1 >= date2) {
          $("#btnundodisapprove").css("display", "block");
        } else {
          $("#btnback2").css("display", "block");
          $("#btnback").css("display", "none");
        }
        document.getElementById('disapprove').style.removeProperty('display');
        break;
      case "Received":
        $('#status').append('<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>Received</strong></div>');
        if (date1 >= date2) {
          $("#btnundoreceive").css("display", "block");
        } else {
          $("#btnback2").css("display", "block");
          $("#btnback").css("display", "none");
        }
        document.getElementById('approve').style.removeProperty('display');
        document.getElementById('receive').style.removeProperty('display');
        document.getElementById('receive_remark').style.removeProperty('display');
        $("#btnprint").css("display", "block");
        break;
      default:
        $('#status').append('<div class="m-badge m-badge--metal text-white m-badge--wide" role="alert"><strong>Cancelled</strong></div>');
        if (date1 >= date2) {
          $("#btnundocancel").css("display", "block");
        }
        $("#btnback2").css("display", "block");
        $("#btnback").css("display", "none");
        document.getElementById('cancel').style.removeProperty('display');
        document.getElementById('cancel_remark').style.removeProperty('display');
        break;
    }
    if (data.data.is_service == 1) {
      document.getElementById('plate_no').style.removeProperty('display');
      document.getElementById('driver').style.removeProperty('display');

    } else if (data.data.is_others == 1) {
      document.getElementById('other_remark').style.removeProperty('display');

    }
    if (data.data.cat == "ex") {
      document.getElementById('waybill').style.removeProperty('display');
    }
    vmTab1.vm_tab1 = Object.assign({}, data.data);
    vmTab1.vm_tab1_body = Object.assign({}, data.transmittal_body);
    vmTabPrint.vmData = Object.assign({}, data.transmittal_body);
    vmTabPrint.vmDataMain = Object.assign({}, data.data);
    console.log(data.data);
  },
  error: function (jqXHR, textStatus, errorThrown) {
    alert("Error get data from ajax");
  }
});

var vmTab1 = new Vue({
  el: "#transmittalDataRenderer",
  data: { vm_tab1: tempData, vm_tab1_body: {} }
});

var vmTabPrint = new Vue({
  el: "#printableArea",
  data: { vmData : {}, vmDataMain : {}}
})

function edit_transmittal() {
  window.location.replace(baseUrl("eforms/transmittal/edit_transmittal?id=") + param_id);
}

function print() {
  window.open(baseUrl("eforms/transmittal/print_transmittal?id=") + param_id);
}

function open_cancel() {
  $('#modal_form_cancel').modal('show'); // show bootstrap modal
  $('.modal-title').text('Cancel Transmittal Form'); // Set Title to Bootstrap modal title
}

function cancel() {
  $.ajax({
    url: baseUrl("eforms/transmittal/cancel_transmittal/") + param_id,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash, cancelled_remarks: $('[name="cancelled_remarks"]').val() },
    success: function (data) {
      location.reload();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error: "ajax_approve"');
    }
  });
}

function open_approve() {
  $('#modal_form_approve').modal('show'); // show bootstrap modal
  $('.modal-title').text('Approval Form'); // Set Title to Bootstrap modal title
}

function approve() {

  $.ajax({
    url: baseUrl("eforms/transmittal/approve_transmittal/") + param_id,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash, approve_remarks: $('[name="approve_remarks"]').val() },
    success: function (data) {
      toastr.success(data.msg, "Transmittal has been approved!");
      location.reload();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error: "ajax_approve"');
    }
  });
}

function open_disapprove() {
  $('#modal_form_disapprove').modal('show'); // show bootstrap modal
  $('.modal-title').text('Disapproval Form'); // Set Title to Bootstrap modal title
}

function disapprove() {
  // ajax delete data to database
  $.ajax({
    url: baseUrl("eforms/transmittal/disapprove_transmittal/") + param_id,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash, disapproved_remarks: $('[name="disapproved_remarks"]').val() },
    success: function (data) {
      toastr.success(data.msg, "Updated successfully!");
      location.reload();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error adding / update data');
    }
  });
}

function open_undodisapprove() {
  $('#modal_form_undodisapprove').modal('show'); // show bootstrap modal
  $('.modal-title').text('Undo Disapproval'); // Set Title to Bootstrap modal title
}

function undo_disapprove() {
  // ajax delete data to database
  $.ajax({
    url: baseUrl("eforms/transmittal/undo_disapprove_transmittal/") + param_id,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash },
    success: function (data) {
      toastr.success(data.msg, "Updated successfully!");
      location.reload();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error adding / update data');
    }
  });
}

function openundo_approve() {
  $('#modal_form_undoapprove').modal('show'); // show bootstrap modal
  $('.modal-title').text('Undo Approval'); // Set Title to Bootstrap modal title
}

function undo_approve() {
  // ajax delete data to database
  $.ajax({
    url: baseUrl("eforms/transmittal/undo_approve_transmittal/") + param_id,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash },
    success: function (data) {
      toastr.success(data.msg, "Updated successfully!");
      location.reload();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error adding / update data');
    }
  });
}

$("#restore").modal('hide');
function restore() {
  $('#restore').modal('show'); // show bootstrap modal
  $('.modal-title').text('Restore');
}

function restore_form() {
  $.ajax({
    url: baseUrl("eforms/transmittal/undo_cancel_transmittal/") + param_id,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash },
    success: function (data) {
      toastr.success(data.msg, "Updated successfully!");
      location.reload();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error adding / update data');
    }
  });
}

function open_undoreceive() {
  $('#modal_form_undoreceived').modal('show'); // show bootstrap modal
  $('.modal-title').text('Undo Receival'); // Set Title to Bootstrap modal title
}

function undo_receive() {
  // ajax delete data to database
  $.ajax({
    url: baseUrl("eforms/transmittal/undo_receive_transmittal/") + param_id,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash },
    success: function (data) {
      toastr.success(data.msg, "Updated successfully!");
      location.reload();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error adding / update data');
    }
  });
}

function open_receive() {
  $('#modal_form_received').modal('show'); // show bootstrap modal
  $('.modal-title').text('Receive form'); // Set Title to Bootstrap modal title
}

function receive() {
  $.ajax({
    url: baseUrl("eforms/transmittal/receive_transmittal/") + param_id,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash, received_remarks: $('[name="received_remarks"]').val() },
    success: function (data) {
      toastr.success(data.msg, "Transmittal has been received!");
      location.reload();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error: "ajax_approve"');
    }
  });

  return false;
}

$(document).ready(function () {
  $("#btnapprove").css("display", "none");
  $("#btndisapprove").css("display", "none");
  $("#btnedit").css("display", "none");
  $("#btncancel").css("display", "none");
  $("#btnreceive").css("display", "none");
  $("#btnundoapprove").css("display", "none");
  $("#btnundodisapprove").css("display", "none");
  $("#btnundoreceive").css("display", "none");
  $("#btnundocancel").css("display", "none");
  $("#btnprint").css("display", "none");
  $("#btnback2").css("display", "none");

  document.getElementById('approve').style.display = 'none';
  document.getElementById('disapprove').style.display = 'none';
  document.getElementById('cancel').style.display = 'none';
  document.getElementById('receive').style.display = 'none';
  document.getElementById('cancel_remark').style.display = 'none';
  document.getElementById('receive_remark').style.display = 'none';
  document.getElementById('other_remark').style.display = 'none';
  document.getElementById('waybill').style.display = 'none';
  document.getElementById('plate_no').style.display = 'none';
  document.getElementById('driver').style.display = 'none';
  document.getElementById('other_remark').style.display = 'none';
  
  // printArea();
});

function printTransmittal() {
  const printContents = document.getElementById('printableArea').innerHTML;
  const originalContents = document.body.innerHTML;
  document.body.innerHTML = printContents;
  window.print();
  document.body.innerHTML = originalContents;
}

function printArea() {
  win = window.open();
  var divToPrint = document.getElementById("printableArea");
  win.document.write(divToPrint.outerHTML);
  win.focus();
  win.print();
  win.close();
}

function populate_body(id) {
  $.ajax({
    url: baseUrl("eforms/transmittal/populate_body/") + id,
    type: "GET",
    dataType: "JSON",
    success: function (data) {
      $.each(data, function (key, val) {
        $('#tbody').append('<tr>' + '<td width="100%"><b>' + val.description + '</b></td>' + '</tr>');
      });
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}