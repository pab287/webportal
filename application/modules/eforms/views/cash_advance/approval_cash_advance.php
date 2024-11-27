<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Cash Advance (for approval)
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						
					</div>
				</div>
				<div class="m-portlet__body">
                <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                    <div class="row align-items-center">
                        <div class="col-xl-8 order-2 order-xl-1">
                            <div class="form-group m-form__group row align-items-center">
                                <div class="col-md-4">
								
                                </div>
								<div class="col-md-4">
									<div class="dropdown">
										<button class="btn btn-brand dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											Actions
										</button>
										<div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
											<a class="dropdown-item" href="#">
												<i class="la la-search-plus"></i> Query Builder
											</a>
											<a class="dropdown-item" href="#">
												<i class="la la-barcode"></i> Generate Barcode
											</a>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item" href="#">
												<i class="la la-file-archive-o"></i> Mass Archive
											</a>
										</div>
									</div>
								</div>
                            </div>
                        </div>
                        <div class="col-xl-4 order-1 order-xl-2 m--align-right">
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
											<button id="tbl-btn-share" type="button" class="btn btn-success m-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="la la-table"></i>
											</button>
											<div class="dropdown-menu" aria-labelledby="btnGroupDrop1" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
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
											<button id="tbl-btn-share" type="button" class="btn btn-info m-btn m-btn--pill-last dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="la la-share"></i>
											</button>
											<div class="dropdown-menu" aria-labelledby="btnGroupDrop2" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
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
						<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll"  style="overflow-x:scroll;">
							<table class="table table-striped table-bordered" id="table-cash-advance" width="100%">
								<thead>
									<tr>
										<th>Status</th>
										<th>CA #</th>
										<th>Employee</th>
										<th>Amount Applied</th>
										<th>Purpose</th>
										<th>Amount Approved</th>
                                        <th>Date Applied</th>
										<th>Date Approved</th>
										<th>Action</th>
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

<!--modal Approve-->
<div class="modal fade" id="approved_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Enter Approved Amount
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="approved_modal_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group row">
                        <label class="col-12 form-control-label">
                            Amount Approved:
                        </label>
                        <div class="col-12">
                            <input type="text" name="amt_approved" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-12 form-control-label">
                            Notes:
                        </label>
                        <div class="col-12">
                            <textarea class="form-control" name="approved_remarks" rows="5" id="approve_remarks"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        Save
                    </button>
                    <button type="button" class="btn text-white btn-metal btnNew" data-dismiss="modal">
                        Close
                    </button>       
                </div>
            </form>
        </div>
    </div>
</div>
<!--end Approve-->
<!--modal disapprove-->
<div class="modal fade" id="disapprove_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Disapprove Form
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="disapprove_modal_form">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-3 col-form-label form-control-label">
                            Reason:
                        </label>
                        <div class="col-9">
                            <textarea class="form-control" rows="5" name="disapproved_remarks" id="reason_disapprove"></textarea>
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
<!--end disapprove modal-->