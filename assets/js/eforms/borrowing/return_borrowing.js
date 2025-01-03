var search_val = "";
var query_builder = "";
var tblBorrowing = $("#table-borrowing").DataTable({
    dom: '<"toolbar">rtlip',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("eforms/borrowing/get_return_request/"),
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
        { data: "reference_no"},        
        { data: "display_name", // data: "firstname"
            render: function (data, type, row, meta) {
                var html = ``;
                // return displayName(row.display_name)

                if(data){
                    html += `<b>${ data }</b>`;
                    html += `<p class="m-0">${ row.company }</p>`;
                    html += `<p class="m-0">${ row.department }</p>`;
                    html += `<p class="m-0">${ row.position }</p>`;
                }

                return html;
            }
        },
        { data: "asset"},
        { data: "date_borrowed", render: function (data) {return formatCalendarDate(data)}},
        { data: "date_due", render: function ( data, type, row, meta ) {return formatCalendarDateDue(data,row)}},
        { data: "date_returned", render: function ( data, type, row, meta ) {return formatCalendarDateReturn(data,row)}},
        { data: "return_remarks"},
        { data: null, width: "8%", className: "text-center"},
    ],
    columnDefs: [
        { targets: [0], className: "statusAlign" },       
        { targets: [7], width: "5%" },
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
          
            render: function ( data, type, row, meta ) { return itemDatatableActions(row.id); },
        }
       
      
    ],buttons: [
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

$("#ExportExcel").on("click", function() {
    tblBorrowing.button( '.buttons-excel' ).trigger();
});

$("#ExportCSV").on("click", function() {
    tblBorrowing.button( '.buttons-csv' ).trigger();
});

$("#ExportPDF").on("click", function() {
    tblBorrowing.button( '.buttons-pdf' ).trigger();
});

function displayName($displayName){
    return $displayName;
}

$.ajax({
    url: baseUrl('eforms/borrowing/overdue_count/') ,
    type: "POST",
    dataType: "JSON",
    data:  { csrf_token: _csrf_hash },
    success: function(data){

        $('#overdue').append(data);
      
    }, error: function (jqXHR, textStatus, errorThrown){
        alert('Error: "count"');
    }
}); 


function formatCalendarDate(data){
    if(data=="0000-00-00 00:00:00"){
        return "";
    }
    else{
        return moment(data).format("MM/DD/YYYY");
    }
  
}
function formatCalendarDateDue(data,row){
    if(data=="0000-00-00 00:00:00"){
        return "";
    } else{
    
        var date_ret = new Date(row.date_returned);
        var due = new Date(data);
        
        if (date_ret <= due) {
            return moment(data).format("MM/DD/YYYY");
        } else {
            return moment(data).format("MM/DD/YYYY").fontcolor( "red" );
        }
        
    }
  
}
function formatCalendarDateReturn(data,row){
    if(data=="0000-00-00 00:00:00"){
        return "";
    } else {
        var date_ret = new Date(data);
        var due = new Date(row.date_due);

        if(date_ret <= due){
            return moment(data).format("MM/DD/YYYY");
        }else{
            return moment(data).format("MM/DD/YYYY").fontcolor( "red" );
        }
        
    }
  
}

function itemDatatableActions($id){
	if($id){
		var _actionButton ="";

        if(jQuery.inArray('undo_return', _currentActions) !== -1){
            _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' title='Undo' onclick='undo_return("+$id+")'><i class='la la-mail-reply'></i></button>";
        }else{
            _actionButton += ' --- ';
        }
           				
		return _actionButton;
	}else{ return false; }
}
//custom global search init
$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    tblBorrowing.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
    tblBorrowing.ajax.reload();
});

$("#undo_return").hide();

function undo_return($id)
{
    $('#undo_return').modal('show');
    $.validate({
        form : '#undo_return_form',
        lang: 'en',
        onSuccess : function(form) {
                $.ajax({
                    url: baseUrl("eforms/borrowing/undo_return/") + $id,
                    type: "POST",
                    dataType: "json",
                    data: $("#undo_return_form").find("input,textarea").serialize(),
                    beforeSend: function(){
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function(data){
                        if(data){
                            $('#undo_return').modal('hide');
                            $('#undo_return_form')[0].reset();
                            tblBorrowing.ajax.reload();
                            toastr.success(data.toastr_msg, "Updated successfully!", 5000);
                        }else{
                            toastr.error(data.toastr_msg, "Error!", 5000);
                        }
                        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                });
            return false;
        },
    });
}

$(document).ready(function () {
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': {delay: 100},
        filters: [
            // {id: 'a.id', label: 'ID #', type: 'integer'},
            {id: 'c.reference_no', label: 'Reference #', type: 'string'},
            {id: 'firstname', label: 'Firstname', type: 'string'},
            {id: 'middlename', label: 'Middlename', type: 'string'},
            {id: 'lastname', label: 'Lastname', type: 'string'},
            {id: 'suffix', label: 'Suffix', type: 'string'},
            {id: 'company', label: 'Company', type: 'string'},
            {id: 'asset_code', label: 'Asset Code', type: 'string', operators : ['contains', 'not_contains', 'begins_with','not_begins_with', 'is_empty', 'is_not_empty']},
            {id: 'asset_name', label: 'Asset Name', type: 'string', operators : ['contains', 'not_contains', 'begins_with','not_begins_with', 'is_empty', 'is_not_empty']},
            {
                id: 'date_borrowed',
                label: 'Date Borrowed',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: {format: 'yyyy-mm-dd'},
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal','between', 'not_between']
            },
            {
                id: 'date_due',
                label: 'Date Due',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: {format: 'yyyy-mm-dd'},
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal','between', 'not_between']
            },
            {
                id: 'date_returned',
                label: 'Date Returned',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: {format: 'yyyy-mm-dd'},
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal','between', 'not_between']
            },
            {id: 'return_remarks', label: 'Return Remarks', type: 'string', operators : ['contains', 'not_contains', 'begins_with','not_begins_with', 'is_empty', 'is_not_empty']},
        ],
    });

    $("#query-builder_group_0").addClass("col-12");
});

$('#query-builder-btn').on('click', function() {
    var result = $('#query-builder').queryBuilder('getSQL');

    if (!$.isEmptyObject(result)) {
        query_builder = result;
        tblBorrowing.ajax.reload();
        $("#modal-query-builder").modal("hide");
    }
});

function clear_query_builder(){
    $('#query-builder').queryBuilder('reset');
    query_builder = null;
    tblBorrowing.ajax.reload();
}

