let amt = '';
let empData = {};
var idEmp="";

$("#select_employee").select2({
    placeholder: 'Select',
    width: '100%',
    minimumInputLength: 3,
    ajax: {
      url: baseUrl("eforms/cash_advance/get_employee"),
      dataType: "json",
      delay: 250,
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

$("input[name=deduct_type]").on("click", function(){
  console.log($("input[name=deduct_type]:checked").val());
  if($("input[name=deduct_type]:checked").val()=="fixed"){
    $("#lbl_todeduct").text("Amount to deduct");
    $("#help-block").toggle("hide");
    $("#amt_deduct").val("0");
    $("#amt_deduct").inputmask({ alias : "pesos", removeMaskOnSubmit: true });
    // checkAmt();
  }else{
    $("#lbl_todeduct").text("Percentage to deduct");
    $("#help-block").toggle("show");
    $("#amt_deduct").inputmask({ alias : "percentage", removeMaskOnSubmit: true });
    $("#amt_deduct").val("20%");
    // checkAmt();
  }  
});

function checkAmt(){
  amt = amt.replace(/₱|,/g,'');
  if(amt > 5000.00){
    $("#fileupload").attr('data-validation', 'required');
  }else{
    if($("input[name=deduct_type]:checked").val()=="fixed"){
      $("#fileupload").attr('data-validation', 'required');
    }else{
      $("#fileupload").removeAttr('data-validation');
    }
    
  }
}

$("#amt_deduct").inputmask({ alias : "percentage", removeMaskOnSubmit: true });
$("#amt_deduct").val("20%");

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

function save(){
  $.validate({
    form : '#frm_status_new',
    lang: 'en',
    onSuccess : function(form) {
            var disabled = $('#frm_status_new').find('textarea:disabled').removeAttr('disabled');
            const formData = new FormData($("#frm_status_new")[0]);
            formData.append("csrf_token", _csrf_hash);
            const amt_deduct = parseFloat(formData.get('amt_deduct'));
            if(empData.work_status && empData.company_id && empData.department_id && empData.position && empData.work_mode && empData.employee_status && empData.idno && (empData.date_start && empData.date_start != '0000-00-00')){
              if(amt_deduct<20 || amt_deduct>100 && $('input[name=deduct_type]:checked').val() == "percentage"){
                toastr.error("PERCENTAGE SHOULD NOT BE LESS THAN 20 OR GREATER THAN 100", "Error!", 5000);
                }
              else if(parseFloat($("#amt_deduct").val().replace(/₱\s?/, '').trim()) == 0 || parseFloat($("#amt_deduct").val().replace(/₱\s?/, '').trim()) < 0 && $('input[name=deduct_type]:checked').val() == "fixed"){
                  toastr.error("FIXED DEDUCT AMOUNT SHOULD NOT BE ZERO", "Error!", 5000);
                }
              else {
                $.ajax({
                url: baseUrl("eforms/cash_advance/save_cash_advance"),
                type: "POST",
                dataType: "json",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data){
                        window.location.href = baseUrl("eforms/cash_advance/masterfile", toastr.success(data.toastr_msg, "Successfully saved!", 5000));
                        disabled.attr('disabled','disabled');
                    }else{
                        toastr.error(data.toastr_msg, "Error!", 5000);
                        disabled.attr('disabled','disabled');
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
              });
            }
            }else{
              Swal.fire({
                title: 'Incomplete Employee Details!',
                html: "Please Contact HR Department.",
                icon: 'error',
                showCancelButton: true,
                cancelButtonText: "Cancel",
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                showConfirmButton: false,
              });
            }
        return false;
      },
    });
}

jQuery(document).ready(function(){
  $("#select_employee").change(function(){
    var emp_id = $("#select_employee").val();
    //fileUploadPhoto(emp_id);
  });

});

// var fileUploadPhoto = function(emp_id) {
//   var url = baseUrl("eforms/cash_advance/upload_file");
//   $("#fileupload")
//     .fileupload({
//       url: url,
//       dataType: "json",
//       formData: { csrf_token: _csrf_hash, emp_id : emp_id },
//       done: function(e, data) {
        
//           var result = data.result;
//           console.log(result);
//           if (result.response) {
//               var filePath = result.added_file;
//               var renderFile = result.render_file;
//               $("#file_append").text(renderFile);
//               $("#path").val(filePath);
//               $("#filename").val(renderFile);
//               if(result.extension=="jpg" || result.extension=="png" || result.extension=="JPG" || result.extension=="PNG" || result.extension=="jpeg"){
//                 $("#picture").attr("src", filePath);
//               }else{
//                 $("#picture").attr("src", "");
//               }
//               toastr.success("File Uploaded!", "Successfully saved!", 5000);
//               $("#fileupload").removeAttr('data-validation');
//           } else {
//             toastr.error(result.toastr_msg, "File error", 5000);
//           }
//       }
//   });
// }

// var imagesPreview = function(input, placeToInsertImagePreview) {
//   if (input.files) {
//     var filesAmount = input.files.length;
//     $(".gallery").html('');
//     for (i = 0; i < filesAmount; i++) {
//       var reader = new FileReader();

//       reader.onload = function(event) {
//         $($.parseHTML('<a href="'+event.target.result+'" class="col-lg-4 text-center" style="margin-bottom: 10px" data-lightbox="photos"><img class="img-fluid" src="'+event.target.result+'"><a/>')).appendTo(placeToInsertImagePreview);
//       }
//       reader.readAsDataURL(input.files[i]);
//     }
//   }
// };

// $('#fileupload').on('change', function() {
//   imagesPreview(this, 'div.gallery');
// });


$('#amt_applied').donetyping(function (callback) {
  amt = $(this).val();
  // checkAmt();
});

// var purpose = [
//   { id: "-1", text: "Select an Option" },
//   { id: "Calamities", text: "Fire / Earthquakes / Other Calamities" },
//   { id: "Medical", text: "Medical Check-up / Laboratory / Eyeglasses, etc." },
//   { id: "Hospitalization", text: "Hospitalization (Admission for at least one day)" },
//   { id: "Bereavement", text: "Bereavement of immediate family member (Single: parent & siblings, Married: Spouse & Children)" },
//   { id: "Drivers License", text: "Driver's License/Vehicle Insurance, etc." },
//   { id: "Education", text: "Education Needs (Tuition Fees, Board Exam, etc.)" },
//   { id: "House Repair", text: "House Repair" },
//   { id: "Bills", text: "Water/Electric Bills, etc." },
//   { id: "Others", text: "Others" },
// ];

var purpose = [
  { id: "-1", text: "Select an Option" },
  { id: "Fire / Earthquakes / Other Calamities", text: "Fire / Earthquakes / Other Calamities" },
  { id: "Medical Check-up / Laboratory / Eyeglasses, etc.", text: "Medical Check-up / Laboratory / Eyeglasses, etc." },
  { id: "Hospitalization (Admission for at least one day)", text: "Hospitalization (Admission for at least one day)" },
  { id: "Bereavement of immediate family member (Single: parent & siblings, Married: Spouse & Children)", text: "Bereavement of immediate family member (Single: parent & siblings, Married: Spouse & Children)" },
  { id: "Driver's License/Vehicle Insurance, etc.", text: "Driver's License/Vehicle Insurance, etc." },
  { id: "Education Needs (Tuition Fees, Board Exam, etc.)", text: "Education Needs (Tuition Fees, Board Exam, etc.)" },
  { id: "House Repair", text: "House Repair" },
  { id: "Water/Electric Bills, etc.", text: "Water/Electric Bills, etc." },
  { id: "Others", text: "Others" },
];

$("#select_purpose").select2({
  placeholder: { id: "-1", text : "Select an Option" },
  width: '100%',
  data: purpose
}).on("select2:select", function(e){
  const data = e.params.data;
  
  if(data.id == 'Others'){
    $("#other-purpose").show();
  }else{
    $("#other_purpose").val('');
    $("#other-purpose").hide();
  }
});

let vmTempUploaded = new Vue({
  el: "#tempUploadedAttachment",
  data: { rows: [], counter: 0 },
  methods: {
      getRowCounter(){
          const instance = this;
          let { rows } = instance;
          setTimeout(function(){ 
              instance.counter = rows.length;
          }, 1000);
      }, 
      removeAttachment(index){
          const instance = this;
          let { rows } = this;
          if(typeof rows[index] !== "undefined" && Object.keys(rows[index]).length > 0){ delete rows[index]; }
          const splicedRow = rows;
          let tempRows = [];
          $.each(splicedRow, function(_k, v){ 
              if(typeof v !== "undefined" && v){
                  tempRows.push(v); 
              }
          });
          instance.rows = tempRows;
          instance.getRowCounter();
          if (tempRows.length === 0) {
            $("#temp_fileupload").attr('data-validation', 'required');
          }
          return instance;
      }
  }, mounted: function(){
      this.getRowCounter();
  }
});


var tempFileUpload = function() {
  var url = baseUrl("eforms/cash_advance/temp_upload_file");
  $("#temp_fileupload").fileupload({
          url: url,
          dataType: "json",
          formData: { csrf_token: _csrf_hash, id:idEmp },
          done: function (e, data) {
              var result = data.result;
              if (result.response) {
                  var imageFilename = result.temp_image;
                  var fileType = result.file_type;
                  var filePath = result.file_path;
                  var isImage = result.is_image;
                  var imageUrl = result.added_image;
                  var thumbnail = result.thumbnail;
                  if (!result.is_image){
                    thumbnail = result.svg_icon
                  }
                  vmTempUploaded.rows.push({ image_url: imageUrl, filename: imageFilename, file_type: fileType, is_image: isImage, file_path: filePath, thumbnail:thumbnail  });
                  vmTempUploaded.$mount();
                  toastr.success(result.toastr_msg, "Upload File", 5000);
                  $("#temp_fileupload").removeAttr('data-validation');
              } else {
                  toastr.error(result.toastr_msg, "Upload File", 5000);
              }
          },

      })
};

tempFileUpload();
