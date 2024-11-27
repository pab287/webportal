<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Allowance
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<ul class="m-portlet__nav">
							<li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
								<a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl">
									<i class="la la-ellipsis-h m--font-brand"></i>
								</a>
								<div class="m-dropdown__wrapper">
									<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
									<div class="m-dropdown__inner">
										<div class="m-dropdown__body">
											<div class="m-dropdown__content">
												<ul class="m-nav">
													<li class="m-nav__item">
														<a href="archive" class="m-nav__link btnArchive">
															<i class="m-nav__link-icon la la-archive"></i>
															<span class="m-nav__link-text">
																Archive
															</span>
														</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                        <a class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air btnNew" href="#" data-toggle="modal" data-target="#modal-add">
                                            <span>
                                                <i class="la la-plus"></i>
                                                <span>
                                                    New
                                                </span>
                                            </span>
                                        </a>
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
                            </div>
                        </div>
                        <br>
						<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
							<table class="table table-striped table-bordered" id="table-allowance" width="100%">
								<thead>
									<tr>
										<th>Code</th>
										<th>Name</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>	
								</tbody>
							</table>
						</div>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-add">
	<form id="frm-add">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add Allowance</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
                        <label class="form-control-label">
                            Code*
                        </label>
                        <div>
                            <input type="text" name="code" class="form-control" data-validation="required"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            Name*
                        </label>
                        <div>
                            <input type="text" name="name" class="form-control" data-validation="required"/>
                        </div>
                    </div>
				</div>
				<div class="modal-footer">
					<button type="submit" onclick="Save()" class="btn btn-primary btn-submit btnSave">Save</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-edit">
	<form id="frm-edit">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Update Allowance</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
                        <label class="form-control-label">
                            Code*
                        </label>
                        <div>
                            <input type="text" id="code" name="code" class="form-control" data-validation="required"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">
                            Name*
                        </label>
                        <div>
                            <input type="text" id="name" name="name" class="form-control" data-validation="required"/>
                        </div>
                    </div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary btn-submit btnUpdate">Update</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-delete">
	<form id="frm-delete">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Archive Allowance</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            <b>Archiving</b> this data will remove it from table. Do you wish to proceed?
                        </label>
                    </div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary btn-submit btnDelete">Yes</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">No</button>
				</div>
			</div>
		</div>
	</form>
</div>
