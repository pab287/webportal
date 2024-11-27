<div class="m-content">
    <div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Print Cash Advance
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
				</div>
                <div class="m-portlet__body" id="printableArea">
                <form id="print_cash_advance">
                    <div class="row">
                        <div class="col-md-12">
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="50%"><h2><div id="company_from"></div></h2></td>
                                    <td align="right" width="25%"><h2><small>SHIPPING ADVICE</small></h2></td>
                                    <td align="right" width="25%"><h2><div v-text="vm_tab1.reference_no"></div></h2></td>
                                </tr>
                                <tr>
                                    <td><!--Carlos Hilado Ave., Circumferencial Road, Brgy. Bata, Bacolod City, Negros Occ.--></td>
                                </tr>
                            </table>
                            <hr style="margin:0px 0px 5px 0px;border-top: 1px dashed black;">
                            <table width="100%" style="font-size:small" border="0">
                                <tr>
                                    <td width="15%">Ship To</td>
                                    <td width="35%" colspan="3"><div v-text="vm_tab1.firstname+' '+vm_tab1.lastname"></div></td>
                                </tr>
                                <tr>
                                    <td width="15%" valign="top">Location</td>
                                    <td width="35%" colspan="3"><div v-text="vm_tab1.location"></div></td>
                                </tr>
                                <tr>
                                    <td width="15%" valign="top">Department</td>
                                    <td width="35%" colspan="3"><div v-text="vm_tab1.department"></div></td>
                                </tr>
                                <tr>
                                    <td width="15%" valign="top">Company</td>
                                    <td width="35%" colspan="3"><div v-text="vm_tab1.company"></div></td>
                                </tr>
                                <tr>
                                    <td width="15%" valign="top">Exact Address</td>
                                    <td width="35%" colspan="3"><div v-text="vm_tab1.ship_to_address"></div></td>
                                </tr>
                                <tr>
                                    <td width="15%">Date</td>
                                    <td width="35%"><div v-text="moment(vm_tab1.ship_date).format('LL')"></div></td>
                                </tr>
                                <tr>
                                    <td width="15%" valign="top">Plate No.</td>
                                    <td width="35%"><div v-text="vm_tab1.plateno"></div></td>
                                    <td width="15%">Transporter&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td width="35%"><div v-text="vm_tab1.transporter"></div></td>
                                </tr>
                                <tr>
                                    <td width="15%" valign="top">Driver</td>
                                    <td width="35%"><div id="driver"></div></td>
                                </tr>
                            </table>
                            <hr style="margin:10px 0px 5px 0px;border-top: 1px dashed black;">
                           
                            <table class="table table-striped table-bordered" width="100%">
                                <thead>
                                    <tr>
                                        <th>ASSET/STOCK CODE</label</th>
                                        <th>Quantity</th>
                                        <th>NAME/DESCRIPTION</th>
                                        <th>Purpose</th>
                                    </tr>
                                </thead>
                                <tbody> 

                                </tbody>
                            </table>
                    
                            <hr style="margin:0px;border-top: 1px solid black;">
                            <table  style="font-size:small">

                            </table>
                            <hr style="margin:0px;border-top: 1px solid black;">
                                <table style="font-size:small;" width="100%" border="0">
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;<div id="approver"></div></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><hr style="margin:0px 5px 0px 0px;border-bottom: 1px dashed black;"></td>
                                        <td colspan="2"><hr style="margin:0px 0px 0px 5px;border-bottom: 1px dashed black;"></td>
                                    </tr>
                                    <tr>
                                        <td width="35%">Approving Authority</td>
                                        <td width="15%" align="right">Time</td>
                                        <td width="35%"><div style="margin-left: 5px">Guard on duty</div></td>
                                        <td width="15%" align="right">Time</td>
                                    </tr>
                                </table>
                                <table style="font-size:small;" width="100%" border="0">
                                    <tr>
                                        <td width="35%">&nbsp;</td>
                                        <td width="15%">&nbsp;</td>
                                        <td width="35%">&nbsp;</td>
                                        <td width="15%">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td style="border-top: 1px dashed black;"><div style="margin-left: 5px">Received By(Printed Name and Signature)</div></td>
                                        <td align="right" style="border-top: 1px dashed black;">Time</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
        <!-- /.row -->
        </div>
    </div>
</div>
</div>
    
