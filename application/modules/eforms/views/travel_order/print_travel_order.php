<div class="m-content">
	<div class="m-portlet m-portlet--mobile">
		<div class="m-portlet__body" id="printableArea">
				<div class="form-group m-form__group row">
					<div class="col-xs-12 table-responsive">
            <div class="row">   
              <table style="font-size:small;" width="100%" border="0">
                <tr>
                  <td width="35%"><h2><div id="company"></div></h2></td>
                  <td style="text-align: right;"  width="30%"><h2><p><small>TRAVEL ORDER</small><p></h2></td>
                  <td style="text-align: right;"  width="35%"><h2><p><div id="reference_no"></p></div></h2></td>
                </tr>
              </table>
            </div>
            <hr style="margin:0px 0px 5px 0px;border-top: 1px dashed black;">
            <div class="row">
              <div class="col-sm-12">
                <table style="font-size:small;" width="100%" border="0">
                  <tr>
                    <td width="15%">Type</td>
                    <td width="50%"><div id="type"></div></td>
                    <td width="10%">Personnel</td>
                    <td width="20%"><div id="personnels"></div></td>
                  </tr>
                  <tr>
                    <td width="15%">Durations</td>
                    <td width="50%"><div id="duration"></div></td>
                  </tr>
                  <tr>
                    <td width="15%">Official Station</td>
                    <td width="35%"><div id="station"></div></td>
                  </tr>
                  <tr>
                    <td width="15%"><div id="vehicle_label"></div></td>
                    <td width="35%"><div id="vehicle"></div></td>
                  </tr>
                  <tr>
                    <td width="15%"><div id="driver_label"></div></td>
                    <td width="35%"><div id="driver"></div></td>          
                  </tr>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-6" style="width:100%;">
              
              </div>
              <div class="col-6" style="width:50%;">
                <table style="font-size:small;" width="100%" border="0">
                  <tr>
                
                  </tr>
                </table>
              </div>
            </div>
            <hr style="margin:0px 0px 5px 0px;border-top: 1px dashed black;">
            <table style="font-size:small;" width="100%" border="0">
              <tr>
                <td style="text-align: left;" width="16%">Departure</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp; rawr</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
              </tr>
              <tr>
                <td width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
              </tr>
              <tr>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
              </tr>
              <tr>
                <td style="border-top:1px dashed black;" style="text-align: left;" width="25%">&nbsp;</td>
                <td style="border-top:0px dashed black;" width="5%">&nbsp;</td>
                <td style="border-top:1px dashed black;" style="text-align: left;" width="25%">&nbsp;</td>
                <td style="border-top:0px dashed black;" width="5%">&nbsp;</td>
                <td style="border-top:1px dashed black;" style="text-align: left;" width="25%">&nbsp;</td>
                <td style="border-top:0px dashed black;" width="5%">&nbsp;</td>
                <td style="border-top:1px dashed black;" style="text-align: left;" width="25%">&nbsp;</td>
                <td style="border-top:0px dashed black;" width="5%">&nbsp;</td>
                <td style="border-top:0px dashed black;" style="text-align: left;" width="25%">&nbsp;</td>
              </tr>
              <tr >
                <td style="text-align: left;" width="16%">Guard on duty</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">Time&emsp;&emsp;&emsp;&emsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">Approving Authority</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">Fuel Tender</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="9" width="100%">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="9" width="100%">&nbsp;</td>
              </tr>
            </table>
            <div class="row">
              <div class="col-xs-12 table-responsive">
                <table style="font-size:small;" class="table table-striped">
                  <thead>
                    <tr>
                      <th style="text-align: left;" width="55%">Destination</th>
                      <th style="text-align: left;"width="40%">Date & Time</th>
                    </tr>
                  </thead>
                  <tbody id="tbody">
              
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
</div>

<script src="http://localhost/portaldev/assets/js/jquery-3.3.1.min.js"></script>
<script src="http://localhost/portaldev/assets/plugins/moment_js/moment.min.js"></script>
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
        $('#reference_no').append(data.data.reference_no.toUpperCase());
        $('#company').append(data.data.company.toUpperCase());
        $('#station').append(': <b>'+data.data.station.toUpperCase()+'</b>');
        $('#type').append(': <b>'+data.type.toUpperCase()+'</b>');
        if (data.data.is_service == 1) {
          $('#vehicle_label').append('Plate Number');
          $('#vehicle').append(': <b>'+data.plateno.toUpperCase()+'</b>');
          $('#driver_label').append('Driver');
          $('#driver').append(': <b>'+data.data.driver.toUpperCase()+'</b>');
        } else if (data.data.is_commute == 1) {
          $('#vehicle_label').append('Vehicle');
          $('#vehicle').append(': <b>COMMUTE</b>');
        } else if (data.data.is_personal == 1) {
          $('#vehicle_label').append('Vehicle');
          $('#vehicle').append(': <b>PERSONAL VEHICLE</b>');
        } else if (data.data.is_others == 1) {
          $('#vehicle_label').append('Vehicle');
          $('#vehicle').append(': <b>'+data.data.others_remarks.toUpperCase()+'</b>');
        }
        view_personnels(getUrlParameter('id'));
        window.print();
        window.close();
      },
      error: function (jqXHR, textStatus, errorThrown)
      {
        alert('Error: "ajax_travel_order_details"');
      }
    });
  }

  function view_personnels(id)
  {
    var getUrlParameter = function getUrlParameter(sParam){var sPageURL = decodeURIComponent(window.location.search.substring(1)),sURLVariables = sPageURL.split('&'),sParameterName,i;for (i = 0; i < sURLVariables.length; i++){sParameterName = sURLVariables[i].split('=');if (sParameterName[0] === sParam){return sParameterName[1] === undefined ? true : sParameterName[1];}}};
    $.ajax({
      url : "http://localhost/portaldev/eforms/travel_order/ajax_view_personnels/" + id,
      type: "POST",
      dataType: "JSON",
      data : {csrf_token : "<?php echo $this->security->get_csrf_hash(); ?>"},
      success: function(data)
      {  
        var names = "";
        for (x = 0; x < data.data.length; x++) {
          names = names + data.data[x][0] + ", ";
        }
        $('#personnels').append(': <b>'+names.slice(0,-2).toUpperCase()+'</b>');
        view_destinations(getUrlParameter('id'));
      },
      error: function (jqXHR, textStatus, errorThrown)
      {
        alert('Error: "ajax_view_personnels"');
      }
    });
  }

    function view_destinations(id)
    {
      var getUrlParameter = function getUrlParameter(sParam){var sPageURL = decodeURIComponent(window.location.search.substring(1)),sURLVariables = sPageURL.split('&'),sParameterName,i;for (i = 0; i < sURLVariables.length; i++){sParameterName = sURLVariables[i].split('=');if (sParameterName[0] === sParam){return sParameterName[1] === undefined ? true : sParameterName[1];}}};
      $.ajax({
        url : "http://localhost/portaldev/eforms/travel_order/ajax_view_destinations_print/" + id,
        type: "POST",
        dataType: "JSON",
        data : {csrf_token : "<?php echo $this->security->get_csrf_hash(); ?>"},
        success: function(data)
        {  
          var temp = "";
          var temp2 = "";
          var duration = "";
          for (x = 0; x < data.data.length; x++) {
            $('#tbody').append('<tr><td>' + data.data[x][0].toUpperCase() + '</td><td>' + data.data[x][1].toUpperCase() + '<br><br><table width="100%"><tr><td>____________________</td><td align="right">____________________</td></tr></table></td></tr>');
            if (data.data.length == 1) {
              if (x == 0) {
                duration = data.data[x][2] + ' - ' + data.data[x][3];
              }
            } else {
              if (x == 0) {
                duration = data.data[x][2];
                temp = duration.split(",");
              } else if (x == parseInt(data.data.length)-1) {
                temp2 = data.data[x][3].split(",");
                if (temp[0] == temp2[0]) {
                  duration = duration + ' - ' + temp2[1].substring(6);
                } else {
                  duration = duration + ' - ' + data.data[x][3];
                }
              }
            }
          }
          $('#duration').append(': <b>'+duration.toUpperCase()+'</b>');
          // print_to();
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          alert('Error: "ajax_view_destinations"');
        }
      });
    }
    function print_to()
    {
      
    }
</script>
