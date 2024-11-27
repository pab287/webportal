$("#select2_class").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
      url: baseUrl("archiving/get_classification"),
      processResults: function (data) {
        return data;
      }
      
    }
  });
  $("#select2_department").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
      url: baseUrl("archiving/get_department"),
      processResults: function (data) {
        return data;
      }
      
    }
  });
  $('#document_dt').datetimepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    format: 'yyyy/mm/dd hh:ii:ss',
});
var search_val = "";
var type="agg";
var tblDocument = $("#table-document").DataTable({
    dom: '<"toolbar">rtlip',
	serverSide: true,
    processing: true,
    ajax: {
		url: baseUrl("archiving/get_document_collection/")+type,
		type: "post",
        dataType: "json",
       data: function(d){
            d.csrf_token = _csrf_hash,
            d.search['value'] = search_val
        }
    },
    searching: true,
    columns: [
       
        { data: "reference", width: "20%"},
        { data: "classification", width: "20%"},
        { data: "department", width: "20%"},
        { data: "description", width: "20%"},
        { data: "document_dt", width: "10%"},
        { data: null, width: "10%", className: "text-center"},
    ],
    columnDefs: [
        
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
          
            render: function ( data, type, row, meta ) { return itemDatatableActions(row.id); },
        }
       
      
    ]
});
 


function itemDatatableActions($id){
	if($id){
        var _actionButton ="";
    _actionButton +="<span style='overflow: visible; width: 110px;'>";
    _actionButton +="<div class='dropdown'>";
    _actionButton +="<a href='#' class='btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' data-toggle='dropdown'>";
    _actionButton +="<i class='la la-ellipsis-h'></i>";
    _actionButton +="</a>";
    _actionButton +="<div class='dropdown-menu dropdown-menu-right'>";
    _actionButton +="<a class='dropdown-item' onclick='edit_document("+$id+")'><i class='la la-pencil-square'></i>Edit</a>";
    _actionButton +="<a class='dropdown-item' onclick='open_move_document("+$id+")'><i class='la la-reply'></i>Move</a>";
    _actionButton +="<a class='dropdown-item' onclick='open_delete("+$id+")'><i class='la la-trash'></i>Delete</a>";
    _actionButton +=" </div>";
    _actionButton +="</div>";
    _actionButton +="</span>";			
		return _actionButton;
	}else{ return false; }
}
//custom global search init
$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    tblDocument.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
    tblDocument.ajax.reload();
});
function open_document() 
{
  save_method = 'add';
  document.getElementById('ref').style.removeProperty( 'display' );
  $('#form_document')[0].reset();
  $('#modal_form_document').modal('show'); // show bootstrap modal
  $('.modal-title').text('New Document'); // Set Title to Bootstrap modal title
}

function open_move_document($id) 
{
  $('[name="move_id"]').val($id);
  $('#modal_form_move').modal('show'); // show bootstrap modal
  $('.modal-title').text('Move'); // Set Title to Bootstrap modal title
}
function edit_document(id)
{
  
	save_method = 'update';
	$('#form_document')[0].reset();
	$.ajax({
	  url : baseUrl("archiving/edit_document/") + id,
	  type: "GET",
	  dataType: "JSON",
	  success: function(data)
	  {   
        document.getElementById('ref').style.display = 'none';      
		  $('[name="id"]').val(data.id);
          $('[name="description"]').val(data.description);
          var newOption = new Option(data.classification, data.classification_id, true, true);
          $('#select2_class').append(newOption).trigger('change');
        newOption = new Option(data.department, data.department_id, true, true);
          $('#select2_department').append(newOption).trigger('change');
          $('[name="document_dt"]').val(data.document_dt);
		 $('#modal_form_document').modal('show'); // show bootstrap modal
  $('.modal-title').text('Edit Document'); // Set Title to Bootstrap modal title
	  },
	  error: function (jqXHR, textStatus, errorThrown)
	  {
		  alert('Error get data from ajax');
	  }
	});
}
function delete_document()
{
  $temp= $('[name="delete_id"]').val();
	// ajax delete data to database
	  $.ajax({
		url : baseUrl("archiving/delete_document/") + $temp,
		type: "POST",
		dataType: "JSON",
		data:  { csrf_token: _csrf_hash },
		success: function(data)
		{
		   //if success reload ajax table
		   tblDocument.ajax.reload();
		  $("#modal_form_delete").modal("hide");
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error adding / update data');
		}
	});
	 
  
}
function move_document()
{
  
	
	  $.ajax({
		url : baseUrl("archiving/move_document/"),
		type: "POST",
		dataType: "JSON",
		data: $('#form_move').serialize(),
		success: function(data)
		{
		   //if success reload ajax table
		   tblDocument.ajax.reload();
		  $("#modal_form_move").modal("hide");
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error adding / update data');
		}
	});
	 
  
}
function save_document()
              {
                var url;
               
                if(save_method == 'add') 
                {
                    url = baseUrl("archiving/add_document/")+type;
                }
                else
                {
                  url = baseUrl("archiving/update_document/");
                }

                    
                    $.validate({
    form : '#form_document',
    lang: 'en',
    onSuccess : function(form) {
            $.ajax({
                url : url,
                      type: "POST",
                      data: $('#form_document').serialize(),
                      dataType: "JSON",
               
                success: function(data){
                    if(data.status){
                      
						tblDocument.ajax.reload();
                        $("#modal_form_document").modal("hide");
                    }else{
                         alert('Error get data from ajax');
                    }
                   
                }
            });
        return false;
    },
});
              }