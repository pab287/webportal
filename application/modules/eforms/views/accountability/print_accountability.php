<div class="m-content">
	<div class="m-portlet m-portlet--mobile">
		<div class="m-portlet__body" id="printableArea">
			<form id="print_accountability">
				<div class="form-group m-form__group row">
					<div class="col-xs-12 table-responsive">
						<table style="font-size:small;" width="100%" border="0">
							<tr>
								<td width="50%"><h1><div id="company_from" v-text="vm_tab1.company"></div></h1></td>
								<td align="right" width="25%"><h2><small>CONTROL #</small></h2></td>
								<td align="right" width="25%"><h1><div id="refernce_no" v-text="vm_tab1.reference_no"></div></h1></td>
							</tr>
							<tr>
								<td><!--Carlos Hilado Ave., Circumferencial Road, Brgy. Bata, Bacolod City, Negros Occ.--></td>
							</tr>
						</table>
						<table style="font-size:small" width="100%">
							<tr align="center">
								<td width="100%" ><b>A C C O U N T A B I L I T Y &nbsp;&nbsp; A G R E E M E N T &nbsp;&nbsp; F O R M &nbsp;&nbsp;(AAF)</b></td>
							</tr>
						</table>
						<hr style="margin:0px 0px 5px 0px;border-top: 1px dashed black;">
						<table style="font-size:small" width="100%">
							<tr>
								<td>Issued to : <b id="issued_to" v-text="vm_tab1.display_name"></b></td>
								<td>Department : <b id="department" v-text="vm_tab1.department"></b></td>
								<td>Date Issue : <b id="date_issued" v-text="vm_tab1.date_issued"></b></td>
							</tr>
						</table>
						<br>
						<table style="font-size:small; width: 100%">
							<tr>
								<td width="100%">
									<p>&emsp;This is to acknowledge receipt of the following items under my accountabilities subject to turn-over to the property custodian upon
									resignation of my service.</p>
								</td>
							</tr>
						</table>
						<hr style="margin:0px 0px 5px 0px;border-top: 1px dashed black;">	
						<table id='content' style="text-align: center; font-size:small; width: 100%">
							<thead >
								<tr>
									<td align="center" width="15%"><b>ASSET CODE</b></td>
									<td align="center" width="45%"><b>ASSET NAME</b></td>
									<td align="center" width="18%"><b>BRAND/MODEL</b></td>
									<td align="center" width="18%"><b>AMOUNT</b></td>
								</tr>
							</thead>
							<tr>
								<hr style="margin:0px;border-top: 1px solid black;">
							<tr>
							<tbody></tbody>
						</table>
						<hr style="margin:5px 0px 5px 0px;border-top: 1px solid black;">
						<table id="tbody1" style="font-size:small; width: 100%">
							<tr style='border-bottom:1px solid black;'>
						
							</tr>
						</table>
						<table style="font-size:small; width: 100%">
							<tr>
								<td width="100%">
									<p>&emsp;I understand that the usage of all the items listed above is for official use only. Furthermore, I agree to pay GC & C Inc. the full
								replacement cost or value of any items lost or damaged and allows the company to deduct it from my salary.</p>
								</td>
							</tr>
						</table>
						<table style="font-size:small; width: 100%; margin-top: 10px" border="0">
							<tr>
								<td width="50%" colspan="2">Issued by :</td>
								<td width="40%" colspan="2" style="margin:0px 0px 0px 5px">Received by :</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td width="50%" colspan="2" align="center"><label id="created_by" v-text="vm_tab1.created_by"></label></td>
								<td width="50%" colspan="2" align="center"><label id="issued_by" v-text="vm_tab1.display_name"></label></td>
							</tr>
							<tr>
								<td colspan="2"><hr style="margin:0px 5px 0px 0px;border-top: 1px dashed black;"></td>
								<td colspan="2"><hr style="margin:0px 0px 0px 5px;border-top: 1px dashed black;"></td>
							</tr>
						</table>
						<table style="font-size:small; width: 100%; margin-top: 20px" border="0">
							<tr>
								<td width="33.33%">Noted By :</td>
								<td width="33.33%">Checked And Verified :</td>
								<td width="33.33%">Approved for Release :</td>
							</tr>
							<tr>
								<td colspan="3">&nbsp;</td>
							</tr>
							<tr>
								<td width="33.33%" align="center">
									<p style="margin:0px">{{ vm_tab1.acctg_noted_by ? vm_tab1.acctg_noted_by: '&nbsp;'}}</p>
									<p style="margin:0px 5px 0px; border-top: 1px dashed black; font-weight: 500;">Finance</p>
								</td>
								<td width="33.33%" align="center">
									<p style="margin:0px">&nbsp;</p>
									<p style="margin:0px; border-top: 1px dashed black; font-weight: 500;">AMS OIC</p>
								</td>
								<td width="33.33%" align="center">
									<p style="margin:0px">&nbsp;</p>
									<p style="margin:0px; border-top: 1px dashed black; font-weight: 500;">Logistic Head</p>
								</td>
							</tr>
						</table>
						<table style="font-size:small; width: 100%; margin-top: 20px" border="0">
							<tr>
								<td width="33.33%" align="center">
									<p style="margin:0px">{{ vm_tab1.hr_noted_by ? vm_tab1.hr_noted_by: '&nbsp;' }}</p>
									<p style="margin:0px 5px 0px; border-top: 1px dashed black; font-weight: 500;">HR</p>
								</td>
								<td width="33.33%" align="center">
									<p style="margin:0px">&nbsp;</p>
									<p style="margin:0px; border-top: 1px dashed black; font-weight: 500;">Warehouse OIC</p>
								</td>
								<td width="33.33%">&nbsp;</td>
							</tr>
						</table>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
    
