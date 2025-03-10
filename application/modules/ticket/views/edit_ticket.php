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
		<div class="col-lg-9 col-md-9 col-sm-12">
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
                <form id="frm_status_new" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				    <div class="m-portlet__body">
					    <div class="row">
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                        Reference: <span style="color:red;">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-12 col-xs-12">
                                        <b v-text="vm_tab1.reference_no"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                        Requested by: <span style="color:red;">*</span>
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-12 col-xs-12">
                                        <b v-text="vm_tab1.requested_by"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                        Date Needed: <span style="color:red;">*</span>
                                    </label>
                                    <div class="col-sm-12 col-xs-12 col-md-6 input-group date" id="need_dt_group">
                                        <input class="form-control m-input" type="text" name="date_required" id="date_required" data-validation="required"/>
                                        <span class="input-group-addon">
											<i class="la la-calendar glyphicon-th"></i>
										</span>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-sm-12 col-xs-12 col-md-4 col-form-label">
                                        Severity <span style="color:red;">*</span>
                                    </label>
                                    <div class="col-sm-12 col-xs-12 col-md-8">
                                        <select id="severity" name="severity" class="form-control select2" data-validation="required">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                <br>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <div class="col-sm-12 col-lg-12 col-md-12"> 
                                    <input type="hidden" class="form-control" name="pic[]" id="pic">
                                        <span class="btn btn-success fileinput-button">
                                            <em class="fa fa-upload"></em>
                                            <span>SELECT FILE </span>
                                            <input type="file" id="fileupload" name="files" multiple accept=".jpg,.jpeg,.png,.pdf" >
                                        </span>
                                        <div id="progress" class="progress" style="height: 7px;">
                                            <div class="progress-bar progress-bar-small progress-bar-success"></div>
                                        </div> 
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 mt-3" id="uploaded_files">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed d-xl-12"></div> 
                        <br>
                        <div class='row'>
                            <div class='col-lg-6 col-md-12 col-sm-12'>
                                <div class="form-group m-form__group row">
                                    <label class="col-sm-12 col-xs-12 col-md-4 col-form-label">
                                        Department <span style="color:red;">*</span>
                                    </label>
                                    <div class="col-sm-12 col-xs-12 col-md-8">
                                        <select id="department" name="department" class="form-control select2" data-validation="required">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-sm-12 col-xs-12 col-md-4 col-form-label">
                                        Category <span style="color:red;">*</span>
                                    </label>
                                    <div class="col-sm-12 col-xs-12 col-md-8">
                                        <select id="category" name="category" class="form-control select2" data-validation="required">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="dept-res">
                                    <label class="col-sm-12 col-xs-12 col-md-4 col-form-label">
                                    Department Responsible: <span style="color:red;">*</span>
                                    </label>
                                    <div class="col-sm-12 col-xs-12 col-md-8">
                                        <select id="responsibility" name="responsibility" class="form-control select2" data-validation="required">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="webportal">
                                    <label class="col-sm-12 col-xs-12 col-md-4 col-form-label">
                                        Sub-Category <span style="color:red;">*</span>
                                    </label>
                                    <div class="col-sm-12 col-xs-12 col-md-8">
                                        <select id="sub_category" name="sub_category" class="form-control select2" data-validation="required">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 col-form-label">
                                        Issue: <span style="color:red;">*</span>
                                    </label>
                                    <div class="col-sm-12 col-xs-12 col-md-8">
                                        <textarea class="form-control m-input" id="issue" name="issue" rows="4" data-validation="required"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class='col-lg-6 col-md-12 col-sm-12'>
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-4">
                                        Performed by:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <select id="performed_by" name="performed_by" class="form-control select2">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-sm-12 col-xs-12 col-md-4 col-form-label">
                                        Status: <span style="color:red;">*</span>
                                    </label>
                                    <div class="col-sm-12 col-xs-12 col-md-8">
                                        <select id="status" name="status" class="form-control select2" data-validation="required">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--solid d-xl-12"></div>
                        <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                            <button type="submit" class="btn m-btn btn-success text-white m-btn--custom m-btn--icon m-btn--air m-btn--box btnSave">
                                <span>
                                    Save
                                </span>
                            </button>
                            <a href="<?php echo base_url("ticket/tickets"); ?>">
                                <button type="button" class="btn m-btn btn-danger text-white m-btn--custom m-btn--icon m-btn--air m-btn--box btnNew">
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
        <div class="col-lg-3 col-md-3 col-sm-12">
			<div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Comments
							</h3>
						</div>
					</div>
				</div>
                <div class="m-portlet__body">
                <form id="frm_comments">
                    <div class="form-group m-form__group row">
                        <div class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="400" style="height: 400px; width: 100%; overflow: visible; max-height: 400px; position: relative;">
                            <div id="mCSB_3" class="mCustomScrollBox mCS-minimal-dark mCSB_vertical mCSB_outside" tabindex="0" style="max-height: none;">
                                <div id="mCSB_3_container" class="mCSB_container" style="position:relative; top:0; left:0;" dir="ltr">
                                    <div class="m-widget3" v-if="vm_tab2">
                                        <div class="m-widget3__item" v-for="data in vm_tab2">
                                            <div class="m-widget3__header">
                                                <!-- <div class="m-widget3__user-img">
                                                </div> -->
                                                <div class="m-widget3__info">
                                                    <span class="m-widget3__username">
                                                        {{ data.created_by }}
                                                    </span>
                                                    <br>
                                                    <span class="m-widget3__time">
                                                    {{ data.created_at }}
                                                    </span>
                                                </div>
                                                <!-- <span class="m-widget3__status mb-4">
                                                    <a href="#" @click="delete_comment(data.id)" class="btn btn-danger m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill m-btn--air">
                                                        <i class="la la-trash"></i>
                                                    </a>
                                                </span> -->
                                            </div>
                                            <div class="m-widget3__body">     
                                                <div class="m-widget3__info"><i>
                                                    {{ data.comment }}</i>
                                                </div>
                                                <!-- <p class="m-widget3__text">  
                                                </p> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-widget3" v-else>
                            <div class="m-widget3__item">
                                <div class="m-widget3__header">
                                    <div class="m-widget3__info">
                                        <span class="m-widget3__username">
                                            No comments.
                                        </span>
                                        <br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                
                <div class="m-separator m-separator--dashed d-xl-12"></div> 
                <form id="frm-add-comment">
                    <div class="m-widget3">
                        <div class="m-widget3__item">
                            <div class="m-widget3__body">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <input name="ticket_id" id="ticket_id" type="hidden">
                                <textarea name="comment" id="comment" class="form-control" placeholder="Write here" row="3"></textarea>
                            </div>
                        </div>
                        <button class="btn btn-success btn-sm" type="submit">Add</button>
                    </div>
                </form>
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