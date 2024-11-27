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

param_id = getUrlParameter("id");
var tempData = {};
var name;
var borrower_id;

$.ajax({
  url: baseUrl("eforms/borrowing/ajax_borrowing_details/") + param_id,
  type: "GET",
  dataType: "JSON",
  success: function (data) {

    $('[name="description"]').val(data.data.company + '\n' + data.data.department + '\n' + data.data.position);

    name = data.name;
    borrower_id = data.data.borrower_id;
    var newOption = new Option(name, borrower_id, true, true);
    $('#select2_borrower').append(newOption).trigger('change');

    vmTab1.vm_tab1 = Object.assign({}, data.data);

  },
  error: function (jqXHR, textStatus, errorThrown) {
    alert("Error get data from ajax");
  }
});

var vmTab1 = new Vue({
  el: "#form_borrowing",
  data: { vm_tab1: {} },
  mounted: function () {
    setTimeout(function () {
      var vmData = this.vmTab1.vm_tab1;

    }, 400);
  }
});

$("#select2_borrower").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/borrowing/get_borrower_collection"),
    global: false,
    processResults: function (data) {
      return data;
    }

  }
});

$("#select2_asset").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/borrowing/get_asset_collection"),
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

$("#select2_vehicle").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/borrowing/get_vehicle_collection"),
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

$('#trans_date').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'mm/dd/yyyy hh:ii',
});

$('#need_dt').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'mm/dd/yyyy hh:ii',
});

$('#borrowed_dt').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'mm/dd/yyyy hh:ii',
}).on('changeDate', function (ev) {
  var dueStartDate = moment(ev.date).add(1, 'hours').format("YYYY-MM-DD HH:mm");
  var dueEndDate = moment(dueStartDate).add(30, 'days').format("YYYY-MM-DD HH:mm");
  $('#due_dt > input').val("");
  $('#due_dt')
    .datetimepicker("setStartDate", dueStartDate)
    .datetimepicker("setEndDate", dueEndDate);
});

$('#due_dt').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'mm/dd/yyyy hh:ii',
});

var search_val = "";
var check = "0";
var tblContent = $("#table-content").DataTable({
  dom: '<"toolbar">rt',
  serverSide: true,
  processing: true,
  ajax: {
    url: baseUrl("eforms/borrowing/get_content_request/") + param_id,
    type: "post",
    dataType: "json",
    data: function (d) {
      d.csrf_token = _csrf_hash,
        d.search['value'] = search_val

    }
  },
  searching: true,
  columns: [
    { data: "asset" },
    { data: "pieces" },
    { data: "date_borrowed", render: function (data, type, row, meta) { return dateFormat(row.date_borrowed); } },
    { data: "date_due", render: function (data, type, row, meta) { return dateFormat(row.date_due); } },
    { data: "date_returned" },
    { data: null, width: "15%", className: "text-center" },
  ],
  columnDefs: [{
    data: null,
    defaultContent: "",
    targets: -1,
    orderable: false,
    render: function (data, type, row, meta) { return itemDatatableActions(row.id); },
  }
  ]
});

function dateFormat(data) {
  if (data == "0000-00-00 00:00:00" || data == null || data == "") {
    return "";
  }
  else {
    return moment(data).format('lll');
  }
}

function itemDatatableActions($id) {
  if ($id) {
    check = "1";
    var _actionButton = "";
    if ($.inArray("edit", _currentActions) !== -1) {
      _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_content(" + $id + ")'><i class='la la-pencil-square'></i></button>";

    }
    if ($.inArray("delete", _currentActions) !== -1) {
      _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' onclick='open_delete(" + $id + ")'><i class='la la-trash'></i></button>";
    }
    _actionButton = _actionButton ? _actionButton : "---";
    return _actionButton;
  } else { return false; }

}
function open_delete($id) {
  $('[name="delete_id"]').val($id);
  $('#modal_form_delete').modal('show'); // show bootstrap modal
  $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}

function clear_content() {
  $('#clear_modal').modal('show');
  $('.modal-title').text('Clear Form'); // Set Title to Bootstrap modal title
  $.validate({
    form: '#clear_form',
    lang: 'en',
    onSuccess: function (form) {
      $.ajax({
        url: baseUrl("eforms/borrowing/delete_all_content/") + param_id,
        type: "POST",
        dataType: "json",
        data: $("#clear_form").find("input,textarea").serialize(),
        beforeSend: function () {
          $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function (data) {
          if (data) {
            $('#clear_modal').modal('hide');
            $('#clear_form')[0].reset();
            tblContent.ajax.reload();
            toastr.success(data.toastr_msg, "Removed contents successfully!", 5000);
          } else {
            toastr.error(data.toastr_msg, "Error", 5000);
          }
          $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }
      });
      return false;
    },
  });
}

function delete_content() {
  $temp = $('[name="delete_id"]').val();
  $.ajax({
    url: baseUrl("eforms/borrowing/delete_content/") + $temp,
    type: "GET",
    dataType: "JSON",
    success: function (data) {
      tblContent.ajax.reload();
      $("#modal_form_delete").modal("hide");
      toastr.success(data.message, "Removed content successfully!");
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error adding / update data');
    }
  });
}

function add_asset() {
  save_method = 'add';
  $('[name="type"]').val('asset');
  document.getElementById('asset_name').style.removeProperty('display');
  document.getElementById('asset').style.removeProperty('display');
  document.getElementById('sample').style.display = 'none';
  document.getElementById('sample_qty').style.display = 'none';
  document.getElementById('vehicle').style.display = 'none';

  $('#form_content')[0].reset(); // reset form on modals
  $('#modal_form_content').modal('show'); // show bootstrap modal
  $('.modal-title').text('Add Assets / Components'); // Set Title to Bootstrap modal title
}

function add_item() {
  save_method = 'add';
  $('[name="type"]').val('sample');
  document.getElementById('asset_name').style.display = 'none';
  document.getElementById('asset').style.display = 'none';
  document.getElementById('sample').style.removeProperty('display');
  document.getElementById('sample_qty').style.removeProperty('display');
  document.getElementById('vehicle').style.display = 'none';
  $('#form_content')[0].reset(); // reset form on modals
  $('#modal_form_content').modal('show'); // show bootstrap modal
  $('.modal-title').text('Add Sample Item'); // Set Title to Bootstrap modal title
}

function add_vehicle() {
  save_method = 'add';
  $('[name="type"]').val('vehicle');
  document.getElementById('asset_name').style.removeProperty('display');
  document.getElementById('asset').style.display = 'none';
  document.getElementById('sample').style.display = 'none';
  document.getElementById('sample_qty').style.display = 'none';
  document.getElementById('vehicle').style.removeProperty('display');
  $('#form_content')[0].reset(); // reset form on modals
  $('#modal_form_content').modal('show'); // show bootstrap modal
  $('.modal-title').text('Add Vehicle Component'); // Set Title to Bootstrap modal title
}

function edit_content(id) {
  save_method = 'update';
  $('#form_content')[0].reset();
  $.ajax({
    url: baseUrl("eforms/borrowing/edit_content/") + id,
    type: "GET",
    dataType: "JSON",
    success: function (data) {
      save_method = 'update';
      $('#form_content')[0].reset();
      $('[name="id"]').val(data.id);
      if (data.type == 'asset') {
        $('[name="type"]').val(data.type);
        document.getElementById('asset_name').style.removeProperty('display');
        document.getElementById('asset').style.removeProperty('display');
        document.getElementById('sample').style.display = 'none';
        document.getElementById('sample_qty').style.display = 'none';
        document.getElementById('vehicle').style.display = 'none';
        var newOption = new Option(data.asset_code, data.asset_id, true, true);
        $('#select2_asset').append(newOption).trigger('change');
        $('.modal-title').text('Edit Asset');
      }
      if (data.type == 'sample') {
        $('[name="type"]').val(data.type);
        document.getElementById('asset_name').style.display = 'none';
        document.getElementById('asset').style.display = 'none';
        document.getElementById('sample').style.removeProperty('display');
        document.getElementById('sample_qty').style.removeProperty('display');
        document.getElementById('vehicle').style.display = 'none';
        $('[name="sample"]').val(data.asset_code);
        $('[name="quantity"]').val(data.quantity);
        $('[name="desc"]').val(data.asset_name);
        $('.modal-title').text('Edit Sample Item');
      }
      if (data.type == 'vehicle') {
        $('[name="type"]').val(data.type);
        document.getElementById('asset_name').style.removeProperty('display');
        document.getElementById('asset').style.display = 'none';
        document.getElementById('sample').style.display = 'none';
        document.getElementById('sample_qty').style.display = 'none';
        document.getElementById('vehicle').style.removeProperty('display');
        var newOption = new Option(data.asset_code, data.asset_id, true, true);
        $('#select2_vehicle').append(newOption).trigger('change');
        $('.modal-title').text('Edit Vehicle Component');
      }
      $('[name="borrowed_dt"]').val(data.date_borrowed);
      $('[name="due_dt"]').val(data.date_due);
      $('[name="remarks"]').val(data.remarks);
      $('#modal_form_content').modal('show'); // show bootstrap modal
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error get data from ajax');
    }
  });
}

function emp_details() {
  var emp = $('[name="borrower"]').val();
  $.ajax({
    url: baseUrl('eforms/borrowing/ajax_emp_details/') + emp,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash },
    success: function (data) {
      $('[name="description"]').val(data.company_id + '\n' + data.department_id + '\n' + data.position);
    }, error: function (jqXHR, textStatus, errorThrown) {
      alert('Error: "ajax_emp_details"');
    }
  });
}

function vehicle_details() {
  var veh = $('[name="vehicle"]').val();
  $.ajax({
    url: baseUrl('eforms/borrowing/ajax_vehicle_details/') + veh,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash },
    success: function (data) {
      $('[name="name"]').val(data.name);
      $('[name="desc"]').val(data.description);
      $('[name="code"]').val(data.gen_code);
      //$('[name="price"]').val(data.purchaseprice);
    }, error: function (jqXHR, textStatus, errorThrown) {
      alert('Error: "ajax_vehicle_details"');
    }
  });
}

function asset_details() {
  var asset = $('[name="asset"]').val();
  $.ajax({
    url: baseUrl('eforms/borrowing/ajax_asset_details/') + asset,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash },
    success: function (data) {
      $('[name="name"]').val(data.name);
      $('[name="code"]').val(data.assetacode);
      $('[name="desc"]').val(data.assetname);
      //$('[name="price"]').val(data.purchaseprice);
    }, error: function (jqXHR, textStatus, errorThrown) {
      alert('Error: "ajax_asset_details"');
    }
  });
}

function save_content() {
  var url;
  if (save_method == 'add') {
    url = baseUrl("eforms/borrowing/add_content/") + param_id;
  }
  else {
    url = baseUrl("eforms/borrowing/update_content/");
  }

  $.validate({
    form: '#form_content',
    lang: 'en',
    onSuccess: function (form) {
      $.ajax({
        url: url,
        type: "POST",
        data: $('#form_content').serialize(),
        dataType: "JSON",
        success: function (data) {
          if (data.status) {
            toastr.success(data.message, "Item added successfully!");
            tblContent.ajax.reload();
            $("#modal_form_content").modal("hide");
          } else {
            alert('Error get data from ajax');
          }
        }
      });
      return false;
    },
  });
}

function update_borrowing() {
  var url;
  url = baseUrl("eforms/borrowing/update_borrowing/") + param_id;
  if (check == "0") {
    $('#table_v').empty();
    $('#table_v').append('<p><font color="#FF0000">Required. Add atleast 1 Content</font></p>');
  }

  $.validate({
    form: '#form_borrowing',
    lang: 'en',
    onSuccess: function (form) {
      if (check == "1") {
        $('#table_v').empty();
        var disabled = $('#modal_form_content').find('textarea:disabled').removeAttr('disabled');
        $.ajax({
          url: url,
          type: "POST",
          data: $('#form_borrowing').serialize(),
          dataType: "JSON",
          success: function (data) {
            if (data.status) {
              disabled.attr('disabled', 'disabled');
              toastr.success("", "Updated Successfully!");
              window.location.replace(baseUrl("eforms/borrowing/view_borrowing?id=") + param_id);
            } else {
              alert('Error get data from ajax');
            }
          }
        });
      }
      return false;
    },
  });
}

$("#clear_modal").hide();