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

var company_desc;
var dep_desc;
var name;
var deliver;
var vehicle;
var tempData = {};

$.ajax({
  url: baseUrl("eforms/transmittal/ajax_transmittal_details2/") + param_id,
  type: "GET",
  dataType: "JSON",
  success: function (data) {
    company_desc = data.company_desc;
    dep_desc = data.department_desc;
    name = data.name;

    var company_from = new Option(company_desc, data.data.company_from, true, true);
    $('#select2_file').append(company_from).trigger('change');

    vmTab1.select2Department('#select2_dep', true, data.data.company_from, { id: data.data.department_from, text: data.department_desc });
    
    // var department_from = new Option(data.department_desc, data.data.department_from, true, true);
    // $('#select2_dep').append(department_from).trigger('change');

    var requested_by = new Option(name, data.data.requested_by, true, true);
    $('#select2_requested').append(requested_by).trigger('change');

    if (data.data.cat == "in") {
      var ship_to = new Option(data.deliver, data.data.ship_to, true, true);
      $('#select2_deliver').append(ship_to).trigger('change');
    }
    if (data.data.is_service == 1) {
      var vehicle_id = new Option(data.vehicle, data.data.vehicle_id, true, true);
      $('#select2_vehicle').append(vehicle_id).trigger('change');
    }

    if (data.data.is_service == 1) {
      radiobtn = document.getElementById("other");
      radiobtn.checked = false;
      radiobtn = document.getElementById("service");
      radiobtn.checked = true;
      vehicle = data.vehicle;
      document.getElementById('other_remark').style.display = 'none';
      document.getElementById('service_veh').style.removeProperty('display');
      document.getElementById('service_driver').style.removeProperty('display');
    } else if (data.data.is_others == 1) {
      radiobtn = document.getElementById("other");
      radiobtn.checked = true;
      radiobtn = document.getElementById("service");
      radiobtn.checked = false;
      document.getElementById('other_remark').style.removeProperty('display');
      document.getElementById('service_veh').style.display = 'none';
      document.getElementById('service_driver').style.display = 'none';
    }

    if (data.data.cat == "in") {
      var type = new Option('INTERNAL', 'internal', true, true);
      $('#type').append(type).trigger('change');
      //$('[name="type"]').val('internal').trigger("change");
      deliver = data.deliver;
      document.getElementById('company_to_in').style.removeProperty('display');
      document.getElementById('company_to_ex').style.display = 'none';
      document.getElementById('delivery_to_in').style.removeProperty('display');
      document.getElementById('delivery_to_ex').style.display = 'none';
      document.getElementById('row_department').style.display = 'none';
      document.getElementById('row_courier').style.display = 'none';
    }
    if (data.data.cat == "ex") {
      var type = new Option('EXTERNAL', 'external', true, true);
      $('#type').append(type).trigger('change');
      //$('[name="type"]').val('external').trigger("change");
      document.getElementById('company_to_ex').style.removeProperty('display');
      document.getElementById('company_to_in').style.display = 'none';
      document.getElementById('delivery_to_in').style.display = 'none';
      document.getElementById('delivery_to_ex').style.removeProperty('display');
      document.getElementById('row_department').style.removeProperty('display');
      document.getElementById('row_courier').style.removeProperty('display');
    }
    if (data.data.priority == "Normal") {
      var priority = new Option('Normal', 'Normal', true, true);

      $('#priority').append(priority).trigger('change');
      // $('[name="priority"]').val('Normal').trigger("change");
    }
    if (data.data.priority == "Important") {
      var priority = new Option('Important', 'Important', true, true);
      $('#priority').append(priority).trigger('change');
      // $('[name="priority"]').val('Important').trigger("change");
    }

    vmTab1.vm_tab1 = Object.assign({}, data.data);

    $('textarea[name="deliver_address"]').val(data.data.ship_to_address);

  },
  error: function (jqXHR, textStatus, errorThrown) {
    alert("Error get data from ajax");
  }
});

var vmTab1 = new Vue({
  el: "#form_transmittal",
  data: { vm_tab1: {} },
  mounted: function () {

    var file_under = $("#select2_file").select2({
      placeholder: 'SELECT AN OPTION',
      width: '100%',
      ajax: {
        url: baseUrl("eforms/transmittal/get_company_collection"),
        global: false,
        delay: 250,
        processResults: function (data) {
          return data;
        }
      }
    });

    var data = [
      {
        id: "internal",
        text: "INTERNAL"
      },
      {
        id: "external",
        text: "EXTERNAL"
      }
    ];

    $("#type").select2({
      placeholder: 'Select. .',
      width: '100%',
      data: data
    });


    var data2 = [
      {
        id: "Normal",
        text: "Normal"
      },
      {
        id: "Important",
        text: "Important"
      }
    ];

    $("#priority").select2({
      placeholder: 'Select. .',
      width: '100%',
      data: data2
    });

    // var department = $("#select2_dep").select2({
    //   placeholder: 'SELECT AN OPTION',
    //   width: '100%',
    //   ajax: {
    //     url: baseUrl("eforms/transmittal/get_department_collection"),
    //     global: false,
    //     delay: 250,
    //     processResults: function (data) {
    //       return data;
    //     }
    //   }
    // })

    this.select2Department('#select2_dep', true);

    var requested_by = $("#select2_requested").select2({
      placeholder: 'SELECT AN OPTION',
      width: '100%',
      minimumInputLength: 3,
      ajax: {
        url: baseUrl("eforms/transmittal/get_request_collection"),
        global: false,
        delay: 250,
        processResults: function (data) {
          return data;
        }
      }
    });

    var deliver = $("#select2_deliver").select2({
      placeholder: 'SELECT AN OPTION',
      width: '100%',
      minimumInputLength: 3,
      ajax: {
        url: baseUrl("eforms/transmittal/get_request_collection"),
        global: false,
        delay: 250,
        processResults: function (data) {
          return data;
        }
      }
    });

    var vehicle = $("#select2_vehicle").select2({
      placeholder: 'SELECT AN OPTION',
      width: '100%',
      ajax: {
        url: baseUrl("eforms/transmittal/get_vehicle_collection"),
        global: false,
        delay: 250,
        processResults: function (data) {
          return data;
        }
      }
    });

    setTimeout(function () {
      var vmData = this.vmTab1.vm_tab1;
    }, 400);

  },
  methods: {
    select2Department (targetElement, destroy = false, id = 0, formData = {}){
      const currentTarget = $(targetElement);
      const select2Init = currentTarget.data('select2');

      if (destroy) {
        currentTarget.empty();
        if (typeof select2Init !== 'undefined') { select2Init.destroy(); }
        currentTarget.off('select2:select');
      }

      if (typeof formData !== 'undefined' && formData) {
        var department_from = new Option(formData.text, formData.id, true, true);
        $('#select2_dep').append(department_from).trigger('change');
      }

      var isDisabled = id == 0 ? true : false;

      currentTarget.prop('disabled', isDisabled);

      currentTarget.select2({
        placeholder: 'SELECT AN OPTION',
        width: '100%',
        allowClear: true,
        ajax: {
          url: baseUrl("eforms/transmittal/get_department_collection"),
          global: false,
          delay: 250,
          data: function ({ term }) {
            return {
              q: term,
              company_id: id
            }  
          },
          processResults: function (data) {
            return data;
          }
        }
      }).on("select2:select", function (e) {
        var self = $(e.target);
        self.validate();
      });
    }
  }
});

var search_val = "";
var check = "0";
var tblContent = $("#table-content").DataTable({
  dom: '<"toolbar">rtp',
  serverSide: true,
  processing: true,
  ajax: {
    url: baseUrl("eforms/transmittal/get_content_request/") + param_id,
    type: "post",
    dataType: "json",
    data: function (d) {
      d.csrf_token = _csrf_hash,
        d.search['value'] = search_val

    }
  },
  searching: true,
  columns: [
    { data: "description" },
    { data: null, width: "15%", className: "text-center" },
  ],
  columnDefs: [
    { targets: [1], width: "15%" },
    {
      data: null,
      defaultContent: "",
      targets: -1,
      orderable: false,

      render: function (data, type, row, meta) { return itemDatatableActions(row.id); },
    }


  ]
});

function emp_details() {
  var emp = $('[name="deliver_to"]').val();
  // if ($('#deliver_company_in').val() !== "") {
    $.ajax({
      url: baseUrl('eforms/transmittal/ajax_emp_details/') + emp,
      type: "POST",
      dataType: "JSON",
      data: { csrf_token: _csrf_hash },
      success: function (data) {
        var _compDisplay = (parseInt(data.use_str) === 1) ? data.comp_str : data.company_id;
        var _deptDisplay = (parseInt(data.temp_dep_str) === 0) ? data.dep_str : data.department_id;
        var _posDisplay = (parseInt(data.temp_pos_str) === 0) ? data.pos_str : data.position;
        var tempDisplay = _compDisplay + '\n' + _deptDisplay + '\n' + _posDisplay;
  

        /*** $('[name="deliver_company"]').val(data.company_id + '\n' + data.department_id + '\n' + data.position); ***/
        $('[name="deliver_company"]').val(tempDisplay);
        $('input[name="deliver_address"]').val(data.company_address);
      }, error: function (jqXHR, textStatus, errorThrown) {
        alert('Error: "ajax_emp_details"');
      }
    });
  // }

}

function veh_details() {
  var veh = $('[name="vehicle"]').val();
  if ($('[name="driver"]').val() !== "") {
    $.ajax({
      url: baseUrl('eforms/Transmittal/ajax_vehicle_details/') + veh,
      type: "POST",
      dataType: "JSON",
      data: { csrf_token: _csrf_hash },
      success: function (data) {
        $('[name="driver"]').val(data.driver);
      }, error: function (jqXHR, textStatus, errorThrown) {
        alert('Error: "ajax_vehicle_details"');
      }
    });
  }
}

var d = new Date();
$('#delivery_date').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'yyyy/mm/dd hh:ii',
  minDate: d.setDate(d.getDate() - 14),
});

function itemDatatableActions($id) {
  if ($id) {
    check = "1";
    var _actionButton = "";
    _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_content(" + $id + ")'><i class='la la-pencil-square'></i></button>";
    _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' onclick='open_delete(" + $id + ")'><i class='la la-trash'></i></button>";
    return _actionButton;
  } else { return false; }
}

function open_delete($id) {
  $('[name="delete_id"]').val($id);
  $('#modal_form_delete').modal('show'); // show bootstrap modal
  $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}

function add_content() {
  save_method = 'add';
  $('#form_content')[0].reset(); // reset form on modals
  $('#modal_form_content').modal('show'); // show bootstrap modal
  $('.modal-title').text('Add Content'); // Set Title to Bootstrap modal title
}

function edit_content(id) {
  save_method = 'update';
  $('#form_content')[0].reset();
  $.ajax({
    url: baseUrl("eforms/transmittal/edit_content/") + id,
    type: "GET",
    dataType: "JSON",
    success: function (data) {
      $('[name="id"]').val(data.id);
      $('[name="description"]').val(data.description);
      $('#modal_form_content').modal('show'); // show bootstrap modal
      $('.modal-title').text('Edit Content'); // Set Title to Bootstrap modal title
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error get data from ajax');
    }
  });
}

function save_content() {
  var url;
  if (save_method == 'add') {
    url = baseUrl("eforms/transmittal/add_content/") + param_id;
  } else {
    url = baseUrl("eforms/transmittal/update_content/");
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
            tblContent.ajax.reload();
            $("#modal_form_content").modal("hide");
            if (save_method == 'add') {
              toastr.success(data.message, "Content added successfully!", 5000);
            }
            else {
              toastr.success(data.message, "Updated successfully!", 5000);
            }
          } else {
            alert('Error get data from ajax');
          }
        }
      });
      return false;
    },
  });
}

function view_back() {
  window.location.replace(baseUrl("eforms/transmittal/view_transmittal?id=") + param_id);
}

function update_transmittal() {
  var url;
  url = baseUrl("eforms/transmittal/update_transmittal/") + param_id;
  if (tblContent.data().length == 0) {
    $('#table_v').empty();
    $('#table_v').append('<p><font color="#FF0000">Required. Add atleast 1 Content</font></p>');
  }

  $.validate({
    form: '#form_transmittal',
    lang: 'en',
    onSuccess: function (form) {
      if (tblContent.data().length !== 0) {
        $('#table_v').empty();
        var disabled = $('#form_transmittal').find('textarea:disabled, input:disabled, select:disabled').removeAttr('disabled');
        $.ajax({
          url: url,
          type: "POST",
          data: $('#form_transmittal').serialize(),
          dataType: "JSON",
          success: function (data) {
            if (data.status) {
              disabled.attr('disabled', 'disabled');
              toastr.success(data.toastr_msg, "Updated successfully!", 5000);
              window.location.replace(baseUrl("eforms/transmittal/view_transmittal?id=") + param_id);
            } else {
              alert('Error get data from ajax');
            }
          }
        });
      }
      return false;
    },
  });

  file_under.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });

  department.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });

  requested_by.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });

  deliver.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });

  vehicle.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });


  $("#delivery_dt").on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });

}

function delete_content() {
  $temp = $('[name="delete_id"]').val();
  // ajax delete data to database
  $.ajax({
    url: baseUrl("eforms/transmittal/delete_content/") + $temp,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash },
    success: function (data) {
      //if success reload ajax table
      tblContent.ajax.reload();
      $("#modal_form_delete").modal("hide");
      toastr.success(data.toastr_msg, "Removed successfully!", 5000);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      toastr.error(data.toastr_msg, "Error removing items!", 5000);
    }
  });
}

$("#clear_modal").hide();
function clear_content() {
  $('.modal-title').text('Clear'); // Set Title to Bootstrap modal title
  $("#clear_modal").modal("show");
  $.validate({
    form: '#clear_form',
    lang: 'en',
    onSuccess: function (form) {
      $.ajax({
        url: baseUrl("eforms/transmittal/delete_all_content/") + param_id,
        type: "POST",
        dataType: "json",
        data: $("#clear_form").find("input,textarea").serialize(),
        beforeSend: function () {
          $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function (data) {
          if (data) {
            $('#clear_modal').modal('hide');
            tblContent.ajax.reload();
            toastr.success(data.toastr_msg, "Removed successfully!", 5000);
          } else {
            toastr.error(data.toastr_msg, "Error removing items!", 5000);
          }
          $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
        }
      });
      return false;
    },
  });

}

function type_change() {
  var type = $('[name="type"]').val();
  if (type == "internal") {
    document.getElementById('delivery_to_in').style.removeProperty('display');
    document.getElementById('delivery_to_ex').style.display = 'none';
    document.getElementById('row_department').style.display = 'none';
    document.getElementById('row_courier').style.display = 'none';
  }
  if (type == "external") {
    document.getElementById('delivery_to_in').style.display = 'none';
    document.getElementById('delivery_to_ex').style.removeProperty('display');
    document.getElementById('row_department').style.removeProperty('display');
    document.getElementById('row_courier').style.removeProperty('display');
  }
}

function change_other() {
  radiobtn = document.getElementById("service");
  radiobtn.checked = false;

  document.getElementById('other_remark').style.removeProperty('display');
  document.getElementById('service_veh').style.display = 'none';
  document.getElementById('service_driver').style.display = 'none';
}

function change_service() {
  radiobtn = document.getElementById("other");
  radiobtn.checked = false;

  document.getElementById('other_remark').style.display = 'none';
  document.getElementById('service_veh').style.removeProperty('display');
  document.getElementById('service_driver').style.removeProperty('display');
}

$(document).ready(function () {
  document.getElementById('delivery_to_ex').style.display = 'none';
  document.getElementById('row_department').style.display = 'none';
  document.getElementById('row_courier').style.display = 'none';
  document.getElementById('other_remark').style.display = 'none';
});
