<div class="m-content">
  <div class="m-portlet m-portlet--mobile">
    <div class="m-portlet__head">
      <div class="m-portlet__head-caption">
      <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Payslip Receive Options
                            </h3>
                        </div>
      </div>
    </div>
    <div class="m-portlet__body">
 <form id="form-filter">
 <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
  <div style="display: flex; gap: 10px; align-items: end;">
  <div class="form-group pt-3">  
  <label for="company">COMPANY</label> 
    <select class="form-control " id="company" name="company" style="width: 200px;"></select> 
  </div>
 <div class="form-group pt-3" id="payrollGroupContainer">   
      <label for="employee">PAYROLL GROUP</label>
      <select class="form-control " id="payroll_group" style="width: 200%;" disabled></select>
  </div>
  <div class="form-group pt-3"> 
  <button type="submit" id="btnsubmit" class="btn btn-success" disabled>Search</button>
</div>
  </div>
 </form>

 <input type="text" class="form-control" placeholder="Search..." id="search-item" style="width: 280px;">
      <table class="table table-striped table-bordered dataTable no-footer" id="payslip-options-table">
        <thead>
          <tr>
            <th></th>
          <th>Employee</th>
          <th>Position</th>
          <th>Company</th>
          <!-- <th>Payroll Group</th> -->
          <th width="10%">
  Telegram <br> Check All <br>
  <label class="m-checkbox m-checkbox--state-brand telegram-checkbox">
    <input type="checkbox" id="check-all-telegram" class="telegram-checkbox">
    <span></span>
  </label>
</th>
<th width="10%">
  Printed  <br> Check All<br>
  <label class="m-checkbox m-checkbox--state-brand printed-checkbox">
    <input type="checkbox" id="check-all-printed" class="printed-checkbox" checked>
    <span></span>
  </label>
</th>
<th width="10%">
  Email <br> Check All<br>
  <label class="m-checkbox m-checkbox--state-brand printed-checkbox">
    <input type="checkbox" id="check-all-email" class="email-checkbox">
    <span></span>
  </label>
</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
  var filteredIds = [];
  var enabledFilter = false;
  var typingTimer;
  let inputVal = ''; 
  var doneTypingInterval = 1000;
  var $search = $('#search-item');
  $search.on('keyup', function() {
          clearTimeout(typingTimer);
          typingTimer = setTimeout(doneTyping2, doneTypingInterval);
        });
        $search.on('keydown', function() {
          clearTimeout(typingTimer);
        });
        function doneTyping2() {
          inputVal = $search.val();
          payslipOptions_table.ajax.reload();
        }

  var payslipOptions_table = $('#payslip-options-table').DataTable({
  serverSide: true,
  processing: true,
  searching: false,
  ajax: {
    url: baseUrl('hris/receive_option/get_payslip_options'),
    data: function (d) {
      d.csrf_token = _csrf_hash;
      d.ids = filteredIds;
      d.filtered = enabledFilter;
      d.search['value'] = $("#search-item").val();
    },
    type: "post",
    dataType: "json",
  },
columns: [
  { data: "id", visible: false },
  { data: "employee" },
  { data: "position" },
  { data: "company" },
  {
    className: 'text-center',
    orderable: false,
    data: "is_telegram",
    render: function(data, type, row) {
  const isChecked = data == 1;
  const checkboxClass = isChecked ? 'm-checkbox--checked' : '';
  const isTelegramEmpty = row.telegram_id == null || row.telegram_id == '';
  const checkboxDisabled = isTelegramEmpty ? 'disabled' : '';
  const tooltip = isTelegramEmpty ? 'data-toggle="tooltip" title="Missing telegram id" data-placement="top"' : '';

  return `<span ${tooltip}>
            <label class="m-checkbox m-checkbox--state-brand ${checkboxClass}">
              <input type="checkbox" class="telegram-checkbox" ${isChecked ? 'checked' : ''} ${checkboxDisabled}>
            <span></span>
          </label></span>`;
},
  },
  {
    className: 'text-center',
    orderable: false,
    data: "is_printed",
    render: function(data, type, row) {
      const isChecked = data == 1 || data == null;
      const checkboxClass = isChecked ? 'm-checkbox--checked' : '';

      return `<label class="m-checkbox m-checkbox--state-brand ${checkboxClass}">
                <input type="checkbox" class="printed-checkbox" ${isChecked ? 'checked' : ''}>
                <span></span>
              </label>`;
    },
  },
  {
    className: 'text-center',
    orderable: false,
    data: "is_email",
    render: function(data, type, row) {
      const isChecked = data == 1;
      const checkboxClass = isChecked ? 'm-checkbox--checked' : '';
      const isEmailEmpty = row.email_id == null || row.email_id == '';
      const checkboxDisabled = isEmailEmpty ? 'disabled' : '';
      const tooltip = isEmailEmpty ? 'data-toggle="tooltip" title="Missing email" data-placement="top"' : '';
      return `
      <span ${tooltip}>
      <label class="m-checkbox m-checkbox--state-brand ${checkboxClass}" style="text-align: 'center'">
                <input type="checkbox" class="email-checkbox" ${isChecked ? 'checked' : '' } ${checkboxDisabled}>
                <span></span>
              </label>
              </span>`;
    },
  }
],
drawCallback: function() {
    $('[data-toggle="tooltip"]').tooltip();
  }
});
// $("#search-item").donetyping(function () {});

$(document).on('change', '.telegram-checkbox, .printed-checkbox, .email-checkbox', function() {
    var rowData = payslipOptions_table.row($(this).closest('tr')).data();
    var ids = [];
    if (rowData) {
        var id = rowData.id;
        var isChecked = $(this).prop('checked') ? 1 : 0;
        var checkboxType;
        if ($(this).hasClass('telegram-checkbox')) {
            checkboxType = 'Telegram';
        } else if ($(this).hasClass('printed-checkbox')) {
            checkboxType = 'Printed';
        } else if ($(this).hasClass('email-checkbox')) {
            checkboxType = 'Email';
        }
        ids.push(id);
        updateIsStatus(ids, isChecked, checkboxType);
    }
});

$(document).on('change', '#check-all-telegram, #check-all-printed, #check-all-email', function() {
    var ids = [];
    var isChecked = $(this).prop('checked') ? 1 : 0;
    var checkboxClass;
    if ($(this).hasClass('telegram-checkbox')) {
        checkboxClass = '.telegram-checkbox';
    } else if ($(this).hasClass('printed-checkbox')) {
        checkboxClass = '.printed-checkbox';
    } else if ($(this).hasClass('email-checkbox')) {
        checkboxClass = '.email-checkbox';
    }
    payslipOptions_table.rows().every(function() {
        var data = this.data();
        var checkbox = $(this.node()).find(checkboxClass);

        if (checkbox.length > 0 && !checkbox.prop('disabled')) {
            ids.push(data.id);
        }
    });
    if (ids.length > 0) {
        // Determine the checkboxType based on the class
        var checkboxType;
        if ($(this).hasClass('telegram-checkbox')) {
            checkboxType = 'Telegram';
        } else if ($(this).hasClass('printed-checkbox')) {
            checkboxType = 'Printed';
        } else if ($(this).hasClass('email-checkbox')) {
            checkboxType = 'Email';
        }
        updateIsStatus(ids, isChecked, checkboxType);
    }
});


$('#check-all-telegram').change(function () {
  var isChecked = $(this).prop('checked');
  $('.telegram-checkbox').each(function() {
    if (!$(this).prop('disabled')) {
      $(this).prop('checked', isChecked);
    }
  });
});

$('#check-all-printed').change(function () {
  var isChecked = $(this).prop('checked');
  $('.printed-checkbox').each(function() {
    if (!$(this).prop('disabled')) {
      $(this).prop('checked', isChecked);
    }
  });
});

$('#check-all-email').change(function () {
  var isChecked = $(this).prop('checked');
  $('.email-checkbox').each(function() {
    if (!$(this).prop('disabled')) {
      $(this).prop('checked', isChecked);
    }
  });
});



$('#company').on('change', function () {
  $('#btnsubmit').prop('disabled', true);
  $('#payroll_group').empty();
  $('#payroll_group').prop('disabled', false);
});
 // Initialize Select2 for payroll_group
 $("#payroll_group").select2({
      placeholder: 'Select a payroll group ',
      width: '100%',
      ajax: {
        url: baseUrl("hris/receive_option/select_payroll_group"),
        dataType: "json",
        type: 'get',
        delay: 250,
        global: false,
        data: function(params) {
          params.company_id = $("form select#company").val();
          return params;
        },
        processResults: function(data) { 
          if (data.results.length > 1) {
            data.results.unshift({
            id: 'all',
            text: 'All',
            employee_ids: []
                  });
                }       
        results = data.results;
        return data;
        }
      }
    }).on("select2:select",function(e){
      $('#btnsubmit').prop('disabled', false);
      if (e.params.data.id === 'all') {
        $.ajax({
          url: baseUrl("hris/receive_option/select_payroll_group"),
          dataType: "json",
          type: 'get',
          delay: 250,
          global: false,
          data: {
            company_id : $("form select#company").val(),
            payrollId : "All",
            },
            success: function(data) {
              console.log(data);
                  filteredIds = [].concat.apply([], data.results.map(function(result) {
                  return result.employee_ids;
              }));
            },
        });
  } else {
    filteredIds = e.params.data.employee_ids;
  }
    });

    $("#form-filter").on("submit", function (e) {
      e.preventDefault();
      enabledFilter = true;
      payslipOptions_table.ajax.reload();
        // $('#btnsubmit').hide();
        // $('#payrollGroupContainer').hide();  
      });

    // Initialize Select2 for company
    $("#company").select2({
      placeholder: 'Select a company',
      width: '100%',
      ajax: {
        url: baseUrl("hris/receive_option/get_company_list"),
        dataType: "json",
        delay: 250,
        global: false,
        processResults: function(data) {
          return data;
        }
      }
    });

});

function updateIsStatus(employeeIds, newStatus, checkbox) {
  var dataToUpdate = {};
  if (checkbox === "Printed") {
    dataToUpdate.isPrinted = newStatus;
  } else if (checkbox === "Telegram") {
    dataToUpdate.isTelegram = newStatus;
  }
  else if (checkbox === "Email") {
    dataToUpdate.isEmail = newStatus;
  }
  $.ajax({
    url: baseUrl('hris/receive_option/update_payslip_options'), 
    type: 'POST',
    dataType: "json",
    data: {
      employeeIds: employeeIds,
      csrf_token: _csrf_hash,
      ...dataToUpdate,
    },
    success: function(response) {
      console.log(response);
    },
    error: function(xhr, status, error) {
      // Handle error if needed
      console.error(error);
    }
  });
}
</script>