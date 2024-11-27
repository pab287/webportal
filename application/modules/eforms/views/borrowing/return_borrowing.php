<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Returned Item
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
					
					</div>
				</div>
				<div class="m-portlet__body">
                <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                    <div class="row align-items-center">
                        <div class="col-xl-8 order-2 order-xl-1">
                            <div class="form-group m-form__group row align-items-center">
                                <div class="col-md-4">
									<button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<span><i class="fa fa-search"></i><span>Filter</span><span class="dropdown-toggle"></span></span>
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
										<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-query-builder">
											Query Builder
										</a>
									</div>
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
					<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
						<div class="row align-items-center">
							<div class="col-xl-8 order-2 order-xl-1">
								
							</div>
						</div>
					</div>
                </div>
					<!--begin: Datatable -->
						<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
							<table class="table table-striped table-bordered" id="table-borrowing" width="100%">
								<thead>
									<tr>
										<th>Reference#</th>
										<th>Borrower</th>
										<th>Item Borrowed</th>
										<th>Date Borrowed</th>
                                        <th>Date Due</th>
                                        <th>Date Returned</th>
                                        <th>Return Remarks</th>
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

<div class="modal fade" id="undo_return" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title" id="exampleModalLabel">
					Undo Return
				</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						×
					</span>
				</button>
			</div>
			<form id = "undo_return_form">
				<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<div class="col-12 modal-body">
					<div class="form-group m-form__group row">
						<label class="col-12 col-form-label form-control-label">
							Are you sure you want to undo changes on this form?
						</label>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-submit btn-primary btnNew">
						Yes
					</button>
					<button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">
						No
					</button> 
				</div>
			</form>
		</div>
	</div>
</div>

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