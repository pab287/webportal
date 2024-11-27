<style>
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
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Edit Ticket
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
				</div>
                <form id="frm_status_new">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				    <div class="m-portlet__body">
					    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group m-form__group row">
                                    <label class="col-3 col-form-label">
                                        Requested by:
                                    </label>
                                    <div class="col-9">
                                        <input class="form-control m-input" type="text" name="requested_by" id="requested_by" data-validation="required" disabled/>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-3 col-form-label">
                                        Date requested:
                                    </label>
                                    <div class="col-9">
                                        <input class="form-control m-input" type="text" name="requested_dt" id="requested_dt" data-validation="required" disabled/>
                                    </div>
                                </div>
                                <div class="m-separator m-separator--dashed d-xl-12"></div>  
                                <br>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Department:
                                    </label>
                                    <div class="col-10">
                                        <select id="department" name="department"  data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Date Needed:
                                    </label>
                                    <div class="col-10 input-group date" id="need_dt_group">
                                        <input class="form-control m-input" type="text" name="need_dt" id="need_dt" maxlength="22" data-validation="required"/>
                                        <span class="input-group-addon">
											<i class="la la-calendar glyphicon-th"></i>
										</span>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Type:
                                    </label>
                                    <div class="col-10">
                                        <select id="type" name="type"  data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="webportal">
                                    <label class="col-2 col-form-label">
                                        Module:
                                    </label>
                                    <div class="col-10">
                                        <select id="module" name="module"  data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-2 col-form-label">
                                        Issue:
                                    </label>
                                    <div class="col-10">
                                        <textarea class="form-control m-input" id="issue" name="issue" rows="4" data-validation="required"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 row">
                                <div class="col-sm-12 col-xs-12 col-md-4"> 
                                    <input type="hidden" class="form-control" name="pic[]" id="pic">
                                    <span class="btn btn-success fileinput-button">
                                        <em class="fa fa-upload"></em>
                                        <span>SELECT FILE</span>
                                        <input type="file" id="fileupload" multiple name="files">
                                    </span>
                                    <br>
                                    <br>
                                    <div id="progress" class="progress mb-2" style="height: 7px;">
                                        <div class="progress-bar progress-bar-small progress-bar-success"></div>
                                    </div> 
                                </div>
                                <div class="col-sm-12 col-xs-12 col-md-8 col-lg-8 col-xl-8" id="uploaded_files">
                                    
                                </div>
                                <!-- <div class="form-group m-form__group row">
                                    <div class="custom-image_container col-12">
                                        <div id="img_primary" style="margin: 0 auto; text-align: center;">
                                        <a href="" id="view_picture" data-lightbox="image-1" data-title="">
                                            <img id="picture" name="picture" style="max-width: 300px; margin: 0 auto;" src="http://localhost/portaldev/assets/images/ams/images/no_image.jpg">
                                        </a><br>
                                            <div id="progress" class="progress">
                                                <div class="progress-bar progress-bar-success"></div>
                                            </div>  
                                            <input type='hidden' name="pic" v-model="vm_tab1.picture" id="pic"><br>
                                            <span class="btn btn-success fileinput-button">
                                                <i class="glyphicon glyphicon-plus"></i>
                                                <i class="fa fa-camera"></i>
                                                <span>Select file</span>
                                                <input type="file" id="fileupload" name="files">
                                            </span>
                                        </div>
                                        <div id="alt_images"></div> 
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        <div class="m-separator m-separator--solid d-xl-12"></div>  
                        <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                            <button type="submit" class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btn-submit btnNew">
                                <span>
                                    <i class="la la-save"></i>
                                    <span>
                                        Save
                                    </span>
                                </span>
                            </button>
                            <a href="<?php echo base_url("ts/ticketing/masterfile"); ?>">
                                <button type="button" class="btn text-white btn-metal m-btn m-btn--custom m-btn--icon m-btn--air m-btn--box btnNew">
                                    <span>
                                        Cancel
                                    </span>
                                </button>
                            </a>
                     
                        </div>    
                    </div>
                </form>
			</div>
  	    <!--end::Portlet-->  
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
<div class="modal fade" id="remove-file-confirmation-modal"
     tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    REMOVE CONFIRMATION
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="frm-remove-file">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <p class="mb-0 m--regular-font-size-lg1">
                        <input type="hidden" id="file_to_be_deleted" name="filename">
                        Do you want to remove this ticket?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        Yes
                    </button>
                    <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                        No
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>