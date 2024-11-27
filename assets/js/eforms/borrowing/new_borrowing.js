var isAdded = false;

var borrower = $("#select2_borrower").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  minimumInputLength: 3,
  ajax: {
    url: baseUrl("eforms/borrowing/get_borrower_collection"),
    delay: 250,
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

var asset = $("#select2_asset").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  minimumInputLength: 3,
  ajax: {
    url: baseUrl("eforms/borrowing/get_asset_collection"),
    delay: 250,
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

var vehicle = $("#select2_vehicle").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  minimumInputLength: 3,
  ajax: {
    url: baseUrl("eforms/borrowing/get_vehicle_collection"),
    delay: 250,
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});
var date = new Date();
var today = new Date(date.getFullYear(), date.getMonth(), date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds());
$('#trans_date').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  setDate: new Date(),
  format: 'mm/dd/yyyy hh:ii:ss',
});
$('#trans_date').datetimepicker('setDate', today);

var date_needed = $('#need_dt').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'mm/dd/yyyy hh:ii:ss',
});

var borrowed_dt = $('#borrowed_dt').datetimepicker({
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

var due_dt = $('#due_dt').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'mm/dd/yyyy hh:ii',
});

$('#date_borrowed').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'mm/dd/yyyy H:i:s',
});

$('#date_due').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'mm/dd/yyyy H:i:s',
});

var search_val = "";
var check = "0";
var tblContent = $("#table-content").DataTable({
  dom: '<"toolbar">rt',
  serverSide: true,
  processing: true,
  ajax: {
    url: baseUrl("eforms/borrowing/get_temp_request/"),
    type: "post",
    dataType: "json",
    data: function (d) {
      d.csrf_token = _csrf_hash,
        d.search['value'] = search_val

    }
  },
  searching: true,
  columns: [
    { data: "item_borrowed" },
    { data: "pieces" },
    { data: "date_borrowed", render: function (data, type, row, meta) { return dateFormat(row.date_borrowed); } },
    { data: "date_due", render: function (data, type, row, meta) { return dateFormat(row.date_due); } },
    { data: "remarks" },
    { data: null, width: "15%", className: "text-center" },
  ],
  columnDefs: [
    {
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
    _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_content(" + $id + ")'><i class='la la-pencil-square'></i></button>";
    _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' onclick='open_delete(" + $id + ")'><i class='la la-trash'></i></button>";
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
        url: baseUrl("eforms/borrowing/delete_temp_all_content/"),
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

function delete_temp_content() {
  $temp = $('[name="delete_id"]').val();
  // ajax delete data to database
  $.ajax({
    url: baseUrl("eforms/borrowing/delete_temp_content/") + $temp,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash },
    success: function (data) {
      //if success reload ajax table
      tblContent.ajax.reload();
      $("#modal_form_delete").modal("hide");
      toastr.warning("Borrowing content has been removed.", "Notice")
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
  document.getElementById('btnSaveContinue').style.removeProperty('display');
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
  document.getElementById('btnSaveContinue').style.display = 'none';
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
  document.getElementById('btnSaveContinue').style.removeProperty('display');
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
    url: baseUrl("eforms/borrowing/edit_temp_content/") + id,
    type: "GET",
    dataType: "JSON",
    success: function (data) {
      save_method = 'update';
      $('#form_content')[0].reset();
      $('[name="id"]').val(data.id);

      if (data.type == 'asset') {
        document.getElementById('asset_name').style.removeProperty('display');
        document.getElementById('asset').style.removeProperty('display');
        document.getElementById('sample').style.display = 'none';
        document.getElementById('sample_qty').style.display = 'none';
        document.getElementById('vehicle').style.display = 'none';
        var newOption = new Option(data.asset_code, data.asset_id, true, true);
        $('#select2_asset').append(newOption).trigger('change');
        $('.modal-title').text('Edit Asset');
        $('[name="type"]').val(data.type);
      }

      if (data.type == 'sample') {
        document.getElementById('asset_name').style.display = 'none';
        document.getElementById('asset').style.display = 'none';
        document.getElementById('sample').style.removeProperty('display');
        document.getElementById('sample_qty').style.removeProperty('display');
        document.getElementById('vehicle').style.display = 'none';
        $('[name="sample"]').val(data.asset_code);
        $('[name="quantity"]').val(data.quantity);
        $('[name="desc"]').val(data.asset_name);
        $('[name="type"]').val(data.type);
        $('.modal-title').text('Edit Sample Item');
      }

      if (data.type == 'vehicle') {
        document.getElementById('asset_name').style.removeProperty('display');
        document.getElementById('asset').style.display = 'none';
        document.getElementById('sample').style.display = 'none';
        document.getElementById('sample_qty').style.display = 'none';
        document.getElementById('vehicle').style.removeProperty('display');
        var newOption = new Option(data.asset_code, data.asset_id, true, true);
        $('#select2_vehicle').append(newOption).trigger('change');
        $('.modal-title').text('Edit Vehicle Component');
        $('[name="type"]').val(data.type);
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
  if (typeof veh !== "undefined" && veh) {
    $.ajax({
      url: baseUrl('eforms/borrowing/ajax_vehicle_details/') + veh,
      type: "POST",
      dataType: "JSON",
      data: { csrf_token: _csrf_hash },
      success: function (data) {
        $('[name="name"]').val(data.name);
        $('[name="desc"]').val(data.description);
        $('[name="code"]').val(data.gen_code);
      }, error: function (jqXHR, textStatus, errorThrown) {
        alert('Error: "ajax_vehicle_details"');
      }
    });
  }
}

function asset_details() {
  var asset = $('[name="asset"]').val();
  if (typeof asset !== "undefined" && asset) {
    $.ajax({
      url: baseUrl('eforms/borrowing/ajax_asset_details/') + asset,
      type: "POST",
      dataType: "JSON",
      data: { csrf_token: _csrf_hash },
      success: function (data) {
        $('[name="name"]').val(data.name);
        $('[name="code"]').val(data.assetacode);
        $('[name="desc"]').val(data.assetname);
      }, error: function (jqXHR, textStatus, errorThrown) {
        alert('Error: "ajax_asset_details"');
      }
    });
  }
}

function save_continue() {
  save_content(false);
}

function save_content(closeModal = true) {
  var url;
  var message;
  if (save_method == 'add') {
    url = baseUrl("eforms/borrowing/add_temp_content/");
    message = "Add content successful";
  } else {
    url = baseUrl("eforms/borrowing/update_temp_content/");
    message = "Updated content successfully";
  }


  asset.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });

  vehicle.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });

  borrowed_dt.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });

  due_dt.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });

  $.validate({
    form: '#form_content',
    lang: 'en',
    onSuccess: function (form) {
      var disabled = $('#form_content').find('textarea:disabled').removeAttr('disabled');
      $.ajax({
        url: url,
        type: "POST",
        data: $('#form_content').serialize(),
        dataType: "JSON",
        success: function (data) {
          if (data.status) {
            tblContent.ajax.reload();
            disabled.attr('disabled', 'disabled');
            if (closeModal) {
              $("#modal_form_content").modal("hide");
            } else {
              var tempType = $("#form_content").find("input[name=type]");
              $("#form_content").find("textarea[name=name]").val("");
              $("#form_content").find("textarea[name=desc]").val("");
              if (tempType.val() == "asset") {
                var tempSelect0 = $("#form_content").find("select#select2_asset");
                if (typeof tempSelect0 !== "undefined" && tempSelect0.length == 1) {
                  tempSelect0.val("");
                  tempSelect0.trigger("change");
                }
              }
              if (tempType.val() == "vehicle") {
                var tempSelect1 = $("#form_content").find("select#select2_vehicle");
                if (typeof tempSelect1 !== "undefined" && tempSelect1.length == 1) {
                  tempSelect1.val("");
                  tempSelect1.trigger("change");
                }
              }
            }
            toastr.success(message, "Notice");
          } else {
            alert('Error get data from ajax');
          }
        }
      });
      return false;
    },
  });
}


function add_borrowing() {
  var url;
  url = baseUrl("eforms/borrowing/add_borrowing");

  if (check == "0") {
    $('#table_v').empty();
    $('#table_v').append('<p><font color="#FF0000">Required. Add atleast 1 Content</font></p>');
  }

  borrower.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });

  date_needed.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });

  $.validate({
    form: '#form_borrowing',
    lang: 'en',
    onSuccess: function (form) {
      if (check == "1") {
        $('#table_v').empty();
        $.ajax({
          url: url,
          type: "POST",
          data: $('#form_borrowing').serialize(),
          dataType: "JSON",
          success: function (data) {
            if (data.status) {
              toastr.success(data.message, "Successfully saved!");
              window.location.replace(baseUrl("eforms/borrowing/masterfile"));
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

function add_sample_items() {
  cat = "sample";
  var _modalWindowSample = $("#modal_form_sample");
  _modalWindowSample.find(".modal-title").text("Add Sample Items");

  $('#btnSaveSample').removeAttr('disabled');
  $('#btnCloseModal1').removeAttr('disabled');

  _modalWindowSample.find('[name="body_id"]').val('0');
  _modalWindowSample.find('[name="employee"]').val($('[name="borrower"]').val());
  _modalWindowSample.find('[name="ref_no"]').val($('[name="reference_no"]').val());
  _modalWindowSample.find('[name="date_borrowed"]').val(moment().format('YYYY-MM-DD HH:mm:ss'));
  _modalWindowSample.find('[name="date_due"]').val();

  _modalWindowSample.modal('show');
  sample();
}

function sample() {
  var _modalWindowSample = $("#modal_form_sample");
  $.ajax({
    url: baseUrl("eforms/borrowing/ajax_sample_name"),
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash },
    success: function (data) {
      if (data == null) {
        var count = 0;
        count += 1;
        _modalWindowSample.find('[name="sample_name"]').val('Sample ' + count);
        _modalWindowSample.find('[name="counter"]').val(count);
      } else {
        var count = parseInt(data.counter);
        count += parseInt(1);
        _modalWindowSample.find('[name="sample_name"]').val('Sample ' + count);
        _modalWindowSample.find('[name="counter"]').val(count);
      }
    }, error: function (errorThrown) {
      console.log(errorThrown);
    }
  });
}

$("#clear_modal").hide();