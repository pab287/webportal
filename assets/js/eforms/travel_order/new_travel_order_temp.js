var file_under = $("#select2_file").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/Travel_order/get_company_collection"),
    dataType: "json",
    global: false,
    delay: 500,
    processResults: function (data) {
      return data;
    }
  }
});

var department = $("#select2_dep").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
      url: baseUrl("eforms/Travel_order/get_department_collection"),
      dataType: "json",
      global: false,
      delay: 500,
      processResults: function (data) {
        return data;
      }
    }
});

var vehicle = $("#select2_vehicle").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/Travel_order/get_vehicle_collection"),
    dataType: "json",
    global: false,
    delay: 500,
    processResults: function (data) {
      return data;
    }
  }
});

var emp = $("#select2_emp").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/Travel_order/get_request_collection"),
    dataType: "json",
    global: false,
    delay: 500,
    processResults: function (data) {
      return data;
    }
  }
});

var req = $("#select2_req").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/Travel_order/get_request_collection"),
    dataType: "json",
    global: false,
    delay: 500,
    processResults: function (data) {
      return data;
    }
  }
});

var driver = $("#driver").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/Travel_order/driver"),
    dataType: "json",
    global: false,
    delay: 500,
    done: function (data) {
      return data;
    }
  }
});

var dt_from = $('#date_from').datetimepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    format: 'yyyy/mm/dd hh:ii',
});

var dt_to = $('#date_to').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'yyyy/mm/dd hh:ii',
 
});

  var search_val = "";
  var check="0";
  var check2="0";

var tblPersonnel = $("#table-personnel").DataTable({
  dom: '<"toolbar">rt',
  serverSide: true,
  processing: true,
  aaSorting: [],
    ajax: {
    url: baseUrl("eforms/Travel_order/get_temp_personnel/"),
    type: "post",
        dataType: "json",
      data: function(d){
            d.csrf_token = _csrf_hash,
            d.search['value'] = search_val
        }
    },
    searching: true,
    columns: [
      { data: "firstname", width: "40%", render: function (data, type, row, meta) {return displayName(row.display_name)}},
        { data: "position", width: "40%"},
        { data: null, width: "20%", className: "text-center"},
    ],
    columnDefs: [{
          data: null,
          defaultContent: "",
          targets: -1,
          orderable: false,
          render: function ( data, type, row, meta ) { return itemDatatableActions(row.id); },
        }
     ]
});

function displayName($displayName){
  return $displayName;
}


 function itemDatatableActions($id){
   if($id){
     check="1";
     var _actionButton ="";
       _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_personnel("+$id+")'><i class='la la-pencil-square'></i></button>"; 
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' onclick='open_delete("+$id+")'><i class='la la-trash'></i></button>";       
     return _actionButton;
   }else{ return false; }
 } 

 function open_delete($id) 
{
  $('[name="delete_id"]').val($id);
  $('#modal_form_delete').modal('show'); // show bootstrap modal
  $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}
 var tblDestination = $("#table-destination").DataTable({
  dom: '<"toolbar">rt',
serverSide: true,
  processing: true,
  aaSorting: [],
  ajax: {
  url: baseUrl("eforms/Travel_order/get_temp_destination/"),
  type: "post",
      dataType: "json",
     data: function(d){
          d.csrf_token = _csrf_hash,
          d.search['value'] = search_val
          
      }
  },
  searching: true,
  columns: [
      { data: "destination", width: "45%", render: function ( data, type, row, meta ) {return specialInstruction(row.destination, row.instructions)}},
      { data: "date_from", width: "40%", render: function ( data, type, row, meta ) {return formatCalendarDate(data,row)}},
      { data: null, width: "15%", className: "text-center"},
  ],
  columnDefs: [
     
      {
          data: null,
          defaultContent: "",
          targets: -1,
          orderable: false,
        
          render: function ( data, type, row, meta ) { return itemDatatableActions2(row.id); },
      }
     
    
  ]
});

function specialInstruction(destination, instruction){
  if(instruction){
    return destination+"<br>Special Instruction: "+instruction;
  }else{
    return destination;
  }
}

function itemDatatableActions2($id){
if($id){
  check2="1";
  var _actionButton ="";
    _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_destination("+$id+")'><i class='la la-pencil-square'></i></button>"; 
     _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' onclick='open_delete2("+$id+")'><i class='la la-trash'></i></button>";       
  return _actionButton;
}else{ return false; }
} 

function formatCalendarDate(data,row){
  if(data=="0000-00-00 00:00:00"){
      return "";
  }else{
      return moment(data).format("MM/DD/YYYY hh:mm A")+" - "+moment(row.date_to).format("MM/DD/YYYY hh:mm A");
  }
}

function open_delete2($id) 
{
  $('[name="delete_id2"]').val($id);
  $('#modal_form_delete2').modal('show'); // show bootstrap modal
  $('.modal-title').text('Delete'); // Set Title to Bootstrap modal title
}

/*
function veh_details(){
  var veh = $('[name="vehicle"]').val();
  $.ajax({
      url: baseUrl('eforms/travel_order/ajax_vehicle_details/') + veh,
      type: "POST",
      dataType: "JSON",
      data:  { csrf_token: _csrf_hash },
      success: function(data){
       
          $('[name="driver"]').val(data.driver);
      }, error: function (jqXHR, textStatus, errorThrown){
          alert('Error: "ajax_vehicle_details"');
      }
  });
  }*/

function add_destination(){
  save_method = 'add';
  $('#form_destination')[0].reset();
  $('#modal_form_destination').modal('show'); 
  $('.modal-title').text('Add New Destination'); 
  $("#modal_form_destination #select2_req").text("").trigger("change");
  $("#modal_form_destination #select2_req").val("").trigger("change");
}

function edit_destination(id){   
  save_method = 'update';
  $('#form_destination')[0].reset();
    $.ajax({
      url : baseUrl("eforms/travel_order/edit_temp_destination/") + id,
      type: "GET",
      dataType: "JSON",
      success: function(data)
      {         
          $('[name="id_destination"]').val(data.id);
          $('[name="from"]').val(data.des_from);
          $('[name="to"]').val(data.des_to);
          var newOption = new Option(data.name, data.requested_by, true, true);
          $('#select2_req').append(newOption).trigger('change');
          $('[name="purpose"]').val(data.purpose);
          $('[name="date_from"]').val(data.date_from);
          $('[name="date_to"]').val(data.date_to);
          $('[name="special"]').val(data.instructions);
          $('[name="remarks"]').val(data.remarks);
          $('#modal_form_destination').modal('show'); // show bootstrap modal
  $('.modal-title').text('Edit Destination'); // Set Title to Bootstrap modal title
      },
      error: function (jqXHR, textStatus, errorThrown)
      {
        alert('Error get data from ajax');
      }
    });
}

function delete_destination(){
  $temp= $('[name="delete_id2"]').val();
    $.ajax({
      url : baseUrl("eforms/travel_order/delete_temp_destination/") + $temp,
      type: "POST",
      dataType: "JSON",
      data:  { csrf_token: _csrf_hash },
      success: function(data){
        tblDestination.ajax.reload();
        toastr.success(data.toastr_msg, "Removed content successfully!", 5000);
        $("#modal_form_delete2").modal("hide");
      },
      error: function (jqXHR, textStatus, errorThrown){
        alert('Error adding / update data');
      }
  });
}

function clear_destination(){
  $('#clear_modal').modal('show'); 
  $('.modal-title').text('Clear');
  $.validate({
    form : '#clear_form',
    lang: 'en',
    onSuccess : function(form) {
            $.ajax({
                url: baseUrl("eforms/travel_order/delete_temp_all_destination"),
                type: "POST",
                dataType: "json",
                data: $("#clear_form").find("input,select,textarea").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    $("#clear_modal").modal("hide");
                    toastr.success(data.toastr_msg, "Removed contents successfully!", 5000);
                    tblDestination.ajax.reload();
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        return false;
      },
  });
}

function save_destination(){
  var url;

  req.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });  

  dt_from.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });  

  dt_to.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  }); 

  if(save_method == 'add') {
    url = baseUrl("eforms/travel_order/add_test_temp_destination/");
  }
  else{
    url = baseUrl("eforms/travel_order/update_temp_destination/");
  }
              
  $.validate({
    form : '#form_destination',
    lang: 'en',
    onSuccess : function(form) {
      $.ajax({
          url : url,
          type: "POST",
          data: $('#form_destination').serialize(),
          dataType: "JSON",
          success: function(data){
              console.log(data);
              // if(data.status){ 
              //   if(save_method == 'add') {
              //     toastr.success(data.toastr_msg, "Destination added successfully!", 5000);
              //   }
              //   else{
              //     toastr.success(data.toastr_msg, "Destination updated successfully!", 5000);
              //   }

              //   $("#modal_form_destination #select2_req").text("").trigger("change");
              //   $("#modal_form_destination #select2_req").val("").trigger("change");
              //   tblDestination.ajax.reload();
              //   $("#modal_form_destination").modal("hide");
              // }else{
              //       alert('Error get data from ajax');
              // }
          }
      });
      return false;
    },
  });
}

function add_personnel() {
  save_method = 'add';
  $('#form_personnel')[0].reset();
  $('#modal_form_personnel').modal('show'); 
  $('.modal-title').text('Add New Personnel');
  $('#modal_form_personnel #select2_emp').text('').trigger('change');
  $('#modal_form_personnel #select2_emp').val('').trigger('change');
}

function edit_personnel(id){
  save_method = 'update';
  $('#form_personnel')[0].reset();
  $.ajax({
    url : baseUrl("eforms/travel_order/edit_temp_personnel/") + id,
    type: "GET",
    dataType: "JSON",
    success: function(data){         
        $('[name="id_personnel"]').val(data.id);
        var newOption = new Option(data.name, data.employee_id, true, true);
        $('#select2_emp').append(newOption).trigger('change');
        $('#modal_form_personnel').modal('show'); // show bootstrap modal
        $('.modal-title').text('Edit Content'); // Set Title to Bootstrap modal title
    },
    error: function (jqXHR, textStatus, errorThrown){
        alert('Error get data from ajax');
    }
  });
}

function delete_personnel(){
  $temp= $('[name="delete_id"]').val();
  $.ajax({
      url : baseUrl("eforms/travel_order/delete_temp_personnel/") + $temp,
      type: "POST",
      dataType: "JSON",
      data:  { csrf_token: _csrf_hash },
      success: function(data){
          tblPersonnel.ajax.reload();
          toastr.success(data.toastr_msg, "Removed personnel successfully!", 5000)
          $("#modal_form_delete").modal("hide");
      },
      error: function (jqXHR, textStatus, errorThrown){
          alert('Error adding / update data');
      }
  });
}

$("#clear_modal").hide();
function clear_personnel(){
  $('#clear_modal').modal('show'); 
  $('.modal-title').text('Clear');
  $.validate({
    form : '#clear_form',
    lang: 'en',
    onSuccess : function(form) {
            $.ajax({
                url: baseUrl("eforms/travel_order/delete_temp_all_personnel"),
                type: "POST",
                dataType: "json",
                data: $("#clear_form").find("input,select,textarea").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    $("#clear_modal").modal("hide");
                    toastr.success(data.toastr_msg, "Removed contents successfully!", 5000);
                    tblPersonnel.ajax.reload();
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        return false;
      },
  });
}

function save_personnel(){
  var url;       

  if(save_method == 'add') {
      url = baseUrl("eforms/travel_order/add_test_temp_destination/");
  }else{
    url = baseUrl("eforms/travel_order/update_temp_personnel/");
  }
                
  $.validate({
  form : '#form_personnel',
  lang: 'en',
  onSuccess : function(form) {
    if($('#modal_form_personnel #select2_emp').text() != ""){
      $.ajax({
        url : url,
        type: "POST",
        data: $('#form_personnel').serialize(),
        dataType: "JSON",
        success: function(data){
          tblPersonnel.ajax.reload();
          $('#modal_form_personnel #select2_emp').text("").trigger('change');
          $('#modal_form_personnel #select2_emp').val("").trigger('change');
          if(save_method == 'add') {
            if(data.message){
              toastr.warning(data.message, "Warning: ", 5000);
            }else{
              toastr.success(data.message, "Personnel added successfully!", 5000);
            }
          }
          else{
            if(data.message){
              toastr.warning(data.message, "Warning: ", 5000);
            }else{
              toastr.success(data.message, "Personnel updated successfully!", 5000);
              $("#modal_form_personnel").modal("hide");
            }
          }
        }
      });
    }else{
      toastr.error("", "Personnel field is required!", 5000);
    }
      return false;
    },
  });
}

function add_travel_order(){
  var url;
  url = baseUrl("eforms/travel_order/add_travel_order/");
  file_under.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });  

  department.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });  

  vehicle.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });  

  driver.on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });  

  if(check=="0"){
    $('#table_v').empty(); 
    $('#table_v').append('<p><font color="#FF0000">Required. Add atleast 1 Content</font></p>');  
  } 
  if(check2=="0"){
    $('#table2_v').empty(); 
      $('#table2_v').append('<p><font color="#FF0000">Required. Add atleast 1 Content</font></p>');  
  }            
                
  $.validate({
    form : '#form_travel_order',
    lang: 'en',
    onSuccess : function(form) {
    if(check=="1"&&check2=="1") {
      $('#table_v').empty(); 
      $('#table2_v').empty();
          $.ajax({
            url : url,
            type: "POST",
            data: $('#form_travel_order').serialize(),
            dataType: "JSON",
            success: function(data){
                if(data.status){
                  window.location.replace(baseUrl("eforms/travel_order/masterfile"));
                    
                }else{
                      alert('Error get data from ajax');
                }              
              }
          });
        }
        return false;
      },
  });
}

function change_service(){
  radiobtn = document.getElementById("commute");
  radiobtn.checked = false;
  radiobtn = document.getElementById("personal");
  radiobtn.checked = false;
  radiobtn = document.getElementById("other");
  radiobtn.checked = false;
  
    document.getElementById('other_remark').style.display = 'none';
    document.getElementById('service_veh').style.removeProperty( 'display' );
    $("#service_driver").show();
    //document.getElementById('service_driver').style.removeProperty( 'display' );
  }
  function change_commute(){
    radiobtn = document.getElementById("service");
    radiobtn.checked = false;
    radiobtn = document.getElementById("personal");
    radiobtn.checked = false;
    radiobtn = document.getElementById("other");
    radiobtn.checked = false;
    
      document.getElementById('other_remark').style.display = 'none';
      document.getElementById('service_veh').style.display = 'none';
      $("#service_driver").hide();
      //document.getElementById('service_driver').style.display = 'none';
    }
    function change_personal(){
      radiobtn = document.getElementById("service");
      radiobtn.checked = false;
      radiobtn = document.getElementById("commute");
  radiobtn.checked = false;
      radiobtn = document.getElementById("other");
      radiobtn.checked = false;
      
        document.getElementById('other_remark').style.display = 'none';
        document.getElementById('service_veh').style.display = 'none';
        //document.getElementById('service_driver').style.display = 'none';
        $("#service_driver").hide();
      }
      function change_other(){
        radiobtn = document.getElementById("service");
        radiobtn.checked = false;
        radiobtn = document.getElementById("commute");
    radiobtn.checked = false;
    radiobtn = document.getElementById("personal");
    radiobtn.checked = false;
        
          document.getElementById('other_remark').style.removeProperty( 'display' );
          document.getElementById('service_veh').style.display = 'none';
         // document.getElementById('service_driver').style.display = 'none';
          $("#service_driver").hide();
        }
$(document).ready(function() {
  
  document.getElementById('other_remark').style.display = 'none';
  radiobtn = document.getElementById("service");
  radiobtn.checked = true;
  });

  // map starts here
  