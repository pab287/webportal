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

var search_val = "";
param_id = getUrlParameter("id");

var tempData = {};
$.ajax({
  url: baseUrl("eforms/loa/ajax_loa_details/") + param_id,
  type: "GET",
  dataType: "JSON",
  success: function(data) {
    var date1=new Date(data.data.date_from);
    var date2=new Date(data.check);
    data.data.created_dt = moment(data.data.created_dt).format("MMMM DD, YYYY hh:mm A");
    data.data.last_edited_dt = data.data.last_edited_dt!="0000-00-00 00:00:00" ? moment(data.data.last_edited_dt).format("MMMM DD, YYYY hh:mm A") : "--";
    data.data.approved_dt = moment(data.data.approved_dt).format("MMMM DD, YYYY hh:mm A");
    data.data.disapproved_dt = moment(data.data.disapproved_dt).format("MMMM DD, YYYY hh:mm A");
    data.data.cancelled_dt = moment(data.data.cancelled_dt).format("MMMM DD, YYYY hh:mm A");
    data.data.hr_noted_dt = moment(data.data.hr_noted_dt).format("MMMM DD, YYYY hh:mm A");
   
    data.data.ref_yr = data.name;
    data.data.created_by = data.created_by+' on '+data.data.created_dt;
    data.data.last_edited_by = data.last_edited_by+' on '+data.data.last_edited_dt;
    data.data.approved_by = data.approved_by+' on '+data.data.approved_dt;
    data.data.disapproved_by = data.disapproved_by+' on '+data.data.disapproved_dt;
    data.data.cancelled_by = data.cancelled_by+' on '+data.data.cancelled_dt;
    data.data.hr_noted_by = data.hr_noted_by+' on '+data.data.hr_noted_dt;
    switch(data.data.status){
        case "Pending":
            $('#status').append('<div class="m-badge m-badge--warning text-white m-badge--wide m--margin-top-5" role="alert"><strong>Pending</strong></div>');
            if(date1>=date2){
              $("#btnapprove").css("display", "block");
              $("#btndisapprove").css("display", "block");
              $("#btnedit").css("display", "block");
              $("#btncancel").css("display", "block");
          }else{
              $("#btnback2").css("display", "block");
              $("#btnback").css("display","none");
          }
        break;
        case "Approved":
            $('#status').append('<div class="m-badge m-badge--success m-badge--wide m--margin-top-5" role="alert"><strong>Approved</strong></div>');
            if(date1>=date2){
              $("#btnundoapprove").css("display", "block");
              $("#btnnote").css("display", "block");
            }else{
              $("#btnback2").css("display", "block");
              $("#btnback").css("display","none");
            }
            $("#btnprint").css("display", "block");
            document.getElementById('approve').style.removeProperty( 'display' );
            //document.getElementById('approve_remark').style.removeProperty( 'display' );
        break;
        case "Disapproved":
            $('#status').append('<div class="m-badge m-badge--danger m-badge--wide m--margin-top-5" role="alert"><strong>Disapproved</strong></div>');
            if(date1>=date2){
              $("#btnundodisapprove").css("display", "block");
            }else{
              $("#btnback2").css("display", "block");
              $("#btnback").css("display","none");
            }
            document.getElementById('disapprove').style.removeProperty( 'display' );
            //document.getElementById('disapprove_remark').style.removeProperty( 'display' );
        break;
        case "HR Noted":
            $('#status').append('<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>HR Noted</strong></div>');
            if(date1>=date2){
              $("#btnundonote").css("display", "block");
            }else{
              $("#btnback2").css("display", "block");
              $("#btnback").css("display","none");
            }
            document.getElementById('approve').style.removeProperty( 'display' );
            document.getElementById('leave').style.removeProperty( 'display' );
            document.getElementById('hrnoted').style.removeProperty( 'display' );
            //document.getElementById('hr_remark').style.removeProperty( 'display' );
            $("#btnprint").css("display", "block");
        break;
        default:
            $('#status').append('<div class="m-badge m-badge--metal text-white m-badge--wide m--margin-top-5" role="alert"><strong>Cancelled</strong></div>');
            if(date1>=date2){
              $("#btnundocancel").css("display", "block");
            }
            $("#btnback2").css("display", "block");
            $("#btnback").css("display","none");
            $("#cancel").css("display", "block");
            //document.getElementById('cancel_reason').style.removeProperty( 'display' );
        break;
    }
    switch(data.data.type){
        case "1":
            var datefrom = new Date(data.data.date_from);
            var dateto = new Date(data.data.date_to);
            var hours =Math.abs(dateto - datefrom)/36e5;
            var hours= (hours).toFixed(0);
            data.data.ref_month= hours +" hours";
            data.data.type="Undertime";
        break;
        case "2":
            data.data.ref_month= '4 hours';
            data.data.type="Half Day";
        break;
        case "3":
            data.data.ref_month = '8 hours';
            data.data.type="Whole Day";
        break;
        default:
            var datefrom = new Date(data.data.date_from);
            var dateto = new Date(data.data.date_to);
            var hours = Math.abs(dateto - datefrom)/ 36e5;
            var temp=hours%24
            var temp2=Math.floor(hours/24);

            if(temp=="0"){
              data.data.ref_month = temp2 +" Days ";
            }else{
              data.data.ref_month = temp2 +" Days "+ temp +" Hours";
            }
            data.data.type="Others";
        break;
        
    }

    if(data=="0000-00-00 00:00:00"){
        data.data.date_from="";
    }else{
      if(data.data.date_to=="0000-00-00 00:00:00"){
        data.data.date_from=moment(data.data.date_from).format("MM/DD/YYYY");
      }else{
          data.data.date_from=moment(data.data.date_from).format("MM/DD/YYYY hh:mm A")+" - "+moment(data.data.date_to).format("MM/DD/YYYY hh:mm A");
      }
    }

var tblContent = $("#table-previous").DataTable({
  dom: '<"toolbar">rt',
  serverSide: true,
  processing: true,
  ajax: {
    url: baseUrl("eforms/loa/get_previous/") + data.data.employee,
    type: "post",
    dataType: "json",
    data: function(d) {
      (d.csrf_token = _csrf_hash), (d.search["value"] = search_val);
    }
  },
  aaSorting: [],
  searching: true,
  columns: [
        { data: "nature"},
        { data: "type",render: function (data) {return renderTypeHtml(data)}},
        { data: "type", render: function ( data, type, row, meta ) {return formatDifference(data,row)}},
        { data: "date_from", render: function ( data, type, row, meta ) {return formatCalendarDate(data,row)}}, 
        { data: "status",render: function ( data, type, row, meta ) {return renderStatusHtml(row)}},     
    ],
    columnDefs: [
        { targets: [4], className: "statusAlign" },
    ]
});
    vmTab1.vm_tab1 = Object.assign({}, data.data);
  },
  error: function(jqXHR, textStatus, errorThrown) {
    alert("Error get data from ajax");
  }
});

var vmTab1 = new Vue({
  el: "#loaDataRenderer",
  data: { vm_tab1: tempData }
});
function renderTypeHtml(data){
    switch(data){
        case "1":
            return 'Undertime';
        break;
        case "2":
            return 'Half Day';
        break;
        case "3":
            return 'Whole Day';
        break;
        default:
            return 'Others';
        break;
        
    }
}
function formatDifference(data,row){
    switch(data){
        case "1":
        var datefrom = new Date(row.date_from);
var dateto = new Date(row.date_to);
        var hours =Math.abs(dateto - datefrom)/36e5;
        var hours= (hours).toFixed(0);
            return hours +" HOURS";
        break;
        case "2":
            return '4 hours';
        break;
        case "3":
            return '8 hours';
        break;
        default:
             var datefrom = new Date(row.date_from);
var dateto = new Date(row.date_to);
        var hours = Math.abs(dateto - datefrom)/ 36e5;
        var temp=hours%24
         var temp2=Math.floor(hours/24);

            return temp2 +" Days "+ temp +" HOURS";
        break;
        
    }
}
function renderStatusHtml(data){
    switch(data.status){
        case "Pending":
            return '<a href="'+baseUrl("eforms/loa/view_loa?id=")+data.id+'" ><button type="button" title="View" class="btn m-btn--pill btn-warning text-white btn-sm btnView"><strong>Pending</strong></button></a>';
            //return '<div class="m-badge m-badge--warning text-white m-badge--wide" role="alert"><strong>Pending</strong></div>';
        break;
        case "Approved":
            return '<a href="'+baseUrl("eforms/loa/view_loa?id=")+data.id+'" ><button type="button" title="View" class="btn m-btn--pill btn-success text-white btn-sm btnView"><strong>Approved</strong></button></a>';
            //return '<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Approved</strong></div>';
        break;
        case "Disapproved":
            return '<a href="'+baseUrl("eforms/loa/view_loa?id=")+data.id+'" ><button type="button" title="View" class="btn m-btn--pill btn-danger text-white btn-sm btnView"><strong>Disapproved</strong></button></a>';
            //return '<div class="m-badge m-badge--danger m-badge--wide" role="alert"><strong>Disapproved</strong></div>';
        break;
        case "HR Noted":
            return '<a href="'+baseUrl("eforms/loa/view_loa?id=")+data.id+'" ><button type="button" title="View" class="btn m-btn--pill btn-accent text-white btn-sm btnView"><strong>HR Noted</strong></button></a>';
            //return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>HR Noted</strong></div>';
        break;
        default:
            return '<a href="'+baseUrl("eforms/loa/view_loa?id=")+data.id+'" ><button type="button" title="View" class="btn m-btn--pill btn-metal text-white btn-sm btnView"><strong>Cancelled</strong></button></a>';
            //return '<div class="m-badge m-badge--metal text-white m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
        break;
    }
}
function formatCalendarDate(data,row){
    if(data=="0000-00-00 00:00:00"){
        return "";
    }
    else{
      if(row.date_to=="0000-00-00 00:00:00"||row.date_to==""){
          return moment(data).format("MM/DD/YYYY hh:mm A");
      }
      else{
          return moment(data).format("MM/DD/YYYY hh:mm A")+" - "+moment(row.date_to).format("MM/DD/YYYY hh:mm A");
      }
    }
}
function edit_loa(){
window.location.replace(baseUrl("eforms/loa/edit_loa?id=")+param_id);
}
function print(){
window.open(baseUrl("eforms/loa/print_loa/")+param_id);
}
function open_cancel(){     
  $('#modal_form_cancel').modal('show'); // show bootstrap modal
  $('.modal-title').text('Cancel Leave of Absence'); // Set Title to Bootstrap modal title
}

$.validate({
  form : '#form_cancel',
  lang: 'en',
  onSuccess : function(form) {
    $.ajax({
      url : baseUrl("eforms/loa/cancel_loa/") + param_id,
      type: "POST",
      dataType: "JSON",
      // data: { csrf_token: _csrf_hash, cancelled_remarks : $('[name="cancelled_remarks"]').val() },
      data: $('#form_cancel').serialize(),
      success: function(data){
        toastr.success("Loa Canceled Successfully");
        window.location.href = siteUrl('eforms/loa/view_loa?id=' + param_id); 
      },
      error: function (jqXHR, textStatus, errorThrown){
        alert('Error: "ajax_approve"');
      }
    });

    return false;
  }
})

function cancel(){
  $.ajax({
    url : baseUrl("eforms/loa/cancel_loa/") + param_id,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash, cancelled_remarks : $('[name="cancelled_remarks"]').val() },
    success: function(data)
    {
      toastr.success("Loa Canceled Successfully");
      // location.reload();
      window.location.href = siteUrl('eforms/loa/view_loa?id=' + param_id); 
      // window.location.replace(baseUrl("eforms/loa"));
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
      alert('Error: "ajax_approve"');
    }
  });
}

function open_approve(){

  $('#modal_form_approve').modal('show'); // show bootstrap modal
  $('.modal-title').text('Approve Leave of Absence'); // Set Title to Bootstrap modal title
}

$.validate({
  form : '#form_approve',
  lang: 'en',
  onSuccess: function(form){
    $.ajax({
      url : baseUrl("eforms/loa/approve_loa/") + param_id,
      type: "POST",
      dataType: "JSON",
      // data: { csrf_token: _csrf_hash, approved_remarks : $('[name="approved_remarks"]').val() },
      data: $('#form_approve').serialize(),
      success: function(data){
        toastr.success("Loa Approved Successfully");
        window.location.href = siteUrl('eforms/loa/view_loa?id=' + param_id);
      },
      error: function (jqXHR, textStatus, errorThrown){
        alert('Error: "ajax_approve"');
      }
    });

    return false;
  }
})

function approve(){
  $.ajax({
    url : baseUrl("eforms/loa/approve_loa/") + param_id,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash, approved_remarks : $('[name="approved_remarks"]').val() },
    success: function(data)
    {
      toastr.success("Loa Approved Successfully");
      // location.reload();
      window.location.href = siteUrl('eforms/loa/view_loa?id=' + param_id);
      // window.location.replace(baseUrl("eforms/loa"));
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
      alert('Error: "ajax_approve"');
    }
  });
}

function open_dis(){
  $('#modal_form_disapprove').modal('show'); // show bootstrap modal
  $('.modal-title').text('Disapprove Leave of Absence'); // Set Title to Bootstrap modal title
}

$.validate({
  form : '#form_disapprove',
  lang: 'en',
  onSuccess : function(form) {
      $.ajax({
        url : baseUrl("eforms/loa/disapprove_loa/") + param_id,
        type: "POST",
        dataType: "JSON",
        // data: { csrf_token: _csrf_hash, disapproved_remarks : $('[name="disapproved_remarks"]').val() },
        data: $("#form_disapprove").serialize(),
        success: function(data){
          toastr.success("Loa Disapproved Successfully");
          window.location.href = siteUrl('eforms/loa/view_loa?id=' + param_id);
        },
        error: function (jqXHR, textStatus, errorThrown){
          alert('Error adding / update data');
        }
      });

    return false;
  },
}); 

function disapprove(){
  $.validate({
    form : '#form_disapprove',
    lang: 'en',
    onSuccess : function(form) {
      // ajax delete data to database
        $.ajax({
          url : baseUrl("eforms/loa/disapprove_loa/") + param_id,
          type: "POST",
          dataType: "JSON",
          data: { csrf_token: _csrf_hash, disapproved_remarks : $('[name="disapproved_remarks"]').val() },
          success: function(data)
          {
            toastr.success("Loa Disapproved Successfully");
            // location.reload();
            window.location.href = siteUrl('eforms/loa/view_loa?id=' + param_id);
          // window.location.replace(baseUrl("eforms/loa"));
            
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error adding / update data');
          }
      });
    },
  });               
}
function open_undo_disapprove() 
{
  
  $('#modal_form_undo_disapprove').modal('show'); // show bootstrap modal
  $('.modal-title').text('Undo'); // Set Title to Bootstrap modal title
}
function undo_disapprove(){
 
    // ajax delete data to database
      $.ajax({
        url : baseUrl("eforms/loa/undo_disapprove_loa/") + param_id,
        type: "POST",
        dataType: "JSON",
        data:  { csrf_token: _csrf_hash },
        success: function(data)
        {
          toastr.success("Loa Undo Disapproval Successful");
          location.reload();
        // window.location.replace(baseUrl("eforms/loa"));
          
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
        }
    });
      
  
}
function open_undo_approve() 
{
  
  $('#modal_form_undo_approve').modal('show'); // show bootstrap modal
  $('.modal-title').text('Undo'); // Set Title to Bootstrap modal title
}
function undo_approve(){
 
    // ajax delete data to database
      $.ajax({
        url : baseUrl("eforms/loa/undo_approve_loa/") + param_id,
        type: "POST",
        dataType: "JSON",
        data:  { csrf_token: _csrf_hash },
        success: function(data)
        {
          toastr.success("Loa Undo approval Successful");
          location.reload();
        // window.location.replace(baseUrl("eforms/loa"));
          
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
        }
    });
      
  
}
function open_restore() 
{
  
  $('#modal_form_restore').modal('show'); // show bootstrap modal
  $('.modal-title').text('Restore'); // Set Title to Bootstrap modal title
}
function restore(){
  
    // ajax delete data to database
      $.ajax({
        url : baseUrl("eforms/loa/undo_cancel_loa/") + param_id,
        type: "POST",
        dataType: "JSON",
        data:  { csrf_token: _csrf_hash },
        success: function(data)
        {
          toastr.success("Loa Restored Successfully");
          location.reload();
          // window.location.replace(baseUrl("eforms/loa"));          
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
        }
    });
      
  
}
function open_undo_note() 
{
  
  $('#modal_form_undo_note').modal('show'); // show bootstrap modal
  $('.modal-title').text('Undo'); // Set Title to Bootstrap modal title
}
function undo_note(){
  
    // ajax delete data to database
      $.ajax({
        url : baseUrl("eforms/loa/undo_note_loa/") + param_id,
        type: "POST",
        dataType: "JSON",
        data:  { csrf_token: _csrf_hash },
        success: function(data)
        {
          toastr.success("Loa Undo Note Successful");
          location.reload();
          // window.location.replace(baseUrl("eforms/loa"));          
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
        }
    });
      
  
}

function open_note(){
  $('#modal_form_noted').modal('show'); // show bootstrap modal
  $('.modal-title').text('HR Note'); // Set Title to Bootstrap modal title
}

$.validate({
  form : '#form_noted',
  lang: 'en',
  onSuccess : function(form) {
    $.ajax({
      url : baseUrl("eforms/loa/note_loa/") + param_id,
      type: "POST",
      dataType: "JSON",
      // data: { csrf_token: _csrf_hash, hr_noted_remarks : $('[name="hr_noted_remarks"]').val(),hr_noted_pay : $('[name="hr_noted_pay"]').val() },
      data: $("#form_noted").serialize(),
      success: function(data){
        toastr.success("Loa Noted Successfully");
        window.location.href = siteUrl('eforms/loa/view_loa?id=' + param_id);
      },
      error: function (jqXHR, textStatus, errorThrown){
        alert('Error: "ajax_approve"');
      }
    });

    return false;
  },
});

function note(){
  $.ajax({
    url : baseUrl("eforms/loa/note_loa/") + param_id,
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash, hr_noted_remarks : $('[name="hr_noted_remarks"]').val(),hr_noted_pay : $('[name="hr_noted_pay"]').val() },
    success: function(data)
    {
      toastr.success("Loa Noted Successfully");
      // location.reload();
      window.location.href = siteUrl('eforms/loa/view_loa?id=' + param_id);
      // window.location.replace(baseUrl("eforms/loa"));
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
      alert('Error: "ajax_approve"');
    }
  });
}


$(document).ready(function() {
  $("#btnapprove").css("display","none");
  $("#btndisapprove").css("display","none");
  $("#btnedit").css("display","none");
  $("#btncancel").css("display","none");
  $("#btnnote").css("display","none");
  $("#btnundoapprove").css("display","none");
  $("#btnundodisapprove").css("display","none");
  $("#btnundonote").css("display","none");
  $("#btnundocancel").css("display","none");
  $("#btnprint").css("display","none");
  $("#btnback2").css("display","none");
  document.getElementById('hrnoted').style.display = 'none';
  document.getElementById('approve').style.display = 'none';
  document.getElementById('disapprove').style.display = 'none';
  //document.getElementById('approve_remark').style.display = 'none';
  //document.getElementById('disapprove_remark').style.display = 'none';
  document.getElementById('cancel').style.display = 'none';
  //document.getElementById('cancel_reason').style.display = 'none';
  document.getElementById('leave').style.display = 'none';
  //document.getElementById('hr_remark').style.display = 'none';
});