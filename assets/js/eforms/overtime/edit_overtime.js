const getUrlParameter = function getUrlParameter(sParam) {
  const sPageURL = decodeURIComponent(window.location.search.substring(1));
  const sURLVariables = sPageURL.split("&");
  let sParameterName, i; 

  for (i = 0; i < sURLVariables.length; i++) {
    sParameterName = sURLVariables[i].split("=");
    if (sParameterName[0] === sParam) {
      return sParameterName[1] === undefined ? true : sParameterName[1];
    }
  }
};

const param_id = getUrlParameter("id");
const vmTab1 = new Vue({
  el: "#form_overtime",
  data: { vm_tab1: {}, loading_content: true },
  methods: {
    displayEmployeeCompany: function () {
      const { details } = this.vm_tab1;
      return  details;
    }
  }
});

$.ajax({
  url: baseUrl("eforms/overtime/get_overtime_request_details/") + param_id,
  type: "GET",
  dataType: "JSON",
  global: false,
  success: function (data) {
    vmTab1.vm_tab1 = { ...data };
    vmTab1.loading_content = false;
    $("#purpose").val(data.purpose);
    const employee = new Option(data.display_name, data.employee, true, true);
    $('#employee').append(employee).trigger('change');
    const requested_by = new Option(data.display_requested_by, data.requested_by, true, true);
    $('#requested_by').append(requested_by).trigger('change');
    const tempMinDate = moment(new Date(data.max_date), "YYYY-MM-DD").add(1, 'days').format("YYYY-MM-DD");
    dateTimeRangePicker(tempMinDate, data.date_from, data.date_to);
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

        if(data.max_date){
          const tempMinDate = moment(new Date(data.max_date), "YYYY-MM-DD").add(1, 'days').format("YYYY-MM-DD");
          dateTimeRangePicker(tempMinDate);
        }
      }
    }
  });
});

const dateTimeRangePicker = function (minDate, startDate, endDate) {
  $("#date_from, #date_to, #date").val("");
  const nMinDate = minDate ? new Date(minDate) : moment().subtract(2, 'years');
  const nStartDate = startDate ? new Date(startDate) : moment().startOf('hour');
  const nEndDate = endDate ? new Date(endDate) : moment().startOf('hour').add(24, 'hour'); //changed default tagged / selected time from 32hrs to 24hrs

  const nMaxDate = new Date(moment().add(1, 'days').format("YYYY-MM-DD 23:59"));
  $("#date_time").daterangepicker({
      timePicker: true,
      timePicker24Hour: false,
      minDate: nMinDate,
      startDate: nStartDate,
      endDate: nEndDate,
      maxDate: nMaxDate,
      locale: {
        format: 'M/DD hh:mm A'
      }
  }).on('apply.daterangepicker', function (ev, picker) {
      vmTab1.vm_tab1.date_from = picker.startDate.format('YYYY-MM-DD HH:mm:ss');
      vmTab1.vm_tab1.date_to = picker.endDate.format('YYYY-MM-DD HH:mm:ss');

      /*** $("#date_from").val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
      $("#date_to").val(picker.endDate.format('YYYY-MM-DD HH:mm:ss'));
      $("#date").val(picker.startDate.format('MM/DD/YYYY hh:mm a') + ' - ' + picker.endDate.format('MM/DD/YYYY hh:mm a')).validate(); ***/
  });
}

dateTimeRangePicker();

function save() {
  $.validate({
    form: '#form_overtime',
    lang: 'en',
    onSuccess: function (form) {

      let from = moment($("#date_from").val()).format('YYYY-MM-DD');
      let to = moment($("#date_to").val()).format('YYYY-MM-DD');
      let allowedOTDate = moment(from).add(1, 'days').format('YYYY-MM-DD');


      if (to <= allowedOTDate){
        const result = isValidTimeRange(moment($("#date_from").val()).format('YYYY-MM-DD HH:mm'), moment($("#date_to").val()).format('YYYY-MM-DD HH:mm'));

        if (result.valid) {
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

                  if (typeof data.ot_data !== 'undefined' && data.ot_data) {
                      let html = '';

                      html += '<div class="text-left" style="text-transform: uppercase; font-size: 13px !important">';
                      html += `<p style="margin-bottom: 0"><strong>Reference No: </strong> ${data.ot_data.reference_no} </p>`;
                      html += `<p style="margin-bottom: 0"><strong>Datetime: </strong> ${moment(data.ot_data.date_from).format('YYYY-MM-DD hh:mm A')} - ${moment(data.ot_data.date_to).format('YYYY-MM-DD hh:mm A')}</p>`;
                      html += `<p style="margin-bottom: 0"><strong>Purpose: </strong></p>`;
                      html += `<p style="margin-bottom: 0; margin-left: 10px">${formatToBullets(data.ot_data.purpose)}</p>`;
                      html += '</div>';

                      Swal.fire({
                          icon: 'warning',
                          title: 'Duplicate Overtime Entry Found!',
                          html: html
                      });
                  }
                  
                  toastr.error(data.message, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
              }
            });
        } else {
            Swal.fire({
                icon: "warning",
                title: 'New Overtime',
                text: result.message
            });
        }
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'New Overtime',
            text: 'Invalid selection. Please ensure the selected date range does not exceed 24 hours.'
        });
    }
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

function isValidTimeRange(from, to) {
  const fromDate = new Date(from.replace(' ', 'T'));
  const toDate = new Date(to.replace(' ', 'T'));

  const diffMs = toDate - fromDate;
  const diffMinutes = diffMs / (1000 * 60);
  const diffHours = diffMinutes / 60;

  if (diffMinutes < 30) {
      return { valid: false, message: "Time range must be more than 30 minutes." };
  }

  if (diffHours > 24) {
    return { valid: false, message: "Time range must not exceed 24 hours." };
  }

  return { valid: true };
}

function formatToBullets(text) {
  return text
    .split(/\r?\n|,/)
    .map(item => item.trim())
    .filter(item => item.length)
    .map(item => `- ${item}`)
    .join('<br>');
}