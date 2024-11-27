<style>
    .fileinput-button {
        display: none;
    }
    .m-widget2__checkbox {
        padding-top: 0 !important;
        vertical-align: middle !important;
        padding-right: 8px !important;
    }

    .m-widget2__desc {
        width: 70% !important;
    }

    .m-widget2__actions {
        width: 100% !important;
        text-align: right !important;
    }
    
    #preview-document-dialog .modal-dialog {
        height: 80%;
    }

    #preview-document-dialog .modal-content {
        height: 100%;
    }

    #preview-document-dialog .modal-body {
        padding: 0;
    }

    #preview-document-dialog iframe, embed {
        border: 0;
        width: 100%;
        height: 100%;
    }
</style>
<div class="m-content">
    <div class="row">
		<div class="col-lg-9 col-md-7 col-sm-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Ticket Details
							</h3>
						</div>
					</div>
				</div>
                <form id="frm_status_new">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				    <div class="m-portlet__body">
					    <div class="row">
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-4">
                                        Requested by:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="vm_tab1.requested_by"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-4">
                                        Date requested:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="moment(vm_tab1.requested_dt).format('LL')"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-4">
                                        Date Needed:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="moment(vm_tab1.need_dt).format('LL')"></b>
                                    </div>
                                </div>
                                <br>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                               
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed d-xl-12"></div> 
                        <br>
                        <div class='row'>
                            <div class='col-lg-6 col-md-12 col-sm-12'>
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-4">
                                        Department:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="vm_tab1.department"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-4">
                                        Type:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="vm_tab1.type"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="webportal">
                                    <label class="col-lg-4 col-md-6 col-sm-4">
                                        Module:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="vm_tab1.module"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-14">
                                        Issue:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="vm_tab1.issue"></b>
                                    </div>
                                </div>
                            </div>
                            <div class='col-lg-6 col-md-12 col-sm-12'>
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-4">
                                        Performed by:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="vm_tab1.performed_by_det"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-4">
                                        Date completed:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="moment(vm_tab1.performed_dt).format('LL')"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-4">
                                        Remarks:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="vm_tab1.remark"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="reopen_field">
                                    <label class="col-lg-4 col-md-6 col-sm-4">
                                        Reason for reopen:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="vm_tab1.open"></b>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--solid d-xl-12"></div>  
                        <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                            <a href="<?php echo base_url("ts/ticketing/masterfile"); ?>">
                                <button type="button" class="btn m-btn btn-metal text-white m-btn--custom m-btn--icon m-btn--air m-btn--box btnNew">
                                    <span>
                                        Back
                                    </span>
                                </button>
                            </a>
                        </div>    
                    </div>
                </form>
			</div>
  	    <!--end::Portlet-->  
		</div>

        <div class="col-lg-3 col-md-5 col-sm-12">
			<div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__body">
                    <div class="form-group m-form__group row">
                        <div id="uploaded_files" class="custom-image_container col-12">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="preview-document-dialog">
    <div class="modal-dialog modal-extra-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Modal body text goes here.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>