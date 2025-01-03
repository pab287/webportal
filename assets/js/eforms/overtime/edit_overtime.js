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

var vmTab1 = new Vue({
  el: "#form_overtime",
  data: { 
    vm_tab1: {
      company: '',
      department: '',
      position: '',
    } 
  }
});

$.ajax({
  url: baseUrl("eforms/overtime/get_overtime_request_details/") + param_id,
  type: "GET",
  dataType: "JSON",
  success: function (data) {
    $("#purpose").val(data.purpose);
    vmTab1.vm_tab1 = Object.assign({}, data);

    var employee = new Option(data.display_name, data.employee, true, true);
    $('#employee').append(employee).trigger('change');

    var requested_by = new Option(data.display_requested_by, data.requested_by, true, true);
    $('#requested_by').append(requested_by).trigger('change');
  }
});

$("#employee").select2({
  placeholder: 'Select',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/overtime/get_employee"),
    dataType: "json",
    delay: 250,
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

$("#requested_by").select2({
  placeholder: 'Select',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/overtime/get_employee"),
    dataType: "json",
    delay: 250,
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

$("#employee").on("select2:select", function () {
  $.ajax({
    type: "GET",
    data: { data: $("#employee option:selected").attr("value") },
    url: baseUrl("eforms/overtime/get_employee_detail"),
    dataType: "json",
    success: function (data) {
      if (typeof data.details !== "undefined") {
        $("#details").val(data.details);
        $("#company").val(data.company);
        $("#department").val(data.department);
        $("#position").val(data.position);
      }
    }
  });
});

$("#date_time").daterangepicker({
  timePicker: true,
  minDate: moment().subtract(2, 'years'),
  startDate: moment().startOf('hour'),
  endDate: moment().startOf('hour').add(32, 'hour'),
  locale: {
    format: 'M/DD hh:mm A'
  }
});

$('#date_time').on('apply.daterangepicker', function (ev, picker) {
  $("#date_from").val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
  $("#date_to").val(picker.endDate.format('YYYY-MM-DD HH:mm:ss'));
  $("#date").val(picker.startDate.format('MM/DD/YYYY hh:mm a') + ' - ' + picker.endDate.format('MM/DD/YYYY hh:mm a'));
});

function save() {
  $.validate({
    form: '#form_overtime',
    lang: 'en',
    onSuccess: function (form) {
      $.ajax({
        url: baseUrl("eforms/overtime/update_overtime/") + param_id,
        type: "POST",
        dataType: "json",
        data: $("#form_overtime").find("input,select,textarea").serialize(),
        beforeSend: function () {
          $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function (data) {
          if (data.state) {
            toastr.success(data.message, "Successfully saved!", 5000);
            setTimeout(function () {
              location.href = 'view_overtime?id=' + param_id;
            }, 1000);
          } else {
            toastr.error(data.message, "Error!", 5000);
          }
          $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }
      });
      return false;
    },
  });
}

function back() {
  location.href = 'view_overtime?id=' + param_id;
}

jQuery(document).ready(function () {
  Init();
});

var CRLF = 10;
var BULLET = String.fromCharCode(45);

function Init() {
  if (purpose.addEventListener) purpose.addEventListener("input", OnInput, false);
}

function OnInput(event) {
  char = event.target.value.substr(-1).charCodeAt(0);
  nowLen = purpose.value.length;
  if (nowLen > prevLen.value) {
    if (char == CRLF) purpose.value = purpose.value + BULLET + " ";
    if (nowLen == 1) purpose.value = BULLET + " " + purpose.value;
  }
  prevLen.value = nowLen;
}