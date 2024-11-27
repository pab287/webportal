<style type="text/css">
    @media print {
        @page { margin: 0; }
        body { margin: 1cm; }
        img { max-width: 140px; }
    }
</style>
<div class="m-content" style="text-transform: uppercase;">
    <div class="m-portlet__body">
        <form id="print_overtime">
            <?php 
                if(strpos($query->company,"HOME") !== false ){ 
                    $src = base_url("assets/images/comp_logos/HW.png"); 
                    $doc = "HW-FM-HRM-023 Rev 1 01/30/19";
                }else if(strpos($query->company,"GCC") !== false){ 
                    $src = base_url("assets/images/comp_logos/GCC.png"); 
                    $doc = "GCC - 421 Rev. 1 01/30/19";
                }else if(strpos($query->company,"PROXIMA") !== false){
                    $src = base_url("assets/images/comp_logos/PROXIMA.png"); 
                    $doc = "PRX - 426 Rev. 1 01/30/19";
                }else if(strpos($query->company,"WEGA") !== false){ 
                    $src = base_url("assets/images/comp_logos/WEGA.png");
                    $doc = "WEG - 423 Rev. 1 01/30/19"; 
                }else if(strpos($query->company,"ESTANCIA") !== false){ 
                    $src = base_url("assets/images/comp_logos/ESTANCIA.png"); 
                    $doc = "ESM - 423 Rev. 6 06/21/19"; 
                }else { 
                    $src = base_url("assets/images/comp_logos/GCC_inc.png"); 
                    $doc = "GCC - 421 Rev. 1 01/30/19";
                }
            ?>
            <table width="100%" border="0" style="margin:0px; padding: 0px;">
                <tr>
                    <td width="50%">
                        <table style="font-size:11; font-family:Book Antiqua; text-align:center;" width="50%" border="0">
                            <tr>
                                <td><img src="<?php echo $src;?>" style="max-width: 140px;"></td>
                                <td><b><h3>OVERTIME SLIP</h3></b><?php echo $doc; ?></td>
                            </tr>
                        </table>
                        <br>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="20%">Branch/Dept.: </td>
                                <td width="50%">&nbsp;<b><?php echo $query->department; ?></b></td>
                                <td width="18%" style="text-align:right;">Date Filed: </td>
                                <td width="12%">&nbsp;<b><?php echo date("m/d/Y", strtotime($query->created_at)); ?></b></td>
                            </tr>
                        </table>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="20%">To: </td>
                                <td width="80%">&nbsp;<b><?php echo $query->display_name; ?></b></td>
                            </tr>
                        </table>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="20%">Fr: </td>
                                <td width="80%">&nbsp;<b><?php echo $query->display_requested_by; ?></b></td>
                            </tr>
                        </table>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="20%">Position: </td>
                                <td width="80%">&nbsp;<b><?php echo $query->position; ?></b></td>
                            </tr>
                        </table>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="100%">Subject: <b>REQUEST FOR OVERTIME</b></td>
                            </tr>
                        </table>   
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="100%">Please be advised that you are requested to render  </td>
                            </tr>
                        </table>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="50%"><b>OVERTIME WORK</b> on <b><?php echo date("m/d/Y h:i a", strtotime($query->date_from)).' - '.date("m/d/Y h:i a", strtotime($query->date_to)); ?></b></td>
                            </tr>
                        </table>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="100%">To perform the following jobs; </td>
                            </tr>
                        </table>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="100%"><b><?php echo $query->print_purpose; ?></b></td>
                            </tr>
                        </table>
                        <br>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="50%" style="text-align: center;">Conforme: </td>
                                <td width="50%" style="text-align: center;">Requested by: </td>
                            </tr>
                        </table>
                        <br>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="50%" style="text-align: center;"><u><b><?php echo $query->display_name; ?></b></u></td>
                                <td width="50%" style="text-align: center;"><u><b><?php echo $query->display_requested_by; ?></b></u></td>
                            </tr>
                        </table>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="50%" style="text-align: center;">Employee's Sign </td>
                                <td width="50%" style="text-align: center;"> Immediate Supervisor </td>
                            </tr>
                        </table>
                        <br>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="30%">Actual time started:</td>
                                <td width="70%">__________________________________</td>
                            </tr>
                        </table>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="30%">Actual time ended:</td>
                                <td width="70%">__________________________________</td>
                            </tr>
                        </table>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="30%">Actual time worked:</td>
                                <td width="70%">__________________________________</td>
                            </tr>
                        </table>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td width="10%">Remarks:</td>
                                <td width="90%">&nbsp;<b><?php echo $query->requested_remarks; ?></b></td>
                            </tr>
                        </table>
                        <table style="font-size:11; font-family:Book Antiqua;" style="text-align: right" width="50%" border="0">
                            <tr>
                                <td style="text-align: right" width="80%">Approved by:</td>  
                            </tr>
                        </table>
                        <br>
                        <table style="font-size:11; font-family:Book Antiqua;" width="50%" border="0">
                            <tr>
                                <td style="text-align: right" width="100%">&nbsp;<b><?php echo $query->approved_by; ?></b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<script type="text/javascript">
    window.print();
    setTimeout(function () { window.close(); }, 1500);
</script>
   