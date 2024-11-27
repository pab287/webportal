var getUrlParameter = function getUrlParameter(sParam){
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
        for (i = 0; i < sURLVariables.length; i++){
                sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] === sParam){
                    return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
  };
  
param_id = getUrlParameter('id');
var search_val = "";
var tbl = $("#table-tickets").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
    processing: true,
    ajax: {
		url: baseUrl("ts/ticketing/archived_list/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function(d){
			d.csrf_token = _csrf_hash,
			d.search['value'] = search_val
		}
    },
    searching: false,
    columns: [
        { data: "id"},
        { data: "type"},
        { data: "issue"},
        { data: "status",render: function (data) {return renderStatusHtml(data)}},
        { data: "need_dt"},
        { data: "name"},
        { data: "performer", defaultContent: "<i>Not set</i>", width: "15%"},
        { data: null, width: "10%", className: "text-center"},
    ],
    columnDefs: [
        {
            targets: [5], width: "15%",
        },
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function ( data, type, row, meta ) { return itemDatatableActions(row.id, row.status); },
        }
    ]
});

function formatCalendarDate(data){
    if(data=="0000-00-00 00:00:00"){
        return "";
    }
    else{
        return moment(data).format("MM/DD/YYYY");
    }
  
}

function itemDatatableActions($id, $status){
    var _actionButton ="";
    if($status=="Open"){
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><a href='"+baseUrl('ts/ticketing/edit_ticket?id=')+$id+"'><i class='la la-pencil-square'></i></a></button>";
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><a href='"+baseUrl('ts/ticketing/service?id=')+$id+"'><i class='la la-wrench'></i></a></button>"
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='deleteR("+$id+")'><a><i class='la la-trash'></i></a></button>"
        return _actionButton;
    }else if($status=="Inprogress"){
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><a href='"+baseUrl('ts/ticketing/edit_ticket?id=')+$id+"'><i class='la la-pencil-square'></i></a></button>";
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><a href='"+baseUrl('ts/ticketing/service?id=')+$id+"'><i class='la la-wrench'></i></a></button>"
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='deleteR("+$id+")'><a><i class='la la-trash'></i></a></button>"		
        return _actionButton;
    }else if($status=="Onhold"){

    }else if($status=="Closed"){
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><a href='"+baseUrl('ts/ticketing/confirm?id=')+$id+"'><i class='la la-check'></i></a></button>";
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><a href='"+baseUrl('ts/ticketing/service?id=')+$id+"'><i class='la la-wrench'></i></a></button>"
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='deleteR("+$id+")'><a><i class='la la-trash'></i></a></button>"				
        return _actionButton;
    }else if($status=="Confirmed"){
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><a href='"+baseUrl('ts/ticketing/view_ticket?id=')+$id+"'><i class='fa fa-eye'></i></a></button>";
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='deleteR("+$id+")'><a><i class='la la-trash'></i></a></button>"			
        return _actionButton;
    }else{ 
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='deleteR("+$id+")'><a><i class='la la-trash'></i></a></button>"
        return _actionButton; 
    }
}

function renderStatusHtml(data){
    switch(data){
        case "Inprogress":
            return '<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Inprogress</strong></div>';
        break;
        case "Confirmed":
            return '<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Resolved</strong></div>';
        break;
        case "Closed":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>Completed</strong></div>';
        break;
        case "Open":
            return '<div class="m-badge m-badge--warning m-badge--wide" role="alert"><strong>Open</strong></div>';
        break;
        case "Onhold":
            return '<div class="m-badge m-badge--warning m-badge--wide" role="alert"><strong>Onhold</strong></div>';
        break;
        default:
            return '<div class="m-badge m-badge--default m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
        break;
    }
}

//custom global search init
$('#generalSearch').donetyping(function(callback) {
	search_val = $(this).val();
    tbl.ajax.reload();
    console.log(search_val);
});

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
	tbl.ajax.reload();
});

function deleteR($id){

            $.ajax({
                url: baseUrl("ts/ticketing/delete_ticket/") + $id,
                type: 'GET',
                dataType: "json",
                success: function() {
                    tbl.ajax.reload();
                }     
            });
    
}