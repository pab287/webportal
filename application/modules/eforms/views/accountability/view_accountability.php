<style>
	@media screen and (max-width: 690px){
        #buttons{
            flex-wrap: wrap;
        }

        #buttons button, #buttons a{
            flex: 1 1 22%;
            max-width: 100%;
        }

        #buttons button, #buttons a{
            margin-bottom: 10px;
        }
    }

    @media screen and (max-width: 480px){
        #buttons button, #buttons a{
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>

<div class="m-content">
    <div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<span class="m-portlet__head-icon">
                                <a type="button" href="<?=isset($_GET['page']) && $_GET['page'] != '' ? $_GET['page'] : 'masterfile' ?>" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
							<h3 class="m-portlet__head-text">
								Accountability Form Detail
							</h3>
						</div>
					</div>
				</div>
				<div class="m-portlet__body">
                    <form action="#" id="accountability_form" class="form-horizontal">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
					    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                        Accountability No.:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <b id="reference_no"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                        File Under:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <b id="company"></b>
                                        <p id="department"></p>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                        Issued to:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <span id='issued_to'></span> <span id='issued_dt'></span>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group m-form__group row">
                                    <label class="col-3">
                                        Status:
                                    </label>
                                    <div class="col-9" id='status'>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                        Created by:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <span id="created_by"></span> <span id="created_dt"></span>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                        Last Edited By:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <b id="last_edited_by"></b> <b id="last_edited_dt"></b>
                                    </div>
                                </div>
                                <br>
                                <div id="cancelled">
                                    <div class="form-group m-form__group row">
                                        <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                            Cancelled By:
                                        </label>
                                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                            <span id="cancelled_by"></span> <span id="cancelled_dt"></span>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                            Reason:
                                        </label>
                                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                            <span id="cancelled_reason"></span>
                                        </div>
                                    </div>
                                </div>
                                <div id="acctg_note" class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                        Accounting Notes:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <span id="acctg_noted_remarks"></span><br> 
                                        <span id="acctg_noted_by"></span><br>
                                        <span id="acctg_noted_dt"></span>
                                    </div>
                                </div>
                                <div id="hr_note" class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                        HR Notes:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <span id="hr_noted_remarks"></span><br> 
                                        <span id="hr_noted_by"></span><br>
                                        <span id="hr_noted_dt"></span>
                                    </div>
                                </div>
                                <div id="release_note" class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                        Released By:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <span id="release_remarks"></span><br> 
                                        <span id="released_by"></span><br>
                                        <span id="released_dt"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--solid d-xl-12"></div> 
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Contents:
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group m-form__group row">
                                    <div class="m_datatable  m-datatable--default table-responsive m-datatable--scroll col-12">
                                        <table class="table table-striped table-bordered" width="100%" id="tblbody">
                                            <thead>
                                                <tr>
                                                    <th style="width: 20%;">Asset Code</th>
                                                    <th style="width: 40%;">Description</th>
                                                    <th style="width: 30%;">Remarks</th>
                                                    <th style="width: 10%;">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody> 
											<tr v-for="vm_data in vmFormData_body.data">
												<td>{{ vm_data.asset_code }}</td>
												<td style="text-align: left !important;">
													<template v-if="vm_data.type == 'Vehicle'">
														<b>{{ vm_data.description }}</b><br>
														Description: {{ vm_data.desc}}<br>
														Serial No: 
														<span v-if="vm_data.serialno !== null">{{vm_data.serialno}}</span>
														<span v-else>N/A</span>
													</template>
													<template v-else>
														<b>{{ vm_data.description }}</b><br>
														Description: {{ vm_data.desc}}<br>
														Brand: {{ vm_data.brand }}<br>
														Model: {{ vm_data.model }}<br>
														Serial: {{ vm_data.serialno }}
													</template>
												</td>
												<td>{{ vm_data.remarks }}</td>
												<td>{{ vm_data.amount }}<td>
											</tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" id="buttons"></div>
                    </form>
                </div>
            </div>
            <!--modal cancel-->
            <div class="modal fade" id="cancel_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
				<div class="modal-dialog modal-md" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">
								Cancel Accountability Form
							</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							    <span aria-hidden="true">
									×
								</span>
							</button>
						</div>
                        <form id = "cancel_modal_form">
                            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
						    <div class="col-12 modal-body">
                                <div class="form-group m-form__group row">
									<label class="col-2 col-form-label form-control-label">
										Remarks:
									</label>
                                    <div class="col-10">
                                        <textarea class="form-control" name="cancelled_remarks" rows="5"></textarea>
                                    </div>
								</div>
						    </div>
						    <div class="modal-footer">
								<button type="submit" class="btn btn-submit btn-primary btnSave">
                                    Save
							    </button>
							    <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">
								    Close
							    </button>
						    </div>
                        </form>
					</div>
				</div>
			</div>
            <!--modal acctg note-->
            <div class="modal fade" id="acctg_note_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
				<div class="modal-dialog modal-md" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">
								Accounting Notes
							</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							    <span aria-hidden="true">
									×
								</span>
							</button>
						</div>
                        <form id = "acctg_note_form">
                            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
						    <div class="col-12 modal-body">
                                <div class="form-group m-form__group row">
									<label class="col-md-2 col-sm-2 col-xs-12 col-form-label form-control-label">
										Notes:
									</label>
                                    <div class="col-md-10 col-sm-10 col-xs-12">
                                        <textarea class="form-control" name="acctg_note" rows="5"></textarea>
                                    </div>
								</div>
						    </div>
						    <div class="modal-footer">
								<button type="submit" class="btn btn-submit btn-primary btnSave">
                                    Save
							    </button>
							    <button type="button" class="text-white btn btn-metal btnClose" data-dismiss="modal">
								    Close
							    </button>
						    </div>
                        </form>
					</div>
				</div>
			</div>
            <!--modal hr note-->
            <div class="modal fade" id="hr_note_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
				<div class="modal-dialog modal-md" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">
								HR Notes
							</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							    <span aria-hidden="true">
									×
								</span>
							</button>
						</div>
                        <form id = "hr_note_form">
                            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
						    <div class="col-12 modal-body">
                                <div class="form-group m-form__group row">
									<label class="col-md-2 col-sm-2 col-xs-12 col-form-label form-control-label">
										Notes:
									</label>
                                    <div class="col-md-10 col-sm-10 col-xs-12">
                                        <textarea class="form-control" name="hr_note" rows="5"></textarea>
                                    </div>
								</div>
						    </div>
						    <div class="modal-footer">
								<button type="submit" class="btn btn-submit btn-primary btnSave">
                                    Save
							    </button>
							    <button type="button" class="btn btn-metal btnClose text-white" data-dismiss="modal">
								    Close
							    </button>
						    </div>
                        </form>
					</div>
				</div>
			</div>
            <div class="modal fade" id="resend_email_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
				<div class="modal-dialog modal-md" role="document">
					<div class="modal-content" id="modal-resend_action">
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">Re-send Email <small>Accountability</small></h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							    <span aria-hidden="true">×</span>
							</button>
						</div>
						<div class="modal-body">
							<h6>Are you sure you want to re-send the email for this `{{row.status}}` accountability?</h6>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-submit btn-primary btnResend" @click="resendEmail()">Yes</button>
							<button type="button" class="btn btn-danger btnClose text-white" data-dismiss="modal">No</button>
						</div>
					</div>
				</div>
			</div>
            <!--modal release note-->
            <div class="modal fade" id="release_note_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
				<div class="modal-dialog modal-md" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">
								Release Remarks
							</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							    <span aria-hidden="true">
									×
								</span>
							</button>
						</div>
                        <form id = "release_note_form">
                            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
						    <div class="col-12 modal-body">
                                <div class="form-group m-form__group row">
									<label class="col-md-2 col-sm-2 col-xs-12 col-form-label form-control-label">
										Remarks:
									</label>
                                    <div class="col-md-10 col-sm-10 col-xs-12">
                                        <textarea class="form-control" name="release_note" rows="5"></textarea>
                                    </div>
								</div>
						    </div>
						    <div class="modal-footer">
								<button type="submit" class="btn btn-submit btn-primary btnSave">
                                    Save
							    </button>
							    <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">
								    Close
							    </button>   
						    </div>
                        </form>
					</div>
				</div>
			</div>
             <!--modal undo acctg note-->
             <div class="modal fade" id="undo_acctg_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
				<div class="modal-dialog modal-md" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h6 class="modal-title" id="exampleModalLabel">
								Undo acctg notes
							</h6>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							    <span aria-hidden="true">
									×
								</span>
							</button>
						</div>
                        <form id = "undo_acctg_form">
                            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
						    <div class="col-12 modal-body">
                                <div class="form-group m-form__group row">
									<label class="col-12 col-form-label form-control-label">
                                        Are you sure you want to undo changes on this form?
									</label>
								</div>
						    </div>
						    <div class="modal-footer">
								<button type="submit" class="btn btn-submit btn-primary btnSave">
                                    Yes
							    </button>
							    <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">
								    No
							    </button>				    
						    </div>
                        </form>
					</div>
				</div>
			</div>
            <!--modal undo hr note-->
            <div class="modal fade" id="undo_hr_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
				<div class="modal-dialog modal-md" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h6 class="modal-title" id="exampleModalLabel">
								Undo HR Notes
							</h6>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							    <span aria-hidden="true">
									×
								</span>
							</button>
						</div>
                        <form id = "undo_hr_form">
                            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
						    <div class="col-12 modal-body">
                                <div class="form-group m-form__group row">
									<label class="col-12 col-form-label form-control-label">
										Are you sure you want to undo changes on this form?
									</label>
								</div>
						    </div>
						    <div class="modal-footer">
								<button type="submit" class="btn btn-submit btn-primary btnSave">
                                    Yes
							    </button>
							    <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">
								    No
							    </button> 
						    </div>
                        </form>
					</div>
				</div>
			</div>
            <!--modal undo hr note-->
            <div class="modal fade" id="undo_release_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
				<div class="modal-dialog modal-md" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h6 class="modal-title" id="exampleModalLabel">
								Undo Releasing
							</h6>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							    <span aria-hidden="true">
									×
								</span>
							</button>
						</div>
                        <form id = "undo_release_form">
                            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
						    <div class="col-12 modal-body">
                                <div class="form-group m-form__group row">
									<label class="col-12 col-form-label form-control-label">
										Are you sure you want to undo changes on this form?
									</label>
								</div>
						    </div>
						    <div class="modal-footer">
								<button type="submit" class="btn btn-submit btn-primary btnSave">
                                    Yes
							    </button>
							    <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">
								    No
							    </button>
						    </div>
                        </form>
					</div>
				</div>
			</div>
            <!--end::Portlet--> 
	    </div>
    </div>    
</div>

<div class="m-content" hidden>
	<div class="m-portlet__body" id="printableArea">
		<form id="print_accountability">
			<div class="form-group m-form__group row">
				<div class="col-xs-12 table-responsive" style="text-transform: uppercase;">
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
							<td>Issued to : <b id="print_issued_to" v-text="vm_tab1.display_name"></b></td>
							<td>Department : <b id="department" v-text="vm_tab1.department"></b></td>
							<td>Date Issue : <b id="date_issued" v-text="vm_tab1.date_issued"></b></td>
						</tr>
					</table>
					<br>
					<table style="font-size:small; width: 100%">
						<tr>
							<td width="100%">
								<p>&emsp;I confirm receipt of the items listed below. They remain under my responsibility until I return them to the property custodian when I'm no longer connected with the company, without the need for a formal demand, or when they are no longer needed by me. <b>{{ vm_tab1.initials }}</b> </p>
								<!-- <p>&emsp;I acknowledge that I have received the items listed below, which are my responsibility until I hand them over to the property custodian either upon resigning from my position or when they are no longer required by me. <b>{{ vm_tab1.initials }}</b> </p> -->
							</td>
						</tr>
					</table>
					<br>
					<table id='content' style="border-top: 1px dashed black; border-collapse: collapse; text-align: center; font-size:small; width: 100%; text-transform: uppercase;">
						<thead style=" border-bottom: 1px solid black;">
							<tr style=" border-bottom: 1px solid black;">
								<td align="center" width="15%" style=" border-bottom: 1px solid black;"><b><small>ASSET CODE</small></b></td>
								<td align="center" width="45%" style=" border-bottom: 1px solid black;"><b><small>ASSET NAME</small></b></td>
								<td align="center" width="18%" style=" border-bottom: 1px solid black;"><b><small>BRAND/MODEL</small></b></td>
								<td align="center" width="18%" style=" border-bottom: 1px solid black;"><b><small>AMOUNT</small></b></td>
							</tr>
						</thead>
						<tbody>
							<tr v-for="vm_data in vm_tab_body.data">
								<td>{{ vm_data.asset_code }}</td>
								<td>
									<template v-if="vm_data.type == 'Vehicle'">
										<b>{{ vm_data.description }}</b><br>
										Description: {{ vm_data.desc}}<br>
										Serial No: 
										<span v-if="vm_data.serialno !== null">{{vm_data.serialno}}</span>
										<span v-else>N/A</span>
									</template>
									<template v-else>
										<b>{{ vm_data.description }}</b><br>
										Description: {{ vm_data.desc}}<br>
									</template>
								</td>
								<td>{{ vm_data.brand }} / {{vm_data.modelno}}</td>
								<td>PHP {{vm_data.amount}}<td>
							</tr>
						</tbody>
						<tfoot style="border-top: 1px solid black;">
							<td align="right" colspan="3"><b>TOTAL AMOUNT</b></td>
							<td><b>PHP {{vm_tab_total_amount.amount}}</b></td>
						</tfoot>
					</table>
					<table id="tbody1" style="font-size:small; width: 100%">
						<tr style='border-bottom:1px solid black;'></tr>
					</table>
					<div style="margin: 10px 0 0 0"></div>
					<table style="font-size:small; width: 100%">
						<tr>
							<td width="100%">
								<!-- <p>&emsp;I acknowledge that the items listed above are to be used strictly for official purposes. Additionally, I consent to reimbursing the company for the entire replacement cost or value of any items that are lost or damaged due to abuse, misuse, improper application, negligence, failure to adequately maintain, or improper storage of the equipment. <b>{{ vm_tab1.initials }}</b> -->
								<p>&emsp;I acknowledge that the items listed above are to be used strictly for official purposes. Additionally, I consent to pay the company for the entire replacement cost or value of any items that are lost or damaged due to abuse, misuse, improper application, negligence, failure to adequately maintain, or improper storage of the equipment. <b>{{ vm_tab1.initials }}</b>
								</p>
							</td>
						</tr>
					</table>
					<div style="margin: 10px 0 0 0"></div>
					<table style="font-size:small; width: 100%">
						<tr>
							<td width="100%">
								<p>&emsp;Furthermore, I will ensure that I obtain written confirmation of return accountability when I return the items; otherwise, it will be deemed that the items have not been returned and are still in my possession. <b>{{ vm_tab1.initials }}</b>
								</p>
								<!-- <p>&emsp;Furthermore, I will ensure that I obtain written confirmation of return accountability when I return the items; otherwise, it will be deemed that the items have not been returned and are still in my possession. <b>{{ vm_tab1.initials }}</b>
								</p> -->
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
							<td width="50%" colspan="2" align="center"><label id="print_issued_by" v-text="vm_tab1.display_name"></label></td>
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
								<p style="margin:0px; border-top: 1px dashed black; font-weight: 500;">Warehouse HEAD</p>
							</td>
							<td width="33.33%">&nbsp;</td>
						</tr>
					</table>
					<table style="font-size:small; width: 100%; margin-top: 10px" border="0" hidden>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td style="text-align: center;"><em><font size="1.5"><strong>NOTE: ABUSE, MISUSE, MISAPPLICATION, NEGLIGENCE, ACCIDENT OR FAILURE TO MAINTAIN, STORE, OR USE OF THE EQUIPMENT SHALL BE CHARGE TO THE OPERATOR.</strong></font></em></td>
						</tr>
					</table>
				</div>
			</div>
		</form>
	</div>
</div>
    
