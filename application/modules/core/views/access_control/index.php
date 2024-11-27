<?php $actions = $this->core_layout->getCurrentActions(); ?>
<div class="m-content">
<div class="m-portlet m-portlet--mobile">
	<div class="m-portlet__head">
		<div class="m-portlet__head-caption">
			<div class="m-portlet__head-title">
				<h3 class="m-portlet__head-text">Access Control</h3>
			</div>
		</div>
		<div class="m-portlet__head-tools"></div>
	</div>
	<div class="m-portlet__body">
		<div class="m-form m-form--label-align-right m--marginless">
			<div class="row align-items-center">
				<div class="col-xl-8 order-2 order-xl-1">
					<div class="form-group m-form__group row align-items-center">
						<div class="col-md-12">
							<a id="access_control-new" href="javascript:void(0);"
								class="btn btn-success m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew btnNewAcl" 
								data-toggle="modal" 
								data-target="#modal-access_control">
								<span>
									<i class="la la-plus"></i>
									<span>
										New
									</span>
								</span>
							</a>
							<a id="access_control-list" href="javascript:void(0);"
								class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnAssign btnAssignAcl">
								<span>
									<i class="fa fa-list"></i>
									<span>
										Tree View
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
		<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
			<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline" id="table-access_control" width="100%">
				<thead>
					<tr>
						<th>Name</th>
						<th>Label</th>
						<th>Url</th>
						<th>Identifier</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>
</div>

<div class="modal fade" id="modal-access_control" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<form action="<?php echo base_url('core/access_control/add_acl')?>" method="POST" id="form-access_control">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<div class="modal-header">
				<h5 class="modal-title">New Access Control</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-control-label">Name *</label>
					<input type="text" name="name" class="form-control inptName" autocomplete=off data-validation="required" />
				</div>
				<div class="form-group">
					<label class="form-control-label">Label *</label>
					<input type="text" name="label" class="form-control inptLabel" autocomplete=off data-validation="required" />
				</div>
				<div class="form-group">
					<label class="form-control-label">Identifier *</label>
					<input type="text" name="identifier" class="form-control inptIdentifier" autocomplete=off data-validation="required" />
				</div>
				<div class="form-group">
					<label class="form-control-label">Url</label>
					<input type="text" name="url" class="form-control inptUrl" autocomplete=off />
				</div>
				<div class="form-group">
					<label class="form-control-label">Icon</label>
					<input type="text" name="icon" class="form-control inptIcon" autocomplete=off />
				</div>
				<div class="form-group">
					<label for="">Status</label>
					<div class="m-radio-inline">
						<label class="m-radio"><input id="status1" type="radio" name="is_active" value="1" checked>Active<span></span></label>
						<label class="m-radio"><input id="status0" type="radio" name="is_active" value="0">Inactive<span></span></label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-primary btnSave btn-submit">Save</button>
			</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-access_control-edit" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<form action="<?php echo base_url('core/access_control/update_acl')?>" method="POST" id="form-access_control-edit">
			<input type="hidden" class="inptId" name="id" />
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<div class="modal-header">
				<h5 class="modal-title">Edit Access Control</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-control-label">Name</label>
					<input type="text" class="form-control inptName" readonly="readonly" />
				</div>
				<div class="form-group">
					<label class="form-control-label">Label *</label>
					<input type="text" name="label" class="form-control inptLabel" autocomplete=off data-validation="required" />
				</div>
				<div class="form-group">
					<label class="form-control-label">Identifier *</label>
					<input type="text" name="identifier" class="form-control inptIdentifier" autocomplete=off data-validation="required" />
				</div>
				<div class="form-group">
					<label class="form-control-label">Url</label>
					<input type="text" name="url" class="form-control inptUrl" autocomplete=off />
				</div>
				<div class="form-group">
					<label class="form-control-label">Icon</label>
					<input type="text" name="icon" class="form-control inptIcon" autocomplete=off />
				</div>
				<div class="form-group">
					<label for="">Status</label>
					<div class="m-radio-inline">
						<label class="m-radio"><input id="status1" type="radio" name="is_active" value="1" checked>Active<span></span></label>
						<label class="m-radio"><input id="status0" type="radio" name="is_active" value="0">Inactive<span></span></label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-primary btnUpdate btn-submit">Save</button>
			</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-access_control-delete" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<input type="hidden" id="removeAcl" value="0" />
			<div class="modal-header">
				<h5 class="modal-title">Delete Access Control</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<i class="la la-warning"></i>  Are you sure you want to delete this access control? 
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-primary btnDelete btn-submit-delete">Delete</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-access_control-list" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m modal-lg" role="document">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal fade" id="modal-access_control-actions" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content"></div>
	</div>
</div>