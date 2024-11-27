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
$("#select_file_under").select2({
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
    minimumInputLength: 3,
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

  $("#select_requested_by").select2({
    placeholder: 'Select. .',
    width: '100%',
    minimumInputLength: 3,
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

  var data =  [
    {
      id: "Normal",
      text: "Normal"
    },
    {
      id: "Important",
      text: "Important"
    }
  ];

  $("#priority").select2({
    placeholder: 'Select. .',
    width: '100%',
    data: data
  });

  $("#location").select2({
    placeholder: 'Select. .',
    width: '100%',
    minimumInputLength: 3,
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


  $("#select_ship_to").select2({
    placeholder: 'Select. .',
    width: '100%',
    minimumInputLength: 3,
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

  $("#select_ship_to").on("select2:select", function() {
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
          $('[name="company"]').val(_tempHtml);
        } else {
          $('[name="company"]').val("");
        }
      }
    });
  });

  $("#service").select2({
    placeholder: 'Select. .',
    width: '100%',
    minimumInputLength: 3,
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
    minimumInputLength: 3,
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

  var tblShipping = $("#table-shipping-content").DataTable({
    dom: '<"toolbar">frtlip',
    width: '100%',
	  serverSide: true,
    processing: true,
    bPaginate: false,
    bInfo: false,
    aaSorting: [],
    ajax: {
		url: baseUrl("eforms/shipping/get_shipping_content/"),
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
        { data: "item_purpose"},
        { data: null, width: "8%", className: "text-center"},
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

$("#items").select2({
  allowClear: true,
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

function addNewItem(){
  $('#items').select2('close');
  $("#select_item_content").attr("hidden", true);
  $("#add_item_content").attr("hidden", false);
}

function addNewItem_edit(){
  $('#edit_items').select2('close');
  $("#select_item_edit_content").attr("hidden", true);
  $("#add_item_edit_content").attr("hidden", false);
}

function backToSelectItem(){
  $("#select_item_content").attr("hidden", false);
  $("#add_item_content").attr("hidden", true);
}

function backToSelectItem_edit(){
  $("#select_item_edit_content").attr("hidden", false);
  $("#add_item_edit_content").attr("hidden", true);
}

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

//$("#edit_item_modal").hide();

function editItem($id){
    $.ajax({
      type: "GET",
      //data: { data: $id },
      url: baseUrl("eforms/shipping/edit_item/")+$id,
      dataType: "json",
      success: function(data) {
        var url;

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
          
          newOption = new Option(data[0].stock_code+' | '+data[0].description, data[0].stock_code, true, true);
          $('#edit_items').append(newOption).trigger('change');  
          //url = baseUrl("eforms/shipping/asset_lookup_details");
         //$('#types').val(url);
        }else{
     
          $("#edit_items").select2({
            allowClear: true,
            placeholder: 'Select. .',
            dropdownParent: $("#edit_item_modal"),
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
                      onClick='addNewItem_edit()'>+ Add New Item</button>
                      </li>`;
              }
            },
            escapeMarkup: function (markup) {
              return markup;
            }
          });
          
          newOption = new Option(data[0].stock_code+' | '+data[0].description, data[0].stock_code, true, true);
          $('#edit_items').append(newOption).trigger('change');  
         //url = baseUrl("eforms/shipping/item_lookup_details");
         //$('#types').val(url);
        }

        $('#edit_item_form #quantity').val(data[0].quantity);
        $('#edit_item_form #uom').val(data[0].uom);
        $('#edit_item_form #description').val(data[0].description);
        $('#edit_item_form #purpose').val(data[0].item_purpose);
        return data;
      }
    });

    $.validate({
      form : '#edit_item_modal',
      lang: 'en',
      onSuccess : function(form) {
              
              $.ajax({
                  url: baseUrl("eforms/shipping/edit_item_modal/") +$id,
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
                      $("#edit_uom").val("").trigger('change');
                      toastr.success(data.toastr_msg, "Item updated successfully!", 5000);
                      $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
                  }
              });
          return false;
      },
    });
}

$("#edit_items").on("select2:select", function() {
  $.ajax({
    type: "GET",
    data: { data: $("#edit_items option:selected").attr("value") },
    //url: url,
    url: baseUrl("eforms/shipping/item_lookup_details"),
    dataType: "json",
    success: function(data) {
      $("#edit_item_modal #quantity").val("1");
      $("#edit_item_modal #uom").val(data.results.uom);
      $("#edit_item_modal #description").val(data.results.description);
    }
  });

});


function deleteItem($id){
  $('#delete_modal').modal('show');
  $.validate({
    form : '#delete_form',
    lang: 'en',
    onSuccess : function(form) {
            $.ajax({
                url: baseUrl("eforms/shipping/delete_content/") +$id,
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

$("#clear").on("click", function() {
  $('#clear_asset_modal').modal('show');
  $.validate({
    form : '#clear_asset_form',
    lang: 'en',
    onSuccess : function(form) {
            $.ajax({
                url: baseUrl("eforms/shipping/clear_contents/"),
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

$("#add_asset_modal").hide();

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

$.validate({
  form : '#add_item_modal',
  lang: 'en',
  onSuccess : function(form) {
          
          $.ajax({
              url: baseUrl("eforms/shipping/add_item_modal/") + param_id,
              type: "POST",
              dataType: "json",
              data: $("#add_item_modal").find("input,textarea,select").serialize(),
              beforeSend: function(){
                  $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
              },
              success: function(data){
                  $('#add_item_modal').modal('hide');
                  tblShipping.ajax.reload();
                  $('#add_item_form')[0].reset();
                  $("#items").val("").trigger('change');
                  toastr.success(data.toastr_msg, "Item added successfully", 5000);
                  $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
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
                  $("#select_item_content").attr("hidden", false);
                  $("#add_item_content").attr("hidden", true);
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
              url: baseUrl("eforms/shipping/add_asset_modal/") + param_id,
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
                  toastr.success(data.toastr_msg, "Asset added successfully", 5000);
                  $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
              }
          });
      return false;
  },
});

var type =  [
  {
    id: "in",
    text: "Internal",
  },
  {
    id: "ex",
    text: "External",
  }
];
$("#type").select2({
  placeholder: 'Select. .',
  width: '100%',
  data: type
  
});

$("#type").on("select2:select", function() {
  if($("#type option:selected").attr("value")=="in"){
    $("#courier").text("");
    $("#ex-left").text("");
    $("#in-left").toggle("show");
   

  }
  if($("#type option:selected").attr("value")=="ex"){
    $("#courier").append("<label class='col-3 col-form-label'>Courier & Waybill #: </label><div class='col-9'><input class='form-control m-input' type='waybill' name='waybill' id='transporter'/></div>");
    $("#ex-left").append("<div class='form-group m-form__group row'><label class='col-2 col-form-label'>Ship to: </label><div class='col-10'><input type='text' name='ship_to_ex' class='form-control m-input'/></div></div><div class='form-group m-form__group row'><label class='col-2 col-form-label'>Company:</label><div class='col-10'><input type='text' name='company_ex' class='form-control m-input'/></div></div><div class='form-group m-form__group row'><label class='col-2 col-form-label'>Department:</label><div class='col-10'><input type='text' name='department_ex' class='form-control m-input'/></div></div><div class='form-group m-form__group row'><label class='col-2 col-form-label'>Address:</label><div class='col-10'><textarea name='address_ex' class='form-control' rows='4' data-validation='required'></textarea></div></div><div class='form-group m-form__group row'><label class='col-2 col-form-label'>Shipping Date:</label><div class='col-10 input-group date' onclick='dateTime()' id='due_dt_ex'><input class='form-control m-input' type='text' name='date_ex' id='date' maxlength='22' data-validation='required'/><span class='input-group-addon'><i class='la la-calendar glyphicon-th'></i></span></div></div>");
    $("#in-left").toggle("hide");
  }
});


$.validate({
    form : '#frm_status_new',
    lang: 'en',
    onSuccess : function(form) {

      var disabled = $('#frm_status_new').find('textarea:disabled').removeAttr('disabled');
      if(tblShipping.data().length !== 0) {
        $.ajax({
          url: baseUrl("eforms/shipping/save_content_body"),
          type: "POST",
          data: $("#frm_status_new").find("input,select,textarea").serialize(),
          beforeSend: function(){
              $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
          },
          success: function(data){
            //alert(data);
            if(data){
              tblShipping.ajax.reload();
              disabled.attr('disabled','disabled');
              window.location.href = baseUrl("eforms/shipping/masterfile", toastr.success(data.toastr_msg, "Successfully saved!", 5000));
              
            }else{
                tblShipping.ajax.reload();
                disabled.attr('disabled','disabled');
                toastr.error(data.toastr_msg, "Error!", 5000);
            }
            $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
          }
        });  
      }else{
          toastr.error(data.toastr_msg, "No content for saving!", 5000);   
         
      }
      return false;  
    }
  });



$("input[name=type]").on("click", function(){
  if($("input[name=type]:checked").val()=="service"){
    $("#service_component").toggle("show");
    $("#other_component").text("");
    $("#others_remarks").text("");
  }else{
      $("#other_component").append("<div class='form-group m-form__group row'><label class='col-3 col-form-label'>Remarks: </label><div class='col-9'><textarea class='form-control m-input' id='others_remarks' name='others_remarks' rows='4' data-validation='required'></textarea></div></div>");
      $("#service_component").toggle("hide");
      $("#driver").text("");
      $("#service").text("");
      $("#service").val("");
      $("#driver").val("");
  }
});

$('#due_dt').datetimepicker({
  todayHighlight: true,
  autoclose: true,
  pickerPosition: 'bottom-left',
  todayBtn: true,
  format: 'mm/dd/yyyy h:i',
    //maxView: 4,
    //minView: 2,
});



function dateTime(){
  $('#ex #due_dt_ex').datetimepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    format: 'mm/dd/yyyy h:i',
    //maxView: 4,
    //minView: 2,
  });
}

$("#clear_asset_modal").hide();
$("#delete_modal").hide();