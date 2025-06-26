let search_val = "";
let query_builder = "";
const tblReadings = $("#table-readings-archive").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/billing/get_reading_archive_collection/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function(d){
            d.csrf_token = _csrf_hash,
            d.search['value'] = search_val,
            d.query_builder = query_builder
        }
    },
    searching: true,
    columns: [
        { data: "ref_no", render: function (data) {
              return "<strong style='color: #525252;'>"+data+"</strong>";
            }
        },
        { data: "accountno"},
        { data: "name"},
        { data: "meterno"},
        { data: "reading_date"},
        { data: "model"},
        { data: "block"},
        { data: "lot"},
        { data: "reading", className: "text-right", render: function (data) {
              return "<strong style='color: #525252;'>"+numberWithCommas(data)+"</strong>";
            }
        },
        { data: "status", className: "text-center", render: function (data) {
                return renderStatus(data)
            }
        },
    ],
});

function numberWithCommas(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function renderStatus(data) {
  switch (data) {
      case "1":
          return '<div class="m-badge text-white m-badge--accent m-badge--wide" role="alert"><strong>Billed</strong></div>';
          break;
      default:
          return '<div class="m-badge text-white m-badge--warning m-badge--wide" role="alert"><strong>Unbilled</strong></div>';
          break;
  }
}

$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    clearTimeout(window.searchTimeout);
    window.searchTimeout = setTimeout(function() {
      tblReadings.ajax.reload();
    }, 1000); // 1000ms delay after typing stops
});