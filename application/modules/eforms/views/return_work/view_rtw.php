<style>
    @media screen and (max-width: 690px){
        #fix-mobile{
            display: flex;
            flex-wrap: wrap;
        }

        #fix-mobile button, #fix-mobile a{
            flex: 1 1 22%;
            max-width: 100%;
        }

        #fix-mobile button, #fix-mobile a{
            margin-bottom: 10px;
        }
    }

    @media screen and (max-width: 480px){
        #fix-mobile button, #fix-mobile a{
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>

<div class="m-content">
    <div class="row" id="return_to_work-content">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" href="<?php echo site_url("eforms/return_to_work/masterfile"); ?>" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
							<h3 class="m-portlet__head-text">Return To Work Request Details</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools"></div>
				</div>
                <div class="m-portlet__body">
					    <div class="row col-md-12">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Reference No: </label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                        <h5 v-text="row.reference_no">&nbsp;</h5>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Employee: </label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" v-text="row.requested_name">&nbsp;</div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Company: </label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                        <p class="m--marginless" v-text="row.company">&nbsp;</p>
                                        <p class="m--marginless" v-text="row.department">&nbsp;</p>
                                        <p class="m--marginless" v-text="row.position">&nbsp;</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <template v-if="row.has_attachment === true">
                                    <h5>ATTACHED IMAGE</h5>
                                    <div class="row m-row--no-padding align-items-center">
                                        <div class="col-md-1 col-sm-12" v-for="(item, index) in row.images">
                                            <div class="m-temp__pic text-center">
                                                <a :href="item.image" data-lightbox="upload_image" :data-title="item.filename">
                                                    <img class="m-temp__img" :src="item.thumbnail" width="60" height="60" style="border: 1px solid #233e6b;" />
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed d-xl-12"></div>
					    <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="m-form__group form-group row">
                                    <label class="col-3 col-form-label">Status: </label>
                                    <div class="col-6">
                                        <template v-if="row.status">
                                            <span id="status_state" class="alert m--font-bold" v-bind:class="getClassStatus(row.status)"><b v-text="row.status_type">&nbsp;</b></span>
                                        </template>
                                        <template v-else>&nbsp;</template>
                                    </div>
                                </div>
                                <div class="m-form__group form-group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Type: </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12"><h5 v-text="row.type_description">&nbsp;</h5></div>
                                </div>
                                <template v-if="row.status">
                                    <template v-if="row.return_type !== '0'">
                                        <div class="m-form__group form-group row">
                                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">From: </label>
                                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-12"><p class="m--marginless" v-text="row.from_date">&nbsp;</p></div>
                                        </div>
                                        <div class="m-form__group form-group row">
                                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">To: </label>
                                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-12"><p class="m--marginless" v-text="row.to_date">&nbsp;</p></div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="m-form__group form-group row">
                                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">From: </label>
                                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-12"><p class="m--marginless" v-text="row.from_date">&nbsp;</p></div>
                                        </div>
                                        <div class="m-form__group form-group row">
                                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">To: </label>
                                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-12"><p class="m--marginless" v-text="row.to_date">&nbsp;</p></div>
                                        </div>
                                        <div class="m-form__group form-group row">
                                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Address on leave: </label>
                                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-12"><p class="m--marginless" v-text="row.address">&nbsp;</p></div>
                                        </div>
                                    </template>
                                </template>
                                <div class="m-form__group form-group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Reason: </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12"><p class="m--marginless" v-text="row.reason">&nbsp;</p></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row" v-if="row.created_by">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Created By: </label>
                                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12"><p class="m--marginless" v-html="setTempDataBy(row.created_by, row.created_name, row.created_at)">&nbsp;</p></div>
                                </div>
                                <template v-if="row.updated_by && row.updated_name">
                                    <div class="form-group m-form__group row">
                                        <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Last Updated By: </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12"><p class="m--marginless" v-html="setTempDataBy(row.updated_by, row.updated_name, row.updated_at)">&nbsp;</p></div>
                                    </div>
                                </template>
                                <template v-if="row.approved_by && row.approved_name">
                                    <div class="form-group m-form__group row">
                                        <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Approved By: </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12"><p class="m--marginless" v-html="setTempDataBy(row.approved_by, row.approved_name, row.approved_at)">&nbsp;</p></div>
                                    </div>
                                </template>
                                <template v-if="row.disapproved_by && row.disapproved_name">
                                    <div class="form-group m-form__group row">
                                        <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Dispproved By: </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12"><p class="m--marginless" v-html="setTempDataBy(row.disapproved_by, row.disapproved_name, row.disapproved_at)">&nbsp;</p></div>
                                    </div>
                                </template>
                                <template v-if="row.cancelled_by && row.cancelled_name">
                                    <div class="form-group m-form__group row">
                                        <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Cancelled By: </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12"><p class="m--marginless" v-html="setTempDataBy(row.cancelled_by, row.cancelled_name, row.cancelled_at)">&nbsp;</p></div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Cancelled Remarks: </label>
                                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12"><p class="m--marginless" v-text="row.cancelled_remarks">&nbsp;</p></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot">
                        <div class="row align-items-center">
                            <div id="fix-mobile" class="col-md-12 col-lg-12 col-12 m--align-right">
                            <template v-if="row.allow_approval === true">
                                <button 
                                    type="button" 
                                    class="btn btn-success text-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnApprove_action"
                                    data-toggle="modal" 
                                    data-target="#approve_modal">
                                    Approve
                                </button>
                                <button 
                                    type="button" 
                                    class="btn btn-danger text-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnDispprove_action"
                                    data-toggle="modal" 
                                    data-target="#disapprove_modal">
                                    Dispprove
                                </button>
                            </template>
                            <template v-if="row.allow_undo_cancel === true">
                                <button 
                                    v-if="row.status === '1'" 
                                    type="button" 
                                    class="btn btn-danger text-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnUndo_approval" 
                                    data-toggle="modal" 
                                    data-target="#undo_approval_modal">
                                    Undo Approval
                                </button>
                                <button 
                                    v-if="row.status === '2'" 
                                    type="button" 
                                    class="btn btn-danger text-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnUndo_disapproval" 
                                    data-toggle="modal" 
                                    data-target="#undo_disapproval_modal">
                                    Undo Disapprove
                                </button>
                            </template>
                            <template v-if="(row.allow_approval === true || row.allow_edit === true || row.allow_undo_cancel === true) && row.status !== '3'">
                                <button 
                                type="button" 
                                class="btn btn-danger text-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnCancel_action"
                                data-toggle="modal" 
                                data-target="#cancel_modal">
                                    Cancel
                                </button>
                            </template>
                                <button v-if="row.status === '1'" type="button" class="btn btn-info text-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnPrint" onclick="printArea()">Print</button>
                                <button v-if="row.allow_edit === true" type="button" class="btn btn-warning m-btn m-btn--custom m-btn--icon m-btn--air m-btn--uppercase text-white btnEdit" @click="editRtwRequest(row.id)">Edit</button>
                                <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnBack" onclick="redirectMasterfile()">Back</button>
                            </div>
                        </div>
                    </div>
			</div>
		</div>
	</div>
</div>

<div class="m-content" hidden>
    <div class="m-portlet__body" id="printableRTW">
        <table width="100%" style="font-family: monseratt; text-transform: uppercase;">
            <tr>
                <td width="50%" colspan="2"><h2 v-text="vmData.company"></h2></td>
                <td align="right" width="50%" colspan="2"><h2 v-text="vmData.reference_no">CONTROL # </h2></td>
            </tr>
            <tr>
                <td align="right" colspan="3">
                    <h4>RETURN TO WORK FORM (RTWF)</h4>
                    <p>GCC-431 REV 2 10/21/2020</p>
                <td>
            </tr>
            <tr><td style="border: 1px dotted black;" colspan="4"></td><tr>
            <tr>
                <td width="20%">NAME: </td>
                <td width="30%" style="font-weight: bold;" v-text="vmData.requested_name"></td>
                <td width="20%">DATE: </td>
                <td width="30%" style="font-weight: bold;" v-text="vmData.dt_created"></td>
            </tr>
            <tr>
                <td>POSITION: </td>
                <td style="font-weight: bold;" v-text="vmData.position"></td>
                <td>DEPARTMENT: </td>
                <td style="font-weight: bold;" v-text="vmData.department"></td>
            </tr>
            <tr>
                <td style="border: 2px solid black;" colspan="4"></td>
            <tr>
            <tr>
                <td colspan="2">
                    <span v-if="vmData.return_type == 0">
                        <input type="checkbox" checked><span style="font-size: 12px;"> UNAUTHORIZED ABSENCE / NO NOTIFICATION</span><br>
                    </span>
                    <span v-else>
                        <input type="checkbox"><span style="font-size: 12px;"> UNAUTHORIZED ABSENCE / NO NOTIFICATION</span><br>
                    </span>
                </td>
                <td colspan="2">
                    <span v-if="vmData.return_type == 1">
                        <input type="checkbox" checked><span style="font-size: 12px;"> RECALLED TO REPORT FOR DUTY</span><br>
                    </span>
                    <span v-else>
                        <input type="checkbox"><span style="font-size: 12px;"> RECALLED TO REPORT FOR DUTY</span><br>
                    </span>
                    
                </td>
            </tr>
            <tr>
                <td align="left" colspan="2">
                    <span style="font-size: 12px;">DATE FROM: <b>{{vmData.from_date}}</b></span>
                <!-- </td>
                <td align="center" style="font-size: 11px;"> -->
                    <span style="font-size: 12px; margin-left: 20px;">TO: <b>{{vmData.to_date}}<b></span>
                </td>
                <td colspan="2">
                    <span v-if="vmData.return_type == 2">
                        <input type="checkbox" checked><span style="font-size: 12px;"> REQUESTED TO EXTEND DAYS OF WORK</span><br>
                    </span>
                    <span v-else>
                        <input type="checkbox"><span style="font-size: 12px;"> REQUESTED TO EXTEND DAYS OF WORK</span><br>
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="2">ADDRESS ON LEAVE</td>
                <template v-if="((vmData.return_type == 1 || vmData.return_type == 2) && (vmData.to_date > vmData.from_date))">
                    <td colspan="2">DATE OF DUTY FROM</td>
                </template>
                <template v-else>
                    <td colspan="2">DATE OF DUTY</td>
                </template>
                
            </tr>
            <tr>
                <td colspan="2">
                    <span v-if="vmData.address != null">
                        {{vmData.address}}
                    </span>
                    <span>
                        ---
                    </span>
                </td>
                <td colspan="2">
                    <template v-if="((vmData.return_type == 1 || vmData.return_type == 2) && (vmData.to_date > vmData.from_date))">
                        <span v-if="((vmData.return_type == 1 || vmData.return_type == 2) && (vmData.from_date != null))">
                            {{vmData.from_date}}
                        </span>
                        <span v-else>
                            ----
                        </span>
                        TO
                        <span v-if="((vmData.return_type == 1 || vmData.return_type == 2) && (vmData.to_date != null))">
                            {{vmData.from_date}}
                        </span>
                        <span v-else>
                            ----
                        </span>
                    </template>
                    <template v-else>
                        <span v-if="((vmData.return_type == 1 || vmData.return_type == 2) && (vmData.from_date != null))">
                            {{vmData.from_date}}
                        </span>
                        <span v-else>
                            ----
                        </span>
                    </template>
                </td>
            </tr>
            <tr>
                <td colspan="2"><span>REASON FOR LEAVE / NO NOTIFICATION</span></td>
                <td colspan="2"><span>REASON FOR REPORTING</span></td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 12px; padding: 5px;">
                    <span v-if="(vmData.return_type == 0 && vmData.reason != null)">
                        {{vmData.reason}}
                    </span>
                    <span v-else>
                        ----
                    </span>
                </td>
                <td colspan="2" style="font-size: 12px; padding: 5px; text-transform: uppercase">
                    <span v-if="((vmData.return_type == 1 || vmData.return_type == 2)) && vmData.reason != null">
                        {{vmData.reason}}
                    </span>
                    <span v-else>
                        ----
                    </span>
                </td>
            </tr>
            <tr><td style="border: 1px solid black;" colspan="4"></td></tr>
            <tr>
                <td colspan="2">PREPARED BY:</td>
                <td colspan="2">APPROVED BY:</td>
            </tr>
            <tr>
                <td align="center" colspan="2" style="border-bottom: 1px dotted black; padding-top: 50px; padding-bottom: 15px;"><span v-text="vmData.requested_name" style=""></span></td>
                <td align="center" colspan="2" style="border-bottom: 1px dotted black; padding-top: 50px; padding-bottom: 15px;"><span v-text="vmData.approved_name" style=""></span></td>
            </tr>
        </table>
    </div>
</div>


<div class="modal fade" id="approve_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Approve return to work request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="approve_form" method="post" action="<?php echo site_url("eforms/return_to_work/approve_rtw"); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="m-portlet m-portlet--bordered m-portlet--unair">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">
                                        Attachment Image
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools">
                                <ul class="m-portlet__nav">
                                    <li class="m-portlet__nav-item">
                                        <span class="m-portlet__nav-link btn btn-success m-btn m-btn--pill m-btn--air fileinput-button btnUpload">
                                            <i class="fa fa-plus"></i>
                                            <span>Upload File</span>
                                            <input id="temp_fileupload" type="file" name="files" multiple />
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div id="progress_approve"
                                class="progress progress-striped active"
                                role="progressbar"
                                aria-valuemin="0"
                                aria-valuemax="100"
                                style="display:none;"
                                >
                                <div
                                    class="progress-bar progress-bar-success"
                                    style="width: 0%;"
                                ></div>
                            </div>
                            <div id="tempModalApproveImages">
                                <template v-if="count > 0">
                                    <div class="row">
                                    <div class="col-2 col-md-2" v-for="(item, index) in rows">
                                        <div class="m-temp__pic text-center">
                                            <a :href="item.image" data-lightbox="tempimage" :data-title="item.filename">
                                                <img class="m-temp__img" :src="item.thumbnail" width="75" height="75" style="margin-bottom: 0.5rem;" />
                                            </a>
                                        <div class="m-checkbox-inline">
                                            <label class="m-checkbox">
                                                <input type="checkbox" name="attachment_image[]" :value="item.current_image" />{{renderImageLabel(index)}}<span></span>
                                            </label>
                                        </div>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                                        <div class="m-alert__icon">
                                            <i class="flaticon-exclamation-1"></i>
                                            <span></span>
                                        </div>
                                        <div class="m-alert__text">
                                            <strong>
                                                Image(s) not found!
                                            </strong>
                                            Upload image first
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <p><b>Approve</b> return to work request? Click <b>yes</b> if you wish to proceed.</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">Yes</button>
                    <button type="button" class="btn text-white btn-danger btnCancel" data-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="undo_approval_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Undo Approval</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="undo_approval_form" method="post" action="<?php echo site_url("eforms/return_to_work/undo_rtw_approval"); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            <p class="m--marginless"><b>Undo approval</b> of return to work request?</p>
                            <p>Click <b>yes</b> if you wish to proceed.</p> 
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">Yes</button>
                    <button type="button" class="btn btn-danger text-white btnCancel" data-dismiss="modal">No</button>				    
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="disapprove_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Disapprove return to work request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="disapprove_form" method="post" action="<?php echo site_url("eforms/return_to_work/disapprove_rtw"); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <b>Disapprove</b> return to work request?</br>Click <b>yes</b> if you wish to proceed.   
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">Yes</button>
                    <button type="button" class="btn btn-danger text-white btnCancel" data-dismiss="modal">No</button>	
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="undo_disapproval_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Undo Disapproval</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id = "undo_disapproval_form" method="post" action="<?php echo site_url("eforms/return_to_work/undo_rtw_disapproval"); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            <b>Undo disapproval</b> of return to work request?</br>Click <b>yes</b> if you wish to proceed. 
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">Yes</button>
                    <button type="button" class="btn btn-danger text-white btnCancel" data-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cancel_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Return to work request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="cancel_form" method="post" action="<?php echo site_url("eforms/return_to_work/cancelled_rtw"); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label form-control-label">Remarks:</label>
                        <div class="col-12">
                            <textarea class="form-control" name="cancelled_remarks" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">Save</button>
                    <button type="button" class="btn btn-danger text-white btnCancel" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

