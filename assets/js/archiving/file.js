var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
      sURLVariables = sPageURL.split("&"),
      sParameterName,
      i;
  
    for (i = 0; i < sURLVariables.length; i++) {
      sParameterName = sURLVariables[i].split("=");
      if (sParameterName[0] === sParam) {
        return sParameterName[1] === undefined ? true : sParameterName[1];
      }
    }
  };
  
  param_id = getUrlParameter("id");
var search_val = "";
var tblFile = $("#table-file").DataTable({
    dom: '<"toolbar">rtlip',
	serverSide: true,
    processing: true,
    ajax: {
		url: baseUrl("archiving/get_file_collection/")+ param_id,
		type: "post",
        dataType: "json",
       data: function(d){
            d.csrf_token = _csrf_hash,
            d.search['value'] = search_val
        }
    },
    searching: true,
    columns: [
       
        { data: "filename", width: "20%"},
        { data: "description", width: "20%"},
        { data: "tag1", width: "35%", render: function (data) {return formatTag(data)}},       
        { data: "modify_dt", width: "10%"},
        { data: null, width: "5%", className: "text-center"},
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
$("#tag_id").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
      url: baseUrl("archiving/get_tag"),
      processResults: function (data) {
        return data;
      }
      
    }
  });
 
function formatTag(data){
    var s1 = data;
    var s2 = s1.substr(1);
        return s2;
    
  
}


function itemDatatableActions($id){
	if($id){
   
              
    var _actionButton ="";
    _actionButton +="<span style='overflow: visible; width: 110px;'>";
    _actionButton +="<div class='dropdown'>";
    _actionButton +="<a href='#' class='btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' data-toggle='dropdown'>";
    _actionButton +="<i class='la la-ellipsis-h'></i>";
    _actionButton +="</a>";
    _actionButton +="<div class='dropdown-menu dropdown-menu-right'>";
    _actionButton +="<a class='dropdown-item' onclick='view_file("+$id+")'><i class='la la-download'></i>Download</a>";
    _actionButton +="<a class='dropdown-item' onclick='edit_file("+$id+")'><i class='la la-pencil-square'></i>Edit</a>";
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
    tblFile.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
    tblFile.ajax.reload();
});
function open_delete($id) 
{
  $('[name="delete_id"]').val($id);
  $('#modal_form_delete').modal('show'); // show bootstrap modal
  $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}
function open_file() 
              {
                var x = document.getElementById("tag_id");
                x.remove(x.selectedIndex);
              save_method = 'add';
                $('#form_document')[0].reset(); // reset form on modals
                $('[name="filename"]').val("");
      $('[name="doc_filename"]').val("");
                
                $('#modal_form_document').modal('show'); // show bootstrap modal
                $('.modal-title').text('Add New File'); // Set Title to Bootstrap modal title
              }
              
              function edit_file(id)
{
  
	save_method = 'update';
	$('#form_document')[0].reset();
	$.ajax({
	  url : baseUrl("archiving/edit_file/") + id,
	  type: "GET",
	  dataType: "JSON",
	  success: function(data)
	  {         
		  $('[name="id"]').val(data.id);
      $('[name="description"]').val(data.description);
      $('[name="tag_temp"]').val(data.tag1);
      $('[name="filename"]').val(data.filename);
      $('[name="doc_filename"]').val("*"+data.filename);
      var x = document.getElementById("tag_id");
      x.remove(x.selectedIndex);
     
      document.getElementById('tag_text').style.removeProperty( 'display' )
               
                      $('#modal_form_document').modal('show'); // show bootstrap modal
  $('.modal-title').text('Edit File'); // Set Title to Bootstrap modal title
	  },
	  error: function (jqXHR, textStatus, errorThrown)
	  {
		  alert('Error get data from ajax');
	  }
	});
}
function delete_file()
{
  $temp= $('[name="delete_id"]').val();
    // ajax delete data to database
      $.ajax({
        url : baseUrl("archiving/delete_file/") + $temp,
        type: "POST",
        dataType: "JSON",
        data:  { csrf_token: _csrf_hash },
        success: function(data)
        {
          tblFile.ajax.reload();
          $('#modal_form_delete').modal('hide');
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
        }
    });
     
  
}
              function view_file($id)
              {
                  save_method = 'update';
                  $.ajax({
                    url : baseUrl("archiving/edit_document/") + param_id,
                     type: "GET",
                     dataType: "JSON",
                     success: function(data2)
                     {         
                  //Ajax Load data from ajax
                  $.ajax({
                    url : baseUrl("archiving/edit_file/") + $id,
                    type: "GET",
                    dataType: "JSON",
                    success: function(data)
                    {         
                      
                        window.location.replace(baseUrl("uploads/module/archiving/")+data2.reference+"/files/"+data.filename);
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        alert('Error get data from ajax');
                    }
                  });
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                  alert('Error get data from ajax');
                }
              });
              }   
              
              
                   function save_file(){
                    var url;
                    if($('[name="tag_temp"]').val()!=null){
                      temp= $('[name="tag_temp"]').val();
                     }
                     
                     if($('[name="tag_id"]').val()!=null){
                     $.each($('[name="tag_id"]').val(), function( index, value ) {
                      if(temp==null){
                        temp=value;
                      }
                      else{
                        temp=temp+","+value;
                      }
      
                     });
                     $('[name="tag_temp"]').val(temp);
                   }
                  
                
               
             if(save_method=="add"){
              url = baseUrl("archiving/add_file/")+ param_id;
             }else{
              url = baseUrl("archiving/update_file/")+$('[name="id"]').val();
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
                                            $('#modal_form_document').modal('hide');
                                            tblFile.ajax.reload();
                                             
                                          }else{
                                               alert('Error get data from ajax');
                                          }
                                         
                                      }
                                  });
                              return false;
                          },
                      });
                   
                       }
            