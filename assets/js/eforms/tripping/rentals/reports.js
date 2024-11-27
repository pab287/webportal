var dtReport = $('#table-report').DataTable({
    paging: false,
    footerCallback: function(row, data, start, end, display){
      var api = this.api();

      // Remove the formatting to get integer data for summation
      var intVal = function (i) {
        return typeof i === 'string' ?
            i.replace(/[\$,]/g, '') * 1 :
            typeof i === 'number' ?
            i : 0;
    };

       // Total over all pages
       total = api
       .column(5)
       .data()
       .reduce(function (a, b) {
           return intVal(a) + intVal(b);
       }, 0);

      // Total over this page
      pageTotal = api
      .column(5, {
          page: 'current'
      })
      .data()
      .reduce(function (a, b) {
          return intVal(a) + intVal(b);
      }, 0);

       // Update footer
       $(api.column(5).footer()).html(
        pageTotal
    );
    }
});

$("#m_daterangepicker_1").daterangepicker({
    buttonClasses: 'm-btn btn',
    applyClass: 'btn-primary',
    cancelClass: 'btn-secondary',
    locale: {
        format: 'YYYY-MM-DD'
    }
}, function(start, end, label) {
    $('#m_daterangepicker_1 .form-control').val( start.format('YYYY-MM-DD') + ' / ' + end.format('YYYY-MM-DD'));
});

$("#driverSelect").select2({
    placeholder: 'SELECT Driver',
    width: '100%',
    ajax: {
      url: baseUrl("eforms/tripping/get_driver_select2"),
      global: false,
      processResults: function (data) {
        return data;
      }
  
    }
});


$.validate({
    form: '#frmSearchRental',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
          url: $("#frmSearchRental").attr('action'),
          type: "POST",
          data: $('#frmSearchRental').serialize(),
          dataType: "JSON",
          success: function (data) {
                dtReport.clear().draw();
                if (data.response) {
                    dtReport.rows.add(data.data).draw();
                } else {

                }
          }
        });
        return false;
    },
  });