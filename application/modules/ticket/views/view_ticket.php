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
		<div class="col-lg-12 col-md-12 col-sm-12">
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
                                        Reference:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="vm_tab1.reference_no"></b>
                                    </div>
                                </div>
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
                                        Date Needed:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="vm_tab1.requested_date"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-4">
                                        Priority:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <span v-html="vm_tab1.priority"></span>
                                    </div>
                                </div>
                                <br>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12" id="uploaded_files">
                                    <!-- <div class="col-lg-12 col-md-12 col-sm-8">
                                    </div> -->
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
                                        <b v-text="vm_tab1.category"></b>
                                    </div>
                                </div>
                                <div v-if="vm_tab1.responsibility" class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-4">
                                        Department Responsible:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="vm_tab1.responsibility"></b>
                                    </div>
                                </div>
                                <div v-if="vm_tab1.sub_category != 'Not set'" class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-4">
                                        Module:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="vm_tab1.sub_category"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-lg-4 col-md-6 col-sm-14">
                                        Issue:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <b v-text="vm_tab1.message"></b>
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
                                        Status:
                                    </label>
                                    <div class="col-lg-8 col-md-6 col-sm-8">
                                        <span v-html="vm_tab1.status"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--solid d-xl-12"></div>  
                        <div class="col-xl-12 order-1 order-xl-2 m--align-right">
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
        <div class="col-lg-8 col-md-8 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Status Logs
							</h3>
						</div>
					</div>
				</div>
                <div class="m-portlet__body" id="status-log">
                    <div class="m-list-timeline">
                        <div class="m-list-timeline__items">
                            <template v-for="(item, index) in trail" :key="index">
                                <div class="m-list-timeline__item">
                                <span 
                                    class="m-list-timeline__badge"
                                    :class="{
                                    'm-list-timeline__badge--success': item.type === 'open' || item.type === 'new',
                                    'm-list-timeline__badge--warning': item.type === 'in progress',
                                    'm-list-timeline__badge--info': item.type === 'completed',
                                    'm-list-timeline__badge--danger': item.type === 'Cancelled'
                                    }"
                                ></span>
                                <span class="m-list-timeline__text" v-text="item.log_message"></span><br>
                                <span class="m-list-timeline__text" v-text="item.name"></span>
                                <span class="m-list-timeline__text text-right" v-text="item.created_at"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
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
                    <div class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="400" style="height: 400px; width: 100%; overflow: visible; max-height: 400px; position: relative;"><div id="mCSB_3" class="mCustomScrollBox mCS-minimal-dark mCSB_vertical mCSB_outside" tabindex="0" style="max-height: none;"><div id="mCSB_3_container" class="mCSB_container" style="position:relative; top:0; left:0;" dir="ltr">

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
                        </div></div></div>
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