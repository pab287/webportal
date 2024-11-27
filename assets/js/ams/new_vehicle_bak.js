//submit
$.validate({
  form : '#frm_newVehicle',
	lang: 'en',
	onSuccess : function(form) {
			$.ajax({
				url: form[0].action,
				type: "POST",
				dataType: "json",
				data: $("#frm_newVehicle").find("input,select,textarea,checkbox").serialize(),
				beforeSend: function(){
					$(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
				},
				success: function(data){
					if(data.status){
            toastr.success(data.toastr_msg, "Notification", 5000);
            window.location.replace(baseUrl("ams/vehicles/vehicle_masterfile"));
					}else{
						toastr.error(data.toastr_msg, "Notification", 5000);
					}
					$(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
				}
			});	
		return false;
	},
});

// init select2 company
$("#select2_company").select2({
  placeholder: 'Select option',
  width: '100%',
  ajax: {
    url: baseUrl("ams/assets/get_company_collection"),
    processResults: function (data) {
      return data;
    }
    
  }
});


// init select2 vehicle type
$("#category").select2({
  placeholder: 'Select option',
  width: '100%',
  ajax: {
    url: baseUrl("ams/vehicles/get_vehicle_type"),
    processResults: function (data) {
      return data;
    }
    
  }
});

// init select2 category
$("#select2_category").select2({
  placeholder: 'Select option',
  width: '100%',
  ajax: {
    url: baseUrl("ams/assets/get_category_collection/vehicle"),
    processResults: function (data) {
      return data;
    }
    
  }
});

$('#select2_category').on('select2:select', function (e) {
  var data = e.params.data;
  var id = data.id;
    $.ajax({
      url : baseUrl("ams/assets/type_lookup"),
      type: "POST",
      dataType: "JSON",
      data: { cat_id: data.cat_id, csrf_token : _csrf_hash },
      success: function(data)
      {
        $("#sub_cat_id").select2("destroy");
        
        $("#sub_cat_id option").each(function() {
          $(this).remove();
        });

        //populate select 2 sub cat
        $("#sub_cat_id").select2({placeholder: 'Select option',width: '100%',});
        $.each( data.results, function( key, value ) {
          var newOption = new Option(value.text, value.id, false, true);
          $('#sub_cat_id').append(newOption).trigger('change');
        });     

        $.ajax({
          url : baseUrl("ams/assets/generate_asset_code"),
          type: "POST",
          dataType: "JSON",
          data: { cat_id: id, sub_cat_id: $("#sub_cat_id").val(), csrf_token : _csrf_hash },
          success: function(data)
          {
            $("input[name=assetacode]").val(data.assetacode);
          }
        });
      }
    });


    //populate type select
    $.ajax({
      url : baseUrl("ams/vehicles/type_lookup"),
      type: "POST",
      dataType: "JSON",
      data: { cat_id: data.cat_id, csrf_token : _csrf_hash },
      success: function(data)
      {
        $("#category").select2("destroy");
        
        $("#category option").each(function() {
          $(this).remove();
        });

        //populate select 2 sub cat
        $("#category").select2({placeholder: 'Select option',width: '100%',});
        $.each( data.results, function( key, value ) {
          var newOption = new Option(value.text, value.id, false, true);
          $('#code').append(newOption).trigger('change');
        });     
      }
    });
});

// init select2 sub cat - type
$("#sub_cat_id").select2({
  placeholder: 'Select option',
  width: '100%',
});

$('#sub_cat_id').on('select2:select', function (e) {
  var data = e.params.data;
  var cat_id = $("#select2_category").val();
    $.ajax({
      url : baseUrl("ams/assets/generate_asset_code"),
      type: "POST",
      dataType: "JSON",
      data: { cat_id: cat_id, sub_cat_id: data.id, csrf_token : _csrf_hash },
      success: function(data)
      {
        $("input[name=assetacode]").val(data.assetacode);
      }
    });
});

// init select2 status
$("#select2_status").select2({
  placeholder: 'Select option',
  width: '100%'
});

// init select2 location
$("#select2_area").select2({
  placeholder: 'Select option',
  width: '100%',
  ajax: {
    url: baseUrl("ams/assets/get_area_collection"),
    processResults: function (data) {
      return data;
    }
  }
});

// init select2 location
$("#select2_station").select2({
  placeholder: 'Select option',
  width: '100%',
  ajax: {
    url: baseUrl("ams/assets/get_location_collection"),
    processResults: function (data) {
      return data;
    }
  }
});

// init date_purchased_picker, date_received_picker
$('#date_purchased_picker, #date_received_picker').datepicker({
  todayHighlight: true,
  orientation: "bottom left",
  templates: {
      leftArrow: '<i class="la la-angle-left"></i>',
      rightArrow: '<i class="la la-angle-right"></i>'
  }
});

function isChecked()
{
    if(document.getElementById('isGen').checked) {
        $('[name="assetacode"]').attr('readOnly', 'readOnly');
        $('[name="assetacode"]').attr('required', false);
        if($('[name="assetacode"]').hasClass('err')){
          $('[name="assetacode"]').removeClass('err');
        }
    } else {
        $('[name="assetacode"]').removeAttr('readOnly');
        $('[name="assetacode"]').attr('required', true);
    }

    var category = $("#select2_category").val();
    var type = $("#sub_cat_id").val();

    if(category != null && type != null){
      $.ajax({
        url : baseUrl("ams/assets/generate_asset_code"),
        type: "POST",
        dataType: "JSON",
        data: { cat_id: category, sub_cat_id: type, csrf_token : _csrf_hash },
        success: function(data)
        {
          $("input[name=assetacode]").val(data.assetacode);
        }
      });
    }
}