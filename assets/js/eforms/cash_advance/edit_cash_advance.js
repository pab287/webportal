var emp_id;
var fileUpload =[];
var idEmp="";
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


  $.ajax({
    url: baseUrl("eforms/cash_advance/get_cash_advance_details/") + param_id,
    type: "GET",
    dataType: "JSON",
    success: function(data){
        if(data.deduct_type == "percentage"){
            $('input:radio[name=deduct_type][value=percentage]').click();
            $("#lbl_todeduct").text("Percentage to deduct");
            $("#help-block").toggle("show");
            $("#amt_deduct").inputmask({ alias : "percentage", removeMaskOnSubmit: true });
            $("#amt_deduct").val("20%");
        }else{
            $('input:radio[name=deduct_type][value=fixed]').click();
            $("#lbl_todeduct").text("Amount to deduct");
            $("#amt_deduct").val("0");
            $("#amt_deduct").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
        }
        vmTab1.vm_tab1 = Object.assign({}, data);
        vmTab1.vm_tab1.rows = data.attachments;
        vmTab1.counter = data.attachments.length;
        if (vmTab1.counter>0){
          $("#temp_fileupload").removeAttr('data-validation');
        }
        var newOption = new Option(data.display_name, data.employee, true, true);
        $('#select_employee').append(newOption).trigger('change');
        idEmp = newOption.value;
        lightbox.option({
          'resizeDuration': 200,
          'wrapAround': true
      });
    }
  });


var vmTab1 = new Vue({
  el: "#edit_cash_advance_renderer",
  data: { vm_tab1: {}, counter:0,rows: []},
  methods:{      
    removeAttachment(index){
      this.vm_tab1.rows.splice(index, 1);
      this.counter = this.vm_tab1.rows.length;
      if (this.counter==0){
        $("#temp_fileupload").attr('data-validation', 'required');
      }
  }
  },
  mounted: function(){
        setTimeout(function(){
          var vmData = this.vmTab1.vm_tab1;
          var newOption = new Option(vmData.display_name, vmData.employee, true, true);
          $('#select_employee').append(newOption).trigger('change');
          
        }, 1500);
  }
});

jQuery(document).ready(function(){
  // $("#select_employee").change(function(){
  //   var emp_id = $("#select_employee").val();
  // fileUploadPhoto(emp_id);
  // });

//   $(document).ready(function() {
//     lightbox.option({
//         'resizeDuration': 200,
//         'wrapAround': true
//     });
// });


});

  $("#select_employee").select2({
    placeholder: 'Select Employee',
    width: '100%',
    ajax: {
      url: baseUrl("eforms/cash_advance/get_employee"),
      global: false,
      processResults: function (data) {
        return data;
      }
    }
  });

  $("#select_employee").on("select2:select", function() {
    $.ajax({
      type: "GET",
      data: { data: $("#select_employee option:selected").attr("value") },
      url: baseUrl("eforms/cash_advance/get_employee_detail"),
      dataType: "json",
      success: function(json) {
        if (typeof json.company_details !== "undefined") {
          $("#company").val(json.company_details);
          emp_id = $("#select_employee").val();
          empData = Object.assign({}, json.empData);
          idEmp = emp_id;
        }
      }
    });
  });
  

  $('input[name=amt_applied').keypress(function() {
    return (/\d/.test(String.fromCharCode(event.which) ));
  });
  
  Inputmask.extendAliases({
    pesos: {
              prefix: "₱ ",
              groupSeparator: ".",
              alias: "numeric",
              placeholder: "0",
              autoGroup: !0,
              digits: 2,
              digitsOptional: !1,
              clearMaskOnLostFocus: !1
          }
  });
  
  $("#amt_applied").inputmask({ alias : "pesos", removeMaskOnSubmit: true });

  $('input[name=amt_deduct').change(function() {
    if($('input[name=deduct_type]:checked').val() == "percentage"){
      if(parseFloat($('input[name=amt_deduct]').val()) < 20 || parseFloat($('input[name=amt_deduct]').val()) > 100){
        $("#help-block").text("Percentage should not be less than 20 or greater than 100");
      }else{
        $("#help-block").text("Note: The set amount/percentage shall be deducted from your salary every payday. 20% shall be the minimum rate for CA deduction.");
      }
    }
    return (/\d/.test(String.fromCharCode(event.which) ));
  });

  $("input[name=deduct_type]").on("click", function(){
    if($("input[name=deduct_type]:checked").val()=="fixed"){
      $("#lbl_todeduct").text("Amount to deduct");
      $("#help-block").toggle("hide");
      $("#amt_deduct").val("0");
      $("#amt_deduct").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
    }else{
      $("#lbl_todeduct").text("Percentage to deduct");
      $("#help-block").toggle("show");
      $("#amt_deduct").inputmask({ alias : "percentage", removeMaskOnSubmit: true });
      $("#amt_deduct").val("20%");
  
    }
    
  });


  

  

  $.validate({
    form : '#edit_cash_advance_renderer',
    lang: 'en',
    onSuccess : function(form) {
            var disabled = $('#edit_cash_advance_renderer').find('textarea:disabled').removeAttr('disabled');
            if( parseFloat($("#amt_deduct").val()) < 20 || parseFloat($("#amt_deduct").val()) > 100 && $('input[name=deduct_type]:checked').val() == "percentage"){
              toastr.error("PERCENTAGE SHOULD NOT BE LESS THAN 20 OR GREATER THAN 100", "Error!", 5000);
              }
              else if( parseFloat($("#amt_deduct").val().replace(/₱\s?/, '').trim()) == 0 || parseFloat($("#amt_deduct").val().replace(/₱\s?/, '').trim()) < 0 && $('input[name=deduct_type]:checked').val() == "fixed"){
                toastr.error("FIXED DEDUCT AMOUNT SHOULD NOT BE ZERO", "Error!", 5000);
              }
            else{
              $.ajax({
                url: baseUrl("eforms/cash_advance/update_cash_advance/") + param_id,
                type: "POST",
                dataType: "json",
                data: $("#edit_cash_advance_renderer").find("input,select,textarea").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data){
                        window.location.href = baseUrl("eforms/cash_advance/view_cash_advance?id="+param_id, toastr.success(data.toastr_msg, "Successfully saved!", 5000));
                        disabled.attr('disabled','disabled');    
                    }else{
                        toastr.error(data.toastr_msg, "Error!", 5000);
                        disabled.attr('disabled','disabled');
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            }
        return false;
    },
});



// var fileUploadPhoto = function(emp_id) {
//   var url = baseUrl("eforms/cash_advance/upload_file");
//   $("#fileupload")
//     .fileupload({
//       url: url,
//       dataType: "json",
//       formData: { csrf_token: _csrf_hash, emp_id : emp_id},
//       done: function(e, data) {
//           var result = data.result;
//           if (result.response) {
//               var filePath = result.added_file;
//               var renderFile = result.render_file;
//               $("#file_append").text(renderFile);
//               $("#path").val(filePath);
//               $("#filename").val(renderFile);
//               toastr.success("File Uploaded!", "Successfully saved!", 5000);
//           } else {
//             toastr.error(result.toastr_msg, "File error", 5000);
//           }
//       }
//   });
// }

var tempFileUpload = function() {
  var url = baseUrl("eforms/cash_advance/update_temp_upload_files");
  $("#temp_fileupload").fileupload({
          url: url,
          dataType: "json",
          formData: { csrf_token: _csrf_hash, id:param_id},
          done: function (e, data) {
              var result = data.result;
              if (result.response) {
                  var imageFilename = result.temp_image;
                  var fileType = result.file_type;
                  var filePath = result.file_path;
                  var imageUrl = result.added_image;
                  var pathImg = result.pathImg;
                  var thumbnail = result.thumbnail;
                  if (!result.is_image){
                    thumbnail = result.svg_icon
                  }
                  vmTab1.vm_tab1.rows.push({ path: filePath, filename: imageFilename, extension: fileType, image_url: imageUrl, pathImg:pathImg, thumbnail:thumbnail });
                  vmTab1.counter = vmTab1.vm_tab1.rows.length;
                  vmTab1.$mount();
                  toastr.success(result.toastr_msg, "Upload File", 5000);
                  $("#temp_fileupload").removeAttr('data-validation');
              } else {
                  toastr.error(result.toastr_msg, "Upload File", 5000);
              }
          },

      })
};

tempFileUpload();