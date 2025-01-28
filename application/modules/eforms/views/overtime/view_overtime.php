<style>
    @media screen and (max-width: 690px){
        #buttons{
            display: flex;
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
                                <a type="button" href="<?=(isset($_GET['page']) && $_GET['page']) ? $_GET['page'] : 'masterfile' ?>" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
							<h3 class="m-portlet__head-text">
								Overtime Request Details
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools"></div>
				</div>
                <form action="#" id="form_overtime" class="form-horizontal">
				<div class="m-portlet__body">
                    
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div id="overtime_renderer">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group m-form__group row">
                                        <label for="" class="col-md-3 col-lg-3 col-sm-12 col-xs-12">Reference no:</label>
                                        <div class="col-md-9 col-lg-9 col-sm-12 col-xs-12"><strong v-text="vm_tab1.reference_no">&nbsp;</strong></div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label for="" class="col-md-3 col-lg-3 col-sm-12 col-xs-12">
                                            Employee:
                                        </label>
                                        <div class="col-md-9 col-lg-9 col-sm-12 col-xs-12"><strong v-text="vm_tab1.display_name">&nbsp;</strong></div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label for="" class="col-md-3 col-lg-3 col-sm-12 col-xs-12">Company:</label>
                                        <div class="col-md-9 col-lg-9 col-sm-12 col-xs-12">
                                            <strong><span v-text="vm_tab1.company">&nbsp;</span></strong><br>
                                            <span v-text="vm_tab1.department">&nbsp;</span><br>
                                            <span v-text="vm_tab1.position">&nbsp;</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <template v-if="vm_tab1.has_attachment === true">
                                        <h5>ATTACHED IMAGE</h5>
                                        <div class="row m-row--no-padding align-items-center">
                                            <div class="col-md-1 col-sm-12" v-for="(item, index) in vm_tab1.images">
                                                <div class="m-temp__pic text-center">
                                                    <a :href="item.image" data-lightbox="upload_image" :data-title="item.filename">
                                                        <img class="m-temp__img" :src="item.thumbnail" :alt="item.filename" width="60" height="60" style="border: 1px solid #233e6b;" />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <br><div class="m-separator m-separator--dashed d-xl-12"></div><br>
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group m-form__group row" id="status">
                                        <label for="" class="col-3">Status:</label>
                                        <div class="col-9">
                                            <strong id="status_state"><span v-text="vm_tab1.status">&nbsp;</span></strong>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group m-form__group row">
                                        <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                            From: 
                                        </label>
                                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                            <b v-text="moment(vm_tab1.date_from).format('LLL')"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                            To: 
                                        </label>
                                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                            <b v-text="moment(vm_tab1.date_to).format('LLL')"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                            Purpose:
                                        </label>
                                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="purpose">
                                            <b v-text="vm_tab1.purpose"></b>
                                        </div>
                                    </div>
                                    <br>
                                    <div id="actual_time" style="display: none;">
                                        <div class="form-group m-form__group row">
                                            <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Actual time started:</label>
                                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                                <b v-text="(vm_tab1.actual_time_start != '0000-00-00 00:00:00') ? moment(vm_tab1.actual_time_start).format('LLL') : '-- : --'"></b>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group row">
                                            <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Actual time ended:</label>
                                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                                <b v-text="(vm_tab1.actual_time_end != '0000-00-00 00:00:00') ? moment(vm_tab1.actual_time_end).format('LLL') : '-- : --'"></b>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group row">
                                            <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Actual time worked:</label>
                                            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                                <b v-text="(vm_tab1.actual_time_work && vm_tab1.actual_time_work != '0') ? vm_tab1.actual_time_work : 'N/A'"></b>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <br>
                                    <div class="form-group m-form__group row">
                                        <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Created By:</label>
                                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                            <b v-text="vm_tab1.created_by"></b> ON <b v-text="moment(vm_tab1.created_at).format('LLL')"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Last Updated By:</label>
                                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                            <b v-text="vm_tab1.updated_by"></b> <span id="updated_at"></span>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row" id="requested_by">
                                        <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Requested by:</label>
                                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                            <b v-text="vm_tab1.display_requested_by"></b> ON <b v-text="vm_tab1.is_imported === '1'? moment(vm_tab1.requested_at).format('LL'): moment(vm_tab1.requested_at).format('LLL')"></b>
                                        </div>
                                        <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12">&nbsp;</label>
                                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="requested_remarks">
                                            Remarks: <b v-text="vm_tab1.requested_remarks"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row" id="approved_by" style="display: none;">
                                        <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Approved by:</label>
                                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                            <b v-text="vm_tab1.approved_by"></b> ON <b v-text="vm_tab1.is_imported === '1'? moment(vm_tab1.approved_at).format('LL'): moment(vm_tab1.approved_at).format('LLL')"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row" id="cancelled_by" style="display: none;">
                                        <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                            Cancelled by:
                                        </label>
                                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                            <b v-text="vm_tab1.cancelled_by"></b> ON <b v-text="moment(vm_tab1.cancelled_at).format('LLL')"></b>
                                        </div>
                                        <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12">&nbsp;</label>
                                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="cancelled_remarks">
                                            Remarks: <b v-text="vm_tab1.cancelled_remarks"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row" id="disapproved_by" style="display: none;">
                                        <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Disapproved by:</label>
                                        <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                            <b v-text="vm_tab1.disapproved_by"></b> ON <b v-text="moment(vm_tab1.disapproved_at).format('LLL')"></b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>

                    <div class="modal-footer" id="buttons">
                        <?php $current_action = $this->core_layout->getCurrentActions(); ?>
                        <?php if(in_array("approve_action", $current_action)): ?>
                            <a class="btn btn-success m-btn m-btn--custom m-btn--icon m-btn--air m-btn--uppercase btnApprove_action btnPending"
                                href="#" data-toggle="modal" data-target="#approve_modal">
                                Approve
                            </a>
                        <?php endif; ?>
                        <?php if(in_array("disapprove_action", $current_action)): ?>
                            <a class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--air m-btn--uppercase btnDisapprove_action btnPending" href="#" data-toggle="modal" data-target="#disapprove_modal">
                                Disapprove
                            </a>
                        <?php endif; ?>
                        <?php if(in_array("edit", $current_action)): ?>
                            <button type="button" class="btn btn-warning m-btn m-btn--custom m-btn--icon m-btn--air m-btn--uppercase btnEdit text-white btnPending" onclick="edit()">
                                Edit
                            </button>
                        <?php endif; ?>
                        <?php if(in_array("cancel", $current_action)): ?>
                            <a class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--air m-btn--uppercase btnCancel btnPending" href="#" data-toggle="modal" data-target="#cancel_modal">
                                Cancel
                            </a>
                        <?php endif; ?>
                        <?php if(in_array("undo_approval", $current_action)): ?>
                            <a class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--air m-btn--uppercase btnUndo_approval btnApproved" href="#" data-toggle="modal" data-target="#undo_approval_modal">
                                Undo Approval
                            </a>
                        <?php endif; ?>
                        <?php if(in_array("undo_disapproval", $current_action)): ?>
                            <a class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--air m-btn--uppercase btnUndo_disapproval btnDisapproved" href="#" data-toggle="modal" data-target="#undo_approval_modal">
                                Undo Disapproval
                            </a>
                        <?php endif; ?>
                        <?php if(in_array("print", $current_action)): ?>
                            <a class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--uppercase btnPrint btnApproved text-white" onclick="prints()">
                                Print
                            </a>
                        <?php endif; ?>
                        <?php if((in_array("back", $current_action))): ?>
                            <a href="<?=(isset($_GET['page']) && $_GET['page']) ? $_GET['page'] : 'masterfile' ?>" class="btn btn-metal m-btn m-btn--custom m-btn--icon m-btn--air m-btn--uppercase btnBack text-white">
                                Back
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
	    </div>
    </div>
</div>
 
<div class="modal fade" id="approve_modal" tabindex="-1" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Approve overtime request
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="approve_form">
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
                            <div id="progress_approve" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="display:none;">
                                <div class="progress-bar progress-bar-success" style="width: 0%;"></div>
                            </div>
                            <div id="tempModalApproveImages">
                                <template v-if="count > 0">
                                    <div class="row">
                                        <div class="col-md-2 col-lg-2 col-sm-2 col-xs-12" v-for="(item, index) in rows">
                                            <div class="m-temp__pic text-center">
                                                <a :href="item.image" data-lightbox="tempimage" :title="item.filename" :data-title="item.filename">
                                                    <img class="m-temp__img" :alt="item.filename" :title="item.filename" :src="item.thumbnail" width="75" height="75" style="margin-bottom: 0.5rem;" />
                                                </a>
                                                <div class="m-checkbox-inline">
                                                    <label class="m-checkbox">
                                                        <input type="checkbox" name="attachment_image[]" :value="item.current_image" class="temp-attachment_image" @click="getCheckedCount" />{{renderImageLabel(index)}}<span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 m--margin-top-10 text-left">
                                        <input id="checked_count" type="hidden" data-validation="checkbox_group_min1" value="0" />
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
                    <p><b>Approve</b> overtime request? Click <b>yes</b> if you wish to proceed.</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">Yes</button>
                    <button type="button" class="btn text-white btn-metal btnCancel" data-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="undo_approval_modal" tabindex="-1" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Undo Approval
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="undo_approval_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label for="" class="col-12 col-form-label form-control-label">
                            <b>Undo approval</b> of overtime request? Click <b>yes</b> if you wish to proceed. 
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">Yes</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="disapprove_modal" tabindex="-1" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Disapprove Overtime request
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="disapprove_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <b>Disapprove</b> overtime request? Click <b>yes</b> if you wish to proceed.
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave"> Yes </button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="undo_disapproval_modal" tabindex="-1" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Undo Disapproval
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "undo_disapproval_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label for="" class="col-12 col-form-label form-control-label">
                            <b>Undo disapproval</b> of overtime request? Click <b>yes</b> if you wish to proceed. 
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">Yes</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cancel_modal" tabindex="-1" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Cancel Overtime Request
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "cancel_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label for="" class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label form-control-label">
                            Remarks:
                        </label>
                        <div class="col-12">
                            <textarea class="form-control" name="cancelled_remarks" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">Save</button>
                    <button type="button" class="btn text-white btn-metal btnCancel" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>