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
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 id="header" class="m-portlet__head-text">
                                Tickets Masterfile
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <a href="<?php echo base_url("ticket/archive"); ?>" class="m-nav__link btnArchive">
                            <i class="m-nav__link-icon flaticon-open-box"></i>
                            <span class="m-nav__link-text">
                                Archive
                            </span>
                        </a>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                        <a href="<?php echo site_url("ticket/index"); ?>"
                                           class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew">
											<span> 
												<i class="la la-plus"></i>
												<span>
													New
												</span>
											</span>
                                        </a>
                                        <button class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnExport" type="button" data-toggle="modal" data-target="#modal-query-builder">
											<span> 
												<i class="la la-list"></i>
												<span>
													Query Builder
												</span>
											</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right d-flex flex-row">
                                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
										<span>
											<i class="la la-search"></i>
										</span>
									</span>
                                </div>
                                <div class="m-btn-group btn-group" role="group">
                                    <button id="tbl-btn-share" title="Export" type="button"
                                            class="btn btnExport btn-success m-btn dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        <i class="la la-external-link"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1"
                                            x-placement="bottom-start"
                                            style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
                                        <a href="" class="dropdown-item datatable-csv" id="ExportCSV">
                                            <i class="m-nav__link-icon la la-file-o"></i>
                                            <span class="m-nav__link-text">
                                                CSV
                                            </span>
                                        </a>
                                        <a href="" class="dropdown-item datatable-pdf" id="ExportPDF">
                                            <i class="m-nav__link-icon la la-file-pdf-o"></i>
                                            <span class="m-nav__link-text">
                                                PDF
                                            </span>
                                        </a>
                                        <a href="" class="dropdown-item datatable-excel" id="ExportExcel">
                                            <i class="m-nav__link-icon la la-file-excel-o"></i>
                                            <span class="m-nav__link-text">
                                                EXCEL
                                            </span>
                                        </a>   
                                    </div>
                                </div>
                                <div class="m-separator m-separator--dashed d-xl-none"></div>
                            </div>
                        </div>
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">
                                <div class="col-xl-8 order-2 order-xl-1">
                                    <div class="colms-12">
                                        <div class="btn-group m-btn-group" role="group" aria-label="...">
                                            <button type="button" class="btn btn-primary" id="reload_dtTbl">
                                                <i class="la la-refresh"></i>
                                            </button>
                                            <div class="m-btn-group btn-group" role="group">
                                                <button id="tbl-btn-share" type="button" class="btn btn-success m-btn dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="la la-table"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" x-placement="bottom-start"
                                                     style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <a class="dropdown-item" href="#">
                                                        Dropdown link
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        Dropdown link
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        Dropdown link
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        Dropdown link
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="m-btn-group btn-group" role="group">
                                                <button id="tbl-btn-share" type="button" class="btn btn-info m-btn m-btn--pill-last dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="la la-share"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop2" x-placement="bottom-start"
                                                     style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <a class="dropdown-item" href="#">
                                                        Excel
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--begin: Datatable -->
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll m-datatable--scroll" style="overflow-x: scroll;">
                        <table class="table table-striped table-bordered table-sm" id="table-tickets" width="100%">
                            <thead>
                            <tr>
                                <th class="notExport"></th>
                                <th>Reference #</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Date Needed</th>
                                <th>Days Overdue</th>
                                <th>Requested by</th>
                                <th>Performed by</th>
                                <th class="notExport">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <!--end: Datatable -->
                </div>
            </div>
            <!--end::Portlet-->
        </div>
    </div>
</div>

<div class="modal fade" id="remove-ticket-confirmation-modal"
     tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    ARCHIVE CONFIRMATION
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="frm-remove-ticket" onsubmit="processRemoveTicket(this); return false;">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <p class="mb-0 m--regular-font-size-lg1">
                        Do you want to archive this ticket?
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
<!-- query builder -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-query-builder">
	<form id="frm-query-builder">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Query Builder</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div id="query-builder"></div>
				</div>
				<div class="modal-footer">
					<button type="button" onclick="clear_query_builder()" class="btn btn-danger btnAdvance_search mr-auto">Clear</button>
					<button type="button" id="query-builder-btn" class="btn btn-primary btnAdvance_search">Generate</button>
				</div>
			</div>
		</div>
	</form>
</div>
<!-- end query builder -->

<div class="modal fade" tabindex="-1" role="dialog" id="view-ticket-modal">
    <div class="modal-dialog modal-extra-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ticket Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="m-portlet m-portlet--mobile">
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
                                        <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
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
                                                    'm-list-timeline__badge--danger': item.type === 'Cancelled',
                                                    'm-list-timeline__badge--focus': item.type === 'restore',
                                                    'm-list-timeline__badge--brand': item.type === 'archive'
                                                    }"
                                                ></span>
                                                <span class="m-list-timeline__text" v-text="item.log_message"></span></br>
                                                <span class="m-list-timeline__text" ></span>
                                                <span class="m-list-timeline__text" v-text="item.name"></span>
                                                <span class="m-list-timeline__text" ></span></br>
                                                <span class="m-list-timeline__text text-left" v-text="item.created_at"></span>
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
        </div>
    </div>
</div>