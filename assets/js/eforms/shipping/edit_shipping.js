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
let selectData = []
$.ajax({
  url: baseUrl("eforms/shipping/view_shipping_details/") + param_id,
  type: "GET",
  dataType: "JSON",
  success: function(data){
      if(data.shipping_main[0].cat == "in"){
        $("#courier").text("");
        $("#ex-left").text("");    
      }else{
        //$("input[name='ship_to_ex']").val();
        $("#courier").append("<label class='col-3 col-form-label'>Courier & Waybill #: </label><div class='col-9'><input class='form-control m-input' type='waybill' name='waybill' value='"+data.shipping_main[0].waybill+"'  id='transporter'/></div>");
        $("#ex-left").append("<div class='form-group m-form__group row'><label class='col-3 col-form-label'>Ship to:</label><div class='col-9'><input type='text' name='ship_to_ex' value='"+data.shipping_main[0].ship_to_name+"' class='form-control m-input'/></div></div><div class='form-group m-form__group row'><label class='col-3 col-form-label'>Company:</label><div class='col-9'><input type='text' value='"+data.shipping_main[0].company_to+"' name='company_ex' class='form-control m-input'/></div></div><div class='form-group m-form__group row'><label class='col-3 col-form-label'>Department:</label><div class='col-9'><input type='text' value='"+data.shipping_main[0].department_to+"' name='department_ex' class='form-control m-input'/></div></div><div class='form-group m-form__group row'><label class='col-3 col-form-label'>Address:</label><div class='col-9'><textarea name='address_ex' class='form-control' rows='4' data-validation='required'>"+data.shipping_main[0].ship_to_address+"</textarea></div></div>");
        $("#in-left").toggle("hide");
      }

      $("input[name=type]").on("click", function(){
        if($("input[name=type]:checked").val()=="others"){
          $("#service_component").toggle("hide");
          $("#other_component").append("<div class='form-group m-form__group row'><label class='col-md-3 col-lg-3 col-sm-12 col-form-label'>Remarks:</label><div class='col-md-9 col-lg-9 col-sm-12'><textarea class='form-control m-input' id='others_remarks' name='others_remarks' rows='4' data-validation='required'>"+data.shipping_main[0].others_remarks+"</textarea></div></div>");
          $("#driver").text("");
          $("#service").text("");
          $("#service").val("");
          $("#driver").val("");
        }else{
          $("#service_component").toggle("show");
          $("#other_component").text("");
          $("#others_remarks").text("");
        }
      });
      
      if(data.shipping_main[0].is_service == "1"){
          $('input:radio[name=type][value=service]').click();
          $("#service_component").toggle("show");
          $("#other_component").text("");
          $("#others_remarks").text("");
      }else{
          $('input:radio[name=type][value=others]').click();
          $("#other_component").append("<div class='form-group m-form__group row'><label class='col-md-3 col-lg-3 col-sm-12 col-form-label'>Remarks:</label><div class='col-md-9 col-lg-9 col-sm-12'><textarea class='form-control m-input' id='others_remarks' name='others_remarks' rows='4' data-validation='required'>"+data.shipping_main[0].others_remarks+"</textarea></div></div>");
          $("#driver").text("");
          $("#service").text("");
          $("#service").val("");
          $("#driver").val("");
      }

      if(data.shipping_main[0].cat =="in"){
          typeName="Internal";
      }else{
          typeName="External";
      }
      var type = new Option(typeName, data.shipping_main[0].cat, true, true);
      $('#type').append(type).trigger('change');
      
      if(data.shipping_main[0].plateno == "N/A" || data.shipping_main[0].plateno == "" || data.shipping_main[0].plateno == null ){
        var service = new Option("Select...","", true, true);
      }else{
        var service = new Option(data.shipping_main[0].plateno+' | '+data.shipping_main[0].vehicle_name, data.shipping_main[0].vehicle_id, true, true);
      }

      var priority = new Option(data.shipping_main[0].priority, data.shipping_main[0].priority, true, true);
      $('#priority').append(priority).trigger('change');
      var file_under = new Option(data.shipping_main[0].company, data.shipping_main[0].company_from, true, true);
      $('#file_under').append(file_under).trigger('change');
      var department = new Option(data.shipping_main[0].department, data.shipping_main[0].department_from, true, true);
      $('#select_department').append(department).trigger('change');
      var requested_by = new Option(data.shipping_main[0].firstname+' '+data.shipping_main[0].lastname, data.shipping_main[0].requested_by, true, true);
      $('#requested_by').append(requested_by).trigger('change');
      var ship_to = new Option(data.shipping_main[0].ship_to_name, data.shipping_main[0].ship_to, true, true);
      $('#select_ship_to').append(ship_to).trigger('change');
      var driver = new Option(data.shipping_main[0].driver_name, data.shipping_main[0].driver, true, true);
      $('#driver').append(driver).trigger('change');
      var location = new Option(data.shipping_main[0].location, data.shipping_main[0].area_id, true, true);
      $('#location').append(location).trigger('change');
      $('#service').append(service).trigger('change');

      vmTab1.vm_tab1 = Object.assign({}, data.shipping_main[0]);
  }
});

$("input[name=type]").on("click", function(){
  if($("input[name=type]:checked").val()=="service"){
    $("#service_component").toggle("show");
    $("#other_component").text("");
  }else{
      $("#other_component").append("<div class='form-group m-form__group row'><label class='col-md-2 col-lg-2 col-sm-12 col-form-label'>Remarks</label><div class='col-md-10 col-lg-10 col-sm-10'><textarea class='form-control m-input' id='company' name='remarks' rows='4' data-validation='required'></textarea></div></div>");
      $("#service_component").toggle("hide");
  }
});

var vmTab1 = new Vue({
  el: "#frm_status_edit",
  data: { vm_tab1: {} },
  mounted: function(){
      // setTimeout(function(){
      //     var typeName ="";
      //     console.log(data);
      //     let vmData = this.vmTab1.vm_tab1; 
      //     console.log(vmData);  
      //     if(vmData.cat =="in"){
      //         typeName="Internal";
      //     }else{
      //         typeName="External";
      //     }

      //     if(vmData.plateno == "N/A" || vmData.plateno == "" || vmData.plateno == null ){
      //       var service = new Option("Select...","", true, true);
      //     }else{
      //       var service = new Option(vmData.plateno+' | '+vmData.vehicle_name, vmData.vehicle_id, true, true);
      //     }
      
      //     var type = new Option(typeName, vmData.cat, true, true);
      //     $('#type').append(type).trigger('change');
      //     var priority = new Option(vmData.priority, vmData.priority, true, true);
      //     $('#priority').append(priority).trigger('change');
      //     var file_under = new Option(vmData.company, vmData.company_from, true, true);
      //     $('#file_under').append(file_under).trigger('change');
      //     var department = new Option(vmData.department, vmData.department_from, true, true);
      //     $('#select_department').append(department).trigger('change');
      //     var requested_by = new Option(vmData.firstname+' '+vmData.lastname, vmData.requested_by, true, true);
      //     $('#requested_by').append(requested_by).trigger('change');
      //     var ship_to = new Option(vmData.ship_to_name, vmData.ship_to, true, true);
      //     $('#select_ship_to').append(ship_to).trigger('change');
      //     var driver = new Option(vmData.driver_name, vmData.driver, true, true);
      //     $('#driver').append(driver).trigger('change');
      //     var location = new Option(vmData.location, vmData.area_id, true, true);
      //     $('#location').append(location).trigger('change');
          
      //     $('#service').append(service).trigger('change');
      // },1000);
  }
});
  
var tblShipping = $("#table-shipping-content").DataTable({
  dom: '<"toolbar">frtlip',
  serverSide: true,
  processing: true,
  bPaginate: false,
  bInfo: false,
  ajax: {
  url: baseUrl("eforms/shipping/shipping_content_table/")  + param_id,
  type: "post",
      dataType: "json",
      data: function(d){
    d.csrf_token = _csrf_hash
  }
  },
  searching: false,
  columns: [
      { data: "stock_code"},
      { data: "quantity", render: function(data, type, row, meta){return row.quantity+" "+row.uom; }},
      { data: "description"},
      { data: "item_purpose" },{ data: null, width: "8%", className: "text-center"},
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
    _actionButton += " <button type='button' onclick='editItem("+$id+")' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' data-toggle='modal' data-target='#edit_item_modal'><a><i class='la la-pencil-square'></i></a></button>";			
    _actionButton += " <button type='button' onclick='deleteItem("+$id+")' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><a><i class='la la-trash'></i></a></button>";				
		return _actionButton;
	}else{ return false; }
}

$("#add_item_modal").hide();
$("#edit_item_modal").hide();
$("#add_asset_modal").hide();

function deleteItem($id){
  $('#delete_modal').modal('show');
  $.validate({
    form : '#delete_form',
    lang: 'en',
    onSuccess : function(form) {
            $.ajax({
                url: baseUrl("eforms/shipping/delete_editContent/") +$id,
                type: "POST",
                dataType: "json",
                data: $("#delete_form").find("input,textarea").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data){
                        $('#delete_modal').modal('hide');
                        tblShipping.ajax.reload();
                        toastr.success(data.toastr_msg, "Removed successfully", 5000);
                    }else{
                        toastr.error(data.toastr_msg, "Error removing item!", 5000);
                    }
                    $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
                }
            });
        return false;
      },
  });
}

function addNewItem(){
  $('#items').select2('close');
  $("#edit_shipping_select_item_content").attr("hidden", true);
  $("#edit_shipping_add_item_content").attr("hidden", false);
}

function addNewItem_edit(){
  $('#edit_items').select2('close');
  $("#select_item_edit_content").attr("hidden", true);
  $("#add_item_edit_content").attr("hidden", false);
}

function backToSelectItem(){
  $("#edit_shipping_select_item_content").attr("hidden", false);
  $("#edit_shipping_add_item_content").attr("hidden", true);
}

function backToSelectItem_edit(){
  $("#select_item_edit_content").attr("hidden", false);
  $("#add_item_edit_content").attr("hidden", true);
}
  
$("#items").select2({
  placeholder: 'Select. .',
  dropdownParent: $("#add_item_modal"),
  width: '100%',
  ajax: {
    url: baseUrl("eforms/shipping/item_lookup"),
    delay: 250,
    dataType: "json",
    global: false,
    processResults: function (data) {
      return data;
    }
  },
  language: {
    noResults: function(){
      return `<button style="width: 100%" type="button"
            class="btn btn-primary" 
            onClick='addNewItem()'>+ Add New Item</button>
            </li>`;
    }
  },
  escapeMarkup: function (markup) {
    return markup;
  }
});

$("#items").on("select2:select", function() {
  $.ajax({
    type: "GET",
    data: { data: $("#items option:selected").attr("value") },
    url: baseUrl("eforms/shipping/item_lookup_details"),
    dataType: "json",
    success: function(data) {
      $("#quantity").val("1");
      $("#uom").val(data.results.uom);
      $("#description").val(data.results.description);
    }
  });
});

$("#add_uom").select2({
  allowClear: true,
  placeholder: 'Select. .',
  dropdownParent: $("#add_item_modal"),
  width: '100%',
  ajax: {
    url: baseUrl("eforms/shipping/uom_lookup"),
    delay: 250,
    dataType: "json",
    global: false,
    processResults: function (data) {
      return data;
    }
  },
  language: {
    noResults: function(){
      return `<div class="form-group">
                  <label class="col-12 col-form-label form-control-label">
                      CODE:
                  </label>
                  <div class="col-12">
                      <input type="text" name="add_code" class="form-control" id="add_code" data-validation="required"/>
                  </div>
              </div>
              <div class="form-group">
                  <label class="col-12 col-form-label form-control-label">
                      DESCRIPTION:
                  </label>
                  <div class="col-12">
                      <input type="text" id="add_description" name="add_description" class="form-control" id="code" data-validation="required"/>
                  </div>
              </div>
            <button style="width: 100%" class="btn btn-primary" onclick='addUOM()'>+ Add New UOM</button>`
    }
  },
  escapeMarkup: function (markup) {
    return markup;
  }
});

$("#edit_uom").select2({
  allowClear: true,
  placeholder: 'Select. .',
  dropdownParent: $("#edit_item_modal"),
  width: '100%',
  ajax: {
    url: baseUrl("eforms/shipping/uom_lookup"),
    delay: 250,
    dataType: "json",
    global: false,
    processResults: function (data) {
      return data;
    }
  },
  language: {
    noResults: function(){
      return `<div class="form-group">
                  <label class="col-12 col-form-label form-control-label">
                      CODE:
                  </label>
                  <div class="col-12">
                      <input type="text" name="edit_code" class="form-control" id="add_code" data-validation="required"/>
                  </div>
              </div>
              <div class="form-group">
                  <label class="col-12 col-form-label form-control-label">
                      DESCRIPTION:
                  </label>
                  <div class="col-12">
                      <input type="text" id="edit_description" name="add_description" class="form-control" id="code" data-validation="required"/>
                  </div>
              </div>
            <button style="width: 100%" class="btn btn-primary" onclick='addUOM_edit()'>+ Add New UOM</button>`
    }
  },
  escapeMarkup: function (markup) {
    return markup;
  }
});

function addUOM(){
  var add_code = $("#add_code").val();
  var add_description = $("#add_description").val();
  var csrf_token = $("#csrf_token").val();
  $.ajax({
    type: "POST",
    data: { 
      csrf_token : csrf_token,
      add_code : add_code,
      add_description : add_description
     },
    url: baseUrl("eforms/shipping/add_new_uom"),
    dataType: "json",
    success: function(response) {
      if(response){
        toastr.success(response.toastr_msg, "New UOM added successfully", 5000);
        $("#add_uom").select2('close');
      }else{
        toastr.error(response.toastr_msg, "No UOM or DESCRIPTION to be saved.", 5000);
      }
    }
  });
}

function addUOM_edit(){
  var add_code = $("#add_code").val();
  var add_description = $("#edit_description").val();
  var csrf_token = $("#edit_csrf_token").val();
  $.ajax({
    type: "POST",
    data: { 
      csrf_token : csrf_token,
      add_code : add_code,
      add_description : add_description
     },
    url: baseUrl("eforms/shipping/add_new_uom"),
    dataType: "json",
    success: function(response) {
      if(response){
        toastr.success(response.toastr_msg, "New UOM added successfully", 5000);
        $("#edit_uom").select2('close');
      }else{
        toastr.error(response.toastr_msg, "No UOM or DESCRIPTION to be saved.", 5000);
      }
    }
  });
}

$("#clear").on("click", function() {
  $('#clear_asset_modal').modal('show');
  $.validate({
    form : '#clear_asset_form',
    lang: 'en',
    onSuccess : function(form) {
            $.ajax({
                url: baseUrl("eforms/shipping/clear_editContent/") + param_id,
                type: "POST",
                dataType: "json",
                data: $("#clear_asset_form").find("input,textarea").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data){
                        $('#clear_asset_modal').modal('hide');
                        tblShipping.ajax.reload();
                        toastr.success(data.toastr_msg, "Removed successfully", 5000);
                    }else{
                        toastr.error(data.toastr_msg, "Error removing item!", 5000);
                    }
                    $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
                }
            });
        return false;
      },
  });
});

var type =  [{
    id: "in",
    text: "Internal",
  },
  {
    id: "ex",
    text: "External",
}];

$("#type").select2({
  placeholder: 'Select. .',
  width: '100%',
  data: type  
});

var data =  [{
    id: "Normal",
    text: "Normal"
  },
  {
    id: "Important",
    text: "Important"
}];

$("#priority").select2({
  placeholder: 'Select. .',
  width: '100%',
  data: data
});

$("#file_under").select2({
  placeholder: 'Select. .',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/shipping/get_file_under"),
    delay: 250,
    dataType: "json",
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

$("#select_department").select2({
  placeholder: 'Select. .',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/shipping/get_department"),
    delay: 250,
    dataType: "json",
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

$("#requested_by").select2({
  placeholder: 'Select. .',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/shipping/get_requested_by"),
    delay: 250,
    dataType: "json",
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});
  
$("#select_ship_to").select2({
  placeholder: 'Select. .',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/shipping/get_requested_by"),
    delay: 250,
    dataType: "json",
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

$("#select_ship_to").on("change", function() {
  $.ajax({
    type: "GET",
    data: { data: $("#select_ship_to option:selected").attr("value") },
    url: baseUrl("eforms/shipping/get_ship_to_detail"),
    dataType: "json",
    success: function(json) {
      if (json.response) {
        var tempRow = Object.assign({}, json.row);
        var _tempHtml = "";
        var tempCompany = (typeof tempRow.company_id !== "undefined") ? tempRow.company_id : "No assigned company";
        var tempDepartment = (typeof tempRow.department_id !== "undefined") ? tempRow.department_id : "No assigned department";
        var tempPosition = (typeof tempRow.position !== "undefined") ? tempRow.position : "No assigned position";
        _tempHtml += tempCompany + '\n';
        _tempHtml += tempDepartment + '\n';
        _tempHtml += tempPosition;
        $("#company").val(_tempHtml);
      } else {
        $("#company").val("");
      }
    }
  });
});

$("#location").select2({
  placeholder: 'Select. .',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/shipping/get_location"),
    delay: 250,
    dataType: "json",
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

$("#service").select2({
  placeholder: 'Select. .',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/shipping/service_vehicle"),
    delay: 250,
    dataType: "json",
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

$("#driver").select2({
  placeholder: 'Select. .',
  width: '100%',
  ajax: {
    url: baseUrl("eforms/shipping/driver"),
    delay: 250,
    dataType: "json",
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

$('#due_dt').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'mm/dd/yyyy h:i',
 // maxView: 4,
 // minView: 2
});

$.validate({
  form : '#add_item_modal',
  lang: 'en',
  onSuccess : function(form) {    
          $.ajax({
              url: baseUrl("eforms/shipping/edit_itemModal/") + param_id,
              type: "POST",
              dataType: "json",
              data: $("#add_item_modal").find("input,textarea,select").serialize(),
              beforeSend: function(){
                  $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
              },
              success: function(data){
                  if(data){
                    $('#add_item_modal').modal('hide');
                    tblShipping.ajax.reload();
                    $('#add_item_form')[0].reset();
                    $("#items").val("").trigger('change');
                      toastr.success(data.toastr_msg, "Added item successfully", 5000);
                  }else{
                     toastr.error(data.toastr_msg, "Error!", 5000);    
                  }
                  $(".btn-submit").removeClass(" m-loader m-loader--light m-loader--right");
              }
          });
      return false;
  },
});

$.validate({
  form : '#add_new_item_form',
  lang: 'en',
  onSuccess : function(form) {
          
          $.ajax({
              url: baseUrl("eforms/shipping/add_new_item"),
              type: "POST",
              dataType: "json",
              data: $("#add_new_item_form").find("input,textarea,select").serialize(),
              beforeSend: function(){
                  $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
              },
              success: function(data){
                  // $('#add_item_modal').modal('hide');
                  // tblShipping.ajax.reload();
                  $('#add_new_item_form')[0].reset();
                  // $("#items").val("").trigger('change');
                  $("#edit_shipping_select_item_content").attr("hidden", false);
                  $("#edit_shipping_add_item_content").attr("hidden", true);
                  toastr.success(data.toastr_msg, "New Item added successfully", 5000);
                  $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
              }
          });
      return false;
  },
});

$.validate({
  form : '#add_new_item_form_edit',
  lang: 'en',
  onSuccess : function(form) {
          
          $.ajax({
              url: baseUrl("eforms/shipping/add_new_item"),
              type: "POST",
              dataType: "json",
              data: $("#add_new_item_form_edit").find("input,textarea,select").serialize(),
              beforeSend: function(){
                  $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
              },
              success: function(data){
                  $('#add_item_modal').modal('hide');
                  // tblShipping.ajax.reload();
                  $('#add_new_item_form_edit')[0].reset();
                  // $("#items").val("").trigger('change');
                  $("#select_item_edit_content").attr("hidden", false);
                  $("#add_item_edit_content").attr("hidden", true);
                  toastr.success(data.toastr_msg, "New Item added successfully", 5000);
                  $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
              }
          });
      return false;
  },
});


$.validate({
  form : '#add_asset_modal',
  lang: 'en',
  onSuccess : function(form) {
          $.ajax({
              url: baseUrl("eforms/shipping/edit_assetModal/") + param_id,
              type: "POST",
              dataType: "json",
              data: $("#add_asset_modal").find("input,textarea,select").serialize(),
              beforeSend: function(){
                  $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
              },
              success: function(data){
                  $('#add_asset_modal').modal('hide');
                  tblShipping.ajax.reload();
                  $('#add_asset_form')[0].reset();
                  $("#assets").val("").trigger('change');
                  toastr.success(data.toastr_msg, "Added asset successfully!", 5000);
                  $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
              }
          });
      return false;
  },
});

$.validate({
  form : '#frm_status_edit',
  lang: 'en',
  onSuccess : function(form) {
    if(tblShipping.data().length !== 0) {
          var disabled = $('#frm_status_edit').find('textarea:disabled,select:disabled').removeAttr('disabled');
        
          $.ajax({
              url: baseUrl("eforms/shipping/update_shipping/") + param_id,
              type: "POST",
              dataType: "json",
              data: $("#frm_status_edit").find("input,select,textarea").serialize(),
              beforeSend: function(){
                  $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
              },
              success: function(data){
                  if(data){
                      window.location.href = baseUrl("eforms/shipping/view_shipping?id="+param_id, toastr.success(data.toastr_msg, "Successfully saved!", 5000));
                      disabled.attr('disabled','disabled');
                      
                  }else{
                      toastr.error(data.toastr_msg, "Error!", 5000);
                      disabled.attr('disabled','disabled');
                  }
                  $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
              }
          });
        }else{
          toastr.error(data.toastr_msg, "No content for saving!", 5000);   
          
      }
      return false;  
  },
});

$("#assets").select2({
  placeholder: 'Select. .',
  dropdownParent: $("#add_asset_modal"),
  width: '100%',
  ajax: {
    url: baseUrl("eforms/shipping/asset_lookup"),
    delay: 250,
    dataType: "json",
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

$("#assets").on("select2:select", function() {
  $.ajax({
    type: "GET",
    data: { data: $("#assets option:selected").attr("value") },
    url: baseUrl("eforms/shipping/asset_lookup_details"),
    dataType: "json",
    success: function(data) {
      $("#quantity2").val("1");
      $("#uom2").val(data.results.uom);
      $("#description2").val(data.results.description);
    }
  });
});

function editItem($id){
    $.ajax({
      type: "GET",
      url: baseUrl("eforms/shipping/update_item/") +$id,
      dataType: "json",
      success: function(data) {
        if(data[0].cat=="asset"){
          $("#edit_items").select2({
            placeholder: 'Select. .',
            dropdownParent: $("#edit_item_modal"),
            width: '100%',
            ajax: {
              url: baseUrl("eforms/shipping/asset_lookup/"),
              delay: 250,
              dataType: "json",
              global: false,
              processResults: function (data) {
                return data;
              }
            }
          });
        }else{
          $("#edit_items").select2({
            placeholder: 'Select. .',
            dropdownParent: $("#edit_item_modal"),
            width: '100%',
            ajax: {
              url: baseUrl("eforms/shipping/item_lookup/"),
              delay: 250,
              dataType: "json",
              global: false,
              processResults: function (data) {
                return data;
              }
            },
            language: {
              noResults: function(){
                return `<button style="width: 100%" type="button"
                      class="btn btn-primary" 
                      onClick='addNewItem_edit()'>+ Add New Item</button>
                      </li>`;
              }
            },
            escapeMarkup: function (markup) {
              return markup;
            }
          });
        }
        var newOption = new Option(data[0]['stock_code']+' | '+data[0]['description'], data[0]['stock_code'], true, true);
        $('#edit_items').append(newOption).trigger('change');  
        $('#edit_item_form #quantity').val(data[0]['quantity']);
        $('#edit_item_form #uom').val(data[0]['uom']);
        $('#edit_item_form #description').val(data[0]['description']);
        $('#edit_item_form #purpose').val(data[0]['item_purpose']); 
        // $('#edit_item_form #cat').val(data[0]['cat']); 
      }
    });

    $.validate({
      form : '#edit_item_modal',
      lang: 'en',
      onSuccess : function(form) {
              
              $.ajax({
                  url: baseUrl("eforms/shipping/update_edit_item/") +$id,
                  type: "POST",
                  dataType: "json",
                  data: $("#edit_item_modal").find("input,textarea,select").serialize(),
                  beforeSend: function(){
                      $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                  },
                  success: function(data){
                      $('#edit_item_modal').modal('hide');
                      tblShipping.ajax.reload();
                      $('#edit_item_form')[0].reset();
                      $("#edit_items").val("").trigger('change');
                      toastr.success(data.toastr_msg, "Updated successfully", 5000);
                      $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
                  }
              });
          return false;
      },
    });

}

$("#edit_items").select2({
  placeholder: 'Select. .',
  dropdownParent: $("#edit_item_modal"),
  width: '100%',
  ajax: {
    url: baseUrl("eforms/shipping/item_lookup/"),
    delay: 250,
    dataType: "json",
    global: false,
    processResults: function (data) {
      return data;
    }
  }
});

$("#edit_items").on("select2:select", function() {
  $.ajax({
    type: "GET",
    data: { data: $("#edit_items option:selected").attr("value") },
    url: baseUrl("eforms/shipping/item_lookup_details"),
    dataType: "json",
    success: function(data) {
      $("#edit_item_modal #quantity").val("1");
      $("#edit_item_modal #uom").val(data.results.uom);
      $("#edit_item_modal #description").val(data.results.description);
      
    }
  });
});

$("#clear_asset_modal").hide();
$("#delete_modal").hide();

