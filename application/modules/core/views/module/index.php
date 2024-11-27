<div class="m-content">
<div class="m-portlet m-portlet--mobile">
	<div class="m-portlet__head">
		<div class="m-portlet__head-caption">
			<div class="m-portlet__head-title">
				<h3 class="m-portlet__head-text">Modules</h3>
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
							<a id="module-new" href="javascript:void(0);"
								class="btn btn-success m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew btnNewModule" 
								data-toggle="modal" 
								data-target="#modal-add_module">
								<span>
									<em class="la la-plus"></em>
									<span>
										New
									</span>
								</span>
							</a>
							<a id="module-list" href="javascript:void(0);"
								class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnAssign btnAssignModule">
								<span>
									<em class="fa fa-list"></em>
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
			<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline" id="table-modules">
				<thead>
					<tr>
						<th>Name</th>
						<th>Label</th>
						<th>Description</th>
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
<div class="modal fade" id="modal-add_module" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<form action="<?php echo base_url('core/module/add_module'); ?>" method="POST" id="form-add_module">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<div class="modal-header">
				<h5 class="modal-title">New Module</h5>
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
					<label class="form-control-label">Description</label>
					<input type="text" name="description" class="form-control inptLabel" autocomplete=off />
				</div>
				<div class="form-group">
					<label class="form-control-label">Database</label>
					<input type="text" name="database" class="form-control inptLabel" autocomplete=off />
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
					<label class="form-control-label">Background Color</label>
					<input type="text" name="bg_color" class="form-control inptBgColor" autocomplete=off />
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
<div class="modal fade" id="modal-edit_module" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="form-edit_module" class="m-form" method="post" action="<?php echo site_url("core/module/update_module"); ?>">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Edit Module</h5>
					<button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="id" v-model="post.id" />
					<input type="hidden" name="current_name" v-model="post.name" />
					<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
					<div class="form-group">
						<label class="form-control-label">Name *</label>
						<input type="text" name="name" class="form-control inptName" autocomplete="off" data-validation="required" v-model="post.name" />
					</div>
					<div class="form-group">
						<label class="form-control-label">Label *</label>
						<input type="text" name="label" class="form-control inptLabel" autocomplete="off" data-validation="required" v-model="post.label" />
					</div>
					<div class="form-group">
						<label class="form-control-label">Description</label>
						<input type="text" name="description" class="form-control inptLabel" autocomplete=off v-model="post.description" />
					</div>
					<div class="form-group">
						<label class="form-control-label">Database</label>
						<input type="text" name="database" class="form-control inptLabel" autocomplete=off v-model="post.database" />
					</div>
					<div class="form-group">
						<label class="form-control-label">Identifier *</label>
						<input type="text" name="identifier" class="form-control inptIdentifier" autocomplete="off" data-validation="required" v-model="post.identifier" />
					</div>
					<div class="form-group">
						<label class="form-control-label">Url</label>
						<input type="text" name="url" class="form-control inptUrl" autocomplete="off" v-model="post.url" />
					</div>
					<div class="form-group">
						<label class="form-control-label">Background Color</label>
						<input type="text" name="bg_color" class="form-control inptBgColor" autocomplete=off v-model="post.bg_color" />
					</div>
					<div class="form-group">
						<label class="form-control-label">Icon</label>
						<input type="text" name="icon" class="form-control inptIcon" autocomplete="off" v-model="post.icon" />
					</div>
					<div class="form-group">
						<label>Status</label>
						<div class="m-radio-inline">
							<label class="m-radio">
								<input type="radio" name="is_active" value="1" v-model="post.is_active" />
								Active<span></span>
							</label>
							<label class="m-radio">
								<input type="radio" name="is_active" value="0" v-model="post.is_active" />
								Inactive<span></span>
							</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn modalClose btn-danger" data-dismiss="modal">Cancel</button>
					<button class="btnSave btn btn-primary btnUpdateModule" type="submit">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-module-delete" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content">
			<input type="hidden" id="removeModule" value="0" />
			<div class="modal-header">
				<h5 class="modal-title">Remove Module</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<em class="la la-warning"></em>  Are you sure you want to remove this module? 
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary btnDelete btn-submit-delete">Yes</button>
				<button class="btn btn-danger" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-module-list" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-m" role="document">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal fade" id="modal-module-actions" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content"></div>
	</div>
</div>
</div>