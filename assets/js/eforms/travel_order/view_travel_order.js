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
var tblPersonnel = $("#table-personnel").DataTable({
  dom: '<"toolbar">rt',
  serverSide: true,
  processing: true,
  aaSorting: [],
  ajax: {
    url: baseUrl("eforms/travel_order/get_personnel/") + param_id,
    type: "post",
    dataType: "json",
    data: function(d) {
      (d.csrf_token = _csrf_hash), (d.search["value"] = search_val);
    }
  },
  searching: true,
  columns: [{ data: "firstname", render: function (data, type, row, meta) {return displayName(row.display_name)}}, { data: "position" }],
  columnDefs: [
    { targets: [0], width: "50%" },
    { targets: [1], width: "50%" }
  ]
});

toggleAccomplishDisable(false);
$("#checkAllBox").click(function () {
  $('#table-destination tbody input[type="checkbox"]:not(.checked').prop('checked', this.checked);
  toggleAccomplishDisable(this.checked);
});

$("#table-destination").on("click", "tbody input[type='checkbox']", function () {
  toggleTableCheckbox();
});

function toggleTableCheckbox(){
  const allCheckboxes = $("#table-destination tbody input[type='checkbox']").length;
  const checkedCheckboxes = $("#table-destination tbody input[type='checkbox']:not(.checked):checked").length;
  const checked = allCheckboxes <= checkedCheckboxes;
  toggleAccomplishDisable(checkedCheckboxes);
  $('#checkAllBox').prop('checked', checked);
}

function toggleAccomplishDisable(checked){
    // $("#btnaccomplish").removeClass('btn-metal');
    // $("#btnaccomplish").addClass('btn-success');
    if(checked){
      // $("#btnaccomplish").removeAttr('disabled');
      $("#btnaccomplish").removeClass('btn-metal');
      $("#btnaccomplish").addClass('btn-success');
    }else{
      // $("#btnaccomplish").attr('disabled',"disabled");
      $("#btnaccomplish").removeClass('btn-success');
      $("#btnaccomplish").addClass('btn-metal');
    }
}

var globalDTdata = {}, globalTemp = [], globalTempSelected = [];
var tblDestination = $("#table-destination").DataTable({
  dom: '<"toolbar">rt',
  serverSide: true,
  processing: true,
  aaSorting: [],
  ajax: {
    url: baseUrl("eforms/travel_order/get_destination/") + param_id,
    type: "post",
    dataType: "json",
    data: function(d) {
      (d.csrf_token = _csrf_hash), (d.search["value"] = search_val);
    }
  },
  drawCallback: function(settings){
    if(typeof settings.json != 'undefined'){
      globalDTdata = Object.assign({}, settings.json.data);
    }
  },
  searching: true,
  columns: [
    {
      width: '2%',
      orderable: false,
      data: null,
      className: 'text-center',
      render: function (data, type, row, meta) {
          var isCheck = '';

          if(row.accomplished == 1){
            isCheck = 'checked';
          //   toggleAccomplishDisable(true); //commented to disable accomplish button
          }

          return `<label class="m-checkbox m-checkbox--air m-checkbox--state-primary" title='Check to Print'> <input `+isCheck+` id="selectedReading" type="checkbox" class="text-gray chckBox ${isCheck}" value="`+row.id+`" name="selected" data-date="`+row.date_to+`" ${isCheck ? 'disabled' : ''}><span></span></label>`;
      }
    },
    // { data: "id", render: function ( data, type, row, meta ) {
    //     console.log(row);
    //     let check;
    //     if(row.travel_order_status == 1 || row.status === "Pending"){
    //       check = "checked";
    //     }
    //     if(row.accomplish == 1 || row.status === "Pending"){
    //       return '<input style="margin-top: 40px; margin-left: 10px; transform: scale(1.3);" type="checkbox" id="destination_check" checked>';
    //     }else if(row.status == "Approved"){
    //       return '<input style="margin-top: 40px; margin-left: 10px; transform: scale(1.3);" type="checkbox" id="destination_check" '+check+'>';
    //     }else{
    //       return '<input style="margin-top: 40px; margin-left: 10px; transform: scale(1.3);" type="checkbox" onchange="checkBox(this, '+row.id+')" '+check+'>';
    //     }
    //   }
    // },
    { data: "destination", render: function ( data, type, row, meta ) {  return specialInstruction(row.destination, row.instructions, row.accomplish)}},
    { data: "date_from", render: function ( data, type, row, meta ) {return formatCalendarDate(data,row)}},
  ],
  columnDefs: [
    { targets: [0], width: "2%" },
    { targets: [1], width: "49%" },
    { targets: [2], width: "49%" }
  ]
});

$(document).on("click", "#destination_check", function (e) {
    var checkbox = $(this);
    e.preventDefault();
    return false;
});

function checkBox($this, $id){
  // console.log($this.checked);
  let stat;
  if($this.checked){
    stat = 1;
  }else{
    stat = 0;
  }

  $.ajax({
    url: baseUrl("eforms/travel_order/destination_status"),
    type: "POST",
    data: {
      csrf_token: _csrf_hash,
      destination_id: $id,
      status: stat
    },
    success: function(resp){
      if(resp == 1){
        $.ajax({
          url: baseUrl("eforms/travel_order/accomplishment_button"),
          type: "POST",
          data: {
            csrf_token: _csrf_hash,
            travel_order_id: param_id
          },
          dataType: "json",
          success: function(resp){
            if(resp == 0){
              $("#btnaccomplish").removeClass('btn-success');
              $("#btnaccomplish").addClass('btn-metal');
              $("#btnaccomplish").attr('disabled',"disabled");
            }else{
              $("#btnaccomplish").removeClass('btn-metal');
              $("#btnaccomplish").addClass('btn-success');
              $("#btnaccomplish").removeAttr('disabled');
            }
            tblDestination.ajax.reload();
          }
        });
      }
    }
  });
}

function formatCalendarDate(data,row){
  if(data=="0000-00-00 00:00:00"){
      return "";
  }
  else{
      return moment(data).format("MM/DD/YYYY hh:mm A")+" - "+moment(row.date_to).format("MM/DD/YYYY hh:mm A");
  }
}

function specialInstruction(destination, instruction, status){
  var str = "", str_instruction = "";
  if(instruction){
    str_instruction += "<br>Special Instruction: "+instruction;
  }else{
    str_instruction += '';
  }

  if(status == 1){
    str += '<br><div class="m-badge m-badge--success m-badge--wide m--margin-top-5" role="alert"><strong>Arrived</strong></div>';
  }else{
    str += '';
  }
  return destination + str_instruction + str;
}
  
var tempData = {};
function displayName($displayName){
  return $displayName;
}

function getCurrentDate(){
  var today = new Date();
  var dd = String(today.getDate()).padStart(2, '0');
  var mm = String(today.getMonth() + 1).padStart(2, '0');
  var yyyy = today.getFullYear();
  return yyyy+'-'+mm+'-'+dd;
}

function dateDifference(_date1, _date2){
  const date1 = new Date(_date1);
  const date2 = new Date(_date2);
  const diffTime = Math.abs(date2 - date1);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays;
}
$(document).ready(function(){

  $("#accomplishment_dt").on("change", function (e) {
    var self = $(e.target);
    self.validate();
  });

$.ajax({
  url: baseUrl("eforms/travel_order/ajax_travel_order_details2/") + param_id,
  type: "GET",
  dataType: "JSON",
  success: function(data) {
    var date1=new Date(data.data.created_dt);
    var date2=new Date(data.check);
    
    data.data.approved_recommend_date = moment(data.data.approved_recommend_date).format(
      "MMMM DD, YYYY hh:mm A"
    );
    data.data.created_dt = moment(data.data.created_dt).format(
      "MMMM DD, YYYY hh:mm A"
    );
    if(data.data.last_edited_by){
      data.data.last_edited_dt = " ON "+moment(data.data.last_edited_dt).format(
        "MMMM DD, YYYY hh:mm A"
      );
    }else{
      data.data.last_edited_dt = ""
    }
    data.data.approved_dt = moment(data.data.approved_dt).format(
      "MMMM DD, YYYY hh:mm A"
    );
    data.data.disapproved_dt = moment(data.data.disapproved_dt).format(
      "MMMM DD, YYYY hh:mm A"
    );
    data.data.cancelled_dt = moment(data.data.cancelled_dt).format(
      "MMMM DD, YYYY hh:mm A"
    );
    data.data.hr_noted_dt = moment(data.data.hr_noted_dt).format(
      "MMMM DD, YYYY hh:mm A"
    );
    data.check=moment(data.check).format(
      "MMMM DD, YYYY hh:mm A"
    );

    if(data.data.last_edited_by){
      data.data.last_edited_by = data.data.last_edited_by;
    }else{
      data.data.last_edited_by = "N/A";
    }
    
    data.data.created_by = data.data.created_by+' on '+data.data.created_dt;
    data.data.last_edited_by = data.data.last_edited_by+data.data.last_edited_dt;
    data.data.approved_by = data.data.approved_by+' on '+data.data.approved_dt;
    data.data.disapproved_by = data.data.disapproved_by+' on '+data.data.disapproved_dt;
    data.data.cancelled_by = data.data.cancelled_by+' on '+data.data.cancelled_dt;
    data.data.hr_noted_by = data.data.hr_noted_by+' on '+data.data.hr_noted_dt;
    data.data.approved_recommend_by = data.data.approved_recommend_by+' on '+data.data.approved_recommend_date;
    data.data.accomplished_by = (data.data.accomplished_by > 0) ? data.data.accomplished_by_name + ' on ' + moment(data.data.accomplishment_dt).format('LLL') : moment(data.data.accomplishment_dt).format('LLL'); 

    document.getElementById('recommend_by').style.display = 'none';

    switch(data.data.status){
      case "Pending":
          $('#status').append('<div class="m-badge text-white m-badge--warning m-badge--wide" role="alert"><strong>For Recommendation</strong></div>');
          $("#btnrecommentapprove").show();
          $("#btndisapprove").show();
          $("#btnedit").show();
          $("#btncancel").show();
        break;
      case "Recommend_Approved":
          $('#status').append('<div class="m-badge text-white m-badge--info m-badge--wide" role="alert"><strong>Pending Approval</strong></div>');
          if(date1>=date2){
            $("#btnapprove").show();
            $("#btndisapprove").show();
            $("#btncancel").show();
            $("#btnundoapproverecommend").show();
            document.getElementById('recommend_by').style.removeProperty( 'display' );
          }else{
            $("#btnback").hide();
            $("#btnback2").show();
        }  

        const now = moment().format('YYYY-MM-DD');
        const dateFromArray = data.destination.map(item => moment(item.date_from).format('YYYY-MM-DD'));

        if (jQuery.inArray(now, dateFromArray) !== -1 && data.data.is_emergency == 0) {
          $("#btnapprove").attr('onclick', 'unable_approve()');
        }

        break;
      case "Approved":
          if(data.data.accomplishment_dt=="0000-00-00 00:00:00" || data.data.accomplished == 0){
            $("#btnaccomplish").show();
            $("#btnundoaccomplish").hide();
            $("#btnundoapprove").show();
          }else{
            $("#btnaccomplish").hide();
            $("#btnundoaccomplish").show();
          }
          if(date1>=date2){
            $("#btnnote").show();
            //$("#btnaccomplish").show();
            document.getElementById('recommend_by').style.removeProperty( 'display' );
          }else{
            $("#btnback").hide();
            $("#btnback2").show();
          }
          if(data.data.accomplishment_dt=="0000-00-00 00:00:00" || data.data.accomplished == 0){
            $('#status').append('<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Approved</strong></div>');
            $("#accomplish_dt").hide();
          }else{
            $('#status').append('<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Accomplished</strong></div>');
            $("#accomplish_dt").show();
          }
          $("#approve").show();
          $("#btnprint").show();
          $("#btnprint2").show();

          var dateDiff = dateDifference(moment(data.data.approved_dt).format("YYYY-MM-DD"), getCurrentDate());
          if(dateDiff > 7){
            $("#btnapprove").hide();
          }

        break;
        case "Disapproved":
            $('#status').append('<div class="m-badge m-badge--danger m-badge--wide" role="alert"><strong>Disapproved</strong></div>');
            if(date1>=date2){
              $("#btnundodisapprove").show();
            }else{
              $("#btnback").show();
              $("#btnback2").hide();
            }
            $("#disapprove").hide();
        break;
        case "HR Noted":
            $('#status').append('<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>HR Noted</strong></div>');
            if(date1>=date2){
            document.getElementById('btnundonote').style.removeProperty( 'display' );
          }else{
            document.getElementById('btnback').style.display = 'none';
            document.getElementById('btnback2').style.removeProperty( 'display' );
          }
            document.getElementById('approve').style.removeProperty( 'display' );
            document.getElementById('noted').style.removeProperty( 'display' );
            document.getElementById('noted_remark').style.removeProperty( 'display' );
            document.getElementById('btnprint').style.removeProperty( 'display' );
            document.getElementById('btnprint2').style.removeProperty( 'display' );
        break;
        default:
            $('#status').append('<div class="m-badge m-badge--metal text-white m-badge--wide" role="alert"><strong>Cancelled</strong></div>');
            if(date1>=date2){
              document.getElementById('btnundocancel').style.removeProperty( 'display' );
            }
              document.getElementById('btnback').style.display = 'none';
              document.getElementById('btnback2').style.removeProperty( 'display' );
              document.getElementById('cancel').style.removeProperty( 'display' );
              document.getElementById('cancel_remark').style.removeProperty( 'display' );
        break;
    }

      if (data.data.is_emergency == 1) {
        $('#status').append('<span class="m-badge m-badge--info m-badge--wide ml-2" style="font-weight: 700"><strong>Emergency</strong></span>');
      }

      if (data.data.is_service == 1) {
        data.data.ref_yr = data.plateno;
        document.getElementById('driver').style.removeProperty( 'display' );           
      }else if (data.data.is_hitch == 1) {
        data.data.ref_yr = data.plateno;
        document.getElementById('driver').style.removeProperty( 'display' );           
      }else if (data.data.is_commute == 1) {
        data.data.ref_yr = "commute";
      }else if (data.data.is_personal == 1) {
        data.data.ref_yr = "personal Vehicle";
      }else if (data.data.is_others == 1) {
        data.data.ref_yr = "Other";
        document.getElementById('other_remark').style.removeProperty( 'display' );
      }    

      if (data.data.is_service == 1) {
        $('#vehicle_label').text('Plate No.');
        $('#vehicle_print').text(data.plateno.toUpperCase());
        $('#driver_label').text('Driver');
        $('#driver_print').text(': '+data.data.driver.toUpperCase());
      }
      if (data.data.is_hitch == 1) {
        $('#vehicle_label').text('Plate No.');
        $('#vehicle_print').text(data.plateno.toUpperCase());
        $('#driver_label').text('Driver');
        $('#driver_print').text(': '+data.data.driver.toUpperCase());
      }
        if (data.data.is_commute == 1) {
        $('#vehicle_label').text('Vehicle');
        $('#vehicle_print').text('COMMUTE');
      } if (data.data.is_personal == 1) {
        $('#vehicle_label').text('Vehicle');
        $('#vehicle_print').text('PERSONAL VEHICLE');
      }  if (data.data.is_others == 1) {
        $('#vehicle_label').text('Remarks');
        $('#vehicle_print').text(data.data.others_remarks.toUpperCase());
      }
    
    vmTab1.vm_tab1 = Object.assign({}, data.data);
    vmPrintArea.vm_to_data = Object.assign({}, data.data);
    vmPrintArea.vm_destination = Object.assign({}, data.destination);
    vmPrintArea.vm_personnel = Object.assign({}, data.personnel);

    $("#table_to_data").find("td:last").html(`<img style="float:right;" src="https://chart.googleapis.com/chart?chs=90x90&cht=qr&chl=`+data.data.reference_no+`&choe=UTF-8" />`);
  },
  error: function(jqXHR, textStatus, errorThrown) {
    alert("Error get data from ajax");
  }
});

});
var vmTab1 = new Vue({
  el: "#travelDataRenderer",
  data: { vm_tab1: tempData }
});

var vmPrintArea = new Vue({
  el: "#formPrintArea",
  data: { vm_to_data: tempData, vm_destination: tempData,  vm_personnel: tempData }
})

function edit_travel(){
  window.location.replace(baseUrl("eforms/travel_order/edit_travel_order?id="+param_id+"&view=true"));
}

function print_form(){
  window.open(baseUrl("eforms/travel_order/print_travel_order?id=")+param_id);
}

function print_trip(){
  window.open(baseUrl("eforms/travel_order/print_travel_order_log?id=")+param_id);
}

function open_cancel(){      
  $('#modal_form_cancel').modal('show'); // show bootstrap modal
  $('.modal-title').text('Cancel Travel Order'); // Set Title to Bootstrap modal title

  $.validate({
    form : '#form_cancel',
    lang: 'en',
    onSuccess: function( form ){
      $.ajax({
        url : baseUrl("eforms/travel_order/cancel_travel/") + param_id,
        type: "POST",
        dataType: "JSON",
        data: { csrf_token: _csrf_hash, cancelled_remarks : $('[name="cancelled_remarks"]').val() },
        beforeSend: function(){
          $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function(data)
        {
          location.reload();
          toastr.success(data.toastr_msg, "Travel order has been cancelled!", 5000); 
          $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          alert('Error: "ajax_approve"');
        }
      });
      return false;
    }
  });
}

function cancel(){
  $.ajax({
    url : baseUrl("eforms/travel_order/cancel_travel/") + param_id,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash, cancelled_remarks : $('[name="cancelled_remarks"]').val() },
    beforeSend: function(){
      $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
    },
    success: function(data)
    {
      location.reload();
      toastr.success(data.toastr_msg, "Travel order has been cancelled!", 5000); 
      $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
      alert('Error: "ajax_approve"');
    }
  });
}

function open_recommend_approve(){
  $('#modal_recommend_approve').modal('show'); // show bootstrap modal
  $('.modal-title').text('Recommendation'); // Set Title to Bootstrap modal title
}

function approve_recommend(){
  $.ajax({
    url : baseUrl("eforms/travel_order/approve_recommend_travel_v2/"),
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash, id: param_id, approved_recommend_remarks: $('[name="approved_recommend_remarks"]').val() },
    beforeSend: function(){
      $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
    },
    success: function(data){
      
      if(data.status == 'success'){
        location.reload();
        toastr.success("Travel order has been approved recommend!");
        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
      } else {
        toastr.error(data.msg, "Error");
      }
    },
    error: function (jqXHR, textStatus, errorThrown){
      alert('Error: "ajax_approve"');
    }
  });
}

function open_approve(){
  $('#modal_form_approve').modal('show'); // show bootstrap modal
  $('.modal-title').text('Approve Travel Order'); // Set Title to Bootstrap modal title
}

function approve(){
  $.ajax({
    url : baseUrl("eforms/travel_order/approve_travel_v2/"),
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash, approve_remarks : $('[name="approved_remarks"]').val(), id: param_id },
    beforeSend: function(){
      $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
    },
    success: function(data){
      
      if(data.status == 'success'){
        location.reload();
        toastr.success("Travel order has been approved!");
        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
      } else if(data.status == 'no_vehicle'){
        toastr.warning(data.msg, "No assigned vehicle or driver");
      } else {
        toastr.error(data.msg, "Error");
      }
    },
    error: function (jqXHR, textStatus, errorThrown){
      alert('Error: "ajax_approve"');
    }
  });
}

function open_disapprove(){
  $('#modal_form_disapprove').modal('show'); // show bootstrap modal
  $('.modal-title').text('Disapprove Travel Order'); // Set Title to Bootstrap modal title
}

function disapprove(){
      $.ajax({
        url : baseUrl("eforms/travel_order/disapprove_travel/") + param_id,
        type: "POST",
        dataType: "JSON",
        data: { csrf_token: _csrf_hash, disapproved_remarks : $('[name="disapproved_remarks"]').val() },
        beforeSend: function(){
          $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function(data)
        {
          location.reload();
          toastr.success(data.toastr_msg, "Travel order has been disapproved!", 5000); 
          $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
        }
    });
}

function undo_disapprove(){
  $('#undo_disapprove_modal').modal('show'); // show bootstrap modal
  $.validate({
    form : '#undo_disapprove_form',
    lang: 'en',
    onSuccess : function(form) {
            $.ajax({
                url: baseUrl("eforms/travel_order/undo_disapprove_travel/") + param_id,
                type: "POST",
                dataType: "json",
                data: $("#undo_disapprove_form").find("input,textarea").serialize(),
                beforeSend: function(){
                  $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                  if(data){
                      $('#undo_disapprove_modal').modal('hide');
                      $('#undo_disapprove_form')[0].reset();
                      location.reload();
                      toastr.success(data.toastr_msg, "Updated successfully!", 5000); 
                  }else{
                      toastr.error(data.toastr_msg, "Error!", 5000);
                  }
                  $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        return false;
      },
  });
}

function undo_approve_recommend(){
  $('#undo_approve_recommend_modal').modal('show'); // show bootstrap modal
  $.validate({
    form : '#undo_approve_recommend_form',
    lang: 'en',
    onSuccess : function(form) {
            $.ajax({
                url: baseUrl("eforms/travel_order/undo_approve_recommend_travel/") + param_id,
                type: "POST",
                dataType: "json",
                data: $("#undo_approve_recommend_form").find("input,textarea").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data){
                        $('#undo_approve_recommend_modal').modal('hide');
                        $('#undo_approve_recommend_form')[0].reset();
                        location.reload();
                        toastr.success(data.toastr_msg, "Updated successfully!", 5000); 
                    }else{
                        toastr.error(data.toastr_msg, "Error", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        return false;
      },
  });
}

function undo_approve(){
  $('#undo_approve_modal').modal('show'); // show bootstrap modal
  $.validate({
    form : '#undo_approve_form',
    lang: 'en',
    onSuccess : function(form) {
            $.ajax({
                url: baseUrl("eforms/travel_order/undo_approve_travel/") + param_id,
                type: "POST",
                dataType: "json",
                data: $("#undo_approve_form").find("input,textarea").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data){
                        $('#undo_approve_modal').modal('hide');
                        $('#undo_approve_form')[0].reset();
                        location.reload();
                        toastr.success(data.toastr_msg, "Updated successfully!", 5000); 
                    }else{
                        toastr.error(data.toastr_msg, "Error", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        return false;
      },
  });
}
              
function undo_note(){
  $('#undo_hr_modal').modal('show'); // show bootstrap modal
  $.validate({
    form : '#undo_hr_form',
    lang: 'en',
    onSuccess : function(form) {
            $.ajax({
                url: baseUrl("eforms/travel_order/undo_note_travel/") + param_id,
                type: "POST",
                dataType: "json",
                data: $("#undo_hr_form").find("input,textarea").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data){
                        $('#undo_hr_modal').modal('hide');
                        $('#undo_hr_form')[0].reset();
                        location.reload();
                        toastr.success(data.toastr_msg, "Updated successfully!", 5000); 
                    }else{
                        toastr.error(data.toastr_msg, "Error!", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        return false;
      },
  });
}

$(document).ready(function(){
  // $.ajax({
  //   url: baseUrl("eforms/travel_order/verify_TO_status"),
  //   type: "POST",
  //   data: {
  //     csrf_token: _csrf_hash,
  //     travel_order_id: param_id
  //   },
  //   dataType: "json",
  //   success: function(resp){
  //     if(resp.status == "Pending" || resp.accomplish == 1){
  //       $("#checkAllBox").attr("checked", "checked");
  //       $("#btnundoapprove").attr("disabled", "disabled");
  //       $("#btnundoapprove").hide();
  //       $("#checkAllBox").on("click", function(e){
  //         var checkbox = $(this);
  //         if (!checkbox.is(":checked")) {
  //           // do the confirmation thing here
  //           e.preventDefault();
            
  //           return false;
  //         }
  //       });

  //       $("#table-destination").find("input[type=checkbox]").on("click", function(e){
  //         var checkbox = $(this);
  //           if (!checkbox.is(":checked")) {
  //             // do the confirmation thing here
  //             e.preventDefault();
  //             return false;
  //           }
  //       });
  //     }else{
  //       $("#checkAllBox").on("change", function(){
  //         $(this).removeAttr("checked");
  //         let status;
  //         if($(this).is(":checked")){
  //           status = 1;
  //           $("#btnaccomplish").removeAttr('disabled');
  //           $("#btnaccomplish").removeClass('btn-metal');
  //           $("#btnaccomplish").addClass('btn-success');
  //         }else{
  //           status = 0;
  //           $("#btnaccomplish").attr('disabled',"disabled");
  //           $("#btnaccomplish").removeClass('btn-success');
  //           $("#btnaccomplish").addClass('btn-metal');
  //         }
  //          $.ajax({
  //             url: baseUrl("eforms/travel_order/set_all_status"),
  //             type: "POST",
  //             data: {
  //               csrf_token: _csrf_hash,
  //               travel_order_id: param_id,
  //               status: status
  //             },
  //             success: function(resp){
  //               if(resp != 0){
  //                 tblDestination.ajax.reload();
  //               }
  //             }
  //           });
  //       });
  //     }
  //   }
  // });

  // $.ajax({
  //   url: baseUrl("eforms/travel_order/accomplishment_button"),
  //   type: "POST",
  //   data: {
  //     csrf_token: _csrf_hash,
  //     travel_order_id: param_id
  //   },
  //   dataType: "json",
  //   success: function(resp){
  //     if(resp == 0){
  //       $("#btnaccomplish").removeClass('btn-success');
  //       $("#btnaccomplish").addClass('btn-metal');
  //       $("#btnaccomplish").attr('disabled',"disabled");
  //     }else if(resp == 2){
  //       $("#btnaccomplish").removeClass('btn-metal');
  //       $("#btnaccomplish").addClass('btn-success');
  //       $("#btnaccomplish").removeAttr('disabled');
  //       $("#checkAllBox").attr("checked", "checked");
  //     }else{
  //       $("#btnaccomplish").removeClass('btn-metal');
  //       $("#btnaccomplish").addClass('btn-success');
  //       $("#btnaccomplish").removeAttr('disabled');
  //       $("#checkAllBox").attr("checked", "checked");
  //     }
  //   }
  // });
});

let startDate = null;
let endDate = null;
let disableToday = false;

function open_accomplish(){
  globalTemp = [];
  globalTempSelected = [];
  let dates = [];

  $("#form_accomplish").trigger('reset');

  $(".chckBox").each(function(i){
        var trig = $(this).is(":checked");
        var date = $(this).data("date");
        if(trig){
          globalTempSelected.push($(this).attr("value"));

          if (!$(this).hasClass('checked')){
            dates.push(date);
          }

        } else {
          globalTemp.push(globalDTdata[i]);
        }
  });
  
  if(globalTemp.length > 0){
    $("#table-selected-destination").show();
    $("#modal_form_accomplish #remarks").show();

    var tblSelected = $("#table-selected-destination").DataTable({
      dom: '<"toolbar">rt',
      destroy: true,
      searching: true,
      ordering: false,
      columns: [
        { data: "destination", render: function ( data, type, row, meta ) {  return specialInstruction(row.destination, row.instructions, row.accomplish)}},
        { data: "date_from", render: function ( data, type, row, meta ) {return formatCalendarDate(data,row)}},
      ],
      columnDefs: [
        { targets: [0], width: "49%" },
        { targets: [1], width: "49%" }
      ]
    });
  
    tblSelected.clear();
    tblSelected.rows.add(globalTemp).draw();
  } else {
    $("#table-selected-destination").hide();
    $("#modal_form_accomplish #remarks").hide();
  }

  if (dates.length == 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Accomplish Travel Order',
      text: 'Please select atleast one (1) Destination to Accomplish.'
    });

    return;
  }

  const uniqueArray = unique(dates);
  var _date = "";
  var _time = "";
  var now = moment().format('YYYY-MM-DD');
  var max = "";
  var min = "";
  var addedDate = "";

  if (uniqueArray.length == 1) {
    _date = new Date(uniqueArray[0]);
    min = moment(_date).format('YYYY-MM-DD');
    _date = moment(_date, 'YYYY-MM-DD HH:mm:ss').add(15, 'days');
    addedDate = _date;

    startDate = moment(min).format('YYYY/MM/DD HH:mm:ss');
  } else {
    min = dates.reduce(function (a, b) { return a < b ? a : b; });
    max = dates.reduce(function (a, b) { return a > b ? a : b; });

    _date = new Date(max);
    _date = moment(_date, 'YYYY-MM-DD HH:mm:ss').add(15, 'days');

    min = moment(min).format('YYYY-MM-DD');
    addedDate = _date;

    startDate = moment(max).format('YYYY/MM/DD HH:mm:ss');
  }

  if (now <= moment(_date).format('YYYY-MM-DD')) {
    time = moment(_date).format('HH:mm:ss');

    if (moment(startDate).format('YYYY-MM-DD') > now) {
      disableToday = false;
    } else if(moment(startDate).format('YYYY-MM-DD') <= now) {
      disableToday = true;
    } else {
      disableToday = true;
    }
    
    _date = now + " " + '23:59';
  }

  endDate = moment(_date).format('YYYY/MM/DD HH:mm:ss');

  $('#due_dt').datetimepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    format: 'yyyy/mm/dd hh:ii:ss',
  });

  $('#due_dt').datetimepicker('setStartDate', moment(startDate).format('YYYY-MM-DD HH:mm'));
  $('#due_dt').datetimepicker('setEndDate', moment(endDate).format('YYYY-MM-DD HH:mm'));

  $("#modal_form_accomplish input[name=param_id]").val(param_id);
  $("#modal_form_accomplish input[name=globalTempSelected]").val(globalTempSelected);
  $('#modal_form_accomplish').modal('show');
  $('#modal_form_accomplish .modal-title').text('Accomplishment Report');

  $('#modal_form_accomplish').on('show.bs.modal', function (e) {
    console.log(disableToday);
    if (!disableToday) {
      $('.datetimepicker .datetimepicker-days .table-condensed tfoot tr:first-child th').removeClass('today');
    }
  });
  
  $('#modal_form_accomplish').on('hidden.bs.modal', function (e) {
    if(!disableToday){
      $(".datetimepicker .datetimepicker-days .table-condensed tr td.today").removeClass('disabled');
      disableToday = false;
    }
  
    $('.datetimepicker .datetimepicker-days .table-condensed tfoot tr:first-child th').addClass('today');
  });
}


function unique(array){
  return array.filter(function(el, index, arr) {
      return index == arr.indexOf(el);
  });
}

$.validate({
  form : '#form_accomplish',
  lang: 'en',
  onSuccess : function(form) {
    var formData = $(form).serialize();
    $.ajax({
        url: baseUrl("eforms/travel_order/accomplish_travel_v2"),
        type: "POST",
        dataType: "json",
        data: formData,
        beforeSend: function(){
          $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function(data){
          if(data.status){
              $('#modal_form_accomplish').modal('hide');
              $('#form_accomplish')[0].reset();
              location.reload();
              toastr.success("Travel order has been accomplished!"); 
          }else{
              toastr.error("Error!");
          }
          $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }
    });
    return false;
    },
});
       
function restore(){
  $('#restore_modal').modal('show'); // show bootstrap modal
  $('.modal-title').text('Restore'); 
  $.validate({
    form : '#restore_form',
    lang: 'en',
    onSuccess : function(form) {
            $.ajax({
                url: baseUrl("eforms/travel_order/undo_cancel_travel/") + param_id,
                type: "POST",
                dataType: "json",
                data: $("#restore_form").find("input,textarea").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data){
                        $('#restore_modal').modal('hide');
                        $('#restore_form')[0].reset();
                        location.reload();
                        toastr.success(data.toastr_msg, "Updated successfully!", 5000); 
                    }else{
                        toastr.error(data.toastr_msg, "Error!", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        return false;
      },
  });
}

function undo_accomplish(){
  $('#undo_accomplishments_modal').modal('show');
  $.validate({
    form : '#undo_accomplishments_form',
    lang: 'en',
    onSuccess : function(form) {
            $.ajax({
                url: baseUrl("eforms/travel_order/undo_accomplish_travel_v2"),
                type: "POST",
                dataType: "json",
                data: { csrf_token: _csrf_hash, param_id: param_id },
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data.status){
                        $('#undo_accomplishments_modal').modal('hide');
                        $('#undo_accomplishments_form')[0].reset();
                        location.reload();
                        toastr.success("Updated successfully!"); 
                    }else{
                        toastr.error("Error!");
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        return false;
      },
  });
}

function open_note(){
  $('#modal_form_noted').modal('show'); // show bootstrap modal
  $('.modal-title').text('HR Note Travel Order'); // Set Title to Bootstrap modal title
}

function noted(){
  $.ajax({
    url : baseUrl("eforms/travel_order/note_travel/") + param_id,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash, hr_noted_remarks : $('[name="hr_noted_remarks"]').val() },
    beforeSend: function(){
      $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
    },
    success: function(data)
    {
      location.reload();
      toastr.success(data.toastr_msg, "Travel order has been noted!", 5000); 
      $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
      alert('Error: "ajax_approve"');
    }
  });
}

$(document).ready(function() {
  $("#approve").hide();
  $("#disapprove").hide();
  $("#cancel").hide();
  $("#noted").hide();
  $("#cancel_remark").hide();
  $("#noted_remark").hide();
  $("#other_remark").hide();
  $("#btnapprove").hide();
  $("#btnrecommentapprove").hide();
  $("#btndisapprove").hide();
  $("#btnedit").hide();
  $("#btncancel").hide();
  $("#btnnote").hide();
  $("#btnaccomplish").hide();
  $("#btnundoapproverecommend").hide();
  $("#btnundoapprove").hide();
  $("#btnundodisapprove").hide();
  $("#btnundocancel").hide();
  $("#btnundonote").hide();
  $("#btnundoaccomplish").hide();
  $("#driver").hide();
  $("#other_remark").hide();
  $("#btnprint").hide();
  $("#btnprint2").hide();
  $("#btnback2").hide();
  $("#accomplish_dt").hide();
});

$("#restore_modal").hide();
$("#undo_approve_recommend_modal").hide();
$("#undo_approve_modal").hide();
$("#undo_disapprove_modal").hide();
$("#undo_hr_modal").hide();
$("#undo_accomplishments_modal").hide();

// $('#due_dt').datetimepicker({
//   todayHighlight: true,
//   autoclose: true,
//   pickerPosition: 'bottom-left',
//   todayBtn: true,
//   format: 'yyyy/mm/dd hh:ii:ss',
// });


function printArea(){
  // setTimeout(function() {
  //     win = window.open(baseUrl('eforms/transmittal/print_transmittal?id=')+param_id);
      
  // }, 1000);

  
  // $.ajax({
  //   url : baseUrl("eforms/travel_order/ajax_travel_order_print/") + param_id,
  //   type: "GET",
  //   dataType: "JSON",
  //   success: function(data){    
  //     $('#reference_no').text(data.data.reference_no.toUpperCase());
  //     $('#company').text(data.data.company.toUpperCase());
  //     $('#station_print').text(data.data.station.toUpperCase());
  //     $('#type').append(': <b>'+data.data.type.toUpperCase()+'</b>');
  //     $('#qr_img').html(`<img style="float:right;" src="https://chart.googleapis.com/chart?chs=90x90&cht=qr&chl=`+data.data.reference_no+`&choe=UTF-8" />`);
  //     if (data.data.is_service == 1) {
  //       $('#vehicle_label').text('Plate No.');
  //       $('#vehicle_print').text(data.plateno.toUpperCase());
  //       $('#driver_label').text('Driver');
  //       $('#driver_print').text(': '+data.data.driver.toUpperCase());
  //     }
  //     if (data.data.is_hitch == 1) {
  //       $('#vehicle_label').text('Plate No.');
  //       $('#vehicle_print').text(data.plateno.toUpperCase());
  //       $('#driver_label').text('Driver');
  //       $('#driver_print').text(': '+data.data.driver.toUpperCase());
  //     }
  //      if (data.data.is_commute == 1) {
  //       $('#vehicle_label').text('Vehicle');
  //       $('#vehicle_print').text('COMMUTE');
  //     } if (data.data.is_personal == 1) {
  //       $('#vehicle_label').text('Vehicle');
  //       $('#vehicle_print').text('PERSONAL VEHICLE');
  //     }  if (data.data.is_others == 1) {
  //       $('#vehicle_label').text('Remarks');
  //       $('#vehicle_print').text(data.data.others_remarks.toUpperCase());
  //     }
      win = window.open();
      var divToPrint = document.getElementById("printableArea");
      win.document.write(divToPrint.outerHTML);
      win.focus();
      win.print();
      win.close();
      

  //   },
  //   error: function (jqXHR, textStatus, errorThrown){
  //     alert('Error: "ajax_travel_order_details"');
  //   }
  // });
  
  // $.ajax({
  //   url : baseUrl("eforms/travel_order/ajax_view_personnels/") + param_id,
  //   type: "GET",
  //   dataType: "JSON",
  //   success: function(data){  
  //     var names = "";
  //     for (x = 0; x < data.data.length; x++) {
  //       names = names + data.data[x][0] + ", ";
  //     }
  //     $('#personnels').text(names.slice(0,-2).toUpperCase());
  //   },
  //   error: function (jqXHR, textStatus, errorThrown){
  //     alert('Error: "ajax_view_personnels"');
  //   }
  // });

  // $.ajax({
  //   url : baseUrl("eforms/travel_order/ajax_view_destinations_print/") + param_id,
  //   type: "GET",
  //   dataType: "JSON",
  //   success: function(data){ 
  //     $('#desitination_print').remove();
  //     var temp = "";
  //     var temp2 = "";
  //     var duration = "";
  //     for (x = 0; x < data.data.length; x++) {
  //       $('#tbody').append('<tr id="desitination_print"><td>' + data.data[x][0].toUpperCase() + '</td><td>' + data.data[x][1].toUpperCase() + '<br><br><table width="100%"><tr><td>____________________</td><td align="right">____________________</td></tr></table></td></tr>');
  //       if (data.data.length == 1) {
  //         if (x == 0) {
  //           duration = data.data[x][2] + ' - ' + data.data[x][3];
  //         }
  //       } else {
  //         if (x == 0) {
  //           duration = data.data[x][2];
  //           temp = duration.split(",");
  //         } else if (x == parseInt(data.data.length)-1) {
  //           temp2 = data.data[x][3].split(",");
  //           if (temp[0] == temp2[0]) {
  //             duration = duration + ' - ' + temp2[1].substring(6);
  //           } else {
  //             duration = duration + ' - ' + data.data[x][3];
  //           }
  //         }
  //       }
  //     }
  //     $('#duration').text(duration.toUpperCase());
  //   },
  //   error: function (jqXHR, textStatus, errorThrown){
  //     alert('Error: "ajax_view_destinations"');
  //   }
  // });
}


function printArea2(){
  $(".remove_table").remove();
  // setTimeout(function() {
  //     win = window.open(baseUrl('eforms/transmittal/print_transmittal?id=')+param_id);
  //     var divToPrint = document.getElementById("printableArea2");
  //     win.document.write(divToPrint.outerHTML);
  //     win.focus();
  //     win.print();
  //     win.close();
  // }, 1000);

  $.ajax({
    url : baseUrl("eforms/travel_order/ajax_travel_order_details2/") + param_id,
    type: "GET",
    dataType: "JSON",
    success: function(data){    
      if (data.data.is_service == 1) {
        $('#plate_no_print2').text(data.data.vehicle.toUpperCase());
        $('#driver_print2').text(data.data.driver.toUpperCase());
      }
      if (data.data.is_hitch == 1) {
        $('#plate_no_print2').text(data.data.vehicle.toUpperCase());
        $('#driver_print2').text(data.data.driver.toUpperCase());
      }
      if (data.data.is_commute == 1) {
        $('#vehicle_print2').text('COMMUTE');
      } if (data.data.is_personal == 1) {
        $('#vehicle_print2').text('PERSONAL VEHICLE');
      }  if (data.data.is_others == 1) {
        $('#vehicle_print2').text(data.data.others_remarks.toUpperCase());
      }

      $('#ref_no_print').text(data.data.reference_no.toUpperCase());
      win = window.open(baseUrl('eforms/travel_order/print_travel_order_log?id=')+param_id);
      setTimeout(function(){
        var divToPrint = document.getElementById("printableArea2");
        win.document.write(divToPrint.outerHTML);
        win.focus();
        win.print();
        win.close();
      }, 1000);
    },
    error: function (jqXHR, textStatus, errorThrown){
      alert('Error: "ajax_travel_order_details"');
    }
  });

  $.ajax({
    url : baseUrl("eforms/travel_order/ajax_view_destinations_print_log/") + param_id,
    type: "GET",
    dataType: "JSON",
    success: function(data){  
      var rows = 12-data.data.length;
      var rownum = data.data.length;
      for (x = 0; x < data.data.length; x++) {
        $('#table').append('<tr class="remove_table" style="border:1px solid black; border-collapse: collapse;" align="center" class="tb bb">'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb">'+(x+1)+'</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb">'+data.data[x][0].toUpperCase()+'</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb">'+data.data[x][1].toUpperCase()+'</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb rb">&nbsp;</td>'+
        '</tr>'); 
      }
      if (rows > 0 ) {
        for (var i = 0; i < rows; i++) {
          rownum++;
          $('#table').append('<tr class="remove_table"  style="border:1px solid black; border-collapse: collapse;" align="center" class="tb bb">'+
            '<td style="border:1px solid black; border-collapse: collapse;" class="lb">'+rownum+'</td>'+
            '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;<br>&nbsp;</td>'+
            '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
            '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
            '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
            '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
            '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
            '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
            '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
            '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
            '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
            '<td style="border:1px solid black; border-collapse: collapse;" class="lb">&nbsp;</td>'+
            '<td style="border:1px solid black; border-collapse: collapse;" class="lb rb">&nbsp;</td>'+
          '</tr>');          
        };
      }
      $('#table').append('<tr class="remove_table" style="border-top:1px solid black; border-collapse: collapse;" align="center">'+
          '<td style="border-top:1px solid black; border-collapse: collapse;" width="53%" colspan="5">&nbsp;</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" width="47%" colspan="8" class="lb rb"><b>Pre-Departure Checklist</b></td>'+
        '</tr>'+
        '<tr class="remove_table" align="center">'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td style="border-left:1px solid black; border-collapse: collapse;" class="lb tb">&nbsp;</td>'+
          '<td style="border-bottom:1px solid black; border-collapse: collapse;" class="tb">Remarks</td>'+
          '<td style="border-right:1px solid black; border-bottom:1px solid black;  border-collapse: collapse;" class="tb">&nbsp;</td>'+
          '<td style="border-top:1px solid black; border-collapse: collapse;" class="lb tb">&nbsp;</td>'+
          '<td style="border-left:1px solid black; border-collapse: collapse;" class="lb tb">&nbsp;</td>'+
          '<td style="border-bottom:1px solid black; border-collapse: collapse;" class="tb">&nbsp;</td>'+
          '<td style="border-bottom:1px solid black; border-collapse: collapse;" class="tb">Remarks</td>'+
          '<td style="border-right:1px solid black; border-bottom:1px solid black;  border-collapse: collapse;" class="rb tb">&nbsp;</td>'+
        '</tr>'+
        '<tr class="remove_table" align="center">'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>Battery</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb tb">&nbsp;</td>'+
          '<td style="border-left:1px solid black; border-bottom:1px solid black; border-collapse: collapse;" class="lb tb">&nbsp;</td>'+
          '<td style="border-right:1px solid black; border-bottom:1px solid black; border-collapse: collapse;" class="tb rb">&nbsp;</td>'+
          '<td>Brakes</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb tb">&nbsp;</td>'+
          '<td style="border-left:1px solid black; border-bottom:1px solid black; border-collapse: collapse;" class="lb tb">&nbsp;</td>'+
          '<td style="border-bottom:1px solid black; border-collapse: collapse;" class="tb">&nbsp;</td>'+
          '<td style="border-right:1px solid black; border-bottom:1px solid black; border-collapse: collapse;" class="rb tb">&nbsp;</td>'+
        '</tr>'+
        '<tr class="remove_table" align="center">'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>Lights</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb tb">&nbsp;</td>'+
          '<td style="border-left:1px solid black; border-bottom:1px solid black;  border-collapse: collapse;" class="lb tb">&nbsp;</td>'+
          '<td style="border-right:1px solid black; border-bottom:1px solid black; border-collapse: collapse;" class="tb rb">&nbsp;</td>'+
          '<td>Air</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb tb">&nbsp;</td>'+
          '<td style="border-left:1px solid black; border-bottom:1px solid black;  border-collapse: collapse;" class="lb tb">&nbsp;</td>'+
          '<td style="border-bottom:1px solid black; border-collapse: collapse;" class="tb">&nbsp;</td>'+
          '<td style="border-right:1px solid black; border-bottom:1px solid black; border-collapse: collapse;" class="rb tb">&nbsp;</td>'+
        '</tr>'+
        '<tr class="remove_table" align="center">'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>Oil</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb tb">&nbsp;</td>'+
          '<td style="border-left:1px solid black; border-bottom:1px solid black; border-collapse: collapse;" class="lb tb">&nbsp;</td>'+
          '<td style="border-right:1px solid black; border-bottom:1px solid black; border-collapse: collapse;" class="tb rb">&nbsp;</td>'+
          '<td>Gas</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb tb">&nbsp;</td>'+
          '<td style="border-left:1px solid black; border-bottom:1px solid black; border-collapse: collapse;" class="lb tb">&nbsp;</td>'+
          '<td style="border-bottom:1px solid black; border-collapse: collapse;" class="tb">&nbsp;</td>'+
          '<td style="border-right:1px solid black; border-bottom:1px solid black; border-collapse: collapse;" class="rb tb">&nbsp;</td>'+
        '</tr>'+
        '<tr class="remove_table" align="center">'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>Water</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb tb bb">&nbsp;</td>'+
          '<td style="border-left:1px solid black; border-bottom:1px solid black; border-collapse: collapse;" class="lb tb bb">&nbsp;</td>'+
          '<td style="border-right:1px solid black; border-bottom:1px solid black; border-collapse: collapse;" class="tb rb bb">&nbsp;</td>'+
          '<td>Tools</td>'+
          '<td style="border:1px solid black; border-collapse: collapse;" class="lb tb bb">&nbsp;</td>'+
          '<td style="border-left:1px solid black; border-bottom:1px solid black; border-collapse: collapse;" class="lb tb bb">&nbsp;</td>'+
          '<td style="border-bottom:1px solid black; border-collapse: collapse;" class="tb bb">&nbsp;</td>'+
          '<td style="border-right:1px solid black; border-bottom:1px solid black; border-collapse: collapse;" class="rb tb bb">&nbsp;</td>'+
        '</tr>');
    },
    error: function (jqXHR, textStatus, errorThrown){
      alert('Error: "ajax_view_destinations"');
    }
  });
}

function unable_approve(){
  const result = getByDate(vmPrintArea.vm_destination, moment().format('YYYY-MM-DD'));

  let html = ``;
  html += '<div>';
    html += `<p class="m-0">Travel orders must be approved at least one day before the travel date. Approval for today’s travel is not allowed.</p>`;

    if (result.length > 0) {
      html += `<ul style="text-align: left; margin-top: 10px">`;
        $.each(result, function(index, item){
          html += `<li>`;
            html += `<p class="mb-1"><b>${item.destination}</b></p>`;
            html += moment(item.date_from).format('lll') + ' - ' + moment(item.date_to).format('lll');
          html += `</li>`;
        });
      html += `</ul>`;
    }
  html += '</div>';


  Swal.fire({
    icon: 'warning',
    title: 'Approve Travel Order',
    html: html
  })
}

function getByDate(dataObj, targetDate) {
  targetDate = new Date(targetDate).toISOString().split('T')[0];
  return Object.values(dataObj).filter(item => {
    const dateOnly = new Date(item.date_from)
      .toISOString()
      .split('T')[0];

    return dateOnly == targetDate;
  });
}