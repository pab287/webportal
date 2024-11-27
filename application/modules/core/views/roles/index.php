<?php $actions = $this->core_layout->getCurrentActions(); ?>
<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Manage Roles
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<ul class="m-portlet__nav">
							<li class="m-portlet__nav-item">
								
							</li>
						</ul>
					</div>
				</div>
				<div class="m-portlet__body">
					<div class="m-form m-form--label-align-right m--marginless">
						<div class="row align-items-center">
							<div class="col-xl-8 order-2 order-xl-1">
								<div class="form-group m-form__group row align-items-center">
									<div class="col-md-12">
										<a id="user_role-new" href="javascript:void(0);"
											class="btn btn-success m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew btnNewUserRole" 
											data-toggle="modal" 
											data-target="#modal-user_role-new">
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
							<div class="col-xl-4 order-1 order-xl-2 m--align-right">
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
					</div>
					<!--begin: Datatable -->
						<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
							<table class="table table-striped table-bordered" id="table-roles" width="100%">
								<thead>
									<tr>
										<th>Name</th>
										<th>Description</th>
										<th>Status</th>
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

<!--begin::Modal-->
<div class="modal fade" id="modal-user_role-new" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<form action="<?php echo base_url('core/roles/add_role'); ?>" method="POST" id="form-roles">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<div class="modal-header"><h5 class="modal-title">New Role</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-control-label">Name</label>
					<input type="text" name="name" class="form-control inptName" data-validation="required">
				</div>
				<div class="form-group">
					<label class="form-control-label">Description</label>
					<textarea class="form-control txtDescription" name="description" data-validation="required"></textarea>
				</div>
				<div class="form-group">
					<label for="">Status</label>
					<div class="m-radio-inline">
						<label class="m-radio"><input id="status1" type="radio" name="status" value="1" checked>Active<span></span></label>
						<label class="m-radio"><input id="status0" type="radio" name="status" value="0">Inactive<span></span></label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-primary btn-submit btnSave">Save</button>
			</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-user_role-edit" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<form action="<?php echo base_url('core/roles/update_role')?>" method="POST" id="form-roles-edit">
			<input type="hidden" class="inptId" name="id" />
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<div class="modal-header">
				<h5 class="modal-title" >Update Role</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-control-label">Name</label>
					<input type="text" class="form-control inptName" readonly="readonly">
				</div>
				<div class="form-group">
					<label class="form-control-label">Description</label>
					<textarea class="form-control txtDescription" name="description" data-validation="required"></textarea>
				</div>
				<div class="form-group">
					<label for="">Status</label>
					<div class="m-radio-inline">
						<label class="m-radio"><input id="status1" type="radio" name="status" value="1" checked>Active<span></span></label>
						<label class="m-radio"><input id="status0" type="radio" name="status" value="0">Inactive<span></span></label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-primary btn-submit btnUpdate">Save</button>
			</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-user_role-delete" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<input type="hidden" id="removeRole" value="0" />
			<div class="modal-header">
				<h5 class="modal-title">Delete User Role</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body"><i class="la la-warning"></i>  Are you sure you want to delete this user role? </div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary btn-submit-delete btnDelete">Yes</button>
				<button class="btn btn-danger" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-user_module-assign" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal fade" id="modal-user_role-assign" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal fade" id="modal-user_role-privilege" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content"></div>
	</div>
</div>
<!--end::Modal-->