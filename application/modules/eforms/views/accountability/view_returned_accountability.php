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
                                <a type="button" href="returned_accountability" title="Back" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew">
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
                    <form id="accountability_form" class="form-horizontal">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
					    <div class="row" id="accountability_form_body">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 co-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Accountability No.:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <b>{{vmAccData.reference_no}}</b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 co-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        File Under:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <b id="company">{{vmAccData.company}}</b>
                                        <p id="department">{{vmAccData.department}}</p>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 co-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Issued to:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <span id=''>{{vmAccData.is_contract == 1 ? vmAccData.contractor : vmAccData.display_name }}</span> <span id=''>{{vmAccData.date_issued}}</span>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group m-form__group row" id="returned-section">
                                    <label class="col-md-3 co-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Returned by:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" v-if="vmAccData.returned_by">
                                        <!-- {{vmAccData.returned_by}} -->
                                        <b id="returnedBy"> {{ vmAccData.returned_by }}</b>
                                        <p id="returnedDate"> {{ vmAccData.returned_date }}</p>
                                    </div>
                                    <b id="emptyReturnedBy"></b>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 co-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Created by:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <span>{{vmAccData.created_by}}</span> <span id="created_dt">{{vmAccData.created_dt}}</span>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 co-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Last Edited By:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <b id="last_edited_by">{{vmAccData.last_edited_by}}</b> <b id="last_edited_dt">{{vmAccData.last_edited_dt}}</b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 co-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Received By:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <b id="marked_returned_by">{{vmAccData.received_by}}</b> <b id="marked_returned_dt"></b>
                                    </div>
                                </div>
                                <div id="acctg_note" class="form-group m-form__group row">
                                    <label class="col-md-3 co-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Accounting Note:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <span id="acctg_noted_remarks"></span><br> 
                                        <span id="acctg_noted_by"></span><br>
                                        <span id="acctg_noted_dt"></span>
                                    </div>
                                </div>
                                <div id="hr_note" class="form-group m-form__group row">
                                    <label class="col-md-3 co-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        HR Note:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <span id="hr_noted_remarks"></span>
                                        <span id="hr_noted_by"></span><br>
                                        <span id="hr_noted_dt"></span><br>
                                    </div>
                                </div>
                                <div id="release_note" class="form-group m-form__group row">
                                    <label class="col-md-3 co-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Released By:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <span id="release_remarks"></span>
                                        <span id="released_by"></span><br>
                                        <span id="released_dt"></span><br> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed d-xl-12"></div> 
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
                                    <div class="m_datatable  m-datatable--default  m-datatable--scroll col-12 table-responsive-sm">
                                        <table class="table table-striped table-bordered" id="tblbody_returned" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Asset Code</th>
                                                    <th>Asset Name</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Remarks</th>
                                                    <th>Cleared</th>
                                                </tr>
                                            </thead>
                                            <tbody> 

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed d-xl-12"></div> 
                        <br>
                        <div class="row">
                            <div class='col-md-6 col-sm-12'>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 co-lg-3 col-sm-3 col-xs-12 col-form-label">
                                        Remarks:
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <textarea name="remarks" id="remarks" class="form-control" rows="8"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class='col-md-6 col-sm-12'>
                                <div class="m-widget14">
                                    <div class="m-widget14__header">
                                        <h3 class="m-widget14__title">
                                            ACCOUNTABILITY LOGS
                                        </h3>
                                    </div>
                                    <div id="accountability-logs">
                                        <template v-if="count > 0">
                                        <div class="m-widget4">
                                            <div class="m-widget4__item" v-for="(item, index) in rows">
                                                <div class="m-widget4__img m-widget4__img--pic">
                                                    <img :src="item.image" alt="">
                                                </div>
                                                <div class="m-widget4__info">
                                                    <div>
                                                        <span class="m-widget4__title">{{item.created_name}}</span>
                                                        <span class="m-widget4__title pull-right">
                                                            <div class="m-badge m-badge--wide alert" :class="item.temp_state" role="alert">
                                                                <strong>{{item.temp_label}}</strong>
                                                            </div>
                                                        </span>
                                                    </div>
                                                    <br>
                                                    <p class="m--marginless m-widget4__sub">{{item.message}}</p>
                                                    <span class="m-widget4__text">
                                                    <template v-if="item.temp_alert === true">
                                                        <p v-if="item.email_sent === '1'"><i class="fa fa-envelope-o"></i> EMAIL HAS BEEN SENT SUCCESSFULLY</p>
                                                        <p v-else><i class="fa fa-envelope-o"></i> EMAIL SENDING FAILED!</p>
                                                    </template>
                                                    <small>{{item.logged_at}}</small>
                                                    </span>
                                                </div>
                                                <div class="m-widget4__ext">&nbsp;</div>
                                            </div>
                                        </div>
                                        </template>
                                        <template v-else>
                                            <div class="m-alert m-alert--icon m-alert--air alert alert-dismissible fade show" role="alert">
                                                <div class="m-alert__icon">
                                                    <i class="flaticon-exclamation-2"></i>
                                                </div>
                                                <div class="m-alert__text">
                                                    <strong>
                                                        Empty!
                                                    </strong>
                                                    No accountability logs available.
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="modal-footer" id="buttons">
                            <button type='button' class='btn btn-accent btnSave m-btn m-btn--custom m-btn--air m-btn--box' data-toggle="modal" data-target="#modal-return_accountability">
                                <span><i class='fa fa-save'></i> </span> Save
                            </button>
                            <template v-if="isLoading === false">
                                <button type='button'  onclick=printAreaReturn() class='btn btn-primary btnPrint m-btn m-btn--custom m-btn--air m-btn--box'><span class="fa fa-print"></span> Print</button>
                                <button type='button' onclick=printArea() class='btn btn-primary btnPrint_aaf m-btn m-btn--custom m-btn--air m-btn--box'><span class="fa fa-print"></span> Print AAF</button>
                            </template>
                            <template v-else>
                                <button type='button'  onclick=printAreaReturn() class='btn btn-primary btnPrint m-btn m-btn--custom m-btn--air m-btn--box' disabled>
                                <span class="m-loader text-white" style="margin-right: 20px;"></span>Print</button>
                                <button type='button' onclick=printArea() class='btn btn-primary btnPrint_aaf m-btn m-btn--custom m-btn--air m-btn--box' disabled>
                                <span class="m-loader text-white" style="margin-right: 20px;"></span>Print AAF</button>
                            </template>
                            <!-- <button type='button'  onclick=printAreaReturn() class='btn btn-primary btnPrint m-btn m-btn--custom m-btn--air m-btn--box'>Print</button>
                            <button type='button' onclick=printArea() class='btn btn-primary btnPrint_aaf m-btn m-btn--custom m-btn--air m-btn--box'>Print AAF</button> -->
                            <a href="<?php echo base_url('eforms/accountability/returned_accountability'); ?>" class='btn btn-metal btnBack m-btn m-btn--custom m-btn--air m-btn--box text-white'>Back</a>            
                        </div>
                    </form>
                </div>
            </div>
            <!--end::Portlet--> 
	    </div>
    </div>    
</div>

<div class="modal fade" id="modal-return_accountability" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Returned Accountability</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to save this returned accountability?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-brand text-white btnSave" onclick="triggerSaveReturnAcct()">Yes</button>
                <button type="button" class="btn btn-danger text-white btnClose" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="change_return" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Accountability Form &nbsp
                </h5>
                <span> (returned asset)</span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "change_return_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <div class="col-12">
                            Returned By:
                        </div>
                        <div class="col-12">
                            <select id="return_by" name="return_by"  data-validation="required">

                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        Save
                    </button>
                    <button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="returned_remarks_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Remarks</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="returned_remarks_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="clear_id">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <div class="col-12">
                            <textarea class="form-control" id="returned_remarks" name="returned_remarks" placeholder="Enter Remarks" rows="5"></textarea>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="clear_returned" value="returned" class="form-check-input" id="clear_returned">
                                <label class="form-check-label mr-5" for="clear_returned">Returned</label>
                                <input type="checkbox" name="clear_unreturned" value="unreturned" class="form-check-input" id="clear_unreturned">
                                <label class="form-check-label" for="clear_unreturned">Unreturned</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">Save</button>
                    <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="unreturned_remarks_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;"><!-- unreturned modal -->
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Remarks</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="unreturned_remarks_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="id">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <div class="col-12">
                            <textarea class="form-control" id="unreturned_remarks" name="unreturned_remarks" placeholder="Enter Remarks" rows="5"></textarea>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="returned" value="returned" class="form-check-input" id="return">
                                <label class="form-check-label mr-5" for="return">Returned</label>
                                <input type="checkbox" name="unreturned" value="unreturned" class="form-check-input" id="unreturn">
                                <label class="form-check-label" for="unreturn">Unreturned</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">Save</button>
                    <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- released accountability -->
<div class="m-content" hidden>
	<div class="m-portlet__body" id="printableArea">
		<form id="print_accountability">
			<div class="form-group m-form__group row" style="text-transform: uppercase;">
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

							<td v-if="vm_tab1.is_contract == 1">Issued to : <b id="print_issued_to"v-text="vm_tab1.contractor"></b></td>
                            <td v-else>Issued to : <b id="print_issued_to" v-text="vm_tab1.display_name"></b></td>
							<td>Department : <b id="department" v-text="vm_tab1.department"></b></td>
							<td>Date Issue : <b id="date_issued" v-text="vm_tab1.date_issued"></b></td>
						</tr>
					</table>
                    <hr style="margin:0px 0px 5px 0px;border-top: 1px dashed black;">
					<br>
                    <br>
					<table style="font-size:small; width: 100%">
						<tr>
							<td width="100%">
								<!-- <p>&emsp;This is to acknowledge receipt of the following items under my accountabilities subject to turn-over to the property custodian upon
								resignation of my service.</p> -->
                                <p>&emsp; I confirm receipt of the items listed below. They remain under my responsibility until I return them to the property custodian when I'm no longer connected with the company, without the need for a formal demand, or when they are no longer needed by me. <b>{{ vm_tab1.initials }}</p>
							</td>
						</tr>
					</table>
                    <br>
					<table id='content' style="border-top: 1px dashed black; border-collapse: collapse;text-align: center; font-size:small; width: 100%">
						<thead >
							<tr>
								<td align="center" width="15%" style=" border-bottom: 1px solid black;"><b><small>ASSET CODE</small></b></td>
								<td align="center" width="45%" style=" border-bottom: 1px solid black;"><b><small>ASSET NAME</small></b></td>
								<td align="center" width="18%" style=" border-bottom: 1px solid black;"><b><small>BRAND/MODEL</small></b></td>
								<td align="center" width="18%" style=" border-bottom: 1px solid black;"><b><small>AMOUNT</small></b></td>
							</tr>
						</thead>
						<tbody>
                            <tr v-for="vm_tab_aaf_print in vm_tab_aaf">
                                <td>{{vm_tab_aaf_print.asset_code}}</td>
                                <td>{{vm_tab_aaf_print.desc}}</td>
                                <td>{{vm_tab_aaf_print.brand}} / {{vm_tab_aaf_print.model}}</td>
                                <td>{{vm_tab_aaf_print.amount}}</td>
                            </tr>
                        </tbody>
                        <tfoot style="border-top: 1px solid black;">
							<td align="right" colspan="3"><b>TOTAL AMOUNT</b></td>
							<td><b>{{vm_tab_aaf_totalamount.amount}}</b></td>
						</tfoot>
					</table>
					<table id="tbody1" style="font-size:small; width: 100%">
						<tr style='border-bottom:1px solid black;'>
					
						</tr>
					</table>
                    <div style="margin: 10px 0 0 0"></div>
					<table style="font-size:small; width: 100%">
						<tr>
							<td width="100%">
								<!-- <p>&emsp;I understand that the usage of all the items listed above is for official use only. Furthermore, I agree to pay GC & C Inc. the full
							replacement cost or value of any items lost or damaged and allows the company to deduct it from my salary.</p> -->
								<p>&emsp;I acknowledge that the items listed above are to be used strictly for official purposes. Additionally, I consent to pay the company for the entire replacement cost or value of any items that are lost or damaged due to abuse, misuse, improper application, negligence, failure to adequately maintain, or improper storage of the equipment. <b>{{ vm_tab1.initials }}</p>
							</td>
						</tr>
					</table>

                    <div style="margin: 10px 0 0 0"></div>
					<table style="font-size:small; width: 100%">
						<tr>
							<td width="100%">
								<p>&emsp;Furthermore, I will ensure that I obtain written confirmation of return accountability when I return the items; otherwise, it will be deemed that the items have not been returned and are still in my possession. <b>{{ vm_tab1.initials }}</b>
								</p>
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
							<td v-if="vm_tab1.is_contract == 1" width="50%" colspan="2" align="center"><label id="received_by" v-text="vm_tab1.contractor"></label></td>
							<td v-else width="50%" colspan="2" align="center"><label id="received_by" v-text="vm_tab1.display_name"></label></td>
                            
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
				</div>
			</div>
		</form>
	</div>
</div>

<div class="m-content" hidden>
	<div class="m-portlet__body" id="printableAreaReturned">
		<form id="print_returned_accountability">
			<div class="form-group m-form__group row">
				<div class="col-xs-12 table-responsive" style="text-transform: uppercase">
                    <style>
                        @media print{
                            /*** table#return_content td.custom-asset_code{
                                width: "200px";
                            } ***/
                        }
                    </style>
                    <table style="font-size:small;" width="100%" border="0">
                        <tr>
                            <td width="50%"><h1><div id="company_from" v-text="vm_tab2.company"></div></h1></td>
							<td align="right" width="25%"><h2><small>CONTROL #</small></h2></td>
							<td align="right" width="25%"><h1><div id="refernce_no" v-text="vm_tab2.reference_no"></div></h1></td>
                        </tr>
                        <tr>
                            <td><!--Carlos Hilado Ave., Circumferencial Road, Brgy. Bata, Bacolod City, Negros Occ.--></td>
                        </tr>
                    </table>
                    <table style="font-size:small" width="100%">
                        <tr align="center">
                            <td width="100%"><b style="font-size: 12px;">R E T U R N E D &nbsp;&nbsp; A C C O U N T A B I L I T Y &nbsp;&nbsp; F O R M &nbsp;&nbsp;(RAF)</b></td>
                        </tr>
                    </table>
                    <hr style="margin:10px 0px 3px 0px;border-top: 1px dashed black;">
                    <table style="font-size:small" width="100%">
                    <tr>
                        <td v-if="vm_tab2.is_contract == 1">Issued to : <b id="print_issued_to2" v-text="vm_tab2.contractor"></b></td>
                        <td v-else>Issued to : <b id="print_issued_to2" v-text="vm_tab2.display_name"></b></td>
                        <td>Department : <b id="department" v-text="vm_tab2.department"></b></td>
                        <td>Date Issue : <b id="date_issued" v-text="vm_tab2.date_issued"></b></td>
                    </tr>
                    </table>
                    <br>
                    <table id='return_content' style="border-top: 1px dashed black; border-collapse: collapse; font-size:small; width: 100%; text-align: center" cellspacing="2">
                        <thead >
							<tr>
								<td align="center" style=" border-bottom: 1px solid black;" width="15%"><b><small>ASSET CODE</small></b></td>
								<td align="center" style=" border-bottom: 1px solid black;" width="45%"><b><small>ASSET NAME</small></b></td>
                                <td align="center" style=" border-bottom: 1px solid black;" width="18%"><b><small>STATUS</small></b></td>
								<td align="center" style=" border-bottom: 1px solid black;" width="18%"><b><small>DATE RETURNED</small></b></td>
                                <td align="center" style=" border-bottom: 1px solid black;" width="18%"><b><small>REMARKS</small></b></td>
								<td align="center" style=" border-bottom: 1px solid black;" width="22%"><b><small>AMOUNT</small></b></td>
							</tr>
						</thead>
                        <tbody>
                            <tr v-for="vm_tab_content_print in vm_tab_content">
                                <td>{{vm_tab_content_print.asset_code}}</td>
                                <td>{{vm_tab_content_print.description}}</td>
                                <td v-if="vm_tab_content_print.is_returned == 1">RETURNED</td>
                                <td v-else>UNRETURNED</td>
                                <td>{{vm_tab_content_print.date_returned}}</td>
                                <td style="font-size: 9px;">{{vm_tab_content_print.remarks_returned}}</td>
                                <td>{{vm_tab_content_print.amount}}</td>
                            </tr>
                        </tbody>
                        <tfoot style="border-top: 1px solid black;">
							<td align="right" colspan="4"><b>TOTAL AMOUNT</b></td>
                            <td></td>
							<td style="font-weight: bold;" id="total_accountability_amount"><b></b></td>
						</tfoot>
                    </table>
                    <table id="tbody" style="font-size:10px; width: 100%">
                    </table>
                    <table id="tbody1" style="font-size:10px; width: 100%">
                        <tr>
                            <td width="15%"></td>
                            <td width="45%"></td> 
                            <!--<td align="right" width="18%"><b>TOTAL</b></td>
                            <td align="right" width="18%" style="font-size: 10px;"><b id="total"></b></td>-->
                        </tr>
                    </table>
                    <table style="font-size:12px; width: 100%; margin-top: 10px" border="0">
                        <tr>
                            <td width="50%" colspan="2" style="font-size: 12px;">Returned by :</td>
                            <td width="40%" colspan="2" style="margin:0px 0px 0px 5px; font-size: 12px;">Received by :</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr style="text-transform: uppercase;">
                            <td width="50%" colspan="2" align="center">
                                <label v-if="vm_tab2.returned_by === ''" id="print_returned_by" style="font-size: 12px;">
                                    <span v-if="vm_tab2.is_contract == 1">
                                        {{vm_tab2.contractor}}
                                    </span>
                                    <span v-else>
                                        {{vm_tab2.display_name}}
                                    </span>
                                </label>
                                <label v-else style="font-size: 12px;">
                                    {{vm_tab2.returned_by_detail}}
                                </label>
                            </td>
                            <td width="50%" colspan="2" align="center"><label id="created_by" v-text="vm_tab2.marked_returned_by" style="font-size: 12px;"></label></td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr style="margin:0px 5px 0px 0px;border-top: 1px dashed black;"></td>
                            <td colspan="2"><hr style="margin:0px 0px 0px 5px;border-top: 1px dashed black;"></td>
                        </tr>
                    </table>
                </div>
			</div>
		</form>
	</div>
</div>

<div class="m-content" hidden>
	<div class="m-portlet m-portlet--mobile">
		<div class="m-portlet__body" id="accountabilityFF">
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
									resignation of my service.</p> arman 123
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
							<tbody>
                                <tr></tr>
                            </tbody>
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

