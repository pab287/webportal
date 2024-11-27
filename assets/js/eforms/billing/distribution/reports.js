var search_val = "";
var query_builder = "";
var report_selected_year = "";

getSubdvTotalReport();
function getSubdvTotalReport() {
    return $.ajax({
        url: baseUrl("eforms/billing/get_distribution_reports2"),
        type: 'post',
        dataType: 'json',
        data: {
            csrf_token: _csrf_hash,
            year: report_selected_year,
        },
        success: function(data) {
            vm_data_consumption.consumption = data.total_consumption;
            vm_data_total_reading_per_mos.total_reading = data.total_reading_per_mos;
            vm_data_percentage.percentage = data.percentage;
            vm_data_total_mos_subdv.total_mos_subdv = data.total_mos_subdv;
            vm_data_mos_diff.mos_diff = data.monthly_differences;
        }
    });
}

var vm_data_mos_diff = new Vue({
    el: "#distri_mos_diff",
    data: {mos_diff: {}},
    methods: {
        number_with_commas(data) {
            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    },
});

var vm_data_total_mos_subdv = new Vue({
    el: "#distri_subdv_col",
    data: {total_mos_subdv: {}},
    methods: {
        number_with_commas(data) {
            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    },
});

var vm_data_consumption = new Vue({
    el: "#dis_total_consumption",
    data: {consumption: {}},
    methods: {
        number_with_commas(data) {
            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    },
});

var vm_data_total_reading_per_mos = new Vue({
    el: "#dis_total_reading",
    data: {total_reading: {}},
    methods: {
        number_with_commas(data) {
            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    }
});

var vm_data_percentage = new Vue({
    el: "#dis_percentage",
    data: {percentage:{}},
    methods: {
        number_with_commas(data) {
            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    }
});

$('#report-date-picker').datepicker({
    format: "yyyy",
    viewMode: "years",
    minViewMode: "years",
    autoclose: true,
    orientation: 'bottom'
}).on('changeYear', function(e) {
    report_selected_year = e.date.getFullYear();
    $('#report-date-picker .selected-filter').text(report_selected_year);

    getSubdvTotalReport();
});

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function renderMonthData(data, month) {
    const monthVal = data.find(item => Object.keys(item)[0] === month) ?. [month] || "";
    return monthVal !== "" ? numberWithCommas(monthVal) : "<span class='text-danger'>0.00</span>";
}
// ==============================================================================================

var _months = ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
var count_mos = _months.length - 1;
var mos_html = '';
for(var i = 0; i <= count_mos; i++) {
    mos_html += '<tr><td>'+ _months[i] +'</td></tr>';
}
document.getElementById('distri_report_tbl_mos').innerHTML = mos_html;

// ==============================================================================================