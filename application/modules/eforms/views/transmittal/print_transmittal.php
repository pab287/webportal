<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Transmittal Print</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!--<style type="text/css">
  	tr td {
		padding: 1px;	
	}
  </style>-->
</head>
<body>
  <!-- Main content -->
  <section class="invoice">
    <div class="row">
      <div class="col-xs-12 table-responsive">
        <table style="font-size:small;" width="100%" border="0">
          <tr>
            <td width="50%"><h1><div id="company_from"></div></h1></td>
            <td align="right" width="30%" style="margin-right: 0px;"><h3>TRANSMITTAL</h3></td>
            <th align="right" width="20%" style="margin-left: 0px; padding-left:0px;"><h1><div style="font-size: 20px;" id="refernce_no"></div></h1></th>
          </tr>
          <tr>
            <td><!--Carlos Hilado Ave., Circumferencial Road, Brgy. Bata, Bacolod City, Negros Occ.--></td>
          </tr>
        </table>
        <hr style="margin:2px 0px 2px 0px;border-top: 1px dashed black;">
        <table style="font-size:small" width="100%">
          <tr>
            <td width="15%">Deliver To</td>
            <td colspan="3" width="85%"><div id="ship_to"></div></td>
          </tr>
          <tr>
            <td width="15%">Date</td>
            <td width="35%"><div id="ship_date"></div></td>
          </tr>
          <tr>
            <td width="15%"><div id="vehicle_label"></div></td>
            <td width="35%"><div id="vehicle"></div></td>
            <td width="15%">Transporter</td>
            <td width="35%"><div id="transporter"></div></td>
          </tr>
          <tr>
            <td width="15%"><div id="driver_label"></div></td>
            <td width="35%"><div id="driver"></div></td>
            <!-- <td><div id="driver_label"></div></td>
            <td>: <b id="driver"></b></td> -->
          </tr>
        </table>
        <hr style="margin:5px 0px 10px 0px;border-top: 1px dashed black;">
        <table style="font-size:small">
        	<tr>
            	<td width="100px"><label>CONTENTS</label></td>
            </tr>
       	</table>
        <hr style="margin:0px;border-top: 1px solid black;">
        <table id="tbody" style="font-size:small">
        </table>
        <hr style="margin:0px;border-top: 1px solid black;">
        <table style="font-size:small; width: 100%">
        	<tr>
            	<td>&nbsp;</td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
            	<td colspan="2"><div class="approvername"></div><hr style="margin:5px 5px 0px 0px;border-top: 1px dashed black;"></td>
                <td colspan="2"><hr style="margin:5px 0px 0px 5px;border-top: 1px dashed black;"></td>
            </tr>
            <tr>
            	<td width="35%">Approving Authority</td>
                <td width="15%" align="right">Time &nbsp;</td>
                <td width="35%"><div style="margin-left: 5px">Guard on duty</div></td>
                <td width="15%" align="right">Time</td>
            </tr>
        </table>
        <table style="font-size:small; width: 100%">
        	<tr>
            	<td width="35%"></td>
                <td width="15%"></td>
                <td width="35%"></td>
                <td width="15%"></td>
            </tr>
        	<tr>
            	<td>&nbsp;</td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2"><hr style="margin:0px 0px 0px 5px;border-top: 1px dashed black;"></td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><div style="margin-left: 5px">Received By</div></td>
                <td align="right">Time</td>
            </tr>
        </table>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</body>
</html>
<script src="http://localhost/portaldev/assets/js/jquery-3.3.1.min.js"></script>
<script src="http://localhost/portaldev/assets/plugins/moment_js/moment.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
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
    populate_head(param_id);
    
    });
  function populate_head(id) {
   
    $.ajax({
      url : "http://localhost/portaldev/eforms/transmittal/populate_head/" + id,
      type : "GET",
      dataType : "JSON",
   
      success : function (data) {
       
        $('#company_from').append(data.company_desc.toUpperCase());
        $('#refernce_no').append(data.data.reference_no.toUpperCase());
        $('#approved_by').append(data.approved.toUpperCase());
        $('.approvername').append(data.approved.toUpperCase());
        $('#ship_to').append(': <b>'+data.deliver.toUpperCase() + '<br>' + '&nbsp;&nbsp;' + data.data.ship_to_address.toUpperCase()+'</b>');
        $('#ship_date').append(': <b>'+moment(data.data.ship_date).format('MMM DD, YYYY hh:mm A').toUpperCase()+'</b>');
        if (data.data.is_service == 1) {
          $('#vehicle_label').append('Plate Number');
          $('#vehicle').append(': <b>'+data.plateno.toUpperCase()+'</b>');
          $('#driver_label').append('Driver');
          $('#driver').append(': <b>'+data.data.driver.toUpperCase()+'</b>');
        } else if (data.data.is_others == 1) {
          $('#vehicle_label').append('Remarks');
          $('#vehicle').append(': <b>'+data.data.others_remarks.toUpperCase()+'</b>');
        }
        $('#transporter').append(': <b>'+data.data.transporter.toUpperCase()+'</b>');
        populate_body(id);
      },
      error : function (jqXHR, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
}
function populate_body(id) {
    $.ajax({
      url : "http://localhost/portaldev/eforms/transmittal/populate_body/" + id,
      type : "GET",
      dataType : "JSON",
   
      success : function (data) {
        $.each(data, function(key, val) {
          $('#tbody').append('<tr>'+
                     '<td width="100%"><b>' + val.description.toUpperCase() + '</b></td>'+
                     '</tr>');  
        });
         window.print();
    window.close();
      },
      error : function (jqXHR, textStatus, errorThrown) {
        alert(errorThrown);
      }
    });
  }
  


</script>