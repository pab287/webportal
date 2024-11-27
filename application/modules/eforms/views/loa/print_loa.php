<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Leave of Absence</title>
    <!-- Tell the browser to be responsive to screen width -->
   
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
    	@media print {
		  @page { margin: 0; }
		  body { margin: 1.6cm; }
		}
    </style>
  </head>
  <body> <!-- onload="window.print();" -->
    <!-- <div class="wrapper"> -->
      <font size="">
      <!-- Main content -->
      <section class="invoice" style="text-transform: uppercase;">

      	<!-- title row -->
        <div class="row">
          <div class="col-xs-12">
            <table style="font-size:small;" width="100%" border="0">
              <tr>
                <td width="25%"><h2><div id="company"></div></h2></td>
                <td width="50%" class="text-center"><h1 style="font: 25px arial;"><strong>Leave of Absence</strong><!-- TO16-0001-06 --><h1></td>
                <td width="25%"><h2><p class="pull-left"><div id="reference_no"></p></div></h2></td>
              </tr>
            </table>
          </div>
        </div>
        <!-- /.row -->
        <?php 

        	$getEmployee = $this->crud->load(array("id"=>$query["employee"]),"gccmaster.tblemployees");
        	if ($getEmployee["suffix"] == "" || $getEmployee["suffix"] == null || $getEmployee["suffix"] == "N/A" || $getEmployee["suffix"] == "NONE") {
			$fullname = $getEmployee["firstname"].' '.substr($getEmployee["middlename"],0,1).'. '.$getEmployee["lastname"];
			} else {
				$fullname = $getEmployee["firstname"].' '.substr($getEmployee["middlename"],0,1).'. '.$getEmployee["lastname"].' '.$getEmployee["suffix"];
			}
if(is_numeric($query["approved_by"])){
  $getEmployee = $this->crud->load(array("id"=>$query["approved_by"]),"gccmaster.tblemployees");
  if ($getEmployee["suffix"] == "" || $getEmployee["suffix"] == null || $getEmployee["suffix"] == "N/A" || $getEmployee["suffix"] == "NONE") {
    $query["approved_by"] = $getEmployee["firstname"].' '.substr($getEmployee["middlename"],0,1).'. '.$getEmployee["lastname"];
} else {
  $query["approved_by"] = $getEmployee["firstname"].' '.substr($getEmployee["middlename"],0,1).'. '.$getEmployee["lastname"].' '.$getEmployee["suffix"];
}
}

			if ($query["type"] == "1") {
			$from = new DateTime($query["date_from"]);
			$to = new DateTime($query["date_to"]);
			$diff = $from->diff($to);
			if ($diff->format('%h') == "0") {
				if ($diff->format('%i') == "1") {
					$duration = $diff->format('%i Minute');
				} else {
					$duration = $diff->format('%i Minutes');
				}
			} else if ($diff->format('%i') == "0") {
				if ($diff->format('%h') == "1") {
					$duration = $diff->format('%h Hour');
				} else {
					$duration = $diff->format('%h Hours');
				}
			} else {
				if ($diff->format('%h') == "1" && $diff->format('%i') == "1") {
					$duration = $diff->format('%h Hour %i Minute');
				} else if ($diff->format('%h') == "1" && $diff->format('%i') != "1") {
					$duration = $diff->format('%h Hour %i Minutes');
				} else if ($diff->format('%h') != "1" && $diff->format('%i') == "1") {
					$duration = $diff->format('%h Hours %i Minute');
				} else {
					$duration = $diff->format('%h Hours %i Minutes');
				}
			}
			$x = explode(' ', $query["date_from"]);
			$y = explode(' ', $query["date_to"]);
			$datetime = date("F d, Y", strtotime($x[0])).' '.date("g:i A", strtotime($x[1])).' - '.date("g:i A", strtotime($y[1])); /*'.date("F d, Y", strtotime($y[0])).' */

		} else if ($query["type"] == "2") {
			$duration = "4 Hours";
			if (substr($query["date_from"], 11, 2) == "8") {
				$ampm = "AM";
			} else {
				$ampm = "PM";
			}
			$datetime = date("F d, Y", strtotime(substr($query["date_from"], 0, -9))).' '.$ampm;
		} else if ($query["type"] == "3") {
			$duration = "1 Day";
			$datetime = date("F d, Y", strtotime(substr($query["date_from"], 0, -9)));
		} else if ($query["type"] == "4") {
			$from = new DateTime($query["date_from"]);
			$to = new DateTime($query["date_to"]);
			$diff = $from->diff($to);
			$m = $diff->format('%m');
			$d = $diff->format('%d');
			$h = $diff->format('%h');
			$i = $diff->format('%i');
			$dur = '';
			for ($x = 0; $x < 4; $x++) { 
				if ($x == 0) {
					if ($m != "0") {
						if ($m == "1") {
							$dur = $m.' Month';
						} else {
							$dur = $m.' Months';
						}
					} 
				} else if ($x == 1) {
					if ($d != "0") {
						if ($d == "1") {
							$dur = $dur.' '.$d.' Day'; 
						} else {
							$dur = $dur.' '.$d.' Days'; 
						}
					}
				} else if ($x == 2) {
					if ($h != "0") {
						if ($h == "1") {
							$dur = $dur.' '.$h.' Hour'; 
						} else {
							$dur = $dur.' '.$h.' Hours'; 
						}
					}
				} else if ($x == 3) {
					if ($i != "0") {
						if ($i == "1") {
							$dur = $dur.' '.$i.' Minute';
						} else {
							$dur = $dur.' '.$i.' Minutes';
						}
					}
				} 
			} 

			$duration = $dur;
			$x = explode(' ', $query["date_from"]);
			$y = explode(' ', $query["date_to"]);
			$datetime = date("F d, Y", strtotime($x[0])).' '.date("g:i A", strtotime($x[1])).' - '.date("F d, Y", strtotime($y[0])).' '.date("g:i A", strtotime($y[1]));
		}

			$type = "";
			if ($query["type"] == "1") {
				$type = "Undertime";
            } else if ($query["type"] == "2") {
				$type = "Half Day";
            } else if ($query["type"] == "3") {
				$type = "Whole Day";
            } else if ($query["type"] == "4") {
				$type = "Others";
            }
        ?>

        <!-- info row -->
        <div class="row">
          <div class="col-sm-12">
            <hr style="margin:0px 0px 5px 0px;border-top: 1px dashed black;">
            <div class="pull-left">
              <table style="padding:0px; font-size:small;" width="100%" border="0" cellpadding="2">
                <tr>
                  <td style="vertical-align:top !important; width:20%;" align="right"><strong>Reference No. : </strong></td>
                  <td style="vertical-align:top !important; width:70%;"><?php echo $query["reference_no"]; ?></td>
                </tr>
                <tr>
                  <td style="vertical-align:top !important; width:20%;" align="right"><strong>Employee Name : </strong></td>
                  <td style="vertical-align:top !important; width:70%;"><?php echo $fullname; ?></td>
                </tr>
                <tr>
                  <td style="vertical-align:top !important; width:20%;" align="right"><strong>Company :</strong></td>
                  <td style="vertical-align:top !important; width:70%;"><?php echo $query["company"];?></td>
                </tr>
                <tr>
                  <td style="vertical-align:top !important; width:20%;" align="right"><strong>Reason for Leave :</strong></td>
                  <td style="vertical-align:top !important; width:70%;"><?php echo $query["reason"];?></td>
                </tr>
                <tr>
                  <td style="vertical-align:top !important; width:20%;" align="right"><strong>Address on Leave :</strong></td>
                  <td style="vertical-align:top !important; width:70%;"><?php echo $query["address"];?></td>        
                </tr>
                <tr>
                  <td style="vertical-align:top !important; width:20%;" align="right"><strong>Number on Leave :</strong></td>
                  <td style="vertical-align:top !important; width:70%;"><?php echo $query["phone"];?></td>      
                </tr>
                <tr>
                  <td style="vertical-align:top !important; width:20%;" align="right"><strong>Type :</strong></td>
                  <td style="vertical-align:top !important; width:70%;"><?php echo $type;?></td>       
                </tr> 
                <tr>
                  <td style="vertical-align:top !important; width:20%;" align="right"><strong>Date & Time :</strong></td>
                  <td style="vertical-align:top !important; width:70%;"><?php echo $datetime; ?></td>          
                </tr>
                <tr>
                  <td style="vertical-align:top !important; width:20%;" align="right"><strong>Duration :</strong></td>
                  <td style="vertical-align:top !important; width:70%;"><?php echo $duration; ?></td>     
                </tr>
                <tr>
                  <td style="vertical-align:top !important; width:20%;" align="right"><strong>Nature of Leave :</strong></td>
                  <td style="vertical-align:top !important; width:70%;"><?php echo $query["nature"]; ?></td>
                </tr>
             
              </table>
			  <table style="font-size:small;" width="100%" border="0" cellpadding="2">
			  	<tr>
                  <td style="vertical-align:top !important; width:22%;" align="right"><strong>Status :</strong></td>
                  <td style="vertical-align:top !important; width:20%;"><?php echo $query["status"]; ?></td>
				  <td style="vertical-align:top !important; width:20%;" align="right"><strong>Approved by :</strong></td>
                  <td style="vertical-align:top !important; width:50%;"><?php echo $query["approved_by"]; ?></td>
                </tr>
			  </table>
            </div>
          </div>
        </div>
        <!-- /.row -->

        

        <hr style="margin:0px 0px 5px 0px;border-top: 1px dashed black;">


      </section>
  	</font>
  </body>

  <script type="text/javascript">

  
    window.print();
    setTimeout(function () { window.close(); }, 100);

     
  </script>
</html>