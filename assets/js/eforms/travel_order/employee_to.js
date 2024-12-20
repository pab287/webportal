var recordsTotal = 0;
var param_status = "";

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

if(typeof getUrlParameter('status') !== 'undefined'){
    param_status = getUrlParameter('status');
}

$(document).ready(function(){
    $(document).on("click","#cb-select-all",function(){
        var isChecked = $(this).is(':checked');
        $(".selectedTravelOrder").each(function(){
            $(this).prop('checked', isChecked);
        })
        isAccomplishModalOpen = $('.selectedTravelOrder:checked').length > 0;
    })
    $(document).on("click","#selectedTravelOrder",function(){
        if ( $('.selectedTravelOrder:checked').length !== $('.selectedTravelOrder').length) {
            var isChecked = $("#cb-select-all").is(':checked');
            $("#cb-select-all").prop('checked', false);
        }else{
            $("#cb-select-all").prop('checked', true);
        }
        isAccomplishModalOpen = $('.selectedTravelOrder:checked').length > 0;
    })
    $(document).on("click",".submit_approval",function(){
        var accomplishment_dt = $("#accomplishment_dt").val();
        if (accomplishment_dt.length<=0) {
            $("#accomplishment_dt").attr("style","border-color: red");
            $("#accomplishment_dt").parent().append('<span class="help-block form-error">This is a required field</span>');
        }else{

            $(".selectedTravelOrder:checkbox:checked").each(function(){ 
                var tID = $(this).val();
                var reference_no = $(this).attr("reference_no");
                    $.ajax({
                    url: baseUrl("eforms/travel_order/confirm_travel_destination"),
                    type: 'POST',
                    data: {
                        csrf_token: _csrf_hash,
                        travel_order_id: tID
                    },
                    success: function(resp){
                        $.ajax({
                            url: baseUrl("eforms/travel_order/accomplish_travel_v2"),
                            type: "post",
                            global: false,
                            data:{accomplishment_remarks:$("#accomplishment_remarks").val(), param_id:tID, csrf_token: _csrf_hash, accomplishment_dt:accomplishment_dt },
                            dataType: "json",
                            beforeSend: function(){
                                $(".submit_approval").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                            },
                            success:function(data){
                                if (data.status) {
                                    $(".accomplishment_"+tID).removeClass("m-badge--warning");
                                    $(".accomplishment_"+tID).addClass("m-badge--success");
                                    $(".accomplishment_"+tID).find("strong").html("ACCOMPLISHED");
                                    $(".cb"+tID).remove();
                                    $(".submit_approval").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                    $("#accomplishment_dt").val("");
                                    $("#cb-select-all").prop('checked', false);
                                }else{
                                    toastr.error("Reference#: "+reference_no, "Unable to update travel order", 5000);
                                }
                            }
                        })
                    }
                });
            })
            confirmModalWithRemarks.modal('hide');
        }
    })

    document.addEventListener("visibilitychange", function() {
        isAccomplishModalOpen = document.hidden;
    });

    load_telegram_config();
    // setInterval(loadTravelOrder, 5000);
    
})

$('#due_dt').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'yyyy/mm/dd hh:ii:ss',
});

const confirmModalWithRemarks = $('#approved-modal');

function approve_to(){ 
    if ( $('.selectedTravelOrder:checked').length === 0) {
        toastr.error("No Travel Order Selected", "", 5000);
    }else{
        confirmModalWithRemarks.modal('show'); 
    }
    $("#accomplishment_remarks").val("");
}   

var search_val = "";
var start_date = "";
var end_date = "";
var query_builder = "";
var tempSearchValue = "";

var tempTravelOrderPersonnel = $("#temp-travel_order-table").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    columns: [
        { data: "employee_name", title: "Employee Name", width: "70%", 
            render: function(data, meta, row){
                return `<p class='m-0'><span>${data}</span></p>
                    <p class='m-0'><small class='m--font-boldest'>${row.position}</small></p>`;
            }
        },
        { data: "added_by", title: "Added By", width: "25%", 
            render: function(data, meta, row){
                return `<p class='m-0'><span>${data}</span></p>
                    <p class='m-0'><small class='m--font-boldest'>${row.created_at_formatted}</small></p>`;
            }
        },
        { data: "id", title: "Action", orderable: false, className: "text-center",
            render: function(data, meta, row){
                return `<a href='javascript:void(0);' 
                    class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnRemoveTemp' onClick='removeTempPersonnel(${data}, "${row.employee_name}")'>
                    <i class='fa fa-trash'></i>
                </a>`;
            }
        }
    ],
    ajax: {
        url: siteUrl("eforms/travel_order/get_travel_order_temp_personnel_list"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = tempSearchValue;
            return d;
        },
        error: function (xhr, error, code){
            tempTravelOrderPersonnel.ajax.reload(null, false);
        }
    },
});

var tblTravelOrder = $("#table-travel_order").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/travel_order/get_travel_order_list"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
            d.search['value'] = search_val,
            d.start_date = start_date,
            d.end_date = end_date,
            d.query_builder = query_builder,
            d.status = param_status
        },
        error: function (xhr, error, code){
            tblTravelOrder.ajax.reload(null, false);
        }
    },
    // drawCallback: function(settings){
    //     if (recordsTotal > 0 && settings.json.recordsTotal > recordsTotal) { toastr.info("You have a new travel order request!"); }
    //     if (search_val == ''){ recordsTotal = settings.json.recordsTotal; }
    //     isAccomplishModalOpen = $('.selectedTravelOrder:checked').length > 0;
    //     $('body>.tooltip').remove();
    //     $('[data-toggle="m-tooltip"]').tooltip();
    // },
    searching: true,
    columns: [
        {
            width: '2%',
            orderable: false,
            data: null,
            className: 'text-center',
            render: function (data, type, row) {
                if (typeof row.status !=="undefined") {
                    if (row.accomplishment_dt === "0000-00-00 00:00:00" && row.status !=="Pending" || (row.accomplished == 0 && row.accomplished)) {
                        if(row.status == 'Approved'){
                            return ` <label class="m-checkbox m-checkbox--air m-checkbox--state-success cb${row.id}">
                            <input type="checkbox" class="selectedTravelOrder " id="selectedTravelOrder" name="selected[]" value="${row.id}" reference_no="${row.reference_no}"
                                id="cb${row.id}"><span></span></label>
                            `;
                        }else{
                            return ` <label class="m-checkbox m-checkbox--air m-checkbox--state-success cb${row.id}">
                            <input type="checkbox" class="selectedTravelOrder " id="selectedTravelOrder" name="selected[]" value="${row.id}" reference_no="${row.reference_no}"
                                id="cb${row.id}" disabled><span></span></label>
                            `;
                        }
                        
                    }else{
                        if (row.status!=="Pending") {
                            return ` <label class="m-checkbox m-checkbox--air m-checkbox--state-success  "> <input type="checkbox" class="text-gray "  checked disabled ><span></span> </label>`;
                        }else{
                            return `  <label class="m-checkbox m-checkbox--air m-checkbox--state-warning  "> <input type="checkbox" class="text-gray "  disabled ><span></span> </label>`;
                        }
                    }

                }
                
            }
        },
        {
            data: "status", render: function (data, type, row, meta) {
                return renderStatusHtml(data, row)
            }
        },
        // { data: "reference_no" },
        // { data: "company", width: "10%" },
        { data: null },
        { data: null, width: "15%", },
        { data: null, width: "20%", },
        { 
            data: 'created_dt', 
            width: "12%"
        },
        {
            data: "created_dt",
            width: "15%",
            orderable: true,
            render: function (data, type, row, meta) {
                if (row.from_to.length > 0) {
                    let template = "" +
                        "<div class='custom-details'>" +
                        "   <ul class='custom_list--dot' id='from_to'>";
                    row.from_to.forEach((d) => {
                        template += "<li style='font-size: 12px;'>" + d + "</li>";
                    });
                    template += "</ul>" +
                        "</div>";
                    return template;
                }
                return "";
            }
        },
        { data: null, width: "8%", className: "text-center" },
    ],
    columnDefs: [{
        data: null,
        defaultContent: "",
        targets: -1,
        orderable: false,
        render: function (data, type, row, meta) {
            return itemDatatableActions(row);
        },
    }, {
        data: null,
        defaultContent: "",
        targets: 2, //targets: 4,
        orderable: false,
        render: function (data, type, row, meta) {
            var tempHtml = "<div class='custom-details driver--details'><p>No Assigned Driver</p></div>";
            if (typeof row.is_service !== "undefined" && row.is_service == "1" && row.driver !== "") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>" + row.driver + "</p>";
                if (typeof row.vehicle_plate !== "undefined" && row.vehicle_description !== "undefined") {
                    tempHtml += "<p>" + row.vehicle_plate + "</p>";
                    tempHtml += "<p>" + row.vehicle_description + "</p>";
                }
                tempHtml += "</div>";
            }
            if (typeof row.is_hitch !== "undefined" && row.is_hitch == "1" && row.driver !== "") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>" + row.driver + "</p>";
                if (typeof row.vehicle_plate !== "undefined" && row.vehicle_description !== "undefined") {
                    tempHtml += "<p>" + row.vehicle_plate + "</p>";
                    tempHtml += "<p>" + row.vehicle_description + "</p>";
                }
                tempHtml += "</div>";
            }
            if (typeof row.is_commute !== "undefined" && row.is_commute == "1") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>Commute</p>";
                tempHtml += "</div>";
            }
            if (typeof row.is_personal !== "undefined" && row.is_personal == "1") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>Personal</p>";
                tempHtml += "<p>Vehicle</p>";
                tempHtml += "</div>";
            }
            if (typeof row.is_others !== "undefined" && row.is_others == "1" && $.trim(row.others_remarks) !== "") {
                tempHtml = "<div class='custom-details driver--details'>";
                tempHtml += "<p>" + row.others_remarks + "</p>";
                tempHtml += "</div>";
            }

            return tempHtml;
        },
    }, {
        data: null,
        defaultContent: "",
        targets: 3, //targets: 5,
        orderable: false,
        render: function (data, type, row, meta) {
            var tempHtml = "<div class='custom-details driver--details'><ul class='custom_list--dot'><li>No Assigned Personnel</li></ul></div>";
            if (typeof row.personnels !== "undefined" && row.personnels.length > 0) {
                tempHtml = "<div class='custom-details'>";
                tempHtml += "<ul class='custom_list--dot'>";
                jQuery.each(row.personnels, function (i, v) {
                    tempHtml += "<li>" + v + "</li>";
                });
                tempHtml += "</ul>";
                tempHtml += "</div>";
            }

            return tempHtml;
        },
    }, {
        data: null,
        defaultContent: "",
        targets: 4, //targets: 6,
        orderable: false,
        render: function (data, type, row, meta) {
            var tempHtml = "<div class='custom-details driver--details'><p>No Assigned Destination</p></div>";
            if (typeof row.destination !== "undefined" && row.destination.length > 0) {
                tempHtml = "<div class='custom-details'>";
                tempHtml += "<ul class='custom_list--dot'>";
                jQuery.each(row.destination, function (i, v) {
                    tempHtml += "<li>" + v + "</li>";
                });
                tempHtml += "</ul>";
                tempHtml += "</div>";
            }

            return tempHtml;
        },
    }], buttons: [
        {
            extend: 'csv',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, {
            extend: 'excel',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, {
            extend: 'pdf',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }
    ]
});

$("#ExportExcel").on("click", function (e) {
    e.preventDefault();
    tblTravelOrder.button('.buttons-excel').trigger();
    $.ajax({
        url: baseUrl("eforms/travel_order/export_event_log/1"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
            csrf_token : _csrf_hash,
            search : search_val,
            query_builder : query_builder
        }
    });
});

$("#ExportCSV").on("click", function (e) {
    e.preventDefault();
    tblTravelOrder.button('.buttons-csv').trigger();
    $.ajax({
        url: baseUrl("eforms/travel_order/export_event_log/2"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val,
            query_builder : query_builder
        }
    });
});

$("#ExportPDF").on("click", function (e) {
    e.preventDefault();
    tblTravelOrder.button('.buttons-pdf').trigger();
    $.ajax({
        url: baseUrl("eforms/travel_order/export_event_log/3"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val,
            query_builder : query_builder
        }
    });
});

function dateDisplay($row) {
    return $row;
}

function renderStatusHtml(data, row) {
    var action= '';
    switch (data) {
        case "Pending":
            action += '<div class="m-badge m-badge--warning text-white m-badge--wide " role="alert"><small><strong>For Recommendation</strong></small></div>';
            break;
        case "Recommend_Approved":
            action += '<div class="m-badge m-badge--info text-white m-badge--wide " role="alert"><small><strong>Pending Approval</strong></small></div>';
            break;
        case "Approved":
            if (row.accomplishment_dt == "0000-00-00 00:00:00" || (row.accomplished == 0 && row.accomplished)) {
                action += '<div class="m-badge m-badge--accent m-badge--wide accomplishment_'+row.id+'" role="alert"><small><strong>Approved</strong></small></div>';
                // action += '<div class="m-badge m-badge--success m-badge--wide accomplishment_'+row.id+'" role="alert"><small><strong>Approved</strong></small></div>';
            } else {
                action += '<div class="m-badge m-badge--success m-badge--wide" role="alert"><small><strong>Accomplished</strong></small></div>';
            }
            break;
        case "Disapproved":
            action += '<div class="m-badge m-badge--danger m-badge--wide" role="alert"><small><strong>Disapproved</strong></small></div>';
            break;
        case "HR Noted":
            action += '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><small><strong>HR Noted</strong></small></div>';
            break;
        case "Received":
            action += '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><small><strong>Received</strong></small></div>';
            break;
        default:
            action += '<div class="m-badge m-badge--metal text-white m-badge--wide" role="alert"><small><strong>Cancelled</strong></small></div>';
            break;
    }

    action += '<div style="line-height: 1.1"><p class="mt-2 mb-0 m-font-3"><small><b>Reference no: '+row.reference_no+'</b></small></p>';
    action += '<p class="mb-0 m-font-3"><small><b>File Under:</b> '+row.company+'</small></p>';
    /*** action += '<p class="mb-0 m-font-3"><small><b>Date Created:</b> '+row.created_dt+'</small></p></div>';    ***/

    return action;
}

function formatCalendarDate(data) {
    if (data == "0000-00-00 00:00:00") {
        return "";
    } else {
        return moment(data).format("MM/DD/YYYY");
    }
}

function itemDatatableActions(row) {
    if (row) {
        var _actionButton = "";

        var _viewUrl = baseUrl('eforms/travel_order/view_travel_order?id=' + row.id);
        var _viewUrl_edit = baseUrl('eforms/travel_order/edit_travel_order?id=' + row.id);

        if ($.inArray("edit", _currentActions) !== -1) {

            // if(row.status != 'Approved' && row.status != 'Recommend_Approved'){
            //     _actionButton += " <a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit' href='" + _viewUrl_edit + "' target='__blank' data-toggle='m-tooltip' data-placement='top' data-original-title='Edit' data-skin='dark'><i class='la la-pencil-square'></i></a>";
            // }

            if(row.status != 'Approved'){
                _actionButton += " <a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit' href='" + _viewUrl_edit + "' target='__blank' data-toggle='m-tooltip' data-placement='top' data-original-title='Edit' data-skin='dark'><i class='la la-pencil-square'></i></a>";
            }

            // if(row.status == 'Recommend_Approved'){
            //     _actionButton += " <a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit' href='javascript:void(0)' data-toggle='m-tooltip' data-placement='top' data-original-title='Pending Approval' data-skin='dark'><i class='la la-pencil-square'></i></a>";
            // }

            _actionButton += " <a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' href='" + _viewUrl + "' target='__blank' data-toggle='m-tooltip' data-placement='top' data-original-title='View' data-skin='dark'><i class='flaticon-clipboard'></i></a>";
        }
        
        if ($.inArray("guard_edit_to", _currentActions) !== -1) {
            _actionButton += " <a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnGuardEditTo' data-toggle='m-tooltip' data-placement='top' data-original-title='View' data-skin='dark' onclick='modalDetails("+row.id+")'><i class='la la-barcode'></i></a>";
        }
        
        return _actionButton;
    } else {
        return false;
    }
}

function modalDetails(id){
    $("#layout_remarks").hide();
    
    $.ajax({
        url : baseUrl("eforms/travel_order/get_travel_order_details"),
        type: "POST",
        dataType: "JSON",
        data:  { csrf_token: _csrf_hash, id: id },
        success: function(data){
            console.log(data)
            
            var travel_type = '';
            if(data.is_commute > 0){
                travel_type = 'Commute';
            } else if(data.is_hitch > 0){
                travel_type = 'Hitch';
                $("#label_driver").html(data.driver_name);
                $("#label_vehicle").html(data.vehicle_name);
            } else if(data.is_others > 0){
                travel_type = 'Others';
                $("#label_remarks").html(data.others_remarks);
                $("#layout_remarks").show();
            } else if(data.is_personal > 0){
                travel_type = 'Personal Vehicle';
            } else if(data.is_service > 0){
                travel_type = 'Service';
                $("#label_driver").html(data.driver_name);
                $("#label_vehicle").html(data.vehicle_name);
            }

            $("#label_file_under").html(data.company);
            $("#label_department").html(data.department);
            $("#label_type").html(data.type);
            $("#label_official_station").html(data.station);
            $("#label_travel_type").html(travel_type);
            $("#label_requested_by").html(data.created_by);
            $("#label_personnel").html(data.personnel_list);
            $("#label_destination").html(data.destination_list);

            $("#m_viewDetails").modal('show');
        }
    });
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblTravelOrder.ajax.reload(null, false);
});

$('#temporarySearch').donetyping(function (callback) {
    tempSearchValue = $(this).val();
    tempTravelOrderPersonnel.ajax.reload(null, false);
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    loadTravelOrder();
});

$("#m_daterangepicker_2").daterangepicker({
    minDate: moment().subtract(2, 'years'),
}, function (start, end, label) {
    $("#end_date").val(end.format('YYYY-MM-DD HH:mm:ss'));
    $("#start_date").val(start.format('YYYY-MM-DD HH:mm:ss'));
    $("#range_date").val(start.format('L') + ' – ' + end.format('L'));
});

$('#m_daterangepicker_2').on('apply.daterangepicker', function (start, end) {
    //do something, like clearing an input
    start_date = $("#start_date").val();
    end_date = $("#end_date").val();
    //search_val= $("#range_date").val();
    loadTravelOrder();
});

$(document).ready(function () {
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': { delay: 100 },
        filters: [
            // { id: 'a.id', label: 'ID #', type: 'integer' },
            {
                id: 'a.type',
                label: 'Type',
                type: 'string',
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select. .',
                    width: '110%',
                    data: [
                        {
                            id: "internal",
                            text: "Internal"
                        }, {
                            id: "external",
                            text: "External"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            }, {
                id: 'a.status',
                label: 'Status',
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select. .',
                    width: '110%',
                    data: [
                        {
                            id: "Pending",
                            text: "For Recommendation"
                        }, {
                            id: "Approved",
                            text: "Approved"
                        }, {
                            id: "Disapproved",
                            text: "Disapproved"
                        }, 
                        // {
                        //     id: "Received",
                        //     text: "Received"
                        // }, 
                        // {
                        //     id: "HR Noted",
                        //     text: "HR Noted"
                        // }, 
                        {
                            id: "Accomplished",
                            text: "Accomplished"
                        }, {
                            id: "Recommend_Approved",
                            text: "For Approval"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },
            { id: 'reference_no', label: 'Advice #', type: 'string' },
            { id: 'personnel', field: 'CONCAT(toe.firstname, toe.lastname)',  label: 'Personnel', type: 'string', operators: ['contains', 'equal'] },
            { id: 'tod.destination', label: 'Destination', type: 'string', operators: ['contains', 'equal', 'not_equal'] },
            { id: 'c.company', label: 'File Under', type: 'string' },
            {
                id: 'driver_id',
                label: 'Driver',
                type: 'integer',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select . . .',
                    width: '110%',
                    dropdownParent: $("#modal-query-builder"),
                    ajax: {
                        url: baseUrl("eforms/Travel_order/driver"),
                        dataType: "json",
                        global: false,
                        delay: 500,
                        done: function (data) {
                            return data;
                        }
                    }
                },
                operators: ['equal', 'not_equal']
            },
        ],
    });

    $("#query-builder_group_0").addClass("col-12");
});

$('#query-builder-btn').on('click', function () {
    var result = $('#query-builder').queryBuilder('getSQL');

    if (!$.isEmptyObject(result)) {
        query_builder = result;
        loadTravelOrder();
        $("#modal-query-builder").modal("hide");
    }
});

function clear_query_builder() {
    $('#query-builder').queryBuilder('reset');
    query_builder = null;
    loadTravelOrder();
}

function test_email(){
    $.ajax({
        url : baseUrl("eforms/travel_order/generate_daily_to_summary_for_next_day"),
        type: "POST",
        dataType: "JSON",
        data:  { csrf_token: _csrf_hash },
        success: function(data){
        }
    });
}

function save_telegram_config(){
    var chat_id = $("#chat_id").val();
    var telegram_bot_token = $("#telegram_bot_token").val();
    var module = $("#module").val();
    var id = $("#config_id").val();
    $.ajax({
        url : baseUrl("eforms/travel_order/add_telegram_config"),
        type: "POST",
        dataType: "JSON",
        data:  { 
            csrf_token : _csrf_hash, 
            telegram_bot_token : telegram_bot_token,
            chat_id : chat_id,
            module : module,
            id : id
        },
        success: function(data){
            toastr.success(data.toastr_msg, "Telegram Configuration Updated!", 5000);
            $("#modal_telegram_config").modal("hide");
        }
    });
};

function load_telegram_config(){
    var module = "travel_order";
    $.ajax({
        url : baseUrl("eforms/travel_order/load_telegram_config"),
        type: "POST",
        dataType: "JSON",
        data:  { 
            csrf_token : _csrf_hash,
            module : module
        },
        success: function(data){
            $("#config_id").val(data.id);
            $("#telegram_bot_token").val(data.telegram_bot_token);
            $("#chat_id").val(data.chat_id);
        }
    })
}

$('#approved-modal').on('show.bs.modal', function () {
    isAccomplishModalOpen = true;
});

$('#approved-modal').on('hidden.bs.modal', function () {
    isAccomplishModalOpen = false;
});

var isAccomplishModalOpen = false;

function loadTravelOrder() {
    if(!isAccomplishModalOpen){
        tblTravelOrder.ajax.reload(null, false);
    }
}

function removeTempPersonnel(id, name){
    if(id){
        Swal.fire(
            '',
            '',
            'question'
        );
        Swal.fire({
            title: 'Temporary Personnel?',
            html: "Are you sure you want to remove <strong class='m--font-danger'>`"+name+"`</strong> on the temporary personnel list?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Remove it!'
          }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url : baseUrl("eforms/travel_order/remove_temporary_employee_data"),
                    type: "POST",
                    dataType: "JSON",
                    data:  { csrf_token : _csrf_hash, id : id },
                    success: function(json){
                        if(json.response){
                            tempTravelOrderPersonnel.ajax.reload(null, false);
                            Swal.fire({
                                title: 'Removed!',
                                html: "<strong class='m--font-danger'>"+name+ "</strong> has been removed successfully.",
                                icon: 'success',
                            });
                        }else{
                            Swal.fire({
                                title: 'Failed!',
                                html: "<strong class='m--font-danger'>"+name+ "</strong> was not removed on the temporary personnel list!",
                                icon: 'error',
                            });
                        }
                    }
                });
            }
          });
    }
}