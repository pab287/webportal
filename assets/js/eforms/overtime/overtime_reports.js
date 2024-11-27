$(document).ready(function (e) {
  select2Employees();
});

$(function() {
  // Define the predefined ranges
  const ranges = {
      'Today': [moment(), moment()],
      'Last 7 Days': [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'Last Year': [moment().subtract(1, 'years').startOf('year'), moment().subtract(1, 'years').endOf('year')]
  };

  // Initialize the date range picker
  $('#date_time').daterangepicker({
      ranges: ranges,
      locale: {
          format: 'YYYY-MM-DD'
      },
      alwaysShowCalendars: true,
      opens: 'left'
  }).on('apply.daterangepicker', function(ev, picker) {
    dateRange = {
      start: picker.startDate.format('YYYY-MM-DD'),
      end: picker.endDate.format('YYYY-MM-DD')
  };
});
});


let _companies = [];
let employee = [];
let psEmployeeGroup = [];
var selectedCompany = null;
var payroll_group = null;
var dateRange = {
  start: moment().format('YYYY-MM-DD'),
  end: moment().format('YYYY-MM-DD')
};
var tblOvertimeReport = $("#table-overtime-report").DataTable({
  dom: 'Blfrtip',
  serverSide: true,
  processing: true,
  searching: false,
  order: [[0, 'desc']],
  ajax: {
    url: baseUrl("eforms/overtime/get_reports"),
    type: "post",
    global: false,
    dataType: "json",
    data: function (d) {
      d.csrf_token = _csrf_hash;
      d.company = selectedCompany; 
      d.dateRange = dateRange;
      d.payroll_group = payroll_group;
      d.employees = employee;
    }
},
columns: [
  // { data: 'id' },
  { data: 'reference_no' },
  { data: 'emp_name' },
  { data: 'company' },
  { data: 'purpose' },
  
  {
    data: "actual_time_start", render: function (data, type, row, meta) {
        return formatCalendarDate(data, row)
    }
},
{
  data: "actual_time_end", render: function (data, type, row, meta) {
      return formatCalendarDate(data, row)
  }
},
  { data: "gps_tracking_in" },
  { data: "gps_tracking_out" },
  {
    data: "duration", render: function (data, type, row, meta) {
        return formatDifference(data, row)
    }
},
],
  buttons: [
{
  extend: 'excelHtml5',
  className: 'btnExcelAction',
  exportOptions : {
    //columns to be included in export excel
    columns: [0,1, 2, 3, 4, 5, 6, 7, 8,]
  }
},
{
  extend: 'pdfHtml5',
  className: 'btnPdfAction',
  orientation: 'portrait',
  pageSize: 'A3',
  customize: function (doc) {
    // doc.defaultStyle.alignment = 'left'; // Center align all text
    doc.styles.tableHeader.alignment = 'center'; // Center align header text
    doc.styles.tableBodyEven.alignment = 'left'; // Center align even rows
    doc.styles.tableBodyOdd.alignment = 'left'; // Center align odd rows
    
    doc.content[1].table.body.forEach(function (row) {
      row.forEach(function (cell) {
        if (cell.text) {
          cell.text = cell.text.toUpperCase();
        }
      });
    });
 
  }
},
  ],
  initComplete: function (settings, json) {
    $(".btnPdfAction").addClass("m-portlet__nav-link btn m-btn--square btn-warning text-white");
$(".btnExcelAction").addClass("m-portlet__nav-link btn m-btn--square btn-warning text-white");
}
});

$("#company").select2({
        placeholder: 'Select an option',
        width: '100%',
        data: _tempContentData.company,
        allowClear: true,
    })
    .on("select2:select", function (data) {
        selectedCompany = data.params.data;
    });

$('#company').on('select2:unselect', function () {
  selectedCompany = null;
  payroll_group = null;
});

var resetFilter = function (event) {
  const form = $(event).closest("form");
  if (typeof form !== "undefined" && form.length == 1) {
      const select2 = form.find("#employee, #payroll_group");
      if (typeof select2 !== "undefined" && select2.length > 0) {
          $.each(select2, function (i, v) {
              const multi = $(v)[0].multiple;
              if (multi) {
                  $(v).val([])
                      .trigger("change")
                      .prop("disabled", false);
              } else {
                  $(v).val("")
                      .trigger("change");
              }
          });
      }
      psEmployeeGroup = [];
  }
}

$("#payroll_group").select2({
  placeholder: 'Select an option',
  width: '100%',
  ajax: {
      url: 'select_payroll_group',
      dataType: "json",
      type: 'get',
      delay: 250,
      global: false,
      data: function (params) {
          params.company_id = $("#company").val();
          return params;
      },
      processResults: function (data) {
          return data;
      }
  }
}).on('select2:select', function (e) {
  const _this = this;
  const tempVal = $(_this).val();
  const data = e.params.data;
  let employees = [];
  if (typeof data.employees == "object" && typeof data.employees !== "undefined") { employees = data.employees; }
  if (tempVal.length > 1) {
    $.ajax({
        url: "select_payroll_group_multiple",
        type: "post",
        dataType: "json",
        data: { group_id: tempVal, [_csrf_token]: _csrf_hash },
        success: function (json) {
            if (json.response) {
                const tempData = json.data;
                if (typeof tempData == "object" && typeof tempData !== "undefined") {
                    const tempEmployeeSelector = $("#employee");
                    if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                        tempEmployeeSelector.empty();
                        $.each(tempData, function (ii, vv) {
                            var tempOption = new Option(vv.text, vv.id, true, true);
                            tempEmployeeSelector.append(tempOption);
                        });
                        tempEmployeeSelector.prop("disabled", true);
                    }
                }
            }
        }
    });
}
else{
  if (typeof employees == "object" && typeof employees !== "undefined") {
    const tempEmployeeSelector = $("#employee");
    if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
        tempEmployeeSelector.empty();
        $.each(employees, function (ii, vv) {
            var tempOption = new Option(vv.text, vv.id, true, true);
            tempEmployeeSelector.append(tempOption);
        });
        tempEmployeeSelector.prop("disabled", true);
    }
}
}
if (typeof data.text !== "undefined" && data.text) {
  const tempEmpGroup = data.text;
  let tempIsInArray = $.inArray(tempEmpGroup, psEmployeeGroup);
  if (tempIsInArray == -1) {
      psEmployeeGroup.push(tempEmpGroup);
  }
}
});

$('#payroll_group').on('select2:unselect', function (e) {
  const _this = this;
  const tempValUnselected = $(_this).val();
  const data = e.params.data;
  if (tempValUnselected.length == 0) {
    const tempEmployeeSelector = $("form#frm-filter select#employees");
    if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
        tempEmployeeSelector.prop("disabled", false);
    }
}
else{
  $.ajax({
    url: baseUrl("payroll/get_payroll_group_multiple"),
    type: "post",
    dataType: "json",
    data: { group_id: tempValUnselected, [_csrf_token]: _csrf_hash },
    success: function (json) {
        if (json.response) {
            const tempData = json.data;
            if (typeof tempData == "object" && typeof tempData !== "undefined") {
                const tempEmployeeSelector = $("#employee");
                if (typeof tempEmployeeSelector !== "undefined" && tempEmployeeSelector.length == 1) {
                    tempEmployeeSelector.empty();
                    $.each(tempData, function (ii, vv) {
                        var tempOption = new Option(vv.text, vv.id, true, true);
                        tempEmployeeSelector.append(tempOption);
                    });
                    tempEmployeeSelector.prop("disabled", true);
                }
            }
        }
    }
});
}
if (typeof data.text !== "undefined" && data.text) {
  const tempEmpGroup = data.text;
  let tempIsInArray = $.inArray(tempEmpGroup, psEmployeeGroup);
  if (tempIsInArray !== -1) {
      const index = psEmployeeGroup.indexOf(tempEmpGroup);
      if (index > -1) { psEmployeeGroup.splice(index, 1); }
  }

}
});

var select2Employees = function () {
  $("#employee").select2({
          placeholder: 'Select an option',
          width: '100%',
          ajax: {
              url: 'select_employee',
              dataType: "json",
              delay: 250,
              global: false,
              processResults: function (data) {
                  return data;
              }
          }
      });
}

$('#reportsForm').on('submit', function(event) {
  event.preventDefault();
  $('#company').trigger('change');
  $('#payroll_group').trigger('change');
  $('#employee').trigger('change');
    selectedCompany = $('select[name="company"]').val();
    dateRange = dateRange;
    payroll_group = $('select[name="payroll_group"]').val();
    employee = $('select[name="employee[]"]').val(); // Get the array of selected employee values
  tblOvertimeReport.ajax.reload();
});


function formatDifference(data, row) {
  // Parse the start and end dates from the row data
  var startDate = new Date(row.date_from);
  var endDate = new Date(row.date_to);
  var difference = endDate - startDate;
  return millisecondsToHumanReadable(difference);
}

function millisecondsToHumanReadable(milliseconds) {
  // Calculate hours, minutes, and seconds
  var hours = Math.floor(milliseconds / (1000 * 60 * 60));
  var minutes = Math.floor((milliseconds % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((milliseconds % (1000 * 60)) / 1000);

  // Format the output string
  var humanReadable = '';
  if (hours > 0) {
      humanReadable += hours + ' hour' + (hours > 1 ? 's ' : ' ');
  }
  if (minutes > 0) {
      humanReadable += minutes + ' minute' + (minutes > 1 ? 's ' : ' ');
  }
  if (seconds > 0) {
      humanReadable += seconds + ' second' + (seconds > 1 ? 's' : '');
  }

  // If the difference is 0, set a default message
  if (humanReadable === '') {
      humanReadable = 'DATE ERROR';
  }

  return humanReadable.trim();
}

function formatCalendarDate(data, row) {

  if (data == "0000-00-00 00:00:00") {
      return "";
  } else {
      if (row.date_to == "0000-00-00 00:00:00") {
          return moment(data).format("MM/DD/YYYY");
      } else {
          return moment(data).format("MM/DD/YYYY hh:mm A") + " - " + moment(row.date_to).format("MM/DD/YYYY hh:mm A");
      }
  }

}