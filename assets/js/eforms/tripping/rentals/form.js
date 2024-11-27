$("#driversSelect").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    minimumInputLength: 3,
    ajax: {
      url: baseUrl("eforms/tripping/get_driver_select2"),
      global: false,
      processResults: function (data) {
        return data;
      }
  
    }
});

$("#driversUnitSelect").select2({
    placeholder: 'SELECT Vehicle',
    width: '100%',
    ajax: {
      url: baseUrl("eforms/tripping/get_drivers_vehicle"),
      global: false,
      processResults: function (data) {
        return data;
      }
  
    }
});

$('#m_datepicker_rental').datepicker({
    todayHighlight: true,
    dateFormat: 'YYYY-MM-DD',
    orientation: "bottom left",
    templates: {
        leftArrow: '<i class="la la-angle-left"></i>',
        rightArrow: '<i class="la la-angle-right"></i>'
    },
});

$.validate({
    form: '#frmRentalForm',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
          url: $("#frmRentalForm").attr('action'),
          type: "POST",
          data: $('#frmRentalForm').serialize(),
          dataType: "JSON",
          success: function (data) {
                if (data.status) {
                  $('#frmRentalForm')[0].reset();
                    toastr.success(data.msg, "Notification");
                } else {
                    toastr.error(data.msg, "Notification");
                }
          }
        });
        return false;
    },
  });