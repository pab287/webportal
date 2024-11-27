<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Borrowing Agreement Form Print</title>
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
            <td width="50%"><h2><div id="company_from"></div></h2></td>
            <td align="right" width="25%"><h2><small>CONTROL #</small></h2></td>
            <td align="right" width="25%"><h2><div id="refernce_no"></div></h2></td>
          </tr>
          <tr>
            <td><!--Carlos Hilado Ave., Circumferencial Road, Brgy. Bata, Bacolod City, Negros Occ.--></td>
          </tr>
        </table>
        <table style="font-size:small" width="100%">
        	<tr align="center">
                <td width="100%" ><b>B O R R O W I N G &nbsp;&nbsp; A G R E E M E N T &nbsp;&nbsp; F O R M &nbsp;&nbsp;(BF)</b></td>
            </tr>
        </table>
        <hr style="margin:10px 0px 3px 0px;border-top: 1px dashed black;">
        <table style="font-size:small" width="100%">
          <tr>
            <td>Issued to : <b id="issued_to"></b></td>
            <td>Department : <b id="department"></b></td>
            <td>Date Issue : <b id="date_issued"></b></td>
          </tr>
        </table>
        <br>
        <table style="font-size:small; width: 100%">
            <tr>
                <td width="100%">
                    <p>&emsp;This is to acknowledge receipt of the following item/s which will be under my temporary accountability and only shall I be released from the said accountability upon return of the said item to the Property Custodian on the agreed schedule.</p>
                </td>
              </tr>
        </table>
        <hr style="margin:0px 0px 5px 0px;border-top: 1px dashed black;">
        <table style="font-size:small; width: 100%" cellspacing="2">
        	<tr>
            	  <td align="center" width="5%"><label>QUANTITY</label></td>
                <td align="center" width="50%"><label>DESCRIPTION</label></td>
                <td align="center" width="10%"><label>BRAND/MODEL</label></td>
                <td align="center" width="15%"><label>DATE DUE</label></td>
                <td align="center" width="10%"><label>UNIT COST</label></td>
                <td align="center" width="10%"><label>AMOUNT</label></td>
            </tr>
       	</table>
        <hr style="margin:0px;border-top: 1px solid black;">
        <table id="tbody" style="font-size:small; width: 100%">
        </table>
        <hr style="margin:5px 0px 5px 0px;border-top: 1px solid black;">
        <table id="tbody1" style="font-size:small; width: 100%">
            <tr>
				<td width="10%"></td>
				<td width="50%"></td> 
				<td width="15%"></td> 
				<td align="center" width="15%"><b>TOTAL</b></td>
				<td align="center" width="10%"><b id="total"></b></td>
			</tr>
        </table>
        <table style="font-size:small; width: 100%">
          <tr>
              <td align="center" width="100%">
                  <p><strong>Warehouse Policy  <br />
                  "All items borrowed anytime during the week must be returned on Saturday."<strong></p>
                </td>
            </tr>
        	<tr>
            	<td width="100%">
                	<p>&emsp;I understand that the usage of all the items listed above is for official use only. Furthermore, I agree to pay GC & C Inc. the full
                  replacement cost or value of any items lost or damaged and allows the company to deduct it from my salary.</p>
                </td>
            </tr>
        </table>
        <table style="font-size:small; width: 100%; margin-top: 10px" border="0">
        	<tr>
            	<td width="25%" colspan="2">Issued by :</td>
                <td width="25%" colspan="2" style="margin:0px 0px 0px 5px">Received by :</td>
                <td width="25%" colspan="2" style="margin:0px 0px 0px 5px">Returned by :</td>
                <td width="25%" colspan="2" style="margin:0px 0px 0px 5px">Acknowledge by :</td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
            </tr>
        	<tr>
            	<td width="25%" colspan="2" align="left"><label id="created_by"></label></td>
                <td width="25%" colspan="2" align="left"><label id="issued_by"></label></td>
                <td width="25%" colspan="2" align="center"><label id=""></label></td>
                <td width="25%" colspan="2" align="center"><label id=""></label></td>
            </tr>
            <tr>
            	<td colspan="2"><hr style="margin:0px 5px 0px 0px;border-top: 1px dashed black;"></td>
                <td colspan="2"><hr style="margin:0px 0px 0px 5px;border-top: 1px dashed black;"></td>
                <td colspan="2"><hr style="margin:0px 0px 0px 5px;border-top: 1px dashed black;"></td>
                <td colspan="2"><hr style="margin:0px 0px 0px 5px;border-top: 1px dashed black;"></td>
            </tr>
        </table>
        <table style="font-size:small; width: 100%; margin-top: 10px" border="0">
          <tr>
            <td><em><font size="-2">Noted by: <b id="acctg_noted_by"></b> - Property Custodian; <b id="hr_noted_by"></b> </font></em></td>
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
<script src="<?php echo site_url("portaldev/assets/js/jquery-3.3.1.min.js"); ?>"></script>
<script src="<?php echo site_url("portaldev/assets/plugins/moment_js/moment.min.js"); ?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		var param_id;
		var getUrlParameter = function getUrlParameter(sParam) 
		{
			var sPageURL = decodeURIComponent(window.location.search.substring(1)),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;
			
			for (i = 0; i < sURLVariables.length; i++) 
			{
				sParameterName = sURLVariables[i].split('=');
				if (sParameterName[0] === sParam) 
				{
					return sParameterName[1] === undefined ? true : sParameterName[1];
				}
			}
		};
	
		param_id = getUrlParameter('id');
		populate_head(param_id);
		
    });
	
	function populate_head(id) {
		$.ajax({
      url : "<?php echo site_url("portaldev/eforms/borrowing/populate_head"); ?>"+"/" + id,
      type : "POST",
      dataType : "JSON",
      data : {csrf_token : "<?php echo $this->security->get_csrf_hash(); ?>"},
			success : function (data) {
				$('#company_from').append(data[0].company);
				$('#refernce_no').append(data[0].reference_no);
				$('#issued_to').append(data[1]);
				$('#department').append(data[0].department);
				$('#date_issued').append(moment(data[0].date_issued).format('MMMM DD, YYYY'));
				$('#issued_by').append(data[1]);
				$('#created_by').append(data[0].created_by);
        $('#acctg_noted_by').append(data[0].acctg_noted_by);
        $('#hr_noted_by').append(data[0].hr_noted_by);
				populate_body(id);
			},
			error : function (jqXHR, textStatus, errorThrown) {
				alert(errorThrown);
			}
		});
	}
	
	function populate_body(id) {
		$.ajax({
			url : "<?php echo site_url("portaldev/eforms/borrowing/populate_body"); ?>"+"/" + id,
      type : "POST",
      dataType : "JSON",
      data : {csrf_token : "<?php echo $this->security->get_csrf_hash(); ?>"},
			success : function (data) {
				var total = 0;
				var cost = 0;
				var amount = 0;
				var y
				$.each(data, function(key, val) {
          y=null;
					cost = val.cost;
					amount = val.amount;
					//var number = 43434;
					if(val.new_due==""){
            var mydate = new Date(val.date_due);
            var y=mydate.toDateString();
            y=y.substr(4);
          }
          else{
            var mydate = new Date(val.new_due);
            var y=mydate.toDateString();
            y=y.substr(4);
          }
      
					$('#tbody').append('<tr>'+
										 '<td align="center" width="5%" style="margin-right: 5px"><b>' + val.quantity + '</b></td>'+
										 '<td align="center" width="50%" style="margin-right: 5px"><b>' + val.asset_name + '</b></td>'+
										 '<td align="center" width="10%" style="margin-right: 5px"><b>' + '&nbsp;' + '</td>'+
                     '<td align="center" width="15%" style="margin-right: 5px"><b>' + y + '</b></td>'+
									   '<td align="center" width="10%" style="margin-right: 5px"><b>' + (cost + "").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") + '</b></td>'+
										 '<td align="center" width="10%"><b>' + (amount + "").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") + '</b></td>'+
									   '</tr>'); 
					total = total + parseFloat(val.amount);
				});
				$('#total').append(total.toLocaleString('en-US', { style: 'currency', currency: 'Php' }));
				print_aa();
			},
			error : function (jqXHR, textStatus, errorThrown) {
				alert(errorThrown);
			}
		});
	}
	
	function print_aa(){
		window.print();
		window.close();
	}
</script>