<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 id="header" class="m-portlet__head-text">
                                Ticket <small>(ARCHIVE)</small>
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <a href="<?php echo base_url("ticket/tickets"); ?>" class="m-nav__link btnArchive">
                            <i class="m-nav__link-icon flaticon-open-box"></i>
                            <span class="m-nav__link-text">
                                Masterfile
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
                                        <button data-toggle="m-tooltip" data-original-title="Restore Selected" data-skin="dark" type="button" data-delay="{&quot;show&quot;: 300}" class="btn btn-primary m-btn m-btn--icon m--margin-right-5 btn-restore-multiple btnMass_restore" onclick="confirmRestoreSelections()" disabled="disabled">
                                            <i class="la la-reply"></i> RESTORE
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
                        <table class="table table-striped table-bordered" id="table-tickets-archive" width="100%">
                            <thead>
                            <tr>
                                <th class="d-flex justify-content-center mx-auto">
                                    <label class="m-checkbox">
                                        <input type="checkbox" class="form-control" id="select-all-archived-page">
                                        <span></span>
                                    </label>
                                </th>
                                <th>Reference #</th>
                                <th>Category</th>
                                <!-- <th>Sub Category</th> -->
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Date Needed</th>
                                <th>Created At</th>
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

<div class="modal fade" id="restore-ticket-confirmation-modal"
     tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    RESTORE CONFIRMATION
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="frm-restore-ticket" onsubmit="processRestoreCategory(this); return false;">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <p class="mb-0 m--regular-font-size-lg1">
                        Do you want to restore this ticket?
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


<div class="modal fade" tabindex="-1" role="dialog" id="confirm-restore-multiple">
    <form id="frm-confirm-restore-multiple" action="<?= base_url('ticket/ticket/restore_archived_ticket_multiple') ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="multiple_id" class="multiple_id">
        <div class="modal-dialog" role="dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Confirm Restore
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div style="text-transform: none; font-size: 20px;">
                        Are you sure to restore selected tickets?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnNew">
                        Yes
                    </button>
                    <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                        No
                    </button>
                </div>
            </div>
        </div>
    </form>
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