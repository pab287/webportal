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
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Create Ticket
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
                                    <label class="col-sm-12 col-xs-12 col-md-2 col-form-label">
                                        Department
                                    </label>
                                    <div class="col-sm-12 col-xs-12 col-md-10">
                                        <select id="department" name="department"  data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-sm-12 col-xs-12 col-md-2 col-form-label">
                                        Date Needed
                                    </label>
                                    <div class="col-sm-12 col-xs-12 col-md-10 input-group date" id="need_dt">
                                        <input class="form-control m-input" type="text" name="need_dt" id="need_dt" maxlength="22" data-validation="required"/>
                                        <span class="input-group-addon">
											<i class="la la-calendar glyphicon-th"></i>
										</span>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-sm-12 col-xs-12 col-md-2 col-form-label">
                                        Type
                                    </label>
                                    <div class="col-sm-12 col-xs-12 col-md-10">
                                        <select id="type" name="type"  data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="webportal">
                                    <label class="col-sm-12 col-xs-12 col-md-2 col-form-label">
                                        Module
                                    </label>
                                    <div class="col-sm-12 col-xs-12 col-md-10">
                                        <select id="module" name="module"  data-validation="required">

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-sm-12 col-xs-12 col-md-2 col-form-label">
                                        Issue
                                    </label>
                                    <div class="col-sm-12 col-xs-12 col-md-10">
                                        <textarea class="form-control m-input" id="issue" name="issue" rows="4" data-validation="required"></textarea>
                                    </div>
                                </div>
                            </div>
                            <!--right-->
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
                            </div>
                            
                            <!--end right-->
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
                                <button type="button" class="btn m-btn btn-metal text-white m-btn--custom m-btn--icon m-btn--air m-btn--box btnNew">
                                    <span>
                                        Close
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