const recommendationModal = $("#modal_recommend_approve");

Array.prototype.remove = function() {
    this.splice(0,this.length);
    return this;
};
const edit_id = [];
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
const param_id = getUrlParameter("id");
$('#to_id').val(param_id);

let defaultRedirect = siteUrl("eforms/travel_order/masterfile");
const viewRedirect = siteUrl("eforms/travel_order/view_travel_order?id="+param_id);
defaultRedirect = param_id ? viewRedirect: defaultRedirect;
const isView = typeof getUrlParameter("view") !== "undefined" ? JSON.parse(getUrlParameter("view")): false;
if(isView){ $("form#form_travel_order a.btnCancel").prop("href", viewRedirect); }

var tempData = {};
$.ajax({
  url: baseUrl("eforms/travel_order/ajax_travel_order_details/") + param_id,
  type: "GET",
  dataType: "JSON",
  success: function (data) {
    
    if (data.data.is_service == 1) {
      radiobtn = document.getElementById("service");
      radiobtn.checked = true;
      radiobtn = document.getElementById("hitch");
      radiobtn.checked = false;
      radiobtn = document.getElementById("commute");
      radiobtn.checked = false;
      radiobtn = document.getElementById("personal");
      radiobtn.checked = false;
      radiobtn = document.getElementById("other");
      radiobtn.checked = false;

      document.getElementById('other_remark').style.display = 'none';
      document.getElementById('service_veh').style.removeProperty('display');
      document.getElementById('service_driver').style.removeProperty('display');
    }
    else if (data.data.is_hitch == 1) {
      radiobtn = document.getElementById("service");
      radiobtn.checked = false;
      radiobtn = document.getElementById("hitch");
      radiobtn.checked = true;
      radiobtn = document.getElementById("commute");
      radiobtn.checked = false;
      radiobtn = document.getElementById("personal");
      radiobtn.checked = false;
      radiobtn = document.getElementById("other");
      radiobtn.checked = false;

      document.getElementById('other_remark').style.display = 'none';
      document.getElementById('service_veh').style.removeProperty('display');
      document.getElementById('service_driver').style.removeProperty('display');
    }
    else if (data.data.is_commute == 1) {
      radiobtn = document.getElementById("service");
      radiobtn.checked = false;
      radiobtn = document.getElementById("hitch");
      radiobtn.checked = false;
      radiobtn = document.getElementById("commute");
      radiobtn.checked = true;
      radiobtn = document.getElementById("personal");
      radiobtn.checked = false;
      radiobtn = document.getElementById("other");
      radiobtn.checked = false;

      document.getElementById('other_remark').style.display = 'none';
      document.getElementById('service_veh').style.display = 'none';
      document.getElementById('service_driver').style.display = 'none';
    }
    else if (data.data.is_personal == 1) {
      radiobtn = document.getElementById("service");
      radiobtn.checked = false;
      radiobtn = document.getElementById("hitch");
      radiobtn.checked = false;
      radiobtn = document.getElementById("commute");
      radiobtn.checked = false;
      radiobtn = document.getElementById("personal");
      radiobtn.checked = true;
      radiobtn = document.getElementById("other");
      radiobtn.checked = false;

      document.getElementById('other_remark').style.display = 'none';
      document.getElementById('service_veh').style.display = 'none';
      document.getElementById('service_driver').style.display = 'none';
    }
    else if (data.data.is_others == 1) {
      radiobtn = document.getElementById("service");
      radiobtn.checked = false;
      radiobtn = document.getElementById("hitch");
      radiobtn.checked = false;
      radiobtn = document.getElementById("commute");
      radiobtn.checked = false;
      radiobtn = document.getElementById("personal");
      radiobtn.checked = false;
      radiobtn = document.getElementById("other");
      radiobtn.checked = true;

      document.getElementById('other_remark').style.removeProperty('display');
      document.getElementById('service_veh').style.display = 'none';
      document.getElementById('service_driver').style.display = 'none';
    }

    if (data.data.type == "internal") {
      radiobtn = document.getElementById("internal");
      radiobtn.checked = true;
    } else {
      radiobtn = document.getElementById("external");
      radiobtn.checked = true;
    }

    $("[name='station']").val(data.data.station);
    $("[name='remark']").val(data.data.others_remarks);

    if (data.data.accomplish == 1){
      $('.to_status').html(`<div class="m-badge m-badge--success text-white m-badge--wide m--font-bolder" role="alert"><strong>Accomplished</strong></div>`);
    } else if(data.data.status == 'Approved'){
      $('.to_status').html(`<div class="m-badge m-badge--success text-white m-badge--wide m--font-bolder" role="alert"><strong>Approved</strong></div>`);
    } else if (data.data.status == 'Cancelled'){
      $('.to_status').html(`<div class="m-badge m-badge--metal text-white m-badge--wide m--font-bolder" role="alert"><strong>Cancelled</strong></div>`);
    } else {
      $('#layout_btn_destination').removeClass('m--hide');
      $('#layout_btn_personnel').removeClass('m--hide');
      $('#btnSaveTravel').removeClass('m--hide');
      $('#layout_content').css('pointer-events', '');
      $('#layout_bottom_content').css('pointer-events', '');
      
      if(data.data.status == 'Recommend_Approved'){
        $('.to_status').html(`<div class="m-badge m-badge--info text-white m-badge--wide m--font-bolder" role="alert"><strong>Pending Approval</strong></div>`);
      } else {
        $('.to_status').html(`<div class="m-badge m-badge--warning text-white m-badge--wide m--font-bolder" role="alert"><strong>For Recommendation</strong></div>`);
        if(_currentActions.includes("recommend")){
          $('#btnRecommendTravel').removeClass('m--hide');
        }
      }
    }
    
    var newOption = new Option(data.plateno, data.data.vehicle_id, true, true);
    if(data.data.vehicle_id > 0){ $('#select2_vehicle').append(newOption).trigger('change'); }
    var newOption = new Option(data.company_desc, data.data.company, true, true);
    $('#select2_file').append(newOption).trigger('change');
    newOption = new Option(data.department_desc, data.data.department, true, true);
    $('#select2_dep').append(newOption).trigger('change');
    newOption = new Option(data.data.driver, data.data.driver_id, true, true);
    if(data.data.driver_id > 0){ $('#driver').append(newOption).trigger('change'); }
    vmTab1.vm_tab1 = Object.assign({}, data.data);
  },
  error: function (jqXHR, textStatus, errorThrown) {
    alert("Error get data from ajax");
  }
});

var vmTab1 = new Vue({
  el: "#form_travel_order",
  data: { vm_tab1: tempData }
});

// var driver = $("#driver").select2({
//   placeholder: 'Select. .',
//   width: '100%',
//   ajax: {
//     url: baseUrl("eforms/shipping/driver"),
//     dataType: "json",
//     global: false,
//     delay: 500,
//     processResults: function (data) {
//       return data;
//     }
//   }
// });

var file_under = $("#select2_file").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/Travel_order/get_company_collection"),
    dataType: "json",
    global: false,
    delay: 500,
    processResults: function (data) {
      return data;
    }
  }
});

var department = $("#select2_dep").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/Travel_order/get_department_collection"),
    dataType: "json",
    global: false,
    delay: 500,
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
    url: baseUrl("eforms/Travel_order/get_vehicle_collection"),
    dataType: "json",
    global: false,
    delay: 500,
    processResults: function (data) {
      return data;
    }
  }
});

var emp = $("#select2_emp").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  minimumInputLength: 3,
  dropdownParent: $("#modal_form_personnel"),
  ajax: {
    url: baseUrl("eforms/Travel_order/get_request_collection"),
    dataType: "json",
    global: false,
    delay: 500,
    processResults: function (data) {
      return data;
    }
  }
});

var req = $("#select2_req").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  dropdownParent: $("#requested-by"),
  ajax: {
    url: baseUrl("eforms/Travel_order/get_request_collection"),
    dataType: "json",
    global: false,
    delay: 500,
    processResults: function (data) {
      return data;
    }
  }
});

var driver = $("#driver").select2({
  placeholder: 'Select. .',
  width: '100%',
  minimumInputLength: 3,
  ajax: {
    url: baseUrl("eforms/Travel_order/driver"),
    dataType: "json",
    global: false,
    delay: 500,
    processResults: function (data) {
      return data;
    }
  }
});

var dt_from = $('#date_from').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'yyyy/mm/dd hh:ii',
  startDate: getCurrentDate(),
});

var dt_to = $('#date_to').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'yyyy/mm/dd hh:ii',
  startDate: getCurrentDate(),
});

var search_val = "";
var check = "0";
var check2 = "0";
var tblPersonnel = $("#table-personnel").DataTable({
  dom: '<"toolbar">rt',
  serverSide: true,
  processing: true,
  ajax: {
    url: baseUrl("eforms/Travel_order/get_personnel/") + param_id,
    type: "post",
    dataType: "json",
    data: function (d) {
      d.csrf_token = _csrf_hash,
        d.search['value'] = search_val
    }
  },
  drawCallback: function(settings){
      personnelSize = settings.json.data.length;
      displayRequiredPersonel();
  },
  searching: true,
  columns: [
    { data: "firstname", render: function (data, type, row, meta) { return displayName(row.display_name) } },
    { data: "position", width: "40%" },
    { data: null, width: "20%", className: "text-center" },
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

function displayName($displayName) {
  return $displayName;
}

function itemDatatableActions($id) {
  if ($id) {
    check = "1";
    var _actionButton = "";
    if ($.inArray("edit", _currentActions) !== -1) {
      _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_personnel(" + $id + ")' data-container='body' data-toggle='m-tooltip' data-placement='top' data-original-title='Edit' data-skin='dark'><i class='la la-pencil-square'></i></button>";
    }
    if ($.inArray("delete", _currentActions) !== -1) {
      _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' onclick='open_delete(" + $id + ")' data-container='body' data-toggle='m-tooltip' data-placement='top' data-original-title='Delete' data-skin='dark'><i class='la la-trash'></i></button>";
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

var tblDestination = $("#table-destination").DataTable({
  dom: '<"toolbar">rt',
  serverSide: true,
  processing: true,
  ajax: {
    url: baseUrl("eforms/Travel_order/get_destination/") + param_id,
    type: "post",
    dataType: "json",
    data: function (d) {
      d.csrf_token = _csrf_hash,
        d.search['value'] = search_val
    }
  },
  drawCallback: function(settings){
    destinationSize = settings.json.data.length;
    displayRequiredDestinations();
  },
  searching: true,
  columns: [
    // { data: "id", render: function ( data, type, row, meta ) {
    //     let check;
    //     if(row.travel_order_status == 1){
    //       check = "checked";
    //     }
    //     if(row.accomplish == 1 || row.status === "Pending"){
    //       return '<input style="margin-top: 40px; margin-left: 10px; transform: scale(1.3);" type="checkbox" id="destination_check" checked>';
    //     }else if(row.status == "Approved"){
    //       return '<input style="margin-top: 40px; margin-left: 10px; transform: scale(1.3);" type="checkbox" id="destination_check" '+check+'>';
    //     }else{
    //       return '<input style="margin-top: 40px; margin-left: 10px; transform: scale(1.3);" type="checkbox" onchange="checkBox(this, '+row.id+')" id="destination_check" '+check+'>';
    //     }
    //   }
    // },
    { data: "destination", width: "45%", render: function (data, type, row, meta) { return specialInstruction(row.destination, row.instructions, row.travel_order_status) } },
    { data: "date_from", width: "40%", render: function (data, type, row, meta) { return formatCalendarDate(data, row) } },
    { data: null, width: "15%", className: "text-center" },
  ],
  columnDefs: [{
    data: null,
    defaultContent: "",
    targets: -1,
    orderable: false,
    render: function (data, type, row, meta) { return itemDatatableActions2(row.id); },
  }]
});

function checkBox($this, $id){
  let stat;
  if($this.checked){
    stat = 1;
  }else{
    stat = 0;
  }

  $.ajax({
    url: baseUrl("eforms/travel_order/destination_status"),
    type: "POST",
    data: {
      csrf_token: _csrf_hash,
      destination_id: $id,
      status: stat
    },
    success: function(resp){
      if(resp == 1){
        $.ajax({
          url: baseUrl("eforms/travel_order/accomplishment_button"),
          type: "POST",
          data: {
            csrf_token: _csrf_hash,
            travel_order_id: param_id
          },
          dataType: "json",
          success: function(resp){
            if(resp == 0){
              $("#btnaccomplish").attr('disabled',"disabled");
            }else{
              $("#btnaccomplish").removeAttr('disabled');
            }
            tblDestination.ajax.reload();
          }
        });
      }
    }
  });
}

function specialInstruction(destination, instruction, status){
  if(instruction){
    return destination+"<br>Special Instruction: "+instruction;
  }else{
    if(status == 1){
      return destination+'<br><div class="m-badge m-badge--success m-badge--wide m--margin-top-5" role="alert"><strong>Arrived</strong></div>';
    }else{
      return destination;
    }
  }
}

function itemDatatableActions2($id) {
  if ($id) {
    check2 = "1";
    var _actionButton = "";
    if ($.inArray("edit", _currentActions) !== -1) {
      _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_destination(" + $id + ")' data-container='body' data-toggle='m-tooltip' data-placement='top' data-original-title='Edit' data-skin='dark'><i class='la la-pencil-square'></i></button>";
    }
    if ($.inArray("delete", _currentActions) !== -1) {
      _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' onclick='open_delete2(" + $id + ")' data-container='body' data-toggle='m-tooltip' data-placement='top' data-original-title='Delete' data-skin='dark'><i class='la la-trash'></i></button>";
    }
    _actionButton = _actionButton ? _actionButton : "---";
    return _actionButton;
  } else { return false; }
}

function formatCalendarDate(data, row) {
  if (data == "0000-00-00 00:00:00") {
    return "";
  } else {
    return moment(data).format("MM/DD/YYYY hh:mm A") + " - " + moment(row.date_to).format("MM/DD/YYYY hh:mm A");
  }
}
function open_delete2($id) {
  $('[name="delete_id2"]').val($id);
  $('#modal_form_delete2').modal('show'); // show bootstrap modal
  $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}

function add_destination() {
  save_method = 'add';
  $('#form_destination')[0].reset(); // reset form on modals
  $('#modal_form_destination').modal('show'); // show bootstrap modal
  $('.modal-title').text('Add New Destination'); // Set Title to Bootstrap modal title
  var emptyOpt = new Option("", "", true, true);
  $('#select2_req').append(emptyOpt).trigger('change');
  initMapTemp();
}

function edit_destination(id) {
  initMapTemp(id);
  save_method = 'update';
  $('#form_destination')[0].reset();
  $.ajax({
    url: baseUrl("eforms/travel_order/edit_destination/") + id,
    type: "GET",
    dataType: "JSON",
    success: function (data) {
      $('[name="id_destination"]').val(data.id);
      $('[name="from"]').val(data.travel_from);
      $('[name="to"]').val(data.travel_to);
      var newOption = new Option(data.name, data.requested_by, true, true);
      $('#select2_req').append(newOption).trigger('change');
      $('[name="purpose"]').val(data.purpose);
      $('[name="date_from"]').val(data.date_from);
      $('[name="date_to"]').val(data.date_to);
      $('[name="special"]').val(data.instructions);
      $('[name="remarks"]').val(data.remarks);
      $('[name="formTravelFrom"]').val(data.coords_from);
      $('[name="formTravelTo"]').val(data.coords_to);
      $('#modal_form_destination').modal('show'); // show bootstrap modal
      $('.modal-title').text('Edit Destination'); // Set Title to Bootstrap modal title
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error get data from ajax');
    }
  });
}

function delete_destination() {
  $temp = $('[name="delete_id2"]').val();
  $.ajax({
    url: baseUrl("eforms/travel_order/delete_destination/") + $temp,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash },
    success: function (data) {
      isValidationShow = true;
      toastr.success(data.toastr_msg, "Removed content successfully!", 5000);
      tblDestination.ajax.reload();
      $("#modal_form_delete2").modal("hide");
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error adding / update data');
    }
  });
}

function clear_destination() {
  if(destinationSize == 0) return toastr.warning("No destination to be delete.");
  
  $('#clear_modal').modal('show');
  $('.modal-title').text('Clear');
  $.validate({
    form: '#clear_form',
    lang: 'en',
    onSuccess: function (form) {
      $.ajax({
        url: baseUrl("eforms/travel_order/delete_all_destination/") + param_id,
        type: "POST",
        dataType: "json",
        data: $("#clear_form").find("input,select,textarea").serialize(),
        beforeSend: function () {
          $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function (data) {
          isValidationShow = true;
          $("#clear_modal").modal("hide");
          toastr.success(data.toastr_msg, "Removed contents successfully!", 5000);
          tblDestination.ajax.reload();
          $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }
      });
      return false;
    },
  });
}

let url;
function save_destination() {
  req.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });

  dt_from.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });

  dt_to.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });
  if (save_method == 'add') {
    url = baseUrl("eforms/travel_order/add_destination/") + param_id;
  } else {
    url = baseUrl("eforms/travel_order/update_destination/") + param_id;
  }

  $("#form_destination").submit();
}

$.validate({
  form: '#form_destination',
  lang: 'en',
  onSuccess: function (form) {
    var isValidDate = false;
    var message = "";
    
    var date_from = $("input[name='date_from']").val();
    var date_to = $("input[name='date_to']").val();

    date_from = new Date(date_from);
    date_to = new Date(date_to);

    date_from = moment(date_from);
    date_to = moment(date_to);

    var duration = moment.duration(date_to.diff(date_from));
		var minutes = duration.asMinutes();

    if (minutes < -1) {
      isValidDate = false;
      message = 'Invalid Date! `Date To` cannot be earlier than `Date From`.';
    } else if (minutes == 0) {
      isValidDate = false;
      message = 'Invalid Date! `Date From` and `Date To` cannot be the same.';
    } else if (minutes <= 30) {
      isValidDate = false;
      message = 'Invalid Date! `Date From` and `Date To` cannot be less than 30 minutes.';
    } else {
      isValidDate = true;
    }

    if (isValidDate) {
      $.ajax({
        url: url,
        type: "POST",
        data: $('#form_destination').serialize(),
        dataType: "JSON",
        success: function (data) {
          if (data.status) {
            if (save_method == 'add') {
              toastr.success(data.toastr_msg, "Destination added successfully!", 5000);
            } else {
              toastr.success(data.toastr_msg, "Destination updated successfully!", 5000);
            }
            $("#modal_form_destination #select2_req").text("").trigger("change");
            $("#modal_form_destination #select2_req").val("").trigger("change");
            tblDestination.ajax.reload();
            $("#modal_form_destination").modal("hide");
          } else {
            alert('Error get data from ajax');
          }
        }
      });
    } else {
      toastr.warning(message, "Error!", 5000);
    }
    return false;
  },
});

// $(document).ready(function(){
//   $.ajax({
//     url: baseUrl("eforms/travel_order/verify_TO_status"),
//     type: "POST",
//     data: {
//       csrf_token: _csrf_hash,
//       travel_order_id: param_id
//     },
//     dataType: "json",
//     success: function(resp){
//       if(resp.status == "Pending"){
//         $("#checkAllBox").attr("checked", "checked");
//         $(this).attr("checked", "checked");
//         $("#checkAllBox").on("click", function(e){
//           var checkbox = $(this);
//           if (!checkbox.is(":checked")) {
//             // do the confirmation thing here
//             e.preventDefault();
//             return false;
//           }
//         });

//         $("#table-destination").find("input[type=checkbox]").on("click", function(e){
//           var checkbox = $(this);
//             if (!checkbox.is(":checked")) {
//               // do the confirmation thing here
//               e.preventDefault();
//               return false;
//             }
//         });
//       }else{
//         $("#checkAllBox").on("change", function(){
//           $(this).removeAttr("checked");
//           let status;
//           if($(this).is(":checked")){
//             status = 1;
//             $("#btnaccomplish").removeAttr('disabled');
//             $("#btnaccomplish").removeClass('btn-metal');
//             $("#btnaccomplish").addClass('btn-success');
//           }else{
//             status = 0;
//             $("#btnaccomplish").attr('disabled',"disabled");
//             $("#btnaccomplish").removeClass('btn-success');
//             $("#btnaccomplish").addClass('btn-metal');
//           }
//           $.ajax({
//             url: baseUrl("eforms/travel_order/set_all_status"),
//             type: "POST",
//             data: {
//               csrf_token: _csrf_hash,
//               travel_order_id: param_id,
//               status: status
//             },
//             success: function(resp){
//               console.log(resp);
//               if(resp != 0){
//                 tblDestination.ajax.reload();
//               }
//             }
//           });
//         });
//       }
//     }
//   });

//   $.ajax({
//     url: baseUrl("eforms/travel_order/accomplishment_button"),
//     type: "POST",
//     data: {
//       csrf_token: _csrf_hash,
//       travel_order_id: param_id
//     },
//     dataType: "json",
//     success: function(resp){
//       if(resp == 0){
//         $("#btnaccomplish").removeClass('btn-success');
//         $("#btnaccomplish").addClass('btn-metal');
//         $("#btnaccomplish").attr('disabled',"disabled");
//       }else if(resp == 2){
//         $("#btnaccomplish").removeClass('btn-metal');
//         $("#btnaccomplish").addClass('btn-success');
//         $("#btnaccomplish").removeAttr('disabled');
//       }else{
//         $("#btnaccomplish").removeClass('btn-metal');
//         $("#btnaccomplish").addClass('btn-success');
//         $("#btnaccomplish").removeAttr('disabled');
//       }
//     }
//   });
// });

function add_personnel() {
  save_method = 'add';
  $('#form_personnel')[0].reset(); // reset form on modals
  $('#modal_form_personnel').modal('show'); // show bootstrap modal
  $('.modal-title').text('Add New Personnel'); // Set Title to Bootstrap modal title
}

function edit_personnel(id) {
  save_method = 'update';
  $('#form_personnel')[0].reset();
  $.ajax({
    url: baseUrl("eforms/travel_order/edit_personnel/") + id,
    type: "GET",
    dataType: "JSON",
    success: function (data) {
      $('[name="id_personnel"]').val(data.id);
      var newOption = new Option(data.name, data.employee_id, true, true);
      $('#select2_emp').append(newOption).trigger('change');
      $('#modal_form_personnel').modal('show'); // show bootstrap modal
      $('.modal-title').text('Edit Content'); // Set Title to Bootstrap modal title
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error get data from ajax');
    }
  });
}

function delete_personnel() {
  $temp = $('[name="delete_id"]').val();
  $.ajax({
    url: baseUrl("eforms/travel_order/delete_personnel/") + $temp,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash },
    success: function (data) {
      isValidationShow = true;
      toastr.success(data.toastr_msg, "Removed personnel successfully!", 5000)
      tblPersonnel.ajax.reload();
      $("#modal_form_delete").modal("hide");
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error adding / update data');
    }
  });
}

$("#clear_modal").hide();
function clear_personnel() {
  if(personnelSize == 0) return toastr.warning("No personnel to be delete.");

  $('#clear_modal').modal('show');
  $('.modal-title').text('Clear');
  $.validate({
    form: '#clear_form',
    lang: 'en',
    onSuccess: function (form) {
      $.ajax({
        url: baseUrl("eforms/travel_order/delete_all_personnel/") + param_id,
        type: "POST",
        dataType: "json",
        data: $("#clear_form").find("input,select,textarea").serialize(),
        beforeSend: function () {
          $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function (data) {
          isValidationShow = true;
          tblPersonnel.ajax.reload();
          $("#clear_modal").modal("hide");
          toastr.success(data.toastr_msg, "Removed contents successfully!", 5000);
          $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }
      });
      return false;
    },
  });
}

function save_personnel() {
  var url;

  if (save_method == 'add') {
    url = baseUrl("eforms/travel_order/add_personnel/") + param_id;
  } else {
    url = baseUrl("eforms/travel_order/update_personnel/") + param_id;
  }

  $.validate({
    form: '#form_personnel',
    lang: 'en',
    onSuccess: function (form) {
      if ($('#modal_form_personnel #select2_emp').text() != "") {
        $.ajax({
          url: url,
          type: "POST",
          data: $('#form_personnel').serialize(),
          dataType: "JSON",
          success: function (data) {
            tblPersonnel.ajax.reload();
            $('#modal_form_personnel #select2_emp').text("").trigger('change');
            $('#modal_form_personnel #select2_emp').val("").trigger('change');
            if (save_method == 'add') {
              if (data.message) {
                toastr.warning(data.message, "Warning: ", 5000);
              } else {
                toastr.success(data.message, "Personnel added successfully!", 5000);
              }
            }
            else {
              if (data.message) {
                toastr.warning(data.message, "Warning: ", 5000);
              } else {
                toastr.success(data.message, "Personnel updated successfully!", 5000);
                $("#modal_form_personnel").modal("hide");
              }
            }
          }

        });
      } else {
        toastr.error("", "Personnel field is required!", 5000);
      }
      return false;
    },
  });
}

function change_service() {
  radiobtn = document.getElementById("commute");
  radiobtn.checked = false;
  radiobtn = document.getElementById("hitch");
  radiobtn.checked = false;
  radiobtn = document.getElementById("personal");
  radiobtn.checked = false;
  radiobtn = document.getElementById("other");
  radiobtn.checked = false;
  
  document.getElementById('other_remark').style.display = 'none';

  if(_currentActions.includes("assign")){
    document.getElementById('service_veh').style.removeProperty('display');
    document.getElementById('service_driver').style.removeProperty('display');
  }
}
function change_hitch() {
  radiobtn = document.getElementById("service");
  radiobtn.checked = false;
  radiobtn = document.getElementById("hitch");
  radiobtn.checked = true;
  radiobtn = document.getElementById("commute");
  radiobtn.checked = false;
  radiobtn = document.getElementById("personal");
  radiobtn.checked = false;
  radiobtn = document.getElementById("other");
  radiobtn.checked = false;
  
  document.getElementById('other_remark').style.display = 'none';

  if(_currentActions.includes("assign")){
    document.getElementById('service_veh').style.removeProperty('display');
    document.getElementById('service_driver').style.removeProperty('display');
  }
}
function change_commute() {
  radiobtn = document.getElementById("service");
  radiobtn.checked = false;
  radiobtn = document.getElementById("hitch");
  radiobtn.checked = false;
  radiobtn = document.getElementById("personal");
  radiobtn.checked = false;
  radiobtn = document.getElementById("other");
  radiobtn.checked = false;

  document.getElementById('other_remark').style.display = 'none';
  document.getElementById('service_veh').style.display = 'none';
  document.getElementById('service_driver').style.display = 'none';
}
function change_personal() {
  radiobtn = document.getElementById("service");
  radiobtn.checked = false;
  radiobtn = document.getElementById("hitch");
  radiobtn.checked = false;
  radiobtn = document.getElementById("commute");
  radiobtn.checked = false;
  radiobtn = document.getElementById("other");
  radiobtn.checked = false;

  document.getElementById('other_remark').style.display = 'none';
  document.getElementById('service_veh').style.display = 'none';
  document.getElementById('service_driver').style.display = 'none';
}
function change_other() {
  radiobtn = document.getElementById("service");
  radiobtn.checked = false;
  radiobtn = document.getElementById("hitch");
  radiobtn.checked = false;
  radiobtn = document.getElementById("commute");
  radiobtn.checked = false;
  radiobtn = document.getElementById("personal");
  radiobtn.checked = false;

  document.getElementById('other_remark').style.removeProperty('display');
  document.getElementById('service_veh').style.display = 'none';
  document.getElementById('service_driver').style.display = 'none';
}

function displayRequiredType(type){
  if(type == undefined){
    $('#radio_type').css('border', 'solid 1px #b94a48');
    $('#radio_type').css('border-radius', '3px');
    $('#radio_type').css('padding', '6px 0px 3px 0px');
    $('#require_msg_type').removeClass('m--hide');
  } else {
    $('#radio_type').css('border', '');
    $('#radio_type').css('border-radius', '');
    $('#radio_type').css('padding', '');
    $('#require_msg_type').addClass('m--hide');
  }
}

$('[name="type"]').change(function(){
  var type = $('input[name="type"]:checked').val();
  displayRequiredType(type);
});

var personnelSize = 0;
function displayRequiredPersonel(){
  if(personnelSize == 0 && isValidationShow){
    $('#table-personnel').css('border', 'solid 1px #b94a48');
    $('#table-personnel').css('border-radius', '3px');
    $('#table-personnel').css('padding', '6px 0px 3px 0px');
    $('#table_v').removeClass('m--hide');
  } else {
    $('#table-personnel').css('border', '');
    $('#table-personnel').css('border-radius', '');
    $('#table-personnel').css('padding', '');
    $('#table_v').addClass('m--hide');
  }
}

var destinationSize = 0;
function displayRequiredDestinations(){
  if(destinationSize == 0 && isValidationShow){
    $('#table-destination').css('border', 'solid 1px #b94a48');
    $('#table-destination').css('border-radius', '3px');
    $('#table-destination').css('padding', '6px 0px 3px 0px');
    $('#table2_v').removeClass('m--hide');
  } else {
    $('#table-destination').css('border', '');
    $('#table-destination').css('border-radius', '');
    $('#table-destination').css('padding', '');
    $('#table2_v').addClass('m--hide');
  }  
}

if(_currentActions.includes("assign")){
  $('#service_veh').removeClass('m--hide');
  $('#service_driver').removeClass('m--hide');
} else {
  $('#service_veh').addClass('m--hide');
  $('#service_driver').addClass('m--hide');
}

var isValidationShow = false;
function update_travel_order(recommend=false) {
  isValidationShow = true;
  var type = $('input[name="type"]:checked').val();
  displayRequiredType(type);
  displayRequiredPersonel();
  displayRequiredDestinations();

  file_under.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });  
 
  department.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });  
 
  vehicle.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });  
 
  driver.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });

  $.validate({
    form: '#form_travel_order',
    lang: 'en',
    onSuccess: function (form) {
      if (personnelSize > 0 && destinationSize > 0 && type != undefined) {
        $('#table_v').empty();
        $('#table2_v').empty();
        $.ajax({
          url: baseUrl("eforms/travel_order/update_travel_order_v2/") + param_id,
          type: "POST",
          data: $('#form_travel_order').serialize(),
          dataType: "JSON",
          success: function (data) {
            if (data.status) {
              toastr.success(data.messages, "Updated successfully!", 5000);
              if(recommend){
                setTimeout(function(){
                  recommendationModal.modal("show");
                  validateRecommendation(recommendationModal);
                }, 600);
              }else{ window.location.replace(defaultRedirect); }
            } else {
              toastr.error(data.messages, "Error get data from ajax!", 5000);
            }
            
          }
        });
      }
      return false;
    },
  });
}

// edit map js

const pointer = {};
const pointer_name = {};
const markersName = {};
const inputGlob = [];
let map;
function initMapTemp(id = "") {
  let input;
  let coordsInput; 
  let title;
  let uluru;
  uluru = {lat: 10.704365710388265, lng: 122.96319995137281};
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      uluru = {lat: position.coords.latitude, lng: position.coords.longitude};
    });
  } else { 
    console.log("Geolocation is not supported by this browser.");
  }
  map = new google.maps.Map(document.getElementById("mapEditTO2"), {
    mapId: "c38a738bd984de98",
    zoom: 19  ,
    center: uluru,
    mapTypeId: 'satellite'
  });

  const marker = new google.maps.Marker({
    map: map,
	draggable: false,
  });

  input = "travelFrom";
  coordsInput = "formTravelFrom";
  title = "From";

  $("#travelFrom").on('focus', function () {
      inputGlob.remove();
      input = "travelFrom";
      title = "From";
      coordsInput = "formNewTravelFrom";
      searchMap(input, coordsInput, title, map);
  });

  $("#travelTo").on('focus', function () {
      const marker = new google.maps.Marker();
      inputGlob.remove();
      input = "travelTo";
      title = "To";
      coordsInput = "formNewTravelTo";
      searchMap(input, coordsInput, title, map);
  });

if(id != ""){
  editmarker(map, id);
}

  map.addListener("click", (mapsMouseEvent) => {
    if(inputGlob.length !== 0){
        input = inputGlob[0];
        if(input === "travelFrom"){
            title = "From";
        }else{
            title = "To";
        }
    }
    const coords = mapsMouseEvent.latLng;
    const placeId = mapsMouseEvent.placeId;
    const addresscoords = mapsMouseEvent.latLng.toJSON();
    if(Object.keys(pointer).length <= 2){
        if(input === "travelFrom" && typeof pointer.from != "undefined"){
            pointer_name.from.setMap(null);
            pointer.from.setMap(null);
        }else if(input === "travelTo" && typeof pointer.to != "undefined"){
            pointer_name.to.setMap(null);
            pointer.to.setMap(null);
        }
    }else{
        if(input === "travelFrom"){
            pointer_name.from.setMap(null);
            pointer.from.setMap(null);
        }else{
            pointer_name.to.setMap(null);
            pointer.to.setMap(null);
        }
    }
    $("#"+coordsInput).val(JSON.stringify(addresscoords));

    GetAddress(coords, map, placeId, input, title);
  });

  $(".closeNewTravel").on('click', function(){
    pointer_name.from.setMap(null);
    pointer_name.to.setMap(null);
    pointer.from.setMap(null);
    pointer.to.setMap(null);
  });
  
}

function editmarker(map, id){
    if(id != ""){
        $.ajax({
            url: baseUrl("eforms/travel_order/edit_destination_marker/"),
            type: "POST",
            data: {
                csrf_token: _csrf_hash,
                travelOrder_id: id,
            },
            dataType: "JSON",
            success: function(resp){
              const bounds = new google.maps.LatLngBounds();
              if(resp.coords_from != "" && resp.coords_to != ""){
                const from = resp.travel_from;
                const to = resp.travel_to;
                
                const fromMarker = new google.maps.Marker({
                  position: JSON.parse(resp.coords_from),
                  map: map,
                });

                var informationFrom = new google.maps.InfoWindow({
                    content: '<h6>From</h6><br><p>'+String(from).toUpperCase()+'</p>'
                });
                informationFrom.open(map, fromMarker);
                Object.assign(pointer_name,{"from": informationFrom});
                Object.assign(pointer,{"from": fromMarker});

                const toMarker = new google.maps.Marker({
                  position: JSON.parse(resp.coords_to),
                  map: map,
                });

                var informationTo = new google.maps.InfoWindow({
                  content: '<h6>To</h6><br><p>'+String(to).toUpperCase()+'</p>'
                });
                informationTo.open(map, toMarker);
                Object.assign(pointer_name,{"to": informationTo});
                Object.assign(pointer,{"to": toMarker});
                const from_coords = JSON.parse(resp.coords_from);
                const to_coords = JSON.parse(resp.coords_to);
                bounds.extend(from_coords);
                bounds.extend(to_coords);

                map.fitBounds(bounds);
                map.setZoom(11);
              }
            }
        });
    }
} 

function searchMap(searchInputID, formTravel, title, map){
  const searchInput = document.getElementById(searchInputID);
  const searchBox = new google.maps.places.SearchBox(searchInput);
  map.addListener("bounds_changed", () => {
		searchBox.setBounds(map.getBounds());
	});

	searchBox.addListener("places_changed", () => {
		const places = searchBox.getPlaces();
		if (places.length == 0) {
		return;
		}
		places.forEach((place) => {
			if (!place.geometry || !place.geometry.location) {
				console.log("Returned place contains no geometry");
				return;
			}

      $("#"+formTravel).val(JSON.stringify(place.geometry.location));

      if(Object.keys(pointer).length < 2){
          if(Object.keys(pointer).length === 1){
              if(searchInputID === "travelFrom" && typeof pointer.from != "undefined"){
                  pointer_name.from.setMap(null);
                  pointer.from.setMap(null);
              }else if(searchInputID === "travelTo" && typeof pointer.to != "undefined"){
                  pointer_name.to.setMap(null);
                  pointer.to.setMap(null);
              }
          }
      }else{
          if(searchInputID === "travelFrom"){
              pointer_name.from.setMap(null);
              pointer.from.setMap(null);
          }else{
              pointer_name.to.setMap(null);
              pointer.to.setMap(null);
          }
      }
        siteMarkers(searchInputID, title, place.name, place.geometry.location, map);
		});
	});
}

function siteMarkers(input, title, placeName, coords, map, funcname = ""){
    const bounds = new google.maps.LatLngBounds();
    const newMarker = new google.maps.Marker();
    newMarker.setPosition(coords);
    newMarker.setMap(map);
     
    if(input === "travelFrom"){
        pointer_name.from.setMap(null);
        var informationFrom = new google.maps.InfoWindow({
            content: '<h6>'+title+'</h6><br><p>'+placeName.toUpperCase()+'</p>'
        });
        bounds.extend(newMarker.position);
        informationFrom.open(map, newMarker);
        Object.assign(pointer_name,{"from": informationFrom});
        Object.assign(pointer,{"from": newMarker});
        Object.assign(markersName,{"fromTitle": placeName.toUpperCase()});
    }else{
        pointer_name.to.setMap(null);
        var informationTo = new google.maps.InfoWindow({
            content: '<h6>'+title+'</h6><br><p>'+placeName.toUpperCase()+'</p>'
        });
        bounds.extend(newMarker.position);
        informationTo.open(map, newMarker);
        Object.assign(pointer_name,{"to": informationTo});
        Object.assign(pointer,{"to": newMarker});
        Object.assign(markersName,{"toTitle": placeName.toUpperCase()});
    }

    const from_marker = pointer.from;
    const from_title = markersName.fromTitle;
    const to_marker = pointer.to;
    const to_title = markersName.toTitle;
    
    if(funcname != 0){
        if(Object.keys(pointer).length != 0){
            if(input === "travelFrom"){
                from_marker.addListener("click", function(mapsMouseEvent){
                    information.open(map, from_marker);
                    inputGlob.remove();
                    inputGlob.push("travelFrom");
                });
            }else{
                to_marker.addListener("click", function(mapsMouseEvent){
                    information.open(map, to_marker);
                    inputGlob.remove();
                    inputGlob.push("travelTo");
                });
            }
        }
    }else{
        if(Object.keys(pointer).length != 0){
            if(input === "travelFrom"){
                newMarker.addListener("click", function(mapsMouseEvent){
                    information.open(map, newMarker);
                    inputGlob.remove();
                    inputGlob.push("travelFrom");
                });
            }else{
                newMarker.addListener("click", function(mapsMouseEvent){
                    information.open(map, newMarker);
                    inputGlob.remove();
                    inputGlob.push("travelTo");
                });
            }
        }
    }
    if(Object.keys(pointer).length == 2){
        if(from_marker != null){
            pointer_name.to.setMap(null);
            bounds.extend(from_marker.getPosition());
            map.fitBounds(bounds);
            var informationFrom = new google.maps.InfoWindow({
              content: '<h6>From</h6><br><p>'+String(from_title).toUpperCase()+'</p>'
            });
            informationFrom.open(map, from_marker);
            Object.assign(pointer_name,{"from": informationFrom});
        }
        if(to_marker != null){
            pointer_name.to.setMap(null);
            bounds.extend(to_marker.getPosition());
            map.fitBounds(bounds);
            var informationTo = new google.maps.InfoWindow({
              content: '<h6>aTo</h6><br><p>'+String(to_title).toUpperCase()+'</p>'
            });
            informationTo.open(map, to_marker);
            Object.assign(pointer_name,{"to": informationTo});
        }
    }  
}

function GetAddress(latlon, map, placeId, inputId, title) {
  const travelTo = [];
  var geocoder = new google.maps.Geocoder();
  
  if(placeId != null){
	const request = {
		placeId: placeId,
		fields: ["name", "formatted_address", "place_id", "geometry"],
	};
	const infowindow = new google.maps.InfoWindow();
	const service = new google.maps.places.PlacesService(map);

	service.getDetails(request, (place, status) => {
		if (
		status === google.maps.places.PlacesServiceStatus.OK &&
		place &&
		place.geometry &&
		place.geometry.location
		) {
      $("#"+inputId).val(place.name);
      siteMarkers(inputId, title, place.name, latlon, map, 1);
		}
	});
  }else{
	geocoder.geocode({
		location: latlon
	}, function (results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			if (results[0]) {
				
			}
		}
    const request = {
        placeId: results[1].place_id,
        fields: ["name", "formatted_address", "place_id", "geometry"],
    };
    const infowindow = new google.maps.InfoWindow();
    const service = new google.maps.places.PlacesService(map);
    service.getDetails(request, (place, status) => {
        if (
        status === google.maps.places.PlacesServiceStatus.OK &&
        place &&
        place.geometry &&
        place.geometry.location
        ) {
            $("#"+inputId).val(place.name);
            siteMarkers(inputId, title, place.name, latlon, map, 1);
        }
    });
	});
  }
}

$("#travelOrderFromIcon .icon").on("click", function(){
  if($("#travelOrderOptionFrom").is(":visible")){
    $("#travelOrderOptionFrom").hide();
  }else{
    $("#travelOrderOptionFrom").show();
  }

  $.ajax({
    url: baseUrl("eforms/travel_order/sites_options/"),
    type: "GET",
    dataType: "JSON",
    success: function(resp){
      if(resp.length > 0){
        vmTab3.checker = true;
      }else{
        vmTab3.checker = false;
      }
      vmTab3.vm_tab3 = Object.assign({}, resp);
    }
  });
});

var vmTab3 = new Vue({
  el: "#travel_option_from",
  data: { 
    vm_tab3: [], checker: false,
  },
  methods: {
    selectedSite: function (id, input_id) {
      $.ajax({
        url: baseUrl("eforms/travel_order/site_selected/"),
        type: "POST",
        data: {
          csrf_token: _csrf_hash,
          sites_id: id,
        },
        dataType: "JSON",
        success: function(resp){
          const coords = {"lat": Number(resp.latitude), "lng": Number(resp.longtitude)};
          if(resp.site_name != null){
            $("#travelFrom").val(resp.site_name);
            Object.assign(markersName,{"fromTitle": resp.site_name});
            $("#formNewTravelFrom").val(JSON.stringify(coords));
            if($("#travelOrderOptionFrom").is(":visible")){
              $("#travelOrderOptionFrom").hide();
            }else{
              $("#travelOrderOptionFrom").show();
            }
            const bounds = new google.maps.LatLngBounds();
            let fromMarker;
            if(pointer.from != null){
              pointer_name.from.setMap(null);
              pointer.from.setMap(null);
              fromMarker = new google.maps.Marker();
              fromMarker.setPosition(coords);
              fromMarker.setMap(map);
              Object.assign(pointer,{"from": fromMarker});
              var informationFrom = new google.maps.InfoWindow({
                content: '<h6>From</h6><br><p>'+String(resp.site_name).toUpperCase()+'</p>'
              });
              informationFrom.open(map, fromMarker);
              Object.assign(pointer_name,{"from": informationFrom});
            }else{
              //pointer_name.from.setMap(null);
              fromMarker = new google.maps.Marker();
              fromMarker.setPosition(coords);
              fromMarker.setMap(map);
              Object.assign(pointer,{"from": fromMarker});
              map.setCenter(coords);
              var informationFrom = new google.maps.InfoWindow({
                content: '<h6>From</h6><br><p>'+String(resp.site_name).toUpperCase()+'</p>'
              });
              informationFrom.open(map, fromMarker);
              Object.assign(pointer_name,{"from": informationFrom});
            }
            const from_marker = pointer.from;
            const from_title = markersName.fromTitle;
            const to_marker = pointer.to;
            const to_title = markersName.toTitle;
            if(Object.keys(pointer).length == 2){
              if(from_marker != null && from_title != null){
                  if(pointer_name.from != null){
                    pointer_name.from.setMap(null);
                  }
                  bounds.extend(from_marker.getPosition());
                  map.fitBounds(bounds);
                  var informationFrom = new google.maps.InfoWindow({
                      content: '<h6>From</h6><br><p>'+String(from_title).toUpperCase()+'</p>'
                  });
                  informationFrom.open(map, from_marker);
                  Object.assign(pointer_name,{"from": informationFrom});
              }
              if(to_marker != null && to_title != null){
                  if(pointer_name != null){
                    pointer_name.to.setMap(null);
                  }
                  bounds.extend(to_marker.getPosition());
                  map.fitBounds(bounds);
                  var informationTo = new google.maps.InfoWindow({
                      content: '<h6>To</h6><br><p>'+String(to_title).toUpperCase()+'</p>'
                  });
                  informationTo.open(map, to_marker);
                  Object.assign(pointer_name,{"to": informationTo});
              }
            }
          } 
        }
      });
    }
  }
});

$("#travelOrderToIcon .icon").on("click", function(){
  if($("#travelOrderOptionTo").is(":visible")){
    $("#travelOrderOptionTo").hide();
  }else{
    $("#travelOrderOptionTo").show();
  }

  $.ajax({
    url: baseUrl("eforms/travel_order/sites_options/"),
    type: "GET",
    dataType: "JSON",
    success: function(resp){
      if(resp.length > 0){
        vmTab2.checker = true;
      }else{
        vmTab2.checker = false;
      }
      vmTab2.vm_tab2 = Object.assign({}, resp);
    }
  });
});

var vmTab2 = new Vue({
  el: "#travel_option_to",
  data: { 
    vm_tab2: [], checker: false,
  },
  methods: {
    selectedSite: function (id) {
      $.ajax({
        url: baseUrl("eforms/travel_order/site_selected/"),
        type: "POST",
        data: {
          csrf_token: _csrf_hash,
          sites_id: id,
        },
        dataType: "JSON",
        success: function(resp){
          const coords = {"lat": Number(resp.latitude), "lng": Number(resp.longtitude)};
          if(resp.site_name != null){
            $("#travelTo").val(resp.site_name);
            Object.assign(markersName,{"toTitle": resp.site_name});
            $("#formNewTravelTo").val(JSON.stringify(coords));
            if($("#travelOrderOptionTo").is(":visible")){
              $("#travelOrderOptionTo").hide();
            }else{
              $("#travelOrderOptionTo").show();
            }
            const bounds = new google.maps.LatLngBounds();
            let toMarker;
            if(pointer.to != null){
              pointer_name.to.setMap(null);
              pointer.to.setMap(null);
              toMarker = new google.maps.Marker();
              toMarker.setPosition(coords);
              toMarker.setMap(map);
              Object.assign(pointer,{"to": toMarker});
              var informationTo = new google.maps.InfoWindow({
                content: '<h6>To</h6><br><p>'+String(resp.site_name)+'</p>'
              });
              informationTo.open(map, toMarker);
              Object.assign(pointer_name,{"to": informationTo});
            }else{
              //pointer_name.to.setMap(null);
              toMarker = new google.maps.Marker();
              toMarker.setPosition(coords);
              toMarker.setMap(map);
              Object.assign(pointer,{"to": toMarker});
              map.setCenter(coords);
              var informationTo = new google.maps.InfoWindow({
                content: '<h6>To</h6><br><p>'+String(resp.site_name)+'</p>'
              });
              informationTo.open(map, toMarker);
              Object.assign(pointer_name,{"to": informationTo});
            }
            const from_marker = pointer.from;
            const from_title = markersName.fromTitle;
            const to_marker = pointer.to;
            const to_title = markersName.toTitle;
          if(Object.keys(pointer).length == 2){
              if(from_marker != null && from_title != null){
                if(pointer_name.from != null){
                  pointer_name.from.setMap(null);
                }
                bounds.extend(from_marker.getPosition());
                map.fitBounds(bounds);
                var informationFrom = new google.maps.InfoWindow({
                    content: '<h6>From</h6><br><p>'+String(from_title)+'</p>'
                });
                informationFrom.open(map, from_marker);
                Object.assign(pointer_name,{"from": informationFrom});
              }
              if(to_marker != null && to_title != null){
                if(pointer_name.to != null){
                  pointer_name.to.setMap(null);
                }
                bounds.extend(to_marker.getPosition());
                map.fitBounds(bounds);
                var informationTo = new google.maps.InfoWindow({
                    content: '<h6>To</h6><br><p>'+String(to_title)+'</p>'
                });
                informationTo.open(map, to_marker);
                Object.assign(pointer_name,{"to": informationTo});
              }
            }
          } 
          }
      });
    }
  }
});

function getCurrentDate(){
  var tempDays = 30;
  if($.inArray("administrator_privilege", _currentActions) !== -1){
    tempDays = 30;
  }
  return moment().subtract(tempDays,"days").format("YYYY/MM/DD 00:00");
}

function validateRecommendation(recommendationModal){
  $.validate({
    form: '#formRecommend',
    lang: 'en',
    onSuccess: function (form) {
      const formData = $(form).serialize();
      $.ajax({
        url: siteUrl("eforms/travel_order/approve_recommend_travel_v2"),
        type: "post",
        data: formData,
        dataType: "json",
        beforeSend: function(){
          $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function(json){
          if(json.status == 'success'){
            toastr.success("Travel order has been recommended successfully!");
            $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            recommendationModal.modal("hide");
            setTimeout(function(){ window.location.replace(defaultRedirect); }, 1000);
          } else {
            toastr.error(json.msg, "Error");
          }
        }
      })
      return false;
    }
  });
}