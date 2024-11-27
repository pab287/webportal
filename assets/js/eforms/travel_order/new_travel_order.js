/*** service vehicle option for logistics and administrator role ***/
let vehicle, driver;
const initVehicleSelect2 = function(){
  return $("#select2_vehicle").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    minimumInputLength: 3,
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
}

const initDriverSelect2 = function(){
  return $("#driver").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    minimumInputLength: 3,
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
}

const vmContentOptions = new Vue({
  el: "#content_option",
  data: { allow_service_vehicle: false },
  mounted: function(){
    vehicle = initVehicleSelect2();
    driver = initDriverSelect2();
    $.validate();
    
    vehicle.on("change", function (e) {
      var self = $(e.target);
      self.validate();
    });  

    driver.on("change", function (e) {
      var self = $(e.target);
      self.validate();
    });  
  }
});

if(typeof _tempContentData.allow_service_vehicle !== "undefined"){
  vmContentOptions.allow_service_vehicle = _tempContentData.allow_service_vehicle;
  vmContentOptions.$mount();
}
/*** service vehicle option for logistics and administrator role ***/

Array.prototype.remove = function() {
    this.splice(0,this.length);
    return this;
};

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

vehicle = initVehicleSelect2();
driver = initDriverSelect2();

var emp = $("#select2_emp").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  minimumInputLength: 3,
  dropdownParent: $("#modal_form_personnel"),
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
  minimumInputLength: 3,
  dropdownParent: $("#modal_form_destination"),
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

var dt_from = $('#date_from').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'yyyy/mm/dd hh:ii',
  startDate: getCurrentDate(),
}).on("changeDate", function (e) {
  moment(e.date).format("yyyy/mm/dd hh:ii tt");
  var self = $(e.target);
  self.validate();
});

var dt_to = $('#date_to').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'yyyy/mm/dd hh:ii',
  startDate: getCurrentDate(),
}).on("changeDate", function (e) {
  moment(e.date).format("yyyy/mm/dd hh:ii tt");
  var self = $(e.target);
  self.validate();
});

var search_val = "";
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
    drawCallback: function(settings){
        personnelSize = settings.json.data.length;
        displayRequiredPersonel();
        console.log(personnelSize)
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
      var _actionButton ="";
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_personnel("+$id+")' data-container='body' data-toggle='m-tooltip' data-placement='top' data-original-title='Edit' data-skin='dark'><i class='la la-pencil-square'></i></button>"; 
          _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' onclick='open_delete("+$id+")' data-container='body' data-toggle='m-tooltip' data-placement='top' data-original-title='Delete' data-skin='dark'><i class='la la-trash'></i></button>";       
      return _actionButton;
    }else{ return false; }
} 

function open_delete($id) {
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
    drawCallback: function(settings){
      destinationSize = settings.json.data.length;
      displayRequiredDestinations();
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
    var _actionButton ="";
      _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='edit_destination("+$id+")' data-container='body' data-toggle='m-tooltip' data-placement='top' data-original-title='Edit' data-skin='dark'><i class='la la-pencil-square'></i></button>"; 
      _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnDeleteItem' onclick='open_delete2("+$id+")' data-container='body' data-toggle='m-tooltip' data-placement='top' data-original-title='Delete' data-skin='dark'><i class='la la-trash'></i></button>";       
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

function open_delete2($id) {
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
    initMapTemp();
}

function edit_destination(id){   
    save_method = 'update';
    $('#form_destination')[0].reset();
      $.ajax({
        url : baseUrl("eforms/travel_order/edit_temp_destination/") + id,
        type: "GET",
        dataType: "JSON",
        success: function(data) { 
            initMapTemp(data.id);
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
            $('#formNewTravelFrom').val(data.coords_from);
            $('#formNewTravelTo').val(data.coords_to);
            $('#modal_form_destination').modal('show'); // show bootstrap modal
            $('.modal-title').text('Edit Destination'); // Set Title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown) {
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
        success: function(data) {
          isValidationShow = true;
          tblDestination.ajax.reload();
          toastr.success(data.toastr_msg, "Removed content successfully!", 5000);
          $("#modal_form_delete2").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown) {
          alert('Error adding / update data');
        }
    });
}

function clear_destination(){
    if(destinationSize == 0) return toastr.warning("No destination to be delete.");

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
            beforeSend: function() {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function(data) {
                isValidationShow = true;
                tblDestination.ajax.reload();
                $("#clear_modal").modal("hide");
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                toastr.success(data.toastr_msg, "Removed contents successfully!", 5000);
            }
        });
        return false;
        },
    });
}

let url;
function save_destination(){
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
      url = baseUrl("eforms/travel_order/add_temp_destination/");
    }
    else{
      url = baseUrl("eforms/travel_order/update_temp_destination/");
    }
    $("#form_destination").submit();
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
              if(data.status){ 
                  tblDestination.ajax.reload();

                  if(save_method == 'add') {
                    toastr.success(data.toastr_msg, "Destination added successfully!", 5000);
                  } else{
                    toastr.success(data.toastr_msg, "Destination updated successfully!", 5000);
                  }

                  $("#modal_form_destination #select2_req").text("").trigger("change");
                  $("#modal_form_destination #select2_req").val("").trigger("change");
                  $("#modal_form_destination").modal("hide");
              }else{
                  alert('Error get data from ajax');
              }
          }
      });
      return false;
    },
});

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
      success: function(data) {         
          $('[name="id_personnel"]').val(data.id);
          var newOption = new Option(data.name, data.employee_id, true, true);
          $('#select2_emp').append(newOption).trigger('change');
          $('#modal_form_personnel').modal('show'); // show bootstrap modal
          $('.modal-title').text('Edit Content'); // Set Title to Bootstrap modal title
      },
      error: function (jqXHR, textStatus, errorThrown) {
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
        success: function(data) {
            isValidationShow = true;
            tblPersonnel.ajax.reload();
            toastr.success(data.toastr_msg, "Removed personnel successfully!", 5000)
            $("#modal_form_delete").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error adding / update data');
        }
    });
}

$("#clear_modal").hide();
function clear_personnel() {
    if(personnelSize == 0) return toastr.warning("No personnel to be delete.");

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
                  isValidationShow = true;
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
        url = baseUrl("eforms/travel_order/add_temp_personnel/");
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
            success: function(data) {
              tblPersonnel.ajax.reload();
              $('#modal_form_personnel #select2_emp').text("").trigger('change');
              $('#modal_form_personnel #select2_emp').val("").trigger('change');
              if(save_method == 'add') {
                if(data.message){
                  toastr.warning(data.message, "Warning: ", 5000);
                }else{
                  toastr.success(data.message, "Personnel added successfully!", 5000);
                }
              }else{
                if(data.message) {
                  toastr.warning(data.message, "Warning: ", 5000);
                }else {
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

function displayRequiredType(type){
  if(type == undefined){
    $('#radio_type').css('border', 'solid 1px #b94a48');
    $('#radio_type').css('border-radius', '3px');
    $('#radio_type').css('padding', '6px 0px 3px 0px');
    $('#require_msg_type').removeClass('m--hide');
  } else {
    $('#radio_type').css('border', '');
    $('#radio_type').css('border-radius', '');
    $('#radio_type').css('padding', '');
    $('#require_msg_type').addClass('m--hide');
  }
}

$('[name="type"]').change(function(){
  var type = $('input[name="type"]:checked').val();
  displayRequiredType(type);
});

var personnelSize = 0;
function displayRequiredPersonel(){
  if(personnelSize == 0 && isValidationShow){
    $('#table-personnel').css('border', 'solid 1px #b94a48');
    $('#table-personnel').css('border-radius', '3px');
    $('#table-personnel').css('padding', '6px 0px 3px 0px');
    $('#table_v').removeClass('m--hide');
  } else {
    $('#table-personnel').css('border', '');
    $('#table-personnel').css('border-radius', '');
    $('#table-personnel').css('padding', '');
    $('#table_v').addClass('m--hide');
  }
}

var destinationSize = 0;
function displayRequiredDestinations(){
  if(destinationSize == 0 && isValidationShow){
    $('#table-destination').css('border', 'solid 1px #b94a48');
    $('#table-destination').css('border-radius', '3px');
    $('#table-destination').css('padding', '6px 0px 3px 0px');
    $('#table2_v').removeClass('m--hide');
  } else {
    $('#table-destination').css('border', '');
    $('#table-destination').css('border-radius', '');
    $('#table-destination').css('padding', '');
    $('#table2_v').addClass('m--hide');
  }  
}

var isValidationShow = false;
function add_travel_order(){
    isValidationShow = true;
    var type = $('input[name="type"]:checked').val();
    displayRequiredType(type);
    displayRequiredPersonel();
    displayRequiredDestinations();

    file_under.on("change", function (e) {
      var self = $(e.target);
      self.validate();
    });

    department.on("change", function (e) {
      var self = $(e.target);
      self.validate();
    });  
                  
    $.validate({
      form : '#form_travel_order',
      lang: 'en',
      onSuccess : function(form) {
        if(personnelSize > 0 && destinationSize > 0 && type != undefined) {
            const tempForm = $(form);
            const tempSubmit = tempForm.find(".m-portlet__foot button#btnSaveTravel");
            const formButtons = tempForm.find(".m-portlet__foot .btn");
            $('#table_v').empty();
            $('#table2_v').empty();
            $.ajax({
              url : baseUrl("eforms/travel_order/add_travel_order_v2/"),
              type: "POST",
              data: $('#form_travel_order').serialize(),
              dataType: "JSON",
              beforeSend: function(){
                tempSubmit.addClass("m-loader m-loader--light m-loader--left");
                if(typeof formButtons !== "undefined" && formButtons.length > 0){
                  formButtons.prop("disabled", true);
                }
              },
              success: function(data){
                tempSubmit.removeClass("m-loader m-loader--light m-loader--left");
                  if(data.status){
                      const toRedirect = data.redirect;
                      toastr.success(data.msg, "New Travel Order");
                      setTimeout(function(){
                        if(typeof toRedirect !== "undefined" && toRedirect){
                          window.location.replace(toRedirect);
                        }else{ window.location.replace(baseUrl("eforms/travel_order/masterfile")); }
                      }, 1500);
                  }else{
                    toastr.error(data.msg, "New Travel Order");
                    if(typeof formButtons !== "undefined" && formButtons.length > 0){
                      formButtons.prop("disabled", true);
                    }
                  }
                }
            });
        }
        return false;
    },
  });
}

function change_service(){
    radiobtn = document.getElementById("hitch");
    radiobtn.checked = false;
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

function change_hitch(){
    radiobtn = document.getElementById("service");
    radiobtn.checked = false;
    radiobtn = document.getElementById("commute");
    radiobtn.checked = false;
    radiobtn = document.getElementById("personal");
    radiobtn.checked = false;
    radiobtn = document.getElementById("other");
    radiobtn.checked = false;
  
    document.getElementById('other_remark').style.display = 'none';
    $("#service_veh").show();
    $("#service_driver").show();
    //document.getElementById('service_driver').style.removeProperty( 'display' );
}
  
function change_commute(){
    radiobtn = document.getElementById("service");
    radiobtn.checked = false;
    radiobtn = document.getElementById("hitch");
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
    radiobtn = document.getElementById("hitch");
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
    radiobtn = document.getElementById("hitch");
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
const pointer = {};
const pointer_name = {};
const markersName = {};
const inputGlob = [];
let map;

function initMapTemp(id = "") {
    let input;
    let coordsInput;
    let title;
    let uluru;
    uluru = {lat: 10.704365710388265, lng: 122.96319995137281};
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        uluru = {lat: position.coords.latitude, lng: position.coords.longitude};
      });
    } else {
      console.log("Geolocation is not supported by this browser.");
    }

    map = new google.maps.Map(document.getElementById("tempMap"), {
      mapId: "c38a738bd984de98",
      zoom: 19,
      center: uluru,
      mapTypeId: 'satellite'
    });

    input = "travelFrom";
    coordsInput = "formNewTravelFrom";
    title = "From";

    if(id != ""){
      editmarker(map, id);
    }

    $("#travelFrom").on('focus', function () {
        inputGlob.remove();
        input = "travelFrom";
        title = "From";
        coordsInput = "formNewTravelFrom";
        searchMap(input, coordsInput, title, map);
        var self = $(this);
        self.validate();
    });

    $("#travelTo").on('focus', function () {
        const marker = new google.maps.Marker();
        inputGlob.remove();
        input = "travelTo";
        title = "To";
        coordsInput = "formNewTravelTo";
        searchMap(input, coordsInput, title, map);
        var self = $(this);
        self.validate();
    });

    map.addListener("click", (mapsMouseEvent) => {
      if(inputGlob.length !== 0){
          input = inputGlob[0];
          if(input === "travelFrom"){
              title = "From";
          }else{
              title = "To";
          }
      }
      const coords = mapsMouseEvent.latLng;
      const placeId = mapsMouseEvent.placeId;
      const addresscoords = mapsMouseEvent.latLng.toJSON();
      if(Object.keys(pointer).length < 2){
          if(Object.keys(pointer).length === 1){
              if(input === "travelFrom" && typeof pointer.from != "undefined"){
                  pointer_name.from.setMap(null);
                  pointer.from.setMap(null);
              }else if(input === "travelTo" && typeof pointer.to != "undefined"){
                  pointer.to.setMap(null);
                  pointer_name.to.setMap(null);
              }
          }
      }else{
          if(input === "travelFrom"){
              pointer_name.from.setMap(null);
              pointer.from.setMap(null);
          }else{
              pointer_name.to.setMap(null);
              pointer.to.setMap(null);
          }
      }
      $("#"+coordsInput).val(JSON.stringify(addresscoords));
      GetAddress(coords, map, placeId, input, title);
    });

    $(".closeNewTravel").on('click', function(){
      if(pointer.length !== 0){
        if(pointer.from !== null && pointer.to !== null){
          pointer_name.from.setMap(null);
          pointer.from.setMap(null);
          pointer_name.to.setMap(null);
          pointer.to.setMap(null);
        }
      }
      
    });
}

function searchMap(searchInputID, formTravel, title, map){
    const searchInput = document.getElementById(searchInputID);
    const searchBox = new google.maps.places.SearchBox(searchInput);
    map.addListener("bounds_changed", () => {
      searchBox.setBounds(map.getBounds());
    });

    searchBox.addListener("places_changed", () => {
      const places = searchBox.getPlaces();

      if (places.length == 0) {
        return;
      }

      places.forEach((place) => {
          if (!place.geometry || !place.geometry.location) {
              console.log("Returned place contains no geometry");
              return;
          }

          $("#"+formTravel).val(JSON.stringify(place.geometry.location));

          if(Object.keys(pointer).length < 2){
              if(Object.keys(pointer).length === 1){
                  if(searchInputID === "travelFrom" && typeof pointer.from != "undefined"){
                      pointer_name.from.setMap(null);
                      pointer.from.setMap(null);
                  }else if(searchInputID === "travelTo" && typeof pointer.to != "undefined"){
                      pointer_name.to.setMap(null);
                      pointer.to.setMap(null);
                  }
              }
          }else{
              if(searchInputID === "travelFrom"){
                  pointer_name.from.setMap(null);
                  pointer.from.setMap(null);
              }else{
                  pointer_name.to.setMap(null);
                  pointer.to.setMap(null);
              }
          }
          siteMarkers(searchInputID, title, place.name, place.geometry.location, map);
      });
    });
}

function editmarker(map, id){
    const bounds = new google.maps.LatLngBounds();
    if(id != ""){
        $.ajax({
            url: baseUrl("eforms/travel_order/new_destination_marker/"),
            type: "POST",
            data: {
                csrf_token: _csrf_hash,
                travelOrder_id: id,
            },
            dataType: "JSON",
            success: function(resp){
                if(resp.coords_from != "" && resp.coords_to != "") {
                  const from = resp.travel_from;
                  const to = resp.travel_to;
                  
                  const fromMarker = new google.maps.Marker({
                    position: JSON.parse(resp.coords_from),
                    map: map,
                  });

                  var informationFrom = new google.maps.InfoWindow({
                      content: '<h6>From</h6><br><p>'+String(from).toUpperCase()+'</p>'
                  });
                  informationFrom.open(map, fromMarker);

                  Object.assign(pointer_name,{"from": informationFrom});
                  Object.assign(pointer,{"from": fromMarker});

                  const toMarker = new google.maps.Marker({
                    position: JSON.parse(resp.coords_to),
                    map: map,
                  });

                  var informationTo = new google.maps.InfoWindow({
                    content: '<h6>To</h6><br><p>'+String(to).toUpperCase()+'</p>'
                  });
                  informationTo.open(map, toMarker);

                  Object.assign(pointer_name,{"to": informationTo});
                  Object.assign(pointer,{"to": toMarker});
                  if(fromMarker !== null){
                    bounds.extend(fromMarker.getPosition());
                  }

                  if(toMarker !== null){
                    bounds.extend(toMarker.getPosition());
                  }
                  map.fitBounds(bounds);
                  map.setZoom(11);
                }
            }
        });
    }
} 

function GetAddress(latlon, map, placeId, inputId, title) {
  const travelTo = [];
  var geocoder = new google.maps.Geocoder();

  if(placeId != null){
    const request = {
      placeId: placeId,
      fields: ["name", "formatted_address", "place_id", "geometry"],
    };
    const service = new google.maps.places.PlacesService(map);

    service.getDetails(request, (place, status) => {
      if (
      status === google.maps.places.PlacesServiceStatus.OK &&
      place &&
      place.geometry &&
      place.geometry.location
      ) {
          $("#"+inputId).val(place.name);
          siteMarkers(inputId, title, place.name, latlon, map, 1);
      }
    });
  }else{
    geocoder.geocode({
      location: latlon
    }, function (results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (results[0]) {
          
        }
      }
      const request = {
          placeId: results[1].place_id,
          fields: ["name", "formatted_address", "place_id", "geometry"],
      };
      const service = new google.maps.places.PlacesService(map);
      service.getDetails(request, (place, status) => {
          if (
          status === google.maps.places.PlacesServiceStatus.OK &&
          place &&
          place.geometry &&
          place.geometry.location
          ) {
              $("#"+inputId).val(place.name);
              siteMarkers(inputId, title, place.name, latlon, map, 1);
          }
      });
    });
  }
}

function siteMarkers(input, title, placeName, coords, map, funcname = ""){
    const bounds = new google.maps.LatLngBounds();
    const newMarker = new google.maps.Marker();
    newMarker.setPosition(coords);
    newMarker.setMap(map);

    if(input === "travelFrom"){
        var informationFrom = new google.maps.InfoWindow({
            content: '<h6>'+title+'</h6><br><p>'+placeName.toUpperCase()+'</p>'
        });

        bounds.extend(newMarker.position);
        informationFrom.open(map, newMarker);
        Object.assign(pointer_name,{"from": informationFrom});
        Object.assign(pointer,{"from": newMarker});
        Object.assign(markersName,{"fromTitle": placeName.toUpperCase()});
    }else{
        var informationTo = new google.maps.InfoWindow({
            content: '<h6>'+title+'</h6><br><p>'+placeName.toUpperCase()+'</p>'
        });

        bounds.extend(newMarker.position);
        informationTo.open(map, newMarker);
        Object.assign(pointer_name,{"to": informationTo});
        Object.assign(pointer,{"to": newMarker});
        Object.assign(markersName,{"toTitle": placeName.toUpperCase()});
    }

    const from_marker = pointer.from;
    const from_title = markersName.fromTitle;
    const to_marker = pointer.to;
    const to_title = markersName.toTitle;
    
    if(funcname != ""){
        if(Object.keys(pointer).length != 0){
            if(input === "travelFrom"){
                from_marker.addListener("click", function(mapsMouseEvent){
                    information.open(map, from_marker);
                    inputGlob.remove();
                    inputGlob.push("travelFrom");
                });
            }else{
                to_marker.addListener("click", function(mapsMouseEvent){
                    information.open(map, to_marker);
                    inputGlob.remove();
                    inputGlob.push("travelTo");
                });
            }
        }
    }else{
        if(Object.keys(pointer).length != 0){
            if(input === "travelFrom"){
                newMarker.addListener("click", function(mapsMouseEvent){
                    information.open(map, newMarker);
                    inputGlob.remove();
                    inputGlob.push("travelFrom");
                });
            }else{
                newMarker.addListener("click", function(mapsMouseEvent){
                    information.open(map, newMarker);
                    inputGlob.remove();
                    inputGlob.push("travelTo");
                });
            }
        }
    }
    if(Object.keys(pointer).length == 2){
        if(from_marker != null){
            pointer_name.from.setMap(null);
            bounds.extend(from_marker.getPosition());
            map.fitBounds(bounds);
            var informationFrom = new google.maps.InfoWindow({
              content: '<h6>From</h6><br><p>'+String(from_title).toUpperCase()+'</p>'
            });
            informationFrom.open(map, from_marker);
            Object.assign(pointer_name,{"from": informationFrom});
        }
        if(to_marker != null){
            pointer_name.to.setMap(null);
            bounds.extend(to_marker.getPosition());
            map.fitBounds(bounds);
            var informationTo = new google.maps.InfoWindow({
              content: '<h6>To</h6><br><p>'+String(to_title).toUpperCase()+'</p>'
            });
            Object.assign(pointer_name,{"to": informationTo});
            informationTo.open(map, to_marker);
        }
    }
}

$("#travelOrderFromIcon .icon").on("click", function(){
    if($("#travelOrderOptionFrom").is(":visible")){
      $("#travelOrderOptionFrom").hide();
    }else{
      $("#travelOrderOptionFrom").show();
    }

    $.ajax({
      url: baseUrl("eforms/travel_order/sites_options/"),
      type: "GET",
      dataType: "JSON",
      success: function(resp){
        if(resp.length > 0){
          vmTab3.checker = true;
        }else{
          vmTab3.checker = false;
        }
        vmTab3.vm_tab3 = Object.assign({}, resp);
      }
    });
});

var vmTab3 = new Vue({
  el: "#travel_option_from",
  data: { 
    vm_tab3: [], checker: false,
  },
  methods: {
    selectedSite: function (id, input_id) {
      $.ajax({
        url: baseUrl("eforms/travel_order/site_selected/"),
        type: "POST",
        data: {
          csrf_token: _csrf_hash,
          sites_id: id,
        },
        dataType: "JSON",
        success: function(resp){

          const coords = {"lat": Number(resp.latitude), "lng": Number(resp.longtitude)};
          $("#travelFrom").val(resp.site_name);
          Object.assign(markersName,{"fromTitle": resp.site_name});
          
          $("#formNewTravelFrom").val(JSON.stringify(coords));
          
          if($("#travelOrderOptionFrom").is(":visible")){
              $("#travelOrderOptionFrom").hide();
          }else{
              $("#travelOrderOptionFrom").show();
          }

          const bounds = new google.maps.LatLngBounds();
          let fromMarker;

          if(pointer.from != null) {
              pointer_name.from.setMap(null);
              pointer.from.setMap(null);
              fromMarker = new google.maps.Marker();
              fromMarker.setPosition(coords);
              fromMarker.setMap(map);
              Object.assign(pointer,{"from": fromMarker});

              var informationFrom = new google.maps.InfoWindow({
                content: '<h6>From</h6><br><p>'+String(resp.site_name).toUpperCase()+'</p>'
              });

              informationFrom.open(map, fromMarker);
              Object.assign(pointer_name,{"from": informationFrom});
          } else {
              fromMarker = new google.maps.Marker();
              fromMarker.setPosition(coords);
              fromMarker.setMap(map);
              Object.assign(pointer,{"from": fromMarker});
              map.setCenter(coords);

              var informationFrom = new google.maps.InfoWindow({
                content: '<h6>From</h6><br><p>'+String(resp.site_name).toUpperCase()+'</p>'
              });

              informationFrom.open(map, fromMarker);
              Object.assign(pointer_name,{"from": informationFrom});
          }

          const from_marker = pointer.from;
          const from_title = markersName.fromTitle;
          const to_marker = pointer.to;
          const to_title = markersName.toTitle;

          if(Object.keys(pointer).length == 2){
              if(from_marker != null && from_title != null){

                  if(pointer_name.from != null){
                      pointer_name.from.setMap(null);
                  }

                  bounds.extend(from_marker.getPosition());
                  map.fitBounds(bounds);

                  var informationFrom = new google.maps.InfoWindow({
                    content: '<h6>From</h6><br><p>'+String(from_title).toUpperCase()+'</p>'
                  });

                  informationFrom.open(map, from_marker);
                  Object.assign(pointer_name,{"from": informationFrom});
              }
              if(to_marker != null && to_title != null){

                  if(pointer_name.to != null) {
                      pointer_name.to.setMap(null);
                  }

                  bounds.extend(to_marker.getPosition());
                  map.fitBounds(bounds);

                  var informationTo = new google.maps.InfoWindow({
                      content: '<h6>To</h6><br><p>'+String(to_title).toUpperCase()+'</p>'
                  });

                  informationTo.open(map, to_marker);
                  Object.assign(pointer_name,{"to": informationTo});
              }
          }
        } 
      });
    }
  }
});

$("#travelOrderToIcon .icon").on("click", function(){
    if($("#travelOrderOptionTo").is(":visible")){
      $("#travelOrderOptionTo").hide();
    }else{
      $("#travelOrderOptionTo").show();
    }

    $.ajax({
      url: baseUrl("eforms/travel_order/sites_options/"),
      type: "GET",
      dataType: "JSON",
      success: function(resp){
        if(resp.length > 0){
          vmTab2.checker = true;
        }else{
          vmTab2.checker = false;
        }
        vmTab2.vm_tab2 = Object.assign({}, resp);
      }
    });
});

var vmTab2 = new Vue({
  el: "#travel_option_to",
  data: { 
    vm_tab2: [], checker: false,
  },
  methods: {
    selectedSite: function (id) {
      $.ajax({
        url: baseUrl("eforms/travel_order/site_selected/"),
        type: "POST",
        data: {
          csrf_token: _csrf_hash,
          sites_id: id,
        },
        dataType: "JSON",
        success: function(resp){

            const coords = {"lat": Number(resp.latitude), "lng": Number(resp.longtitude)};
            $("#travelTo").val(resp.site_name);
            Object.assign(markersName,{"toTitle": resp.site_name});
            $("#formNewTravelTo").val(JSON.stringify(coords));

            if($("#travelOrderOptionTo").is(":visible")){
              $("#travelOrderOptionTo").hide();
            }else{
              $("#travelOrderOptionTo").show();
            }

            const bounds = new google.maps.LatLngBounds();
            let toMarker;

            if(pointer.to != null){
              pointer.to.setMap(null);
              pointer_name.to.setMap(null);
              toMarker = new google.maps.Marker();
              toMarker.setPosition(coords);
              toMarker.setMap(map);
              Object.assign(pointer,{"to": toMarker});
              var informationTo = new google.maps.InfoWindow({
                content: '<h6>To</h6><br><p>'+String(resp.site_name).toUpperCase()+'</p>'
              });
              informationTo.open(map, toMarker);
              Object.assign(pointer_name,{"to": informationTo});
            }else{
              toMarker = new google.maps.Marker();
              toMarker.setPosition(coords);
              toMarker.setMap(map);
              Object.assign(pointer,{"to": toMarker});
              map.setCenter(coords);
              var informationTo = new google.maps.InfoWindow({
                content: '<h6>To</h6><br><p>'+String(resp.site_name).toUpperCase()+'</p>'
              });
              informationTo.open(map, toMarker);
              Object.assign(pointer_name,{"from": informationTo});
            }

            const from_marker = pointer.from;
            const from_title = markersName.fromTitle;
            const to_marker = pointer.to;
            const to_title = markersName.toTitle;

            if(Object.keys(pointer).length == 2){
              
                if(from_marker != null && from_title != null){
                    if(pointer_name.from != null){
                      pointer_name.from.setMap(null);
                    }
                    bounds.extend(from_marker.getPosition());
                    map.fitBounds(bounds);
                    var informationFrom = new google.maps.InfoWindow({
                        content: '<h6>From</h6><br><p>'+String(from_title).toUpperCase()+'</p>'
                    });
                    informationFrom.open(map, from_marker);
                    Object.assign(pointer_name,{"from": informationFrom});
                }

                if(to_marker != null && to_title != null){
                  if(pointer_name.to != null){
                      pointer_name.to.setMap(null);
                    }
                    bounds.extend(to_marker.getPosition());
                    map.fitBounds(bounds);
                    var informationTo = new google.maps.InfoWindow({
                        content: '<h6>To</h6><br><p>'+String(to_title).toUpperCase()+'</p>'
                    });
                    informationTo.open(map, to_marker);
                    Object.assign(pointer_name,{"to": informationTo});
                }
            }
        } 
      });
    }
  }
});

function getCurrentDate(){
  var tempDays = 30;
  if($.inArray("administrator_privilege", _currentActions) !== -1){
    tempDays = 30;
  }
  return moment().subtract(tempDays,"days").format("YYYY/MM/DD 00:00");
}