var search_val = "";
var tblResume = $("#table-resume").DataTable({
    dom: '<"toolbar">rtlip',
	serverSide: true,
    processing: true,
    ajax: {
		url: baseUrl("crs/get_blacklist_collection/"),
    type: "post",
    global: false,
        dataType: "json",
       data: function(d){
            d.csrf_token = _csrf_hash,
            d.search['value'] = search_val
        }
    },
    searching: true,
    columns: [
       
        { data: "name", width: "15%"},
        { data: "school", width: "10%", render: function (data) {return formatTag(data)}},
        { data: "course", width: "10%", render: function (data) {return formatTag(data)}},
        { data: "position", width: "10%", render: function (data) {return formatTag(data)}},
        { data: "status", width: "10%"},
        { data: "tag1", width: "20%", render: function (data) {return formatTag(data)}},
        { data: "recruitment", width: "10%"},
        { data: "applied_dt", width: "10%"},
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
    url: baseUrl("crs/get_tag"),
    processResults: function (data) {
      return data;
    }
  }
});
$("#school_id").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
    url: baseUrl("crs/get_school"),
    processResults: function (data) {
      return data;
    }
  }
});
$("#course_id").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
    url: baseUrl("crs/get_course"),
    processResults: function (data) {
      return data;
    }
  }
});
$("#position_id").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
    url: baseUrl("crs/get_position"),
    processResults: function (data) {
      return data;
    }
  }
});
$('#applied_dt').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'yyyy/mm/dd hh:ii',
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
    _actionButton +="<a class='dropdown-item' onclick='view_resume("+$id+")'><i class='la la-eye'></i>View</a>";
    _actionButton +="<a class='dropdown-item' onclick='edit_resume("+$id+")'><i class='la la-pencil-square'></i>Edit</a>";
    _actionButton +="<a class='dropdown-item' onclick='archive_resume("+$id+")'><i class='la la-folder'></i>Archive</a>";
    _actionButton +="<a class='dropdown-item' onclick='open_delete("+$id+")'><i class='la la-trash'></i>Delete</a>";
    _actionButton +=" </div>";
    _actionButton +="</div>";
    _actionButton +="</span>";
    // _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill btnViewItem' onclick='view_resume("+$id+")'><i class='la la-eye'></i></button>";
    // _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_resume("+$id+")'><i class='la la-pencil-square'></i></button>";
    // _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' onclick='archive_resume("+$id+")'><i class='la la-folder'></i></button>";  
		// _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' onclick='delete_resume("+$id+")'><i class='la la-trash'></i></button>"; 				
		return _actionButton;
	}else{ return false; }
}
//custom global search init
$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    tblResume.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
    tblResume.ajax.reload();
});
function open_delete($id) 
{
  $('[name="delete_id"]').val($id);
  $('#modal_form_delete').modal('show'); // show bootstrap modal
  $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}
function archive_resume($id)
              {
                if(confirm('Are you sure you want to archive resume?'))
                {
                  // ajax delete data to database
                    $.ajax({
                      url : baseUrl("crs/archive_resume/") + $id,
                      type: "POST",
                      dataType: "JSON",
                      data:  { csrf_token: _csrf_hash },
                      success: function(data)
                      {
                        tblResume.ajax.reload();
                        
                      },
                      error: function (jqXHR, textStatus, errorThrown)
                      {
                          alert('Error adding / update data');
                      }
                  });
                   
                }
              }
              function view_resume($id)
              {
                  save_method = 'update';

                  //Ajax Load data from ajax
                  $.ajax({
                    url : baseUrl("crs/edit_resume/") + $id,
                    type: "GET",
                    dataType: "JSON",
                    success: function(data)
                    {         
                      
                        window.location.replace(baseUrl("uploads/module/crs/files/")+data.filename);
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        alert('Error get data from ajax');
                    }
                  });
              }   
              function edit_resume(id)
              {
                
                save_method = 'update';
                $('#form_document')[0].reset();
                $.ajax({
                  url : baseUrl("crs/edit_resume/") + id,
                  type: "GET",
                  dataType: "JSON",
                  success: function(data)
                  {         
                    $('[name="id"]').val(data.id);
                    $('[name="firstname"]').val(data.firstname);
                    $('[name="lastname"]').val(data.lastname);
                    $('[name="recruitment"]').val(data.recruitment).trigger('change');
                    $('[name="referral"]').val(data.referral);
                    $('[name="status"]').val(data.status).trigger('change');
                    $('[name="applied_dt"]').val(data.applied_dt);
                    $('[name="description2"]').val(data.description);
                    $('[name="tag_temp"]').val(data.tag1);
                    $('[name="school_temp"]').val(data.school);
                    $('[name="course_temp"]').val(data.course);
                    $('[name="position_temp"]').val(data.position);
                    $('[name="filename"]').val(data.filename);
                    $('[name="doc_filename"]').val(data.filename);
                    var x = document.getElementById("tag_id");
                    x.remove(x.selectedIndex);
                    x = document.getElementById("school_id");
                    x.remove(x.selectedIndex);
                    x = document.getElementById("course_id");
                    x.remove(x.selectedIndex);
                    x = document.getElementById("position_id");
                    x.remove(x.selectedIndex);
                    document.getElementById('tag_text').style.removeProperty( 'display' )
                              document.getElementById('school_text').style.removeProperty( 'display' )
                              document.getElementById('course_text').style.removeProperty( 'display' )
                              document.getElementById('position_text').style.removeProperty( 'display' )
                                    $('#modal_form_document').modal('show'); // show bootstrap modal
                $('.modal-title').text('Edit Resume'); // Set Title to Bootstrap modal title
                  },
                  error: function (jqXHR, textStatus, errorThrown)
                  {
                    alert('Error get data from ajax');
                  }
                });
              } 
              function delete_resume()
              {
                $temp= $('[name="delete_id"]').val();
                  // ajax delete data to database
                    $.ajax({
                      url : baseUrl("crs/delete_resume/") + $temp,
                      type: "POST",
                      dataType: "JSON",
                      data:  { csrf_token: _csrf_hash },
                      success: function(data)
                      {
                        tblResume.ajax.reload();
                        $('#modal_form_delete').modal('hide');
                      },
                      error: function (jqXHR, textStatus, errorThrown)
                      {
                          alert('Error adding / update data');
                      }
                  });
                   
                
              }
              function show(){
                if($('[name="recruitment"]').val() == "REFERRAL"){
                    document.getElementById('referral_text').style.removeProperty( 'display' );
                }else{
                    document.getElementById('referral_text').style.display = 'none'; 
                }
                   }
                   function save_document(){
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
                   if($('[name="school_temp"]').val()!=null){
                    temp= $('[name="school_temp"]').val();
                   }
                   
                   if($('[name="school_id"]').val()!=null){
                   $.each($('[name="school_id"]').val(), function( index, value ) {
                    if(temp==null){
                      temp=value;
                    }
                    else{
                      temp=temp+","+value;
                    }
    
                   });
                   $('[name="school_temp"]').val(temp);
                 }
                 if($('[name="course_temp"]').val()!=null){
                  temp= $('[name="course_temp"]').val();
                 }
                 
                 if($('[name="course_id"]').val()!=null){
                 $.each($('[name="course_id"]').val(), function( index, value ) {
                  if(temp==null){
                    temp=value;
                  }
                  else{
                    temp=temp+","+value;
                  }
  
                 });
                 $('[name="course_temp"]').val(temp);
               }
               if($('[name="position_temp"]').val()!=null){
                temp= $('[name="position_temp"]').val();
               }
               
               if($('[name="position_id"]').val()!=null){
               $.each($('[name="position_id"]').val(), function( index, value ) {
                if(temp==null){
                  temp=value;
                }
                else{
                  temp=temp+","+value;
                }

               });
               $('[name="position_temp"]').val(temp);
             }
             if(save_method=="add"){
              url = baseUrl("crs/add_resume/");
             }else{
              url = baseUrl("crs/update_resume/")+$('[name="id"]').val();
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
                                            tblResume.ajax.reload();
                                             
                                          }else{
                                               alert('Error get data from ajax');
                                          }
                                         
                                      }
                                  });
                              return false;
                          },
                      });
                   
                       }
              $(document).ready(function() {
                // document.getElementById('tag_text').style.display = 'none';
                // document.getElementById('school_text').style.display = 'none';
                // document.getElementById('course_text').style.display = 'none';
                // document.getElementById('position_text').style.display = 'none';
                document.getElementById('referral_text').style.display = 'none';
                });