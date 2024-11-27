<div class="m-content">
	<div class="m-portlet m-portlet--mobile">
		<div class="m-portlet__body" id="printableArea">
        <div class="row">
          <div class="col-sm-12">
            <table style="font-size:small;" width="100%" border="0">
              <tr>
                <td style="text-align: center;" ><h4><b>TRIP LOG SHEET</b></h4></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <table style="font-size:small;" width="100%" border="0">
              <tr>
                <td width="33%">Vehicle:&emsp;<u><b id="vehicle"></b></u></td>
                <td width="33%">Plate No:&emsp;<u><b id="plate_no"></b></u></td>
                <td width="34%">Date: _______________________________</td>
              </tr>
              <tr>
                <td width="33%">Driver:&emsp;<u><b id="driver"></b></u></td>
                <td width="33%">&nbsp;</td>
                <td width="34%">TO No:&emsp;<u><b id="ref_no"></b></u></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <table id="table" style="border-right:1px solid black; border-collapse: collapse; font-size:small; margin-top:5px; margin-bottom:5px;" width="100%" border="0">
              <tr style="text-align: center;" >
                <td style="border-bottom:1px solid black; border-collapse: collapse;" width="3%">&nbsp;</td>
                <td style="border-bottom:1px solid black; border-collapse: collapse;" width="22%">&nbsp;</td>
                <td style="border-bottom:1px solid black; border-collapse: collapse;" width="14%">&nbsp;</td>
                <td style="border:1px solid black; border-collapse: collapse;" width="14%" colspan="2" class="tb lb"><b>ODOMETER READING</b></td>
                <td style="border:1px solid black; border-collapse: collapse;" width="24%" colspan="4" class="tb lb"><b>ACTUAL TIME / SIGNATURE(S)</b></td>
                <td style="border:1px solid black; border-collapse: collapse;" width="5%" class="tb lb"><b>TONNAGE</b></td>
                <td style="border:1px solid black; border-collapse: collapse;" width="8%" colspan="2" class="tb lb"><b>FUEL</b></td>
                <td style="border:1px solid black; border-collapse: collapse;" width="10%" class="tb lb rb"><b>REMARKS</b></td>
              </tr>
              <tr align="center" class="tb bb">
                <td style="margin-top:5px;" width="3%" class="lb">No.</td>
                <td width="22%" class="lb">Itineraries</td>
                <td width="14%" style="font-size:small;" class="lb">Date & Time</td>
                <td width="7%" class="lb">Departure</td>
                <td width="7%" class="lb">Arrival</td>
                <td width="6%" class="lb">Departure</td>
                <td width="6%" class="lb">Signature</td>
                <td width="6%" class="lb">Arrival</td>
                <td width="6%" class="lb">Signature</td>
                <td width="5%" class="lb">/m3</td>
                <td width="4%" class="lb">(LTR)</td>
                <td width="6%" class="lb">Signature</td>
                <td width="8%" class="lb rb">&nbsp;</td>
              </tr>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <table style="font-size:small; margin-top:35px;" width="100%" border="0">
              <tr>
                <td width="50%">Submitted By: ____________________________ </td>
                <td width="50%">Received By: ____________________________ </td>
              </tr>
            </table>
          </div>
        </div>
    </div>
  </div>
</div>

<script src="http://localhost/portaldev/assets/js/jquery-3.3.1.min.js"></script>
<script src="http://localhost/portaldev/assets/plugins/moment_js/moment.min.js"></script>
<style type="text/css">.lb{border-left:1px solid black;}.rb{border-right:1px solid black;}.tb{border-top:1px solid black;}.bb{border-bottom:1px solid black;}</style>
<script type="text/javascript">
  $(document).ready(function() {
    travel_order_details();
  });
  function travel_order_details()
  {
    var getUrlParameter = function getUrlParameter(sParam){var sPageURL = decodeURIComponent(window.location.search.substring(1)),sURLVariables = sPageURL.split('&'),sParameterName,i;for (i = 0; i < sURLVariables.length; i++){sParameterName = sURLVariables[i].split('=');if (sParameterName[0] === sParam){return sParameterName[1] === undefined ? true : sParameterName[1];}}};
    $.ajax({
        url : "http://localhost/portaldev/eforms/travel_order/ajax_travel_order_details2/" + getUrlParameter('id'),
      type: "GET",
      dataType: "JSON",
      data : {csrf_token : "<?php echo $this->security->get_csrf_hash(); ?>"},
      success: function(data)
      {    
        console.log(data.data);
        $('#vehicle').append(data.data.vehicle_name.toUpperCase());
        $('#plate_no').append(data.data.vehicle.toUpperCase());
        $('#driver').append(data.data.driver.toUpperCase());
        $('#ref_no').append(data.data.reference_no.toUpperCase());
        view_destinations(getUrlParameter('id'));
      },
      error: function (jqXHR, textStatus, errorThrown)
      {
        alert('Error: "ajax_travel_order_details"');
      }
    });
  }

  function view_destinations(id){
    var getUrlParameter = function getUrlParameter(sParam){var sPageURL = decodeURIComponent(window.location.search.substring(1)),sURLVariables = sPageURL.split('&'),sParameterName,i;for (i = 0; i < sURLVariables.length; i++){sParameterName = sURLVariables[i].split('=');if (sParameterName[0] === sParam){return sParameterName[1] === undefined ? true : sParameterName[1];}}};
    $.ajax({
      url :"http://localhost/portaldev/eforms/travel_order/ajax_view_destinations_print_log/" + id,
      type: "POST",
      dataType: "JSON",
      data : {csrf_token : "<?php echo $this->security->get_csrf_hash(); ?>"},
      success: function(data)
      {  
        console.log(data.data);
        var rows = 12-data.data.length;
        var rownum = data.data.length;
        for (x = 0; x < data.data.length; x++) {
          $('#table').append('<tr align="center" class="tb bb">'+
            '<td class="lb">'+(x+1)+'</td>'+
            '<td class="lb">'+data.data[x][0].toUpperCase()+'</td>'+
            '<td class="lb">'+data.data[x][1].toUpperCase()+'</td>'+
            '<td class="lb">&nbsp;</td>'+
            '<td class="lb">&nbsp;</td>'+
            '<td class="lb">&nbsp;</td>'+
            '<td class="lb">&nbsp;</td>'+
            '<td class="lb">&nbsp;</td>'+
            '<td class="lb">&nbsp;</td>'+
            '<td class="lb">&nbsp;</td>'+
            '<td class="lb">&nbsp;</td>'+
            '<td class="lb">&nbsp;</td>'+
            '<td class="lb rb">&nbsp;</td>'+
          '</tr>'); 
        }
        if (rows > 0 ) {
          for (var i = 0; i < rows; i++) {
            rownum++;
            $('#table').append('<tr align="center" class="tb bb">'+
              '<td class="lb">'+rownum+'</td>'+
              '<td class="lb">&nbsp;<br>&nbsp;</td>'+
              '<td class="lb">&nbsp;</td>'+
              '<td class="lb">&nbsp;</td>'+
              '<td class="lb">&nbsp;</td>'+
              '<td class="lb">&nbsp;</td>'+
              '<td class="lb">&nbsp;</td>'+
              '<td class="lb">&nbsp;</td>'+
              '<td class="lb">&nbsp;</td>'+
              '<td class="lb">&nbsp;</td>'+
              '<td class="lb">&nbsp;</td>'+
              '<td class="lb">&nbsp;</td>'+
              '<td class="lb rb">&nbsp;</td>'+
            '</tr>');          
          };
        }
        $('#table').append('<tr align="center">'+
            '<td width="53%" colspan="5">&nbsp;</td>'+
            '<td width="47%" colspan="8" class="lb rb"><b>Pre-Departure Checklist</b></td>'+
          '</tr>'+
          '<tr align="center">'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td class="lb tb">&nbsp;</td>'+
            '<td class="tb">Remarks</td>'+
            '<td class="tb">&nbsp;</td>'+
            '<td class="lb tb">&nbsp;</td>'+
            '<td class="lb tb">&nbsp;</td>'+
            '<td class="tb">&nbsp;</td>'+
            '<td class="tb">Remarks</td>'+
            '<td class="rb tb">&nbsp;</td>'+
          '</tr>'+
          '<tr align="center">'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td>Battery</td>'+
            '<td class="lb tb">&nbsp;</td>'+
            '<td class="lb tb">&nbsp;</td>'+
            '<td class="tb rb">&nbsp;</td>'+
            '<td>Brakes</td>'+
            '<td class="lb tb">&nbsp;</td>'+
            '<td class="lb tb">&nbsp;</td>'+
            '<td class="tb">&nbsp;</td>'+
            '<td class="rb tb">&nbsp;</td>'+
          '</tr>'+
          '<tr align="center">'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td>Lights</td>'+
            '<td class="lb tb">&nbsp;</td>'+
            '<td class="lb tb">&nbsp;</td>'+
            '<td class="tb rb">&nbsp;</td>'+
            '<td>Air</td>'+
            '<td class="lb tb">&nbsp;</td>'+
            '<td class="lb tb">&nbsp;</td>'+
            '<td class="tb">&nbsp;</td>'+
            '<td class="rb tb">&nbsp;</td>'+
          '</tr>'+
          '<tr align="center">'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td>Oil</td>'+
            '<td class="lb tb">&nbsp;</td>'+
            '<td class="lb tb">&nbsp;</td>'+
            '<td class="tb rb">&nbsp;</td>'+
            '<td>Gas</td>'+
            '<td class="lb tb">&nbsp;</td>'+
            '<td class="lb tb">&nbsp;</td>'+
            '<td class="tb">&nbsp;</td>'+
            '<td class="rb tb">&nbsp;</td>'+
          '</tr>'+
          '<tr align="center">'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td>&nbsp;</td>'+
            '<td>Water</td>'+
            '<td class="lb tb bb">&nbsp;</td>'+
            '<td class="lb tb bb">&nbsp;</td>'+
            '<td class="tb rb bb">&nbsp;</td>'+
            '<td>Tools</td>'+
            '<td class="lb tb bb">&nbsp;</td>'+
            '<td class="lb tb bb">&nbsp;</td>'+
            '<td class="tb bb">&nbsp;</td>'+
            '<td class="rb tb bb">&nbsp;</td>'+
          '</tr>');
          print_log_to();
      },
      error: function (jqXHR, textStatus, errorThrown)
      {
        alert('Error: "ajax_view_destinations"');
      }
    });
  }

  function print_log_to()
  {
    window.print();
    window.close();
  }

</script>
